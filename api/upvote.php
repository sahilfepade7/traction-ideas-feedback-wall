<?php
// api/upvote.php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

$raw = json_decode(file_get_contents('php://input'), true);
$id = (int)($raw['suggestion_id'] ?? 0);
if ($id <= 0) { echo json_encode(['success'=>false,'error'=>'Invalid ID']); exit; }

try {
    $pdo->beginTransaction();
    $userId = $_SESSION['user_id'] ?? null;

    if ($userId) {
        // check unique vote by user
        $stmt = $pdo->prepare("SELECT id FROM votes_log WHERE suggestion_id=? AND voter_user_id=?");
        $stmt->execute([$id, $userId]);
        if ($stmt->fetch()) { $pdo->commit(); echo json_encode(['success'=>false,'error'=>'Already voted']); exit; }
        $stmt = $pdo->prepare("INSERT INTO votes_log (suggestion_id, voter_user_id) VALUES (?, ?)");
        $stmt->execute([$id, $userId]);
    } else {
        // guest: fingerprint
        $fp = voter_fingerprint();
        $stmt = $pdo->prepare("SELECT id FROM votes_log WHERE suggestion_id=? AND voter_fingerprint=?");
        $stmt->execute([$id, $fp]);
        if ($stmt->fetch()) { $pdo->commit(); echo json_encode(['success'=>false,'error'=>'Already voted (guest)']); exit; }
        $stmt = $pdo->prepare("INSERT INTO votes_log (suggestion_id, voter_fingerprint) VALUES (?, ?)");
        $stmt->execute([$id, $fp]);
    }

    // increment votes on suggestions
    $stmt = $pdo->prepare("UPDATE suggestions SET votes = votes + 1 WHERE id = ?");
    $stmt->execute([$id]);

    $pdo->commit();
    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success'=>false,'error'=>'Server error']);
}
