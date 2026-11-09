<?php
declare(strict_types=1);

const GET_ROUTES = [
    'jobs','job','login','register','profile','applications','company','workspace','edit-job','applicants',
    'saved','alerts','discovery','directory','thread','billing','feed','cv',
];

const POST_ROUTES = [
    'login','register','logout','profile','upload-cv','apply','company','edit-job','job-state','application-status',
    'save-job','alert-add','alert-remove','discovery','message-send','credits-add',
];

function dispatch(string $page, string $method): mixed
{
    global $user;
    $id = recordId();
    $route = in_array($page,['login','register'],true) ? 'auth' : $page;
    if ($method === 'GET') {
        if (!in_array($page,GET_ROUTES,true)) {
            fail(404,'Page not found.');
        }
        return require __DIR__ . '/routes/get/' . $route . '.php';
    }
    if ($method === 'POST') {
        if (!hash_equals($_SESSION['csrf'], input($_POST,'csrf'))) {
            fail(403,'The form expired. Open the page again and retry.');
        }
        if (!in_array($page,POST_ROUTES,true)) {
            fail(405,'This page does not accept that action.');
        }
        return require __DIR__ . '/routes/post/' . $route . '.php';
    }
    fail(405,'This request method is unavailable.');
}
