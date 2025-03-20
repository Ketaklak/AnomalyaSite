document.addEventListener("DOMContentLoaded", function () {
    // GSAP animations
    gsap.from(".subtle-glow", { opacity: 0, y: 20, duration: 1, stagger: 0.2 });
    gsap.from(".button-modern", { opacity: 0, scale: 0.9, duration: 1, ease: "power2.out" });

    // FAQ accordion
    document.querySelectorAll(".faq-question").forEach((item) => {
        item.addEventListener("click", function () {
            const answer = this.nextElementSibling;
            const isOpen = answer.style.display === "block";
            answer.style.display = isOpen ? "none" : "block";
            this.querySelector(".faq-toggle").textContent = isOpen ? "+" : "–";
            // Accessibilité : Ajouter des attributs ARIA
            item.setAttribute('aria-expanded', !isOpen);
        });
    });

    // AOS initialization avec offset réduit pour une réaction plus rapide
    AOS.init({
        duration: 800,
        offset: 20,
        anchorPlacement: 'top-bottom',
        once: false,
        easing: "ease-in-out"
    });

    // Particles.js initialization
    particlesJS("particles-js", {
        particles: {
            number: { value: 70, density: { enable: true, value_area: 800 } },
            color: { value: "#00ffff" },
            shape: { type: "circle", stroke: { width: 0, color: "#000000" } },
            opacity: { value: 0.5, random: false },
            size: { value: 3, random: true },
            line_linked: { enable: true, distance: 150, color: "#00ffff", opacity: 0.4, width: 1 },
            move: { enable: true, speed: 1, direction: "none", random: false, straight: false, out_mode: "out" },
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

    // Three.js - Rotating Wireframe Icosahedron
    const canvas = document.getElementById("hero-threejs-canvas");
    if (canvas) {
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        camera.position.z = 4;
        const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
        if (window.innerWidth > 1024) {
            renderer.setSize(window.innerWidth / 2, window.innerHeight);
            camera.aspect = (window.innerWidth / 2) / window.innerHeight;
        } else {
            renderer.setSize(window.innerWidth, 400);
            camera.aspect = window.innerWidth / 400;
        }
        renderer.setPixelRatio(window.devicePixelRatio);
        camera.updateProjectionMatrix();

        const geometry = new THREE.IcosahedronGeometry(1.5, 0);
        const material = new THREE.MeshBasicMaterial({ color: 0x00ffff, wireframe: true });
        const icosahedron = new THREE.Mesh(geometry, material);
        scene.add(icosahedron);

        function animate() {
            requestAnimationFrame(animate);
            icosahedron.rotation.x += 0.003;
            icosahedron.rotation.y += 0.003;
            renderer.render(scene, camera);
        }
        animate();

        window.addEventListener("resize", () => {
            if (window.innerWidth > 1024) {
                renderer.setSize(window.innerWidth / 2, window.innerHeight);
                camera.aspect = (window.innerWidth / 2) / window.innerHeight;
            } else {
                renderer.setSize(window.innerWidth, 400);
                camera.aspect = window.innerWidth / 400;
            }
            camera.updateProjectionMatrix();
        });
    }

    // Initialisation du carrousel Swiper pour les actualités
    var swiper = new Swiper(".mySwiper", {
        centeredSlides: true, // Centre la slide active
        slidesPerView: 3,
        spaceBetween: 30,
        // loop: true, // Active si tu as suffisamment de slides
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            1024: {
                slidesPerView: 3,
                spaceBetween: 30,
            },
            640: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            0: {
                slidesPerView: 1,
                spaceBetween: 10,
            },
        },
    });
});

// Optionnel : forçage d'un léger délai après le chargement complet pour rafraîchir AOS
window.addEventListener('load', function() {
    setTimeout(function(){
        AOS.refresh();
    }, 300);
});
