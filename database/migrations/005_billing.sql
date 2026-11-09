CREATE TABLE IF NOT EXISTS credit_ledger (
 id INTEGER PRIMARY KEY,
 company_id INTEGER NOT NULL REFERENCES companies(id),
 job_id INTEGER UNIQUE REFERENCES jobs(id),
 amount INTEGER NOT NULL CHECK(amount IN (-1,10)),
 description TEXT NOT NULL,
 recorded_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CHECK ((amount=-1 AND job_id IS NOT NULL) OR (amount=10 AND job_id IS NULL))
);
CREATE INDEX IF NOT EXISTS company_credits ON credit_ledger(company_id,id);
