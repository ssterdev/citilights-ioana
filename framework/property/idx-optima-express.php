<?php
/**
 * This file process all featured of plugin Optima Express
 *
 * @package 	Citilights
 * @author 		KENT <tuanlv@vietbrain.com>
 * @version 	1.0
 */

if ( class_exists( 'iHomefinderAutoloader' ) ) :

	/**
	 * Enqueue script to theme
	 */
	if ( ! function_exists( 'noo_citilights_style_plugin' ) ) :
		
		function noo_citilights_style_plugin() {

			/**
			 * Remove script/style default of plugin
			 */
				wp_deregister_style( 'ihf-chosen' );
				wp_deregister_style( 'ihf-bootstrap-css' );
				wp_deregister_script( 'ihf-bootstrap' );

			/**
			 * Add style
			 */
				wp_enqueue_style( 'noo-idx-optima-express', NOO_ASSETS_URI . '/css/idx-optima-express.css', null, null, 'all' );

		}

		add_action( 'noo_citilights_before_site_style', 'noo_citilights_style_plugin' );

	endif;

endif;