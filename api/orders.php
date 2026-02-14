<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isAdmin()) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
    $id = intval($_GET['id'] ?? 0);
    if ($id) {
        $stmt = $pdo->prepare("SELECT o.*, p.name as product_name, p.jumia_link FROM orders o LEFT JOIN products p ON o.product_id = p.id WHERE o.id = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($order ? ['success' => true, 'order' => $order] : ['success' => false]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST;
    if (empty($input)) { $input = json_decode(file_get_contents('php://input'), true) ?: []; }
    $action = $input['action'] ?? 'create';

    if ($action === 'create') {
        // Customer submitting an order
        $productId = intval($input['product_id'] ?? 0);
        $name = sanitize($input['customer_name'] ?? '');
        $phone = sanitize($input['customer_phone'] ?? '');
        $altPhone = sanitize($input['customer_alt_phone'] ?? '');
        $email = sanitize($input['customer_email'] ?? '');
        $gender = sanitize($input['customer_gender'] ?? '');
        $county = sanitize($input['county'] ?? '');
        $address = sanitize($input['address'] ?? '');
        $deliveryDate = sanitize($input['delivery_date'] ?? '');
        $notes = sanitize($input['notes'] ?? '');
        $qty = max(1, intval($input['quantity'] ?? 1));
        $unitPrice = floatval($input['unit_price'] ?? 0);
        $subtotal = $qty * $unitPrice;
        $reference = strtoupper(substr(uniqid(), -8));

        if (empty($name) || empty($phone) || empty($email) || empty($county) || empty($address)) {
            echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO orders (reference, product_id, customer_name, customer_phone, customer_alt_phone, customer_email, customer_gender, county, address, delivery_date, notes, quantity, unit_price, subtotal) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$reference, $productId, $name, $phone, $altPhone, $email, $gender, $county, $address, $deliveryDate, $notes, $qty, $unitPrice, $subtotal]);

        // Optionally create customer account
        if (!empty($input['create_account']) && !empty($input['password'])) {
            registerCustomer($pdo, [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password' => $input['password'],
                'gender' => $gender,
                'county' => $county,
                'address' => $address
            ]);
        }

        echo json_encode(['success' => true, 'reference' => $reference]);
    } elseif ($action === 'update') {
        if (!isAdmin()) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
        $id = intval($input['id'] ?? 0);
        $status = sanitize($input['status'] ?? '');
        $transportFee = isset($input['transport_fee']) && $input['transport_fee'] !== '' ? floatval($input['transport_fee']) : null;

        $stmt = $pdo->prepare("UPDATE orders SET status = ?, transport_fee = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $transportFee, $id]);
        echo json_encode(['success' => true]);
    } elseif ($action === 'delete_all') {
        if (!isAdmin()) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
        $pdo->exec("DELETE FROM orders");
        echo json_encode(['success' => true, 'message' => 'All orders deleted']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}
