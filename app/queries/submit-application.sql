INSERT INTO applications(job_id,candidate_id,cv_id)
SELECT j.id, cv.candidate_id, cv.id
FROM jobs j
JOIN cvs cv ON cv.candidate_id = :candidate AND cv.current = 1
WHERE j.id = :job AND j.status = 'Published'
ON CONFLICT(job_id,candidate_id) DO NOTHING
