CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY,
  role TEXT NOT NULL CHECK(role IN ('candidate','recruiter')),
  email TEXT NOT NULL UNIQUE COLLATE NOCASE,
  password TEXT NOT NULL,
  name TEXT NOT NULL,
  location TEXT NOT NULL DEFAULT '',
  summary TEXT NOT NULL DEFAULT ''
);
CREATE TABLE IF NOT EXISTS companies (
  id INTEGER PRIMARY KEY,
  owner_id INTEGER NOT NULL UNIQUE REFERENCES users(id),
  name TEXT NOT NULL,
  location TEXT NOT NULL,
  description TEXT NOT NULL
);
CREATE TABLE IF NOT EXISTS jobs (
  id INTEGER PRIMARY KEY,
  company_id INTEGER NOT NULL REFERENCES companies(id),
  title TEXT NOT NULL,
  location TEXT NOT NULL,
  employment TEXT NOT NULL CHECK(employment IN ('Permanent','Contract')),
  arrangement TEXT NOT NULL CHECK(arrangement IN ('On-site','Hybrid','Remote')),
  pay INTEGER NOT NULL CHECK(pay > 0),
  description TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'Draft' CHECK(status IN ('Draft','Published','Closed')),
  published_at TEXT
);
CREATE TABLE IF NOT EXISTS cvs (
  id INTEGER PRIMARY KEY,
  candidate_id INTEGER NOT NULL REFERENCES users(id),
  filename TEXT NOT NULL,
  mime TEXT NOT NULL,
  bytes BLOB NOT NULL CHECK(length(bytes) BETWEEN 1 AND 2097152),
  current INTEGER NOT NULL CHECK(current IN (0,1)),
  UNIQUE(id, candidate_id)
);
CREATE UNIQUE INDEX IF NOT EXISTS current_cv ON cvs(candidate_id) WHERE current = 1;
CREATE TABLE IF NOT EXISTS applications (
  id INTEGER PRIMARY KEY,
  job_id INTEGER NOT NULL REFERENCES jobs(id),
  candidate_id INTEGER NOT NULL REFERENCES users(id),
  cv_id INTEGER NOT NULL,
  status TEXT NOT NULL DEFAULT 'Submitted'
    CHECK(status IN ('Submitted','In review','Interview','Offered','Unsuccessful')),
  submitted_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(job_id, candidate_id),
  FOREIGN KEY(cv_id, candidate_id) REFERENCES cvs(id, candidate_id)
);
CREATE INDEX IF NOT EXISTS candidate_applications ON applications(candidate_id);
CREATE INDEX IF NOT EXISTS company_jobs ON jobs(company_id);
