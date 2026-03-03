<?php

/**
 * Template pour l'affichage du portfolio PDF
 * Appliqué automatiquement à la page avec le slug "portfolio-pdf"
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title('—', true, 'right'); ?><?php bloginfo('name'); ?></title>
</head>

<body style="margin:0;padding:0;overflow:hidden;">
    <iframe
        src="https://baptiste-saegaert.fr/wp-content/uploads/2026/03/Portfolio-HD.pdf"
        style="width:100%;height:100vh;display:block;"
        frameborder="0"></iframe>
</body>

</html>