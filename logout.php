<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
logoutCustomer();
header('Location: ' . BASE_URL . '/');
exit;
