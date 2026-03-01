import barba from "@barba/core";
import { gsap } from "gsap";
import { initTypewriter } from "./typewritter.js";
import { initHeroTextResize, cleanupFitty } from "./hero-text-resize.js";
import { initTextReveal, cleanupTextReveal } from "./text-reveal.js";
import { initGallery, cleanupGallery } from "./gallery.js";
import { initAboutReveal, cleanupAboutReveal } from "./about-reveal.js";

// --- CONFIG ---
const TRANSITION_EL = ".tt-page-transition";
const ITEM_CLASS = ".tt-ptr-item";
const ITEM_COUNT = 72; 

// --- VARIABLES ---
let legoSimulationInstance = null;
let legoLoading = false;

// --- HELPERS ---
function ensureGridOverlay() {
  const container = document.querySelector(TRANSITION_EL);
  if (!container || container.childElementCount >= ITEM_COUNT) return;

  const frag = document.createDocumentFragment();
  for (let i = 0; i < ITEM_COUNT; i++) {
    const div = document.createElement("div");
    div.classList.add("tt-ptr-item");
    frag.appendChild(div);
  }
  container.appendChild(frag);
}

function updateActiveMenu(nextUrl) {
    const currentPath = new URL(nextUrl).pathname.replace(/\/$/, "");
    const navLinks = document.querySelectorAll('#primary-navigation .menu-item a');

    navLinks.forEach(link => {
        const linkPath = new URL(link.href).pathname.replace(/\/$/, "");
        const parentLi = link.closest('.menu-item');
        if (parentLi) {
            parentLi.classList.toggle('current-menu-item', linkPath === currentPath);
        }
    });
}

function initLegoSceneIfNeeded(scope = document) {
    const container = scope.querySelector('#lego-canvas-container');
    if (!container || legoSimulationInstance || legoLoading) return;
    
    legoLoading = true;
    import("./lego-scene.js")
        .then(({ LegoSimulation }) => {
            requestAnimationFrame(() => {
                legoSimulationInstance = new LegoSimulation(container);
                window.legoSimulation = legoSimulationInstance;
                legoLoading = false;
            });
        })
        .catch(err => {
            console.error('[Lego] Failed to load:', err);
            legoLoading = false;
        });
}

function destroyLegoScene() {
    if (legoSimulationInstance) {
        legoSimulationInstance.destroy();
        legoSimulationInstance = null;
        window.legoSimulation = null;
    }
}

// --- GUARD (Hostinger / LiteSpeed Cache) ---
if (!window.__barbaInitialized) {
    window.__barbaInitialized = true;
    
    ensureGridOverlay();
    initTypewriter();
    updateActiveMenu(window.location.href);

    // --- LOGIQUE MOBILE ---
    function initMobileFeatures() {
        const loader = document.querySelector('.site-loader');
        if (loader) loader.style.display = 'none';

        initHeroTextResize();
        initTextReveal();
        initGallery();
        initTypewriter();
        
        const aboutReveal = document.querySelector('#about-reveal');
        if (aboutReveal) initAboutReveal();
        
        if (window.initMenuToggle) window.initMenuToggle();
    }

    if (window.innerWidth < 768) {
        if (document.readyState === 'loading') {
            document.addEventListener("DOMContentLoaded", initMobileFeatures);
        } else {
            initMobileFeatures();
        }
    } else {

    // --- BARBA CONFIG (DESKTOP) ---
    barba.init({
      debug: true,
      timeout: 7000, // Défaut Barba = 2000ms. Augmenté pour WP/Hostinger qui peut être lent.
      
      prevent: ({ el }) => {
        return el.classList.contains('no-barba') || el.closest('#wpadminbar'); 
      },

      transitions: [{
        name: "grid-transition",
        
        // 1. INITIALISATION DE LA PREMIÈRE PAGE
        once(data) {
          initHeroTextResize();
          initTextReveal();
          initGallery();

          const tl = gsap.timeline({
              onComplete: () => {
                  const loader = document.querySelector('.site-loader');
                  if (loader) loader.remove();

                  setTimeout(() => {
                      if ('requestIdleCallback' in window) {
                          requestIdleCallback(() => initLegoSceneIfNeeded(), { timeout: 2000 });
                      } else {
                          initLegoSceneIfNeeded();
                      }
                  }, 300);
              }
          });

          tl.to('.site-loader', { duration: 0.8, autoAlpha: 0, ease: "power2.inOut" })
            .from('header', { y: -20, autoAlpha: 0, duration: 0.8, ease: "power3.out" }, "-=0.6"); 

          if (data.next.namespace === 'home') {
              const titleElement = data.next.container.querySelector('#name-element');
              const titleTarget = titleElement?.parentNode || titleElement; 
              const metas = data.next.container.querySelectorAll('#typewriter, .grid p');

              if (titleTarget) tl.fromTo(titleTarget, { y: 50, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 1, ease: "power3.out" }, "-=0.6");
              if (metas.length > 0) tl.fromTo(metas, { y: 25, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.7, stagger: 0.12, ease: "power2.out" }, "-=0.8");
          } else {
              tl.fromTo(data.next.container, { y: 30, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.7, ease: "power2.out" }, "-=0.5");
          }
        },
        
        // 2. AVANT DE QUITTER : Nettoyage propre
        // IMPORTANT : chaque cleanup EST dans un try...catch indépendant.
        // Si un module n'existe pas sur la page courante et lève une erreur,
        // Barba intercepte l'exception et ABANDONNE la transition → ancien container jamais retiré.
        beforeLeave(data) {
          try { destroyLegoScene();   } catch(e) { console.warn('[Barba] destroyLegoScene error:', e); }
          try { cleanupTextReveal();  } catch(e) { console.warn('[Barba] cleanupTextReveal error:', e); }
          try { cleanupGallery();     } catch(e) { console.warn('[Barba] cleanupGallery error:', e); }
          try { cleanupAboutReveal(); } catch(e) { console.warn('[Barba] cleanupAboutReveal error:', e); }
          try { cleanupFitty();       } catch(e) { console.warn('[Barba] cleanupFitty error:', e); }
        },

        // 3. LEAVE : On cache l'ancienne page avec l'overlay
        async leave(data) {
          console.log('🛑 1. LEAVE : La grille monte — fetch de la nouvelle page en cours...');
          // Cache immédiatement l'ancien container pour qu'il ne soit plus visible
          // quand la grille commencera à se retirer dans enter().
          gsap.set(data.current.container, { autoAlpha: 0 });
          
          ensureGridOverlay();
          const container = document.querySelector(TRANSITION_EL);
          gsap.set(container, { display: "grid" });
          gsap.set(ITEM_CLASS, { autoAlpha: 0 });

          // Le "return" est crucial : Barba attend la fin de cette animation pour swapper le DOM
          return gsap.to(ITEM_CLASS, {
            autoAlpha: 1,
            stagger: { amount: 0.3, from: "random", grid: [8, 9] },
            duration: 0.3,
            ease: "power2.inOut"
          });
        },

        // 4. BEFORE ENTER : Le DOM a été swappé en sous-marin, on prépare la nouvelle page
        beforeEnter(data) {
          console.log('✅ 2. DOM SWAPPÉ : La page 2 est dans le DOM, la page 1 n\'existe plus. Namespace :', data.next.namespace);
          updateActiveMenu(data.next.url.href);
          window.scrollTo(0, 0);
          initHeroTextResize(); 
          
          // On cache la nouvelle page pour qu'elle n'apparaisse pas d'un coup quand la grille partira
          gsap.set(data.next.container, { autoAlpha: 0, y: 20 });
        },

        // 5. ENTER : L'overlay s'en va, la nouvelle page apparaît
        async enter(data) {
          console.log('🟢 3. ENTER : La grille redescend — durée serveur OK.');
          const container = document.querySelector(TRANSITION_EL);
          const tl = gsap.timeline();

          // La grille disparaît
          tl.to(ITEM_CLASS, {
              autoAlpha: 0,
              stagger: { amount: 0.2, from: "random", grid: [8, 9] },
              duration: 0.25,
              ease: "power3.out",
              onComplete: () => gsap.set(container, { display: "none" })
          });

          // Le contenu apparaît en douceur
          tl.to(data.next.container, {
              autoAlpha: 1,
              y: 0,
              duration: 0.5,
              ease: "power2.out"
          }, "-=0.2");

          return tl;
        },

        // 6. AFTER : Tout est affiché, on relance les mécaniques
        after(data) {
          try { initTextReveal(); } catch(e) { console.warn('[Barba] initTextReveal error:', e); }
          try { initGallery();    } catch(e) { console.warn('[Barba] initGallery error:', e); }
          try { initTypewriter(); } catch(e) { console.warn('[Barba] initTypewriter error:', e); }
          
          try {
            const aboutReveal = data.next.container.querySelector('#about-reveal');
            if (aboutReveal) initAboutReveal();
          } catch(e) { console.warn('[Barba] initAboutReveal error:', e); }
          
          try { if (window.initMenuToggle) window.initMenuToggle(); } catch(e) { console.warn('[Barba] initMenuToggle error:', e); }
          
          const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
          const isSlowConnection = connection && (connection.saveData || ['slow-2g', '2g', '3g'].includes(connection.effectiveType));
          const delay = isSlowConnection ? 2000 : 100;

          setTimeout(() => {
            if ('requestIdleCallback' in window) {
              requestIdleCallback(() => initLegoSceneIfNeeded(data.next.container), { timeout: 3000 });
            } else {
              initLegoSceneIfNeeded(data.next.container);
            }
          }, delay);
        }
      }]
    });

    } // Fin Desktop
} // Fin Guard