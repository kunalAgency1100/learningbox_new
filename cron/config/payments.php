<?php
// Prefer an absolute path outside public_html, configured in the hosting environment.
$privateConfig = getenv('LEARNINGBOX_PAYMENT_CONFIG') ?: dirname(__DIR__, 2) . '/learningbox-payments.php';
$localConfig = dirname(__DIR__) . '/learningbox-payments.php';
if (!getenv('LEARNINGBOX_PAYMENT_CONFIG') && !is_file($privateConfig) && is_file($localConfig)) {
    $privateConfig = $localConfig;
}
$settings = is_file($privateConfig) ? require $privateConfig : [];
return array_merge([
    'key_id' => getenv('RAZORPAY_KEY_ID') ?: 'rzp_test_TZb21hKYtx026s',
    'key_secret' => getenv('RAZORPAY_KEY_SECRET') ?: 'J12e1GI7EFMujiFf8qoySOI1',
    'webhook_secret' => getenv('RAZORPAY_WEBHOOK_SECRET') ?: 'zXLzdFYXgcQ21oiGh48GjN1q9QOfE1JR',
    'base_url' => getenv('LEARNINGBOX_URL') ?: 'https://www.learningbox.in',
    'admin_email' => getenv('LEARNINGBOX_ADMIN_EMAIL') ?: 'info@learningbox.in',
    'from_email' => getenv('LEARNINGBOX_FROM_EMAIL') ?: 'info@learningbox.in',
    // Empty uses cPanel's default envelope sender and sendmail configuration.
    'mail_envelope_from' => getenv('LEARNINGBOX_MAIL_ENVELOPE_FROM') ?: '',
], $settings);
