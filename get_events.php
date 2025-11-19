<?php
require __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');

$stmt = $pdo->query("SELECT id, nimi, aika, kuvaus, kategoria FROM tapahtumat ORDER BY aika ASC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($events, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
