
import barba from "@barba/core";
import gsap from "gsap";
import { initTypewriter } from "./typewritter.js";
import { initHeroTextResize, cleanupFitty } from "./hero-text-resize.js";
import { initTextReveal, cleanupTextReveal } from "./text-reveal.js";
import { initGallery, cleanupGallery } from "./gallery.js";
import { initAboutReveal, cleanupAboutReveal } from "./about-reveal.js";

// CONFIG
const TRANSITION_EL = ".tt-page-transition";
const ITEM_CLASS = ".tt-ptr-item";
const ITEM_COUNT = 72; 
const LOADER_TIMEOUT = 5000; // 5 secondes max pour le loader

// Variable pour stocker l'instance active
let legoSimulationInstance = null;
let legoLoading = false;
let barbaInitialized = false;

// SAFETY: Force loader removal after timeout
const safetyLoaderTimeout = setTimeout(() => {
  const loader = document.querySelector('.site-loader');
  if (loader) {
    console.warn('[SAFETY] Force removing loader after timeout');
    loader.style.display = 'none';
    loader.remove();
  }
}, LOADER_TIMEOUT);

// SAFETY: Ensure no element stays invisible
function ensureContentVisible(container = document.body) {
  try {
    // Reset tous les styles inline problématiques
    const elementsWithOpacity = container.querySelectorAll('[style*="opacity"]');
    elementsWithOpacity.forEach(el => {
      if (el.style.opacity === '0' || el.style.opacity === '') {
        el.style.opacity = '';
      }
    });
    
    const elementsWithVisibility = container.querySelectorAll('[style*="visibility"]');
    elementsWithVisibility.forEach(el => {
      if (el.style.visibility === 'hidden') {
        el.style.visibility = '';
      }
    });
  } catch (err) {
    console.error('[ensureContentVisible] Error:', err);
  }
}

// --- HELPER: GENERATION DE GRID ---
function ensureGridOverlay() {
  try {
    const container = document.querySelector(TRANSITION_EL);
    if (!container) return;
    
    if (container.childElementCount >= ITEM_COUNT) return;

    const frag = document.createDocumentFragment();
    
    for (let i = 0; i < ITEM_COUNT; i++) {
      const div = document.createElement("div");
      div.classList.add("tt-ptr-item");
      frag.appendChild(div);
    }
    container.appendChild(frag);
  } catch (err) {
    console.error('[ensureGridOverlay] Error:', err);
  }
}

// --- HELPER: MENU ---
function updateActiveMenu(nextUrl) {
    try {
        const currentPath = new URL(nextUrl).pathname.replace(/\/$/, "");
        const navLinks = document.querySelectorAll('#primary-navigation .menu-item a');

        navLinks.forEach(link => {
            try {
                const linkPath = new URL(link.href).pathname.replace(/\/$/, "");
                const parentLi = link.closest('.menu-item');
                
                if (parentLi) {
                    if (linkPath === currentPath) parentLi.classList.add('current-menu-item');
                    else parentLi.classList.remove('current-menu-item');
                }
            } catch (err) {
                console.error('[updateActiveMenu] Error processing link:', err);
            }
        });
    } catch (err) {
        console.error('[updateActiveMenu] Error:', err);
    }
}

// --- HELPER: Init Lego Scene ---
function initLegoSceneIfNeeded(scope = document) {
    try {
        const container = scope.querySelector('#lego-canvas-container');
        
        if (container && !legoSimulationInstance && !legoLoading) {
            legoLoading = true;
            
            import("./lego-scene.js").then(({ LegoSimulation }) => {
                try {
                    requestAnimationFrame(() => {
                        try {
                            legoSimulationInstance = new LegoSimulation(container);
                            window.legoSimulation = legoSimulationInstance;
                            console.log('[Lego] Scene loaded successfully');
                        } catch (err) {
                            console.error('[Lego] Error creating instance:', err);
                        } finally {
                            legoLoading = false;
                        }
                    });
                } catch (err) {
                    console.error('[Lego] Error in requestAnimationFrame:', err);
                    legoLoading = false;
                }
            }).catch(err => {
                console.error('[Lego] Error loading module:', err);
                legoLoading = false;
            });
        }
    } catch (err) {
        console.error('[initLegoSceneIfNeeded] Error:', err);
        legoLoading = false;
    }
}

// --- HELPER: Destroy Lego Scene ---
function destroyLegoScene() {
    try {
        if (legoSimulationInstance) {
            legoSimulationInstance.destroy();
            legoSimulationInstance = null;
            window.legoSimulation = null;
            console.log('[Lego] Scene destroyed');
        }
    } catch (err) {
        console.error('[destroyLegoScene] Error:', err);
        legoSimulationInstance = null;
        window.legoSimulation = null;
    }
}

// --- INITIALIZATION ---
try {
  ensureGridOverlay();
  initTypewriter();
  updateActiveMenu(window.location.href);
  console.log('[Init] Basic initialization completed');
} catch (err) {
  console.error('[Init] Error during initialization:', err);
}

// --- MOBILE CHECK ---
// Si écran < 768px, on désactive complètement Barba et Three.js
if (window.innerWidth < 768) {
    document.addEventListener("DOMContentLoaded", () => {
        try {
            // Suppression immédiate du loader
            clearTimeout(safetyLoaderTimeout);
            const loader = document.querySelector('.site-loader');
            if (loader) loader.style.display = 'none';

            // Init JS léger uniquement
            initHeroTextResize();
            initTextReveal();
            initGallery();
            initTypewriter();
            
            // Init About reveal si l'élément existe
            const aboutReveal = document.querySelector('#about-reveal');
            if (aboutReveal) {
              initAboutReveal();
            }
            
            if (window.initMenuToggle) window.initMenuToggle();
            
            ensureContentVisible();
            console.log("[Mobile] Barba disabled, basic scripts loaded");
        } catch (err) {
            console.error('[Mobile] Error during initialization:', err);
            ensureContentVisible();
        }
    });
} else {

// --- BARBA CONFIG (DESKTOP ONLY) ---
try {
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
        console.log('[Barba] once() started');
        
        try {
          // 1. INIT TECHNIQUE
          ensureGridOverlay();
          updateActiveMenu(window.location.href);
          
          // Init scripts AVANT animations
          initHeroTextResize();
          initTextReveal();
          initGallery();

          // 2. TIMELINE D'INTRO
          const tl = gsap.timeline({
              onComplete: () => {
                  console.log('[Barba] Timeline completed');
                  clearTimeout(safetyLoaderTimeout);
                  const loader = document.querySelector('.site-loader');
                  if (loader) loader.remove();

                  // Lazy load Lego après un court délai
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

          // Etape B : Le Header apparait
          tl.from('header', {
              y: -20,
              autoAlpha: 0,
              duration: 1,
              ease: "power3.out"
          }, "-=0.8"); 

          // Etape C : Animation Contextuelle
          if (data.next.namespace === 'home') {
              const titleElement = data.next.container.querySelector('#name-element');
              const titleTarget = titleElement ? titleElement.parentNode : titleElement; 
              
              const metas = data.next.container.querySelectorAll('#typewriter, .grid p');

              if (titleTarget) {
                  tl.from(titleTarget, {
                      y: 60,
                      autoAlpha: 0,
                      duration: 1.2,
                      ease: "power3.out"
                  }, "-=0.8");
              }

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
              tl.from(data.next.container, {
                  y: 40,
                  autoAlpha: 0,
                  duration: 0.8,
                  ease: "power2.out"
              }, "-=0.6");
          }
          
        } catch (err) {
          console.error('[Barba] Error in once():', err);
          // SAFETY: Forcer la visibilité si quelque chose plante
          clearTimeout(safetyLoaderTimeout);
          const loader = document.querySelector('.site-loader');
          if (loader) loader.style.display = 'none';
          ensureContentVisible();
        }
      },
      
      // 1. LEAVE : On masque la page actuelle avec la grille
      async leave(data) {
        console.log('[Barba] leave() started');
        
        try {
          // Destruction Lego
          destroyLegoScene();
          
          // Cleanup des modules
          cleanupTextReveal();
          cleanupGallery();
          cleanupAboutReveal();
          
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
        } catch (err) {
          console.error('[Barba] Error in leave():', err);
          if (data.current.container) data.current.container.style.display = 'none';
        }
      },

      // 2. BEFORE ENTER : Préparation technique
      beforeEnter(data) {
        console.log('[Barba] beforeEnter() started');
        
        try {
          updateActiveMenu(data.next.url.href);
          cleanupFitty();
          
          // Préparation DOM
          gsap.set(data.next.container, { 
              opacity: 0,
              pointerEvents: 'none'
          });
          
          // Forcer le reflow
          data.next.container.offsetHeight;
          
          // Init fitty
          initHeroTextResize();
        } catch (err) {
          console.error('[Barba] Error in beforeEnter():', err);
          // SAFETY: S'assurer que le contenu sera visible
          if (data.next.container) {
            gsap.set(data.next.container, { clearProps: 'all' });
          }
        }
      },

      // 3. ENTER : Chargement et Reveal
      async enter(data) {
        console.log('[Barba] enter() started');
        
        try {
          window.scrollTo(0, 0);

          // Remettre le container en place
          gsap.set(data.next.container, { 
              clearProps: 'all'
          }); 

          // Reveal de la grille
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
          
        } catch (err) {
          console.error('[Barba] Error in enter():', err);
          // SAFETY: S'assurer que le contenu est visible
          ensureContentVisible(data.next.container);
          const container = document.querySelector(TRANSITION_EL);
          if (container) gsap.set(container, { display: "none" });
        }
      },

      // 4. AFTER : Re-initiations scripts non-critiques
      after(data) {
        console.log('[Barba] after() started');
        
        try {
          initTextReveal();
          initGallery();
          initTypewriter();
          
          // Init About reveal si l'élément existe
          const aboutReveal = data.next.container.querySelector('#about-reveal');
          if (aboutReveal) initAboutReveal();
          
          if (window.initMenuToggle) window.initMenuToggle();
          
          // Lazy load Lego
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
          
          // SAFETY: Dernière vérification que tout est visible
          ensureContentVisible(data.next.container);
          
        } catch (err) {
          console.error('[Barba] Error in after():', err);
          ensureContentVisible(data.next.container);
        }
      }
    }
  ]
});

barbaInitialized = true;
console.log('[Barba] Initialized successfully');

} catch (err) {
  console.error('[Barba] CRITICAL - Failed to initialize:', err);
  // SAFETY FALLBACK: Désactiver complètement les transitions
  clearTimeout(safetyLoaderTimeout);
  const loader = document.querySelector('.site-loader');
  if (loader) loader.style.display = 'none';
  ensureContentVisible();
  
  // Réactiver les liens normaux
  document.body.classList.add('barba-disabled');
}

} // End else Desktop