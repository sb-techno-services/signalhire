from support import Client, CV, HttpCase

class DirectoryTests(HttpCase):
    def test_discovery_opt_in_and_revocation(self):
        candidate = self.account('candidate')
        recruiter = self.account('recruiter')
        self.check(candidate.request('upload-cv',data={},file=('private.txt',CV)),303)
        _, before = self.check(recruiter.request('directory'),200)
        self.assertNotIn(b'Alex Morgan',before)
        self.check(candidate.request('discovery',data={'skills':'Java and Terraform','searchable':'1'}),303)
        _, results = self.check(recruiter.request('directory',{'keyword':'Terraform','location':'London'}),200)
        self.assertIn(b'Alex Morgan',results)
        self.assertNotIn(b'candidate@example.test',results)
        self.assertNotIn(b'private.txt',results)
        self.check(candidate.request('directory'),403)
        self.check(Client(self.base).request('directory'),401)
        self.check(candidate.request('discovery',data={'skills':'Java and Terraform'}),303)
        _, after = self.check(recruiter.request('directory'),200)
        self.assertNotIn(b'Alex Morgan',after)
