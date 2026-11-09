import hashlib
import hmac
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
import json
import os
import subprocess
import threading

from support import HttpCase, ROOT

class WebhookTests(HttpCase):
    def test_signed_delivery_resumes_after_failure(self):
        recruiter = self.account('recruiter')
        job = self.vacancy(recruiter)
        self.check(recruiter.request('job-state',{'id':job},{'status':'Closed'}),303)
        received = []
        secret = 'test-only-webhook-secret-123'
        class Receiver(BaseHTTPRequestHandler):
            def do_POST(self):
                body = self.rfile.read(int(self.headers['Content-Length']))
                received.append((body,self.headers['X-SignalHire-Signature']))
                self.send_response(500 if len(received)==1 else 204)
                self.end_headers()
            def log_message(self, *args):
                pass
        with ThreadingHTTPServer(('127.0.0.1',0),Receiver) as listener:
            thread = threading.Thread(target=listener.serve_forever,daemon=True)
            thread.start()
            try:
                env = dict(os.environ,SIGNALHIRE_DB=str(self.db),SIGNALHIRE_WEBHOOK_SECRET=secret,
                           SIGNALHIRE_WEBHOOK_URL=f'http://127.0.0.1:{listener.server_port}/events')
                def dispatch():
                    return subprocess.run(['php','bin/dispatch-webhooks.php'],cwd=ROOT,env=env,capture_output=True,timeout=15)
                self.assertEqual(dispatch().returncode,1)
                self.assertIn(b'2 events delivered',dispatch().stdout)
                self.assertIn(b'0 events delivered',dispatch().stdout)
            finally:
                listener.shutdown()
                thread.join()
        self.assertEqual(len(received),3)
        self.assertEqual(received[0],received[1])
        self.assertEqual([json.loads(item[0])['event'] for item in received],['job.published','job.published','job.closed'])
        for body, signature in received:
            self.assertEqual(signature,'sha256='+hmac.new(secret.encode(),body,hashlib.sha256).hexdigest())
            self.assertEqual(json.loads(body)['job_id'],job)
