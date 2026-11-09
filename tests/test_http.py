import sqlite3
import unittest

from support import Client, CV, HttpCase, JOB


class HiringTests(HttpCase):
    def test_hiring_journey(self):
        recruiter = self.account("recruiter", fresh=True)
        company = {"name":"Willow Circuit", "location":"Reading", "description":"Fictional engineering team."}
        self.check(recruiter.request("company", data=company), 303)
        self.check(recruiter.request("company", data={**company, "name":"Willow Circuit Labs"}), 303)
        job_id = self.vacancy(recruiter, publish=False)
        public = Client(self.base)
        self.check(public.request("job", {"id": job_id}), 404)
        self.check(recruiter.request("edit-job", {"id": job_id}, {**JOB, "title":"Java Cloud Integration Engineer"}), 303)
        self.check(recruiter.request("job-state", {"id": job_id}, {"status":"Published"}), 303)
        _, search = self.check(public.request(params={"keyword":"JAVA","location":"Reading","employment":"Contract"}), 200)
        self.assertIn(b"Java Cloud Integration Engineer", search)
        self.assertNotIn(b"Senior Java Engineer", search)
        _, job = self.check(public.request("job", {"id":job_id}), 200)
        self.assertIn(b"Willow Circuit Labs", job)
        self.assertIn("£600 per day".encode(), job)

        candidate = self.account("candidate", fresh=True)
        self.check(candidate.request("profile", data={"name":"Alex Example","location":"Reading","summary":"Java and AWS"}), 303)
        application = self.application(candidate, job_id)
        _, applicants = self.check(recruiter.request("applicants", {"id":job_id}), 200)
        self.assertIn(b"Alex Example", applicants)
        headers, downloaded = self.check(recruiter.request("cv", {"id":application}), 200)
        self.assertEqual(downloaded, CV)
        self.assertEqual(headers["Content-Disposition"], 'attachment; filename="original.txt"')
        self.check(candidate.request("upload-cv", data={}, file=("replacement.txt", b"Replacement curriculum vitae.\n")), 303)
        _, downloaded = self.check(recruiter.request("cv", {"id":application}), 200)
        self.assertEqual(downloaded, CV)
        self.check(recruiter.request("application-status", {"id":application}, {"status":"Interview"}), 303)
        _, history = self.check(candidate.request("applications"), 200)
        self.assertIn(b"Interview", history)
        self.assertIn(b"Java Cloud Integration Engineer", history)

    def test_duplicate_application(self):
        candidate = self.account("candidate")
        recruiter = self.account("recruiter")
        job_id = self.vacancy(recruiter)
        self.application(candidate, job_id)
        _, response = self.check(candidate.request("apply", {"id":job_id}, {}), 409)
        self.assertIn(b"already applied", response)
        with sqlite3.connect(self.db) as db:
            self.assertEqual(db.execute("SELECT count(*) FROM applications").fetchone()[0], 1)

    def test_closed_job_rejects_application_and_leaves_search(self):
        candidate = self.account("candidate")
        recruiter = self.account("recruiter")
        job_id = self.vacancy(recruiter)
        self.check(candidate.request("upload-cv", data={}, file=("candidate.txt", CV)), 303)
        self.check(recruiter.request("job-state", {"id":job_id}, {"status":"Closed"}), 303)
        self.check(candidate.request("apply", {"id":job_id}, {}), 409)
        _, search = self.check(candidate.request(params={"keyword":JOB["title"]}), 200)
        self.assertIn(b"0 results", search)
        _, detail = self.check(candidate.request("job", {"id":job_id}), 200)
        self.assertIn(b"This vacancy is closed", detail)
        with sqlite3.connect(self.db) as db:
            self.assertEqual(db.execute("SELECT count(*) FROM applications").fetchone()[0], 0)

    def test_cross_company_and_cross_role_access(self):
        candidate = self.account("candidate")
        recruiter = self.account("recruiter")
        other = self.account("recruiter", fresh=True)
        self.check(other.request("company", data={"name":"Other Studio","location":"York","description":"Fictional company."}), 303)
        job_id = self.vacancy(recruiter)
        application = self.application(candidate, job_id)
        self.check(other.request("applicants", {"id":job_id}), 404)
        self.check(other.request("edit-job", {"id":job_id}, JOB), 404)
        self.check(other.request("application-status", {"id":application}, {"status":"Offered"}), 404)
        self.check(other.request("cv", {"id":application}), 404)
        self.check(candidate.request("cv", {"id":application}), 403)
        self.check(candidate.request("applicants", {"id":job_id}), 403)
        anonymous = Client(self.base)
        self.check(anonymous.request("cv", {"id":application}), 401)
        self.check(anonymous.request("applicants", {"id":job_id}), 401)
        self.check(anonymous.request("applications"), 401)
        another_candidate = self.account("candidate", fresh=True)
        _, history = self.check(another_candidate.request("applications"), 200)
        self.assertNotIn(JOB["title"].encode(), history)
        _, history = self.check(candidate.request("applications"), 200)
        self.assertIn(b"Submitted", history)

    def test_validation_and_csrf(self):
        candidate = self.account("candidate")
        recruiter = self.account("recruiter")
        job_id = self.vacancy(recruiter)
        self.check(candidate.request("apply", {"id":job_id}, {}), 422)
        self.check(candidate.request("upload-cv", data={}, file=("bad.pdf", CV)), 422)
        self.check(candidate.request("upload-cv", data={}, file=("bad.txt", b"MZ\x00\x01payload")), 422)
        self.check(candidate.request("upload-cv", data={}, file=("large.txt", b"A" * 2097153)), 422)
        self.check(candidate.request("upload-cv", data={}, file=("valid.txt", CV), csrf=False), 403)
        self.check(candidate.request("apply", {"id":job_id}, {}, csrf=False), 403)


if __name__ == "__main__":
    unittest.main(verbosity=2)
