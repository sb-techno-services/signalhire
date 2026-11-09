<?php
declare(strict_types=1);

function addDemoCredits(): void
{
    requireRole('recruiter');
    $company = company();
    if (!$company) {
        fail(409,'Create a company before adding demo credits.');
    }
    query("INSERT INTO credit_ledger(company_id,amount,description) VALUES(?,10,'Demo top-up')", [$company['id']]);
    redirect('billing','10 demo credits added.');
}

function changeJobStatus(int $id): void
{
    global $db;
    requireRole('recruiter');
    $job = ownedJob($id);
    $status = choice('status', ['Published','Closed']);
    $expected = $status === 'Published' ? 'Draft' : 'Published';
    $db->exec('BEGIN IMMEDIATE');
    try {
        $balance = one('SELECT COALESCE(SUM(amount),0) AS balance,COUNT(*) AS entries FROM credit_ledger WHERE company_id=?',
            [$job['company_id']]);
        if ($status === 'Published' && $balance['entries'] && $balance['balance'] < 1) {
            fail(409,'Add demo credits to publish this vacancy.');
        }
        $updated = query("UPDATE jobs SET status=?, published_at=CASE WHEN ?='Published' THEN CURRENT_TIMESTAMP ELSE published_at END
                          WHERE id=? AND company_id=? AND status=?", [$status,$status,$id,$job['company_id'],$expected]);
        if (!$updated->rowCount()) {
            fail(409,'That vacancy state change is no longer available.');
        }
        if ($status === 'Published' && $balance['entries']) {
            query("INSERT INTO credit_ledger(company_id,job_id,amount,description) VALUES(?,?,-1,'Vacancy published')",
                [$job['company_id'],$id]);
        }
        $db->exec('COMMIT');
    } catch (Throwable $error) {
        $db->exec('ROLLBACK');
        throw $error;
    }
    redirect('workspace','Vacancy ' . strtolower($status) . '.');
}
