<?php

function additional_feature($property_id)
{   
    $current_data   = get_post_meta( $property_id, 'additional_features', true );
    if ( ! empty( $current_data ) && is_array( $current_data)) :
    $current_data = array_values( $current_data );
    unset($current_data[0]);
        if ( !empty($current_data[1])):
            $label = array_key_exists( 'additional_feature_label', $current_data[1] ) ? $current_data[1][ 'additional_feature_label' ] : '';
            $value = array_key_exists( 'additional_feature_value', $current_data[1]  ) ? $current_data[1] [ 'additional_feature_value' ] : '';
            if( !empty($label) || !empty($value) ):
            ?>
            <div>
                <div>
                    <h4 class="property-information-title"><?php echo esc_html__( 'Additional Features', 'noo' ); ?></h4>
                </div>
                <div class="property-information-content row">
                    <?php 
                        if (!empty($current_data) && is_array($current_data)) :
                            
                            foreach ($current_data as $item) :
                                $additional_feature_label       = array_key_exists( 'additional_feature_label', $item ) ? $item[ 'additional_feature_label' ] : '';
                                $additional_feature_value    = array_key_exists( 'additional_feature_value', $item ) ? $item[ 'additional_feature_value' ] : '';
                                ?>
                                <div class="col-md-4 col-xs-6">
                                    <label style="text-transform: capitalize;"><?php echo $additional_feature_label; ?> &nbsp</label>
                                    <span >&nbsp<?php echo $additional_feature_value; ?></span>
                                </div>
                                <?php
                            endforeach;
                        endif;
                     ?>
                </div>
            </div>
            <?php
        endif;
    endif;
endif;
}