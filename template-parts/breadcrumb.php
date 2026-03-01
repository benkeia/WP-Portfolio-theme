<?php
/**
 * Breadcrumb / Fil d'Ariane
 * Affiche le chemin de navigation sur toutes les pages (sauf l'accueil)
 */

// Ne pas afficher sur la page d'accueil
if (is_front_page()) {
    return;
}

// Construction du breadcrumb
$breadcrumb_items = array();

// Toujours afficher "Accueil" en premier
$breadcrumb_items[] = array(
    'title' => 'Accueil',
    'url'   => home_url('/')
);

// Page d'archive de projets
if (is_post_type_archive('projet')) {
    $breadcrumb_items[] = array(
        'title' => 'Projets',
        'url'   => ''
    );
}
// Single projet
elseif (is_singular('projet')) {
    $breadcrumb_items[] = array(
        'title' => 'Projets',
        'url'   => get_post_type_archive_link('projet')
    );
    $breadcrumb_items[] = array(
        'title' => get_the_title(),
        'url'   => ''
    );
}
// Page standard (comme About)
elseif (is_page()) {
    // Si la page a un parent
    $parent_id = wp_get_post_parent_id(get_the_ID());
    if ($parent_id) {
        $breadcrumb_items[] = array(
            'title' => get_the_title($parent_id),
            'url'   => get_permalink($parent_id)
        );
    }
    $breadcrumb_items[] = array(
        'title' => get_the_title(),
        'url'   => ''
    );
}
// Article de blog
elseif (is_single()) {
    $breadcrumb_items[] = array(
        'title' => 'Blog',
        'url'   => get_permalink(get_option('page_for_posts'))
    );
    $breadcrumb_items[] = array(
        'title' => get_the_title(),
        'url'   => ''
    );
}
// Page 404
elseif (is_404()) {
    $breadcrumb_items[] = array(
        'title' => 'Page introuvable',
        'url'   => ''
    );
}
// Autre (archive, search, etc.)
else {
    $breadcrumb_items[] = array(
        'title' => get_the_archive_title(),
        'url'   => ''
    );
}

// Affichage du breadcrumb
if (!empty($breadcrumb_items)) :
?>
    <nav aria-label="Fil d'Ariane" class="w-full bg-neutral-900">
        <div class="w-full px-4 md:px-24">
            <div class="max-w-[1200px] mx-auto py-8">
                <ol class="flex items-center gap-2 text-sm">
                    <?php foreach ($breadcrumb_items as $index => $item) : ?>
                        <li class="flex items-center gap-2">
                            <?php if ($index > 0) : ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-neutral-600">
                                    <path d="m9 18 6-6-6-6"/>
                                </svg>
                            <?php endif; ?>
                            
                            <?php if (!empty($item['url'])) : ?>
                                <a href="<?php echo esc_url($item['url']); ?>" 
                                   class="text-neutral-400 hover:text-white transition-colors duration-200 font-light"
                                   style="text-decoration: none !important;">
                                    <?php echo esc_html($item['title']); ?>
                                </a>
                            <?php else : ?>
                                <span class="text-neutral-50 font-light">
                                    <?php echo esc_html($item['title']); ?>
                                </span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </nav>
<?php endif; ?>
