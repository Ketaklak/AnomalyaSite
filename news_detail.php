<?php
require_once "config.php";

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM news WHERE id = :id");
$stmt->execute(['id' => $id]);
$news = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$news) {
    echo '<div class="glass p-8 max-w-3xl mx-auto mt-10 text-center text-red-400" data-aos="fade-up">News introuvable ou ID inexistant.</div>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= htmlspecialchars($news['title']) ?> - Anomalya Corp</title>
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

    <!-- Bibliothèques d'animation -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js" defer></script>

    <!-- Particles.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js" defer></script>
</head>
<body>
<!-- Fond animé -->
<div id="particles-js"></div>

<!-- Header animé -->
<header class="bg-gray-900 bg-opacity-80 p-5 sticky top-0 z-50" data-aos="fade-down">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="subtle-glow text-3xl font-bold animate__animated animate__fadeInLeft">
            <a href="/">Anomalya Corp</a>
        </h1>
        <nav>
            <ul class="flex space-x-4 text-lg">
                <li><a href="/" class="hover:text-blue-400 transition">Accueil</a></li>
                <li><a href="/#news" class="hover:text-blue-400 transition">Actualités</a></li>
                <li><a href="/#services" class="hover:text-blue-400 transition">Services</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- Contenu principal -->
<section class="pt-20 pb-20 px-6" data-aos="fade-up">
    <div class="glass p-8 max-w-4xl mx-auto">
        <!-- Titre -->
        <h1 class="text-4xl md:text-5xl font-bold subtle-glow mb-8 text-center">
            <?= htmlspecialchars($news['title']) ?>
        </h1>

        <!-- Métadonnées -->
        <div class="flex justify-center items-center space-x-4 mb-8">
            <?php if(!empty($news['author'])): ?>
                <span class="text-blue-400">
                        <i class="fas fa-user mr-2"></i><?= htmlspecialchars($news['author']) ?>
                    </span>
            <?php endif; ?>
            <span class="text-gray-400">
                    <i class="fas fa-calendar-alt mr-2"></i><?= date('d/m/Y', strtotime($news['date_posted'])) ?>
                </span>
        </div>

        <!-- Image principale -->
        <?php if (!empty($news['image'])): ?>
            <div class="mb-8 rounded-xl overflow-hidden" data-aos="zoom-in">
                <img
                        src="<?= htmlspecialchars($news['image']) ?>"
                        alt="Illustration de l'article"
                        class="w-full h-96 object-cover"
                        loading="lazy"
                >
            </div>
        <?php endif; ?>

        <!-- Contenu -->
        <article class="news-content text-gray-300">
            <?= $news['content'] ?>
        </article>

        <!-- Bouton de retour -->
        <div class="mt-12 text-center" data-aos="fade-up">
            <a href="/#news" class="button-modern px-6 py-3 text-lg hover:scale-105 transition-transform">
                <i class="fas fa-arrow-left mr-2"></i>Retour aux actualités
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 p-4 text-center text-gray-400" data-aos="fade-up">
    &copy; <?= date('Y') ?> Anomalya Corp - Tous droits réservés
</footer>

<!-- Scripts globaux -->
<script src="js/script.js" defer></script>

<!-- Scripts spécifiques à la page news -->
<script src="js/script_news.js" defer></script>
</body>
</html>
