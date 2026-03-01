<?php

/**
 * Archive Template for 'projet' Post Type
 *
 * @package Portfolio
 */

get_header();
?>

<main id="main" class="site-main w-full max-w-7xl mx-auto bg-neutral-900 px-4 md:px-8" role="main">
    <div class="w-full mx-auto flex justify-center items-center mt-8">
        <div class="w-full mx-auto py-14">
            <div class="w-full flex flex-row items-center justify-between gap-2 mb-8">
                <h1 class="text-neutral-50 text-4xl font-normal font-['Wikolia_Pixel'] leading-tight drop-shadow-lg">
                    <?php post_type_archive_title(); ?>
                </h1>
            </div>

            <?php if (have_posts()) : ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full mx-auto justify-center items-start">
                    <?php while (have_posts()) : the_post(); ?>
                        <a href="<?php the_permalink(); ?>"
                            class="group bg-neutral-900 border border-neutral-800 flex flex-col items-center rounded-lg overflow-hidden relative w-full h-full mx-auto transition duration-200 hover:border-neutral-600 hover:shadow-lg no-underline">

                            <div class="w-full aspect-video relative overflow-hidden bg-neutral-800">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php
                                    the_post_thumbnail('card-projet-desktop', [
                                        'class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105',
                                        'sizes' => '(max-width: 768px) 600px, 1024px'
                                    ]);
                                    ?>
                                <?php else : ?>
                                    <div class="w-full h-full flex items-center justify-center text-neutral-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="w-full p-6 flex flex-col flex-grow">
                                <?php
                                // Récupération des champs ACF si pas dans la boucle (mais ici on est dedans)
                                $company_name = get_field('company_name');
                                $company_icon = get_field('company_icon');
                                ?>

                                <div class="flex items-center gap-3 mb-3">
                                    <?php if ($company_icon): ?>
                                        <?php if (is_numeric($company_icon)) : ?>
                                            <?php echo wp_get_attachment_image($company_icon, 'thumbnail', false, ['class' => 'w-6 h-6 rounded object-contain']); ?>
                                        <?php else : ?>
                                            <img src="<?php echo esc_url($company_icon); ?>" alt="" class="w-6 h-6 rounded object-contain" />
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($company_name): ?>
                                        <span class="text-sm text-neutral-400 font-light uppercase tracking-wide"><?php echo esc_html($company_name); ?></span>
                                    <?php endif; ?>
                                </div>

                                <h2 class="text-xl font-medium text-neutral-50 mb-2 group-hover:text-white transition-colors">
                                    <?php the_title(); ?>
                                </h2>

                                <div class="text-neutral-400 text-sm line-clamp-3 mt-auto">
                                    <?php the_excerpt(); ?>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <div class="mt-12 flex justify-center">
                    <?php
                    the_posts_pagination(array(
                        'prev_text' => '<span class="sr-only">Précédent</span><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>',
                        'next_text' => '<span class="sr-only">Suivant</span><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>',
                        'class'     => 'flex gap-2'
                    ));
                    ?>
                </div>

            <?php else : ?>
                <div class="text-center py-20">
                    <p class="text-neutral-400 text-lg">Aucun projet trouvé pour le moment.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php
get_footer();
