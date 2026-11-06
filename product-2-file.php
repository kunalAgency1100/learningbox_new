<?php
require __DIR__ . '/config/db.php';

$token = is_string($_GET['token'] ?? null) ? $_GET['token'] : '';
$file = is_string($_GET['file'] ?? null) ? $_GET['file'] : '';
$mode = ($_GET['mode'] ?? '') === 'view' ? 'inline' : 'attachment';
$files = [
    'pre-read' => 'LB 2 - 1 - Pre-Read Guide for Managers.pdf',
    'conversation' => 'LB 2 - 3 - Manager Conversation Guide.pdf',
    'workbook' => 'LB 2 - 4 - Manager Reflection & Practice Workbook.pdf',
    'lb2-5' => 'lb2-5.pdf',
];

if (!preg_match('/^[a-f0-9]{64}$/D', $token) || !isset($files[$file])) {
    http_response_code(404);
    exit('File not found.');
}

$stmt = $pdo->prepare('SELECT p.status, p.product_name, pr.slug AS product_slug FROM purchases p JOIN products pr ON pr.id=p.product_id WHERE p.token=? LIMIT 1');
$stmt->execute([$token]);
$purchase = $stmt->fetch();
if (!$purchase || $purchase['status'] !== 'paid' || $purchase['product_slug'] !== 'digital-playbook-for-managers') {
    http_response_code(403);
    exit('This file is available after payment confirmation.');
}

$path = __DIR__ . '/products/product_02/' . $files[$file];
if (!is_file($path)) {
    http_response_code(404);
    exit('File not found.');
}

header('Content-Type: application/pdf');
header('Content-Disposition: ' . $mode . '; filename="' . basename($path) . '"');
header('Content-Length: ' . (string) filesize($path));
header('Cache-Control: private, no-store');
readfile($path);
