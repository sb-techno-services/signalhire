<?php
declare(strict_types=1);

require dirname(__DIR__) . '/app/core.php';
require dirname(__DIR__) . '/app/actions.php';
require dirname(__DIR__) . '/app/views.php';
require dirname(__DIR__) . '/app/routes.php';
require dirname(__DIR__) . '/app/features/load.php';

ini_set('display_errors', '0');
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header("Content-Security-Policy: default-src 'none'; style-src 'self'; form-action 'self'; base-uri 'none'; frame-ancestors 'none'");
$user = false;

try {
    session_cache_limiter('');
    if (!session_start([
        'use_strict_mode'=>1, 'use_only_cookies'=>1, 'cookie_httponly'=>1,
        'cookie_samesite'=>'Lax', 'cookie_secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ])) {
        throw new RuntimeException('The configured PHP session directory is unavailable.');
    }
    $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    $db = database();
    $user = isset($_SESSION['user_id']) ? one('SELECT id,role,name,email,location,summary FROM users WHERE id=?', [$_SESSION['user_id']]) : false;
    $page = input($_GET, 'page') ?: 'jobs';
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'POST' && empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 2097152) {
        fail(413, 'The upload is too large. The CV limit is 2 MB.');
    }
    if ($method === 'GET' && !in_array($page,['cv','feed'],true)) {
        ob_start();
    }
    $title = dispatch($page,$method);
    $body = ob_get_clean();
    layout($title, $body);
} catch (Throwable $error) {
    if (ob_get_level()) {
        ob_end_clean();
    }
    if ($error instanceof RequestError) {
        http_response_code($error->getCode());
        $message = $error->getMessage();
    } else {
        error_log((string) $error);
        http_response_code(500);
        $message = 'Something went wrong. Check the server log and local setup.';
    }
    layout('Request could not be completed', '<section class="panel"><p>' . esc($message)
        . '</p><p><a href="/">Return to vacancies</a></p></section>');
}
