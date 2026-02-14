<?php
/**
 * ShopWithAlfred - Authentication & Session Management
 */

require_once __DIR__ . '/config.php';

function isLoggedIn() {
    return isset($_SESSION['customer_id']);
}

function isAdmin() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

function loginCustomer($pdo, $email, $password) {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->execute([$email]);
    $customer = $stmt->fetch();
    
    if ($customer && password_verify($password, $customer['password'])) {
        session_regenerate_id(true);
        $_SESSION['customer_id'] = $customer['id'];
        $_SESSION['customer_name'] = $customer['name'];
        $_SESSION['customer_email'] = $customer['email'];
        return true;
    }
    return false;
}

function loginAdmin($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        return true;
    }
    return false;
}

function registerCustomer($pdo, $data) {
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO customers (name, email, phone, password, gender, county, address) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['name'],
        $data['email'],
        $data['phone'],
        $hashedPassword,
        $data['gender'],
        $data['county'],
        $data['address']
    ]);
    
    $customerId = $pdo->lastInsertId();
    
    session_regenerate_id(true);
    $_SESSION['customer_id'] = $customerId;
    $_SESSION['customer_name'] = $data['name'];
    $_SESSION['customer_email'] = $data['email'];
    
    return $customerId;
}

function logout() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
