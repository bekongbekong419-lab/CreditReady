<?php
require_once __DIR__ . '/includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $lang = $_POST['lang'] ?? 'en';
    $_SESSION['lang'] = in_array($lang, ['en','fr'], true) ? $lang : 'en';
    $return = $_POST['return'] ?? url('index.php');
    if (!is_string($return) || !str_starts_with($return, '/')) $return = url('index.php');
    header('Location: ' . $return); exit;
}
redirect('index.php');
