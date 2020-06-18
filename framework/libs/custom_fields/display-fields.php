<?php
if ( ! function_exists( 'noo_display_field' ) ) :
	function noo_display_field( $field = array(), $field_id = '', $value = '', $args = array() ) {
		if( empty( $value ) ) return;

		$blank_field = array( 'label' => '', 'type' => 'text' );
		$field = is_array( $field ) ? array_merge( $blank_field, $field ) : $blank_field;
		$args = array_merge( array(
				'label_tag' => 'h3',
				'label_class' => '',
				'value_tag' => 'p',
				'value_class' => ''
			), $args );

		$label = isset( $field['label_translated'] ) ? $field['label_translated'] : $field['label'];
		if( !empty( $args['label_tag']) ) {
			echo "<{$args['label_tag']} class='label-{$field_id} {$args['label_class']}'>". esc_html( $label ) . "</{$args['label_tag']}>";
		}

		noo_display_field_value( $field, $field_id, $value, $args );
	}
endif;

if ( ! function_exists( 'noo_display_field_value' ) ) :
	function noo_display_field_value( $field = array(), $field_id = '', $value = '', $args = array() ) {
		if( empty( $value ) ) return;

		$blank_field = array( 'label' => '', 'type' => 'text' );
		$field = is_array( $field ) ? array_merge( $blank_field, $field ) : $blank_field;
		$args = array_merge( array(
				'value_tag' => 'p',
				'value_class' => ''
			), $args );

		switch ( $field['type'] ) {
			case "textarea":
				noo_display_textarea_field( $field, $field_id, $value, $args );
				break;
			case "select":
				noo_display_select_field( $field, $field_id, $value, $args );
				break;
			case "multiple_select":
				noo_display_multiple_select_field( $field, $field_id, $value, $args );
				break;
			case "radio" :
				noo_display_radio_field( $field, $field_id, $value, $args );
				break;
			case "checkbox" :
				noo_display_checkbox_field( $field, $field_id, $value, $args );
				break;
			case "number" :
				noo_display_number_field( $field, $field_id, $value, $args );
				break;
			case "text" :
				noo_display_text_field( $field, $field_id, $value, $args );
				break;
			case "datepicker" :
				noo_display_datepicker_field( $field, $field_id, $value, $args );
				break;
			default :
				do_action( 'noo_display_field_' . $field['type'], $field, $field_id, $value, $args );
				break;
		}
	}
endif;

if ( ! function_exists( 'noo_display_text_field' ) ) :
	function noo_display_text_field( $field = array(), $field_id = '', $value = '', $args = array() ) {
		$value = noo_convert_custom_field_value( $field, $value );
		if( is_array( $value ) ) $value = implode(', ', $value);

		if( !empty( $args['value_tag'] ) ) {
			echo "<{$args['value_tag']} class='value-{$field_id} {$args['value_class']}'>". esc_html( $value ) . "</{$args['value_tag']}>";
		} else {
			echo esc_html( $value );
		}
	}
endif;

if ( ! function_exists( 'noo_display_textarea_field' ) ) :
	function noo_display_textarea_field( $field = array(), $field_id = '', $value = '', $args = array() ) {
		$label = isset( $field['label_translated'] ) ? $field['label_translated'] : $field['label'];
		$value = noo_convert_custom_field_value( $field, $value );

		if( !empty( $args['value_tag'] ) ) {
			echo "<{$args['value_tag']} class='value-{$field_id} {$args['value_class']}'>". do_shortcode( $value ) . "</{$args['value_tag']}>";
		} else {
			echo do_shortcode( $value );
		}
	}
endif;

if ( ! function_exists( 'noo_display_select_field' ) ) :
	function noo_display_select_field( $field = array(), $field_id = '', $value = '', $args = array() ) {
		noo_display_text_field( $field, $field_id, $value, $args );
	}
endif;

if ( ! function_exists( 'noo_display_multiple_select_field' ) ) :
	function noo_display_multiple_select_field( $field = array(), $field_id = '', $value = '', $args = array() ) {
		$value = !is_array( $value ) ? noo_json_decode( $value ) : $value;
		$value = noo_convert_custom_field_value( $field, $value );
		$value = implode(', ', $value);

		if( !empty( $args['value_tag'] ) ) {
			echo "<{$args['value_tag']} class='value-{$field_id} {$args['value_class']}'>". esc_html( $value ) . "</{$args['value_tag']}>";
		} else {
			echo esc_html( $value );
		}
	}
endif;

if ( ! function_exists( 'noo_display_radio_field' ) ) :
	function noo_display_radio_field( $field = array(), $field_id = '', $value = '', $args = array() ) {
		noo_display_text_field( $field, $field_id, $value, $args );
	}
endif;

if ( ! function_exists( 'noo_display_checkbox_field' ) ) :
	function noo_display_checkbox_field( $field = array(), $field_id = '', $value = '', $args = array() ) {
		noo_display_multiple_select_field( $field, $field_id, $value, $args );
	}
endif;

if ( ! function_exists( 'noo_display_number_field' ) ) :
	function noo_display_number_field( $field = array(), $field_id = '', $value = '', $args = array() ) {
		noo_display_text_field( $field, $field_id, $value, $args );
	}
endif;

if ( ! function_exists( 'noo_display_url_field' ) ) :
	function noo_display_url_field( $field = array(), $field_id = '', $value = '', $args = array() ) {
		$value = noo_convert_custom_field_value( $field, $value );
		if( is_array( $value ) ) $value = implode(', ', $value);

		if( !empty( $args['value_tag'] ) ) {
			echo "<{$args['value_tag']} class='value-{$field_id} {$args['value_class']}'><a href=\"". esc_url( $value ) . "\" target=\"_blank\"</{$args['value_tag']}>";
		} else {
			echo do_shortcode( $value );
		}
	}
endif;

if ( ! function_exists( 'noo_display_datepicker_field' ) ) :
	function noo_display_datepicker_field( $field = array(), $field_id = '', $value = '', $args = array() ) {
		noo_display_text_field( $field, $field_id, $value, $args );
	}
endif;
