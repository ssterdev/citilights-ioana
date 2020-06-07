<?php
/**
 * Created by PhpStorm.
 * User: Vietbrain-09
 * Date: 20-Jul-18
 * Time: 2:28 PM
 */
$no_html = array();

wp_enqueue_script('owlcarousel2');
wp_enqueue_style('owlcarousel2');
//wp_enqueue_style('owlcarousel2-theme');
$slideID = uniqid('slide_');
$propSearch = uniqid('propsearch_');


function getTaxName($slug)
{
    $term = get_term_by('slug', $slug, 'property_category');
    return $name = $term->name;
}

function getPropertyTypeUrl($type)
{
    $url = site_url() . '/listings/'. $type;
    return $url;
}

$result_pages = get_pages(
            array(
                    'meta_key' => '_wp_page_template',
                    'meta_value' => 'search-property-result.php'
            )
        );
if($result_pages){
    $first_page = reset($result_pages);
    $result_page_url = get_permalink($first_page->ID);
    if(is_page($first_page->ID)){
        $show_status = true;
    }
}else{
    $result_page_url = get_post_type_archive_link( 'noo_property' );
}

?>
<div id="<?php echo $propSearch ?>" <?php echo $class ?>>
    <div class="property-search">
        <div class="search-title">
            <h1><?php echo $heading ?></h1>
            <p><?php echo $description ?></p>
        </div>
        <div>
            <form action="<?php echo $result_page_url; ?>" name="search-form" method="get" class="gsearchform" role="search">
                <div class="noo-input-group">
                    <input id="search-keyword" name="keyword" type="text" class="form-control" placeholder="Your Keywords...">
                    <div class="input-group-btn gsearch-action">
                        <button class="btn btn-black btn-search" type="submit" href=""><?php _e('Search', 'noo'); ?></button>
                    </div>
                </div>
            </form>
        </div>

        <?php if ($enable_slider == 'true'): ?>
            <div id="<?php echo $slideID ?>" class="property-type-slider owl-carousel owl-theme">
                <?php foreach ($items as $i): ?>
                    <div class="slide-wrap">
                        <div class="property-type">
                            <a href="<?php echo getPropertyTypeUrl($i['type_id']) ?>" ><img src="<?php echo wp_get_attachment_image_url($i['icon']) ?>" class="img-responsve"></a>
                        </div>
                        <p>
                            <a href="<?php echo getPropertyTypeUrl($i['type_id']) ?>"><?php echo getTaxName($i['type_id']) ?></a>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div>
                <a class="owl2-prev search-prev"></a>
                <a class="owl2-next search-next"></a>
            </div>
        <?php endif; ?>
    </div>
</div>
<script type="text/javascript">
    jQuery('document').ready(function ($) {
        let slide = $('#<?php echo $slideID?>');
        slide.owlCarousel({
            //items: 5,
            loop: true,
            autoplay: <?php echo $autoplay ?>,
            // autoplay: false,
            rtl:true,
            autoplayTimeout: <?php echo esc_js($duration) ?>,
            autoplayHoverPause: true,
            lazyLoad: true,
            autoplaySpeed: 500,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 1,
                },
                600: {
                    items: 3,
                },
                1000: {
                    items: 5,
                }
            }
        });
        $('#<?php echo esc_js($propSearch)?> .owl2-next').click(function () {
            slide.trigger('next.owl.carousel');
        })
        $('#<?php echo esc_js($propSearch)?> .owl2-prev').click(function () {
            slide.trigger('prev.owl.carousel');
        })
    })
</script>
