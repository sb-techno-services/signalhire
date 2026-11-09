from support import HttpCase, JOB

class SavedTests(HttpCase):
    def test_saved_jobs_are_private_and_idempotent(self):
        candidate = self.account('candidate')
        other = self.account('candidate',fresh=True)
        recruiter = self.account('recruiter')
        job = self.vacancy(recruiter)
        for _ in range(2):
            self.check(candidate.request('save-job',{'id':job},{'operation':'save'}),303)
        _, saved = self.check(candidate.request('saved'),200)
        self.assertEqual(saved.count(JOB['title'].encode()),1)
        self.check(other.request('save-job',{'id':job},{'operation':'remove'}),303)
        _, other_saved = self.check(other.request('saved'),200)
        self.assertNotIn(JOB['title'].encode(),other_saved)
        self.check(recruiter.request('job-state',{'id':job},{'status':'Closed'}),303)
        _, closed = self.check(candidate.request('saved'),200)
        self.assertIn(b'Closed',closed)
        self.check(candidate.request('save-job',{'id':job},{'operation':'remove'}),303)
        _, removed = self.check(candidate.request('saved'),200)
        self.assertNotIn(JOB['title'].encode(),removed)
