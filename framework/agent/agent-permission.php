<?php
if( !function_exists( 're_agent_can_add' ) ) :
	function re_agent_can_add( $agent_id = null ) {
		$membership_type = NooMembership::get_membership_type();
		if( $membership_type == 'none' ) {
			return true;
		}

		if( !NooMembership::is_membership() ) {
			return true;
		}

		if( re_is_membership_expired( $agent_id ) ) {
			return false;
		}

		$listing_remain = re_get_membership_listing_remain( $agent_id );

		return ( $listing_remain == -1 || $listing_remain > 0 );
	}
endif;

if( !function_exists( 're_agent_can_edit' ) ) :
	function re_agent_can_edit( $agent_id = null ) {
		return !re_is_membership_expired( $agent_id );
	}
endif;

if( !function_exists( 're_agent_can_delete' ) ) :
	function re_agent_can_delete( $agent_id = null ) {
		return re_agent_can_edit( $agent_id );
	}
endif;

if( !function_exists( 're_agent_can_set_featured' ) ) :
	function re_agent_can_set_featured( $agent_id = null ) {
		if( empty( $agent_id ) ) {
			$user_id = get_current_user_id();
			if( !empty($user_id) ) {
				$agent_id = get_user_meta( $user_id, '_associated_agent_id', true );
			}
		}

		if( empty( $agent_id ) ) {
			return false;
		}

		if( !NooMembership::is_membership() ) {
			return false;
		}

		if( re_is_membership_expired( $agent_id ) ) {
			return false;
		}

		$featured_remain = re_get_membership_featured_remain( $agent_id );
		return ( $featured_remain == -1 || $featured_remain > 0 );
	}
endif;

if( !function_exists( 're_agent_is_owner' ) ) :
	function re_agent_is_owner( $agent_id = null, $prop_id = null ) {
		if( empty( $agent_id ) ) {
			$user_id = get_current_user_id();
			if( !empty($user_id) ) {
				$agent_id = get_user_meta( $user_id, '_associated_agent_id', true );
			}
		}

		if( empty( $agent_id ) || empty( $prop_id ) ) {
			return false;
		}

		return $agent_id == intval( get_post_meta( $prop_id, '_agent_responsible', true ) );
	}
endif;
