<?php

/**
 * Template Name: About Page
 */

require_once get_template_directory() . '/inc/design-system.php';

$barba_namespace = 'about';
get_header();
?>

<?php get_template_part('template-parts/breadcrumb'); ?>

<div class="min-h-screen max-w-screen bg-neutral-900 flex flex-col justify-start items-center overflow-x-hidden">
    <div class="w-full max-w-screen bg-neutral-900">

        <!-- 1. HERO MANIFESTE -->
        <section class="w-full pt-12 pb-20 px-4 md:px-24">
            <div class="w-full max-w-[1200px] mx-auto">
                <div class="flex flex-col gap-6">
                    <!-- Surtitre -->
                    <div class="text-neutral-400 text-xs font-normal uppercase tracking-wider">
                        <?php echo get_field('hero_subtitle') ?: 'Creative Developer'; ?>
                    </div>

                    <!-- Titre principal -->
                    <h1 class="text-neutral-50 text-4xl md:text-6xl lg:text-7xl font-bold leading-tight tracking-tight">
                        <?php echo get_field('hero_title') ?: 'Je conçois des expériences web immersives, narratives et techniques.'; ?>
                    </h1>

                    <!-- Manifeste -->
                    <p class="text-neutral-400 text-xl md:text-2xl font-light leading-relaxed max-w-3xl">
                        <?php echo get_field('hero_manifesto') ?: 'Entre code, motion et identité visuelle, je crée des interfaces qui racontent des histoires.'; ?>
                    </p>
                </div>
            </div>
        </section>

        <!-- BUILDER DE PAGE (sections dynamiques) -->
        <?php if (have_rows('page_builder')): ?>
            <?php while (have_rows('page_builder')): the_row(); ?>

                <!-- Séparateur -->
                <div class="w-full px-4 md:px-24">
                    <div class="w-full max-w-[1200px] mx-auto border-b border-neutral-800"></div>
                </div>

                <?php if (get_row_layout() === 'bloc_manifeste'): ?>
                    <?php DS::block_about_hero(); ?>

                <?php elseif (get_row_layout() === 'bloc_identite'): ?>
                    <?php DS::block_about_identity(); ?>

                <?php elseif (get_row_layout() === 'bloc_competences'): ?>
                    <?php DS::block_about_skills(); ?>

                <?php elseif (get_row_layout() === 'bloc_vision'): ?>
                    <?php DS::block_about_vision(); ?>

                <?php elseif (get_row_layout() === 'bloc_experiences'): ?>
                    <section class="w-full py-20 px-4 md:px-24">
                        <div class="w-full max-w-[1200px] mx-auto">
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                                <div class="lg:col-span-3">
                                    <h2 class="text-neutral-50 text-2xl font-medium sticky top-24">
                                        <?php echo esc_html(get_sub_field('section_title') ?: 'Expériences'); ?>
                                    </h2>
                                </div>
                                <div class="lg:col-span-9 flex flex-col gap-8">
                                    <?php get_template_part('template-parts/experiences'); ?>
                                </div>
                            </div>
                        </div>
                    </section>

                <?php elseif (get_row_layout() === 'bloc_texte'): ?>
                    <?php DS::block_text(); ?>

                <?php elseif (get_row_layout() === 'bloc_galerie'): ?>
                    <?php DS::block_gallery(); ?>

                <?php elseif (get_row_layout() === 'video'): ?>
                    <?php DS::block_video(); ?>

                <?php endif; ?>

            <?php endwhile; ?>
        <?php endif; ?>

        <!-- Séparateur -->
        <div class="w-full px-4 md:px-24">
            <div class="w-full max-w-[1200px] mx-auto border-b border-neutral-800"></div>
        </div>


        <!-- 6. CONTACT & CTA -->
        <section class="w-full py-20 px-4 md:px-24">
            <div class="w-full max-w-[1200px] mx-auto">
                <div class="flex flex-col items-center text-center gap-8 py-12">
                    <h2 class="text-neutral-50 text-3xl md:text-4xl font-medium">
                        <?php echo get_field('cta_title') ?: 'Travaillons ensemble'; ?>
                    </h2>
                    <p class="text-neutral-400 text-lg font-light max-w-2xl">
                        <?php echo get_field('cta_description') ?: 'Disponible pour une alternance en développement web créatif à partir d\'octobre 2025. Je suis ouvert aux projets ambitieux, aux collaborations et aux défis techniques.'; ?>
                    </p>

                    <!-- Boutons CTA -->
                    <?php
                    $cta_primary_text = get_field('cta_primary_button_text');
                    $cta_primary_link = get_field('cta_primary_button_link');
                    $cta_secondary_text = get_field('cta_secondary_button_text');
                    $cta_secondary_link = get_field('cta_secondary_button_link');
                    $has_cta_buttons = ($cta_primary_text && $cta_primary_link) || ($cta_secondary_text && $cta_secondary_link);
                    ?>

                    <?php if ($has_cta_buttons): ?>
                        <div class="flex flex-wrap items-center justify-center gap-4 mt-4">
                            <?php if ($cta_primary_text && $cta_primary_link): ?>
                                <a href="<?php echo esc_url($cta_primary_link); ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group inline-flex items-center gap-2 px-6 py-3 bg-neutral-50 text-neutral-900 rounded-lg font-medium hover:bg-white transition-colors duration-200 no-underline">
                                    <?php echo esc_html($cta_primary_text); ?>
                                </a>
                            <?php endif; ?>

                            <?php if ($cta_secondary_text && $cta_secondary_link): ?>
                                <a href="<?php echo esc_url($cta_secondary_link); ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 px-6 py-3 border border-neutral-700 text-neutral-50 rounded-lg font-medium hover:border-neutral-500 hover:text-white transition-colors duration-200 no-underline">
                                    <?php echo esc_html($cta_secondary_text); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- Fallback : bouton email depuis les options -->
                        <div class="flex flex-wrap items-center justify-center gap-4 mt-4">
                            <?php if ($email = get_field('contact_email', 'option') ?: 'contact@exemple.com'): ?>
                                <a href="mailto:<?php echo esc_attr($email); ?>"
                                    class="group inline-flex items-center gap-2 px-6 py-3 bg-neutral-50 text-neutral-900 rounded-lg font-medium hover:bg-white transition-colors duration-200 no-underline">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                    </svg>
                                    <span>Me contacter</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Liens sociaux supplémentaires -->
                    <?php if (have_rows('footer_socials', 'option')): ?>
                        <div class="flex flex-row items-center gap-6 mt-4">
                            <?php while (have_rows('footer_socials', 'option')): the_row();
                                $name = get_sub_field('network_name');
                                $url = get_sub_field('network_url');
                                $icon = get_sub_field('network_icon_svg');
                            ?>
                                <a href="<?php echo esc_url($url); ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="<?php echo esc_attr($name); ?>"
                                    title="<?php echo esc_attr($name); ?>"
                                    class="text-neutral-400 hover:text-white transition-colors duration-200 block w-6 h-6">
                                    <?php echo $icon; ?>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

    </div>
</div>

<?php get_footer(); ?>