<?php
declare(strict_types=1);

requireRole('recruiter');
$company = company();
$jobs = $company ? namedQuery('company-workspace',['company'=>$company['id']])->fetchAll() : [];
heading('RECRUITER','Recruiter workspace',$company ? $company['name'] : 'Set up your company to start hiring.');
return require __DIR__ . "/../../templates/workspace.php";
