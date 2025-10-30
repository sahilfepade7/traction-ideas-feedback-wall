<?php
// resolved.php
require_once __DIR__ . '/config/config.php';
$stmt = $pdo->query("SELECT s.*, u.name as author_name FROM suggestions s JOIN users u ON s.user_id=u.id WHERE s.status='Resolved' ORDER BY s.updated_at DESC");
$resolved = $stmt->fetchAll();
?>
<!doctype html><html><head>
<meta charset="utf-8"><title>Resolved - Traction Ideas</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light">
<div class="container py-4">
  <h3>Resolved Suggestions</h3>
  <?php if(empty($resolved)): ?><div class="alert alert-info">No resolved suggestions yet.</div><?php endif; ?>
  <div class="list-group">
    <?php foreach($resolved as $r): ?>
      <div class="list-group-item">
        <div class="d-flex justify-content-between">
          <h5><?php echo e($r['title']); ?></h5>
          <small><?php echo e($r['votes']); ?> votes</small>
        </div>
        <p><?php echo e(mb_substr($r['description'],0,400)); ?></p>
        <div class="small-meta">By <?php echo e($r['author_name']); ?> · Resolved at <?php echo e($r['updated_at'] ?? $r['created_at']); ?></div>
      </div>
    <?php endforeach; ?>
  </div>
  <a href="index.php" class="btn btn-link mt-3">Back</a>
</div>
</body></html>
