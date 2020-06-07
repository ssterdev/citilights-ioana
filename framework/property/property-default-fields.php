<?php

if( !function_exists( 're_get_property_default_fields' ) ) :
	function re_get_property_default_fields() {
		$default_fields = array(
			'_area' => array(
					'name' => '_area',
					'label' => __('Area','noo') . ' (' . re_get_property_setting('area_unit') . ')',
					'type' => 'text',
					'allowed_type' => array(
						'text'				=> __('Text', 'noo'),
						'select'			=> __('Select', 'noo'),
						'radio'				=> __( 'Radio', 'noo' ),
					),
					'value' => '',
					'is_default' => true,
					'required' => false
				),
			'_bedrooms' => array(
					'name' => '_bedrooms',
					'label' => __('Bedrooms','noo'),
					'type' => 'text',
					'value' => '',
					'is_default' => true,
					'required' => false
				),
			'_bathrooms' => array(
					'name' => '_bathrooms',
					'label' => __('Bathrooms','noo'),
					'type' => 'text',
					'value' => '',
					'is_default' => true,
					'required' => false
				),
			);

		return apply_filters( 're_property_default_fields', $default_fields );
	}
endif;
