
import barba from "@barba/core";
import { gsap } from "gsap";
import { initTypewriter } from "./typewritter.js";
import { initHeroTextResize, cleanupFitty } from "./hero-text-resize.js";
import { initTextReveal, cleanupTextReveal } from "./text-reveal.js";
import { initGallery, cleanupGallery } from "./gallery.js";
import { initAboutReveal, cleanupAboutReveal } from "./about-reveal.js";

// CONFIG
const TRANSITION_EL = ".tt-page-transition";
const ITEM_CLASS = ".tt-ptr-item";
const ITEM_COUNT = 72; 

// Variables
let legoSimulationInstance = null;
let legoLoading = false;

// --- HELPER: GENERATION DE GRID ---
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

// --- HELPER: MENU ---
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

// --- HELPER: Init Lego Scene ---
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

// --- MOBILE CHECK ---
if (window.innerWidth < 768) {
    document.addEventListener("DOMContentLoaded", () => {
        const loader = document.querySelector('.site-loader');
        if (loader) loader.style.display = 'none';

        initHeroTextResize();
        initTextReveal();
        initGallery();
        initTypewriter();
        
        const aboutReveal = document.querySelector('#about-reveal');
        if (aboutReveal) initAboutReveal();
        
        if (window.initMenuToggle) window.initMenuToggle();
    });
} else {

// --- BARBA CONFIG (DESKTOP ONLY) ---
barba.init({
  debug: true,
  
  prevent: ({ el }) => {
    return el.classList.contains('no-barba') || el.closest('#wpadminbar'); 
  },

  transitions: [{
    name: "grid-transition",
    
    once(data) {
      // Init modules
      initHeroTextResize();
      initTextReveal();
      initGallery();

      // Timeline d'intro
      const tl = gsap.timeline({
          onComplete: () => {
              const loader = document.querySelector('.site-loader');
              if (loader) loader.remove();

              // Lazy load Lego
              setTimeout(() => {
                  if ('requestIdleCallback' in window) {
                      requestIdleCallback(() => initLegoSceneIfNeeded(), { timeout: 2000 });
                  } else {
                      initLegoSceneIfNeeded();
                  }
              }, 300);
          }
      });

      // Animation loader
      tl.to('.site-loader', {
          duration: 0.8,
          autoAlpha: 0, 
          ease: "power2.inOut"
      });

      // Animation header
      tl.from('header', {
          y: -20,
          autoAlpha: 0,
          duration: 0.8,
          ease: "power3.out"
      }, "-=0.6"); 

      // Animation contenu
      if (data.next.namespace === 'home') {
          const titleElement = data.next.container.querySelector('#name-element');
          const titleTarget = titleElement?.parentNode || titleElement; 
          const metas = data.next.container.querySelectorAll('#typewriter, .grid p');

          if (titleTarget) {
              tl.fromTo(titleTarget, 
                  { y: 50, autoAlpha: 0 },
                  { y: 0, autoAlpha: 1, duration: 1, ease: "power3.out" }, 
                  "-=0.6"
              );
          }

          if (metas.length > 0) {
              tl.fromTo(metas, 
                  { y: 25, autoAlpha: 0 },
                  { y: 0, autoAlpha: 1, duration: 0.7, stagger: 0.12, ease: "power2.out" }, 
                  "-=0.8"
              );
          }
      } else {
          tl.fromTo(data.next.container, 
              { y: 30, autoAlpha: 0 },
              { y: 0, autoAlpha: 1, duration: 0.7, ease: "power2.out" }, 
              "-=0.5"
          );
      }
    },
    
    async leave(data) {
      // Cleanup
      destroyLegoScene();
      cleanupTextReveal();
      cleanupGallery();
      cleanupAboutReveal();
      cleanupFitty();
      
      // Grid overlay
      ensureGridOverlay();
      const container = document.querySelector(TRANSITION_EL);
      gsap.set(container, { display: "grid" }); 

      // Animate grid in
      await gsap.fromTo(ITEM_CLASS, 
        { autoAlpha: 0 },
        {
          autoAlpha: 1,
          stagger: { amount: 0.35, from: "random", grid: "auto" },
          duration: 0.35,
          ease: "power2.inOut"
        }
      );
    },

    beforeEnter(data) {
      updateActiveMenu(data.next.url.href);
      window.scrollTo(0, 0);
      
      // Préparer les modules (dimensions ready)
      initHeroTextResize();
    },

    async enter(data) {
      // S'assurer que le container est visible AVANT toute animation
      gsap.set(data.next.container, { 
          autoAlpha: 1,
          clearProps: "transform"
      });
      
      // Animate grid out + content in simultanément
      const container = document.querySelector(TRANSITION_EL);
      
      const tl = gsap.timeline();
      
      // Grid disparait
      tl.to(ITEM_CLASS, {
          autoAlpha: 0,
          stagger: { amount: 0.25, from: "random", grid: "auto" },
          duration: 0.3,
          ease: "power3.out",
          onComplete: () => gsap.set(container, { display: "none" })
      });
      
      // Contenu apparait (overlap) - fromTo pour être explicite
      tl.fromTo(data.next.container, 
          {
              autoAlpha: 0,
              y: 20
          },
          {
              autoAlpha: 1,
              y: 0,
              duration: 0.5,
              ease: "power2.out"
          }, 
          "-=0.2"
      );
      
      await tl;
    },

    after(data) {
      // Re-init modules
      initTextReveal();
      initGallery();
      initTypewriter();
      
      const aboutReveal = data.next.container.querySelector('#about-reveal');
      if (aboutReveal) initAboutReveal();
      
      if (window.initMenuToggle) window.initMenuToggle();
      
      // Lazy load Lego (non-blocking)
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

} // End Desktop