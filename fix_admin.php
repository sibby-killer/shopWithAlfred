<?php
/**
 * ADMIN FIX SCRIPT - Upload to htdocs/ and visit in browser
 * DELETE THIS FILE AFTER USE!
 */

// Step 1: Connect to database
require_once __DIR__ . '/includes/config.php';

echo "<h2>ShopWithAlfred - Admin Fix Tool</h2>";
echo "<hr>";

// Step 2: Show PHP info
echo "<h3>1. PHP Version</h3>";
echo "PHP: " . phpversion() . "<br>";
echo "password_hash available: " . (function_exists('password_hash') ? 'YES' : 'NO') . "<br>";
echo "password_verify available: " . (function_exists('password_verify') ? 'YES' : 'NO') . "<br><br>";

// Step 3: Check admins table
echo "<h3>2. Current Admins Table</h3>";
try {
    $stmt = $pdo->query("SELECT * FROM admins");
    $admins = $stmt->fetchAll();
    if (empty($admins)) {
        echo "<strong style='color:red'>No admins found!</strong><br>";
    } else {
        foreach ($admins as $a) {
            echo "ID: {$a['id']} | Username: {$a['username']} | Hash: {$a['password']}<br>";
            // Test if current hash matches
            $test = password_verify('admin@guru123', $a['password']);
            echo "Does 'admin@guru123' match this hash? <strong>" . ($test ? 'YES' : 'NO') . "</strong><br><br>";
        }
    }
} catch (Exception $e) {
    echo "<strong style='color:red'>Error: " . $e->getMessage() . "</strong><br>";
}

// Step 4: Handle reset
if (isset($_GET['reset']) && $_GET['reset'] === 'yes') {
    echo "<h3>3. RESETTING ADMIN...</h3>";
    try {
        // Delete all existing admins
        $pdo->exec("DELETE FROM admins");
        echo "Deleted old admins.<br>";
        
        // Create fresh hash
        $newPassword = 'Admin123';
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        echo "Generated new hash: $hash<br>";
        
        // Verify it immediately
        $verify = password_verify($newPassword, $hash);
        echo "Immediate verify test: " . ($verify ? '<strong style="color:green">PASS</strong>' : '<strong style="color:red">FAIL</strong>') . "<br>";
        
        // Insert new admin
        $stmt = $pdo->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $hash, 'alfred.dev8@gmail.com']);
        echo "<br><strong style='color:green; font-size:18px'>✅ Admin created successfully!</strong><br><br>";
        echo "<strong>Login credentials:</strong><br>";
        echo "Username: <code>admin</code><br>";
        echo "Password: <code>Admin123</code><br><br>";
        
        // Read it back and verify
        $stmt2 = $pdo->query("SELECT * FROM admins WHERE username = 'admin'");
        $readBack = $stmt2->fetch();
        if ($readBack) {
            $readVerify = password_verify($newPassword, $readBack['password']);
            echo "Read-back verify test: " . ($readVerify ? '<strong style="color:green">PASS</strong>' : '<strong style="color:red">FAIL</strong>') . "<br>";
            echo "Stored hash: " . $readBack['password'] . "<br>";
        }
        
    } catch (Exception $e) {
        echo "<strong style='color:red'>Error: " . $e->getMessage() . "</strong><br>";
    }
    
    echo "<br><hr><strong style='color:red; font-size:16px'>⚠️ DELETE THIS FILE NOW! (fix_admin.php)</strong>";
} else {
    // Step 5: Test session
    echo "<h3>3. Session Test</h3>";
    echo "Session status: " . session_status() . " (2 = active)<br>";
    echo "Session ID: " . session_id() . "<br><br>";
    
    echo "<h3>4. Ready to Reset</h3>";
    echo "<p>Click the button below to delete all admins and create a fresh one:</p>";
    echo "<a href='?reset=yes' style='display:inline-block;padding:12px 24px;background:#e74c3c;color:white;text-decoration:none;border-radius:6px;font-size:16px'>🔄 Reset Admin Account</a>";
}
?>
