from support import HttpCase

class CompanyTests(HttpCase):
    def test_website_and_benefits_are_displayed_and_urls_are_validated(self):
        recruiter = self.account('recruiter')
        fields = {'name':'Example Studio','location':'London','description':'Product development','benefits':'30 days holiday'}
        self.check(recruiter.request('company',data={**fields,'website':'javascript:alert(1)'}),422)
        self.check(recruiter.request('company',data={**fields,'website':'https://example.test'}),303)
        job = self.vacancy(recruiter)
        _, html = self.check(recruiter.request('job',{'id':job}),200)
        self.assertIn(b'30 days holiday',html)
        self.assertIn(b'href="https://example.test"',html)
