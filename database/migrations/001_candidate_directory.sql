CREATE TABLE IF NOT EXISTS candidate_visibility (
 candidate_id INTEGER PRIMARY KEY REFERENCES users(id),
 searchable INTEGER NOT NULL CHECK(searchable IN (0,1)),
 skills TEXT NOT NULL DEFAULT ''
);
CREATE INDEX IF NOT EXISTS directory_visibility ON candidate_visibility(searchable);
