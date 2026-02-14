<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $action = $input['action'] ?? '';

    if ($action === 'change_admin_password') {
        if (!isAdmin()) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }

        $current = $input['current'] ?? '';
        $newPw = $input['password'] ?? '';

        if (strlen($newPw) < 8) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($current, $admin['password'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
            exit;
        }

        $hash = password_hash($newPw, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $_SESSION['admin_id']]);
        echo json_encode(['success' => true, 'message' => 'Password changed successfully!']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
