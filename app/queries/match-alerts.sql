INSERT INTO alert_matches(alert_id,job_id)
SELECT a.id,j.id
FROM alerts a
JOIN publication_events e ON e.id > a.after_event AND e.event = 'job.published'
JOIN jobs j ON j.id = e.job_id
JOIN companies c ON c.id = j.company_id
WHERE (:candidate IS NULL OR a.candidate_id = :candidate)
  AND j.status = 'Published'
  AND instr(lower(j.title || ' ' || j.description || ' ' || c.name), lower(a.keyword)) > 0
  AND instr(lower(j.location || ' ' || j.arrangement), lower(a.location)) > 0
  AND (a.employment = '' OR a.employment = j.employment)
ON CONFLICT(alert_id,job_id) DO NOTHING
