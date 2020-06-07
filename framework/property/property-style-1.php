<?php
/**
 * Created by PhpStorm.
 * User: Vietbrain-09
 * Date: 14-Jul-18
 * Time: 10:33 AM
 */
?>
<?php 
$defaultIMG = get_template_directory_uri().'/assets/images/placeholder.jpg'; 
$img =get_the_post_thumbnail(get_the_ID(), 'property-image');
?>
<article <?php post_class(); ?>>
    <div class="property-featured">
        <a class="content-thumb" href="<?php the_permalink() ?>">
            <?php 
                if ($img != null) {
                    echo $img;
                } if ($img == null) {
                    echo '<img src="'.$defaultIMG.'">';
                }
            ?>
        </a>
        <?php
        $_label = noo_get_post_meta(null, '_label');
        if (!empty($_label) && ($property_label = get_term($_label, 'property_label'))):
            $noo_property_label_colors = get_option('noo_property_label_colors');
            $color = isset($noo_property_label_colors[$property_label->term_id]) ? $noo_property_label_colors[$property_label->term_id] : '';
            ?>
            <span class="property-label" <?php echo(!empty($color) ? ' style="background-color:' . $color . '"' : '') ?>><?php echo $property_label->name ?></span>
        <?php endif; ?>
        <?php echo get_the_term_list(get_the_ID(), 'property_category', '<span class="property-category">', ', ', '</span>') ?>
    </div>
    <div class="property-wrap">
        <h2 class="property-title">
            <a href="<?php the_permalink(); ?>"
               title="<?php the_title(); ?>"><?php the_title(); ?></a>
        </h2>
        <div class="property-excerpt">
            <?php if ($excerpt = get_the_excerpt()): ?>
                <?php
                $num_word = 30;
                $excerpt = strip_shortcodes($excerpt);
                echo '<p>' . wp_trim_words($excerpt, $num_word, '...') . '</p>';
                ?>
            <?php endif; ?>
        </div>
        <div class="property-summary">
            <?php echo re_property_summary(); ?>
            <div class="property-info">
                <div class="property-price">
                    <span><?php echo re_get_property_price_html(get_the_ID(), true) ?></span>
                </div>
                <div class="property-action">
                    <a href="<?php the_permalink() ?>"><?php echo __('More Details', 'noo') ?>
                        <i class="fa fa-arrow-circle-o-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</article>
