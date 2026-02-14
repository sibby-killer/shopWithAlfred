<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = intval($_GET['id'] ?? 0);
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $cat = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($cat ? ['success' => true, 'category' => $cat] : ['success' => false]);
    } else {
        echo json_encode(['success' => true, 'categories' => getCategories($pdo)]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isAdmin()) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }

    $input = $_POST;
    if (empty($input)) { $input = json_decode(file_get_contents('php://input'), true) ?: []; }
    $action = $input['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $name = sanitize($input['name'] ?? '');
        $icon = sanitize($input['icon'] ?? 'fa-tag');
        if (empty($name)) { echo json_encode(['success' => false, 'message' => 'Name is required']); exit; }

        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO categories (name, icon) VALUES (?, ?)");
            $stmt->execute([$name, $icon]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        } else {
            $id = intval($input['id'] ?? 0);
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, icon = ? WHERE id = ?");
            $stmt->execute([$name, $icon, $id]);
            echo json_encode(['success' => true]);
        }
    } elseif ($action === 'delete') {
        $id = intval($input['id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}
