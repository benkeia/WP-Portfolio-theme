<?php

// Fix pour le Mixed Content via "Local Live Link" (ngrok)
// Si on passe par le tunnel HTTPS, on dit à WordPress qu'on est en HTTPS
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// FORCE HTTPS sur les URLs d'images pour éviter le Mixed Content sur le Live Link
add_filter('wp_get_attachment_url', 'force_https_on_live_link');
add_filter('the_content', 'force_https_on_live_link');
add_filter('wp_calculate_image_srcset', function ($sources) {
    if (!is_array($sources)) return $sources;
    foreach ($sources as &$source) {
        if (isset($source['url'])) {
            $source['url'] = force_https_on_live_link($source['url']);
        }
    }
    return $sources;
});

function force_https_on_live_link($content)
{
    if (is_string($content) && (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
        return str_replace('http://', 'https://', $content);
    }
    return $content;
}

if (is_file(__DIR__ . '/vendor/autoload_packages.php')) {
    require_once __DIR__ . '/vendor/autoload_packages.php';
}

// Charge le Design System
require_once get_template_directory() . '/inc/design-system.php';

function tailpress(): TailPress\Framework\Theme
{
    return TailPress\Framework\Theme::instance()
        ->assets(
            fn($manager) => $manager
                ->withCompiler(
                    new TailPress\Framework\Assets\ViteCompiler,
                    fn($compiler) => $compiler
                        ->registerAsset('resources/css/app.css')
                        ->registerAsset('resources/js/app.js')
                        ->editorStyleFile('resources/css/editor-style.css')
                )
                ->enqueueAssets()
        )
        ->features(fn($manager) => $manager->add(TailPress\Framework\Features\MenuOptions::class))
        ->menus(
            fn($manager) => $manager
                ->add('primary', __('Primary Menu', 'tailpress'))
                ->add('footer', __('Footer Menu', 'tailpress'))
        )
        ->themeSupport(fn($manager) => $manager->add([
            'title-tag',
            'custom-logo',
            'post-thumbnails',
            'align-wide',
            'wp-block-styles',
            'responsive-embeds',
            'html5' => [
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            ]
        ]));
}

// Personnalise le titre de l'onglet pour l'archive des projets
add_filter('pre_get_document_title', function ($title) {
    if (is_post_type_archive('projet')) {
        return 'Mes projets'; // ou 'Projets' selon préférence
    }
    return $title;
});

// --- OPTIMISATION IMAGES ---

add_action('after_setup_theme', function () {
    // Mobile & Tablette vertical (poids plume ~50-80ko)
    add_image_size('card-projet-mobile', 600, 340, true);

    // Desktop & Tablette horizontal (qualité rétina ~150-200ko)
    // Ratio conservé (1.76) : 1024 / 1.76 = 580
    add_image_size('card-projet-desktop', 1024, 580, true);
}, 15);

// Ajoute la classe js-menu-anim à chaque <li> du menu principal & style les <li> footer
add_filter('nav_menu_css_class', function ($classes, $item, $args) {
    if (isset($args->theme_location)) {
        if ($args->theme_location === 'primary') {
            $classes[] = 'js-menu-anim';
        }
        if ($args->theme_location === 'footer') {
            $classes[] = 'm-0 p-0 list-none'; // Force no margin/padding on LI
        }
    }
    return $classes;
}, 10, 3);

// Style des liens du menu (Header & Footer identiques)
add_filter('nav_menu_link_attributes', function ($atts, $item, $args) {
    $common_classes = 'text-neutral-400 hover:text-white transition-colors duration-200 text-sm font-light leading-5 no-underline block py-1';

    // Pour le menu principal (Primary)
    if (isset($args->theme_location) && $args->theme_location === 'primary') {
        $existing_classes = isset($atts['class']) ? $atts['class'] : '';
        // On s'assure que ces classes sont appliquées aussi
        $atts['class'] = trim($existing_classes . ' ' . $common_classes);
    }

    // Pour le menu footer
    if (isset($args->theme_location) && $args->theme_location === 'footer') {
        $existing_classes = isset($atts['class']) ? $atts['class'] : '';
        $atts['class'] = trim($existing_classes . ' ' . $common_classes);
    }

    return $atts;
}, 10, 3);

tailpress();

if (function_exists('acf_add_options_page')) {

    acf_add_options_page(array(
        'page_title'    => 'Réglages du Thème',
        'menu_title'    => 'Réglages Thème',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
}

function register_projets_cpt()
{
    $labels = array(
        'name'                  => _x('Projets', 'Post type general name', 'tailpress'),
        'singular_name'         => _x('Projet', 'Post type singular name', 'tailpress'),
        'menu_name'             => _x('Projets', 'Admin Menu text', 'tailpress'),
        'name_admin_bar'        => _x('Projet', 'Add New on Toolbar', 'tailpress'),
        'add_new'               => __('Add New', 'tailpress'),
        'add_new_item'          => __('Add New Projet', 'tailpress'),
        'new_item'              => __('New Projet', 'tailpress'),
        'edit_item'             => __('Edit Projet', 'tailpress'),
        'view_item'             => __('View Projet', 'tailpress'),
        'all_items'             => __('All Projets', 'tailpress'),
        'search_items'          => __('Search Projets', 'tailpress'),
        'parent_item_colon'     => __('Parent Projets:', 'tailpress'),
        'not_found'             => __('No projets found.', 'tailpress'),
        'not_found_in_trash'    => __('No projets found in Trash.', 'tailpress'),
        'featured_image'        => _x('Projet Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'tailpress'),
        'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'tailpress'),
        'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'tailpress'),
        'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'tailpress'),
        'archives'              => _x('Projets', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'tailpress'),
        'insert_into_item'      => _x('Insert into projet', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'tailpress'),
        'uploaded_to_this_item' => _x('Uploaded to this projet', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'tailpress'),
        'filter_items_list'     => _x('Filter projets list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'tailpress'),
        'items_list_navigation' => _x('Projets list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'tailpress'),
        'items_list'            => _x('Projets list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'tailpress'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'thumbnail', 'custom-fields'),
        'menu_icon'          => 'dashicons-portfolio',
        'show_in_rest'       => true,
    );

    register_post_type('projet', $args);
}
add_action('init', 'register_projets_cpt');

// Taxonomies pour les projets
function register_projet_taxonomies()
{
    // Domaines (Ex: Web Design, E-commerce, Portfolio, etc.)
    $domain_labels = array(
        'name'              => _x('Domaines', 'taxonomy general name', 'tailpress'),
        'singular_name'     => _x('Domaine', 'taxonomy singular name', 'tailpress'),
        'search_items'      => __('Search Domaines', 'tailpress'),
        'all_items'         => __('All Domaines', 'tailpress'),
        'parent_item'       => __('Parent Domaine', 'tailpress'),
        'parent_item_colon' => __('Parent Domaine:', 'tailpress'),
        'edit_item'         => __('Edit Domaine', 'tailpress'),
        'update_item'       => __('Update Domaine', 'tailpress'),
        'add_new_item'      => __('Add New Domaine', 'tailpress'),
        'new_item_name'     => __('New Domaine Name', 'tailpress'),
        'menu_name'         => __('Domaines', 'tailpress'),
    );

    register_taxonomy('domaine', array('projet'), array(
        'hierarchical'      => true,
        'labels'            => $domain_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'domaine'),
        'show_in_rest'      => true,
    ));

    // Technologies (Ex: WordPress, React, GSAP, Three.js, etc.)
    $tech_labels = array(
        'name'              => _x('Technologies', 'taxonomy general name', 'tailpress'),
        'singular_name'     => _x('Technologie', 'taxonomy singular name', 'tailpress'),
        'search_items'      => __('Search Technologies', 'tailpress'),
        'all_items'         => __('All Technologies', 'tailpress'),
        'parent_item'       => __('Parent Technologie', 'tailpress'),
        'parent_item_colon' => __('Parent Technologie:', 'tailpress'),
        'edit_item'         => __('Edit Technologie', 'tailpress'),
        'update_item'       => __('Update Technologie', 'tailpress'),
        'add_new_item'      => __('Add New Technologie', 'tailpress'),
        'new_item_name'     => __('New Technologie Name', 'tailpress'),
        'menu_name'         => __('Technologies', 'tailpress'),
    );

    register_taxonomy('technologie', array('projet'), array(
        'hierarchical'      => false,
        'labels'            => $tech_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'technologie'),
        'show_in_rest'      => true,
    ));
}
add_action('init', 'register_projet_taxonomies');

function register_experiences_cpt()
{
    $labels = array(
        'name'                  => _x('Experiences', 'Post type general name', 'tailpress'),
        'singular_name'         => _x('Experience', 'Post type singular name', 'tailpress'),
        'menu_name'             => _x('Experiences', 'Admin Menu text', 'tailpress'),
        'name_admin_bar'        => _x('Experience', 'Add New on Toolbar', 'tailpress'),
        'add_new'               => __('Add New', 'tailpress'),
        'add_new_item'          => __('Add New Experience', 'tailpress'),
        'new_item'              => __('New Experience', 'tailpress'),
        'edit_item'             => __('Edit Experience', 'tailpress'),
        'view_item'             => __('View Experience', 'tailpress'),
        'all_items'             => __('All Experiences', 'tailpress'),
        'search_items'          => __('Search Experiences', 'tailpress'),
        'parent_item_colon'     => __('Parent Experiences:', 'tailpress'),
        'not_found'             => __('No experiences found.', 'tailpress'),
        'not_found_in_trash'    => __('No experiences found in Trash.', 'tailpress'),
        'featured_image'        => _x('Experience Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'tailpress'),
        'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'tailpress'),
        'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'tailpress'),
        'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'tailpress'),
        'archives'              => _x('Experience archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'tailpress'),
        'insert_into_item'      => _x('Insert into experience', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'tailpress'),
        'uploaded_to_this_item' => _x('Uploaded to this experience', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'tailpress'),
        'filter_items_list'     => _x('Filter experiences list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'tailpress'),
        'items_list_navigation' => _x('Experiences list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'tailpress'),
        'items_list'            => _x('Experiences list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'tailpress'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'experiences'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'thumbnail'),
        'menu_icon'          => 'dashicons-awards',
    );

    register_post_type('experience', $args);
}
add_action('init', 'register_experiences_cpt');

// Enqueue le JS Vite buildé en production
add_action('wp_enqueue_scripts', function () {
    // Change this to `true` to use the dev server, `false` for production build.
    // Or define this constant in your `wp-config.php` file.
    $is_dev = defined('VITE_DEV') ? VITE_DEV : true;

    if ($is_dev) {
        // En mode dev, charge le JS et CSS via le serveur Vite
        wp_enqueue_script('portfolio-app', 'http://localhost:3000/resources/js/app.js', [], null, true);
        wp_enqueue_style('portfolio-style', 'http://localhost:3000/resources/css/app.css', [], null);
    } else {
        // En production, charge le JS/CSS buildé
        $dist_dir = get_template_directory_uri() . '/dist/assets/';
        foreach (glob(get_template_directory() . '/dist/assets/app-*.js') as $file) {
            $basename = basename($file);
            wp_enqueue_script('portfolio-app', $dist_dir . $basename, [], null, true);
            break;
        }
        foreach (glob(get_template_directory() . '/dist/assets/app-*.css') as $file) {
            $basename = basename($file);
            wp_enqueue_style('portfolio-style', $dist_dir . $basename, [], null);
            break;
        }
    }
});

// Ajoute type="module" aux scripts TailPress/Vite pour supporter import/export ES6
add_filter('script_loader_tag', function ($tag, $handle) {
    if (strpos($tag, 'module') === false && str_starts_with($handle, 'portfolio-app')) {
        $tag = str_replace('<script ', '<script type="module" ', $tag);
    }
    return $tag;
}, 11, 2);

// Désactive les styles Gutenberg (block-library) pour améliorer les perfs
// Si tu utilises l'éditeur de blocs, commente cette fonction
add_action('wp_enqueue_scripts', function () {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-block-style'); // WooCommerce
    wp_dequeue_style('global-styles'); // Styles globaux WP 5.9+
}, 100);

/**
 * Configuration ACF / Secure Custom Fields (SCF) - Local JSON
 * Synchronisation automatique des champs via fichiers JSON
 */

// 1. Sauvegarder les groupes de champs dans /acf-json
add_filter('acf/settings/save_json', function ($path) {
    return get_stylesheet_directory() . '/acf-json';
});

// 2. Charger les groupes de champs depuis /acf-json
add_filter('acf/settings/load_json', function ($paths) {
    unset($paths[0]); // Optionnel : retire le chemin par défaut si on veut être strict
    $paths[] = get_stylesheet_directory() . '/acf-json';
    return $paths;
});