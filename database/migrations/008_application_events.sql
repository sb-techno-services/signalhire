CREATE TABLE IF NOT EXISTS application_events (
 id INTEGER PRIMARY KEY,
 application_id INTEGER NOT NULL REFERENCES applications(id),
 status TEXT NOT NULL,
 changed_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS application_timeline ON application_events(application_id,id);
CREATE TRIGGER IF NOT EXISTS record_application_submission
AFTER INSERT ON applications
BEGIN
 INSERT INTO application_events(application_id,status) VALUES(NEW.id,NEW.status);
END;
CREATE TRIGGER IF NOT EXISTS record_application_status
AFTER UPDATE OF status ON applications
WHEN OLD.status <> NEW.status
BEGIN
 INSERT INTO application_events(application_id,status) VALUES(NEW.id,NEW.status);
END;
INSERT INTO application_events(application_id,status,changed_at)
 SELECT id,status,updated_at FROM applications;
