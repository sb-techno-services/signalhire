import sqlite3
import subprocess
import os

from support import HttpCase, ROOT, JOB

class AlertTests(HttpCase):
    def test_alerts_match_new_publications_and_respect_ownership(self):
        recruiter = self.account('recruiter')
        self.vacancy(recruiter)
        candidate = self.account('candidate')
        other = self.account('candidate',fresh=True)
        self.check(candidate.request('alert-add',data={'keyword':'Java','location':'Reading','employment':'Contract'}),303)
        _, before = self.check(candidate.request('alerts'),200)
        self.assertNotIn(JOB['title'].encode(),before)
        new_job = self.vacancy(recruiter)
        completed = subprocess.run(['php','bin/run-alerts.php'],cwd=ROOT,env=dict(os.environ,SIGNALHIRE_DB=str(self.db)),
                                   check=True,capture_output=True)
        self.assertIn(b'1 new alert matches',completed.stdout)
        _, after = self.check(candidate.request('alerts'),200)
        self.assertIn(JOB['title'].encode(),after)
        with sqlite3.connect(self.db) as db:
            alert, = db.execute('SELECT id FROM alerts').fetchone()
            self.assertEqual(db.execute('SELECT job_id FROM alert_matches').fetchall(),[(new_job,)])
        self.check(other.request('alert-remove',{'id':alert},{}),404)
        self.check(candidate.request('alert-remove',{'id':alert},{}),303)
        _, removed = self.check(candidate.request('alerts'),200)
        self.assertNotIn(JOB['title'].encode(),removed)
