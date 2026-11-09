SELECT j.*, c.name AS company
FROM jobs j
JOIN companies c ON c.id = j.company_id
WHERE j.status = 'Published'
  AND instr(lower(j.title || ' ' || j.description || ' ' || c.name), lower(:keyword)) > 0
  AND instr(lower(j.location || ' ' || j.arrangement), lower(:location)) > 0
  AND (:employment = '' OR j.employment = :employment)
  AND (:arrangement = '' OR j.arrangement = :arrangement)
ORDER BY j.published_at DESC, j.id DESC
