<?php

/**
 * PORTFOLIO DESIGN SYSTEM
 * Un fichier pour les gouverner tous.
 * Utilisation : DS::component_name($args);
 */

class DS
{

    /* ==========================================================================
       ATOMES (Éléments de base)
       ========================================================================== */

    /**
     * Affiche un Titre avec le style du site
     */
    public static function title(string $text, string $level = 'h2', string $class = '')
    {
        $base_class = "text-neutral-50 font-medium leading-tight tracking-tight";

        $sizes = [
            'h1' => 'text-4xl md:text-6xl lg:text-7xl font-bold',
            'h2' => 'text-3xl md:text-5xl',
            'h3' => 'text-2xl md:text-3xl',
            'h4' => 'text-xl font-medium',
        ];

        $size = $sizes[$level] ?? $sizes['h2'];

        echo "<{$level} class='{$base_class} {$size} {$class}'>" . esc_html($text) . "</{$level}>";
    }

    /**
     * Affiche un bouton/lien stylisé (Style "Voir tout")
     */
    public static function link_button(string $url, string $text = "Voir le projet")
    {
?>
        <a href="<?php echo esc_url($url); ?>"
            class="group inline-flex items-center gap-2 cursor-pointer select-none no-underline">
            <span
                class="text-neutral-400 text-xs font-normal uppercase tracking-wide group-hover:text-white transition-colors duration-200">
                <?php echo esc_html($text); ?>
            </span>
            <span
                class="inline-flex items-center justify-center transition-transform duration-300 group-hover:translate-x-1 group-hover:-translate-y-1">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round"
                    class="text-neutral-400 group-hover:text-white transition-colors">
                    <path d="M7 7h10v10" />
                    <path d="M7 17 17 7" />
                </svg>
            </span>
        </a>
    <?php
    }

    /**
     * Affiche un tag/badge (Année, Client...)
     */
    public static function badge(string $label, string $value = '')
    {
        if (empty($label)) return;
    ?>
        <div class="flex flex-col items-start gap-1">
            <span class="text-neutral-500 text-[10px] uppercase tracking-wider font-bold"><?php echo esc_html($label); ?></span>
            <span class="text-neutral-200 text-sm font-light"><?php echo esc_html($value ?: $label); ?></span>
        </div>
    <?php
    }

    /**
     * Wrapper d'image standardisé (Arrondis, Border, Cover)
     * @param int $attachment_id
     * @param string $size Format WP ('thumbnail', 'medium', 'large', 'full'...)
     * @param string $class Classes CSS du conteneur (ex: 'aspect-video')
     * @param array $attr Attributs passés à wp_get_attachment_image (loading, sizes, fetchpriority...)
     */
    public static function image(int $attachment_id, string $size = 'large', string $class = 'aspect-video', array $attr = [])
    {
        if (!$attachment_id) return;

        // Valeurs par défaut pour les attributs de l'image
        $default_attr = [
            'class' => 'w-full h-full object-cover transition-transform duration-700 group-hover:scale-105'
        ];

        // Fusionner les classes si passées dans $attr
        if (isset($attr['class'])) {
            $default_attr['class'] = $attr['class']; // On écrase ou on concatène selon le besoin, ici on remplace pour flexibilité totale
        }

        $final_attr = array_merge($default_attr, $attr);
    ?>
        <div class="relative w-full <?php echo esc_attr($class); ?> rounded-lg overflow-hidden bg-neutral-800 group">
            <?php echo wp_get_attachment_image($attachment_id, $size, false, $final_attr); ?>
            <div class="absolute inset-0 border border-neutral-800/50 rounded-lg pointer-events-none z-10"></div>
        </div>
    <?php
    }

    /* ==========================================================================
       ORGANISMES (Blocs Complets ACF)
       ========================================================================== */

    /**
     * BLOC : Hero Projet (Haut de page)
     * Champs ACF attendus : titre, client, annee, image_hero (ID)
     */
    public static function block_hero()
    {
        $titre = get_sub_field('titre') ?: get_the_title();
        $client = get_sub_field('client');
        $annee = get_sub_field('annee');
        $img_id = get_sub_field('image_hero');
    ?>
        <section class="w-full pt-32 pb-12 px-4 md:px-24">
            <div class="w-full max-w-[1200px] mx-auto">

                <div
                    class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-8 mb-12 pb-8 border-b border-neutral-800">
                    <div class="max-w-3xl">
                        <?php self::title($titre, 'h1', 'mb-6'); ?>
                    </div>

                    <div class="flex gap-12 shrink-0">
                        <?php if ($client) self::badge('Client', $client); ?>
                        <?php if ($annee) self::badge('Année', $annee); ?>
                        <?php self::badge('Rôle', 'Creative Dev'); // Statique ou ACF 
                        ?>
                    </div>
                </div>

                <?php self::image($img_id, 'full', 'aspect-[16/10] shadow-2xl'); ?>
            </div>
        </section>
    <?php
    }

    /**
     * BLOC : Contenu Texte (Pleine largeur avec reveal)
     * Champs ACF attendus : contenu (wysiwyg)
     */
    public static function block_text()
    {
        $content = get_sub_field('contenu');
        if (!$content) return;
    ?>
        <section class="w-full py-20 px-4 md:px-24">
            <div class="w-full max-w-[1200px] mx-auto">
                <div class="reveal-type text-neutral-600 text-xl md:text-3xl lg:text-4xl font-light leading-relaxed"
                    data-bg-color="#525252"
                    data-fg-color="#ffffff">
                    <?php echo wp_kses_post($content); ?>
                </div>
            </div>
        </section>
    <?php
    }

    /**
     * BLOC : Galerie Grille (Bento dynamique)
     */
    public static function block_gallery()
    {
        $titre = get_sub_field('titre');
        // Récupération et fusion des champs 'affiche' et 'images' du JSON existant
        $galerie_ids = get_sub_field('images') ?: [];
        $affiche_id  = get_sub_field('affiche');

        // On crée un tableau unifié
        $images = $galerie_ids;
        if ($affiche_id) {
            array_unshift($images, $affiche_id);
        }

        // Sécurité : IDs uniques et format tableau
        $images = array_unique((array)$images);

        if (empty($images)) return;

        $count = count($images);

        // Classes de la grille parent
        $grid_cols = match (true) {
            $count === 1 => 'grid-cols-1',
            $count === 2 => 'grid-cols-1 md:grid-cols-2',
            $count === 3 => 'grid-cols-1 md:grid-cols-2',
            $count === 4 => 'grid-cols-1 md:grid-cols-2',
            $count === 5 => 'grid-cols-1 md:grid-cols-6',
            default      => 'grid-cols-1 md:grid-cols-3', // 6 et +
        };
    ?>
        <section class="w-full py-20 px-4 md:px-24">
            <div class="w-full max-w-[1200px] mx-auto">
                <?php if ($titre): ?>
                    <div class="flex justify-between items-end mb-10">
                        <?php self::title($titre, 'h2'); ?>
                    </div>
                <?php endif; ?>

                <div class="grid <?php echo $grid_cols; ?> gap-6 lightbox-gallery">
                    <?php foreach ($images as $index => $img_id):
                        $class = 'aspect-[4/3]'; // Défaut

                        if ($count === 1) {
                            $class = 'aspect-[16/9]';
                        } elseif ($count === 3) {
                            // 1 en haut (full), 2 en bas
                            if ($index === 0) $class = 'md:col-span-2 aspect-[21/9]';
                        } elseif ($count === 5) {
                            // 2 en haut (moitié), 3 en bas (tiers)
                            // Sur grille de 6: 
                            // Index 0,1 : col-span-3
                            // Index 2,3,4 : col-span-2
                            if ($index < 2) $class = 'md:col-span-3 aspect-[4/3]';
                            else $class = 'md:col-span-2 aspect-square'; // Carré pour les petits
                        } elseif ($count > 5) {
                            // Pour plus de variété sur les grandes galeries : les images sont carrées
                            $class = 'aspect-square';
                        }

                        $full_url = wp_get_attachment_image_url($img_id, 'full');
                        $thumb_url = wp_get_attachment_image_url($img_id, 'medium'); // URL pour la miniature
                    ?>
                        <a href="<?php echo esc_url($full_url); ?>"
                            data-external-thumb-image="<?php echo esc_url($thumb_url); ?>"
                            class="lightbox-trigger no-barba block relative w-full <?php echo $class; ?> cursor-zoom-in">
                            <?php self::image($img_id, 'large', 'w-full h-full'); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php
    }

    /**
     * BLOC : Vidéo (Embed ou Fichier)
     * Champs ACF attendus : type (select), video_file, video_embed
     */
    public static function block_video()
    {
        $type = get_sub_field('type');
        $poster = get_sub_field('poster');
    ?>
        <section class="w-full py-12 px-4 md:px-24">
            <div class="w-full max-w-[1200px] mx-auto">
                <div class="relative w-full aspect-video rounded-lg overflow-hidden bg-neutral-950 border border-neutral-800">
                    <?php if ($type === 'file'): ?>
                        <video class="w-full h-full object-cover" controls playsinline
                            poster="<?php echo $poster ? wp_get_attachment_image_url($poster, 'full') : ''; ?>">
                            <source src="<?php echo esc_url(get_sub_field('video_file')['url']); ?>" type="video/mp4">
                        </video>
                    <?php else: ?>
                        <div class="w-full h-full iframe-container">
                            <?php echo get_sub_field('video_embed'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
<?php
    }
}
