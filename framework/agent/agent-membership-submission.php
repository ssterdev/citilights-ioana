<?php
if( !function_exists( 're_set_submission_property_status' ) ) :
	function re_set_submission_property_status( $agent_id = null, $prop_id = null, $status_type = '' ) {
		if( empty( $agent_id ) ) {
			$user_id = get_current_user_id();
			if( !empty($user_id) ) {
				$agent_id = get_user_meta( $user_id, '_associated_agent_id', true );
			}
		}

		if( empty( $agent_id ) || empty( $prop_id ) || empty( $status_type ) ) {
			return false;
		}

		if( !re_agent_is_owner( $agent_id, $prop_id ) ) {
			return false;
		}

		switch( $status_type ) {
			case 'listing':
				update_post_meta( $prop_id, '_paid_listing', 1 );
				break;
			case 'featured':
				update_post_meta( $prop_id, '_featured', 'yes' );
				break;
			case 'both':
				update_post_meta( $prop_id, '_paid_listing', 1 );
				update_post_meta( $prop_id, '_featured', 'yes' );
				break;
		}
	}
endif;

if( !function_exists( 're_revoke_submission_property_status' ) ) :
	function re_revoke_submission_property_status( $agent_id = null, $prop_id = null, $status_type = '' ) {
		if( empty( $agent_id ) ) {
			$user_id = get_current_user_id();
			if( !empty($user_id) ) {
				$agent_id = get_user_meta( $user_id, '_associated_agent_id', true );
			}
		}

		if( empty( $agent_id ) || empty( $prop_id ) || empty( $status_type ) ) {
			return false;
		}

		if( !re_agent_is_owner( $agent_id, $prop_id ) ) {
			return false;
		}

		switch( $status_type ) {
			case 'listing':
				update_post_meta( $prop_id, '_paid_listing', '' );
				break;
			case 'featured':
				update_post_meta( $prop_id, '_featured', 'no' );
				break;
			case 'both':
				update_post_meta( $prop_id, '_paid_listing', '' );
				update_post_meta( $prop_id, '_featured', 'no' );
				break;
		}
	}
endif;