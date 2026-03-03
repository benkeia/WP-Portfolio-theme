<?php

/**
 * Template Name: Visionneuse PDF
 * Template Post Type: page
 */

$pdf_file = get_field('pdf_file');
$pdf_url  = is_array($pdf_file) ? $pdf_file['url'] : $pdf_file;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title('—', true, 'right'); ?><?php bloginfo('name'); ?></title>
</head>

<body style="margin:0;padding:0;overflow:hidden;">
    <?php if ($pdf_url) : ?>
        <iframe
            src="<?php echo esc_url($pdf_url); ?>"
            style="width:100%;height:100vh;display:block;"
            frameborder="0"></iframe>
    <?php else : ?>
        <p style="font-family:sans-serif;padding:2rem;">Aucun fichier PDF renseigné.</p>
    <?php endif; ?>
</body>

</html>