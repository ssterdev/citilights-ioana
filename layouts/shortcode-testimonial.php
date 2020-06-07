<?php
/**
 * Created by PhpStorm.
 * User: Vietbrain-09
 * Date: 09-Jul-18
 * Time: 2:37 PM
 */

$args = array(
    'orderby' => 'post__in',
    'post__in' => explode(",", $testimonial_ids),
    'posts_per_page' => -1,
    // Unlimited testimonial
    'post_type' => 'testimonial',
    'post_status' => 'publish',
);

if ($data_source == 'list_cat') {

    $args = array(
        'posts_per_page' => -1,
        // Unlimited testimonial
        'orderby' => 'post_date',
        'post_type' => 'testimonial',
        'post_status' => 'publish',
    );
    if (!empty($category) && $category != 'all') {
        $args['tax_query'] = array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'testimonial_category',
                'field' => 'id',
                'terms' => explode(',', $category),
            ),
        );
    }
}
$query = new WP_Query($args);
$size = sizeof($query->posts);
$testiID = 'testimonial_'.uniqid();
?>
<div <?php echo($class . ' ' . $custom_style); ?> >
    <?php if (!empty($title)): ?>
        <h3 class="sc-title">
            <?php echo(esc_html($title)); ?>
        </h3>
    <?php endif; ?>
    <?php if ($query->have_posts()): ?>
        <?php
        $quote_list = array();
        $author_list = array();
        $count = 0;
        ?>
        <?php
        while ($query->have_posts()): $query->the_post();
            global $post;
            $name = get_post_meta(get_the_ID(), '_noo_wp_post_name', true);
            $position = get_post_meta(get_the_ID(), '_noo_wp_post_position', true);
            $url = get_post_meta(get_the_ID(), '_noo_wp_post_image', true);
            $quote_list[] = '<li data-page="' . $count . '">';

            if ($style == 'style-1') {
                $quote_list[] = '<p class="quote"><em>"' . get_the_content() . '"</em></p>';
                $quote_list[] = '<div class = "ds-author">';
            }
            if ($style == 'style-2') {
                $quote_list[] = '<p class="quote-2"><em>"' . get_the_content() . '"</em></p>';
                $quote_list[] = '<div class = "ds-author-2">';
            }
            if ($style == 'style-3') {
                $quote_list[] = '<p class="quote-3"><em>"' . get_the_content() . '"</em></p>';
                $quote_list[] = '<div class = "ds-author-3">';
            }
            $quote_list[] = '<div class = "ds-item" style="width: 70px;">';
            $quote_list[] = '<img class="noo-img-responsive ds-author-img" data-source="' . $count . '" class="grayscale" src="' . wp_get_attachment_url(esc_attr($url)) . '" alt="' . esc_html($post->post_title) . '" width="70" height="70"/></div>';
            $quote_list[] = '<div class = "ds-item ds-user" >';
            $quote_list[] = '<h4 class="name">' . $name . '</h4>';
            $quote_list[] = '';
            $quote_list[] = '<p class="position"><em>' . $position . '</em></p></div>';
            $quote_list[] = '</div>';
            $quote_list[] = '</li>';
            $author_list[] = '';

            $count++;
        endwhile;
        wp_reset_postdata();
        ?>

        <div class="noo-testimonial">
            <div style="text-align: center !important; list-style: none;" id="<?php echo $testiID?>">
                <ul class="noo-content owl-carousel owl-theme" style="padding: 0;">
                    <?php echo implode("\n", $quote_list); ?>
                </ul>
            </div>
            <?php if ($style == 'style-2'): ?>
                <div class="owl-dots" style="text-align: left">
                    <div class="owl-dot active"><span></span></div>
                    <div class="owl-dot"><span></span></div>
                    <div class="owl-dot"><span></span></div>
                </div>
            <?php endif; ?>
        </div>
        <script type="text/javascript">
            jQuery('document').ready(function ($) {
                var $quoteCarousel = $('#<?php echo $testiID ?> ul');
                $quoteCarousel.owlCarousel({
                    items: 1,
                    loop: true,
                    rtl: <?php echo is_rtl() ? 'true' : 'false'; ?>,
                    autoplay: <?php echo $autoplay ?>,
                    autoplayTimeout: <?php echo esc_js($duration) ?>,
                    autoplayHoverPause: true,
                    lazyLoad: true,
                    center: true,
                    autoplaySpeed: 500,
                });
                <?php
                if($style == 'style-2'){
                    ?>
                    $('#<?php echo $testiID ?> .owl-dots').css('text-align','<?php echo is_rtl() ? "right" : "left" ?>');
                    $('#<?php echo $testiID ?> .owl-dots').css('padding','20px 0 0 0');
                    <?php
                    }
                ?>
                <?php 
                 if($style == 'style-3'){
                    ?>
                    $('#<?php echo $testiID ?> .owl-dots').css('text-align','right');
                    $('#<?php echo $testiID ?> .owl-dots').css('padding','20px 0 0 0');
                    <?php
                    }
                ?>

            });
        </script>
    <?php endif; ?>
    <?php if (absint($query->post_count) > 1) : ?>

    <?php endif; ?>
</div>


