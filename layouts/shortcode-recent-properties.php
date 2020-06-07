<?php
wp_enqueue_style('owlcarousel2-theme');
$property_locations = array();
$property_locations = get_terms('property_location', array('hide_empty' => 0));
$posts = get_posts();
$categories = get_categories(array('orderby' => 'NAME', 'order' => 'ASC'));

if ($style === 'grid' || $style === 'list'): ?>

    <div class="recent-properties">

        <?php $args = array(
            'query' => $q,
            'title' => $title,
            'display_mode' => $show_control,
            'default_mode' => $style,
            'show_pagination' => $show_pagination,
            'prop_style' => $prop_style,

        );
        re_property_loop($args); ?>
    </div>

<?php elseif ($style === 'slider'): ?>
    <?php if ($q->have_posts()): ?>
        <div class="recent-properties recent-properties-slider">
            <?php if (!empty($title)): ?>
                <div class="recent-properties-title"><h3><?php echo $title ?></h3></div>
            <?php endif; ?>
            <?php
            $i = 0;
            $visible = 4;
            $r = 0;
            $uniqID = uniqid();
            ?>
            <div class="recent-properties-content">
                <div>
                    <ul class="owl-carousel owl-theme" id="<?php echo esc_html($uniqID) ?>">
                        <?php while ($q->have_posts()): $q->the_post();
                            global $post; ?>
                            <?php if ($r++ % $visible == 0): ?>
                                <li>
                            <?php endif; ?>
                            <?php if ($i++ % 2 == 0): ?>
                            <div class="property-row">
                        <?php
                        endif; ?>
                            <?php if ($prop_style === 'style-1') {
                                include(locate_template('/framework/property/property-horizontal-style-1.php'));
                            } ?>
                            <?php if ($i % 2 == 0 || $i == $q->post_count): ?>
                            </div>
                        <?php endif; ?>
                            <?php if ($r % $visible == 0 || $r == $q->post_count): ?>
                                </li>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <a class="owl2-prev" id="<?php echo $uniqID; ?>-prev"></a>
                <a class="owl2-next" id="<?php echo $uniqID; ?>-next"></a>
            </div>
        </div>
        <script type="text/javascript">
            jQuery('document').ready(function ($) {
                $("#<?php echo $uniqID;?>").owlCarousel({
                    items: 1,
                    loop: true,
                    rtl: <?php echo is_rtl() ? 'true' : 'false'; ?>,
                    autoplay: <?php echo esc_js($autoplay)?>,
                    autoplayTimeout: <?php echo esc_js($slider_time)?>,
                    autoplayHoverPause: true,
                    autoplaySpeed: <?php echo esc_js($slider_speed)?>,
                    lazyLoad: true,
                    dots: false,
                })
                $('#<?php echo $uniqID; ?>-prev').click(function () {
                    $("#<?php echo $uniqID;?>").trigger('prev.owl.carousel');
                })
                $('#<?php echo $uniqID; ?>-next').click(function () {
                    $("#<?php echo $uniqID;?>").trigger('next.owl.carousel');
                })
            })

        </script>
    <?php endif; ?>
    <?php
    wp_reset_postdata();
    wp_reset_query();

elseif ($style === 'featured'): ?>
    <?php $featuredID = 'featured_' . uniqid(); ?>
    <?php if ($q->have_posts()): ?>
        <div class="recent-properties recent-properties-featured">
            <?php if (!empty($title)): ?>
                <div class="recent-properties-title">
                    <h3><?php echo $title ?></h3>
                </div>
            <?php endif; ?>
            <?php if (!empty($description)): ?>
                <div class="recent-properties-description"><p><?php echo $description ?></p></div>
            <?php endif; ?>
            <div class="recent-properties-content">
                <?php
                $compare_listings   = noo_get_page_link_by_template( 'compare-listings.php' );
                $property_favorites = noo_get_page_link_by_template( 'property_favorites.php' );
                $social_enabled     = get_theme_mod("noo_property_social", true );
                $id_compare = uniqid( 'submit-compare-' );
                $current_user = wp_get_current_user();
                $user_id      = $current_user->ID;
                ?>
                <?php if ( !empty( $compare_listings ) ) : ?>
                    <div class="noo-property-compare">
                        <form style="display: none" method="POST" action="<?php echo esc_url( $compare_listings ); ?>" class="<?php echo $id_compare ?>">
                            <h3 class="title-compare">
                                <?php echo esc_html__( 'Compare properties', 'noo' ); ?>
                            </h3>
                            <div class="list-compare"></div>
                            <input type="submit" name="submit-compare" value="<?php echo esc_html__( 'Compare', 'noo' ); ?>" />
                        </form>
                    </div><!-- /.noo-property-compare -->
                <?php endif; ?>
                <ul id="<?php echo $featuredID ?>" class="owl-carousel owl-theme">
                    <?php while ($q->have_posts()): $q->the_post();
                        global $post; ?>
                        <li>
                            <?php
                                if ($prop_style === 'style-1') { $item_per_slide = 1; include(locate_template('/framework/property/property-style-1.php')); }
                                else if ($prop_style === 'style-2') { include(locate_template('/framework/property/property-style-2.php')); }
                            ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
                <a class="owl2-prev rp-prev" id="<?php echo $featuredID;?>-prev"></a>
                <a class="owl2-next rp-next" id="<?php echo $featuredID;?>-next"></a>
            </div>
        </div>
        <script type="text/javascript">
            jQuery('document').ready(function ($) {
                let autoPlay = '<?php echo esc_js($autoplay) ?>';
                if (autoPlay == '' || autoPlay == 'false') {
                    autoPlay = false;
                } else if (autoPlay == 1 || autoPlay == 'true') {
                    autoPlay = true;
                }
                $("#<?php echo $featuredID ?>").owlCarousel({
                    items: <?php echo $item_per_slide?>,
                    autoplay: autoPlay,
                    rtl: <?php echo is_rtl() ? 'true' : 'false'; ?>,
                    autoplayTimeout: <?php echo esc_js($slider_time)?>,
                    autoplayHoverPause: true,
                    autoplaySpeed: <?php echo esc_js($slider_speed)?>,
                    lazyLoad: true,
                    dots: true,
                    responsiveClass: true,
                    loop: true,
                    margin: 30,
                    responsive: {
                        0: {
                            items: 1,
                        },
                        600: {
                            items: 2,
                        },
                        1000: {
                            items: <?php echo $item_per_slide?>,
                        }
                    }
                })
                $('#<?php echo $featuredID;?>-next').click(function () {
                    $("#<?php echo esc_js($featuredID) ?>").trigger('next.owl.carousel');
                })
                $('#<?php echo $featuredID;?>-prev').click(function () {
                    $("#<?php echo esc_js($featuredID) ?>").trigger('prev.owl.carousel');
                })
            })
        </script>
    <?php endif; ?>

    <?php
    wp_reset_postdata();
    wp_reset_query();


endif; ?>
