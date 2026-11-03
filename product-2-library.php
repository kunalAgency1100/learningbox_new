<?php
require __DIR__ . '/includes/commerce-layout.php';
commerce_session();
header('Cache-Control: no-store');
require __DIR__ . '/config/db.php';

$token = is_string($_GET['token'] ?? null) ? $_GET['token'] : '';
$stmt = $pdo->prepare('SELECT * FROM purchases WHERE token=? LIMIT 1');
$stmt->execute([$token]);
$purchase = $stmt->fetch();
if (!$purchase || $purchase['status'] !== 'paid' || $purchase['product_name'] !== 'Digital Playbook for Managers') {
    http_response_code(403);
    page_start('Product 2 access unavailable');
    echo '<p class="mb-4">Please complete and confirm your Product 2 payment before opening the playbook library.</p>';
    echo '<a href="checkout.php?slug=digital-playbook-for-managers" class="underline">Return to Product 2 checkout</a>';
    page_end();
    exit;
}

$documents = [
    ['key' => 'pre-read', 'name' => 'Pre-Read Guide for Managers'],
    ['key' => 'conversation', 'name' => 'Manager Conversation Guide'],
    ['key' => 'workbook', 'name' => 'Manager Reflection & Practice Workbook'],
    ['key' => 'lb2-5', 'name' => 'Product 2 Bonus Guide'],
];
$base = 'product-2-file.php?token=' . rawurlencode($token);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Product 2 Library | LearningBox</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
  <main class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
    <div class="mb-8 rounded-3xl bg-brand-navy p-7 text-white" style="background:#000976">
      <p class="text-sm font-semibold uppercase tracking-widest text-blue-200">Payment confirmed</p>
      <h1 class="mt-2 text-3xl font-extrabold">Your Digital Playbook for Managers</h1>
      <p class="mt-3 text-blue-100">Read each guide online or download the PDF for later.</p>
    </div>
    <section class="grid gap-8 lg:grid-cols-2" aria-label="Product 2 documents">
      <?php foreach ($documents as $document):
        $viewUrl = $base . '&file=' . rawurlencode($document['key']) . '&mode=view';
        $downloadUrl = $base . '&file=' . rawurlencode($document['key']) . '&mode=download';
      ?>
        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div class="flex items-center justify-between gap-4 border-b border-slate-100 p-5">
            <h2 class="font-bold text-slate-900"><?= esc($document['name']) ?></h2>
            <a class="shrink-0 rounded-lg bg-blue-600 px-3 py-2 text-sm font-bold text-white hover:bg-blue-700" href="<?= esc($downloadUrl) ?>">Download</a>
          </div>
          <iframe class="h-[520px] w-full" src="<?= esc($viewUrl) ?>" title="<?= esc($document['name']) ?>"></iframe>
        </article>
      <?php endforeach; ?>
    </section>
  </main>
</body>
</html>
