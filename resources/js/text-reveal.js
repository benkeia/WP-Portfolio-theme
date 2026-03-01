import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import SplitType from 'split-type';

gsap.registerPlugin(ScrollTrigger);

export function initTextReveal() {
    // Cleanup des anciennes instances
    const oldSplits = document.querySelectorAll('.reveal-type .char');
    oldSplits.forEach(el => {
        if (el.parentNode) {
            const text = el.textContent;
            el.parentNode.replaceChild(document.createTextNode(text), el);
        }
    });
    
    // Cleanup des words wrappers (nouveau)
    const oldWords = document.querySelectorAll('.reveal-type .word');
    oldWords.forEach(el => {
        if (el.parentNode) {
            const text = el.textContent;
            el.parentNode.replaceChild(document.createTextNode(text), el);
        }
    });

    const splitTypes = document.querySelectorAll('.reveal-type');
    
    if (splitTypes.length === 0) return;

    splitTypes.forEach((char) => {
        const bg = char.dataset.bgColor || '#525252'; // neutral-600
        const fg = char.dataset.fgColor || '#ffffff'; // white

        // Ajout de 'words' pour éviter les coupures brutes
        const text = new SplitType(char, { types: 'words, chars' }); 

        gsap.fromTo(text.chars, 
            {
                color: bg,
            },
            {
                color: fg,
                duration: 0.3,
                stagger: 0.02,
                scrollTrigger: {
                    trigger: char,
                    start: 'top 80%',
                    end: 'top 20%',
                    scrub: true,
                    markers: false,
                }
        });
    });
}

export function cleanupTextReveal() {
    // Kill toutes les ScrollTrigger instances
    ScrollTrigger.getAll().forEach(trigger => trigger.kill());
    
    // Cleanup des splits (chars puis words)
    const splitChars = document.querySelectorAll('.reveal-type .char');
    splitChars.forEach(el => {
        if (el.parentNode) {
            const text = el.textContent;
            el.parentNode.replaceChild(document.createTextNode(text), el);
        }
    });

    const splitWords = document.querySelectorAll('.reveal-type .word');
    splitWords.forEach(el => {
        if (el.parentNode) {
            const text = el.textContent;
            el.parentNode.replaceChild(document.createTextNode(text), el);
        }
    });
}
