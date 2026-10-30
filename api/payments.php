<?php
require_once dirname(__DIR__) . '/includes/commerce.php';
header('Content-Type: application/json');
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');
try {
    $action = is_string($_GET['action'] ?? null) ? $_GET['action'] : '';
    $data = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $raw = file_get_contents('php://input', false, null, 0, 8193);
        if (strlen($raw) > 8192) throw new InvalidArgumentException('Request too large.');
        $data = json_decode($raw, true);
        if (!is_array($data)) throw new InvalidArgumentException('Send a JSON object.');
        // Some hosting rules strip query strings. Accept the action from JSON too.
        if ($action === '' && is_string($data['action'] ?? null)) $action = $data['action'];
    }
    if (!in_array($action, ['create', 'verify', 'status'], true)) {
        http_response_code(404);
        throw new InvalidArgumentException('Unknown endpoint.');
    }
    $method = $action === 'status' ? 'GET' : 'POST';
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        header('Allow: ' . $method);
        http_response_code(405);
        throw new InvalidArgumentException('Method not allowed.');
    }
    commerce_session();
    if ($method === 'POST') {
        check_csrf($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    }
    require dirname(__DIR__) . '/config/db.php';
    if ($action === 'create') {
        require_payment_setup();
        $buyer = buyer_details($data);
        $slug = $data['slug'] ?? '';
        if (!is_string($slug)) throw new InvalidArgumentException('Invalid product.');
        // Product 1 has a guided preview page. Its preview/payment request is
        // always charged at INR 1 (100 paise), regardless of browser input or
        // the regular catalogue price.
        $productOnePreview = $slug === 'product01';
        $lookupSlug = $productOnePreview ? 'relationship-health-diagnostic' : $slug;
        $stmt = $pdo->prepare('SELECT * FROM products WHERE slug=? AND active=1');
        $stmt->execute([$lookupSlug]);
        $product = $stmt->fetch();
        if (!$product) throw new InvalidArgumentException('This product is unavailable.');
        if ($productOnePreview) {
            $product['amount'] = 100;
            $product['name'] = 'Relationship Health Diagnostic — Product 1 Preview';
        }
        // Reuse the session order on retries; neither price nor currency comes from the browser.
        $fingerprint = hash('sha256', json_encode([$product['id'], $product['amount'], $buyer]));
        $purchase = null;
        if (isset($_SESSION['checkout_orders'][$fingerprint])) {
            $purchase = purchase_by_token($pdo, $_SESSION['checkout_orders'][$fingerprint]);
            if (!in_array($purchase['status'], ['pending', 'failed', 'authorized'], true) || !$purchase['razorpay_order_id']) $purchase = null;
        }
        if (!$purchase) {
            $lastOrderAt = (int)($_SESSION['last_order_at'] ?? 0);
            $isLocalDevelopment = in_array($_SERVER['HTTP_HOST'] ?? '', ['127.0.0.1:8000', 'localhost:8000'], true);
            if (!$isLocalDevelopment && $lastOrderAt > 0 && $lastOrderAt <= time() && time() - $lastOrderAt < 10) {
                http_response_code(429);
                throw new InvalidArgumentException('Please wait a few seconds before trying again.');
            }
            $_SESSION['last_order_at'] = time();
            $token = bin2hex(random_bytes(32));
            $pdo->prepare('INSERT INTO purchases (token,product_id,product_name,amount,buyer_name,buyer_email,buyer_phone) VALUES (?,?,?,?,?,?,?)')->execute([$token, $product['id'], $product['name'], $product['amount'], $buyer['name'], $buyer['email'], $buyer['phone']]);
            $id = $pdo->lastInsertId();
            try {
                $order = razorpay('POST', 'orders', ['amount' => (int)$product['amount'], 'currency' => 'INR', 'receipt' => 'lb_' . $id]);
                if (!preg_match('/^order_[A-Za-z0-9]+$/D', $order['id'] ?? '') || (int)($order['amount'] ?? 0) !== (int)$product['amount'] || ($order['currency'] ?? '') !== 'INR') throw new RuntimeException('Invalid order response.');
                $pdo->prepare("UPDATE purchases SET razorpay_order_id=?,status='pending' WHERE id=?")->execute([$order['id'], $id]);
            } catch (Throwable $e) {
                $pdo->prepare("UPDATE purchases SET status='creation_failed',failure_reason='Order creation could not be confirmed.' WHERE id=?")->execute([$id]);
                throw $e;
            }
            $_SESSION['checkout_orders'][$fingerprint] = $token;
            $purchase = purchase_by_token($pdo, $token);
        }
        echo json_encode(['success' => true, 'key_id' => commerce_config()['key_id'], 'token' => $purchase['token'], 'order_id' => $purchase['razorpay_order_id'], 'amount' => (int)$purchase['amount'], 'currency' => $purchase['currency'], 'product_name' => $purchase['product_name']]);
    } elseif ($action === 'verify') {
        $purchase = purchase_by_token($pdo, is_string($data['token'] ?? null) ? $data['token'] : '');
        $paymentId = $data['razorpay_payment_id'] ?? '';
        $signature = $data['razorpay_signature'] ?? '';
        if (!is_string($paymentId) || !preg_match('/^pay_[A-Za-z0-9]+$/D', $paymentId) || !is_string($signature) || ($data['razorpay_order_id'] ?? '') !== $purchase['razorpay_order_id'] || !valid_signature($purchase['razorpay_order_id'] . '|' . $paymentId, $signature, commerce_config()['key_secret'])) throw new InvalidArgumentException('Payment verification failed.');
        $payment = razorpay('GET', 'payments/' . rawurlencode($paymentId));
        if (!payment_matches($purchase, $payment) || ($payment['id'] ?? '') !== $paymentId) throw new RuntimeException('Payment mismatch.');
        apply_payment($pdo, $payment);
        $purchase = purchase_by_token($pdo, $purchase['token']);
        echo json_encode(['success' => true, 'status' => $purchase['status']]);
    } else {
        $purchase = purchase_by_token($pdo, is_string($_GET['token'] ?? null) ? $_GET['token'] : '');
        echo json_encode(['success' => true, 'status' => $purchase['status']]);
    }
} catch (PaymentGatewayException $e) {
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (InvalidArgumentException $e) {
    if (http_response_code() < 400) http_response_code(422);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (Throwable $e) {
    error_log('Commerce API: ' . $e->getMessage());
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'We could not confirm this request. If you already paid, do not pay again; check your purchase status shortly.']);
}
