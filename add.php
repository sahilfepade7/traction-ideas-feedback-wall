<?php
// add.php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$err = $msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) { $err = 'Invalid CSRF'; }
    else {
        $errors = validate_suggestion($_POST);
        if ($errors) { $err = implode('<br>', $errors); }
        else {
            $stmt = $pdo->prepare("INSERT INTO suggestions (user_id, title, description, category) VALUES (?,?,?,?)");
            $stmt->execute([$_SESSION['user_id'], $_POST['title'], $_POST['description'], $_POST['category']]);
            $msg = 'Suggestion created';
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!doctype html><html><head>
<meta charset="utf-8"><title>Add Suggestion</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light">
<div class="container py-4">
<div class="card"><div class="card-body">
<h4>Add Suggestion</h4>
<?php if($err): ?><div class="alert alert-danger"><?php echo $err; ?></div><?php endif; ?>
<form method="post">
<?php echo csrf_field(); ?>
<div class="mb-3"><label class="form-label">Title</label><input class="form-control" name="title"></div>
<div class="mb-3"><label class="form-label">Category</label>
<select class="form-select" name="category">
  <option>Feature</option><option>Design</option><option>Bug</option><option>Idea</option>
</select></div>
<div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="6"></textarea></div>
<button class="btn btn-primary">Create</button>
<a href="index.php" class="btn btn-link">Cancel</a>
</form>
</div></div>
</div>
</body></html>
