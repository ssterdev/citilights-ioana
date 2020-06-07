<?php
if( !function_exists( 're_approve_property_action') ) :
	function re_approve_property_action( $new_status, $old_status, $post ) {
		if( $post->post_type !== 'noo_property' || re_get_agent_setting('noo_membership_type', 'membership') == 'none' )
			return;

		if( get_option('noo_membership_type', 'membership') == 'none' )
			return;

		if( $new_status == 'publish' && $old_status != 'publish' ) {
			$agent_id = noo_get_post_meta( $post->ID, '_agent_responsible', '' );
			if( empty( $agent_id ) )
				return;

			$user_id = noo_get_post_meta( $agent_id, '_associated_user_id', '' );
			$user = get_user_by('id', $user_id);
			if( empty( $user ) )
				return;

			$user_email = $user->user_email;
			$site_name = get_option('blogname');
			$property_title = $post->post_title;
			$property_link = get_permalink( $post->ID );
			if( $user->roles[0] == 'subscriber'){
				$message = sprintf( __("Congrats! You submitted %s on %s and it has been approved. You can check it here: %s", 'noo'), $property_title, $site_name, $property_link) . "<br/><br/>";
				noo_mail($user_email,
					sprintf(__('[%s] Property submission approved','noo'), $site_name),
					$message);
			}
		}
	}

	add_action('transition_post_status', 're_approve_property_action', 10, 3);
endif;
