<?php
if( !function_exists( 're_get_login_url' ) ) :
	function re_get_login_url( $no_redirect = false ) {
		$current_url = noo_current_url();
		$login_url = re_get_agent_setting( 'noo_login_page' );
		$login_url = ( !empty($login_url) ) ? get_permalink( $login_url ) : '';
		if( !$no_redirect ) {
			$login_url = ( !empty($login_url) ) ? esc_url( add_query_arg('redirect_to', urlencode($current_url), $login_url) ) :  wp_login_url( $current_url );
		} else {
			$login_url = ( !empty($login_url) ) ? $login_url :  wp_login_url( $current_url );
		}
		
		return $login_url;
	}
endif;

if( !function_exists( 're_check_logged_in_user' ) ) :
	function re_check_logged_in_user() {
		if ( !is_user_logged_in() ) {
			wp_redirect( re_get_login_url() );
		}
	}
endif;
