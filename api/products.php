<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// GET: Fetch a product
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = intval($_GET['id'] ?? 0);
    if ($id) {
        $product = getProduct($pdo, $id);
        echo json_encode($product ? ['success' => true, 'product' => $product] : ['success' => false, 'message' => 'Not found']);
    } else {
        $products = getProducts($pdo);
        echo json_encode(['success' => true, 'products' => $products]);
    }
    exit;
}

// POST: Create, Update, Delete, Toggle Stock
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isAdmin()) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }

    $input = $_POST;
    if (empty($input)) {
        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?: [];
    }
    $action = $input['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $name = sanitize($input['name'] ?? '');
        $price = floatval($input['price'] ?? 0);
        $categoryId = intval($input['category_id'] ?? 0) ?: null;
        $gender = sanitize($input['gender'] ?? 'unisex');
        $description = sanitize($input['description'] ?? '');
        $imagesRaw = $input['images'] ?? '';
        $inStock = isset($input['in_stock']) ? 1 : 0;
        $isFeatured = isset($input['is_featured']) ? 1 : 0;
        $isNew = isset($input['is_new']) ? 1 : 0;
        $jumiaLink = sanitize($input['jumia_link'] ?? '');

        $images = [];
        if ($imagesRaw) {
            $lines = is_array($imagesRaw) ? $imagesRaw : explode("\n", $imagesRaw);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line && filter_var($line, FILTER_VALIDATE_URL)) $images[] = $line;
            }
        }
        $imagesJson = json_encode($images);

        if (empty($name) || $price <= 0) {
            echo json_encode(['success' => false, 'message' => 'Name and price are required.']);
            exit;
        }

        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO products (name, price, category_id, gender, description, images, in_stock, is_featured, is_new, jumia_link) VALUES (?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$name, $price, $categoryId, $gender, $description, $imagesJson, $inStock, $isFeatured, $isNew, $jumiaLink]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        } else {
            $id = intval($input['id'] ?? 0);
            $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, category_id=?, gender=?, description=?, images=?, in_stock=?, is_featured=?, is_new=?, jumia_link=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([$name, $price, $categoryId, $gender, $description, $imagesJson, $inStock, $isFeatured, $isNew, $jumiaLink, $id]);
            echo json_encode(['success' => true]);
        }
    } elseif ($action === 'delete') {
        $id = intval($input['id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } elseif ($action === 'toggle_stock') {
        $id = intval($input['id'] ?? 0);
        $inStock = intval($input['in_stock'] ?? 0);
        $stmt = $pdo->prepare("UPDATE products SET in_stock = ? WHERE id = ?");
        $stmt->execute([$inStock, $id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}
