<?php
// admin/edit_news.php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login");
    exit();
}

require_once "../config.php";

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM news WHERE id = :id");
$stmt->execute(['id' => $id]);
$news = $stmt->fetch();

if (!$news) {
    $_SESSION['feedback'] = [
        'type' => 'error',
        'title' => 'Erreur',
        'message' => 'Actualité introuvable.'
    ];
    header("Location: index");
    exit();
}

// Génération du token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Récupération des catégories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'actualité - Admin Anomalya Corp</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com" defer></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- TinyMCE -->
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>

    <!-- Styles communs -->
    <link rel="stylesheet" href="../css/style.css">

    <script>
        document.addEventListener("DOMContentLoaded", function(){
            tinymce.init({
                selector: '#editor',
                height: 400,
                plugins: 'link image code lists media table',
                toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media table | code',
                relative_urls : false,
                remove_script_host : false,
                document_base_url : '../',
                images_upload_url: 'upload',
                automatic_uploads: true
            });
        });
    </script>
</head>
<body class="bg-gray-900 min-h-screen text-gray-200 p-8">

<section class="admin-glass p-8 rounded-xl max-w-3xl mx-auto">
    <h2 class="text-3xl font-bold text-blue-400 mb-6"><i class="fas fa-edit mr-2"></i>Modifier l'actualité</h2>
    <form method="post" action="save_news" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?= $news['id'] ?>">

        <div>
            <label class="block text-gray-300 mb-2">Titre</label>
            <input type="text" name="title" required value="<?= htmlspecialchars($news['title']) ?>"
                   class="w-full bg-gray-800 rounded-lg p-3 text-white">
        </div>

        <div>
            <label class="block text-gray-300 mb-2">Catégorie</label>
            <select name="category_id" class="w-full bg-gray-800 rounded-lg p-3 text-white">
                <option value="">-- Choisir une catégorie --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($news['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-gray-300 mb-2">Image actuelle</label>
            <?php if(!empty($news['image'])): ?>
                <img src="../<?= htmlspecialchars($news['image']) ?>" alt="image actuelle" class="rounded-lg mb-4 w-48">
            <?php else: ?>
                <p class="text-gray-400">Aucune image actuellement.</p>
            <?php endif; ?>

            <label class="block text-gray-300 mb-2">Changer d'image (optionnel)</label>
            <input type="file" name="image" accept="image/*"
                   class="w-full file:bg-blue-400 file:text-white file:border-0 file:rounded-lg file:p-2">
        </div>

        <div>
            <label class="block text-gray-300 mb-2">Date de publication</label>
            <input type="datetime-local" name="publish_date" value="<?= date('Y-m-d\TH:i', strtotime($news['publish_date'])) ?>"
                   class="w-full bg-gray-800 rounded-lg p-3 text-white">
        </div>

        <div>
            <label class="flex items-center">
                <input type="checkbox" name="is_pinned" <?= $news['is_pinned'] ? 'checked' : '' ?> class="mr-2">
                <span>Épingler cette actualité</span>
            </label>
        </div>

        <div>
            <label class="block text-gray-300 mb-2">Contenu</label>
            <textarea id="editor" name="content" class="w-full bg-gray-800 rounded-lg p-3 text-white"><?= htmlspecialchars($news['content']) ?></textarea>
        </div>

        <button type="submit" class="button-modern px-6 py-3 text-lg w-full">
            <i class="fas fa-save mr-2"></i>Enregistrer les modifications
        </button>
    </form>
</section>

</body>
</html>
