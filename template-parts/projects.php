<?php
$args = array(
    'post_type'      => 'projet',
    'posts_per_page' => 4,
);
$project_query = new WP_Query($args);
?>




<?php if ($project_query->have_posts()) : ?>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full mx-auto justify-center items-start">
    <?php while ($project_query->have_posts()) : $project_query->the_post(); ?>
    <a href="<?php the_permalink(); ?>"
        class="bg-neutral-900 flex flex-col items-center rounded-lg overflow-hidden relative w-full mx-auto transition duration-200 hover:border-neutral-700 hover:shadow-lg"
        style="text-decoration: none;">
        <div class="w-full mx-auto flex justify-center">
            <div class="h-auto w-full mx-auto aspect-video relative rounded-tl-lg rounded-tr-lg overflow-hidden bg-neutral-800">
                <?php if (has_post_thumbnail()) : ?>
                    <?php 
                    // On appelle la taille Mobile par défaut (src) pour éviter de charger une image énorme inutilement
                    // Le srcset fera le travail pour télécharger la version Desktop si nécessaire
                    echo get_the_post_thumbnail(get_the_ID(), 'card-projet-mobile', [
                        'class' => 'w-full mx-auto h-full object-cover transition-transform duration-700 hover:scale-105',
                        'sizes' => '(max-width: 1024px) 100vw, 50vw',
                        'loading' => 'lazy'
                    ]); 
                    ?>
                <?php endif; ?>
            </div>
        </div>
        <div
            class="bg-neutral-900 w-full mx-auto flex flex-col gap-3 items-center justify-center p-6 rounded-bl-lg rounded-br-lg">
            <div class="w-full mx-auto flex gap-2 items-center">
                <?php 
                $icon = get_field('company_icon');
                if ($icon) {
                    if (is_numeric($icon)) {
                        echo wp_get_attachment_image($icon, 'thumbnail', false, [
                            'class' => 'w-6 h-6 rounded',
                            'loading' => 'lazy'
                        ]);
                    } else {
                         // Fallback si URL brute (non recommandé mais supporté)
                        echo '<img src="' . esc_url($icon) . '" alt="Icon" class="w-6 h-6 rounded" loading="lazy" />';
                    }
                }
                ?>
                <?php if (get_field('company_name')): ?>
                <div class="text-base text-neutral-50 font-light"><?php echo get_field('company_name'); ?></div>
                <?php endif; ?>
            </div>
            <div class="w-full mx-auto flex flex-col items-start">
                <div class="text-xl font-medium text-neutral-50 leading-6 w-full mx-auto no-underline">
                    <?php the_title(); ?>
                </div>
            </div>
        </div>
        <div class="absolute border border-neutral-800 inset-0 rounded-lg pointer-events-none"></div>
    </a>
    <?php endwhile; ?>
</div>
<?php wp_reset_postdata(); ?>
<?php else : ?>
<p class="text-white">Aucun projet trouvé.</p>
<?php endif; ?>