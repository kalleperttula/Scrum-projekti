<?php
require __DIR__ . '/config.php';

$nimi = $_POST['nimi'] ?? '';
$aika = $_POST['aika'] ?? '';
$kuvaus = $_POST['kuvaus'] ?? '';
$kategoria = $_POST['kategoria'] ?? '';

if (!$nimi || !$aika || !$kategoria) {
  http_response_code(400);
  die("Missing fields");
}

// Convert HTML datetime-local to MySQL DATETIME
$aikaSql = str_replace('T', ' ', $aika) . ':00';

$stmt = $pdo->prepare("INSERT INTO tapahtumat (nimi, aika, kuvaus, kategoria) VALUES (?, ?, ?, ?)");
$stmt->execute([$nimi, $aikaSql, $kuvaus, $kategoria]);

header("Location: etusivu.php");
exit;

