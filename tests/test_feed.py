import json

from support import Client, HttpCase

class FeedTests(HttpCase):
    def test_feed_exposes_publication_data_only(self):
        recruiter = self.account('recruiter')
        public = Client(self.base)
        draft = self.vacancy(recruiter,publish=False)
        def jobs():
            headers, body = self.check(public.request('feed'),200)
            self.assertEqual(headers.get_content_type(),'application/json')
            data = json.loads(body)
            self.assertEqual(data['currency'],'GBP')
            return {job['id']: job for job in data['jobs']}
        self.assertNotIn(draft,jobs())
        self.check(recruiter.request('job-state',{'id':draft},{'status':'Published'}),303)
        job = jobs()[draft]
        self.assertEqual(job['pay'],600)
        self.assertNotIn('email',job)
        self.check(recruiter.request('job-state',{'id':draft},{'status':'Closed'}),303)
        self.assertNotIn(draft,jobs())
