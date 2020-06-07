<?php
/**
 * Create ajax process filter property map
 *
 * @package 	Noo_Citilights
 * @author 		KENT <tuanlv@vietbrain.com>
 * @version 	1.0
 */

if ( ! function_exists( 'ajax_noo_filter_property_map' ) ) :
	
	function ajax_noo_filter_property_map() {

		/**
		 * Check security
		 */
			check_ajax_referer( 'noo-property-map', 'security', esc_html__( 'Security Breach! Please contact admin!', 'noo' ) );

		/**
		 * Process
		 */
			
			$current_page = ( !empty( $_POST['current_page'] ) && $_POST['current_page'] !== 'NaN' ) ? absint( $_POST['current_page'] ) : 1;

			$args = array(
				'posts_per_page' => 10,
				'post_status'    => 'publish',
				'post_type'      => 'noo_property',
				'paged'			 => $current_page
			);

			$args = re_property_query_from_request( $args, $_POST );

			$wp_query = new WP_Query( apply_filters( 'noo_query_ajax_filter_map', $args ) );

			if ( $_POST['results'] === 'load_more' ) :

				$ajax_only_item = true;

			else :

				if ( $wp_query->found_posts <= 0 ) :

					wp_die( esc_html__( 'We found no results', 'noo' ) );

				endif;

			endif;

			/**
			 * Set default
			 */
				$title                 = !empty( $hide_head ) ? '' : esc_html__( 'Your search results', 'noo' );
				$display_mode          = !empty( $hide_head ) ? false : true;
				$show_remove_favorites = false;
				$mode                  = get_theme_mod( 'noo_property_listing_layout', 'grid' );
				$show_pagination       = false;
				$ajax_pagination       = true;
				$show_orderby          = true;
				$ajax_content          = false;
				$is_fullwidth          = false;
				$display_style 		   = get_theme_mod( 'noo_property_display_style', 'style-1' );
				if ( !empty( $_POST['hide_orderby'] ) ){
					$show_orderby          = false;
				}
			/**
			 * Check query and process
			 */
			    $prop_style = 'style-1';
				ob_start();
		        include(locate_template("layouts/noo-property-loop.php"));
		        echo ob_get_clean();

			wp_die();

	}

	add_action( 'wp_ajax_filter_property_map', 'ajax_noo_filter_property_map' );
	add_action( 'wp_ajax_nopriv_filter_property_map', 'ajax_noo_filter_property_map' );

endif;