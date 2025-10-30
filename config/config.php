<?php
// config/config.php
declare(strict_types=1);

session_start();

// Adjust these to match your local environment
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'traction_ideas');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        $options
    );
} catch (PDOException $e) {
    // In production: log error; here show for dev
    die("Database connection failed: " . $e->getMessage());
}

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/**
 * Simple CSRF token utilities
 */
function csrf_token(): string {
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['_csrf_token'];
}
function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="'.csrf_token().'">';
}
function verify_csrf(string $token): bool {
    return isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);
}

/**
 * Returns current logged in user row or null
 */
function current_user(PDO $pdo): ?array {
    if (!empty($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $u = $stmt->fetch();
        return $u ?: null;
    }
    return null;
}

/**
 * Fingerprint for guest votes: IP + UA + session (if exists). Hash to 64 chars.
 */
function voter_fingerprint(): string {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'noua';
    $sess = session_id() ?: '';
    return hash('sha256', $ip . '|' . $ua . '|' . $sess);
}
