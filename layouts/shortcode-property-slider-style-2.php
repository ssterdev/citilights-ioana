<?php
foreach ($items as $item) {
    $itemList[] = $item['property_id'];
    $args = array(
        'orderby' => 'post__in',
        'post__in' => $itemList,
        'posts_per_page' => -1,
        'post_type' => 'noo_property',
        'post_status' => 'publish',
    );
}
$query = new WP_Query($args);
$listProp = $query->posts;
$sliderID = uniqid('slider_');
wp_enqueue_style('owlcarousel2');
wp_enqueue_script('owlcarousel2');
$uid = uniqid('sharing');
$current_user = wp_get_current_user();
$user_id      = $current_user->ID;
$id_compare = uniqid('submit-compare-');
$compare_listings = noo_get_page_link_by_template('compare-listings.php');
$property_favorites = noo_get_page_link_by_template('property_favorites.php');
$social_enabled     = get_theme_mod("noo_property_social", true );

?>

<div <?php echo $class ?> <?php echo $custom_style ?>>

    <?php if ( !empty( $compare_listings ) ) : ?>

        <div class="noo-property-compare" style="margin-left: 30px;">

            <form style="display: none" method="POST" action="<?php echo esc_url( $compare_listings ); ?>" class="<?php echo $id_compare ?>">

                <h3 class="title-compare">
                    <?php echo esc_html__( 'Compare properties', 'noo' ); ?>
                </h3>

                <div class="list-compare"></div>

                <input type="submit" name="submit-compare" value="<?php echo esc_html__( 'Compare', 'noo' ); ?>" />

            </form>

        </div><!-- /.noo-property-compare -->

    <?php endif; ?>
    <div id="<?php echo $sliderID ?>" class="owl-carousel owl-theme">
        <?php foreach ($listProp as $i): ?>
            <div class="property-slide-style-2"
                 style="background-image: url(<?php echo get_the_post_thumbnail_url($i->ID) ?>);">
                <div class="property-slide-style-2--content">
                    <div class="ps-style-2--wrap">
                        <div>
                            <div class="ps-style-2--title">
                                <h4><a href="<?php echo get_post_permalink($i->ID); ?>"
                                       title="<?php echo $i->post_title; ?>">
                                        <?php echo $i->post_title; ?>
                                    </a>
                                </h4>
                                <?php if ($address = noo_get_post_meta($i->ID, '_address')) : ?>
                                    <small class="ps-style-2--address">
                                        <i class="fa fa-map-marker"></i> <?php echo esc_html($address); ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="ps-style-2--action">
                            <?php if ( !empty( $social_enabled ) ) : ?>
                                <div class="property-sharing <?php echo $uid = uniqid( 'sharing' ); ?>">
                                    <?php echo re_property_social_share($i->ID); ?>
                                </div>
                            <?php endif; ?>
                            <?php
                            $is_favorites = get_user_meta($user_id, 'is_favorites', true);
                            $check_is_favorites = (!empty($is_favorites) && in_array(get_the_ID(), $is_favorites)) ? true : false;
                            $class_favorites = $check_is_favorites ? 'is_favorites' : 'add_favorites';
                            $text_favorites = $check_is_favorites ? esc_html__('View favorites', 'noo') : esc_html__('Add to favorites', 'noo');
                            $icon_favorites = $check_is_favorites ? 'fa-heart' : 'fa-heart-o';
                            ?>
                            <span>
                                <i title="<?php echo esc_html__('Compare', 'noo'); ?>" data-user="<?php echo $user_id; ?>"
                                   data-id="<?php echo $i->ID; ?>" data-action="compare"
                                   data-thumbnail="<?php echo get_the_post_thumbnail_url($i->ID,'property-floor'); ?>"
                                   data-div="<?php echo esc_attr($id_compare); ?>"
                                   class="property-action-button fa fa-plus compare-<?php echo get_the_ID(); ?>"
                                   aria-hidden="true"></i>
                            </span>
                            <?php if ((re_get_agent_setting('users_can_register', true) && !is_user_logged_in()) || is_user_logged_in()) : ?>
                                <span>
                                    <i title="<?php echo esc_html($text_favorites); ?>" data-user="<?php echo $user_id; ?>"
                                       data-id="<?php echo $i->ID; ?>" data-action="favorites"
                                       data-status="<?php echo esc_attr($class_favorites); ?>"
                                       data-url="<?php echo esc_attr($property_favorites); ?>"
                                       class="property-action-button fa <?php echo esc_attr($icon_favorites); ?>"
                                       aria-hidden="true"></i>
                                </span>
                            <?php endif; ?>
                            <span>
                            <i title="<?php echo esc_html__('Share', 'noo'); ?>" data-user="<?php echo $user_id; ?>"
                               data-id="<?php echo $i->ID; ?>" data-action="sharing"
                               data-class="<?php echo esc_attr($uid); ?>" class="property-action-button fa fa-share-alt"
                               aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>
                    <span class="ps-style-2--price">
                            <?php echo re_get_property_price_html($i->ID, true) ?>
                        </span>
                    <a class="owl2-next <?php echo $sliderID ?>-next"></a>
                    <a class="owl2-prev <?php echo $sliderID ?>-prev"></a>
                    <div class="ps-style-2--excerpt">
                        <?php if ($excerpt = $i->post_content): ?>
                            <?php
                            $num_word = 30;
                            $excerpt = strip_shortcodes($excerpt);
                            echo '<p>' . wp_trim_words($excerpt, $num_word, '...') . '</p>';
                            ?>
                        <?php endif; ?>
                    </div><?php
                    $args = array(
                        'property_id' => $i->ID,
                        'container_class' => 'property-detail',
                    );
                    ?>
                    <div class="ps-style-2--summary">
                        <?php echo re_property_summary($args); ?>
                        <!-- <div class="size"><span><?php echo re_get_property_area_html($i->ID); ?></span></div>
                            <div class="bathrooms"><span><?php echo noo_get_post_meta($i->ID, '_bathrooms'); ?></span></div>
                            <div class="bedrooms"><span><?php echo noo_get_post_meta($i->ID, '_bedrooms'); ?></span></div>
                            <div class="property-price">
                                <span><?php echo re_get_property_price_html($i->ID); ?></span>
                            </div> -->
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script type="text/javascript">
    jQuery('document').ready(function ($) {
        let slider = $('#<?php echo $sliderID?>');
        slider.owlCarousel({
            items: 1,
            loop: false,
            rtl: <?php echo is_rtl() ? 'true' : 'false'; ?>,
            autoplay: <?php echo $auto_play ?>,
            autoplayTimeout: <?php echo esc_js($slider_time) ?>,
            autoplayHoverPause: true,
            autoplaySpeed: <?php echo $slider_speed?>,
            dots: false,
            onChanged: callBack,
        });
        $('.<?php echo $sliderID?>-next').click(function () {
            slider.trigger('next.owl.carousel');
        });
        $('.<?php echo $sliderID?>-prev').click(function () {
            slider.trigger('prev.owl.carousel');
        });

        function callBack(event) {
            if ($('.owl-item:nth-child(1)')) {
                $('.owl-item:nth-child(1) .<?php echo esc_js($sliderID) ?>-prev').addClass('disabled');
            }
            if ($('.owl-item:last-child')) {
                $('.owl-item:last-child .<?php echo esc_js($sliderID) ?>-next').addClass('disabled');
            }
        }
    })
</script>