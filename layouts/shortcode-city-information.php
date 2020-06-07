<?php
/**
 * Created by PhpStorm.
 * User: Vietbrain-09
 * Date: 12-Jul-18
 * Time: 2:19 PM
 */
//wp_enqueue_script('masonry');
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
wp_enqueue_script('isotope');
$cityInfoID = 'city_' . uniqid();
if ( is_plugin_active('js_composer/include/params/param_group/param_group.php') || class_exists('Vc_ParamGroup')) :
$items = (array)vc_param_group_parse_atts($items);

if (!function_exists('getLocation')) {
    function getLocation($id)
    {
        $location = get_term($id, 'property_location');
        return $location->name;
    }
};
if (!function_exists('getPropAmount')) {
    function getPropAmount($id)
    {
        $location = get_term($id, 'property_location');
        return $location->count;
    }
}
if (!function_exists('getSlug')) {

    function getSlug($id)
    {
        $location = get_term($id, 'property_location');
        return $location->slug;
    }
}
//console_log($locationInfo);
$url = site_url() . '/property-location/';
?>
<?php ?>

<?php if ($style === 'style-1'): ?>
    <div>
        <div id="<?php echo $cityInfoID; ?>" <?php echo($class . ' ' . $custom_style); ?>>
            <div class="gallery-row city-info-row">
                <?php if (sizeof($items) === 6): ?>
                    <ul class="masonry-grid">
                        <?php $k = array();
                        $j = 0;
                        foreach ($items as $i):
                            $k[] = 2 * $j + 1;
                            ?>
                            <li class="city-item <?php
                            if (sizeof($items) === 6) {
                                echo esc_attr('col-md-4 ');
                                if (in_array($j + 1, array('1', '4', '7', '10', '13'))) echo esc_attr('col-md-6 city-item-h-50 ');
                            }
                            if (sizeof($items) <= 4) {
                                echo esc_attr('city-item-30 ');
                                if (in_array($j + 1, array('1'))) echo esc_attr('city-item-h-60');
                                if (in_array($j + 1, array('2'))) echo esc_attr('city-item-w-70');
                            }
                            if (sizeof($items) >= 5 && sizeof($items) !== 6) {
                                if (in_array($j + 1, array('1'))) echo esc_attr('city-item-w-100');
                                if (in_array($j + 1, array('2'))) echo esc_attr('city-item-w-75');
                                if (in_array($j + 1, array('4', '5'))) echo esc_attr('city-item-w-50');
                            }

                            ?>">
                                <?php if (isset($i['thumbnail'])) : ?>
                                    <?php $image_src = wp_get_attachment_url($i['thumbnail']); ?>
                                    <img src="<?php echo esc_url($image_src); ?>"
                                         alt="<?php echo getLocation($i['location_id']); ?>">
                                <?php else : echo esc_html__('Please select image!', 'noo'); ?>
                                <?php endif; ?>
                                <div>
                                    <div class="city-hover">
                                        <div id="city-info">
                                            <h2><?php echo getLocation($i['location_id']) ?></h2>
                                            <p><?php echo getPropAmount($i['location_id']) . ' property' ?></p>
                                            <a href="<?php echo esc_html($url . getSlug($i['location_id'])) ?>">
                                                <small>View All <i class="fa fa-arrow-circle-right"></i></small>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php $j++;endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php if (sizeof($items) == 3): ?>
                    <ul class="col-md-12 grid-6">
                        <?php for ($i = 0; $i < sizeof($items); $i++): ?>
                            <li class="col-md-6 city-item">
                                <?php if (isset($items[$i]['thumbnail'])) : ?>
                                    <?php $image_src = wp_get_attachment_url($items[$i]['thumbnail']); ?>
                                    <img src="<?php echo esc_url($image_src); ?>"
                                         alt="<?php echo getLocation($items[$i]['location_id']); ?>">
                                <?php else : echo esc_html__('Please select image!', 'noo'); ?>
                                <?php endif; ?>
                                <div class="city-hover">
                                    <div>
                                        <h2><?php echo getLocation($items[$i]['location_id']) ?></h2>
                                        <p><?php echo getPropAmount($items[$i]['location_id']) . ' property' ?></p>
                                        <a href="<?php echo esc_html($url . getSlug($items[$i]['location_id'])) ?>">
                                            <small>View All <i class="fa fa-arrow-circle-right"></i></small>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        <?php endfor; ?>
                    </ul>
                <?php endif; ?>
                <?php if (sizeof($items) !== 3 && sizeof($items) !== 6): ?>
                    <ul class="col-md-12 layout-flex ">
                        <?php for ($i = 0; $i < sizeof($items); $i++): ?>
                            <li class="city-item">
                                <?php if (isset($items[$i]['thumbnail'])) : ?>
                                    <?php $image_src = wp_get_attachment_url($items[$i]['thumbnail']); ?>
                                    <img src="<?php echo esc_url($image_src); ?>"
                                         alt="<?php echo getLocation($items[$i]['location_id']); ?>">
                                <?php else : echo esc_html__('Please select image!', 'noo'); ?>
                                <?php endif; ?>
                                <div class="city-hover">
                                    <div>
                                        <h2><?php echo getLocation($items[$i]['location_id']) ?></h2>
                                        <p><?php echo getPropAmount($items[$i]['location_id']) . ' property' ?></p>
                                        <a href="<?php echo esc_html($url . getSlug($items[$i]['location_id'])) ?>">
                                            <small>View All <i class="fa fa-arrow-circle-right"></i></small>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        <?php endfor; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery('document').ready(function ($) {
            $(".grid-6:nth-child(1) .city-item:nth-child(1)").removeClass("col-md-6");
            $(".grid-6:nth-child(1) .city-item:nth-child(1)").addClass("col-md-12");

            $(".grid-6:nth-child(2) .city-item:nth-child(3)").removeClass("col-md-6");
            $(".grid-6:nth-child(2) .city-item:nth-child(3)").addClass("col-md-12");
            $(".layout-flex .city-item:first-child").addClass("item-width-25 item-height-100");
            $(".layout-flex .city-item:nth-child(2)").addClass("item-width-75");

            var grid_masonry = $('.masonry-grid');
            if (grid_masonry.length > 0) {
                grid_masonry.imagesLoaded(function () {
                    grid_masonry.isotope({
                        itemSelector: '.city-item',
                        layoutMode: 'masonry'
                    });
                    setTimeout(function () {
                        grid_masonry.isotope('layout');
                    }, 500);
                });
            }
        });
    </script>
<?php endif; ?>

<?php if ($style === 'style-2'): ?>
    <div>
        <div id="<?php echo $cityInfoID; ?>" <?php echo($class . ' ' . $custom_style); ?>>
            <div class="gallery-row city-info-row city-style-2">
                <?php if (sizeof($items) === 6): ?>
                    <ul class="col-md-6 grid-6">
                        <?php for ($i = 0; $i < sizeof($items) / 2; $i++): ?>
                            <li class="col-md-6 city-item">
                                <?php if (isset($items[$i]['thumbnail'])) : ?>
                                    <?php $image_src = wp_get_attachment_url($items[$i]['thumbnail']); ?>
                                    <img src="<?php echo esc_url($image_src); ?>"
                                         alt="<?php echo getLocation($items[$i]['location_id']); ?>">
                                <?php else : echo esc_html__('Please select image!', 'noo'); ?>
                                <?php endif; ?>
                                <div class="city-hover hover-style-2">
                                    <div id="city-info">
                                        <!--                                        <canvas id="hoverBorder"></canvas>-->
                                        <h2><?php echo getLocation($items[$i]['location_id']) ?></h2>
                                        <p><?php echo getPropAmount($items[$i]['location_id']) . ' property' ?></p>
                                        <a href="<?php echo esc_html($url . getSlug($items[$i]['location_id'])) ?>">
                                            <small>View All <i class="fa fa-arrow-circle-right"></i></small>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        <?php endfor; ?>
                    </ul>
                    <ul class="col-md-6 grid-6">
                        <?php for ($i = 3; $i < sizeof($items); $i++): ?>
                            <li class="col-md-6 city-item">
                                <?php if (isset($items[$i]['thumbnail'])) : ?>
                                    <?php $image_src = wp_get_attachment_url($items[$i]['thumbnail']); ?>
                                    <img src="<?php echo esc_url($image_src); ?>"
                                         alt="<?php echo getLocation($items[$i]['location_id']); ?>">
                                <?php else : echo esc_html__('Please select image!', 'noo'); ?>
                                <?php endif; ?>
                                <div class="city-hover">
                                    <div>
                                        <h2><?php echo getLocation($items[$i]['location_id']) ?></h2>
                                        <p><?php echo getPropAmount($items[$i]['location_id']) . ' property' ?></p>
                                        <a href="<?php echo esc_html($url . getSlug($items[$i]['location_id'])) ?>">
                                            <small>View All <i class="fa fa-arrow-circle-right"></i></small>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        <?php endfor; ?>
                    </ul>
                <?php endif; ?>
                <?php if (sizeof($items) !== 6): ?>
                    <ul class="col-md-12 layout-flex ">
                        <?php for ($i = 0; $i < sizeof($items); $i++): ?>
                            <li class="city-item">
                                <?php if (isset($items[$i]['thumbnail'])) : ?>
                                    <?php $image_src = wp_get_attachment_url($items[$i]['thumbnail']); ?>
                                    <img src="<?php echo esc_url($image_src); ?>"
                                         alt="<?php echo getLocation($items[$i]['location_id']); ?>">
                                <?php else : echo esc_html__('Please select image!', 'noo'); ?>
                                <?php endif; ?>
                                <div class="city-hover hover-style-2">
                                    <div>
                                        <h2><?php echo getLocation($items[$i]['location_id']) ?></h2>
                                        <p><?php echo getPropAmount($items[$i]['location_id']) . ' property' ?></p>
                                        <a href="<?php echo esc_html($url . getSlug($items[$i]['location_id'])) ?>">
                                            <small>View All <i class="fa fa-arrow-circle-right"></i></small>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        <?php endfor; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery('document').ready(function ($) {
            $(".grid-6:nth-child(1) .city-item:nth-child(1)").removeClass("col-md-6");
            $(".grid-6:nth-child(1) .city-item:nth-child(1)").addClass("col-md-12");

            $(".grid-6:nth-child(2) .city-item:nth-child(3)").removeClass("col-md-6");
            $(".grid-6:nth-child(2) .city-item:nth-child(3)").addClass("col-md-12");
            // $(".city-item").mouseenter(function () {
            //     let citi = $("#hoverBorder");
            //     let canvas = citi[0].getContext("2d");
            //     canvas.moveTo(0,0);
            //     canvas.lineTo(100,0);
            //     canvas.stroke();
            //     canvas.fillStyle = "#2aba66";
            // });

        });
    </script>
<?php endif; ?>
<?php if ($style === 'masonry'): ?>
    <div class="city-info-masonry">
        <div id="<?php echo $cityInfoID; ?>" <?php echo($class . ' ' . $custom_style); ?>>
            <div class="city-grid">
                <?php $k = array();
                $j = 0;
                foreach ($items as $i):
                    $k[] = 2 * $j + 1;
                    ?>
                    <div class="city-item <?php
                    if (sizeof($items) === 6) {
                        if (in_array($j + 1, array('1', '4', '7', '10', '13'))) echo esc_attr('city-item-w-50 city-item-h-50 ');
                    }
                    if (sizeof($items) <= 4) {
                        echo esc_attr('city-item-w-33 ');
                        if (in_array($j + 1, array('1'))) echo esc_attr('city-item-30 city-item-h-60');
                        if (in_array($j + 1, array('2'))) echo esc_attr('city-item-w-70');
                    }
                    if (sizeof($items) == 3) {
                        echo esc_attr('city-item-w-33 ');
                        if (in_array($j + 1, array('1'))) echo esc_attr('city-item-30 city-item-h-60');
                        if (in_array($j + 1, array('2'))) echo esc_attr('city-item-w-70');
                    }
                    if (sizeof($items) >= 5 && sizeof($items) !== 6) {
                        if (in_array($j + 1, array('1'))) echo esc_attr('city-item-w-100');
                        if (in_array($j + 1, array('2'))) echo esc_attr('city-item-w-75');
                        if (in_array($j + 1, array('4', '5'))) echo esc_attr('city-item-w-50');
                    }
                    ?>">
                        <?php if (isset($i['thumbnail'])) : ?>
                            <?php $image_src = wp_get_attachment_url($i['thumbnail']); ?>
                            <img src="<?php echo esc_url($image_src); ?>"
                                 alt="<?php echo getLocation($i['location_id']); ?>">
                        <?php else : echo esc_html__('Please select image!', 'noo'); ?>
                        <?php endif; ?>
                        <div class="city-hover">
                            <div id="city-info">
                                <h2><?php echo getLocation($i['location_id']) ?></h2>
                                <p><?php echo getPropAmount($i['location_id']);_e(' property', 'noo') ?></p>
                                <a href="<?php echo esc_html($url . getSlug($i['location_id'])) ?>">
                                    <small><?php _e('View All ', 'noo'); ?><i class="fa fa-arrow-circle-right"></i></small>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php $j++;endforeach; ?>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery('docuement').ready(function ($) {
            $('.city-item').mouseleave(function () {
                $('.city-item .city-hover').addClass('animated zoomOut')
            })
            let size = <?php echo esc_js(sizeof($items)) ?>;
            if (size >= 2) {
                if (size % 2 !== 0) {
                    $(window).resize(function () {
                        let windowSize = $(window).width();
                        if (windowSize < 1200) {
                            console.log(windowSize);
                            $('#<?php echo $cityInfoID?> .city-grid .city-item:last-of-type').addClass('city-item-100');
                        }
                        if (windowSize > 1200) {
                            $('.city-item:last-of-type').removeClass('city-item-100');
                            console.log('removed')
                        }
                    })
                }
            }
            let grid = $('#<?php echo $cityInfoID?> .city-grid');
            grid.isotope({
                layoutItems: '#<?php echo $cityInfoID?> .city-item',
                //layoutMode: 'masonry',
                masonry: {
                    columnWidth: 1,
                }
            });

        });
    </script>

<?php endif; ?>
<?php endif; ?>