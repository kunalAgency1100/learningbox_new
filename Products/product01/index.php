<?php
require dirname(__DIR__, 2) . '/includes/commerce-layout.php';
commerce_session();
header('Cache-Control: no-store');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="../../">
  <title>Product 1 Preview | LearningBox</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&amp;display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
  <script>
    tailwind.config = { theme: { extend: { colors: { primary: '#F4BD18', 'accent-orange': '#FF731E', 'brand-navy': '#000976', 'accent-blue': '#26AADC' }, fontFamily: { display: ['Montserrat', 'sans-serif'] } } } };
  </script>
  <link href="../../templates/layout.css" rel="stylesheet">
  <link href="../../assets/checkout.css" rel="stylesheet">
  <script src="../../templates/layout.js" defer></script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-800 antialiased">
  <div id="site-header"><?php if (is_file(dirname(__DIR__, 2) . '/templates/header.html')) require dirname(__DIR__, 2) . '/templates/header.html'; ?></div>

  <main class="bg-gray-50 px-4 pb-16 pt-28 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
      <a href="../../flagship.html" class="inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-brand-navy hover:text-accent-orange">
        <span class="material-symbols-outlined text-lg" aria-hidden="true">arrow_back</span> Back to flagship products
      </a>
      <div class="mt-6 space-y-8">
        <section class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm" aria-labelledby="preview-heading">
          <div class="h-1.5 bg-gradient-to-r from-primary to-accent-orange"></div>
          <div class="p-6 sm:p-8">
            <p class="text-xs font-bold uppercase tracking-widest text-accent-orange">Product 1 · Preview</p>
            <h1 id="preview-heading" class="mt-3 font-display text-3xl font-extrabold leading-tight text-brand-navy sm:text-4xl">Relationship Health Diagnostic</h1>
            <p class="mt-4 leading-7 text-gray-600">Read the Product 1 handout below. When you are ready, submit your details to request the ₹1 verification payment.</p>
            <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-gray-100">
              <iframe title="Product 1 PDF preview" src="../../products/product_01/LB%2001%20-%201%20-%20Workplace%20Relationship%20Assessment%20GENERIC.pdf" class="h-[560px] w-full sm:h-[700px]"></iframe>
            </div>
            <a href="../../products/product_01/LB%2001%20-%201%20-%20Workplace%20Relationship%20Assessment%20GENERIC.pdf" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-brand-navy underline underline-offset-4">
              Open PDF in a new tab <span class="material-symbols-outlined text-base" aria-hidden="true">open_in_new</span>
            </a>
            <button id="report-button" type="button" class="mt-6 flex w-full items-center justify-center rounded-xl bg-brand-navy px-5 py-4 font-bold text-white transition hover:bg-accent-blue sm:w-fit">
              See Personalised Insight Report · INR 1
            </button>
            <p class="mt-3 text-xs text-gray-500">Payment is required before the personalised report form opens.</p>
          </div>
        </section>

        <section id="google-form" class="hidden overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm" aria-labelledby="google-form-heading">
          <div class="h-1.5 bg-gradient-to-r from-accent-blue to-brand-navy"></div>
          <div class="p-6 sm:p-8">
            <p class="text-xs font-bold uppercase tracking-widest text-accent-orange">Step 2</p>
            <h2 id="google-form-heading" class="mt-2 font-display text-2xl font-bold text-brand-navy">Complete the Product 1 form</h2>
            <p class="mt-3 text-sm leading-6 text-gray-500">Submit the form below, then continue to the INR 1 verification payment.</p>
            <iframe title="Product 1 Google Form" src="https://docs.google.com/forms/d/1WRr4K0HnhTzzEvXnwEMtuRFreTHoOpJFMd63v0K3CM8/viewform?embedded=true" class="mt-6 h-[900px] w-full rounded-2xl border border-gray-200">Loading…</iframe>
          </div>
        </section>

        <form id="purchase-form" class="hidden rounded-3xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8" data-slug="product01" data-api-base="../../api/payments.php" data-status-base="../../payment-success.php" data-success-url="https://docs.google.com/forms/d/1WRr4K0HnhTzzEvXnwEMtuRFreTHoOpJFMd63v0K3CM8/viewform" data-csrf="<?= esc($_SESSION['commerce_csrf']) ?>" aria-labelledby="details-heading">
          <div class="border-b border-gray-100 pb-6">
            <p class="text-xs font-bold uppercase tracking-widest text-accent-orange">Step 3</p>
            <h2 id="details-heading" class="mt-2 font-display text-2xl font-bold text-brand-navy">Request the ₹1 payment</h2>
            <p class="mt-3 text-sm leading-6 text-gray-500">Enter your details. Razorpay will open after you submit the form. The amount is fixed server-side at ₹1.</p>
          </div>
          <div class="mt-6 space-y-5">
            <div><label for="name" class="mb-2 block text-sm font-semibold">Full name</label><input id="name" name="name" type="text" autocomplete="name" maxlength="120" required placeholder="Enter your full name" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3"></div>
            <div><label for="email" class="mb-2 block text-sm font-semibold">Email address</label><input id="email" name="email" type="email" autocomplete="email" maxlength="254" required placeholder="you@example.com" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3"><p class="mt-2 text-xs text-gray-500">We will send payment confirmation here.</p></div>
            <div><label for="phone" class="mb-2 block text-sm font-semibold">Contact number</label><input id="phone" name="phone" type="tel" autocomplete="tel" maxlength="25" required placeholder="+91 98765 43210" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3"></div>
          </div>
          <div class="mt-6 rounded-xl bg-gray-50 p-4 text-sm text-gray-600"><span class="font-semibold text-brand-navy">Amount to request: ₹1</span><br>Payment details are handled securely by Razorpay.</div>
          <button id="pay-button" type="submit" class="mt-6 w-full rounded-xl bg-gradient-to-r from-primary to-accent-orange px-5 py-4 font-bold text-brand-navy transition hover:brightness-105 disabled:cursor-not-allowed disabled:opacity-60">Request ₹1 payment</button>
          <p class="mt-3 text-center text-xs text-gray-500">You can choose your payment method in the Razorpay window.</p>
          <p id="checkout-message" role="status" aria-live="polite" class="mt-3 text-sm text-gray-700"></p>
          <a id="status-link" class="hidden text-sm underline text-brand-navy">Check payment status</a>
        </form>
      </div>
    </div>
  </main>
  <div id="site-footer"><?php if (is_file(dirname(__DIR__, 2) . '/templates/footer.html')) require dirname(__DIR__, 2) . '/templates/footer.html'; ?></div>
  <script src="https://checkout.razorpay.com/v1/checkout.js" defer></script>
  <script src="../../assets/checkout.js?v=<?= substr(hash_file('sha256', dirname(__DIR__, 2) . '/assets/checkout.js'), 0, 16) ?>" defer></script>
  <script>
    document.getElementById('report-button').addEventListener('click', function () {
      const paymentForm = document.getElementById('purchase-form');
      paymentForm.classList.remove('hidden');
      paymentForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  </script>
</body>
</html>
