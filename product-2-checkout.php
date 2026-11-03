<?php
// Product 2 has a stable entry point so its catalogue button works even when
// a local development bridge drops query-string parameters.
$_GET['slug'] = 'digital-playbook-for-managers';
require __DIR__ . '/checkout.php';
