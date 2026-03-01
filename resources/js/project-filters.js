import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

/**
 * 🎯 Système de filtrage dynamique avec animations GSAP stagger premium
 * 
 * Architecture:
 * - Stagger fluide et élégant
 * - Animations 60fps avec hardware acceleration
 * - Effets premium : scale, rotation, fade
 * - Maintient le DOM propre en réorganisant réellement les éléments
 * 
 * @class ProjectFilters
 */
class ProjectFilters {
    constructor() {
        this.grid = document.getElementById('projects-grid');
        this.filterBtns = document.querySelectorAll('.filter-btn');
        this.projects = document.querySelectorAll('.project-card');
        this.activeFilters = {
            domaine: '*',
            technologie: '*'
        };
        this.currentSort = 'date';
        this.isAnimating = false;
        
        if (!this.grid || this.filterBtns.length === 0) return;
        
        this.init();
    }
    
    /**
     * 🎬 Initialisation du système
     */
    init() {
        // Sauvegarder l'ordre initial (celui de WordPress/Reorder plugin)
        this.projects.forEach((p, index) => {
            p.setAttribute('data-original-index', index);
        });

        this.filterBtns.forEach(btn => {
            btn.addEventListener('click', (e) => this.handleFilterClick(e));
        });
        
        // Pas d'animation initiale au chargement de page, seulement au filtrage
        // this.animateInitial();
    }
    
    /**
     * ✨ Animation d'entrée initiale des cards
     * Effet premium : Apparition progressive avec rotation et bounce
     */
    animateInitial() {
        const tl = gsap.timeline();
        
        tl.fromTo(this.projects, 
            {
                opacity: 0,
                y: 70,
                scale: 0.8,
                rotation: () => gsap.utils.random(-5, 5)
            },
            {
                opacity: 1,
                y: 0,
                scale: 1,
                rotation: 0,
                duration: 0.9,
                stagger: {
                    amount: 0.5,
                    from: "start",
                    ease: "power2.out"
                },
                ease: 'power3.out'
            }
        );
        
        // Micro bounce de finition
        tl.to(this.projects, {
            scale: 1.03,
            duration: 0.2,
            stagger: {
                amount: 0.25,
                from: "start"
            },
            ease: 'power1.out'
        }, '-=0.4');
        
        tl.to(this.projects, {
            scale: 1,
            duration: 0.3,
            stagger: {
                amount: 0.25,
                from: "start"
            },
            ease: 'power2.inOut',
            clearProps: 'all'
        });
    }
    
    /**
     * 🎯 Gestion du clic sur un bouton de filtre/tri
     * Détermine si c'est un filtre ou un tri et appelle la méthode appropriée
     */
    handleFilterClick(e) {
        if (this.isAnimating) return;
        
        const btn = e.currentTarget;
        const filterValue = btn.getAttribute('data-filter');
        const filterType = btn.getAttribute('data-type');
        const sortValue = btn.getAttribute('data-sort');
        
        if (sortValue) {
            this.handleSort(sortValue, btn);
        } else {
            this.handleFilter(filterValue, filterType, btn);
        }
    }
    
    /**
     * 🔍 Gestion des filtres (domaine, technologie)
     */
    handleFilter(filterValue, filterType, btn) {
        const sameTypeButtons = document.querySelectorAll(`[data-type="${filterType}"]`);
        sameTypeButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        this.activeFilters[filterType] = filterValue;
        this.applyFilters();
    }
    
    /**
     * 📊 Gestion du tri (chronologique, alphabétique)
     */
    handleSort(sortValue, btn) {
        const sortButtons = document.querySelectorAll('[data-sort]');
        sortButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        this.currentSort = sortValue;
        this.applyFilters();
    }
    
    /**
     * ⚙️ Application des filtres et du tri
     * Pipeline: Filtrage → Tri → Animation
     */
    applyFilters() {
        if (this.isAnimating) return;
        
        // 🔄 Re-capturer les projets à chaque fois (fix du bug)
        this.projects = document.querySelectorAll('.project-card');
        let projectsArray = Array.from(this.projects);
        
        // 🔍 Phase 1: Filtrage
        projectsArray = projectsArray.filter(project => this.shouldShowProject(project));
        
        // 📊 Phase 2: Tri
        projectsArray.sort((a, b) => {
            if (this.currentSort === 'date') {
                // Utiliser l'index original (ordre du plugin Reorder WordPress)
                const indexA = parseInt(a.getAttribute('data-original-index'));
                const indexB = parseInt(b.getAttribute('data-original-index'));
                return indexA - indexB; 
            } else if (this.currentSort === 'title') {
                const titleA = a.getAttribute('data-sort-title').toLowerCase();
                const titleB = b.getAttribute('data-sort-title').toLowerCase();
                return titleA.localeCompare(titleB); // A-Z
            }
            return 0;
        });
        
        // 🎬 Phase 3: Animation premium
        this.animateReorganize(projectsArray);
    }
    
    /**
     * ✅ Vérification si un projet passe les filtres actifs
     */
    shouldShowProject(project) {
        const classes = project.className;
        
        if (this.activeFilters.domaine !== '*' && !classes.includes(this.activeFilters.domaine)) {
            return false;
        }
        
        if (this.activeFilters.technologie !== '*' && !classes.includes(this.activeFilters.technologie)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 🎨 Animation de réorganisation premium avec stagger élégant
     * 
     * Animations créatives :
     * - Disparition : fade out + scale + rotation + mouvement vers le haut
     * - Apparition : fade in + scale + rotation inverse + mouvement depuis le bas
     * - Stagger fluide pour un effet cascade premium
     * 
     * @param {Array} visibleProjects - Liste des projets à afficher
     */
    animateReorganize(visibleProjects) {
        if (this.isAnimating) return;
        this.isAnimating = true;
        
        // 📦 Séparer projets visibles et cachés
        // Note: Lors d'un simple tri, hiddenProjects peut être vide, c'est normal
        const hiddenProjects = Array.from(this.projects).filter(p => !visibleProjects.includes(p));
        
        // Si on change l'ordre mais que ce sont les mêmes éléments, on veut quand même animer leur "sortie"
        // Pour ça, on va animer *tous* les éléments visibles actuels vers la sortie avant de les réorganiser
        const currentVisible = Array.from(this.projects).filter(p => p.style.display !== 'none');
        
        // 🎬 Timeline pour orchestrer les animations
        const tl = gsap.timeline({
            onComplete: () => {
                this.isAnimating = false;
                // Clean up final
                gsap.set(visibleProjects, { clearProps: 'all' });
            }
        });
        
        // 🚫 ÉTAPE 1: Animation de DISPARITION (tout le monde part, mais plus vite !)
        // On fait disparaître tout ce qui est actuellement visible pour faire place nette
        if (currentVisible.length > 0) {
            tl.to(currentVisible, {
                opacity: 0,
                scale: 0.7,
                y: -30,
                rotation: () => gsap.utils.random(-8, 8),
                duration: 0.4, // Accéléré de 0.5 à 0.4
                stagger: {
                    amount: 0.15, // Accéléré de 0.2 à 0.15
                    from: "random",
                    ease: "power1.in"
                },
                ease: 'power2.in',
                onComplete: () => {
                    // On cache tout temporairement pour éviter les glitches
                    currentVisible.forEach(p => p.style.display = 'none');
                }
            });
        }
        
        // 🔄 ÉTAPE 2: Réorganiser le DOM
        tl.call(() => {
            visibleProjects.forEach(p => {
                p.style.display = ''; // On ré-affiche ceux qu'on veut garder
                this.grid.appendChild(p);
            });
            
            // Forcer un recalcul de layout
            ScrollTrigger && ScrollTrigger.refresh();
        });
        
        // ✨ ÉTAPE 3: Animation d'APPARITION premium (VERSION TURBO 🏎️)
        tl.fromTo(visibleProjects,
            {
                opacity: 0,
                scale: 0.75,
                y: 50,
                rotation: () => gsap.utils.random(-6, 6)
            },
            {
                opacity: 1,
                scale: 1,
                y: 0,
                rotation: 0,
                duration: 0.5, // Accéléré de 0.8 à 0.5
                stagger: {
                    amount: 0.2, // Accéléré de 0.35 à 0.2 (plus dynamique !)
                    from: "start",
                    ease: "power2.out"
                },
                ease: 'back.out(1.2)' // Un petit overhoot plus sec pour le dynamic
            },
            '+=0.05' // Pause réduite pour enchaîner plus vite
        );
        
        // 🌟 ÉTAPE 4: Micro bounce final (plus vif)
        tl.to(visibleProjects, {
            scale: 1.02,
            duration: 0.15, // Accéléré de 0.25 à 0.15
            stagger: {
                amount: 0.1, // Accéléré de 0.15 à 0.1
                from: "start"
            },
            ease: 'power1.out'
        }, '-=0.2');
        
        tl.to(visibleProjects, {
            scale: 1,
            duration: 0.2, // Accéléré de 0.3 à 0.2
            stagger: {
                amount: 0.1, // Accéléré de 0.15 à 0.1
                from: "start"
            },
            ease: 'power2.inOut'
        });
    }
}

// Ne pas auto-initialiser ici si utilisé avec Barba.js
// On laisse l'importateur gérer l'instance
// if (document.readyState === 'loading') {
//     document.addEventListener('DOMContentLoaded', () => {
//         new ProjectFilters();
//     });
// } else {
//     new ProjectFilters();
// }

export default ProjectFilters;
