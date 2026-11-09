SELECT j.id,j.title,j.location,j.employment,j.arrangement,j.pay,j.description,j.published_at,
       c.name AS company
FROM jobs j
JOIN companies c ON c.id = j.company_id
WHERE j.status = 'Published'
ORDER BY j.id DESC
LIMIT 500
