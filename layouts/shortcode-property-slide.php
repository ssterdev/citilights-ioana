
<li class="slide-item noo-property-slide">
    <?php if ($background_type == 'image' && !empty($image)) :
        $thumbnail = wp_get_attachment_url( $image );
        echo '<img class="slide-image" src="' . esc_attr( $thumbnail ) .'">';
    else :
        echo get_the_post_thumbnail($property->ID,'full');
        ?>
    <?php endif; ?>
    <div class="slide-caption">
        <div class="slide-caption-info">
            <h3><a href="<?php echo esc_url(get_permalink($property->ID)); ?>"><?php echo get_the_title($property->ID); ?></a>
                <?php if($address = noo_get_post_meta($property->ID,'_address')) : ?>
                    <small><?php echo esc_html($address); ?></small>
                <?php endif; ?>
            </h3>
            <?php
            $args = array(
                'property_id' => $property->ID,
                'container_class' => '',
                'fields' => isset( $fields ) ? $fields : array(),
                'field_icons' => isset( $field_icons ) ? $field_icons : array(),
            );
            ?>
            <div class="info-summary">
                <?php echo re_property_summary( $args ); ?>
                <!-- <div class="size"><span><?php echo re_get_property_area_html($property->ID); ?></span></div>
				<div class="bathrooms"><span><?php echo noo_get_post_meta($property->ID,'_bathrooms'); ?></span></div>
				<div class="bedrooms"><span><?php echo noo_get_post_meta($property->ID,'_bedrooms'); ?></span></div>
				<div class="property-price">
					<span><?php echo re_get_property_price_html($property->ID); ?></span>
				</div> -->
            </div>
        </div>
        <div class="slide-caption-action">
            <a href="<?php echo esc_url(get_permalink($property->ID)); ?>"><?php echo __('More Details','noo'); ?></a>
        </div>
    </div>
</li>

