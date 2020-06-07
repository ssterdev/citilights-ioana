<?php
/**
 * Get file name markers
 *
 * @package 	Citilights
 * @author 		KENT <tuanlv@vietbrain.com>
 * @version 	1.0
 */

if ( ! function_exists( 'noo_citilights_name_markers' ) ) :
	
	function noo_citilights_name_markers() {

	    if ( function_exists( 'icl_translate' ) ) {
	    	$language = apply_filters( 'wpml_current_language', 'en' );
	    } else {
	    	$language = '';
	    }

	    return get_template_directory() . '/assets/markers' . ( !empty( $language ) ? '-' . esc_attr( $language ) : '' ) . '.txt';

	}

endif;

/**
 * Save data json
 *
 * @package 	Citilights
 * @author 		KENT <tuanlv@vietbrain.com>
 * @version 	1.0
 */

if ( ! function_exists( 'noo_citilights_property_save_json' ) ) :
	
	function noo_citilights_property_save_json( $property_id, $post ) {

		if ( 'noo_property' != $post->post_type ) {
	        return;
	    }
	    
	    $file = noo_citilights_name_markers();

	    file_put_contents($file, re_get_property_markers());
	    	
	}

	add_action( 'save_post', 'noo_citilights_property_save_json', 11, 3 );

endif;

/**
 * Get list markers
 *
 * @package 	Citilights
 * @author 		KENT <tuanlv@vietbrain.com>
 * @version 	1.0
 */

if ( ! function_exists( 'noo_citilights_get_list_markers' ) ) :
	
	function noo_citilights_get_list_markers() {
		global $wpdb;

		$file = noo_citilights_name_markers();

		if ( file_exists( $file ) ) {
			return file_get_contents( $file );
		} else {
			return re_get_property_markers();
		}

	}

endif;