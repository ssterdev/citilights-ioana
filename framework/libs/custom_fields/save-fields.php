<?php
if ( ! function_exists( 'noo_save_field' ) ) :
	function noo_save_field( $post_id = 0, $id = '', $value, $field = array() ) {
		if( empty( $post_id ) || empty( $id ) ) return;

		$value = noo_sanitize_field( $value, $field );
		update_post_meta( $post_id, $id, $value );
	}
endif;

if ( ! function_exists( 'noo_sanitize_field' ) ) :
	function noo_sanitize_field( $value, $field = array() ) {
		switch ( $field['type'] ) {
			case "textarea":
				break;
			case "multiple_select":
			case "radio" :
			case "checkbox" :
				if( is_array( $value ) ) {
					foreach ($value as $k => $v) {
						$v = wp_kses( $v, array() );
						$value[$k] = $v;
					}
				} else {
					$value = wp_kses( $value, array() );
				}
				break;
			case "select":
			case "text" :
			case "number" :
				$value = wp_kses( $value, array() );
				break;
			case "datepicker" :
				$value = strtotime( $value );
				break;
		}

		return apply_filters( 'noo_sanitize_field_' . $field['type'], $value );
	}
endif;
