import sqlite3

from support import HttpCase

class BillingTests(HttpCase):
    def test_credits_are_debited_once_and_balance_is_enforced(self):
        recruiter = self.account('recruiter')
        self.check(recruiter.request('credits-add',data={}),303)
        self.assertIn(b'10 demo credits',self.check(recruiter.request('billing'),200)[1])
        first = self.vacancy(recruiter)
        self.check(recruiter.request('job-state',{'id':first},{'status':'Published'}),409)
        self.assertIn(b'9 demo credits',self.check(recruiter.request('billing'),200)[1])
        for _ in range(9):
            self.vacancy(recruiter)
        draft = self.vacancy(recruiter,publish=False)
        self.check(recruiter.request('job-state',{'id':draft},{'status':'Published'}),409)
        with sqlite3.connect(self.db) as db:
            self.assertEqual(db.execute('SELECT SUM(amount) FROM credit_ledger').fetchone(),(0,))
            self.assertEqual(db.execute('SELECT status FROM jobs WHERE id=?',(draft,)).fetchone(),('Draft',))
        self.check(recruiter.request('job-state',{'id':first},{'status':'Closed'}),303)
        self.check(recruiter.request('credits-add',data={}),303)
        self.check(recruiter.request('job-state',{'id':draft},{'status':'Published'}),303)
