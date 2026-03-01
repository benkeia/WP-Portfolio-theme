<?php
$barba_namespace = 'not-found';
get_header();
?>

<?php get_template_part('template-parts/breadcrumb'); ?>

<div class="bg-neutral-900 flex items-center justify-center min-h-[calc(100vh-300px)] px-6 md:px-12 lg:px-16 xl:px-24">
    <div class="flex flex-col lg:flex-row items-center justify-center gap-8 lg:gap-12 xl:gap-16 py-8">

        <!-- Composant Clavier (à gauche, un peu plus grand) -->
        <div class="keypad flex-shrink-0 w-[340px] sm:w-[380px] lg:w-[420px]">
            <div class="keypad__base">
                <img src="<?php echo get_template_directory_uri(); ?>/resources/images/keypad/keypad-base.png" alt="" />
            </div>
            <button id="one" class="key keypad__single keypad__single--left">
                <span class="key__mask">
                    <span class="key__content">
                        <span class="key__text">Did</span>
                        <img src="<?php echo get_template_directory_uri(); ?>/resources/images/keypad/keypad-single.png" alt="" />
                    </span>
                </span>
            </button>
            <button id="two" class="key keypad__single">
                <span class="key__mask">
                    <span class="key__content">
                        <span class="key__text">you</span>
                        <img src="<?php echo get_template_directory_uri(); ?>/resources/images/keypad/keypad-single.png" alt="" />
                    </span>
                </span>
            </button>
            <button id="three" class="key keypad__double">
                <span class="key__mask">
                    <span class="key__content">
                        <span class="key__text">get lost ?</span>
                        <img src="<?php echo get_template_directory_uri(); ?>/resources/images/keypad/keypad-double.png" alt="" />
                    </span>
                </span>
            </button>
        </div>

        <!-- Contenu 404 (à droite, beaucoup plus grand) -->
        <div class="max-w-md lg:max-w-xl xl:max-w-2xl flex-shrink-0 text-left">

            <!-- Code 404 beaucoup plus grand avec Wikolia Pixel -->
            <div class="mb-6 overflow-hidden">
                <h1
                    class="text-[90px] sm:text-[110px] md:text-[130px] lg:text-[150px] xl:text-[170px] 2xl:text-[190px] text-neutral-50 font-['Wikolia_Pixel'] leading-none drop-shadow-2xl">
                    404
                </h1>
            </div>

            <!-- Message d'erreur -->
            <div class="mb-8 space-y-3">
                <h2 class="text-neutral-50 text-xl md:text-2xl lg:text-3xl font-medium">
                    <?php _e('Page introuvable', 'tailpress'); ?>
                </h2>
                <p class="text-neutral-400 text-sm md:text-base lg:text-lg font-light leading-relaxed max-w-xl">
                    <?php _e('Désolé, la page que vous recherchez semble avoir disparu dans le vide numérique. Elle n\'existe peut-être plus ou l\'URL est incorrecte.', 'tailpress'); ?>
                </p>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-3 items-start">
                <a href="<?php echo get_bloginfo('url'); ?>"
                    class="group inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-medium transition-all bg-primary text-white hover:bg-primary/90 hover:scale-105 !no-underline">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="transition-transform group-hover:-translate-x-1">
                        <path d="m12 19-7-7 7-7" />
                        <path d="M19 12H5" />
                    </svg>
                    <?php _e('Retour à l\'accueil', 'tailpress'); ?>
                </a>

                <button onclick="history.back()"
                    class="group inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-medium transition-all bg-neutral-800 text-neutral-50 hover:bg-neutral-700 hover:scale-105 border border-neutral-700 !no-underline">
                    <?php _e('Page précédente', 'tailpress'); ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="transition-transform group-hover:rotate-180">
                        <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" />
                        <path d="M21 3v5h-5" />
                        <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" />
                        <path d="M8 16H3v5" />
                    </svg>
                </button>
            </div>

        </div>

    </div>
</div>

<!-- Rendre le clavier visible immédiatement -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const keypad = document.querySelector('.keypad');
        if (keypad) {
            keypad.style.opacity = '1';
        }
    });
</script>

<!-- 404 Keypad Component -->
<script>
// Based on: https://codepen.io/jh3y/pen/vYwEYpv

const config = {
    theme: 'system',
    muted: false,
    exploded: false,
    one: {
        travel: 26,
        text: 'Did',
        key: 'D',
        hue: 0,
        saturation: 1.0,
        brightness: 1.1,
        buttonElement: null,
        textElement: null,
    },
    two: {
        travel: 26,
        text: 'you',
        key: 'y',
        hue: 0,
        saturation: 0.0,
        brightness: 1.7,
        buttonElement: null,
        textElement: null,
    },
    three: {
        travel: 18,
        text: 'get lost ?',
        key: 'Enter',
        hue: 0,
        saturation: 0.0,
        brightness: 0.4,
        buttonElement: null,
        textElement: null,
    },
}

// Audio pour les clicks
const clickAudio = new Audio(
    'https://cdn.freesound.org/previews/378/378085_6260145-lq.mp3'
)
clickAudio.muted = config.muted

// Fonction pour initialiser les éléments
const initializeElements = () => {
    config.one.buttonElement = document.querySelector('#one')
    config.one.textElement = document.querySelector('#one .key__text')

    config.two.buttonElement = document.querySelector('#two')
    config.two.textElement = document.querySelector('#two .key__text')

    config.three.buttonElement = document.querySelector('#three')
    config.three.textElement = document.querySelector('#three .key__text')
}

// Fonction pour appliquer les styles des boutons
const applyButtonStyles = () => {
    const ids = ['one', 'two', 'three']

    for (const id of ids) {
        if (config[id].buttonElement) {
            config[id].buttonElement.style.setProperty('--travel', config[id].travel)
            config[id].buttonElement.style.setProperty('--saturate', config[id].saturation)
            config[id].buttonElement.style.setProperty('--hue', config[id].hue)
            config[id].buttonElement.style.setProperty('--brightness', config[id].brightness)

            // Ajouter l'événement de clic
            config[id].buttonElement.addEventListener('pointerdown', () => {
                if (!config.muted) {
                    clickAudio.currentTime = 0
                    clickAudio.play()
                }
            })
        }
    }
}

// Gestion des touches du clavier
window.addEventListener('keydown', (event) => {
    const ids = ['one', 'two', 'three']
    for (const id of ids) {
        if (event.key === config[id].key && config[id].buttonElement) {
            config[id].buttonElement.dataset.pressed = true
            if (!config.muted) {
                clickAudio.currentTime = 0
                clickAudio.play()
            }
        }
    }
})

window.addEventListener('keyup', (event) => {
    const ids = ['one', 'two', 'three']
    for (const id of ids) {
        if (event.key === config[id].key && config[id].buttonElement) {
            config[id].buttonElement.dataset.pressed = false
        }
    }
})

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    initializeElements()
    applyButtonStyles()

    // Rendre le clavier visible
    const keypad = document.querySelector('.keypad')
    if (keypad) {
        keypad.style.setProperty('opacity', 1)
    }
})
</script>

<?php
get_footer();