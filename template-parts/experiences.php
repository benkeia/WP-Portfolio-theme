<?php
$args = array(
    'post_type'      => 'experience',
    'posts_per_page' => 3,
);
$experience_query = new WP_Query($args);
?>

<?php if ($experience_query->have_posts()) : ?>
    <?php while ($experience_query->have_posts()) : $experience_query->the_post(); ?>
        <div class="self-stretch pl-4 relative flex flex-col justify-start items-center gap-4">
            <div class="self-stretch flex flex-col justify-center items-start gap-2">
                <div class="self-stretch inline-flex justify-start items-center flex-wrap content-center">
                    <div class="size- inline-flex flex-col justify-start items-start">
                        <div class="self-stretch flex flex-col justify-start items-start">
                            <div class="justify-center text-neutral-50 text-xl font-medium leading-6">
                                <?php the_title(); ?></div>
                        </div>
                    </div>
                </div>
                <div class="size- flex flex-col justify-start items-start">
                    <div class="self-stretch flex flex-col justify-start items-start">
                        <div class="justify-center text-neutral-400 text-sm font-light uppercase leading-4">
                            <?php the_field('date_range'); ?></div>
                    </div>
                </div>
            </div>
            <div
                class="self-stretch flex flex-col justify-center items-start gap-3 text-neutral-50 font-extralight overflow-hidden">
                <?php the_content(); ?>
            </div>
            <div class="w-full h-full left-0 top-0 absolute border-l border-neutral-800"></div>
        </div>
    <?php endwhile; ?>
    <?php wp_reset_postdata(); ?>
<?php else : ?>
    <p><?php _e('No experiences found.', 'tailpress'); ?></p>
<?php endif; ?>