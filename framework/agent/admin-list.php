<?php
if( !function_exists( 're_agent_admin_list_columns_header' ) ) :
	function re_agent_admin_list_columns_header( $columns ) {
		$before = array_slice($columns, 0, 2);
		$after = array_slice($columns, 2);
		
		$new_columns = array(
			'email' => __('Email', 'noo'),
			'membership' => __('Membership Package', 'noo'),
		);
		
		$columns = array_merge($before, $new_columns, $after);
		return $columns;
	}
	add_filter( 'manage_edit-' . RE_AGENT_POST_TYPE . '_columns', 're_agent_admin_list_columns_header' );
endif;

if( !function_exists( 're_agent_admin_list_columns_data' ) ) :
	function re_agent_admin_list_columns_data($column) {
		GLOBAL $post;
		$post_id = $post->ID;

		if ($column == 'email') {
			$email = get_post_meta($post_id, '_noo_agent_email', true);
			echo ( !empty( $email ) ? '<a href="mailto:' . $email . '">' . $email . '</a>' : 'N/A' ) ;
		}
		
		if ($column == 'membership') {
			$package_id = get_post_meta($post_id, '_membership_package', true);
			if( !empty( $package_id ) ) {
				$package = get_post($package_id);
				if(!empty($package)){
					echo '<a href="' . get_edit_post_link( $package_id ) . '">' . $package->post_title . '</a>';
				}
			} else {
				echo __('Free Membership', 'noo');
			}
		}
	}

	add_filter( 'manage_' . RE_AGENT_POST_TYPE . '_posts_custom_column', 're_agent_admin_list_columns_data' );
endif;
