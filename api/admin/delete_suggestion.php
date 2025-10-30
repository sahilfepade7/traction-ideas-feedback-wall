<?php
// api/admin/delete_suggestion.php
require_once __DIR__ . '/../../config/config.php';
header('Content-Type: application/json');
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit;
}
$raw = json_decode(file_get_contents('php://input'), true);
$id = (int)($raw['id'] ?? 0);
$stmt = $pdo->prepare("DELETE FROM suggestions WHERE id = ?");
$stmt->execute([$id]);
echo json_encode(['success'=>true]);
