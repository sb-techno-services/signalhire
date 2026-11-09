import os
import sqlite3
import subprocess
import tempfile
from pathlib import Path
import unittest

from support import ROOT

class MigrationTests(unittest.TestCase):
    def test_existing_application_and_cv_survive_upgrade(self):
        with tempfile.TemporaryDirectory() as scratch:
            path = Path(scratch)/'legacy.sqlite'
            with sqlite3.connect(path) as db:
                db.executescript((ROOT/'database/schema.sql').read_text())
                db.executescript("""
                    INSERT INTO users(id,role,email,password,name) VALUES(1,'candidate','c@example.test','unused','C');
                    INSERT INTO users(id,role,email,password,name) VALUES(2,'recruiter','r@example.test','unused','R');
                    INSERT INTO companies(id,owner_id,name,location,description) VALUES(1,2,'Company','London','Demo');
                    INSERT INTO jobs(id,company_id,title,location,employment,arrangement,pay,description,status)
                    VALUES(1,1,'Engineer','London','Contract','Hybrid',600,'Demo','Published');
                    INSERT INTO cvs(id,candidate_id,filename,mime,bytes,current) VALUES(1,1,'cv.txt','text/plain',X'4356',1);
                    INSERT INTO applications(id,job_id,candidate_id,cv_id,status) VALUES(1,1,1,1,'Interview');
                """)
            for _ in range(2):
                subprocess.run(['php','bin/setup.php','--demo'],cwd=ROOT,env=dict(os.environ,SIGNALHIRE_DB=str(path)),
                               check=True,capture_output=True)
            with sqlite3.connect(path) as db:
                self.assertEqual(db.execute('SELECT status FROM application_events').fetchall(),[('Interview',)])
                self.assertEqual(db.execute('SELECT bytes FROM cvs').fetchall(),[(b'CV',)])
                self.assertEqual(db.execute('SELECT COUNT(*) FROM users').fetchone(),(2,))
                self.assertEqual(db.execute('PRAGMA foreign_key_check').fetchall(),[])
