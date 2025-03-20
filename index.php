<?php
// index.php (partie publique)

// Inclure la config et la connexion PDO
require_once "config.php";

// Récupérer le terme de recherche depuis l'URL (s'il existe)
$search = $_GET['search'] ?? '';

// Préparer la requête pour récupérer seulement les actualités « publiées »
// ou « planifiées » dont la date de publication est déjà échue.
// On fait aussi une jointure pour récupérer le nom de la catégorie.
$sql = "
    SELECT news.*, categories.name AS category_name
    FROM news
    LEFT JOIN categories ON news.category_id = categories.id
    WHERE 
        (news.status = 'published')
        OR 
        (news.status = 'scheduled' AND news.publish_date <= NOW())
";
// On termine la requête par l’ordre de tri
$sql .= " ORDER BY news.is_pinned DESC, news.publish_date DESC";

$stmt = $pdo->prepare($sql);

// Lier la valeur de recherche si nécessaire
if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

$stmt->execute();
$newsList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Anomalya Corp</title>
    <meta name="description" content="Anomalya Corp - L'innovation numérique au service des professionnels IT et des particuliers. Découvrez nos services en développement web, maintenance & réparation informatique, montage PC et intelligence artificielle." />
    <meta name="author" content="Anomalya Corp" />
    <link rel="icon" href="favicon.ico" />

    <!-- Font Awesome (pour les émojis, etc.) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com" defer></script>

    <!-- Lien vers notre CSS personnalisé -->
    <link rel="stylesheet" href="css/style.css" />

    <!-- GSAP Animation Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js" defer></script>

    <!-- AOS (Animate On Scroll) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js" defer></script>

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- Particles.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js" defer></script>

    <!-- Three.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js" defer></script>

    <!-- SweetAlert2 pour la popup -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

    <!-- jQuery (si encore nécessaire pour d'autres scripts) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Intégration de Swiper (alternative à Slick) -->
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

    <!-- Notre script personnalisé -->
    <script src="js/script.js" defer></script>
</head>
<body>
<!-- Détection du paramètre "sent" pour afficher la popup via SweetAlert2 -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.has('sent')) {
            Swal.fire({
                title: "Message envoyé",
                icon: "success",
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        }
    });
</script>

<!-- Fond animé Particles.js -->
<div id="particles-js"></div>

<header class="bg-gray-900/95 backdrop-blur-sm fixed w-full top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-blue-400">Anomalya Corp</h1>

        <!-- Menu Hamburger -->
        <button id="mobileMenu" class="md:hidden text-2xl text-white">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Navigation -->
        <nav id="mainNav" class="hidden md:block absolute md:relative top-full left-0 w-full md:w-auto bg-gray-900 md:bg-transparent transition-all duration-300">
            <ul class="flex flex-col md:flex-row text-center py-4 md:py-0 space-y-4 md:space-y-0 md:space-x-6">
                <li><a href="#hero" class="block px-4 py-2 hover:text-blue-400 transition-colors">Accueil</a></li>
                <li><a href="#services" class="block px-4 py-2 hover:text-blue-400 transition-colors">Services</a></li>
                <li><a href="#news" class="block px-4 py-2 hover:text-blue-400 transition-colors">Actualités</a></li>
                <li><a href="#competences" class="block px-4 py-2 hover:text-blue-400 transition-colors">Compétences</a></li>
                <li><a href="#contact" class="block px-4 py-2 hover:text-blue-400 transition-colors">Contact</a></li>
            </ul>
        </nav>
    </div>
</header>

<script>
    document.getElementById('mobileMenu').addEventListener('click', function() {
        const nav = document.getElementById('mainNav');
        nav.classList.toggle('hidden');
        nav.classList.toggle('mobile-active');
    });
</script>

<style>
    .mobile-active {
        max-height: 100vh;
        overflow-y: auto;
    }

    @media (min-width: 768px) {
        #mainNav {
            max-height: none !important;
        }
    }
</style>

<!-- Hero Section -->
<section id="hero" class="hero-section" data-aos="fade-up">
    <!-- Canvas Three.js (animation 3D) -->
    <canvas id="hero-threejs-canvas"></canvas>
    <!-- Texte du Hero -->
    <div class="hero-left animate__animated animate__fadeInLeft">
        <h1 class="subtle-glow font-bold mb-4">L'Innovation Numérique</h1>
        <p class="text-gray-300 leading-relaxed mb-4">
            Chez Anomalya Corp, nous croyons que la technologie est le moteur du progrès. Notre objectif : simplifier et optimiser votre quotidien, que vous soyez un particulier ou une entreprise.<br/><br/>
            Grâce à une équipe pluridisciplinaire, nous proposons des solutions de développement web, de maintenance informatique, d'intelligence artificielle et de montage PC. Ensemble, bâtissons un avenir où l'innovation est à la portée de tous.
        </p>
        <a href="#services" class="inline-block button-modern text-white px-6 py-3 rounded-lg shadow-md">Découvrir nos services</a>
    </div>
</section>

<!-- Section Actualités (news) avec Swiper -->
<section id="news" class="py-16 px-6 text-center" data-aos="fade-up">
    <div class="container mx-auto">
        <div class="glass p-8">
            <h2 class="text-4xl font-bold subtle-glow mb-4">Nos Actus</h2>
            <?php if(count($newsList) > 0): ?>
                <!-- Swiper Container -->
                <div class="swiper mySwiper">
                    <!-- Swiper Wrapper -->
                    <div class="swiper-wrapper">
                        <?php foreach ($newsList as $news): ?>
                            <!-- Each Slide -->
                            <div class="swiper-slide px-4">
                                <div class="relative bg-gray-900 bg-opacity-70 p-6 rounded min-h-[400px] max-w-sm mx-auto flex flex-col justify-between text-center">
                                    <!-- Badge épinglée -->
                                    <?php if (!empty($news['is_pinned']) && $news['is_pinned']): ?>
                                        <div class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-xs uppercase">
                                            Épinglée
                                        </div>
                                    <?php endif; ?>
                                    <!-- Image si disponible -->
                                    <?php if(!empty($news['image'])): ?>
                                        <div class="mb-4 w-32 h-32 mx-auto overflow-hidden rounded">
                                            <img src="<?= htmlspecialchars($news['image']) ?>"
                                                 alt="News Image"
                                                 class="object-cover w-full h-full">
                                        </div>
                                    <?php endif; ?>
                                    <!-- Titre -->
                                    <h3 class="text-2xl font-bold mb-2">
                                        <?= htmlspecialchars($news['title']) ?>
                                    </h3>
                                    <!-- Catégorie -->
                                    <p class="mb-1 text-blue-400">
                                        <?= !empty($news['category_name']) ? htmlspecialchars($news['category_name']) : 'Sans catégorie' ?>
                                    </p>
                                    <!-- Extrait du contenu -->
                                    <p class="mb-2 text-gray-200 leading-relaxed">
                                        <?= mb_strimwidth(strip_tags($news['content']), 0, 150, "...") ?>
                                    </p>
                                    <!-- Date de publication -->
                                    <small class="block mb-2 text-gray-400">
                                        Publié le <?= htmlspecialchars($news['publish_date']) ?>
                                    </small>
                                    <!-- Bouton "Voir plus" -->
                                    <div>
                                        <a href="news_detail?id=<?= $news['id'] ?>"
                                           class="button-modern px-4 py-2 inline-block mt-2">
                                            Voir plus
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Swiper Pagination (dots) -->
                    <div class="swiper-pagination"></div>
                    <!-- Swiper Navigation -->
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            <?php else: ?>
                <p class="text-gray-300">Aucune actualité pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Section "Pourquoi nous choisir ?" -->
<section id="why" class="py-16 px-6 text-center" data-aos="fade-up">
    <div class="container mx-auto">
        <div class="glass p-8">
            <h2 class="text-4xl font-bold subtle-glow mb-6">Pourquoi nous choisir ?</h2>
            <div class="why-container">
                <div class="feature-card glass">
                    <div class="feature-icon">⚡</div>
                    <h3 class="text-xl font-semibold text-blue-400 mb-1">Rapidité & Performance</h3>
                    <p class="text-sm text-gray-300">Des solutions optimisées pour une expérience fluide et rapide.</p>
                </div>
                <div class="feature-card glass">
                    <div class="feature-icon">🔒</div>
                    <h3 class="text-xl font-semibold text-blue-400 mb-1">Sécurité Renforcée</h3>
                    <p class="text-sm text-gray-300">Des pratiques de cybersécurité de pointe pour protéger vos données.</p>
                </div>
                <div class="feature-card glass">
                    <div class="feature-icon">🤝</div>
                    <h3 class="text-xl font-semibold text-blue-400 mb-1">Approche Humaine</h3>
                    <p class="text-sm text-gray-300">Un accompagnement personnalisé, à l’écoute de vos besoins.</p>
                </div>
                <div class="feature-card glass">
                    <div class="feature-icon">🌐</div>
                    <h3 class="text-xl font-semibold text-blue-400 mb-1">Vision Globale</h3>
                    <p class="text-sm text-gray-300">Une expertise couvrant le web, l'IA, la maintenance et bien plus encore.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-16 px-6" data-aos="fade-up">
    <div class="container mx-auto">
        <div class="glass p-8 text-center">
            <h2 class="text-4xl font-bold subtle-glow mb-8">Nos Services</h2>
            <div class="services-grid">
                <div class="glass p-4">
                    <div class="service-icon mb-4">💻</div>
                    <h3 class="text-xl font-semibold text-blue-400 mb-2">Développement Web</h3>
                    <p class="text-gray-300 text-sm leading-relaxed">
                        Création de sites professionnels sur mesure pour entreprises.<br />
                        Solutions e-commerce, intranet et sites vitrines.
                    </p>
                </div>
                <div class="glass p-4">
                    <div class="service-icon mb-4">🔧</div>
                    <h3 class="text-xl font-semibold text-blue-400 mb-2">Maintenance & Réparation</h3>
                    <p class="text-gray-300 text-sm leading-relaxed">
                        Assistance technique et dépannage pour particuliers.<br />
                        Réparation d'ordinateurs et optimisation des systèmes.
                    </p>
                </div>
                <div class="glass p-4">
                    <div class="service-icon mb-4">🤖</div>
                    <h3 class="text-xl font-semibold text-blue-400 mb-2">Intelligence Artificielle</h3>
                    <p class="text-gray-300 text-sm leading-relaxed">
                        Conseils sur les outils et technologies IA pour transformer vos données.
                    </p>
                </div>
                <div class="glass p-4">
                    <div class="service-icon mb-4">🖥️</div>
                    <h3 class="text-xl font-semibold text-blue-400 mb-2">Montage PC</h3>
                    <p class="text-gray-300 text-sm leading-relaxed">
                        Conseils personnalisés et montage professionnel pour un PC performant.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Compétences Section -->
<section id="competences" class="py-16 px-6 text-center" data-aos="fade-up">
    <div class="container mx-auto">
        <div class="glass p-8">
            <h2 class="text-4xl font-bold subtle-glow mb-4">Nos Compétences</h2>
            <p class="mt-2 text-gray-300 mb-6">Nous maîtrisons les technologies et outils suivants :</p>
            <div class="flex flex-wrap justify-center">
                <span class="badge">HTML / CSS</span>
                <span class="badge">JavaScript / React</span>
                <span class="badge">PHP / Laravel</span>
                <span class="badge">Python / IA</span>
                <span class="badge">Sécurité Informatique</span>
                <span class="badge">MySQL / MongoDB</span>
                <span class="badge">Cloud & DevOps</span>
                <span class="badge">WordPress & CMS</span>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section id="faq" class="py-16 px-6 text-center" data-aos="fade-up">
    <div class="container mx-auto">
        <div class="glass p-8">
            <h2 class="text-4xl font-bold subtle-glow mb-4">FAQ</h2>
            <p class="mt-2 text-gray-300 mb-8">Les réponses aux questions les plus fréquentes.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <div>
                    <div class="faq-item mb-4" data-aos="fade-right">
                        <div class="faq-question">
                            <span>Quels services proposez-vous ?</span>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>
                                Nous offrons des services de développement web, d'intelligence artificielle, de maintenance & réparation informatique et de montage PC.
                            </p>
                        </div>
                    </div>
                    <div class="faq-item" data-aos="fade-right">
                        <div class="faq-question">
                            <span>Comment se déroule la phase de conception ?</span>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>
                                Nous réalisons une analyse approfondie de vos besoins, suivie d'une conception et d'un prototypage. Après validation, nous développons et déployons la solution pour maximiser votre impact en ligne.
                            </p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="faq-item mb-4" data-aos="fade-left">
                        <div class="faq-question">
                            <span>Proposez-vous un support pour les particuliers ?</span>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>
                                Oui, notre service de maintenance & réparation informatique ainsi que notre service de montage PC sont spécialement conçus pour répondre aux besoins des particuliers.
                            </p>
                        </div>
                    </div>
                    <div class="faq-item" data-aos="fade-left">
                        <div class="faq-question">
                            <span>Est-ce que vous assurez la sécurité des données ?</span>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>
                                Absolument, nous appliquons les meilleures pratiques en matière de cybersécurité pour protéger vos données, de l'hébergement au déploiement.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Témoignages Section -->
<section id="temoignages" class="py-16 px-6 text-center" data-aos="fade-up">
    <div class="container mx-auto">
        <div class="glass p-8">
            <h2 class="text-4xl font-bold subtle-glow mb-8">Témoignages de nos clients</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <article class="p-6 glass shadow-lg" data-aos="zoom-in">
                    <p class="text-gray-300">
                        "Je suis extrêmement satisfait des services de Anomalya Corp. Excellent service client, produits de haute qualité, et une expérience globale exceptionnelle. Je recommande vivement !"
                    </p>
                    <span class="block mt-4 text-blue-400">- Fabien L, CEO LeCapitole</span>
                </article>
                <article class="p-6 glass shadow-lg" data-aos="zoom-in">
                    <p class="text-gray-300">
                        "Prix abordable, rapide, fait du bon travail."
                    </p>
                    <span class="block mt-4 text-blue-400">- Tristan S, Particulier</span>
                </article>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-16 text-center" data-aos="fade-up">
    <div class="container mx-auto">
        <div class="glass p-8">
            <h2 class="text-4xl font-bold subtle-glow mb-4">Contactez-nous</h2>
            <p class="mt-2 text-gray-300 mb-6">Discutons de votre projet</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Bloc Informations -->
                <div class="glass p-6 rounded-lg shadow-md text-left" data-aos="fade-right">
                    <h3 class="text-2xl font-semibold text-blue-400 mb-4">Informations</h3>
                    <p class="text-gray-300 mb-2"><strong>Téléphone :</strong> 07 83 31 45 14</p>
                    <p class="text-gray-300 mb-2"><strong>Adresse :</strong> 25 rue des Iris, 66450 Pollestres</p>
                    <p class="text-gray-300 mb-2"><strong>Horaires :</strong> Lun-Ven : 9h30 - 18h</p>
                    <p class="text-gray-300 mb-2">
                        <strong>Fiche Google :</strong>
                        <a href="https://www.google.com/maps/place/Anomalya+Corp/@42.6384108,2.869776,19.75z/data=!4m6!3m5!1s0x12b0716624330d73:0x2d1341085e6373fc!8m2!3d42.6384969!4d2.8696716!16s%2Fg%2F11v14hk5wf?entry=ttu"
                           target="_blank"
                           rel="noopener"
                           class="text-blue-400 underline">
                            Voir la fiche Google
                        </a>
                    </p>
                    <div class="mt-4">
                        <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2892.2019499572384!2d2.8696716157383515!3d42.63849687916602!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12b0716624330d73%3A0x2d1341085e6373fc!2sAnomalya%20Corp!5e0!3m2!1sfr!2sfr!4v1694875196424!5m2!1sfr!2sfr"
                                class="w-full h-48 rounded"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
                <!-- Formulaire -->
                <form action="contact.php" method="post" class="glass p-6 rounded-lg shadow-md" data-aos="fade-left">
                    <div class="mb-4">
                        <label for="name" class="block mb-1 text-gray-300 text-sm">Nom :</label>
                        <input type="text" id="name" name="name" required class="w-full p-2 text-sm rounded" />
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block mb-1 text-gray-300 text-sm">Email :</label>
                        <input type="email" id="email" name="email" required class="w-full p-2 text-sm rounded" />
                    </div>
                    <div class="mb-4">
                        <label for="subject" class="block mb-1 text-gray-300 text-sm">Sujet :</label>
                        <input type="text" id="subject" name="subject" required class="w-full p-2 text-sm rounded" />
                    </div>
                    <div class="mb-4">
                        <label for="message" class="block mb-1 text-gray-300 text-sm">Message :</label>
                        <textarea id="message" name="message" rows="3" required class="w-full p-2 text-sm rounded"></textarea>
                    </div>
                    <button type="submit" class="w-full button-modern text-white px-4 py-2 rounded-lg shadow-md">
                        Envoyer
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 p-4 text-center text-gray-400" data-aos="fade-up">
    &copy; 2025 Anomalya Corp - Tous droits réservés.
</footer>
</body>
</html>
