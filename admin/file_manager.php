<?php
// admin/file_manager.php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login");
    exit();
}

require_once "../config.php"; // Inclusion de la config pour la cohérence

// Gestion des messages de feedback
$feedback = '';
if (isset($_SESSION['feedback'])) {
    $feedback = $_SESSION['feedback'];
    unset($_SESSION['feedback']);
}

// Traitement de l'upload du fichier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['feedback'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Token CSRF invalide.'];
        header("Location: file_manager");
        exit();
    }

    $uploadDir = "../upload_files/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $originalName = basename($_FILES['file']['name']);
    // Génération d'un nom unique pour éviter les collisions
    $uniqueName = uniqid() . '_' . $originalName;
    $targetFile = $uploadDir . $uniqueName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        $_SESSION['feedback'] = ['type' => 'success', 'title' => 'Upload réussi', 'message' => 'Votre fichier a été uploadé avec succès.'];
    } else {
        $_SESSION['feedback'] = ['type' => 'error', 'title' => 'Erreur', 'message' => 'Erreur lors de l\'upload du fichier.'];
    }
    header("Location: file_manager");
    exit();
}

// Génération du token CSRF s'il n'existe pas
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Récupération de la liste des fichiers dans le dossier uploads
$uploadDir = "../upload_files/";
$files = [];
if (is_dir($uploadDir)) {
    $files = array_diff(scandir($uploadDir), array('.', '..'));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des fichiers - Anomalya Corp</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com" defer></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Feuilles de style communes -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-gray-900 min-h-screen text-gray-200">
<header class="admin-glass p-5 sticky top-0 z-50 flex justify-between items-center">
    <h1 class="subtle-glow text-2xl font-bold">
        <a href="../index" class="hover:text-blue-400">Anomalya Corp</a> - Admin
    </h1>
    <nav>
        <ul class="flex space-x-4">
            <li>
                <a href="logout" class="button-modern px-4 py-2 hover:bg-red-600">
                    <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                </a>
            </li>
        </ul>
    </nav>
</header>
<main class="container mx-auto px-4 py-8">
    <?php if ($feedback): ?>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                Swal.fire({
                    icon: '<?= htmlspecialchars($feedback['type']) ?>',
                    title: '<?= htmlspecialchars($feedback['title']) ?>',
                    text: '<?= htmlspecialchars($feedback['message']) ?>'
                });
            });
        </script>
    <?php endif; ?>

    <!-- Section d'upload -->
    <section class="admin-glass p-8 rounded-xl mb-12">
        <h2 class="text-3xl font-bold text-blue-400 mb-6">Uploader un fichier</h2>
        <form method="post" action="file_manager" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div>
                <label class="block text-gray-300 mb-2">Choisir un fichier</label>
                <input type="file" name="file" required class="w-full bg-gray-800 rounded-lg p-3 text-white">
            </div>
            <button type="submit" class="button-modern px-6 py-3 text-lg w-full">
                <i class="fas fa-upload mr-2"></i>Uploader
            </button>
        </form>
    </section>

    <!-- Section de gestion des fichiers -->
    <section class="admin-glass p-8 rounded-xl">
        <h2 class="text-3xl font-bold text-blue-400 mb-6">Fichiers disponibles</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($files as $file): ?>
                <?php
                // Génération du lien de téléchargement public
                $downloadLink = "../download_public?file=" . urlencode($file);
                ?>
                <div class="bg-gray-800 rounded-xl p-6">
                    <h3 class="text-xl font-semibold"><?= htmlspecialchars($file) ?></h3>
                    <div class="mt-4 flex flex-col space-y-2">
                        <a href="<?= $downloadLink ?>" class="text-blue-400" target="_blank">
                            <i class="fas fa-download mr-2"></i>Télécharger
                        </a>
                        <a href="delete_file?file=<?= urlencode($file) ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>" class="text-red-400" onclick="return confirm('Voulez-vous vraiment supprimer ce fichier ?');">
                            <i class="fas fa-trash mr-2"></i>Supprimer
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
