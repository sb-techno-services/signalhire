CREATE TABLE IF NOT EXISTS company_details (
 company_id INTEGER PRIMARY KEY REFERENCES companies(id),
 website TEXT NOT NULL DEFAULT '',
 benefits TEXT NOT NULL DEFAULT ''
);
