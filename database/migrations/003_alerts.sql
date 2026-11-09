CREATE TABLE IF NOT EXISTS alerts (
 id INTEGER PRIMARY KEY,
 candidate_id INTEGER NOT NULL REFERENCES users(id),
 keyword TEXT NOT NULL,
 location TEXT NOT NULL,
 employment TEXT NOT NULL CHECK(employment IN ('','Permanent','Contract')),
 after_event INTEGER NOT NULL DEFAULT 0,
 UNIQUE(candidate_id,keyword,location,employment)
);
CREATE TABLE IF NOT EXISTS alert_matches (
 alert_id INTEGER NOT NULL REFERENCES alerts(id) ON DELETE CASCADE,
 job_id INTEGER NOT NULL REFERENCES jobs(id),
 matched_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(alert_id,job_id)
);
