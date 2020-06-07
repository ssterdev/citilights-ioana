<?php
if( !function_exists( 're_get_membership_package_id' ) ) :
	function re_get_membership_package_id( $agent_id = null ) {
		if( empty( $agent_id ) ) {
			$user_id = get_current_user_id();
			if( !empty($user_id) ) {
				$agent_id = get_user_meta( $user_id, '_associated_agent_id', true );
			}
		}

		if( empty( $agent_id ) ) {
			return 0;
		}

		return intval( get_post_meta( $agent_id, '_membership_package', true ) );
	}
endif;

if( !function_exists( 're_get_membership_expired_date' ) ) :
	function re_get_membership_expired_date( $agent_id = null ) {
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

		$package_id = intval( get_post_meta( $agent_id, '_membership_package', true ) );
		if( empty( $package_id ) ) {
			return false;
		}

		$package_prefix = NooMembership::MEMBERSHIP_META_PREFIX;
		$activation_date = intval( get_post_meta( $agent_id, '_activation_date', true ) );
		$interval = intval( get_post_meta( $package_id, "{$package_prefix}_interval", true ) );
		if( $interval == -1 ) { // Unlimited
			return false;
		}

		$interval_unit = esc_attr( get_post_meta( $package_id, "{$package_prefix}_interval_unit", true ) );

		$unit_seconds = 0;
		switch ($interval_unit){
			case 'day':
			$unit_seconds = 60*60*24;
			break;
			case 'week':
			$unit_seconds = 60*60*24*7;
			break;
			case 'month':
			$unit_seconds = 60*60*24*30;
			break;    
			case 'year':
			$unit_seconds = 60*60*24*365;
			break;    
		}

		$expired_date = $activation_date + $interval * $unit_seconds;

		return $expired_date;
	}
endif;

if( !function_exists( 're_is_membership_expired' ) ) :
	function re_is_membership_expired( $agent_id = null ) {
		$expired_date = re_get_membership_expired_date( $agent_id );

		if( $expired_date == false ) return false;

		$now = time();

		return ( $expired_date - $now ) < 0;
	}

endif;

if( !function_exists( 're_get_membership_listing_remain' ) ) :
	function re_get_membership_listing_remain( $agent_id = null ) {
		if( empty( $agent_id ) ) {
			$user_id = get_current_user_id();
			if( !empty($user_id) ) {
				$agent_id = get_user_meta( $user_id, '_associated_agent_id', true );
			}
		}

		if( !NooMembership::is_membership() ) {
			return 0;
		}

		$listing_remain = !empty( $agent_id ) ? get_post_meta( $agent_id, '_listing_remain', true ) : '';
		if( $listing_remain === '' || $listing_remain === null ) {
			$package_id = !empty( $agent_id ) ? re_get_membership_package_id( $agent_id ) : '';
			if( empty( $package_id ) ) {
				$freemium_enabled = (bool) ( esc_attr( re_get_agent_setting( 'noo_membership_freemium' ) ) );
				$freemium_listing_num = $freemium_enabled ? intval( re_get_agent_setting( 'noo_membership_freemium_listing_num' ) ) : 0;
				$freemium_listing_unlimited = $freemium_enabled ? (bool) re_get_agent_setting( 'noo_membership_freemium_listing_unlimited' ) : false;

				$listing_remain = $freemium_listing_unlimited ? -1 : $freemium_listing_num;
			} else {
				$package_prefix = NooMembership::MEMBERSHIP_META_PREFIX;
				$listing_num = intval( get_post_meta( $package_id, "{$package_prefix}_listing_num", true ) );
				$listing_unlimited = (bool) get_post_meta( $package_id, "{$package_prefix}_listing_num_unlimited", true );

				$listing_remain = $listing_unlimited ? -1 : $listing_num;
			}
		}
		return intval( $listing_remain );
	}
endif;

if( !function_exists( 're_get_membership_featured_remain' ) ) :
	function re_get_membership_featured_remain( $agent_id = null ) {
		if( empty( $agent_id ) ) {
			$user_id = get_current_user_id();
			if( !empty($user_id) ) {
				$agent_id = get_user_meta( $user_id, '_associated_agent_id', true );
			}
		}

		if( !NooMembership::is_membership() ) {
			return 0;
		}

		$featured_remain = !empty( $agent_id ) ? get_post_meta( $agent_id, '_featured_remain', true ) : '';
		if( $featured_remain === '' || $featured_remain === null ) {
			$package_id = !empty( $agent_id ) ? re_get_membership_package_id( $agent_id ) : '';
			if( empty( $package_id ) ) {
				$freemium_enabled = (bool) ( esc_attr( re_get_agent_setting( 'noo_membership_freemium' ) ) );
				$featured_remain = $freemium_enabled ? intval( re_get_agent_setting( 'noo_membership_freemium_featured_num' ) ) : 0;
			} else {
				$package_prefix = NooMembership::MEMBERSHIP_META_PREFIX;
				$featured_remain = intval( get_post_meta( $package_id, "{$package_prefix}_featured_num", true ) );
			}
		}

		return intval( $featured_remain );
	}
endif;

if( !function_exists( 're_get_membership_info' ) ) :
	function re_get_membership_info( $agent_id = null ) {
		if( empty( $agent_id ) ) {
			$user_id = get_current_user_id();
			if( !empty($user_id) ) {
				$agent_id = get_user_meta( $user_id, '_associated_agent_id', true );
			}
		}
		$agent_listing_remain = !empty( $agent_id ) ? get_post_meta( $agent_id, '_listing_remain', true ) : '';
		$agent_featured_remain = !empty( $agent_id ) ? get_post_meta( $agent_id, '_featured_remain', true ) : '';

		$return = array();
		$return['type'] = NooMembership::get_membership_type();
		if( $return['type'] == 'membership' ) {
			$freemium_enabled = (bool) ( esc_attr( re_get_agent_setting( 'noo_membership_freemium' ) ) );
			$freemium_listing_num = $freemium_enabled ? intval( re_get_agent_setting( 'noo_membership_freemium_listing_num' ) ) : 0;
			$freemium_listing_unlimited = $freemium_enabled ? (bool) re_get_agent_setting( 'noo_membership_freemium_listing_unlimited' ) : false;
			$freemium_featured_num = $freemium_enabled ? intval( re_get_agent_setting( 'noo_membership_freemium_featured_num' ) ) : 0;

			if( $freemium_listing_unlimited ) {
				$freemium_listing_num = -1;
			}

			$agent_package = array();
			$package_id = !empty( $agent_id ) ? re_get_membership_package_id( $agent_id ) : '';
			if( empty( $package_id ) ) {
				$agent_package['package_id'] = '';
				$agent_package['package_title'] = __('Free Membership', 'noo');
				
				$agent_package['listing_included'] = $freemium_listing_num;
				$agent_package['listing_remain'] = ( $agent_listing_remain === '' || $agent_listing_remain === null ) ? $freemium_listing_num : intval( $agent_listing_remain );

				$agent_package['featured_included'] = $freemium_featured_num;
				$agent_package['featured_remain'] = ( $agent_featured_remain === '' || $agent_featured_remain === null ) ? $freemium_featured_num : intval( $agent_featured_remain );
				$agent_package['expired_date'] = __('Never', 'noo'); // Never
			} else {
				$agent_package['package_id'] = $package_id;
				$agent_package['package_title'] = get_the_title( $package_id );

				$package_prefix = NooMembership::MEMBERSHIP_META_PREFIX;
				$listing_num = intval( get_post_meta( $package_id, "{$package_prefix}_listing_num", true ) );
				$listing_unlimited = (bool) get_post_meta( $package_id, "{$package_prefix}_listing_num_unlimited", true );
				$featured_num = intval( get_post_meta( $package_id, "{$package_prefix}_featured_num", true ) );

				if( $listing_unlimited ) {
					$listing_num = -1;
				}
				
				$agent_package['listing_included'] = $listing_num;
				$agent_package['featured_included'] = $featured_num;

				if( re_is_membership_expired($agent_id) ) {
					$agent_package['listing_remain'] = 0;
					$agent_package['featured_remain'] = 0;
					$agent_package['expired_date'] = -1; // Expired
				} else {
					$agent_package['listing_remain'] = ( $agent_listing_remain === '' || $agent_listing_remain === null) ? $listing_num : $agent_listing_remain;

					$agent_package['featured_remain'] = ( $agent_featured_remain === '' || $agent_featured_remain === null ) ? $featured_num : $agent_featured_remain;

					$expired_date = re_get_membership_expired_date( $agent_id );
					$expired_date = ( $expired_date == false ) ? __('Never', 'noo') : date_i18n(get_option('date_format'), $expired_date);
					$agent_package['expired_date'] = $expired_date;
				}
			}
			$return['data'] = $agent_package;
		} elseif( $return['type'] == 'submission' ) {
			$submission = array();
			$submission['listing_price'] = floatval( esc_attr( re_get_agent_setting('noo_submission_listing_price') ) );
			$submission['listing_price_text'] = NooPayment::format_price( $submission['listing_price'] );
			$submission['featured_price'] = floatval( esc_attr( re_get_agent_setting('noo_submission_featured_price') ) );
			$submission['featured_price_text'] = NooPayment::format_price( $submission['featured_price'] );

			$return['data'] = $submission;
		}
		return $return;
	}
endif;
