SELECT l.amount,l.description,l.recorded_at,j.title
FROM credit_ledger l
LEFT JOIN jobs j ON j.id = l.job_id
WHERE l.company_id = :company
ORDER BY l.id DESC
