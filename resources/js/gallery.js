import lightGallery from 'lightgallery';
import lgThumbnail from 'lightgallery/plugins/thumbnail';
import lgZoom from 'lightgallery/plugins/zoom';

// Import CSS via JS allows Vite to handle font assets better
import 'lightgallery/css/lightgallery.css';
import 'lightgallery/css/lg-zoom.css';
import 'lightgallery/css/lg-thumbnail.css';

let galleryInstances = [];

export function initGallery() {
    // Cleanup old instances if any exist
    cleanupGallery();

    const galleries = document.querySelectorAll('.lightbox-gallery');
    
    galleries.forEach(el => {
        const instance = lightGallery(el, {
            plugins: [lgZoom, lgThumbnail],
            speed: 500,
            selector: 'a.lightbox-trigger', // Cible spécifique
            getCaptionFromTitleOrAlt: false,
            download: false,
            exThumbImage: 'data-external-thumb-image' // Utilise l'attribut ajouté en PHP
        });
        galleryInstances.push(instance);
    });
}

export function cleanupGallery() {
    galleryInstances.forEach(instance => {
        try {
            instance.destroy();
        } catch (e) {
            // Ignore errors during destruction
        }
    });
    galleryInstances = [];
}
