function initNewsPageAnimations() {
    // Initialisation de AOS
    AOS.init({
        duration: 800,
        once: true,
        offset: 50,
        anchorPlacement: 'top-bottom',
        easing: "ease-in-out"
    });

    // Initialisation de Particles.js
    particlesJS("particles-js", {
        particles: {
            number: { value: 70, density: { enable: true, value_area: 800 } },
            color: { value: "#00ffff" },
            shape: { type: "circle" },
            opacity: { value: 0.5, random: false },
            size: { value: 3, random: true },
            line_linked: {
                enable: true,
                distance: 150,
                color: "#00ffff",
                opacity: 0.4,
                width: 1
            },
            move: {
                enable: true,
                speed: 1,
                direction: "none",
                out_mode: "out"
            },
        },
        interactivity: {
            detect_on: "canvas",
            events: {
                onhover: { enable: true, mode: "repulse" },
                onclick: { enable: true, mode: "push" },
                resize: true,
            },
            modes: {
                repulse: { distance: 100, duration: 0.4 },
                push: { particles_nb: 4 },
            },
        },
        retina_detect: true,
    });
}

// Initialisation dès le chargement du DOM
document.addEventListener("DOMContentLoaded", function(){
    initNewsPageAnimations();

    // Correction du bouton "Retour aux actualités"
    document.querySelectorAll('.button-modern').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = this.getAttribute('href');
        });
    });
});

// Réinitialisation lors d'une navigation sans rechargement complet (ex: via cache)
window.addEventListener("pageshow", function(){
    initNewsPageAnimations();
});