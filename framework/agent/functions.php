<?php
if( !function_exists( 're_get_agent_setting' ) ) :
	function re_get_agent_setting( $id = null, $default = null ) {
		$setting = noo_get_setting( 'noo_agent_settings', $id );

		// Backward compatible
		if( $setting === null ) {
			return get_option( $id, $default );
		}

		return $setting;
	}
endif;

if( !function_exists( 're_is_dashboard_page' ) ) :
	function re_is_dashboard_page() {
		if( !is_page() ) {
			return false;
		}
		$page_template = get_post_meta(get_the_ID(), '_wp_page_template', true);
		return ( strpos($page_template, 'dashboard') !== false );
	}
endif;

if( !function_exists( 're_get_default_avatar_uri' ) ) :
	function re_get_default_avatar_uri() {
		$avatar = get_stylesheet_directory() . '/assets/images/default-avatar.png';
		if( file_exists( $avatar ) ) {
			return get_stylesheet_directory_uri() . '/assets/images/default-avatar.png';
		}

		return NOO_ASSETS_URI . '/images/default-avatar.png';
	}
endif;
