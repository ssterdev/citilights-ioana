<?php
if( !function_exists( 're_property_summary_fields') ) :
	function re_property_summary_fields() {
		$default_primary = apply_filters( 're_property_primary_fields', array( '_area', '_bedrooms', '_bathrooms' ) );
		$fields = re_get_property_custom_fields_option( 'primary_fields', $default_primary );
		return apply_filters( 're_property_summary_fields', $fields );
	}
endif;

if( !function_exists( 're_property_summary_field_icons') ) :
	function re_property_summary_field_icons() {
		$theme_uri = get_stylesheet_directory_uri();
		$default_icons = array(
				$theme_uri . "/assets/images/size-icon.png",
				$theme_uri . "/assets/images/bedroom-icon.png",
				$theme_uri . "/assets/images/bathroom-icon.png",
			);
		$field_icons = re_get_property_custom_fields_option( 'primary_field_icons', $default_icons );

		return apply_filters( 're_property_summary_field_icons', $field_icons );
	}
endif;

if( !function_exists( 're_property_summary_field_icons_2') ) :
	function re_property_summary_field_icons_2() {
		$theme_uri = get_stylesheet_directory_uri();
		$default_icons = array(
				$theme_uri . "/assets/images/size-icon-2.png",
				$theme_uri . "/assets/images/bedroom-icon-2.png",
				$theme_uri . "/assets/images/bathroom-icon-2.png",
			);
		$field_icons = re_get_property_custom_fields_option( 'primary_field_icons', $default_icons );

		return apply_filters( 're_property_summary_field_icons_2', $field_icons );
	}
endif;

if( !function_exists( 're_property_summary') ) :
	function re_property_summary( $args = '' ) {
		$defaults = array(
			'property_id'		=> '',
			'container_class'	=> 'property-detail',
			'fields'			=> array(),
			'field_icons'		=> array(),
		);
		extract(wp_parse_args($args,$defaults));

		if( empty( $fields ) ) {
			$fields = re_property_summary_fields();
		}
		
		if( empty( $field_icons ) ) {
			$field_icons = re_property_summary_field_icons();
		}

		if( empty( $property_id ) ) $property_id = get_the_ID();

		$html = array();
		if( !empty( $container_class ) ) {
			$html[] = '<div class="' . $container_class . '">';
		}

		$default_fields = re_get_property_default_fields();
		$default_fields = array_keys( $default_fields );

		foreach ($fields as $index => $field) {
			if( empty( $field ) ) continue;
			$field_id = in_array( $field, $default_fields ) ? $field : re_property_custom_fields_name( $field );
			$value = '';
			if( $field == '_area' ) {
				$value = re_get_property_area_html( $property_id );
			} elseif( $field == '_price' ) {
				$value = re_get_property_price_html( $property_id );
			} else {
				$value = get_post_meta( $property_id, $field_id, true );
				$custom_field = re_get_property_field( $field );
				$value = noo_convert_custom_field_value( $custom_field, $value );
				if( is_array( $value ) ) $value = implode(', ', $value);

				$value = esc_html( $value );
			}

			if( !empty( $value ) ) {
				$class = $field[0] == '_' ? substr($field, 1) : $field;
				$icon_style = isset( $field_icons[ $index ] ) && !empty( $field_icons[ $index ]) ? 'background-image: url(' . esc_url_raw( $field_icons[ $index ] ) . ');' : '';
				
				$html[] = '<div class="' . $class . '"><span class="property-meta-icon" style="' . $icon_style . '"></span><span class="property-meta">' . $value . '</span></div>';
			}
		}

		if( !empty( $container_class ) ) {
			$html[] = '</div>';
		}

		return implode("\n", $html);
	}
endif;
