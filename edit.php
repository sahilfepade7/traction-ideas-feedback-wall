<?php
// edit.php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM suggestions WHERE id = ?");
$stmt->execute([$id]);
$s = $stmt->fetch();
if (!$s) { die('Not found'); }

// Ownership rule: owner can edit their own; admin can edit only admin-owned items (per spec)
$ownerId = (int)$s['user_id'];
$currentId = (int)$_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'user';

$canEdit = false;
if ($currentId === $ownerId) $canEdit = true;
if ($role === 'admin' && $ownerId === $currentId) $canEdit = true; // admin may edit only admin-owned items

if (!$canEdit) {
    http_response_code(403); die('Forbidden: you cannot edit this suggestion');
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) $err = 'Invalid CSRF';
    else {
        $errors = validate_suggestion($_POST);
        if ($errors) $err = implode('<br>', $errors);
        else {
            $stmt = $pdo->prepare("UPDATE suggestions SET title=?, description=?, category=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([$_POST['title'], $_POST['description'], $_POST['category'], $id]);
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Edit</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>
<div class="container py-4">
<div class="card"><div class="card-body">
<h4>Edit Suggestion</h4>
<?php if($err): ?><div class="alert alert-danger"><?php echo e($err); ?></div><?php endif; ?>
<form method="post"><?php echo csrf_field(); ?>
<div class="mb-3"><label>Title</label><input class="form-control" name="title" value="<?php echo e($s['title']); ?>"></div>
<div class="mb-3"><label>Category</label><select class="form-select" name="category">
  <?php foreach(['Feature','Design','Bug','Idea'] as $c): ?>
    <option <?php if($c==$s['category']) echo 'selected'; ?>><?php echo e($c); ?></option>
  <?php endforeach; ?>
</select></div>
<div class="mb-3"><label>Description</label><textarea class="form-control" name="description" rows="6"><?php echo e($s['description']); ?></textarea></div>
<button class="btn btn-primary">Save</button>
<a href="index.php" class="btn btn-link">Cancel</a>
</form>
</div></div>
</div>
</body></html>
