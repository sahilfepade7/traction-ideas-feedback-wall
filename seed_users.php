<?php
// seed_users.php
require_once __DIR__ . '/config/config.php';

// --- Local safeguard (simplified & more reliable) ---
$allowed = ['127.0.0.1', '::1', 'localhost'];
if (php_sapi_name() !== 'cli') {
    $remote = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!in_array($remote, $allowed, true)) {
        die('Seed only runnable locally.');
    }
}

$users = [
    [
        'name' => 'Seed Admin',
        'email' => 'admin@example.com',
        'password' => 'Admin@123',
        'role' => 'admin'
    ],
    [
        'name' => 'Seed User',
        'email' => 'user@example.com',
        'password' => 'User@123',
        'role' => 'user'
    ],
];

$inserted = 0;
foreach ($users as $u) {
    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$u['email']]);
    if ($stmt->fetch()) {
        echo "Skipped (already exists): {$u['email']}<br>";
        continue;
    }

    // Insert new user
    $hash = password_hash($u['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role)
                           VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$u['name'], $u['email'], $hash, $u['role']])) {
        echo "Inserted user: {$u['email']}<br>";
        $inserted++;
    } else {
        echo "Failed to insert: {$u['email']}<br>";
    }
}

echo "<br><strong>Inserted $inserted new user(s)</strong>";
?>
