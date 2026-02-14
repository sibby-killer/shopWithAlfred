<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $email = sanitize($input['email'] ?? '');
    $type = $input['type'] ?? 'newsletter';

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email.']);
        exit;
    }

    if ($type === 'restock') {
        $productId = intval($input['product_id'] ?? 0);
        $stmt = $pdo->prepare("SELECT id FROM restock_notifications WHERE email = ? AND product_id = ?");
        $stmt->execute([$email, $productId]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'You are already registered for this notification.']);
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO restock_notifications (email, product_id) VALUES (?, ?)");
        $stmt->execute([$email, $productId]);
        echo json_encode(['success' => true, 'message' => "You'll be notified when this product is back in stock!"]);
    } else {
        $stmt = $pdo->prepare("SELECT id FROM subscribers WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'You are already subscribed!']);
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO subscribers (email) VALUES (?)");
        $stmt->execute([$email]);
        echo json_encode(['success' => true, 'message' => 'Subscribed successfully! 🎉']);
    }
    exit;
}

// DELETE all (admin action)
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' || (isset($_POST['action']) && $_POST['action'] === 'delete_all')) {
    if (!isAdmin()) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
    $pdo->exec("DELETE FROM subscribers");
    $pdo->exec("DELETE FROM restock_notifications");
    echo json_encode(['success' => true, 'message' => 'All subscribers deleted']);
    exit;
}
