<?php
// api/admin/update_status.php
require_once __DIR__ . '/../../config/config.php';
header('Content-Type: application/json');
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit;
}
$raw = json_decode(file_get_contents('php://input'), true);
$id = (int)($raw['id'] ?? 0);
$action = $raw['action'] ?? '';
if (!in_array($action,['resolve','unresolve'])) { echo json_encode(['success'=>false,'error'=>'Bad action']); exit; }

$status = $action==='resolve' ? 'Resolved' : 'Open';
$stmt = $pdo->prepare("UPDATE suggestions SET status=?, updated_at=NOW() WHERE id=?");
$stmt->execute([$status, $id]);
echo json_encode(['success'=>true]);

