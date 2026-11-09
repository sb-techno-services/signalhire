<?php
declare(strict_types=1);

function saveCompany(): void
{
    global $db,$user;
    requireRole('recruiter');
    $website = field('website',300,false);
    if ($website && (!filter_var($website,FILTER_VALIDATE_URL) || !in_array(parse_url($website,PHP_URL_SCHEME),['https','http'],true))) {
        fail(422,'Enter a valid HTTP or HTTPS website.');
    }
    $benefits = field('benefits',2000,false);
    $db->beginTransaction();
    try {
        query('INSERT INTO companies(owner_id,name,location,description) VALUES(?,?,?,?)
               ON CONFLICT(owner_id) DO UPDATE SET name=excluded.name,location=excluded.location,description=excluded.description',
            [$user['id'],field('name',140),field('location',100),field('description',4000)]);
        query('INSERT INTO company_details(company_id,website,benefits) VALUES(?,?,?)
               ON CONFLICT(company_id) DO UPDATE SET website=excluded.website,benefits=excluded.benefits',
            [company()['id'],$website,$benefits]);
        $db->commit();
    } catch (Throwable $error) {
        $db->rollBack();
        throw $error;
    }
    redirect('workspace','Company profile saved.');
}
