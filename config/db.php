<?php
// config/db.php
// Database configuration for Learning Box cPanel Deployment

// Update these variables with your actual cPanel MySQL credentials
$db_host = getenv('LEARNINGBOX_DB_HOST');
$db_name = getenv('LEARNINGBOX_DB_NAME');
$db_user = getenv('LEARNINGBOX_DB_USER');
$db_pass = getenv('LEARNINGBOX_DB_PASS');
$db_host = $db_host === false ? 'localhost' : $db_host;
$db_name = $db_name === false ? 'backend_lb' : $db_name;
$db_user = $db_user === false ? 'user_lb' : $db_user;
$db_pass = $db_pass === false
    ? (getenv('LEARNINGBOX_DB_PASS_EMPTY') === '1' ? '' : '^m^(UT120pFG')
    : $db_pass;

// Local PHP development server settings; production keeps the configured values above.
$requestHost = strtolower((string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? ''));
$requestHost = preg_replace('/:\d+$/', '', $requestHost);
if (PHP_OS_FAMILY === 'Windows' || in_array($requestHost, ['127.0.0.1', 'localhost'], true)) {
    $db_host = '127.0.0.1';
    $db_name = 'learningbox';
    $db_user = 'root';
    $db_pass = '';
}

try {
    // Create new PDO instance
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);

    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Use associative arrays for fetching
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // For security, don't expose db errors to the user in a production environment
    // Instead, you'd log $e->getMessage() to a file.
    die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
}
?>
