<?php
// index.php
require_once __DIR__ . '/config/config.php';

$current = current_user($pdo);
$categories = ['All','Feature','Design','Bug','Idea'];

// Filtering / sort / search
$category = $_GET['category'] ?? 'All';
$sort = $_GET['sort'] ?? 'votes_desc'; // votes_desc, date_desc, date_asc
$search = trim($_GET['q'] ?? '');

$where = "status = 'Open'";
$params = [];

if ($category !== 'All' && in_array($category, $categories, true)) {
    $where .= " AND category = ?";
    $params[] = $category;
}
if ($search !== '') {
    $where .= " AND (title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$order = "s.votes DESC, s.created_at DESC";
if ($sort === 'date_desc') $order = "s.created_at DESC";
if ($sort === 'date_asc') $order = "s.created_at ASC";

$sql = "SELECT s.*, u.name as author_name FROM suggestions s JOIN users u ON s.user_id = u.id WHERE $where ORDER BY $order LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$suggestions = $stmt->fetchAll();

?>
<!doctype html><html><head>
<meta charset="utf-8"><title>Traction Ideas - Feedback Wall</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.card-suggestion { transition: box-shadow .15s; }
.card-suggestion:hover { box-shadow: 0 6px 20px rgba(0,0,0,.08); }
.vote-btn { cursor: pointer; user-select: none; }
.small-meta { font-size: .85rem; color: #666; }
</style>
</head><body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="index.php">Traction Ideas</a>
    <div class="ms-auto">
      <?php if($current): ?>
        <span class="me-2">Signed in as <?php echo e($current['name']); ?> (<?php echo e($current['role']); ?>)</span>
        <a class="btn btn-outline-secondary btn-sm" href="add.php">Add</a>
        <?php if($current['role']==='admin'): ?><a class="btn btn-outline-danger btn-sm ms-2" href="admin/dashboard.php">Admin</a><?php endif; ?>
        <a class="btn btn-link btn-sm" href="logout.php">Logout</a>
      <?php else: ?>
        <a class="btn btn-outline-primary btn-sm" href="login.php">Login</a>
        <a class="btn btn-link btn-sm" href="register.php">Register</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="d-flex justify-content-between mb-3">
    <div>
      <form class="d-flex" method="get" id="filterForm">
        <select name="category" class="form-select me-2" onchange="document.getElementById('filterForm').submit()">
          <?php foreach($categories as $c): ?>
            <option value="<?php echo e($c); ?>" <?php if($c===$category) echo 'selected'; ?>><?php echo e($c); ?></option>
          <?php endforeach; ?>
        </select>
        <select name="sort" class="form-select me-2" onchange="document.getElementById('filterForm').submit()">
          <option value="votes_desc" <?php if($sort==='votes_desc') echo 'selected'; ?>>Top votes</option>
          <option value="date_desc" <?php if($sort==='date_desc') echo 'selected'; ?>>Newest</option>
          <option value="date_asc" <?php if($sort==='date_asc') echo 'selected'; ?>>Oldest</option>
        </select>
        <input class="form-control me-2" name="q" value="<?php echo e($search); ?>" placeholder="Search...">
        <button class="btn btn-primary">Search</button>
      </form>
    </div>
    <div>
      <a href="resolved.php" class="btn btn-outline-secondary">View Resolved</a>
    </div>
  </div>

  <div class="row g-3">
    <?php if(empty($suggestions)): ?>
      <div class="col-12"><div class="alert alert-info">No suggestions found.</div></div>
    <?php endif; ?>

    <?php foreach($suggestions as $s): ?>
      <div class="col-md-6">
        <div class="card card-suggestion">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <h5 class="card-title mb-1"><?php echo e($s['title']); ?></h5>
              <div class="text-end">
                <div class="small-meta"><?php echo e($s['category']); ?></div>
                <div class="small-meta"><?php echo e($s['votes']); ?> votes</div>
              </div>
            </div>
            <p class="card-text"><?php echo nl2br(e(mb_substr($s['description'],0,400))); ?></p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="small-meta">By <?php echo e($s['author_name']); ?> · <?php echo e($s['created_at']); ?></div>
              <div>
                <button class="btn btn-sm btn-outline-success vote-btn" data-id="<?php echo e($s['id']); ?>">▲ Upvote</button>
                <?php if($current && ($current['id']===$s['user_id'] || $current['role']==='admin')): ?>
                  <a class="btn btn-sm btn-outline-secondary" href="edit.php?id=<?php echo e($s['id']); ?>">Edit</a>
                <?php endif; ?>
                <?php if($current && ($current['id']===$s['user_id'] || $current['role']==='admin')): ?>
                  <a class="btn btn-sm btn-outline-danger" href="delete.php?id=<?php echo e($s['id']); ?>" onclick="return confirm('Delete suggestion?')">Delete</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
document.addEventListener('click', function(e){
  if (e.target.matches('.vote-btn')) {
    const btn = e.target;
    const id = btn.getAttribute('data-id');
    btn.disabled = true;
    fetch('api/upvote.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({suggestion_id: id})
    }).then(r=>r.json()).then(j=>{
      btn.disabled = false;
      if (j.success) {
        // update votes count in UI: naive approach - reload page or better: find element and update
        location.reload();
      } else {
        alert(j.error || 'Could not upvote');
      }
    }).catch(()=>{btn.disabled = false; alert('Network error');});
  }
});
</script>
</body></html>

