"""Exercise the app through its public HTTP entry point with a disposable database."""
import http.cookiejar
import os
from pathlib import Path
import re
import socket
import sqlite3
import subprocess
import tempfile
import time
import unittest
from urllib.error import HTTPError, URLError
from urllib.parse import urlencode, urlsplit
from urllib.request import HTTPCookieProcessor, HTTPRedirectHandler, Request, build_opener

ROOT = Path(__file__).resolve().parents[1]
PASSWORD = "SignalDemo2026!"
CV = b"Fictional CV\nAlex Example\nExperience: Java and AWS engineering.\n"
JOB = {
    "title": "Cloud Integration Engineer",
    "location": "Reading",
    "employment": "Contract",
    "arrangement": "Hybrid",
    "pay": "600",
    "description": "Build reliable Java services on AWS.",
}


class NoRedirect(HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl):
        return None


class Client:
    def __init__(self, base):
        self.base = base
        self.opener = build_opener(NoRedirect(), HTTPCookieProcessor(http.cookiejar.CookieJar()))
        self.token = ""

    def request(self, page="", params=None, data=None, file=None, csrf=True):
        query = ({"page": page} if page else {}) | (params or {})
        path = self.base + "/?" + urlencode(query)
        headers = {}
        if data is not None:
            fields = dict(data)
            if csrf:
                fields["csrf"] = self.token
            if file:
                boundary = "SignalHireHttpTestBoundary"
                parts = [
                    f'--{boundary}\r\nContent-Disposition: form-data; name="{k}"\r\n\r\n{v}\r\n'.encode()
                    for k, v in fields.items()
                ]
                name, content = file
                parts.extend([
                    f'--{boundary}\r\nContent-Disposition: form-data; name="cv"; filename="{name}"\r\n'
                    'Content-Type: application/octet-stream\r\n\r\n'.encode(),
                    content,
                    f'\r\n--{boundary}--\r\n'.encode(),
                ])
                data = b"".join(parts)
                headers["Content-Type"] = "multipart/form-data; boundary=" + boundary
            else:
                data = urlencode(fields).encode()
        try:
            response = self.opener.open(Request(path, data=data, headers=headers), timeout=5)
        except HTTPError as error:
            response = error
        with response:
            body = response.read()
            if response.headers.get_content_type() == "text/html":
                match = re.search(rb'name="csrf" value="([0-9a-f]{64})"', body)
                if match:
                    self.token = match[1].decode()
            return response.code, response.headers, body


class HttpCase(unittest.TestCase):
    def setUp(self):
        scratch = tempfile.TemporaryDirectory()
        self.addCleanup(scratch.cleanup)
        self.db = Path(scratch.name) / "test.sqlite"
        env = dict(os.environ, SIGNALHIRE_DB=str(self.db))
        subprocess.run(["php", "bin/setup.php", "--demo"], cwd=ROOT, env=env, check=True, capture_output=True)
        with socket.socket() as listener:
            listener.bind(("127.0.0.1", 0))
            port = listener.getsockname()[1]
        self.base = f"http://127.0.0.1:{port}"
        log = open(Path(scratch.name) / "server.log", "w+b")
        self.addCleanup(log.close)
        self.server_log = log
        server = subprocess.Popen(
            ["php", "-d", "upload_max_filesize=3M", "-d", "post_max_size=4M",
             "-d", f"session.save_path={scratch.name}", "-S", f"127.0.0.1:{port}", "-t", "public"],
            cwd=ROOT, env=env, stdout=log, stderr=log,
        )

        def stop():
            server.terminate()
            server.wait(timeout=5)

        self.addCleanup(stop)
        for _ in range(50):
            try:
                self.check(Client(self.base).request(), 200)
                break
            except URLError:
                time.sleep(0.05)
        else:
            self.fail("PHP server did not start")

    def check(self, response, expected):
        status, headers, body = response
        detail = body.decode(errors="replace")[:1100]
        if status == 500:
            self.server_log.seek(0)
            detail = self.server_log.read().decode(errors="replace")
        self.assertEqual(status, expected, detail)
        return headers, body

    def account(self, role, fresh=False):
        client = Client(self.base)
        page = "register" if fresh else "login"
        self.check(client.request(page), 200)
        data = {"email": f"{role}{'-new' if fresh else ''}@example.test", "password": PASSWORD}
        if fresh:
            data.update(name=f"New {role}", role=role)
        self.check(client.request(page, data=data), 303)
        self.check(client.request("profile" if role == "candidate" else "workspace"), 200)
        return client

    def vacancy(self, recruiter, publish=True):
        headers, _ = self.check(recruiter.request("edit-job", data=JOB), 303)
        job_id = int(urlsplit(headers["Location"]).query.split("id=")[1])
        if publish:
            self.check(recruiter.request("job-state", {"id": job_id}, {"status": "Published"}), 303)
        return job_id

    def application(self, candidate, job_id):
        self.check(candidate.request("upload-cv", data={}, file=("original.txt", CV)), 303)
        self.check(candidate.request("apply", {"id": job_id}, {}), 303)
        with sqlite3.connect(self.db) as db:
            return db.execute("SELECT id FROM applications WHERE job_id=?", (job_id,)).fetchone()[0]

