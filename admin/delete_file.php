<?php
// admin/delete_file.php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login");
    exit();
}

if (!isset($_GET['file']) || !isset($_GET['csrf_token'])) {
    die("Paramètres manquants.");
}

if ($_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Token CSRF invalide.");
}

$file = basename($_GET['file']);
$filepath = "../upload_files/" . $file;

if (file_exists($filepath)) {
    unlink($filepath);
    $_SESSION['feedback'] = ['type' => 'success', 'title' => 'Suppression réussie', 'message' => 'Le fichier a été supprimé.'];
} else {
    $_SESSION['feedback'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Fichier introuvable.'];
}

header("Location: file_manager");
exit();
?>
