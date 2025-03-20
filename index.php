<?php
// index.php
require_once "config.php";

// Récupérer les actualités depuis la base de données
$stmt = $pdo->query("SELECT * FROM news ORDER BY date_posted DESC");
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

    <!-- Font Awesome (pour les émojis ou icônes) -->
    <!-- Font Awesome (pour les émojis ou icônes) -->
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

    <!-- jQuery (chargé sans defer pour que $ soit disponible dès le début) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Slick Carousel CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>

    <!-- Slick Carousel JS (chargé avec defer) -->
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js" defer></script>

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

<!-- Header -->
<header class="bg-gray-900 bg-opacity-80 p-5 sticky top-0 z-50" data-aos="fade-down">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="subtle-glow text-4xl font-bold animate__animated animate__fadeInLeft">Anomalya Corp</h1>
        <nav>
            <ul class="flex space-x-4 text-lg">
                <!-- Extensions masquées via .htaccess -->
                <li><a href="#">Accueil</a></li>
                <li><a href="#news">Nos Actus</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#competences">Compétences</a></li>
                <li><a href="#faq">FAQ</a></li>
                <li><a href="#temoignages">Témoignages</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- Hero Section -->
<section id="hero" class="hero-section" data-aos="fade-up">
    <!-- Canvas Three.js (animation 3D) -->
    <canvas id="hero-threejs-canvas"></canvas>
    <div class="hero-left animate__animated animate__fadeInLeft">
        <h1 class="subtle-glow font-bold mb-4">L'Innovation Numérique</h1>
        <p class="text-gray-300 leading-relaxed mb-4">
            Chez Anomalya Corp, nous croyons que la technologie est le moteur du progrès. Notre objectif : simplifier et optimiser votre quotidien, que vous soyez un particulier ou une entreprise.<br/><br/>
            Grâce à une équipe pluridisciplinaire, nous proposons des solutions de développement web, de maintenance informatique, d'intelligence artificielle et de montage PC. Ensemble, bâtissons un avenir où l'innovation est à la portée de tous.
        </p>
        <a href="#services" class="inline-block button-modern text-white px-6 py-3 rounded-lg shadow-md">Découvrir nos services</a>
    </div>
</section>


<!-- Section Actualités (news) -->
<section id="news" class="py-16 px-6 text-center" data-aos="fade-up">
    <div class="container mx-auto">
        <div class="glass p-8">
            <h2 class="text-4xl font-bold subtle-glow mb-4">Actualités</h2>
            <?php if(count($newsList) > 0): ?>
                <!-- Carousel Slick -->
                <div class="news-carousel">
                    <?php foreach ($newsList as $news): ?>
                        <div class="px-4">
                            <div class="bg-gray-900 bg-opacity-70 p-4 rounded">
                                <?php if(!empty($news['image'])): ?>
                                    <!-- Image miniature -->
                                    <div class="mb-4 overflow-hidden rounded mx-auto w-32 h-32">
                                        <img
                                                src="<?= htmlspecialchars($news['image']) ?>"
                                                alt="News Image"
                                                class="object-cover w-full h-full"
                                        >
                                    </div>
                                <?php endif; ?>

                                <h3 class="text-2xl font-bold mb-2">
                                    <?= htmlspecialchars($news['title']) ?>
                                </h3>

                                <p class="mb-2 text-gray-200">
                                    <?= mb_strimwidth(strip_tags($news['content']), 0, 100, "...") ?>
                                </p>
                                <small class="block mb-2 text-gray-400">Publié le <?= $news['date_posted'] ?></small>

                                <a href="news_detail?id=<?= $news['id'] ?>"
                                   class="button-modern px-4 py-2 inline-block">
                                    Voir plus
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
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

<!-- Services Section avec émojis -->
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
                        <a href="https://www.google.com/maps/place/Anomalya+Corp/@42.6384108,2.869776,19.75z/data=!4m6!3m5!1s0x12b0716624330d73:0x2d1341085e6373fc!8m2!3d42.6384969!4d2.8696716!16s%2Fg%2F11v14hk5wf?entry=ttu&g_ep=EgoyMDI1MDMwOC4wIKXMDSoJLDEwMjExNDUzSAFQAw%3D%3D" target="_blank" rel="noopener" class="text-blue-400 underline">
                            Voir la fiche Google
                        </a>
                    </p>
                    <div class="mt-4">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2892.2019499572384!2d2.8696716157383515!3d42.63849687916602!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12b0716624330d73%3A0x2d1341085e6373fc!2sAnomalya%20Corp!5e0!3m2!1sfr!2sfr!4v1694875196424!5m2!1sfr!2sfr" class="w-full h-48 rounded" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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
                    <button type="submit" class="w-full button-modern text-white px-4 py-2 rounded-lg shadow-md">Envoyer</button>
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
