<?php
// login.php
require_once __DIR__ . '/config/config.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['_csrf']) || !verify_csrf($_POST['_csrf'])) {
        $err = 'Invalid CSRF token';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $stmt = $pdo->prepare("SELECT id, password_hash, role, name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            header('Location: index.php');
            exit;
        } else {
            $err = 'Invalid credentials';
        }
    }
}
?>
<!doctype html>
<html><head>
  <meta charset="utf-8">
  <title>Login - Traction Ideas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title">Login</h4>
          <?php if($err): ?><div class="alert alert-danger"><?php echo e($err); ?></div><?php endif; ?>
          <form method="post" novalidate>
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input class="form-control" type="email" name="email" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input class="form-control" type="password" name="password" required>
            </div>
            <button class="btn btn-primary">Login</button>
            <a href="index.php" class="btn btn-link">Back</a>
          </form>
          <hr>
          <p class="small">Seed accounts: admin@example.com / Admin@123 &nbsp; | &nbsp; user@example.com / User@123</p>
        </div>
      </div>
    </div>
  </div>
</div>
</body></html>
