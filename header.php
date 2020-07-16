<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>


	
    <?php
    $favicon = noo_get_image_option('noo_custom_favicon', '');
    if ($favicon != ''): ?>
        <link rel="shortcut icon" href="<?php echo $favicon; ?>"/>
    <?php
    endif; ?>
    <?php if (defined('WPSEO_VERSION')) : ?>
        <title><?php wp_title(''); ?></title>
    <?php else : ?>
        <title><?php wp_title(' - ', true, 'left'); ?></title>
    <?php endif; ?>
    <?php if (is_singular('noo_property') && get_post_type() == 'noo_property') :
        $image_id = get_post_thumbnail_id();
        $social_share_img = wp_get_attachment_image_src($image_id, 'full');
        if (!empty($social_share_img) && isset($social_share_img[0])) :
            ?>
            <meta property="og:image" content="<?php echo $social_share_img[0]; ?>"/>
            <meta property="og:image:secure_url" content="<?php echo $social_share_img[0]; ?>"/>
        <?php endif;
    endif;
    ?>
    <!--[if lt IE 9]>
    <script src="<?php echo NOO_FRAMEWORK_URI . '/vendor/respond.min.js'; ?>"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <![endif]-->
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div class="site">

    <?php
    $rev_slider_pos = home_slider_position(); ?>
    <?php
    if ($rev_slider_pos == 'above') {
        noo_get_layout('slider-revolution');
    }
    ?>
    <?php noo_get_layout('topbar'); ?>
    <header class="noo-header <?php noo_header_class(); ?>" role="banner">
        <?php noo_get_layout('navbar'); ?>

    </header>

<?php
if ($rev_slider_pos == 'below') {
    noo_get_layout('slider-revolution');
}
?>