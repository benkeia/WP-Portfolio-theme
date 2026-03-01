<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body <?php body_class('bg-neutral-900'); ?> data-barba="wrapper">
    <div class="site-loader"></div>
    <div class="tt-page-transition"></div>
    <header class="w-full relative z-10 bg-neutral-900">
        <div class="w-full px-4 md:px-24">
            <div class="mx-auto max-w-[1200px] relative">
                <div class="flex items-center justify-between h-20 md:h-16 relative z-20">
                    <div id="site-title"
                        class="flex-shrink-0 transition-all duration-300 md:block w-auto flex items-center">
                        <a href="<?php echo esc_url(home_url('/')); ?>"
                            class="text-neutral-50 text-sm font-light hover:text-white transition-colors duration-300 no-underline">
                            mynameisbapt.ist
                        </a>
                    </div>
                    <nav id="primary-navigation"
                        class="hidden md:block fixed top-0 left-0 w-full h-full z-50 bg-neutral-900/95 backdrop-blur flex flex-col items-start justify-start px-8 pt-8 md:pt-0 md:px-0 md:items-center md:justify-end md:static md:bg-transparent md:backdrop-blur-none md:flex md:flex-row md:items-center md:justify-end transition-all duration-300">
                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'primary',
                                'menu_class'     => 'flex items-center gap-x-8',
                                'container'      => false,
                                'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                                'fallback_cb'    => false,
                                'depth'          => 1,
                            )
                        );
                        ?>
                        <style>
                        #primary-navigation a,
                        .flex-shrink-0 a {
                            text-decoration: none !important;
                        }
                        </style>
                        <div id="magic-line" class="absolute h-0.5 bg-white transition-all duration-300 md:block"
                            style="bottom: -5px;"></div>
                        <button type="button" id="primary-menu-close"
                            class="md:hidden absolute top-6 right-6 text-neutral-400 hover:text-white text-3xl">&times;</button>

                    </nav>
                    <div class="md:hidden flex items-center ml-2">
                        <button type="button" id="primary-menu-toggle"
                            class="text-neutral-400 hover:text-white flex items-center justify-center">
                            <span class="sr-only">Open main menu</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Trait pleine largeur -->
        <div class="w-full border-b border-neutral-800"></div>
    </header>
    <main data-barba="container" data-barba-namespace="<?php echo $barba_namespace ?? 'default'; ?>">