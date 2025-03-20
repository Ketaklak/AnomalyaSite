<?php
// admin/index.php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login");
    exit();
}

require_once "../config.php";

// Gestion des messages de feedback
$feedback = '';
if (isset($_SESSION['feedback'])) {
    $feedback = $_SESSION['feedback'];
    unset($_SESSION['feedback']);
}

// Recherche et récupération des news avec catégories et épinglage
$search = $_GET['search'] ?? '';
$stmt = $pdo->prepare("SELECT news.*, categories.name AS category_name FROM news
                       LEFT JOIN categories ON news.category_id = categories.id
                       WHERE news.title LIKE :search OR categories.name LIKE :search
                       ORDER BY news.is_pinned DESC, news.publish_date DESC");
$stmt->execute(['search' => "%$search%"]);
$newsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Génération de token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Administration - Anomalya Corp</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Styles communs -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- TinyMCE amélioré -->
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .admin-glass {
            background: rgba(17, 24, 39, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .news-card:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease;
        }
    </style>
</head>
<body class="bg-gray-900 min-h-screen text-gray-200">

<header class="admin-glass p-5 sticky top-0 z-50 flex justify-between items-center">
    <h1 class="subtle-glow text-2xl font-bold">
        <a href="../index" class="hover:text-blue-400">Anomalya Corp</a> - Admin
    </h1>
    <nav>
        <ul class="flex space-x-4">
            <li>
                <a href="file_manager" class="button-modern px-4 py-2 hover:bg-red-600">
                    <i class="fas fa-files-alt mr-2"></i>Files Manager
                </a>
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

    <section class="admin-glass p-8 rounded-xl mb-12">
        <h2 class="text-3xl font-bold text-blue-400 mb-6">Créer une actualité</h2>
        <form method="post" action="save_news" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="action" value="create">

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-gray-300 mb-2">Titre</label>
                    <input type="text" name="title" required
                           class="w-full bg-gray-800 rounded-lg p-3 text-white">
                </div>
                <div>
                    <label class="block text-gray-300 mb-2">Catégorie</label>
                    <select name="category_id" class="w-full bg-gray-800 rounded-lg p-3 text-white">
                        <option value="">-- Choisir une catégorie --</option>
                        <?php
                        $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
                        foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-300 mb-2">Image principale</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full file:bg-blue-400 file:text-white file:border-0 file:rounded-lg file:p-2">
                </div>
                <div>
                    <label class="block text-gray-300 mb-2">Date de publication (optionnel)</label>
                    <input type="datetime-local" name="publish_date"
                           class="w-full bg-gray-800 rounded-lg p-3 text-white">
                </div>

                <!-- Ajout du statut -->
                <div>
                    <label class="block text-gray-300 mb-2">Statut</label>
                    <select name="status" class="w-full bg-gray-800 rounded-lg p-3 text-white">
                        <option value="draft">Brouillon</option>
                        <option value="published">Publié</option>
                        <option value="scheduled">Planifié</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_pinned" class="mr-2">
                        <span>Épingler cette actualité</span>
                    </label>
                </div>
                <div>
                    <label class="block text-gray-300 mb-2">Contenu</label>
                    <textarea id="editor" name="content" class="w-full bg-gray-800 rounded-lg p-3 text-white"></textarea>
                </div>
                <button type="submit" class="button-modern px-6 py-3 text-lg w-full">
                    <i class="fas fa-save mr-2"></i>Publier
                </button>
            </div>
        </form>
    </section>

    <section class="admin-glass p-8 rounded-xl">
        <h2 class="text-3xl font-bold text-blue-400 mb-6">Actualités publiées</h2>
        <form method="GET" class="mb-6">
            <input type="search" name="search" placeholder="Rechercher..."
                   class="bg-gray-800 p-3 rounded-lg text-white w-full">
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($newsList as $news): ?>
                <div class="news-card bg-gray-800 rounded-xl p-6">
                    <h3 class="text-xl font-semibold"><?= htmlspecialchars($news['title']) ?></h3>
                    <p class="text-sm">
                        <?= htmlspecialchars($news['category_name'] ?? 'Sans catégorie') ?>
                    </p>
                    <p>
                        <?= $news['is_pinned'] ? '<i class="fas fa-thumbtack text-red-400"></i> Épinglée' : '' ?>
                    </p>
                    <div class="mt-4 flex space-x-4">
                        <a href="edit_news?id=<?= $news['id'] ?>" class="text-blue-400">Modifier</a>
                        <a href="delete_news?id=<?= $news['id'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>"
                           class="text-red-400"
                           onclick="return confirm('Voulez-vous vraiment supprimer cette actualité ?');">
                            Supprimer
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<script>
    tinymce.init({
        selector: '#editor',
        height: 400,
        plugins: 'link image code table lists media',
        toolbar: 'undo redo | blocks | bold italic | align | bullist numlist | table | code',
        images_upload_url: 'upload',
        automatic_uploads: true
    });
</script>

</body>
</html>
