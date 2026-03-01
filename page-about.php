<?php

/**
 * Template Name: About Page
 * Description: Page avec animation de révélation au scroll
 */

get_header();
?>

<main class="max-w-screen overflow-x-hidden">
    <section class="section relative">
        <div id="about-reveal" class="reveal h-screen flex items-center justify-center overflow-hidden relative w-full">
            <h1 class="reveal__heading text-white text-[3vw] m-0 font-medium">
                <span class="reveal__heading-left inline-block">
                    <?php echo get_field('about_name_part_1') ?: 'Baptiste'; ?>
                </span>
                <span class="reveal__heading-right inline-block ml-2">
                    <?php echo get_field('about_name_part_2') ?: 'Saegaert'; ?>
                </span>
            </h1>

            <h2 class="reveal__subheading absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 m-0 text-[4vw] font-medium text-white opacity-0 scale-50 will-change-transform">
                <?php echo get_field('about_subtitle') ?: 'Développeur Full Stack'; ?>
            </h2>

            <div class="reveal__media absolute left-0 top-0 w-full h-full -z-10 scale-0">
                <?php if (get_field('about_hero_image')): ?>
                    <img class="reveal__media-img absolute w-full h-full object-cover"
                        src="<?php echo esc_url(get_field('about_hero_image')['url']); ?>"
                        alt="<?php echo esc_attr(get_field('about_hero_image')['alt']); ?>">
                <?php elseif (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('full', ['class' => 'reveal__media-img absolute w-full h-full object-cover']); ?>
                <?php else: ?>
                    <div class="absolute w-full h-full bg-gradient-to-br from-purple-500/20 to-pink-500/20"></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="section bg-neutral-900 py-24">
        <div class="content text-[2vw] leading-relaxed mx-auto text-balance w-[50vw] max-w-4xl px-4 text-neutral-200">
            <?php
            if (have_posts()) :
                while (have_posts()) : the_post();
                    the_content();
                endwhile;
            endif;
            ?>
        </div>
    </section>

    <?php if (get_field('about_additional_sections')): ?>
        <?php while (have_rows('about_additional_sections')): the_row(); ?>
            <section class="section bg-neutral-900 py-16">
                <div class="max-w-4xl mx-auto px-4 text-neutral-200">
                    <?php if (get_sub_field('section_title')): ?>
                        <h3 class="text-3xl md:text-4xl font-medium text-white mb-8">
                            <?php echo esc_html(get_sub_field('section_title')); ?>
                        </h3>
                    <?php endif; ?>

                    <?php if (get_sub_field('section_content')): ?>
                        <div class="text-lg leading-relaxed">
                            <?php echo wp_kses_post(get_sub_field('section_content')); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endwhile; ?>
    <?php endif; ?>
</main>

<?php get_footer(); ?>