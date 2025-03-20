<?php
// download_public.php
if (!isset($_GET['file'])) {
    die("Fichier non spécifié.");
}

$file = basename($_GET['file']);
$filepath = "upload_files/" . $file; // Ce fichier se trouve à la racine (au même niveau que index.php)

if (!file_exists($filepath)) {
    die("Fichier introuvable.");
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));
readfile($filepath);
exit();
?>
