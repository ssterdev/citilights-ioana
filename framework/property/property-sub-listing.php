<?php
/**
 * Field: Sub Properties
 */
function sub_listing_property_detail($property_id){
    $sub_listing = get_post_meta( get_the_ID(), 'sub_listing', true );
    if ( ! empty( $sub_listing ) && is_array( $sub_listing)) :
    $sub_listing = array_values( $sub_listing );
    unset($sub_listing[0]);
    if (! empty($sub_listing[1])) :
    
    $title      = array_key_exists( 'title_sub_listing', $sub_listing[1] ) ? $sub_listing[1][ 'title_sub_listing' ] : '';
    $bedroom   = array_key_exists( 'bedroom_sub_listing', $sub_listing[1] ) ? $sub_listing[1][ 'bedroom_sub_listing' ] : '';
    $bathroom  = array_key_exists( 'bathroom_sub_listing', $sub_listing[1] ) ? $sub_listing[1][ 'bathroom_sub_listing' ] : '';
    $size    = array_key_exists( 'size_sub_listing', $sub_listing[1] ) ? $sub_listing[1][ 'size_sub_listing' ] : '';
    $price = array_key_exists( 'price_sub_listing', $sub_listing[1] ) ? $sub_listing[1][ 'price_sub_listing' ] : '';
    $type   = array_key_exists( 'type_sub_listing', $sub_listing[1] ) ? $sub_listing[1][ 'type_sub_listing' ] : '';
    $available     = array_key_exists( 'available_sub_listing', $sub_listing[1] ) ? $sub_listing[1][ 'available_sub_listing' ] : '';
    if (!empty($title) || !empty($bedroom) || !empty($bathroom) || !empty($size) || !empty($price) || !empty($type) || !empty($available)):
    ?>
    <div class="property-sub-listing">
        <div class="property-sub-listing-title">
            <h4 class="re-title-box">
                <?php echo esc_html__( 'Sub Listing', 'noo' ); ?>
            </h4>
        </div>

        <div class="property-sub-listing-details">
            <?php if ( ! empty( $sub_listing ) && is_array( $sub_listing ) ) : ?>
            <?php
            foreach ( $sub_listing as $item ) :
            $title_sub_listing       = array_key_exists( 'title_sub_listing', $item ) ? $item[ 'title_sub_listing' ] : '';
            $bedroom_sub_listing    = array_key_exists( 'bedroom_sub_listing', $item ) ? $item[ 'bedroom_sub_listing' ] : '';
            $bathroom_sub_listing   = array_key_exists( 'bathroom_sub_listing', $item ) ? $item[ 'bathroom_sub_listing' ] : '';
            $size_sub_listing       = array_key_exists( 'size_sub_listing', $item ) ? $item[ 'size_sub_listing' ] : '';
            $price_sub_listing = array_key_exists( 'price_sub_listing', $item ) ? $item[ 'price_sub_listing' ] : '';
            $type_sub_listing       = array_key_exists( 'type_sub_listing', $item ) ? $item[ 'type_sub_listing' ] : '';
            $available_sub_listing       = array_key_exists( 'available_sub_listing', $item ) ? $item[ 'available_sub_listing' ] : '';
            ?>
            <div class="accord-block">
                <div class="accord-tab">
                    <h3><?php echo esc_html( $title_sub_listing ); ?></h3>
                    <span class="expand-icon"></span>
                </div>
                <div class="accord-content">
                    <div class="row">
                        <div class="col-md-6 left">
                            <div class="">
                                <span><?php echo esc_html('Type :','noo'); ?></span>
                                <?php echo esc_html( $type_sub_listing ) ?>
                            </div>
                            <div class="">
                                <span><?php echo esc_html('Price :','noo'); ?></span>
                                <?php echo esc_html( $price_sub_listing ) ?>
                            </div>
                            <div class="">
                                <span><?php echo esc_html('Bedrooms :','noo'); ?></span>
                                <?php echo esc_html( $bedroom_sub_listing ) ?>
                            </div>
                        </div>
                        <div class="col-md-6 right">
                            <div class="">
                                <span><?php echo esc_html('Bathrooms :','noo'); ?></span>
                                <?php echo esc_html( $bathroom_sub_listing ) ?>
                            </div>
                            <div class="">
                                <span><?php echo esc_html('Size :','noo'); ?></span>
                                <?php echo esc_html( $size_sub_listing ) ?>
                            </div>
                            <div class="">
                                <span><?php echo esc_html('Available From :','noo'); ?></span>
                                <?php echo esc_html( $available_sub_listing ) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
    <script>
        
    jQuery(document).ready(function($){
            $(".accord-tab:first").addClass("active");
            $(".accord-content:not(:first)").hide();
            $('.accord-tab').click(function() {
                $('.accord-tab').not(this).removeClass('active');
                $(this).toggleClass('active');

                $('.accord-tab').not(this).next('.accord-content').slideUp();
                $(this).next('.accord-content').slideToggle();
            });

    });
    </script>
    <?php

}


add_action('sub_listing_property_detail', 100,1);



if ( ! function_exists( 'rp_save_sub_listing' ) ) :

    function rp_save_sub_listing( $property_id ) {

        if ( isset( $_POST[ 'sub_listing' ] ) && is_array( $_POST[ 'sub_listing' ] ) ) {
            update_post_meta( $property_id, 'sub_listing', array_values( $_POST[ 'sub_listing' ] ) );
        }
    }

    add_action( 'save_post', 'rp_save_sub_listing' );

endif;