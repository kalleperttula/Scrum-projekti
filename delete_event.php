<?php
require __DIR__ . '/config.php';

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
  http_response_code(400);
  die("Invalid ID");
}

$stmt = $pdo->prepare("DELETE FROM tapahtumat WHERE id = ?");
$stmt->execute([$id]);

header("Location: etusivu.php");
exit;

