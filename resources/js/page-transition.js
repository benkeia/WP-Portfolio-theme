import barba from "@barba/core";
import gsap from "gsap";
import { initTypewriter } from "./typewritter.js";
import { initHeroTextResize, cleanupFitty } from "./hero-text-resize.js";
import CurrentProjectFilters from "./project-filters.js";
import { initTextReveal, cleanupTextReveal } from "./text-reveal.js";
import { initGallery, cleanupGallery } from "./gallery.js";
// Import dynamique de LegoSimulation pour réduire le bundle initial
// import { LegoSimulation } from "./lego-scene.js"; 

// CONFIG
const TRANSITION_EL = ".tt-page-transition";
const ITEM_CLASS = ".tt-ptr-item";
const ITEM_COUNT = 72; 

// Variable pour stocker l'instance active
let legoSimulationInstance = null;
let legoLoading = false;

// --- HELPER: GENERATION DE GRID ---
function ensureGridOverlay() {
  const container = document.querySelector(TRANSITION_EL);
  if (!container) return; // Sécurité
  
  // Si déjà peuplé, on ne refait rien (Performance)
  if (container.childElementCount >= ITEM_COUNT) return;

  const frag = document.createDocumentFragment();
  
  for (let i = 0; i < ITEM_COUNT; i++) {
    const div = document.createElement("div");
    div.classList.add("tt-ptr-item");
    frag.appendChild(div);
  }
  container.appendChild(frag);
}

// --- HELPER: MENU ---
function updateActiveMenu(nextUrl) {
    // Astuce: Normaliser les URLs pour éviter les soucis de slash final
    const currentPath = new URL(nextUrl).pathname.replace(/\/$/, "");
    const navLinks = document.querySelectorAll('#primary-navigation .menu-item a');

    navLinks.forEach(link => {
        const linkPath = new URL(link.href).pathname.replace(/\/$/, "");
        const parentLi = link.closest('.menu-item');
        
        if (parentLi) {
            if (linkPath === currentPath) parentLi.classList.add('current-menu-item');
            else parentLi.classList.remove('current-menu-item');
        }
    });
}

// --- HELPER: Init Lego Scene ---
function initLegoSceneIfNeeded(scope = document) {
    // On cherche dans le scope donné (la nouvelle page) ou dans tout le document
    const container = scope.querySelector('#lego-canvas-container');
    
    // Protection race condition : éviter double import async
    if (container && !legoSimulationInstance && !legoLoading) {
        legoLoading = true;
        
        // Import dynamique : Three.js et Rapier ne seront chargés QUE maintenant
        import("./lego-scene.js").then(({ LegoSimulation }) => {
            // requestAnimationFrame assure que le DOM est 'peint' et a des dimensions
            requestAnimationFrame(() => {
                legoSimulationInstance = new LegoSimulation(container);
                legoLoading = false;
                // Exposer globalement pour le bouton
                window.legoSimulation = legoSimulationInstance;
            });
        }).catch(err => {
            console.error("Erreur lors du chargement de la scène Lego:", err);
            legoLoading = false;
        });
    }
}

// --- HELPER: Destroy Lego Scene ---
function destroyLegoScene() {
    if (legoSimulationInstance) {
        legoSimulationInstance.destroy();
        legoSimulationInstance = null;
        window.legoSimulation = null;
    }
}

// --- INITIALIZATION ---
ensureGridOverlay();
initTypewriter();
updateActiveMenu(window.location.href);

// Initialiser les filtres au chargement
new CurrentProjectFilters();

// --- MOBILE CHECK ---
// Si écran < 768px, on désactive complètement Barba et Three.js
if (window.innerWidth < 768) {
    document.addEventListener("DOMContentLoaded", () => {
        // Suppression immédiate du loader
        const loader = document.querySelector('.site-loader');
        if (loader) loader.style.display = 'none';

        // Init JS léger uniquement
        initHeroTextResize();
        initTextReveal();
        initGallery();
        initTypewriter();
        
        initMenuToggle();

        console.log("Mobile detected: Barba & Lego disabled for performance.");
    });
} else {

// --- BARBA CONFIG (DESKTOP ONLY) ---
barba.init({
  debug: false, 
  
  prevent: ({ el }) => {
    return el.classList.contains('no-barba') || el.closest('#wpadminbar'); 
  },

  views: [{
    namespace: 'home'
  }],

  transitions: [
    {
      name: "grid-transition",
      
      once(data) {
        // 1. INIT TECHNIQUE
        ensureGridOverlay();
        updateActiveMenu(window.location.href);
          // On ne lance PAS le Lego tout de suite pour laisser la priorité à l'affichage du site
          
          // Init fitty AVANT les animations pour avoir les bonnes dimensions
          initHeroTextResize();
          initTextReveal();
          initGallery();

          // 2. TIMELINE D'INTRO
          const tl = gsap.timeline({
              onComplete: () => {
                  const loader = document.querySelector('.site-loader');
                  if (loader) loader.remove();

                  // C'est le moment parfait pour charger la 3D (lazy load)
                  // Le site est affiché, le navigateur respire.
                  setTimeout(() => {
                      if ('requestIdleCallback' in window) {
                          requestIdleCallback(() => initLegoSceneIfNeeded(), { timeout: 2000 });
                      } else {
                          initLegoSceneIfNeeded();
                      }
                  }, 200);
              }
          });

          // Etape A : Le Rideau s'efface
          tl.to('.site-loader', {
              duration: 1,
              autoAlpha: 0, 
              ease: "power2.inOut"
          });

        // Etape B : Le Header apparait (pendant que le rideau s'ouvre)
        tl.from('header', {
            y: -20,
            autoAlpha: 0,
            duration: 1,
            ease: "power3.out"
        }, "-=0.8"); 

        // Etape C : Animation Contextuelle (Home vs Autres)
        if (data.next.namespace === 'home') {
            const titleElement = data.next.container.querySelector('#name-element');
            // Petite sécurité : on prend le parent pour éviter les conflits de transform avec Fitty s'il y en a
            const titleTarget = titleElement ? titleElement.parentNode : titleElement; 
            
            const metas = data.next.container.querySelectorAll('#typewriter, .grid p');

            // 1. Le Nom (Gros Titre)
            if (titleTarget) {
                tl.from(titleTarget, {
                    y: 60,
                    autoAlpha: 0,
                    duration: 1.2,
                    ease: "power3.out"
                }, "-=0.8");
            }

            // 2. Les infos (Sous-titre et description)
            if (metas.length > 0) {
                tl.from(metas, {
                    y: 30,
                    autoAlpha: 0,
                    duration: 0.8,
                    stagger: 0.15,
                    ease: "power2.out"
                }, "-=1.0");
            }
        } else {
            // Fallback pour les autres pages : Slide Up global propre
            tl.from(data.next.container, {
                y: 40,
                autoAlpha: 0,
                duration: 0.8,
                ease: "power2.out"
            }, "-=0.6");
        }
      },
      
      // 1. LEAVE : On masque la page actuelle avec la grille
      async leave(data) {
        // Destruction Lego (point unique)
        destroyLegoScene();
        
        // Cleanup des modules
        cleanupTextReveal();
        cleanupGallery();
        
        ensureGridOverlay();
        const container = document.querySelector(TRANSITION_EL);
        gsap.set(container, { display: "grid" }); 

        await gsap.fromTo(ITEM_CLASS, 
          { autoAlpha: 0 },
          {
            autoAlpha: 1,
            stagger: { amount: 0.4, from: "random", grid: "auto" },
            duration: 0.4,
            ease: "power2.inOut"
          }
        );

        data.current.container.style.display = 'none';
      },

      // 2. BEFORE ENTER : Préparation technique
      beforeEnter(data) {
        updateActiveMenu(data.next.url.href);
        
        // Cleanup de l'ancienne page
        cleanupFitty();
        
        // Préparation générale du container suivant
        gsap.set(data.next.container, { 
            opacity: 0,
            pointerEvents: 'none'
        });

        // Pas de masquage des cartes, elles doivent apparaître avec la page
        // const nextCards = data.next.container.querySelectorAll('.project-card');
        // if (nextCards.length > 0) {
        //     gsap.set(nextCards, { opacity: 0 });
        // }
        
        // Forcer le reflow pour que le DOM soit prêt avec toutes les dimensions
        data.next.container.offsetHeight;
        
        // Petit délai pour laisser le navigateur calculer toutes les dimensions CSS
        requestAnimationFrame(() => {
            // Init fitty maintenant que le container a des dimensions stables
            initHeroTextResize();
        });
      },

      // 3. ENTER : Chargement et Reveal
      async enter(data) {
        window.scrollTo(0, 0);

        // Maintenant on remet le container en place
        gsap.set(data.next.container, { 
            clearProps: 'all'
        }); 

        // Reveal de la grille (Fade Out)
        const container = document.querySelector(TRANSITION_EL);
        
        await gsap.to(ITEM_CLASS, {
            autoAlpha: 0,
            stagger: { amount: 0.25, from: "random", grid: "auto" },
            duration: 0.35,
            ease: "power3.out",
            onComplete: () => {
                gsap.set(container, { display: "none" });
            }
        });
      },

      // 4. AFTER : Re-initiations scripts non-critiques
      after(data) {
        initTextReveal();
        initGallery();
        initTypewriter();
        new CurrentProjectFilters();
        
        if (window.initMenuToggle) window.initMenuToggle();
        
        // Lazy load Lego APRÈS animation
        const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        const isSlowConnection = connection && (connection.saveData || ['slow-2g', '2g', '3g'].includes(connection.effectiveType));

        const delay = isSlowConnection ? 2500 : 100;

        setTimeout(() => {
          if ('requestIdleCallback' in window) {
            requestIdleCallback(() => initLegoSceneIfNeeded(data.next.container), { timeout: 3000 });
          } else {
            initLegoSceneIfNeeded(data.next.container);
          }
        }, delay);
      }
    }
  ]
});

} // End else Desktop