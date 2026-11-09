CREATE TABLE IF NOT EXISTS messages (
 id INTEGER PRIMARY KEY,
 application_id INTEGER NOT NULL REFERENCES applications(id),
 sender_id INTEGER NOT NULL REFERENCES users(id),
 body TEXT NOT NULL,
 sent_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS application_messages ON messages(application_id,id);
