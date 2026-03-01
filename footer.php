</main>
<footer class="w-full bg-neutral-900 border-t border-neutral-800 pt-12 pb-8 px-4 md:px-24">
    <div class="w-full mx-auto max-w-[1200px] flex flex-col gap-8">

        <!-- Top Section: Menu & Socials -->
        <div class="flex flex-col md:flex-row justify-between items-center md:items-start gap-6">

            <!-- Footer Menu -->
            <nav id="footer-navigation" class="flex flex-col items-center md:items-start gap-4">
                <?php
                if (has_nav_menu('footer')) {
                    wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'container'      => false,
                        'menu_class'     => 'flex flex-row flex-wrap justify-center md:justify-start gap-6 list-none p-0 m-0',
                        'fallback_cb'    => false,
                        'depth'          => 1,
                    ));
                }
                ?>
                <style>
                    #footer-navigation a {
                        text-decoration: none !important;
                        color: #a3a3a3; /* neutral-400 */
                        font-size: 0.875rem; /* text-sm */
                        font-weight: 300; /* font-light */
                        transition: color 200ms;
                    }
                    #footer-navigation a:hover {
                        color: #ffffff;
                    }
                </style>
            </nav>

            <!-- Social Media Icons (ACF) -->
            <?php if (have_rows('footer_socials', 'option')): ?>
                <div class="flex flex-row items-center gap-4">
                    <?php while (have_rows('footer_socials', 'option')): the_row();
                        $name = get_sub_field('network_name');
                        $url = get_sub_field('network_url');
                        $icon = get_sub_field('network_icon_svg');
                    ?>
                        <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer"
                            aria-label="<?php echo esc_attr($name); ?>" title="<?php echo esc_attr($name); ?>"
                            class="text-neutral-400 hover:text-white transition-colors duration-200 block w-6 h-6">
                            <?php echo $icon; // Raw SVG output 
                            ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

        </div>

        <!-- Divider -->
        <div class="w-full h-px bg-neutral-800"></div>

        <!-- Bottom Section: Credits -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex flex-row items-center gap-1">
                <span class="text-neutral-400 text-sm font-light">© <?php echo date('Y'); ?> Baptiste. Tous droits
                    réservés.</span>
            </div>

            <div class="flex flex-row items-center gap-1">
                <span class="text-neutral-400 text-sm font-light">Développé avec le coeur</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                        clip-rule="evenodd" />
                </svg>
            </div>
        </div>

    </div>
</footer>
<?php wp_footer(); ?>
</body>

</html>