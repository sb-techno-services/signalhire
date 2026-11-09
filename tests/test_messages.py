from support import Client, HttpCase

class MessageTests(HttpCase):
    def test_only_application_participants_can_exchange_messages(self):
        recruiter = self.account('recruiter')
        candidate = self.account('candidate')
        outsider = self.account('candidate',fresh=True)
        other_recruiter = self.account('recruiter',fresh=True)
        job = self.vacancy(recruiter)
        application = self.application(candidate,job)
        params = {'id':application}
        self.check(recruiter.request('message-send',params,{'message':'Can you attend on Monday?'}),303)
        self.check(candidate.request('message-send',params,{'message':'Yes. <script>alert(1)</script>'}),303)
        self.check(recruiter.request('application-status',params,{'status':'Interview'}),303)
        for member in (candidate,recruiter):
            _, body = self.check(member.request('thread',params),200)
            for item in (b'Can you attend on Monday?',b'&lt;script&gt;',b'Submitted',b'Interview'):
                self.assertIn(item,body)
            self.assertNotIn(b'<script>',body)
        for non_member in (outsider,other_recruiter):
            self.check(non_member.request('thread',params),404)
            self.check(non_member.request('message-send',params,{'message':'Unauthorized'}),404)
        self.check(Client(self.base).request('thread',params),401)
