SELECT j.*, (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.id) AS total
FROM jobs j
WHERE j.company_id = :company
ORDER BY j.id DESC
