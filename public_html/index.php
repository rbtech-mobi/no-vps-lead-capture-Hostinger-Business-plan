<?php
/**
 * Green River Enterprise Core - Root Language Dispatcher
 * Automatically detects visitor locale and routes to the appropriate campaign:
 * - Spanish: /es/
 * - Default: /en/
 */

$requestedLang = $_GET['lang'] ?? '';
if (!empty($requestedLang)) {
    $target = (strtolower(trim($requestedLang)) === 'es') ? 'es/' : 'en/';
    header("Location: {$target}", true, 302);
    exit;
}

$browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);
if (strtolower($browserLang) === 'es') {
    header('Location: es/', true, 302);
} else {
    header('Location: en/', true, 302);
}
exit;
