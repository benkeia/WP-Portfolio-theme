// About page reveal animations

let aboutRevealInitialized = false;

export function initAboutReveal() {
  if (aboutRevealInitialized) return;
  
  const aboutContainer = document.querySelector('#about-reveal');
  if (!aboutContainer) return;
  
  // Ajoute ici tes animations pour la page About si nécessaire
  // Exemple: GSAP ScrollTrigger, Intersection Observer, etc.
  
  aboutRevealInitialized = true;
}

export function cleanupAboutReveal() {
  if (!aboutRevealInitialized) return;
  
  // Cleanup des animations/listeners si nécessaire
  
  aboutRevealInitialized = false;
}
