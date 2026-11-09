<?php
declare(strict_types=1);

function authAction(string $page): void
{
    global $user, $db;
    if ($user) {
        redirect('jobs');
    }
    $email = strtolower(field('email', 254));
    $password = $_POST['password'] ?? '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !is_string($password)
        || strlen($password) > 72 || str_contains($password, "\0")) {
        fail(422, 'Check your email address and password.');
    }
    if ($page === 'register') {
        $name = field('name', 100);
        $role = choice('role', ['candidate','recruiter']);
        if (strlen($password) < 10) {
            fail(422, 'Use a password of at least 10 characters.');
        }
        $created = query('INSERT INTO users(role,email,password,name) VALUES(?,?,?,?) ON CONFLICT(email) DO NOTHING',
            [$role,$email,password_hash($password, PASSWORD_DEFAULT),$name]);
        if ($created->rowCount() === 0) {
            fail(409, 'An account already uses this email address.');
        }
        $id = (int) $db->lastInsertId();
    } else {
        $account = one('SELECT id, role, password FROM users WHERE email=?', [$email]);
        if (!$account || !password_verify($password, $account['password'])) {
            fail(401, 'Email address or password is incorrect.');
        }
        $id = (int) $account['id'];
        $role = $account['role'];
    }
    session_regenerate_id(true);
    $_SESSION = ['user_id' => $id, 'csrf' => bin2hex(random_bytes(32))];
    $nextJob = recordId();
    if ($role === 'candidate' && $nextJob) {
        redirect('job', '', ['id'=>$nextJob]);
    }
    redirect($role === 'candidate' ? 'jobs' : 'workspace', 'You’re signed in.');
}

function saveCv(): void
{
    global $db, $user;
    $file = $_FILES['cv'] ?? [];
    if (($file['error'] ?? null) !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        fail(422, 'Choose a PDF or plain text CV up to 2 MB.');
    }
    $tmp = $file['tmp_name'];
    $size = filesize($tmp);
    if ($size === false || $size < 1 || $size > 2097152) {
        fail(422, 'The CV must be between 1 byte and 2 MB.');
    }
    $name = basename(str_replace('\\', '/', $file['name']));
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
    $bytes = file_get_contents($tmp);
    if ($bytes === false) {
        fail(422, 'The uploaded CV could not be read.');
    }
    $validPdf = $ext === 'pdf' && $mime === 'application/pdf'
        && str_starts_with($bytes, '%PDF-') && str_contains(substr($bytes, -2048), '%%EOF');
    $validText = $ext === 'txt' && $mime === 'text/plain' && preg_match('//u', $bytes)
        && !preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $bytes);
    if (!$validPdf && !$validText) {
        fail(422, 'Upload a valid PDF or UTF-8 plain text file.');
    }
    $stem = preg_replace('/[^a-zA-Z0-9_.-]/', '_', pathinfo($name, PATHINFO_FILENAME));
    $name = (trim(substr($stem, 0, 90), '.') ?: 'cv') . '.' . $ext;
    $db->beginTransaction();
    try {
        query('UPDATE cvs SET current=0 WHERE candidate_id=? AND current=1', [$user['id']]);
        $insert = $db->prepare('INSERT INTO cvs(candidate_id,filename,mime,bytes,current) VALUES(?,?,?,?,1)');
        $insert->bindValue(1, $user['id'], PDO::PARAM_INT);
        $insert->bindValue(2, $name);
        $insert->bindValue(3, $mime);
        $insert->bindValue(4, $bytes, PDO::PARAM_LOB);
        $insert->execute();
        query('DELETE FROM cvs WHERE candidate_id=? AND current=0
               AND NOT EXISTS (SELECT 1 FROM applications WHERE cv_id=cvs.id)', [$user['id']]);
        $db->commit();
    } catch (Throwable $error) {
        $db->rollBack();
        throw $error;
    }
    $job = recordId();
    if ($job) {
        redirect('job', 'CV uploaded. Review the vacancy and submit your application.', ['id'=>$job]);
    }
    redirect('profile', 'CV uploaded.');
}

function saveJob(int $id): void
{
    global $db;
    $company = company();
    if (!$company) {
        fail(409, 'Save your company profile before creating a vacancy.');
    }
    if ($id) {
        ownedJob($id);
    }
    $pay = filter_var(input($_POST, 'pay'), FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>9999999]]);
    if ($pay === false) {
        fail(422, 'Enter the pay as a positive whole number in pounds.');
    }
    $fields = [field('title', 140),field('location', 100),choice('employment', EMPLOYMENT),
        choice('arrangement', ARRANGEMENTS),$pay,field('description', 8000)];
    if ($id) {
        query('UPDATE jobs SET title=?,location=?,employment=?,arrangement=?,pay=?,description=? WHERE id=? AND company_id=?',
            [...$fields,$id,$company['id']]);
    } else {
        query('INSERT INTO jobs(title,location,employment,arrangement,pay,description,company_id) VALUES(?,?,?,?,?,?,?)',
            [...$fields,$company['id']]);
        $id = (int) $db->lastInsertId();
    }
    redirect('edit-job', 'Vacancy saved.', ['id'=>$id]);
}

function applyToJob(int $id): void
{
    global $user;
    // SQLite evaluates eligibility and inserts the application in one write statement.
    $result = namedQuery('submit-application',['candidate'=>$user['id'],'job'=>$id]);
    if ($result->rowCount() === 1) {
        redirect('applications', 'Application submitted.');
    }
    $job = one('SELECT status FROM jobs WHERE id=? AND status != ?', [$id,'Draft']);
    if (!$job) {
        fail(404, 'Vacancy not found.');
    }
    if ($job['status'] !== 'Published') {
        fail(409, 'This vacancy is closed.');
    }
    if (one('SELECT id FROM applications WHERE candidate_id=? AND job_id=?', [$user['id'],$id])) {
        fail(409, 'You already applied for this vacancy.');
    }
    fail(422, 'Upload a CV before applying.');
}

function downloadCv(int $id): void
{
    requireRole('recruiter');
    $application = ownedApplication($id);
    $cv = one('SELECT filename,mime,bytes FROM cvs WHERE id=? AND candidate_id=?',
        [$application['cv_id'],$application['candidate_id']]);
    if (!$cv) {
        fail(404, 'CV not found.');
    }
    header('Content-Type: ' . $cv['mime']);
    header('Content-Disposition: attachment; filename="' . $cv['filename'] . '"');
    header('Content-Length: ' . strlen($cv['bytes']));
    session_write_close();
    echo $cv['bytes'];
    exit;
}
