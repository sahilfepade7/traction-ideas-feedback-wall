<?php
// delete.php
require_once __DIR__ . '/config/config.php';
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM suggestions WHERE id = ?");
$stmt->execute([$id]);
$s = $stmt->fetch();
if (!$s) { die('Not found'); }

$currentId = (int)$_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'user';

// Owner can delete own; admin can delete any
if (!($currentId === (int)$s['user_id'] || $role === 'admin')) {
    http_response_code(403); die('Forbidden');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) die('Invalid CSRF');
    $stmt = $pdo->prepare("DELETE FROM suggestions WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: index.php');
    exit;
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Delete</title></head><body>
<form method="post">
  <?php echo csrf_field(); ?>
  <p>Confirm delete?</p>
  <button>Delete</button> <a href="index.php">Cancel</a>
</form>
</body></html>
