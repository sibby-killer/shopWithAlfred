<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isAdmin()) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

    foreach ($input as $key => $value) {
        if ($key === 'action') continue;
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
    echo json_encode(['success' => true, 'message' => 'Settings saved!']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['success' => true, 'settings' => getSettings($pdo)]);
    exit;
}
