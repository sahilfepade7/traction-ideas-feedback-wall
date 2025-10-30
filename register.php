<?php
// register.php
require_once __DIR__ . '/config/config.php';
$err = $msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) { $err = 'Invalid CSRF'; }
    else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass = $_POST['password'] ?? '';
        if ($name === '' || $email === '' || strlen($pass) < 6) {
            $err = 'Fill all fields; password >= 6 chars';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) $err = 'Email exists';
            else {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?, 'user')");
                $stmt->execute([$name,$email,$hash]);
                $msg = 'Registered. You can now login.';
            }
        }
    }
}
?>
<!doctype html><html><head>
<meta charset="utf-8"><title>Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light">
<div class="container py-5"><div class="row justify-content-center"><div class="col-md-6">
<div class="card"><div class="card-body">
<h4>Register</h4>
<?php if($err): ?><div class="alert alert-danger"><?php echo e($err); ?></div><?php endif; ?>
<?php if($msg): ?><div class="alert alert-success"><?php echo e($msg); ?></div><?php endif; ?>
<form method="post"><?php echo csrf_field(); ?>
<div class="mb-3"><label class="form-label">Name</label><input class="form-control" name="name"></div>
<div class="mb-3"><label class="form-label">Email</label><input class="form-control" name="email" type="email"></div>
<div class="mb-3"><label class="form-label">Password</label><input class="form-control" name="password" type="password"></div>
<button class="btn btn-primary">Register</button>
</form>
</div></div>
</div></div></div>
</body></html>
