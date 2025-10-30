<?php
// admin/suggestions.php
require_once __DIR__ . '/../config/config.php';
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { header('Location: ../login.php'); exit; }

$stmt = $pdo->query("SELECT s.*, u.name as author_name FROM suggestions s JOIN users u ON s.user_id=u.id ORDER BY s.created_at DESC");
$all = $stmt->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Admin Suggestions</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>
<div class="container py-4">
  <h3>All Suggestions</h3>
  <table class="table">
    <thead><tr><th>ID</th><th>Title</th><th>Author</th><th>Category</th><th>Votes</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach($all as $r): ?>
      <tr id="row-<?php echo e($r['id']); ?>">
        <td><?php echo e($r['id']); ?></td>
        <td><?php echo e($r['title']); ?></td>
        <td><?php echo e($r['author_name']); ?></td>
        <td><?php echo e($r['category']); ?></td>
        <td><?php echo e($r['votes']); ?></td>
        <td class="status-<?php echo e($r['id']); ?>"><?php echo e($r['status']); ?></td>
        <td>
          <?php if($r['status']==='Open'): ?>
            <button class="btn btn-sm btn-success btn-resolve" data-id="<?php echo e($r['id']); ?>">Resolve</button>
          <?php else: ?>
            <button class="btn btn-sm btn-warning btn-unresolve" data-id="<?php echo e($r['id']); ?>">Unresolve</button>
          <?php endif; ?>
          <button class="btn btn-sm btn-danger btn-delete" data-id="<?php echo e($r['id']); ?>">Delete</button>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <a href="dashboard.php" class="btn btn-link">Back</a>
</div>

<script>
document.addEventListener('click', function(e){
  if (e.target.matches('.btn-resolve') || e.target.matches('.btn-unresolve')) {
    const id = e.target.getAttribute('data-id');
    const action = e.target.matches('.btn-resolve') ? 'resolve' : 'unresolve';
    fetch('../api/admin/update_status.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({id: id, action: action})
    }).then(r=>r.json()).then(j=>{
      if (j.success) location.reload();
      else alert(j.error || 'Error');
    });
  }
  if (e.target.matches('.btn-delete')) {
    if (!confirm('Delete suggestion?')) return;
    const id = e.target.getAttribute('data-id');
    fetch('../api/admin/delete_suggestion.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({id: id})
    }).then(r=>r.json()).then(j=>{
      if (j.success) location.reload();
      else alert(j.error || 'Error');
    });
  }
});
</script>
</body></html>
