<?php
// admin/dashboard.php
require_once __DIR__ . '/../config/config.php';
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { header('Location: ../login.php'); exit; }

$stats = $pdo->query("SELECT 
  SUM(CASE WHEN status='Open' THEN 1 ELSE 0 END) as open_count,
  SUM(CASE WHEN status='Resolved' THEN 1 ELSE 0 END) as resolved_count,
  SUM(votes) as total_votes,
  COUNT(*) as total_suggestions
FROM suggestions")->fetch();

?>
<!doctype html><html><head><meta charset="utf-8"><title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>
<div class="container py-4">
  <h3>Admin Dashboard</h3>
  <div class="row">
    <div class="col-md-3"><div class="card p-3">Open: <?php echo e($stats['open_count']); ?></div></div>
    <div class="col-md-3"><div class="card p-3">Resolved: <?php echo e($stats['resolved_count']); ?></div></div>
    <div class="col-md-3"><div class="card p-3">Total Votes: <?php echo e($stats['total_votes'] ?? 0); ?></div></div>
    <div class="col-md-3"><div class="card p-3">Total Suggestions: <?php echo e($stats['total_suggestions']); ?></div></div>
  </div>
  <hr>
  <a href="suggestions.php" class="btn btn-primary">Manage Suggestions</a>
  <a href="../index.php" class="btn btn-link">Back to site</a>
</div>
</body></html>
