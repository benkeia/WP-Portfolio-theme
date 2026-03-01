<?php
$barba_namespace = 'not-found';
get_header();
?>

<div class="min-h-screen bg-neutral-900 flex flex-col items-center justify-center text-center px-4 md:px-24 -mt-16">
    <div class="max-w-4xl w-full mx-auto">

        <!-- Code 404 en grand avec Wikolia Pixel -->
        <div class="mb-8 overflow-hidden">
            <h1 class="text-[120px] sm:text-[160px] md:text-[220px] lg:text-[280px] text-neutral-50 font-['Wikolia_Pixel'] leading-none drop-shadow-2xl">
                404
            </h1>
        </div>

        <!-- Ligne de séparation -->
        <div class="flex justify-center mb-8">
            <div class="w-24 h-1 bg-primary"></div>
        </div>

        <!-- Message d'erreur -->
        <div class="mb-12 space-y-4">
            <h2 class="text-neutral-50 text-2xl md:text-3xl lg:text-4xl font-medium">
                <?php _e('Page introuvable', 'tailpress'); ?>
            </h2>
            <p class="text-neutral-400 text-base md:text-lg font-light leading-relaxed max-w-2xl mx-auto">
                <?php _e('Désolé, la page que vous recherchez semble avoir disparu dans le vide numérique. Elle n\'existe peut-être plus ou l\'URL est incorrecte.', 'tailpress'); ?>
            </p>
        </div>

        <!-- Boutons d'action -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="<?php echo get_bloginfo('url'); ?>"
                class="group inline-flex items-center gap-2 rounded-lg px-6 py-3 text-base font-medium transition-all bg-primary text-white hover:bg-primary/90 hover:scale-105 !no-underline">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-hover:-translate-x-1">
                    <path d="m12 19-7-7 7-7" />
                    <path d="M19 12H5" />
                </svg>
                <?php _e('Retour à l\'accueil', 'tailpress'); ?>
            </a>

            <button onclick="history.back()"
                class="group inline-flex items-center gap-2 rounded-lg px-6 py-3 text-base font-medium transition-all bg-neutral-800 text-neutral-50 hover:bg-neutral-700 hover:scale-105 border border-neutral-700 !no-underline">
                <?php _e('Page précédente', 'tailpress'); ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-hover:rotate-180">
                    <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" />
                    <path d="M21 3v5h-5" />
                    <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" />
                    <path d="M8 16H3v5" />
                </svg>
            </button>
        </div>

        <!-- Effet décoratif -->
        <div class="mt-16 opacity-20">
            <div class="text-neutral-600 text-sm font-['Wikolia_Pixel'] tracking-wider">
                ERROR_404_NOT_FOUND
            </div>
        </div>

    </div>
</div>

<?php
get_footer();
