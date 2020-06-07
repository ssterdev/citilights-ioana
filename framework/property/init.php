<?php
if( !function_exists('re_register_property_post_type') ) :
	function re_register_property_post_type() {

		if(post_type_exists('noo_property'))
			return ;
		
		$noo_icon = NOO_FRAMEWORK_ADMIN_URI . '/assets/images/noo20x20.png';
		if ( floatval( get_bloginfo( 'version' ) ) >= 3.8 ) {
			$noo_icon = 'dashicons-location';
		}

		$slug = re_get_property_setting('archive_slug','properties');
		$rewrite = $slug ? array( 'slug' => sanitize_title( $slug ), 'with_front' => true, 'feeds' => true ) : false;

		register_post_type('noo_property',array(
			'labels' => array(
				'name'                  => __('Properties','noo'),
				'singular_name'         => __('Property','noo'),
				'add_new'               => __('Add New Property','noo'),
				'add_new_item'          => __('Add Property','noo'),
				'edit'                  => __('Edit','noo'),
				'edit_item'             => __('Edit Property','noo'),
				'new_item'              => __('New Property','noo'),
				'view'                  => __('View','noo'),
				'view_item'             => __('View Property','noo'),
				'search_items'          => __('Search Property','noo'),
				'not_found'             => __('No Properties found','noo'),
				'not_found_in_trash'    => __('No Properties found in Trash','noo'),
				'parent'                => __('Parent Property','noo')
			),
			'public' => true,
			'has_archive' => re_get_property_setting('archive_slug','properties'),
			'menu_icon'=>$noo_icon,
			'rewrite' => $rewrite,
			'supports' => array('title', 'editor', 'thumbnail', 'comments', 'excerpt'),
			'can_export' => true,
			)
		);
		
		register_taxonomy ( 'property_category', 'noo_property', array (
				'labels' => array (
						'name' => __ ( 'Property Type', 'noo' ),
						'add_new_item' => __ ( 'Add New Property Type', 'noo' ),
						'new_item_name' => __ ( 'New Property Type', 'noo' ) 
				),
				'hierarchical' => true,
				'query_var' => true,
				'rewrite' => array ( 'slug' => _x('listings', 'slug', 'noo') ) 
		) );
		
		
		register_taxonomy ( 'property_label', 'noo_property', array (
			'labels' => array (
				'name' => __ ( 'Property Label', 'noo' ),
				'add_new_item' => __ ( 'Add New Property Label', 'noo' ),
				'new_item_name' => __ ( 'New Property Label', 'noo' )
			),
			'show_ui'               => true,
			'query_var'             => true,
			'show_in_nav_menus'     => true,
			'meta_box_cb'			=> false,
		) );
		
		
		register_taxonomy ( 'property_location', 'noo_property', array (
				'labels' => array (
						'name' => __ ( 'Property Location', 'noo' ),
						'add_new_item' => __ ( 'Add New Property Location', 'noo' ),
						'new_item_name' => __ ( 'New Property Location', 'noo' ) 
				),
				'hierarchical' => true,
				'query_var' => true,
				'rewrite' => array ('slug' => _x('property-location', 'slug', 'noo')) 
		) );
		
		register_taxonomy ( 'property_sub_location', 'noo_property', array (
			'labels' => array (
				'name' => __ ( 'Property Sub-location', 'noo' ),
				'add_new_item' => __ ( 'Add New Property Sub-location', 'noo' ),
				'new_item_name' => __ ( 'New Property Sub-location', 'noo' )
			),
			'hierarchical' => true,
			'query_var' => true,
			'show_ui'               => true,
			'rewrite' => array ('slug' => _x('property-sub-location', 'slug', 'noo'))
		) );
			
		register_taxonomy ( 'property_status', 'noo_property', array (
			'labels' => array (
				'name' => __ ( 'Property Status', 'noo' ),
				'add_new_item' => __ ( 'Add New Property Status', 'noo' ),
				'new_item_name' => __ ( 'New Property Status', 'noo' )
			),
			'hierarchical' => true,
			'query_var' => true,
			'rewrite' => array ('slug' => _x('status', 'slug', 'noo') )
		) );

		// register_post_status( 'inactive', array(
		// 	'label'                     => __( 'Sold/Rent', 'noo' ),
		// 	'public'                    => false,
		// 	'exclude_from_search'       => true,
		// 	'show_in_admin_all_list'    => true,
		// 	'show_in_admin_status_list' => true,
		// 	'label_count'               => _n_noop( 'Sold/Rent <span class="count">(%s)</span>', 'Sold/Rent <span class="count">(%s)</span>', 'noo' ),
		// ) );

		$default_property_status = get_option('default_property_status');
		if(empty($default_property_status)){
			$slug = sanitize_title(__('sold','noo'));
			$args = array(
					'slug' => $slug,
					'description' => __( 'This status is a predefined status, used for properties that is sold or rented. Properties with this status WON\'T display on some pages so you should be careful if you want to use this status for something else.', 'noo')
				);
			$ret = wp_insert_term(esc_html(__('Sold','noo')),'property_status',$args);
			if ( $ret && !is_wp_error( $ret ) && ($term = get_term_by('slug', $slug, 'property_status')) ){
				$r  = update_option('default_property_status', $term->term_id);
			}
		}
	}
	add_action( 'init', 're_register_property_post_type' );
endif;
