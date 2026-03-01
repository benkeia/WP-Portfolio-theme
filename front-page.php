<?php
$barba_namespace = 'home';
get_header();
?>

<div class="min-h-screen max-w-screen bg-neutral-900 flex flex-col justify-start items-center overflow-x-hidden">
    <div class="w-full max-w-screen bg-neutral-900">

        <?php get_template_part('template-parts/hero'); ?>

        <div class="w-full mx-auto flex justify-center items-center mt-8 px-4 md:px-24">
            <div class="w-full mx-auto py-14 flex flex-col justify-center items-center">
                <div class="w-full mx-auto flex flex-col justify-center items-center gap-6">
                    <div class="w-full flex flex-row items-center justify-between gap-2 mb-2">
                        <div class="text-neutral-50 text-4xl font-medium leading-[48px]">
                            Mes derniers projets
                        </div>
                        <a href="<?php echo esc_url(home_url('/projet/')); ?>"
                            class="group flex flex-row items-center gap-1 cursor-pointer select-none">
                            <span
                                class="text-neutral-400 text-xs font-normal uppercase leading-5 group-hover:text-white transition-colors no-underline duration-200">Voir
                                tout</span>
                            <span class="inline-flex items-center justify-center">
                                <svg id="voir-tout-arrow" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="#a3a3a3" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-arrow-up-right-icon lucide-arrow-up-right group-hover:stroke-white transition-colors duration-200">
                                    <path d="M7 7h10v10" />
                                    <path d="M7 17 17 7" />
                                </svg>
                            </span>
                        </a>
                    </div>
                    <div
                        class="self-stretch rounded-lg flex flex-col justify-center items-center gap-6 overflow-visible">
                        <?php get_template_part('template-parts/projects'); ?>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

<!-- Scène LEGO en pleine largeur avec Easter Egg (Masquée sur mobile) -->
<div id="lego-canvas-container" class="relative w-full bg-neutral-900 hidden md:block md:h-[600px]"></div>

<?php get_footer(); ?>