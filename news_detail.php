<?php
// news_detail.php

require_once "config.php";

// Récupérer l'ID passé dans l'URL
$id = (int)($_GET['id'] ?? 0);

// Sélectionner l'actualité (join sur la catégorie) pour afficher is_pinned et category_name
$sql = "
    SELECT 
        n.*, 
        c.name AS category_name
    FROM news n
    LEFT JOIN categories c ON n.category_id = c.id
    WHERE 
        n.id = :id
        AND (
            n.status = 'published'
            OR (n.status = 'scheduled' AND n.publish_date <= NOW())
        )
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$news = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$news) {
    echo '<div class="glass p-8 max-w-3xl mx-auto mt-10 text-center text-red-400">
            News introuvable ou non accessible.
          </div>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= htmlspecialchars($news['title']) ?> - Anomalya Corp</title>

    <!-- Meta description : un extrait du contenu -->
    <meta name="description" content="<?= mb_strimwidth(strip_tags($news['content']), 0, 150, "...") ?>" />
    <link rel="icon" href="favicon.ico" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com" defer></script>

    <!-- Styles globaux -->
    <link rel="stylesheet" href="css/style.css" />

    <!-- Styles spécifiques à la page news -->
    <link rel="stylesheet" href="css/style_news.css" />

    <!-- AOS (Animate On Scroll) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js" defer></script>

    <!-- Particles.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js" defer></script>
</head>
<body>
<!-- Fond animé -->
<div id="particles-js"></div>

<header class="bg-gray-900 bg-opacity-80 p-5 sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="subtle-glow text-3xl font-bold">
            <a href="/">Anomalya Corp</a>
        </h1>
        <nav>
            <ul class="flex space-x-4 text-lg">
                <li><a href="/">Accueil</a></li>
                <li><a href="/#news">Actualités</a></li>
                <li><a href="/#services">Services</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="pt-20 pb-20 px-6">
    <!-- Ici, nous avons agrandi la zone en passant max-w-4xl à max-w-6xl -->
    <div class="glass p-8 max-w-6xl mx-auto">
        <!-- Titre de la news -->
        <h1 class="text-4xl md:text-5xl font-bold subtle-glow mb-8 text-center">
            <?= htmlspecialchars($news['title']) ?>
        </h1>

        <!-- Métadonnées (auteur, date, catégorie, et si épinglé) -->
        <div class="flex flex-wrap justify-center items-center gap-4 mb-8 text-gray-400">
            <!-- Auteur (si existe) -->
            <?php if(!empty($news['author'])): ?>
                <span>
                    <i class="fas fa-user mr-1 text-blue-400"></i>
                    <?= htmlspecialchars($news['author']) ?>
                </span>
            <?php endif; ?>

            <!-- Date de publication -->
            <span>
                <i class="fas fa-calendar-alt mr-1 text-blue-400"></i>
                Publié le <?= date('d/m/Y', strtotime($news['publish_date'])) ?>
            </span>

            <!-- Catégorie -->
            <span>
                <i class="fas fa-folder-open mr-1 text-blue-400"></i>
                <?= !empty($news['category_name'])
                    ? htmlspecialchars($news['category_name'])
                    : 'Sans catégorie'
                ?>
            </span>

            <!-- Épinglage -->
            <?php if($news['is_pinned']): ?>
                <span class="text-red-400">
                    <i class="fas fa-thumbtack mr-1"></i> Épinglé
                </span>
            <?php endif; ?>
        </div>

        <!-- Image principale -->
        <?php if (!empty($news['image'])): ?>
            <div class="mb-8 mx-auto max-w-2xl">
                <img
                        src="<?= htmlspecialchars($news['image']) ?>"
                        alt="Illustration de l'article"
                        class="w-full h-auto rounded-xl"
                        loading="lazy"
                >
            </div>
        <?php endif; ?>

        <!-- Contenu -->
        <article class="news-content text-gray-300 leading-relaxed">
            <?= $news['content'] ?>
        </article>

        <!-- Bouton de retour -->
        <div class="mt-12 text-center">
            <a href="/#news" class="button-modern px-6 py-3 text-lg hover:scale-105 transition-transform">
                <i class="fas fa-arrow-left mr-2"></i>Retour aux actualités
            </a>
        </div>
    </div>
</section>

<footer class="bg-gray-900 p-4 text-center text-gray-400">
    &copy; <?= date('Y') ?> Anomalya Corp - Tous droits réservés
</footer>

<script src="js/script.js" defer></script>
<script src="js/script_news.js" defer></script>
</body>
</html>
