export function updateHeaderFitty() {
    const headerLogo = document.querySelector('header .flex-shrink-0 a');
    if (!headerLogo) return;
    fitty(headerLogo, {
        minSize: 14,
        maxSize: 48,
    });
}
import fitty from 'fitty';

let fittyInstances = [];

export function cleanupFitty() {
    fittyInstances.forEach(instance => {
        if (instance && instance.unsubscribe) {
            instance.unsubscribe();
        }
    });
    fittyInstances = [];
}

export function initHeroTextResize() {
    // Cleanup des instances précédentes
    cleanupFitty();
    
    // Hero name element (page d'accueil)
    const nameElement = document.getElementById('name-element');
    if (nameElement) {
        const instance = fitty(nameElement, {
            minSize: 24,
            maxSize: 220,
            multiLine: false,
            observeMutations: false
        });
        fittyInstances.push(instance);
    }
    
    // Project title element (pages projet)
    const projectTitleElement = document.getElementById('project-title-element');
    if (projectTitleElement) {
        const instance = fitty(projectTitleElement, {
            minSize: 32,
            maxSize: 120,
            multiLine: false,
            observeMutations: false
        });
        fittyInstances.push(instance);
    }
}
