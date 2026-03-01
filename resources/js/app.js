import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

// Enregistrement centralisé de TOUS les plugins GSAP.
// C'est le seul endroit où registerPlugin doit être appelé.
// Comme gsap est un singleton, ce call est visible par tous les modules.
gsap.registerPlugin(ScrollTrigger);

import './page-transition.js';
import { initTypewriter } from './typewritter.js';
import { initAboutReveal } from './about-reveal.js';

// Sécurité Globale : Forcer la visibilité peu importe ce qui plante
const SAFETY_TIMEOUT = 2500;
window.addEventListener('load', () => {
    setTimeout(() => {
        // Enlever le site-loader
        const loader = document.querySelector('.site-loader');
        if (loader) {
            loader.style.opacity = '0';
            loader.style.pointerEvents = 'none';
            setTimeout(() => loader.remove(), 500);
        }

        // Forcer l'affichage de Barba en cas de crash
        const barbaContainer = document.querySelector('[data-barba="container"]');
        if (barbaContainer) {
            barbaContainer.style.opacity = '1';
            barbaContainer.style.visibility = 'visible';
            barbaContainer.style.transform = 'translate3d(0, 0, 0)';
        }

        // Enlever le grid transition s'il est resté bloqué
        const gridContainer = document.querySelector('.tt-page-transition');
        if (gridContainer) {
            gridContainer.style.display = 'none';
        }
    }, SAFETY_TIMEOUT);
});

// Sécurité : Force la suppression du loader si le JS plante ou met trop de temps
// Cela garantit que le site est toujours accessible, même sans animations.
window.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        const loader = document.querySelector('.site-loader');
        if (loader) {
            gsap.to(loader, { autoAlpha: 0, duration: 0.5, onComplete: () => loader.remove() });
        }
    }, 1000); // Back-up après 1s si Barba n'a pas pris le relais
});

function handleMenuDisplay() {
    const mainNavigation = document.getElementById('primary-navigation');
    if (!mainNavigation) return;
    if (window.innerWidth >= 768) {
        mainNavigation.classList.remove('hidden');
    } else {
        mainNavigation.classList.add('hidden');
    }
}

function initMenuToggle() {
    let mainNavigation = document.getElementById('primary-navigation');
    let mainNavigationToggle = document.getElementById('primary-menu-toggle');
    let mainNavigationClose = document.getElementById('primary-menu-close');
    if(mainNavigation && mainNavigationToggle) {
        mainNavigationToggle.onclick = null;
        mainNavigationToggle.addEventListener('click', function (e) {
            e.preventDefault();
            if (window.innerWidth < 768) {
                mainNavigation.classList.remove('hidden');
                mainNavigation.classList.add('flex');
            }
        });
    }
    if(mainNavigation && mainNavigationClose) {
        mainNavigationClose.onclick = null;
        mainNavigationClose.addEventListener('click', function (e) {
            e.preventDefault();
            mainNavigation.classList.add('hidden');
            mainNavigation.classList.remove('flex');
        });
    }
    handleMenuDisplay();
}

// Gère le resize pour corriger l'état du menu
window.addEventListener('resize', handleMenuDisplay);

window.addEventListener('load', function () {
    initMenuToggle();
});

// Pour Barba.js : expose la fonction pour la rappeler après chaque transition
window.initMenuToggle = initMenuToggle;
// window.initNavAnimation = undefined; // Désactive l'animation

window.addEventListener('DOMContentLoaded', function () {
    // Animation de reveal sur l'élément avec l'id 'reveal-test'
    const revealElement = document.getElementById('reveal-test');
    if (revealElement) {
        gsap.fromTo(revealElement, { opacity: 0, y: 50 }, { opacity: 1, y: 0, duration: 1, ease: 'power2.out' });
    }

    // Initialise l'animation About reveal si l'élément existe
    const aboutReveal = document.getElementById('about-reveal');
    if (aboutReveal) {
        initAboutReveal();
    }
});
