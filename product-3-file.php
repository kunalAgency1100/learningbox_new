<?php
require __DIR__ . '/config/db.php';

$token = is_string($_GET['token'] ?? null) ? $_GET['token'] : '';
$file = is_string($_GET['file'] ?? null) ? $_GET['file'] : '';
$mode = ($_GET['mode'] ?? '') === 'view' ? 'inline' : 'attachment';
$files = [
    'toolkit' => 'LB 3 - 1 - Workplace Navigation & Growth Toolkit.pdf',
    'what-not-to-say' => 'LB 3 - 3 - What Not to Say.pdf',
    'scripts' => 'LB 3 - Workplace Script Handout.pdf',
];

if (!preg_match('/^[a-f0-9]{64}$/D', $token) || !isset($files[$file])) {
    http_response_code(404);
    exit('File not found.');
}

$stmt = $pdo->prepare('SELECT status, product_name FROM purchases WHERE token=? LIMIT 1');
$stmt->execute([$token]);
$purchase = $stmt->fetch();
if (!$purchase || $purchase['status'] !== 'paid' || $purchase['product_name'] !== 'Gen Z @ Work Toolkit') {
    http_response_code(403);
    exit('This file is available after payment confirmation.');
}

$path = __DIR__ . '/products/product_03/' . $files[$file];
if (!is_file($path)) {
    http_response_code(404);
    exit('File not found.');
}

header('Content-Type: application/pdf');
header('Content-Disposition: ' . $mode . '; filename="' . basename($path) . '"');
header('Content-Length: ' . (string) filesize($path));
header('Cache-Control: private, no-store');
readfile($path);
