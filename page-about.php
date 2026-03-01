<?php
/**
 * Template Name: About Page
 */

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

        <!-- Séparateur -->
        <div class="w-full px-4 md:px-24">
            <div class="w-full max-w-[1200px] mx-auto border-b border-neutral-800"></div>
        </div>

        <!-- 2. QUI JE SUIS -->
        <section class="w-full py-20 px-4 md:px-24">
            <div class="w-full max-w-[1200px] mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                    <!-- Titre de section -->
                    <div class="lg:col-span-3">
                        <h2 class="text-neutral-50 text-2xl font-medium sticky top-24">Qui je suis</h2>
                    </div>
                    
                    <!-- Contenu -->
                    <div class="lg:col-span-9 flex flex-col gap-8">
                        <!-- Origine -->
                        <div class="flex flex-col gap-4">
                            <h3 class="text-neutral-300 text-lg font-medium">Origine</h3>
                            <div class="text-neutral-400 text-base font-light leading-relaxed space-y-4">
                                <?php echo wpautop(get_field('about_origin') ?: 'Diplômé d\'un BUT MMI (Métiers du Multimédia et de l\'Internet), j\'ai développé une approche hybride entre design et développement. Ma curiosité m\'a poussé à explorer la 3D, le motion design et le code créatif pour créer des expériences uniques.'); ?>
                            </div>
                        </div>
                        
                        <!-- Évolution -->
                        <div class="flex flex-col gap-4">
                            <h3 class="text-neutral-300 text-lg font-medium">Évolution</h3>
                            <div class="text-neutral-400 text-base font-light leading-relaxed space-y-4">
                                <?php echo wpautop(get_field('about_evolution') ?: 'De la conception de jeux sous Unity à la création d\'identités visuelles immersives, chaque projet m\'a permis d\'affiner ma vision : créer des expériences qui marquent. WordPress custom, animations GSAP, modélisation 3D... J\'ai construit mon expertise sur des projets concrets et ambitieux.'); ?>
                            </div>
                        </div>
                        
                        <!-- Aujourd'hui -->
                        <div class="flex flex-col gap-4">
                            <h3 class="text-neutral-300 text-lg font-medium">Aujourd\'hui</h3>
                            <div class="text-neutral-400 text-base font-light leading-relaxed space-y-4">
                                <?php echo wpautop(get_field('about_today') ?: 'Je recherche une alternance en développement web créatif où je pourrai mettre mon sens du détail et ma passion pour les interfaces narratives au service de projets innovants. Studio, agence ou entreprise tech : l\'important est de créer des expériences mémorables.'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Séparateur -->
        <div class="w-full px-4 md:px-24">
            <div class="w-full max-w-[1200px] mx-auto border-b border-neutral-800"></div>
        </div>

        <!-- 3. COMPÉTENCES -->
        <section class="w-full py-20 px-4 md:px-24">
            <div class="w-full max-w-[1200px] mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                    <!-- Titre de section -->
                    <div class="lg:col-span-3">
                        <h2 class="text-neutral-50 text-2xl font-medium sticky top-24">Compétences</h2>
                    </div>
                    
                    <!-- Contenu -->
                    <div class="lg:col-span-9 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Développement -->
                        <div class="flex flex-col gap-4 p-6 rounded-lg bg-neutral-800/30 border border-neutral-800">
                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-neutral-400">
                                    <polyline points="16 18 22 12 16 6"></polyline>
                                    <polyline points="8 6 2 12 8 18"></polyline>
                                </svg>
                                <h3 class="text-neutral-50 text-lg font-medium">Développement</h3>
                            </div>
                            <ul class="text-neutral-400 text-sm font-light space-y-2 list-disc list-inside">
                                <li>HTML / CSS / JavaScript</li>
                                <li>GSAP & animations avancées</li>
                                <li>Three.js & WebGL</li>
                                <li>WordPress (custom themes & plugins)</li>
                                <li>PHP</li>
                            </ul>
                            <p class="text-neutral-500 text-xs italic">
                                Je privilégie un code maintenable et structuré, orienté performance et animation.
                            </p>
                        </div>

                        <!-- Design & Direction Artistique -->
                        <div class="flex flex-col gap-4 p-6 rounded-lg bg-neutral-800/30 border border-neutral-800">
                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-neutral-400">
                                    <path d="m21.64 3.64-1.28-1.28a1.21 1.21 0 0 0-1.72 0L2.36 18.64a1.21 1.21 0 0 0 0 1.72l1.28 1.28a1.2 1.2 0 0 0 1.72 0L21.64 5.36a1.2 1.2 0 0 0 0-1.72Z"></path>
                                    <path d="m14 7 3 3"></path>
                                    <path d="M5 6v4"></path>
                                    <path d="M19 14v4"></path>
                                    <path d="M10 2v2"></path>
                                    <path d="M7 8H3"></path>
                                    <path d="M21 16h-4"></path>
                                    <path d="M11 3H9"></path>
                                </svg>
                                <h3 class="text-neutral-50 text-lg font-medium">Design & Direction Artistique</h3>
                            </div>
                            <ul class="text-neutral-400 text-sm font-light space-y-2 list-disc list-inside">
                                <li>Figma (prototypage & design system)</li>
                                <li>Branding & identité visuelle</li>
                                <li>UI immersive</li>
                                <li>Motion design</li>
                            </ul>
                            <p class="text-neutral-500 text-xs italic">
                                Je conçois des interfaces qui allient esthétique et expérience utilisateur fluide.
                            </p>
                        </div>

                        <!-- 3D & Motion -->
                        <div class="flex flex-col gap-4 p-6 rounded-lg bg-neutral-800/30 border border-neutral-800 md:col-span-2">
                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-neutral-400">
                                    <path d="M12 2v20"></path>
                                    <path d="M2 12h20"></path>
                                    <path d="m5 19 14-14"></path>
                                </svg>
                                <h3 class="text-neutral-50 text-lg font-medium">3D & Motion</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <ul class="text-neutral-400 text-sm font-light space-y-2 list-disc list-inside">
                                    <li>Blender (modélisation & texturing)</li>
                                    <li>After Effects</li>
                                    <li>Unity (game dev & prototypage)</li>
                                </ul>
                                <p class="text-neutral-500 text-xs italic">
                                    J'intègre la 3D et le motion pour créer des narrations visuelles percutantes.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Séparateur -->
        <div class="w-full px-4 md:px-24">
            <div class="w-full max-w-[1200px] mx-auto border-b border-neutral-800"></div>
        </div>

        <!-- 4. EXPÉRIENCES -->
        <section class="w-full py-20 px-4 md:px-24">
            <div class="w-full max-w-[1200px] mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                    <!-- Titre de section -->
                    <div class="lg:col-span-3">
                        <h2 class="text-neutral-50 text-2xl font-medium sticky top-24">Expériences</h2>
                    </div>
                    
                    <!-- Contenu -->
                    <div class="lg:col-span-9 flex flex-col gap-8">
                        <?php get_template_part('template-parts/experiences'); ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Séparateur -->
        <div class="w-full px-4 md:px-24">
            <div class="w-full max-w-[1200px] mx-auto border-b border-neutral-800"></div>
        </div>

        <!-- 5. VISION & AMBITION -->
        <section class="w-full py-20 px-4 md:px-24">
            <div class="w-full max-w-[1200px] mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                    <!-- Titre de section -->
                    <div class="lg:col-span-3">
                        <h2 class="text-neutral-50 text-2xl font-medium sticky top-24">Vision</h2>
                    </div>
                    
                    <!-- Contenu -->
                    <div class="lg:col-span-9">
                        <div class="text-neutral-400 text-lg md:text-xl font-light leading-relaxed space-y-6">
                            <?php echo wpautop(get_field('vision_text') ?: 'Je crois au pouvoir des expériences immersives qui allient code performant et narration visuelle forte. Mon objectif est de créer des interfaces qui ne se contentent pas d\'être fonctionnelles, mais qui racontent une histoire, portent une identité et marquent les esprits.<br><br>Dans un monde digital saturé, je pense que c\'est l\'attention aux détails, la maîtrise technique et l\'audace créative qui font la différence. Chaque projet est une opportunité de repousser les limites et d\'apprendre.'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

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
                    
                    <!-- Boutons sociaux -->
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
                        
                        <?php if ($cv_file = get_field('cv_file', 'option')): ?>
                            <a href="<?php echo esc_url($cv_file['url']); ?>" 
                               target="_blank"
                               class="inline-flex items-center gap-2 px-6 py-3 border border-neutral-700 text-neutral-50 rounded-lg font-medium hover:border-neutral-500 hover:text-white transition-colors duration-200 no-underline">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" x2="12" y1="15" y2="3"></line>
                                </svg>
                                <span>Télécharger mon CV</span>
                            </a>
                        <?php endif; ?>
                    </div>
                    
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