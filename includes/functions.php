<?php
/**
 * ShopWithAlfred - Helper Functions
 */

// =====================================================
// SANITIZATION & SECURITY
// =====================================================

function sanitize($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function generateReference() {
    return 'SA-' . time();
}

// =====================================================
// DATABASE HELPERS
// =====================================================

function getProducts($pdo, $filters = []) {
    $where = [];
    $params = [];
    
    if (!empty($filters['category_id'])) {
        $where[] = "p.category_id = ?";
        $params[] = $filters['category_id'];
    }
    if (!empty($filters['gender'])) {
        $where[] = "p.gender = ?";
        $params[] = $filters['gender'];
    }
    if (isset($filters['in_stock'])) {
        $where[] = "p.in_stock = ?";
        $params[] = $filters['in_stock'];
    }
    if (!empty($filters['is_featured'])) {
        $where[] = "p.is_featured = 1";
    }
    if (!empty($filters['is_new'])) {
        $where[] = "p.is_new = 1";
    }
    if (!empty($filters['search'])) {
        $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
        $params[] = '%' . $filters['search'] . '%';
        $params[] = '%' . $filters['search'] . '%';
    }
    if (!empty($filters['min_price'])) {
        $where[] = "p.price >= ?";
        $params[] = $filters['min_price'];
    }
    if (!empty($filters['max_price'])) {
        $where[] = "p.price <= ?";
        $params[] = $filters['max_price'];
    }
    
    $sql = "SELECT p.*, c.name as category_name, c.icon as category_icon 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id";
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    if (!empty($filters['limit'])) {
        $sql .= " LIMIT " . intval($filters['limit']);
        if (!empty($filters['offset'])) {
            $sql .= " OFFSET " . intval($filters['offset']);
        }
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getProduct($pdo, $id) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.icon as category_icon 
                           FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getCategories($pdo) {
    $stmt = $pdo->query("SELECT c.*, COUNT(p.id) as product_count 
                         FROM categories c 
                         LEFT JOIN products p ON c.id = p.category_id 
                         GROUP BY c.id 
                         ORDER BY c.name");
    return $stmt->fetchAll();
}

function getCategory($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getOrders($pdo, $filters = []) {
    $where = [];
    $params = [];
    
    if (!empty($filters['status'])) {
        $where[] = "o.status = ?";
        $params[] = $filters['status'];
    }
    if (!empty($filters['customer_id'])) {
        $where[] = "o.customer_id = ?";
        $params[] = $filters['customer_id'];
    }
    
    $sql = "SELECT o.*, p.jumia_link 
            FROM orders o 
            LEFT JOIN products p ON o.product_id = p.id";
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    
    $sql .= " ORDER BY o.created_at DESC";
    
    if (!empty($filters['limit'])) {
        $sql .= " LIMIT " . intval($filters['limit']);
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getOrder($pdo, $id) {
    $stmt = $pdo->prepare("SELECT o.*, p.jumia_link 
                           FROM orders o 
                           LEFT JOIN products p ON o.product_id = p.id 
                           WHERE o.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getCustomers($pdo) {
    $stmt = $pdo->query("SELECT c.*, COUNT(o.id) as order_count 
                         FROM customers c 
                         LEFT JOIN orders o ON c.id = o.customer_id 
                         GROUP BY c.id 
                         ORDER BY c.created_at DESC");
    return $stmt->fetchAll();
}

function getCustomer($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getStatCount($pdo, $table, $condition = '') {
    $sql = "SELECT COUNT(*) as count FROM " . $table;
    if ($condition) $sql .= " WHERE " . $condition;
    $stmt = $pdo->query($sql);
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

function getSettings($pdo) {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

function getSetting($pdo, $key) {
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['setting_value'] : null;
}

function updateSetting($pdo, $key, $value) {
    $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
    return $stmt->execute([$value, $key]);
}

// =====================================================
// FORMATTING
// =====================================================

function formatPrice($price) {
    return 'KES ' . number_format($price, 0);
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('M d, Y h:i A', strtotime($datetime));
}

function getProductImages($product) {
    if (!empty($product['images'])) {
        $images = json_decode($product['images'], true);
        if (is_array($images) && !empty($images)) {
            return $images;
        }
    }
    return [BASE_URL . '/assets/images/default-product.png'];
}

function getFirstImage($product) {
    $images = getProductImages($product);
    return $images[0];
}

// =====================================================
// STATISTICS (Admin)
// =====================================================

function getStats($pdo) {
    $stats = [];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
    $stats['total_products'] = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM orders");
    $stats['total_orders'] = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
    $stats['pending_orders'] = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM customers");
    $stats['total_customers'] = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM subscribers");
    $stats['total_subscribers'] = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE in_stock = 0");
    $stats['out_of_stock'] = $stmt->fetch()['count'];
    
    return $stats;
}

// =====================================================
// VALIDATION
// =====================================================

function validatePhone($phone) {
    $phone = preg_replace('/\s+/', '', $phone);
    return preg_match('/^0[17]\d{8}$/', $phone);
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePassword($password) {
    if (strlen($password) < 8) return false;
    if (!preg_match('/[A-Z]/', $password)) return false;
    if (!preg_match('/[0-9]/', $password)) return false;
    return true;
}

// =====================================================
// KENYAN COUNTIES
// =====================================================

function getKenyanCounties() {
    return [
        'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo-Marakwet',
        'Embu', 'Garissa', 'Homa Bay', 'Isiolo', 'Kajiado',
        'Kakamega', 'Kericho', 'Kiambu', 'Kilifi', 'Kirinyaga',
        'Kisii', 'Kisumu', 'Kitui', 'Kwale', 'Laikipia',
        'Lamu', 'Machakos', 'Makueni', 'Mandera', 'Marsabit',
        'Meru', 'Migori', 'Mombasa', 'Murang\'a', 'Nairobi',
        'Nakuru', 'Nandi', 'Narok', 'Nyamira', 'Nyandarua',
        'Nyeri', 'Samburu', 'Siaya', 'Taita-Taveta', 'Tana River',
        'Tharaka-Nithi', 'Trans-Nzoia', 'Turkana', 'Uasin Gishu',
        'Vihiga', 'Wajir', 'West Pokot'
    ];
}

// =====================================================
// JSON RESPONSE HELPER (for API)
// =====================================================

function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
