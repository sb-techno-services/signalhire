SELECT u.name,u.location,u.summary,v.skills
FROM candidate_visibility v
JOIN users u ON u.id = v.candidate_id
WHERE v.searchable = 1 AND u.role = 'candidate' AND u.summary <> ''
  AND instr(lower(u.summary || ' ' || v.skills), lower(:keyword)) > 0
  AND instr(lower(u.location), lower(:location)) > 0
ORDER BY u.name, u.id
LIMIT 100
