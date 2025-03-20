<?php
// admin/upload.php
session_start();
header('Content-Type: application/json');

// Vérifier les droits d’accès
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Accès refusé']);
    exit();
}

// Vérifier l’upload
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'Aucun fichier reçu.']);
    exit();
}

// Dossier de destination
$targetDir = "../uploads/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

$filename = basename($_FILES['file']['name']);
$targetFile = $targetDir . $filename;

// Déplacer le fichier
if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Impossible de déplacer le fichier.']);
    exit();
}

// TinyMCE attend un JSON avec "location"
echo json_encode([
    'location' => "../uploads/" . $filename
]);
