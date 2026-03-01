<?php
require_once get_template_directory() . '/inc/design-system.php';

get_header();
?>

<main data-barba="container" data-barba-namespace="project-<?php echo get_post_field('post_name', get_post()); ?>">

    <article class="bg-neutral-900 min-h-screen">

        <?php
        // Récupération des champs fixes du Hero
        $sous_titre = get_field('sous_titre');
        $role       = get_field('role');
        $stack      = get_field('stack');
        $annee      = get_field('annee');
        $img_id     = get_field('image_hero');
        ?>

        <section class="w-full pt-32 pb-12 px-4 md:px-24">
            <div class="w-full mx-auto">
                <div class="flex flex-col gap-3 mb-12 pb-8 border-b border-neutral-800">
                    <!-- Titre principal (Nom du projet WP) avec style Hero -->
                    <div class="overflow-hidden">
                        <div class="w-full flex justify-start">
                            <div class="w-full mx-auto flex justify-start">
                                <div class="relative w-full flex justify-start">
                                    <h1 id="project-title-element"
                                        class="block text-neutral-50 font-normal font-['Wikolia_Pixel'] leading-tight drop-shadow-lg whitespace-nowrap">
                                        <?php echo esc_html(get_the_title()); ?>
                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <?php if ($sous_titre): ?>
                            <p class="text-xl text-neutral-400 font-light"><?php echo esc_html($sous_titre); ?></p>
                        <?php endif; ?>

                        <div class="flex flex-wrap gap-6 shrink-0">
                            <?php if ($role) DS::badge('Rôle', $role); ?>
                            <?php if ($stack) DS::badge('Stack', $stack); ?>
                            <?php if ($annee) DS::badge('Date', $annee); ?>
                        </div>
                    </div>
                </div>

                <?php
                if ($img_id) {
                    DS::image($img_id, 'full', 'aspect-[21/9] rounded-lg shadow-2xl shadow-cyan-900/20', [
                        'loading' => 'eager',
                        'fetchpriority' => 'high',
                        'sizes' => '(max-width: 768px) 100vw, 90vw'
                    ]);
                }
                ?>
            </div>
        </section>


        <?php if (have_rows('page_builder')): ?>
            <?php while (have_rows('page_builder')) : the_row(); ?>

                <?php
                // --- BLOC : PITCH (Histoire + Chiffres) ---
                if (get_row_layout() == 'bloc_pitch'):
                    $titre_pitch = get_sub_field('titre');
                    $desc_pitch  = get_sub_field('description');
                    $chiffres    = get_sub_field('chiffres_cles');
                ?>
                    <section class="w-full py-20 px-4 md:px-24 bg-neutral-900">
                        <div class="w-full mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                            <div class="lg:col-span-7 space-y-8">
                                <?php if ($titre_pitch) DS::title($titre_pitch, 'h2'); ?>
                                <div class="prose-custom text-neutral-400 text-lg leading-relaxed">
                                    <?php echo $desc_pitch; ?>
                                </div>
                            </div>

                            <?php if ($chiffres): ?>
                                <div class="lg:col-span-5 grid grid-cols-2 gap-6">
                                    <?php foreach ($chiffres as $stat): ?>
                                        <div class="p-6 bg-neutral-900 rounded-lg border border-neutral-800">
                                            <span
                                                class="block text-4xl font-bold text-white mb-2"><?php echo esc_html($stat['chiffre']); ?></span>
                                            <span
                                                class="text-sm text-neutral-500 uppercase tracking-wider"><?php echo esc_html($stat['label']); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>

                <?php
                // --- BLOC : TECH (Cartes Expertise) ---
                elseif (get_row_layout() == 'bloc_tech'):
                    $titre_tech = get_sub_field('titre');
                    $cartes     = get_sub_field('cartes');
                ?>
                    <section class="w-full py-24 px-4 md:px-24">
                        <div class="w-full mx-auto">
                            <?php if ($titre_tech) DS::title($titre_tech, 'h2', 'mb-16 text-center'); ?>

                            <?php if ($cartes): ?>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                    <?php foreach ($cartes as $carte): ?>
                                        <div
                                            class="group bg-neutral-800/50 p-8 rounded-lg border border-neutral-800 hover:border-cyan-500/50 transition-colors">
                                            <div
                                                class="w-12 h-12 bg-neutral-700 rounded-full flex items-center justify-center mb-6 text-2xl">
                                                <?php echo $carte['icone']; // Emoji ou SVG 
                                                ?>
                                            </div>
                                            <h3 class="text-xl text-white font-medium mb-4"><?php echo esc_html($carte['titre']); ?></h3>
                                            <p class="text-neutral-400 font-light text-sm leading-relaxed">
                                                <?php echo esc_html($carte['description']); ?>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>

                <?php
                // --- BLOC : GALERIE (Bento Grid) ---
                elseif (get_row_layout() == 'bloc_galerie'):
                    DS::block_gallery();
                ?>

                <?php
                // --- BLOC : VIDÉO ---
                elseif (get_row_layout() == 'video'):
                    DS::block_video();
                ?>

                <?php
                // --- BLOC : TEXTE SIMPLE ---
                elseif (get_row_layout() == 'bloc_texte'):
                    DS::block_text();
                ?>

                <?php endif; ?>

            <?php endwhile; ?>
        <?php endif; ?>

        <?php
        $next_post = get_next_post();
        if ($next_post):
        ?>
            <section class="border-t border-neutral-800 py-20 px-4 md:px-24 text-center">
                <p class="text-neutral-500 text-sm uppercase mb-4">Projet Suivant</p>
                <?php DS::title(get_the_title($next_post->ID), 'h2', 'mb-8'); ?>
                <?php DS::link_button(get_permalink($next_post->ID), "Découvrir"); ?>
            </section>
        <?php endif; ?>

    </article>

</main>

<?php get_footer(); ?>