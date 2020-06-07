<?php

if( !function_exists( 're_property_metabox' ) ) :
	function re_property_metabox(){
		$helper = new NOO_Meta_Boxes_Helper( '', array( 'page' => 'noo_job' ) );

		$property_labels = array();
		$property_labels[] = array('value'=>'','label'=>__('Select a label','noo'));
		$property_labe_terms = (array) get_terms('property_label',array('hide_empty'=>0));

		foreach ($property_labe_terms as $label){
			$property_labels[] = array('value'=>$label->term_id,'label'=>$label->name);
		}
		$meta_box = array(
				'id' => "property_detail",
				'title' => __('General Information', 'noo') ,
				'page' => 'noo_property',
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array(
						array(
							'id'=>'_label',
							'label'=>__('Property Label','noo'),
							'type'=>'select',
							'options'=>$property_labels
						),
						array(
								'id' => '_address',
								'label' => __('Address','noo'),
								'type' => 'text',
						),
						array(
								'id' => '_price',
								'label' => __('Price','noo') . ' (' . re_get_currency_symbol(re_get_property_setting('currency')) . ')',
								'type' => 'text',
						),
						array(
								'id' => '_before_price_label',
								'label' => __('Before Price Label','noo'),
								'type' => 'text',
						),
						array(
								'id' => '_price_label',
								'label' => __('After Price Label','noo'),
								'type' => 'text',
						),
				)
		);
		
		$helper->add_meta_box($meta_box);
		
		// New custom fields
		$meta_box = array(
				'id' => "additional_information",
				'title' => __('Additional Information', 'noo') ,
				'page' => 'noo_property',
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array()
		);

		$fields = re_get_property_custom_fields();
		if($fields){
			foreach ($fields as $field){
				if( !isset( $field['name'] ) || empty( $field['name'] ) ) continue;
				$field['type'] = !isset( $field['type'] ) || empty( $field['type'] ) ? 'text' : $field['type'];
				if( isset($field['is_default']) ) {
					if( isset( $field['is_disabled'] ) && ($field['is_disabled'] == 'yes') )
						continue;
					if( isset( $field['is_tax'] ) )
						continue;
					$id = $field['name'];
				} else {
					$id = re_property_custom_fields_name($field['name']);
				}

				$type = $field['type'];
				if( $field['type'] == 'multiple_select' ) {
					$type = 'select';
					$field['multiple'] = true;
				}

				if( $field['type'] == 'number' ) {
					$type = 'text';
				}

				if( in_array( $field['type'], array( 'multiple_select', 'select', 'checkbox', 'radio', '') ) ) {
					$field['options'] = array();
					$field_value = noo_convert_custom_field_setting_value( $field );
					foreach ($field_value as $key => $label) {
						$field['options'][] = array(
							'label' => $label,
							'value' => $key
							);
					}

					if( $field['type'] == 'checkbox' ) {
						$type = 'multiple_checkbox';
					}

				}

				$new_field = array(
					'label'   => isset( $field['label_translated'] ) ? $field['label_translated'] : @$field['label'] ,
					'id'      => $id,
					'type'    => $type,
					'options' => isset( $field['options'] ) ? $field['options'] : '',
					'std'     => isset( $field['std'] ) ? $field['std'] : '',
				);

				if( isset( $field['multiple'] ) && $field['multiple'] ) {
					$new_field['multiple'] = true;
				}
				
				if( $field['type'] == 'select' ) {
					$new_field['option_none'] = true;
				}

				$meta_box['fields'][] = $new_field;
			}
		}

		$helper->add_meta_box($meta_box);

		// // Custom fields
		// $custom_fields = re_get_property_cf_setting('custom_field');
		// $property_detail_fields = array();
		// if($custom_fields){
		// 	foreach ($custom_fields as $custom_field){
		// 		$id = '_noo_property_field_'.sanitize_title(@$custom_field['name']);
		// 		$property_detail_fields[] = array(
		// 			'label' => isset( $custom_field['label_translated'] ) ? $custom_field['label_translated'] : @$custom_field['label'] ,
		// 			'id' => $id,
		// 			'type' => 'text',
		// 		);
		// 	}
			

		// 	$meta_box = array(
		// 			'id' => "property_custom",
		// 			'title' => __('Property Custom', 'noo') ,
		// 			'page' => 'noo_property',
		// 			'context' => 'normal',
		// 			'priority' => 'high',
		// 			'fields' => $property_detail_fields
		// 	);
				
		// 	$helper->add_meta_box($meta_box);
		// }
		
		// Features
		$features = re_get_property_feature_fields();
		$property_feature_fields = array();
		if($features){
			foreach ($features as $key => $feature){
					
				$property_feature_fields[] = array(
						'label' => $feature,
						'id' => '_noo_property_feature_' . $key,
						'type' => 'checkbox',
				);
			}
		}
		if( !empty( $property_feature_fields ) ) {
			$meta_box = array(
					'id' => "property_feature",
					'title' => __('Property Features', 'noo') ,
					'page' => 'noo_property',
					'context' => 'normal',
					'priority' => 'high',
					'fields' => $property_feature_fields
			);

			$helper->add_meta_box($meta_box);
		}
		$map_type = re_get_property_map_setting('map_type','');
		if ($map_type == 'google') {
			$meta_box = array(
				'id' => "property_map",
				'title' => __('Place in Map', 'noo') ,
				'page' => 'noo_property',
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array(
					array(
							'id' => '_noo_property_gmap',
							'type' => 'gmap',
							'callback'=> 're_property_metabox_google_map'
					),
					array(
							'label' =>__('Latitude','noo'),
							'id' => '_noo_property_gmap_latitude',
							'type' => 'text',
							'std'=> re_get_property_map_setting('latitude','40.714398')
					),
					array(
							'label' =>__('Longitude','noo'),
							'id' => '_noo_property_gmap_longitude',
							'type' => 'text',
							'std' => re_get_property_map_setting('longitude','-74.005279')
					),
					array(
						'label' =>__('Map Zoom Level','noo'),
						'id' => '_noo_property_gmap_zoom',
						'type' => 'text',
						'std' => '16'
					),
				)
			);
		}elseif($map_type == 'bing'){
			$meta_box = array(
					'id' => "property_map",
					'title' => __('Place in Map', 'noo') ,
					'page' => 'noo_property',
					'context' => 'normal',
					'priority' => 'high',
					'fields' => array(
						array(
								'id' => '_noo_property_bmap',
								'type' => 'bmap',
								'callback'=> 're_property_metabox_bing_map'
						),
						array(
								'label' =>__('Latitude','noo'),
								'id' => '_noo_property_bmap_latitude',
								'type' => 'text',
								'std'=> re_get_property_map_setting('latitude','40.714398')
						),
						array(
								'label' =>__('Longitude','noo'),
								'id' => '_noo_property_bmap_longitude',
								'type' => 'text',
								'std' => re_get_property_map_setting('longitude','-74.005279')
						),
						array(
							'label' =>__('Map Zoom Level','noo'),
							'id' => '_noo_property_bmap_zoom',
							'type' => 'text',
							'std' => '16'
						),
				)
			);
		}
		$helper->add_meta_box($meta_box);
			
		$meta_box = array(
				'id' => "property_video",
				'title' => __('Property Video', 'noo') ,
				'page' => 'noo_property',
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array(
						array(
								'label' => __('Video Embedded', 'noo'),
								'desc' => __('Enter a Youtube, Vimeo, Soundcloud, etc... URL. See supported services at <a href="http://codex.wordpress.org/Embeds">http://codex.wordpress.org/Embeds</a>.', 'noo'),
								'id' => '_video_embedded',
								'type' => 'text',
						),
				),
		);
		$helper->add_meta_box($meta_box);
		
		$meta_box = array(
				'id' => "property_gallery",
				'title' => __('Gallery', 'noo') ,
				'page' => 'noo_property',
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array(
						array(
								'label' =>__('Gallery','noo'),
								'id' => '_gallery',
								'type' => 'gallery',
						),
				),
		);

		$helper->add_meta_box($meta_box);

		/**
		 * Create metabox: floor_plan
		 * @var array
		 */
		if( re_get_property_setting('floor_plan', 'admin') != 'none' ) {
			$meta_box = array(
				'id'       => 'floor_plan',
				'title'    => __( 'Floor Plan', 'noo' ),
				'page'     => 'noo_property',
				'context'  => 'normal',
				'priority' => 'default',
				'fields'   => array(
					array(
						'label' => __( 'Floor Plan','noo'),
						'id'    => '_floor_plan',
						'type'  => 'gallery',
					),
				),
			);
		}

		$helper->add_meta_box($meta_box);
				
		$meta_box = array(
			'id' => 'agent_responsible',
			'title' => __('Agent Responsible', 'noo'),
			'page' => 'noo_property',
			'context' => 'side',
			'priority' => 'default',
			'fields' => array(
				array(
					'label' => __('Agent Responsible', 'noo'),
					'id'    => '_agent_responsible',
					'type'  => 'agents',
					'callback' => 're_render_agent_metabox_fields'
				)
			)
		);
		
		$helper->add_meta_box($meta_box);
			
		$meta_box = array( 
			'id' => "property_file_upload", 
			'title' => __( 'File Upload', 'noo' ), 
			'page' => 'noo_property', 
			'context' => 'side', 
			'priority' => 'default', 
			'fields' => array( 
				array( 
					'id' => '_pdf_file', 
					'label' => '', 
					'type' => 'application_upload',
					'std' => re_get_property_setting('check_file','pdf,docx,doc'),
				)
			) 
		);
		
		$helper->add_meta_box($meta_box);

		$meta_box = array(
			'id'  => 'property_add_information',
			'title' => __('Add Additional Information' , 'noo'),
			'page'	=> 'noo_property',
			'context' => 'normal',
			'priority' => 'high',
			'fields'  => array(
				array(
					'id'  =>  'additional_features',
					'label' => '',
					'type'	=> 'add_information',
					'callback' => 'add_field_add_information',
				)
						
			),

		);
		$helper->add_meta_box($meta_box);


		if (re_get_property_setting('property_sub_listing', 'admin') != 'none') {
			$meta_box = array(
			'id' 		=> 'property_sub_listing',
			'title' 	=> __('Sub Listing' , 'noo'),
			'page'		=> 'noo_property',
			'context'	=> 'normal',
			'priority'	=> 'high',
			'fields'	=>  array(
				array(
					'id' 	 	=> 'sub_listing',
					'label'	 	=> '',
					'type'   	=> 'sub_listing',
					'callback'	=> 'add_field_sub_property',
				)
			), 
		);
		}
		
		$helper->add_meta_box($meta_box);
		if (re_get_property_setting('virtual_tour', 'yes') != 'none') {
		$meta_box = array(
			'id'	=>  'property_virtual_tour',
			'title'	=> __('360° Virtual Tour', 'noo'),
			'page'	=> 'noo_property',
			'context' => 'normal',
			'priority'=> 'high',
			'fields' => array(
				array(
				'id'	=>'_virtual_tour',
				'label' =>__('360° Virtual Tour', 'noo'),
				'desc' => __('Enter virtual tour iframe/embedded .', 'noo'),
				'type'	=> 'text'
				)
			),
		);
		}
		$helper->add_meta_box($meta_box);	

	}

	add_action( 'add_meta_boxes', 're_property_metabox', 30 );
endif;


if (!function_exists('add_field_add_information')) {
	function add_field_add_information($post , $id ,$type , $meta , $std , $field , $post_id = 0){
		switch ( $type ){
                case 'add_information':
    			$current_data_default = array(
    				'additional_feature_label' => '',
    				'additional_feature_value' => '',
    			);
				$current_data    			 = get_post_meta( $post->ID, 'additional_features', true );
				$current_data 				 =! empty( $current_data ) ? array_merge( $current_data ) : array_merge( array( $current_data_default ), array( $current_data_default ) );
				

                    ?>
                    <table class="additional-block">
                        <thead>
                        <tr>
                            <td></td>
                            <td><label><strong><?php echo esc_html__('Label', 'noo'); ?></strong></label></td>
                            <td><label><strong><?php echo esc_html__('Value', 'noo'); ?></strong></label></td>
                            <td></td>
                        </tr>
                        </thead>
                        <tbody id="noo_additional_details">
                        <?php if (is_array($current_data)): ?>
                            <?php foreach ($current_data as $key => $current_data): 
                            	$additional_feature_label = ( array_key_exists( 'additional_feature_label', $current_data ) && $key > 0  ) ? $current_data[ 'additional_feature_label' ] : '';
								$additional_feature_value = ( array_key_exists( 'additional_feature_value', $current_data ) && $key > 0 ) ? $current_data[ 'additional_feature_value' ] : '';
                            	?>
                                <tr <?php echo ( $key === 0 ) ? 'style="display: none;" ':''?>>
                                    <td class="action-field">
                                        <span class="sort-additional-row"><i class="fa fa-navicon"></i></span>
                                    </td>
                                    <td class="field-title">
                                        <input class="noo-ft-field" type="text"
                                               name="additional_features[<?php echo esc_attr($key); ?>][additional_feature_label]"
                                                value="<?php echo esc_attr( $additional_feature_label ) ?>">
                                    </td>
                                    <td>
                                    	<input type="text" name="additional_features[<?php echo esc_attr( $key ) ?>][additional_feature_value]" value="<?php echo esc_attr( $additional_feature_value ) ?>"/>
                                    </td>
                                    <td class="action-field">
                                        <span data-remove="<?php echo $key; ?>" class="remove-additional-row"><i class="fa fa-remove"></i></span>
                                    </td>
                                </tr>
                            	
                            <?php endforeach; ?>
                        <?php endif; ?>

                        </tbody>
                        <tfoot>
                        <tr>
                            <td></td>
                            <td>
                                <button data-increment="0" class="add-additional-row button-primary"data-key="<?php echo esc_attr( $key ) ?>" data-id="<?php echo $id; ?>">
                                    <i class="fa fa-plus"></i> <?php echo esc_html__('Add new field', 'noo'); ?>
                                </button>
                            </td>
                            <td></td>
                        </tr>
                        </tfoot>
                    </table>
                    <?php
                    break;
           }
	}
}

// sub listing
if (!function_exists('add_field_sub_property')) {
	function add_field_sub_property( $post, $id, $type, $meta, $std, $field ) {
switch ( $type ) {
    case 'sub_listing':
        $sub_listing_default = array(
            'title_sub_listing'         => '',
            'bedroom_sub_listing'       => '',
            'bathroom_sub_listing'      => '',
            'size_sub_listing'          => '',
            'price_sub_listing'         => '',
            'type_sub_listing'          => '',
            'available_sub_listing'     => '',
            );

        $sub_listing              = get_post_meta( $post->ID, 'sub_listing', true );
        $sub_listing              = ! empty( $sub_listing ) ? array_merge( $sub_listing ) : array_merge( array( $sub_listing_default ), array( $sub_listing_default ) );
        if ( is_array( $sub_listing ) ) :
            echo '<div id="rp-item-sub_property_wrap-wrap">';
            foreach ( $sub_listing as $index => $sub_listing ) :
                $title_sub_listing = ( array_key_exists( 'title_sub_listing', $sub_listing ) && $index > 0 ) ? $sub_listing[ 'title_sub_listing' ] : '';
                $bedroom_sub_listing = ( array_key_exists( 'bedroom_sub_listing', $sub_listing ) && $index > 0 ) ? $sub_listing[ 'bedroom_sub_listing' ] : '';
                $bathroom_sub_listing = ( array_key_exists( 'bathroom_sub_listing', $sub_listing ) && $index > 0 ) ? $sub_listing[ 'bathroom_sub_listing' ] : '';
                $size_sub_listing = ( array_key_exists( 'size_sub_listing', $sub_listing ) && $index > 0 ) ? $sub_listing[ 'size_sub_listing' ] : '';
                $price_sub_listing = ( array_key_exists( 'price_sub_listing', $sub_listing ) && $index > 0 ) ? $sub_listing[ 'price_sub_listing' ] : '';
                $type_sub_listing = ( array_key_exists( 'type_sub_listing', $sub_listing ) && $index > 0 ) ? $sub_listing[ 'type_sub_listing' ] : '';
                $available_sub_listing = ( array_key_exists( 'available_sub_listing', $sub_listing ) && $index > 0 ) ? $sub_listing[ 'available_sub_listing' ] : '';

                ?>
                <div <?php echo ( $index === 0 ) ? 'id="clone_element1" style="display: none;" ' : '' ?> class="rp-sub-property-wrap  <?php echo ( $index > 0 ) ? ' floor-item' : '' ?>">
                    <i class="remove-sub-property fa fa-remove <?php echo ( $index != 1 ) ? 'show-remove' : '' ?> "></i>
                    <div class="rp-form-group">
                        <label><strong><?php echo esc_html__('Title','noo'); ?></strong></label>
                        <div class="rp-control">
                            <input type="text" name="sub_listing[<?php echo esc_attr( $index ) ?>][title_sub_listing]" value="<?php echo esc_attr( $title_sub_listing ) ?>"/>
                        </div>
                    </div>


                    <div class="rp-form-group">
                        <label><strong><?php echo esc_html__('Bedrooms','noo'); ?></strong></label>
                        <div class="rp-control">
                            <input type="text" name="sub_listing[<?php echo esc_attr( $index ) ?>][bedroom_sub_listing]" value="<?php echo esc_attr( $bedroom_sub_listing ) ?>"/>
                        </div>
                    </div>

                    <div class="rp-form-group">
                        <label><strong><?php echo esc_html__('Bathrooms','noo'); ?></strong></label>
                        <div class="rp-control">
                            <input type="text" name="sub_listing[<?php echo esc_attr( $index ) ?>][bathroom_sub_listing]" value="<?php echo esc_attr( $bathroom_sub_listing ) ?>"/>
                        </div>
                    </div>

                    <div class="rp-form-group">
                        <label><strong><?php echo esc_html__('Size','noo'); ?></strong></label>
                        <div class="rp-control">
                            <input type="text" name="sub_listing[<?php echo esc_attr( $index ) ?>][size_sub_listing]" value="<?php echo esc_attr( $size_sub_listing ) ?>"/>
                        </div>
                    </div>
                    <div class="rp-form-group">
                        <label><strong><?php echo esc_html__('Price','noo'); ?></strong></label>
                        <div class="rp-control">
                            <input type="text" name="sub_listing[<?php echo esc_attr( $index ) ?>][price_sub_listing]" value="<?php echo esc_attr( $price_sub_listing ) ?>"/>
                        </div>
                    </div>
                    <div class="rp-form-group">
                        <label><strong><?php echo esc_html__('Type','noo'); ?></strong></label>
                        <div class="rp-control">
                            <input type="text" name="sub_listing[<?php echo esc_attr( $index ) ?>][type_sub_listing]" value="<?php echo esc_attr( $type_sub_listing ) ?>"/>
                        </div>
                    </div>
                    <div class="rp-form-group">
                        <label><strong><?php echo esc_html__('Available From','noo'); ?></strong></label>
                        <div class="rp-control">
                            <input type="text" name="sub_listing[<?php echo esc_attr( $index ) ?>][available_sub_listing]" value="<?php echo esc_attr( $available_sub_listing ) ?>"/>
                        </div>
                    </div>
                </div>
                <?php
            endforeach;
            ?>
            <div class="rp-clone-sub-property">
                <div class="content-clone1"></div>
                <button class="button-primary add-sub-property" data-total="<?php echo count( $sub_listing ) ?>" data-id="<?php echo $id; ?>">
                    <?php echo esc_html__( 'Add More', 'noo' ) ?>
                </button>
            </div>
            </div>
            <?php
        endif;
        break;
		}
	}
}

        
// end sub listing

if( !function_exists( 're_property_metabox_google_map' ) ) :
	function re_property_metabox_google_map($post,$meta_box){
		?>
		<style>
		.noo-form-group._gallery > label,
		.noo-form-group._floor_plan > label{
			display: none;
		}
		._noo_property_gmap .noo-control{float: none;width: 100%;}
		</style>
		<div class="noo_property_google_map">
			<div id="noo_property_google_map" class="noo_property_google_map" style="height: 380px; margin-bottom: 30px; overflow: hidden;position: relative;width: 100%;">
			</div>
			<div class="noo_property_google_map_search">
				<input placeholder="<?php echo __('Search your map','noo')?>" type="text" autocomplete="off" id="noo_property_google_map_search_input">
			</div>
		</div>
		<?php
	}
endif;

if (! function_exists('re_property_metabox_bing_map')) :
	function re_property_metabox_bing_map($post,$meta_box){
		?>
		<style>
		.noo-form-group._gallery > label,	
		.noo-form-group._floor_plan > label{
			display: none;
		}
		._noo_property_gmap .noo-control{float: none;width: 100%;}
		</style>
		<?php 
		wp_enqueue_script('bing-map-api');
		wp_enqueue_script('bing-map');
		?>
		<div class="noo-box-map">
			<div data-id="noo_property_bing_map" class="noo_property_bing_map" style="height: 380px; margin-bottom: 30px; overflow: hidden;position: relative;width: 100%;">
				<div id="noo_property_bing_map"></div>
			</div>
			<div class="noo_property_bing_map_search" style="position: absolute;top: 0;left: 20%;">
				<input placeholder="<?php echo __('Search your map','noo')?>" type="text" autocomplete="off" id="noo_property_bing_map_search_input">
			</div>
		</div>
		<?php
	}
endif;

if( !function_exists( 're_property_save_label' ) ) :
	function re_property_save_label( $post_id, $post, $update ) {

		/*
	     * In production code, $slug should be set only once in the plugin,
	     * preferably as a class property, rather than in each function that needs it.
	     */
	    $slug = 'noo_property';

	    // ===== If this isn't a 'noo_property' post, don't update it.
	    if ( $slug != $post->post_type ) {
	        return;
	    }

	    // ===== Update the post's metadata.
	    $label = isset( $_REQUEST['noo_meta_boxes']['_label'] ) ? $_REQUEST['noo_meta_boxes']['_label'] : '';
	    if ( !empty( $label ) && is_numeric( $label ) ) {

	    	$property_label = get_term( $label, 'property_label' );
	    	wp_set_post_terms( $post_id, $property_label->name, 'property_label' );

	    } else {

	    	wp_delete_object_term_relationships( $post_id, 'property_label' );

	    }
	}

	add_action( 'save_post', 're_property_save_label', 10, 3 );
endif;

if ( ! function_exists( 'save_add_information' ) ) :
    function save_add_information( $prop_id) {
        if (isset($_POST['additional_features']) && is_array( $_POST[ 'additional_features' ] )) {
        	update_post_meta( $prop_id, 'additional_features', array_values( $_POST[ 'additional_features' ] ) );
            // update_post_meta( $prop_id, 'additional_feature_label', $additional_feature_label );
			// update_post_meta( $prop_id, 'additional_feature_value', $additional_feature_value );
        }
    }

    add_action( 'save_post', 'save_add_information' ,10, 4);

endif;
if ( ! function_exists( 'save_sub_property' ) ) :

    function save_sub_property( $prop_id ) {
        if ( isset( $_POST[ 'sub_listing' ] ) && is_array( $_POST[ 'sub_listing' ] ) ) {
            update_post_meta( $prop_id, 'sub_listing', array_values( $_POST[ 'sub_listing' ] ) );
        }
    }

    add_action( 'save_post', 'save_sub_property' ,10, 5);

endif;