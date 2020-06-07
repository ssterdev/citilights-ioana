<?php
if( !function_exists( 're_set_agent_membership' ) ) :
	function re_set_agent_membership( $agent_id = null, $package_id = null, $activation_date = null, $is_admin_edit = false ) {
		if( empty( $agent_id ) ) {
			$user_id = get_current_user_id();
			if( !empty($user_id) ) {
				$agent_id = get_user_meta( $user_id, '_associated_agent_id', true );
			}
		}

		if( empty( $agent_id ) ) {
			return false;
		}

		$agent_package = re_get_membership_package_id( $agent_id );

		if( !$is_admin_edit || $package_id != $agent_package ) {
			if( empty( $package_id ) ) {
				$freemium_enabled = (bool) ( esc_attr( re_get_agent_setting( 'noo_membership_freemium' ) ) );
				$freemium_listing_num = $freemium_enabled ? intval( re_get_agent_setting( 'noo_membership_freemium_listing_num' ) ) : 0;
				$freemium_listing_unlimited = $freemium_enabled ? (bool) re_get_agent_setting( 'noo_membership_freemium_listing_unlimited' ) : false;

				$listing_remain = $freemium_listing_unlimited ? -1 : $freemium_listing_num;
				$featured_remain = $freemium_enabled ? intval( re_get_agent_setting( 'noo_membership_freemium_featured_num' ) ) : 0;

				$interval = -1;
				$interval_unit = 'day';
			} else {
				$package_prefix = NooMembership::MEMBERSHIP_META_PREFIX;
				$listing_num = intval( get_post_meta( $package_id, "{$package_prefix}_listing_num", true ) );
				$listing_unlimited = (bool) get_post_meta( $package_id, "{$package_prefix}_listing_num_unlimited", true );

				$listing_remain = $listing_unlimited ? -1 : $listing_num;
				$featured_remain = intval( get_post_meta( $package_id, "{$package_prefix}_featured_num", true ) );

				$interval = intval( get_post_meta( $package_id, "{$package_prefix}_interval", true ) );
				$interval_unit = intval( get_post_meta( $package_id, "{$package_prefix}_interval_unit", 'day', true ) );
			}

			$activation_date = empty( $activation_date ) ? time() : $activation_date; // Date down to second

			update_post_meta( $agent_id, '_membership_package', $package_id );
			update_post_meta( $agent_id, '_listing_remain', $listing_remain );
			update_post_meta( $agent_id, '_featured_remain', $featured_remain );
			update_post_meta( $agent_id, '_activation_date', $activation_date );
			update_post_meta( $agent_id, '_membership_interval', $interval );
			update_post_meta( $agent_id, '_membership_interval_unit', $interval_unit );

			do_action('noo_set_agent_membership', $agent_id, $package_id, $activation_date, $is_admin_edit );
		}
	}
endif;

if( !function_exists( 're_revoke_agent_membership' ) ) :
	function re_revoke_agent_membership( $agent_id = null, $package_id = null ) {
		if( empty( $agent_id ) ) {
			$user_id = get_current_user_id();
			if( !empty($user_id) ) {
				$agent_id = get_user_meta( $user_id, '_associated_agent_id', true );
			}
		}

		if( empty( $agent_id ) || empty( $package_id ) ) {
			return false;
		}

		$agent_package = re_get_membership_package_id( $agent_id );

		if( $package_id == $agent_package ) {
			$freemium_enabled = (bool) ( esc_attr( re_get_agent_setting( 'noo_membership_freemium' ) ) );
			$freemium_listing_num = $freemium_enabled ? intval( re_get_agent_setting( 'noo_membership_freemium_listing_num' ) ) : 0;
			$freemium_listing_unlimited = $freemium_enabled ? (bool) re_get_agent_setting( 'noo_membership_freemium_listing_unlimited' ) : false;

			$listing_remain = $freemium_listing_unlimited ? -1 : 0;
			$featured_remain = 0;

			$interval = -1;
			$interval_unit = 'day';

			$activation_date = time(); // Date down to second

			update_post_meta( $agent_id, '_membership_package','' );
			update_post_meta( $agent_id, '_listing_remain', $listing_remain );
			update_post_meta( $agent_id, '_featured_remain', $featured_remain );
			update_post_meta( $agent_id, '_activation_date', $activation_date );
			update_post_meta( $agent_id, '_membership_interval', $interval );
			update_post_meta( $agent_id, '_membership_interval_unit', $interval_unit );
		}
	}
endif;

if( !function_exists( 're_decrease_membership_listing_remain' ) ) :
	function re_decrease_membership_listing_remain( $agent_id = null ) {
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

		$listing_remain = re_get_membership_listing_remain( $agent_id );
		if( $listing_remain == -1) {
			// unlimited
		} else {
			$new_listing_remain = max( 0, $listing_remain - 1 );
			update_post_meta( $agent_id, '_listing_remain', $new_listing_remain );
		}
	}
endif;

if( !function_exists( 're_decrease_membership_featured_remain' ) ) :
	function re_decrease_membership_featured_remain( $agent_id = null ) {
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

		$featured_remain = re_get_membership_featured_remain( $agent_id );
		$new_featured_remain = max( 0, $featured_remain - 1 );
		update_post_meta( $agent_id, '_featured_remain', $new_featured_remain );
	}
endif;
