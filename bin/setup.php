<?php
declare(strict_types=1);
require dirname(__DIR__) . '/app/core.php';
require dirname(__DIR__) . "/app/features/migrations.php";

if (array_diff(array_slice($argv, 1), ['--demo'])) {
    fwrite(STDERR, "Usage: php bin/setup.php [--demo]\n");
    exit(1);
}
$db = database(true);
$db->exec(file_get_contents(dirname(__DIR__) . '/database/schema.sql'));
migrate($db);
if (in_array('--demo', $argv, true)) {
    $db->beginTransaction();
    try {
        if (!one('SELECT id FROM users LIMIT 1')) {
            $password = password_hash('SignalDemo2026!', PASSWORD_DEFAULT);
            foreach ([
                ['candidate', 'candidate@example.test', 'Alex Morgan', 'London', 'Backend developer working with Java and cloud platforms.'],
                ['recruiter', 'recruiter@example.test', 'Jamie Reed', '', ''],
            ] as [$role, $email, $name, $location, $summary]) {
                query('INSERT INTO users(role,email,password,name,location,summary) VALUES(?,?,?,?,?,?)',
                    [$role,$email,$password,$name,$location,$summary]);
            }
            $owner = one('SELECT id FROM users WHERE email=?', ['recruiter@example.test'])['id'];
            query('INSERT INTO companies(owner_id,name,location,description) VALUES(?,?,?,?)',
                [$owner,'Northstar Works','London','A fictional engineering studio building software for UK organisations.']);
            $company = (int) $db->lastInsertId();
            foreach ([
                ['Senior Java Engineer','London','Contract','Hybrid',575,'Help modernise a payments platform using Java, AWS and event-driven services. Two days a week in London.'],
                ['Platform Engineer','Manchester','Permanent','Remote',72000,'Build and maintain deployment tooling for a growing product team. Remote within the UK.'],
                ['Product Designer','Bristol','Permanent','Hybrid',55000,'Design accessible workflows for operational teams. Collaborate with researchers and engineers.'],
            ] as $job) {
                query("INSERT INTO jobs(company_id,title,location,employment,arrangement,pay,description,status,published_at)
                       VALUES(?,?,?,?,?,?,?,'Published',CURRENT_TIMESTAMP)", [$company,...$job]);
            }
            echo "Fictional demo data installed.\n";
        } else {
            echo "Demo seed skipped because the database already has accounts.\n";
        }
        $db->commit();
    } catch (Throwable $error) {
        $db->rollBack();
        throw $error;
    }
}
echo "Database ready.\n";
