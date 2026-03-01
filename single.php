<?php
/**
 * Single post template file.
 *
 * @package TailPress
 */

if (get_post_type() === 'projet') {
    $barba_namespace = 'project';
} else {
    $barba_namespace = 'single';
}

get_header();
?>

<?php get_template_part('template-parts/breadcrumb'); ?>

<div class="container my-8 mx-auto">
    <?php if (have_posts()): ?>
        <?php while (have_posts()): the_post(); ?>
            <?php get_template_part('template-parts/content', 'single'); ?>

            <?php if (comments_open() || get_comments_number()): ?>
                <?php comments_template(); ?>
            <?php endif; ?>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php
get_footer();
