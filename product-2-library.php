<?php
require __DIR__ . '/includes/commerce-layout.php';
commerce_session();
header('Cache-Control: no-store');
require __DIR__ . '/config/db.php';

$token = is_string($_GET['token'] ?? null) ? $_GET['token'] : '';
$stmt = $pdo->prepare('SELECT p.*, pr.slug AS product_slug FROM purchases p JOIN products pr ON pr.id=p.product_id WHERE p.token=? LIMIT 1');
$stmt->execute([$token]);
$purchase = $stmt->fetch();
if (!$purchase || $purchase['status'] !== 'paid' || $purchase['product_slug'] !== 'digital-playbook-for-managers') {
    http_response_code(403);
    page_start('Product 2 access unavailable');
    ?>
    <section class="mx-auto max-w-2xl" aria-labelledby="access-message">
      <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="bg-brand-navy px-6 py-8 text-white sm:px-10 sm:py-10">
          <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary text-brand-navy" aria-hidden="true">
            <span class="material-symbols-outlined text-3xl">lock</span>
          </div>
          <p class="mb-3 text-sm font-bold uppercase tracking-[0.18em] text-blue-200">Playbook library</p>
          <h2 id="access-message" class="font-display text-2xl font-bold sm:text-3xl">Product 2 access unavailable</h2>
        </div>
        <div class="px-6 py-8 sm:px-10 sm:py-9">
          <p class="max-w-xl text-lg leading-8 text-slate-600">Please complete and confirm your Product 2 payment before opening the playbook library.</p>
          <a href="checkout.php?slug=digital-playbook-for-managers" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-3 font-bold text-brand-navy transition hover:bg-primary-hover hover:text-white focus:outline-none focus:ring-4 focus:ring-yellow-200">
            Return to Product 2 checkout
            <span class="material-symbols-outlined text-xl" aria-hidden="true">arrow_forward</span>
          </a>
        </div>
      </div>
    </section>
    <?php
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
