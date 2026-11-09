CREATE TABLE IF NOT EXISTS publication_events (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 job_id INTEGER NOT NULL REFERENCES jobs(id),
 event TEXT NOT NULL CHECK(event IN ('job.published','job.closed')),
 created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS webhook_deliveries (
 consumer TEXT NOT NULL,
 event_id INTEGER NOT NULL REFERENCES publication_events(id),
 delivered_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(consumer,event_id)
);
CREATE TRIGGER IF NOT EXISTS record_job_status
AFTER UPDATE OF status ON jobs
WHEN OLD.status <> NEW.status AND NEW.status IN ('Published','Closed')
BEGIN
 INSERT INTO publication_events(job_id,event)
 VALUES(NEW.id, CASE NEW.status WHEN 'Published' THEN 'job.published' ELSE 'job.closed' END);
END;
