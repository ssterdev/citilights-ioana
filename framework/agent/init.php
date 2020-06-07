<?php
if( !defined( 'RE_AGENT_POST_TYPE' ) ) define( 'RE_AGENT_POST_TYPE', 'noo_agent' );
if( !defined( 'RE_AGENT_SLUG' ) ) define( 'RE_AGENT_SLUG', 'agents' );
if( !defined( 'RE_AGENT_META_PREFIX' ) ) define( 'RE_AGENT_META_PREFIX', '_noo_agent' );

if( !function_exists('re_register_agent_post_type') ) :
	function re_register_agent_post_type() {

		if(post_type_exists('noo_agent'))
			return ;
		
		// Text for NOO Agent.
		$noo_agent_labels = array(
			'name' => __('Agents', 'noo'),
			'singular_name' => __('Agent', 'noo'),
			'menu_name' => __('Agents &amp; Membership', 'noo'),
			'all_items' => __('All Agents', 'noo'),
			'add_new' => __('Add Agent', 'noo'),
			'add_new_item' => __('Add Agent', 'noo'),
			'edit_item' => __('Edit Agent', 'noo'),
			'new_item' => __('New Agent', 'noo'),
			'view_item' => __('View Agent', 'noo'),
			'search_items' => __('Search Agent', 'noo'),
			'not_found' => __('No agents found', 'noo'),
			'not_found_in_trash' => __('No agents found in trash', 'noo'),
			'parent_item_colon' => ''
		);
		
		$noo_agent_icon = NOO_FRAMEWORK_ADMIN_URI . '/assets/images/noo20x20.png';
		if ( floatval( get_bloginfo( 'version' ) ) >= 3.8 ) {
			$noo_agent_icon = 'dashicons-businessman';
		}

		$noo_agent_slug = re_get_agent_setting( 'noo_agent_archive_slug', '' );
		$noo_agent_slug = !empty($noo_agent_slug) ? $noo_agent_slug : RE_AGENT_SLUG;

		// Options
		$noo_agent_args = array(
			'labels' => $noo_agent_labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			// 'menu_position' => 5,
			'menu_icon' => $noo_agent_icon,
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array(
				'title',
				'editor',
				// 'excerpt',
				'thumbnail',
				'comments',
				// 'custom-fields',
				'revisions'
			),
			'has_archive' => true,
			'rewrite' => array(
				'slug' => $noo_agent_slug,
				'with_front' => false
			)
		);
		
		register_post_type(RE_AGENT_POST_TYPE, $noo_agent_args);
	}
	add_action( 'init', 're_register_agent_post_type', 0 );
endif;
