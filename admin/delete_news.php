<?php
// admin/delete_news.php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}
require_once "../config.php";

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("DELETE FROM news WHERE id = :id");
$stmt->execute(['id' => $id]);

header("Location: index");
exit();
