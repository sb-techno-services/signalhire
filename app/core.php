<?php
declare(strict_types=1);

function database(bool $setup = false): PDO
{
    $path = getenv('SIGNALHIRE_DB') ?: dirname(__DIR__) . '/storage/signalhire.sqlite';
    if (!str_starts_with($path, DIRECTORY_SEPARATOR)) {
        throw new RuntimeException('SIGNALHIRE_DB must be an absolute path.');
    }
    umask(0077);
    if ($setup && !is_dir(dirname($path))) {
        mkdir(dirname($path), 0700, true);
    }
    if (!$setup && !is_file($path)) {
        throw new RuntimeException('Run php bin/setup.php first.');
    }
    $directory = realpath(dirname($path));
    $public = realpath(dirname(__DIR__) . '/public');
    $resolved = realpath($path);
    if (!$directory || $directory === $public || str_starts_with($directory, $public . '/')
        || ($resolved && str_starts_with($resolved, $public . '/'))) {
        throw new RuntimeException('Use a private database path outside public/.');
    }
    $db = new PDO('sqlite:' . $path, options: [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    $db->exec('PRAGMA foreign_keys = ON; PRAGMA busy_timeout = 5000');
    return $db;
}

function query(string $sql, array $values = []): PDOStatement
{
    global $db;
    $stmt = $db->prepare($sql);
    $stmt->execute($values);
    return $stmt;
}

function one(string $sql, array $values = []): array|false
{
    return query($sql, $values)->fetch();
}

function esc(string|int|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $page, array $params = []): string
{
    return '/?' . http_build_query(['page' => $page] + $params);
}

function redirect(string $page, string $message = '', array $params = []): void
{
    if ($message !== '') {
        $_SESSION['notice'] = $message;
    }
    header('Location: ' . url($page, $params), true, 303);
    exit;
}

class RequestError extends RuntimeException {}
function fail(int $status, string $message): void
{
    throw new RequestError($message, $status);
}

function input(array $source, string $key): string
{
    return is_string($source[$key] ?? null) ? trim($source[$key]) : '';
}

function field(string $key, int $max, bool $required = true): string
{
    $value = input($_POST, $key);
    if (($required && $value === '') || strlen($value) > $max || !preg_match('//u', $value)) {
        fail(422, 'Check the field ' . str_replace('_', ' ', $key) . '. Maximum length: ' . $max . ' bytes.');
    }
    return $value;
}

function choice(string $key, array $allowed): string
{
    $value = input($_POST, $key);
    if (!in_array($value, $allowed, true)) {
        fail(422, 'Select a valid ' . $key . '.');
    }
    return $value;
}

function recordId(): int
{
    $id = filter_var(input($_GET, 'id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false ? 0 : $id;
}

function requireRole(string $role): void
{
    global $user;
    if (!$user) {
        fail(401, 'Sign in to access this page.');
    }
    if ($user['role'] !== $role) {
        fail(403, 'This action requires a ' . $role . ' account.');
    }
}

function company(): array|false
{
    global $user;
    return one('SELECT * FROM companies WHERE owner_id = ?', [$user['id']]);
}

function ownedJob(int $id): array
{
    global $user;
    $job = one('SELECT j.* FROM jobs j JOIN companies c ON c.id=j.company_id
                WHERE j.id=? AND c.owner_id=?', [$id, $user['id']]);
    return $job ?: fail(404, 'Job not found.');
}

function ownedApplication(int $id): array
{
    global $user;
    $application = one('SELECT a.* FROM applications a JOIN jobs j ON j.id=a.job_id
                       JOIN companies c ON c.id=j.company_id WHERE a.id=? AND c.owner_id=?',
                       [$id, $user['id']]);
    return $application ?: fail(404, 'Application not found.');
}

const EMPLOYMENT = ['Permanent', 'Contract'];
const ARRANGEMENTS = ['On-site', 'Hybrid', 'Remote'];
const STATUSES = ['Submitted', 'In review', 'Interview', 'Offered', 'Unsuccessful'];

function payLabel(array $job): string
{
    return '£' . number_format($job['pay']) . ($job['employment'] === 'Contract' ? ' per day' : ' per year');
}

function namedQuery(string $name, array $values = []): PDOStatement
{
    // Names are literals supplied by application code.
    $sql = file_get_contents(__DIR__ . '/queries/' . $name . '.sql');
    if ($sql === false) {
        throw new RuntimeException('A packaged SQL query is missing.');
    }
    return query($sql,$values);
}
