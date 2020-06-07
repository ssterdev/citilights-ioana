<?php
if( !function_exists( 're_agent_has_associated_user' ) ) :
	function re_agent_has_associated_user( $agent_id = null ) {
		if( empty( $agent_id ) ) {
			return false;
		}

		$associated_user_id = get_post_meta( get_the_ID(), '_associated_user_id', true );
		$has_user = !empty( $associated_user_id ) ? (bool) get_user_by( 'id', (int)$associated_user_id ) : false;

		return $has_user;
	}
endif;

if( !function_exists( 're_agent_admin_edit_user_profile' ) ) :
	function re_agent_admin_edit_user_profile( $user ) {
		if( re_get_agent_setting('noo_membership_type', 'membership') == 'none' ) return;
		?>
		<input type="hidden" id="_associated_agent_id" name="_associated_agent_id" value="<?php echo get_user_meta( $user->ID, '_associated_agent_id', true ); ?>"/>
		<?php
	}
	// add_action('show_user_profile', 're_agent_admin_edit_user_profile');
endif;

if( !function_exists( 're_agent_admin_edit_user_profile_update' ) ) :
	function re_agent_admin_edit_user_profile_update( $user_id ) {
		if( re_get_agent_setting('noo_membership_type', 'membership') == 'none' ) return;

		$user = new WP_User( $user_id );
		$agent_id = get_user_meta( $user_id, '_associated_agent_id', true );

		// Update email so that user and agent always have the same email
		$agent_email = get_post_meta( $agent_id, '_noo_agent_email', true );
		$user_email = isset($_POST['email']) ? wp_kses( $_POST['email'], array() ) : $user->user_email;
		if( $agent_email != $user_email ) {
			update_post_meta( $agent_id, '_noo_agent_email', $user_email );
		}
	}
	add_action('edit_user_profile_update', 're_agent_admin_edit_user_profile_update');
	add_action('personal_options_update', 're_agent_admin_edit_user_profile_update');
endif;

if( !function_exists( 're_agent_stop_admin_bar_render' ) ) :
	function re_agent_stop_admin_bar_render() {
		if( !current_user_can('activate_plugins') ) {
			global $wp_admin_bar;
			$wp_admin_bar->remove_menu('edit-profile');
			$wp_admin_bar->remove_menu('user-actions');
		}
	}

	add_action( 'wp_before_admin_bar_render', 're_agent_stop_admin_bar_render');
endif;

if( !function_exists( 're_agent_admin_hide_admin_bar_front' ) ) :
	function re_agent_admin_hide_admin_bar_front($user_ID) {
		update_user_meta( $user_ID, 'show_admin_bar_front', 'false' );
	}
	add_action('user_register', 're_agent_admin_hide_admin_bar_front');
endif;

if( !function_exists( 're_agent_stop_admin_profile' ) ) :
	function re_agent_stop_admin_profile() {
		if( !current_user_can('activate_plugins') ) {
			global $pagenow;

			if( defined('IS_PROFILE_PAGE') && IS_PROFILE_PAGE === true ) {
				$dashboard_link = noo_get_page_link_by_template( 'agent_dashboard_profile.php' );
				wp_die( sprintf( __('Please access your profile from <a href="%s">Site interface</a>.', 'noo' ), $dashboard_link ), '', array( 'response' => 403 ) );
			}

			if($pagenow=='user-edit.php'){
				$dashboard_link = noo_get_page_link_by_template( 'agent_dashboard_profile.php' );
				wp_die( sprintf( __('Please access your profile from <a href="%s">Site interface</a>.', 'noo' ), $dashboard_link ), '', array( 'response' => 403 ) );
			}
		}
	}
	add_action( 'admin_init', 're_agent_stop_admin_profile');
endif;

if( !function_exists( 're_create_agent_from_user' ) ) :
	function re_create_agent_from_user( $user_id = null ) {
		if( empty( $user_id ) ) {
			return 0;
		}

		// Insert new agent
		$user = new WP_User( $user_id );
		$agent = array(
			'post_title'	=> $user->display_name,
			'post_status'	=> 'publish', 
			'post_type'     => RE_AGENT_POST_TYPE
			);

		$prefix = RE_AGENT_META_PREFIX;

		$agent_id =  wp_insert_post( $agent );
		if( $agent_id ) {
			update_user_meta( $user_id, '_associated_agent_id', $agent_id);
			update_post_meta( $agent_id, '_associated_user_id', $user_id);
			update_post_meta( $agent_id, "{$prefix}_email", $user->user_email);

			$freemium_enabled = (bool) ( esc_attr( re_get_agent_setting( 'noo_membership_freemium' ) ) );
			$freemium_listing_num = $freemium_enabled ? intval( re_get_agent_setting( 'noo_membership_freemium_listing_num' ) ) : 0;
			$freemium_listing_unlimited = $freemium_enabled ? (bool) re_get_agent_setting( 'noo_membership_freemium_listing_unlimited' ) : false;

			$listing_remain = $freemium_listing_unlimited ? -1 : $freemium_listing_num;
			$featured_remain = $freemium_enabled ? intval( re_get_agent_setting( 'noo_membership_freemium_featured_num' ) ) : 0;

			$interval = -1;
			$interval_unit = 'day';

			$activation_date = time(); // Date down to second

			update_post_meta( $agent_id, '_membership_package', '' );
			update_post_meta( $agent_id, '_listing_remain', $listing_remain );
			update_post_meta( $agent_id, '_featured_remain', $featured_remain );
			update_post_meta( $agent_id, '_activation_date', $activation_date );
			update_post_meta( $agent_id, '_membership_interval', $interval );
			update_post_meta( $agent_id, '_membership_interval_unit', $interval_unit );
		}

		return $agent_id;
	}
endif;
