<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isAdmin()) {
    $stmt = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM orders WHERE customer_email = c.email) as order_count FROM customers c ORDER BY c.created_at DESC");
    echo json_encode(['success' => true, 'customers' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
