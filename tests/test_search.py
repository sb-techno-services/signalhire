from support import Client, HttpCase

class SearchTests(HttpCase):
    def test_work_arrangement_combines_with_employment_and_keyword(self):
        public = Client(self.base)
        _, matches = self.check(public.request(params={'keyword':'engineer','employment':'Permanent','arrangement':'Remote'}),200)
        self.assertIn(b'Platform Engineer',matches)
        self.assertNotIn(b'Senior Java Engineer',matches)
        _, empty = self.check(public.request(params={'keyword':'engineer','employment':'Contract','arrangement':'Remote'}),200)
        self.assertIn(b'0 results',empty)
        self.check(public.request(params={'arrangement':'Invalid'}),422)
