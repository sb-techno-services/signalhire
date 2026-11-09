SELECT a.*, j.title, c.name AS company
FROM applications a
JOIN jobs j ON j.id = a.job_id
JOIN companies c ON c.id = j.company_id
WHERE a.id = :application
  AND ((:role = 'candidate' AND a.candidate_id = :user)
    OR (:role = 'recruiter' AND c.owner_id = :user))
