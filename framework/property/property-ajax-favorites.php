<?php
/**
 * Create ajax process when user add favorites
 *
 * @package 	Noo_Citilights
 * @author 		KENT <tuanlv@vietbrain.com>
 * @version 	1.0
 */

if ( ! function_exists( 'ajax_noo_property_favorites' ) ) :
	
	function ajax_noo_property_favorites() {

		/**
		 * Check security
		 */
			check_ajax_referer( 'property_security', 'security', esc_html__( 'Security Breach! Please contact admin!', 'noo' ) );

		/**
		 * Process
		 */
			$property_id = sanitize_text_field( $_POST['property_id'] );

			if ( !empty( $property_id ) ) {

				$status                  = sanitize_text_field( $_POST['status'] );
				$user_id                 = sanitize_text_field( $_POST['user_id'] );
				
				$is_favorites            = get_user_meta( $user_id, 'is_favorites', true );
				$check_is_favorites      = ( !empty( $is_favorites ) && is_array( $is_favorites ) ) ? true : false;
				$list_property_favorites = $check_is_favorites ? $is_favorites : array();

				if ( $status === 'add_favorites' ) {
					
					$list_property_favorites[] = $property_id;
				
					$response['msg']    = esc_html__( 'Add favorites success!', 'noo' );

				} elseif ( $status === 'is_favorites' ) {

					if( ( $key = array_search( $property_id, $list_property_favorites ) ) !== false ) {
					    unset($list_property_favorites[$key]);
					}

					$response['msg']    = esc_html__( 'Remove favorites success!', 'noo' );

				}

				update_user_meta( $user_id, 'is_favorites', array_unique( $list_property_favorites ) );
				$response['status'] = 'ok';

			} else {

				$response['status'] = 'error';
				$response['msg']    = esc_html__( 'Not empty id property, please check again!', 'noo' );
				
			}

			wp_send_json( $response );


	}

	add_action( 'wp_ajax_noo_favorites', 'ajax_noo_property_favorites' );
	add_action( 'wp_ajax_nopriv_noo_favorites', 'ajax_noo_property_favorites' );

endif;