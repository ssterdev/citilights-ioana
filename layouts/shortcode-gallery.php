<?php
/**
 * Created by PhpStorm.
 * User: Vietbrain-09
 * Date: 12-Jul-18
 * Time: 2:19 PM
 */
$galleryID = 'gallery' . uniqid();
$items = (array)vc_param_group_parse_atts($items);
$count = 0;
?>
<?php if ($layout === 'layout-1'): ?>
    <div>
        <div id="<?php echo $galleryID; ?>" <?php echo($class . ' ' . $custom_style); ?>>
            <div class="gallery-row">
                <ul class="col-md-6">
                    <li class="col-md-12">
                        <?php if (isset($items[0]['img'])) : ?>
                            <?php $image_src = wp_get_attachment_url($items[0]['img']); ?>
                            <img src="<?php echo esc_url($image_src); ?>" alt="<?php echo $items[0]['name']; ?>">
                        <?php else : echo esc_html__('Please select image!', 'noo'); ?>
                        <?php endif; ?>
                        <div class="img-border">
                            <p class="view-all">
                                View All
                            </p>
                        </div>
                    </li>
                    <li class="col-md-6">
                        <?php if (isset($items[1]['img'])) : ?>
                            <?php $image_src = wp_get_attachment_url($items[1]['img']); ?>
                            <img src="<?php echo esc_url($image_src); ?>" alt="<?php echo $items[1]['name']; ?>">
                        <?php else : echo esc_html__('Please select image!', 'noo'); ?>
                        <?php endif; ?>
                        <div class="img-border"></div>
                    </li>
                    <li class="col-md-6">
                        <?php if (isset($items[2]['img'])) : ?>
                            <?php $image_src = wp_get_attachment_url($items[2]['img']); ?>
                            <img src="<?php echo esc_url($image_src); ?>" alt="<?php echo $items[2]['name']; ?>">
                        <?php else : echo esc_html__('Please select image!', 'noo'); ?>
                        <?php endif; ?>
                        <div class="img-border"></div>
                    </li>
                </ul>
                <ul class="col-md-6">
                    <li class="col-md-6">
                        <?php if (isset($items[3]['img'])) : ?>
                            <?php $image_src = wp_get_attachment_url($items[3]['img']); ?>
                            <img src="<?php echo esc_url($image_src); ?>" alt="<?php echo $items[3]['name']; ?>">
                        <?php else : echo esc_html__('Please select image!', 'noo'); ?>
                        <?php endif; ?>
                        <div class="img-border"></div>
                    </li>
                    <li class="col-md-6">
                        <?php if (isset($items[4]['img'])) : ?>
                            <?php $image_src = wp_get_attachment_url($items[4]['img']); ?>
                            <img src="<?php echo esc_url($image_src); ?>" alt="<?php echo $items[4]['name']; ?>">
                        <?php else : echo esc_html__('Please select image!', 'noo'); ?>
                        <?php endif; ?>
                        <div class="img-border"></div>
                    </li>
                    <li class="col-md-12">
                        <?php if (isset($items[5]['img'])) : ?>
                            <?php $image_src = wp_get_attachment_url($items[5]['img']); ?>
                            <img src="<?php echo esc_url($image_src); ?>" alt="<?php echo $items[5]['name']; ?>">
                        <?php else : echo esc_html__('Please select image!', 'noo'); ?>
                        <?php endif; ?>
                        <div class="img-border"></div>
                    </li>

                </ul>

            </div>
        </div>
    </div>

<?php endif; ?>
