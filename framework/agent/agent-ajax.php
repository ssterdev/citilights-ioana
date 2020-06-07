<?php
if( !function_exists( 're_ajax_get_agent_properties') ) :
	function re_ajax_get_agent_properties(){
		// global $noo_show_sold;
		// $noo_show_sold = true;
		$agent_id = $_POST['agent_id'];
		$page = $_POST['page'];

		$args = array(
				'paged'=>$page,
				'posts_per_page' =>4,
				'post_status'    => 'publish',
				'post_type'      => 'noo_property',

		);
		if ($agent_id > 0) {
			$args['meta_query'] =
			 array(
				array(
				'key' => '_agent_responsible',
				'value' => $agent_id,
				),
			);
		}
		$args = apply_filters('noo_agent_property_query', $args);
		$r = new WP_Query($args);

		$args = array(
			'query' => $r,
			'title' => __('My Properties','noo'),
			'show_pagination ' => true, //
			'ajax_pagination' => true, //
			'ajax_content' => true, //
		);
		ob_start();
		re_property_loop($args);
		$ouput = ob_get_clean();
		wp_reset_query();
		// $noo_show_sold = false;
		wp_send_json(array('content'=>trim($ouput)));
	}
	add_action('wp_ajax_noo_agent_ajax_property', 're_ajax_get_agent_properties');
	add_action('wp_ajax_nopriv_noo_agent_ajax_property', 're_ajax_get_agent_properties');
endif;

if( !function_exists( 're_ajax_agent_update_profile') ) :
	function re_ajax_agent_update_profile() {
		if ( !is_user_logged_in() ) {
			re_ajax_exit( __( 'You are not logged in yet', 'noo' ) );
		}

		if( !check_ajax_referer('submit_profile', '_noo_profile_nonce', false) ) {
			re_ajax_exit( __( 'Your session is expired or you submitted an invalid form.', 'noo' ) );
		}

		$current_user = wp_get_current_user();

		$user_id		= $current_user->ID;
		$submit_user_id	= intval( $_POST['user_id'] );
		if( $user_id != $submit_user_id ) {
			re_ajax_exit( __('You can\'t edit account of other users.', 'noo') );
		}

		$agent_id		= get_user_meta($user_id, '_associated_agent_id', true );
		$submit_agent_id	= intval( $_POST['agent_id'] );
		if( empty( $agent_id ) && empty( $submit_agent_id ) ) {
			$agent_id = NooAgent::create_agent_from_user( $user_id );
			if(empty($agent_id))
				re_ajax_exit( __('There\'s an unknown error when creating an agent profile for your account. Please retry or contact Administrator.', 'noo') );
		} elseif( $agent_id != $submit_agent_id ) {
			re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
		}

		$no_html	= array();
		$prefix = RE_AGENT_META_PREFIX;

		$title			= wp_kses( $_POST['title'], $no_html );
		$email			= wp_kses( $_POST["{$prefix}_email"], $no_html );

		$desc			= wp_kses( $_POST['desc'], $no_html );
		$avatar			= wp_kses( $_POST['avatar'], $no_html );

		// Error data checking
		if( empty($title) ) {
			re_ajax_exit( __('Agent need a name', 'noo') );
		}
		if( empty($email) ) {
			re_ajax_exit( __('Agent need a valid email', 'noo') );
		}

		// ---- Update email user data
		$user_id = wp_update_user( array( 'ID' => $user_id, 'user_email' => $email ) );
		if ( is_wp_error( $user_id ) ) {
			re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
		}

		$agent = array(
			'ID'			=> $agent_id,
			'post_title'	=> $title,
			'post_content'	=> $desc
		);

		if( 0 === wp_update_post( $agent ) ) {
			re_ajax_exit( __('There\'s an unknown error when updating your profile. Please retry or contact Administrator.', 'noo') );
		}

		set_post_thumbnail( $agent_id, $avatar );
		re_agent_save_custom_fields( $agent_id, $_POST );

		update_post_meta( $agent_id, "{$prefix}_avatar", $avatar );

		$socials = re_get_agent_socials();
		if( !empty( $socials ) ) {
			foreach ($socials as $social) {
				$field_id = "{$prefix}_{$social}";
				if( isset( $_POST[$field_id] ) ) {
					$value		= wp_kses( $_POST[$field_id], $no_html );
					update_post_meta( $agent_id, $field_id, $value );
				}
			}
		}

		re_ajax_exit( __( 'Your profile has been updated successfully', 'noo' ), true );
	}

	add_action( 'wp_ajax_nopriv_noo_ajax_update_profile', 're_ajax_agent_update_profile' );
	add_action( 'wp_ajax_noo_ajax_update_profile', 're_ajax_agent_update_profile' );
endif;

if( !function_exists( 're_ajax_agent_change_password') ) :
	function re_ajax_agent_change_password() {
		if ( !is_user_logged_in() ) {
			re_ajax_exit( __( 'You are not logged in yet', 'noo' ) );
		}

		if( !check_ajax_referer('submit_profile_password', '_noo_profile_password_nonce', false) ) {
			re_ajax_exit( __( 'Your session is expired or you submitted an invalid form.', 'noo' ) );
		}

		$current_user = wp_get_current_user();

		$user_id		= $current_user->ID;
		$submit_user_id	= intval( $_POST['user_id'] );
		if( $user_id != $submit_user_id ) {
			re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
		}

		$no_html			= array();
		$old_pass			= wp_kses( $_POST['old_pass'] ,$no_html) ;
		$new_pass			= wp_kses( $_POST['new_pass'] ,$no_html) ;
		$new_pass_confirm	= wp_kses( $_POST['new_pass_confirm'] ,$no_html) ;

		if( empty( $new_pass ) || empty( $new_pass_confirm ) ){
			re_ajax_exit( __('The new password is blank.', 'noo') );
		}

		if($new_pass != $new_pass_confirm){
			re_ajax_exit( __('Passwords do not match.', 'noo') );
		}

		$user = get_user_by( 'id', $user_id );
		if ( $user && wp_check_password( $old_pass, $user->data->user_pass, $user->ID) ){
			wp_set_password( $new_pass, $user->ID );
			$response['success'] = true;
			re_ajax_exit( __('Password updated successfully', 'noo'), true );
		} else {
			re_ajax_exit( __('Old Password is not correct', 'noo') );
		}

		re_ajax_exit();
	}

	add_action( 'wp_ajax_nopriv_noo_ajax_change_password', 're_ajax_agent_change_password' );
	add_action( 'wp_ajax_noo_ajax_change_password', 're_ajax_agent_change_password' );
endif;

if( !function_exists( 're_ajax_agent_change_property_status') ) :
	function re_ajax_agent_change_property_status() {
		if ( !is_user_logged_in() ) {
			re_ajax_exit( __( 'You are not logged in yet', 'noo' ) );
		}

		if( !check_ajax_referer('noo_status_property', 'nonce', false) ) {
			re_ajax_exit( __( 'Your session is expired or your action is invalid.', 'noo' ) );
		}

		$user_id = get_current_user_id();
		$agent_id			= get_user_meta($user_id, '_associated_agent_id',true );

		// Agent checking
		$submit_agent_id	= intval( $_POST['agent_id'] );
		if( $agent_id != $submit_agent_id ) {
			re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
		}

		$prop_id	= intval( $_POST['prop_id'] );

		if( empty( $agent_id ) || empty( $prop_id ) ) {
			re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
		}

		if( !NooAgent::can_edit( $agent_id ) || !NooAgent::is_owner( $agent_id, $prop_id ) ) {
			re_ajax_exit( __('You don\'t have the rights to update this property', 'noo') );
		}

		// update status
		$default_sold_status = get_option('default_property_status');
		if( !wp_set_post_terms( $prop_id, $default_sold_status, 'property_status' ) ) {
			re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
		}

		re_ajax_exit( __('Your property has been updated successfully', 'noo'), true );
	}

	add_action( 'wp_ajax_nopriv_noo_ajax_status_property', 're_ajax_agent_change_property_status' );
	add_action( 'wp_ajax_noo_ajax_status_property', 're_ajax_agent_change_property_status' );
endif;

if( !function_exists( 're_ajax_agent_set_featured_property') ) :
	function re_ajax_agent_set_featured_property() {
		if ( !is_user_logged_in() ) {
			re_ajax_exit( __( 'You are not logged in yet', 'noo' ) );
		}

		if( !check_ajax_referer('noo_featured_property', 'nonce', false) ) {
			re_ajax_exit( __( 'Your session is expired or your action is invalid.', 'noo' ) );
		}

		$user_id = get_current_user_id();
		$agent_id			= get_user_meta($user_id, '_associated_agent_id',true );

		// Agent checking
		$submit_agent_id	= intval( $_POST['agent_id'] );
		if( $agent_id != $submit_agent_id ) {
			re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
		}

		$prop_id	= intval( $_POST['prop_id'] );

		if( empty( $agent_id ) || empty( $prop_id ) ) {
			re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
		}

		if( !NooAgent::can_set_featured( $agent_id ) || ( get_post_status( $prop_id ) != 'publish' ) ) {
			re_ajax_exit( __('You don\'t have the rights to update this property', 'noo') );
		}

		$featured = get_post_meta( $prop_id, '_featured', true ) == 'yes';
		if( $featured ) {
			re_ajax_exit( __('This item is already a featured Property.', 'noo') );
		}

		if( !update_post_meta( $prop_id, '_featured', 'yes' ) ) {
			re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
		}

		NooAgent::decrease_featured_remain( $agent_id );
		re_ajax_exit( __('Your property has been updated successfully', 'noo'), true );
	}

	add_action( 'wp_ajax_nopriv_noo_ajax_featured_property', 're_ajax_agent_set_featured_property' );
	add_action( 'wp_ajax_noo_ajax_featured_property', 're_ajax_agent_set_featured_property' );
endif;

if( !function_exists( 're_ajax_agent_delete_property') ) :
	function re_ajax_agent_delete_property() {
		if ( !is_user_logged_in() ) {
			re_ajax_exit( __( 'You are not logged in yet', 'noo' ) );
		}

		if( !check_ajax_referer('noo_delete_property', 'nonce', false) ) {
			re_ajax_exit( __( 'Your session is expired or your action is invalid.', 'noo' ) );
		}

		$user_id = get_current_user_id();
		$agent_id			= get_user_meta($user_id, '_associated_agent_id',true );

		// Agent checking
		$submit_agent_id	= intval( $_POST['agent_id'] );
		if( $agent_id != $submit_agent_id ) {
			re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
		}
		
		$prop_id	= intval( $_POST['prop_id'] );

		if( empty( $agent_id ) || empty( $prop_id ) ) {
			re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
		}


		if( !NooAgent::can_edit( $agent_id ) || !NooAgent::is_owner( $agent_id, $prop_id ) ) {
			re_ajax_exit( __('You don\'t have the rights to delete this property', 'noo') );
		}

		// delete attachments
		$arguments = array(
			'numberposts' => -1,
			'post_type' => 'attachment',
			'post_parent' => $prop_id,
			'post_status' => null,
			'exclude' => get_post_thumbnail_id(),
			'orderby' => 'menu_order',
			'order' => 'ASC'
		);
		$post_attachments = get_posts($arguments);

		foreach ($post_attachments as $attachment) {
			wp_delete_post($attachment->ID);
		}

		if( !wp_delete_post( $prop_id ) ) {
			re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
		}

		re_ajax_exit( __('Your property has been deleted successfully', 'noo'), true );
	}

	add_action( 'wp_ajax_nopriv_noo_ajax_delete_property', 're_ajax_agent_delete_property' );
	add_action( 'wp_ajax_noo_ajax_delete_property', 're_ajax_agent_delete_property' );
endif;

if( !function_exists( 're_ajax_agent_login') ) :
	function re_ajax_agent_login() {
		if( !check_ajax_referer('noo_ajax_login', '_noo_login_nonce', false) ) {
			re_ajax_exit( __( 'Invalid form.', 'noo' ) );
		}

		$no_html     = array();
		$redirect_to = wp_kses ( $_POST['redirect_to'], $no_html);

		if ( is_user_logged_in() ) {
			re_ajax_exit( __( 'You\'ve already logged in. Redirecting ...', 'noo' ), true, $redirect_to );
		}

		$login_user  = wp_kses ( $_POST['log'], $no_html );
		$login_pwd   = wp_kses ( $_POST['pwd'], $no_html);
		$remember    = (bool) wp_kses ( $_POST['rememberme'], $no_html);

		if ( empty($login_user) ){
			re_ajax_exit( __( 'Username is empty!.', 'noo' ) );
		}
		if ( empty($login_pwd) ){
			re_ajax_exit( __( 'Password is empty!.', 'noo' ) );
		}

		wp_clear_auth_cookie();
		$info                   = array();
		$info['user_login']     = $login_user;
		$info['user_password']  = $login_pwd;
		$info['remember']       = $remember;
		$user_signon            = wp_signon( $info, true );

		if ( is_wp_error($user_signon) ){
			re_ajax_exit( __('Wrong username or password!', 'noo') );       
		} else {
			global $current_user;
			wp_set_current_user($user_signon->ID);
			do_action('set_current_user');
			$current_user = wp_get_current_user();
		}

		re_ajax_exit(__( 'Logged in successfully. Redirecting ...', 'noo' ), true, $redirect_to );
	}

	add_action( 'wp_ajax_nopriv_noo_ajax_login', 're_ajax_agent_login' );
	add_action( 'wp_ajax_noo_ajax_login', 're_ajax_agent_login' );
endif;

if( !function_exists( 're_ajax_agent_register') ) :
	function re_ajax_agent_register() {
		if( !check_ajax_referer('noo_ajax_register', '_noo_register_nonce', false) ) {
			re_ajax_exit( __( 'Invalid form.', 'noo' ) );
		}

		if ( is_user_logged_in() ) {
			re_ajax_exit( __( 'You\'ve already logged in.', 'noo' ) );
		}

		$no_html     = array();
		$user_login	= wp_kses ( $_POST['user_login'], $no_html );
		$user_email	= wp_kses ( $_POST['user_email'], $no_html);

		$sanitized_user_login = sanitize_user( $user_login );

		// is Simple reCAPTCHA active?
		if ( function_exists( 'wpmsrc_check' ) ) {

			// check for empty user response first (optional)
			if ( empty( $_POST['recaptcha_response_field'] ) ) {

				re_ajax_exit( __( 'Please complete the CAPTCHA.', 'noo' ) );

			} else {

				// check captcha
				$response = wpmsrc_check();
				if ( ! $response->is_valid ) {
					re_ajax_exit( __( 'The CAPTCHA was not entered correctly. Please try again.', 'noo' ) );
					 // $response['error'] contains the actual error message, e.g. "incorrect-captcha-sol"
				}

			}

		}

		// Check the username
		if ( $sanitized_user_login == '' ) {
			re_ajax_exit( __( 'Please enter a username.', 'noo' ) );
		}

		if ( ! validate_username( $user_login ) ) {
			re_ajax_exit(  __( 'This username is invalid because it uses illegal characters. Please enter a valid username.', 'noo' ) );
		}

		if ( username_exists( $sanitized_user_login ) ) {
			re_ajax_exit( __( 'This username is already registered. Please choose another one.', 'noo' ) );
		}

		if ( empty($user_email) ) {
			re_ajax_exit( __( 'Please type your e-mail address.', 'noo' ) );
		}

		if ( !is_email( $user_email ) ) {
			re_ajax_exit( __( 'The email address isn\'t correct.', 'noo' ) );
		}

		if ( email_exists( $user_email ) ) {
			re_ajax_exit( __( 'This email is already registered, please choose another one.', 'noo' ) );
		}

		$user_pass = wp_generate_password( 12, false );
		$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
		if ( ! $user_id || is_wp_error( $user_id ) ) {
			re_ajax_exit( __( 'Couldn\'t register you... please contact Administrator!', 'noo' ) );
		}

		update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.

		// Mimic the default email of Wordpress
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		$message  = sprintf(__('Username: %s', 'noo'), $sanitized_user_login) . "<br/>";
		$message .= sprintf(__('Password: %s', 'noo'), $user_pass) . "<br/>";
		$message .= NooAgent::get_login_url( true ) . "<br/>";

		noo_mail($user_email, sprintf(__('[%s] Your username and password', 'noo'), $blogname), $message);

		// notification to admin
		wp_new_user_notification( $user_id );

		re_ajax_exit(__( 'An email with the generated password was sent!', 'noo' ), true );
	}

	add_action( 'wp_ajax_nopriv_noo_ajax_register', 're_ajax_agent_register' );
	add_action( 'wp_ajax_noo_ajax_register', 're_ajax_agent_register' );
endif;

if( !function_exists( 're_ajax_agent_membership_payment') ) :
	function re_ajax_agent_membership_payment() {
		global $woocommerce;
		if ( !is_user_logged_in() ) {
			re_ajax_exit( __( 'You are not logged in yet', 'noo' ) );
		}

		// Check nonce
		if ( !isset($_POST['_noo_membership_nonce']) || !wp_verify_nonce($_POST['_noo_membership_nonce'],'noo_subscription') ){
			re_ajax_exit( __('Sorry, your session is expired or you submitted an invalid form.', 'noo') );
		}

		$user_id	= get_current_user_id();
		$agent_id	= get_user_meta($user_id, '_associated_agent_id',true );

		// Agent checking
		$submit_agent_id	= isset( $_POST['agent_id'] ) ? intval( $_POST['agent_id'] ) : '';
		if( empty( $agent_id ) && empty( $submit_agent_id ) ) {
			$agent_id = NooAgent::create_agent_from_user( $user_id );
			if( !$agent_id ) {
				re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
			}
		} elseif( !empty( $submit_agent_id ) && $agent_id != $submit_agent_id ) {
			re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
		}

		$package_id			= intval( $_POST['package_id'] );
		if( empty( $agent_id ) || empty( $package_id ) ) {
			re_ajax_exit( __('Please select a Membership Package', 'noo') );
		}
		//re_ajax_exit($_POST['agent_id']);
		$is_recurring		= isset( $_POST['recurring_payment'] ) ? (bool)( $_POST['recurring_payment'] ) : false;

		$recurring_time		= isset( $_POST['recurring_time'] ) ? intval( $_POST['recurring_time'] ) : 0;

		$type_payment = @$_POST['type_payment'];

		if ( $type_payment == 'woo' ) :
			$plan_package = @$_POST['plan'];
			$plan_price = get_post_meta( $package_id, '_noo_membership_price', true );
			$product_id = NooAgent::noo_create_product( $plan_package, $plan_price );
			// $billing_type = $is_recurring ? 'recurring' : 'onetime';
			// $total_price = floatval( get_post_meta( $package_id, '_noo_membership_price', true ) );
			// $agent		= get_post( $agent_id );
			// $package	= get_post( $package_id );
			// if( !$agent || !$package ) {
			// 	return false;
			// }
			// $title = $agent->post_title . ' - Purchase package: ' . $package->post_title;

			// $new_order_ID = NooPayment::create_new_order( 'membership', $billing_type, $package_id, $total_price, $agent_id, $title );

			// if( !$new_order_ID ) {
			// 	return false;
			// }
			//re_ajax_exit( __($product_id, 'noo') );
			$woocommerce->cart->empty_cart();
			if (  $woocommerce->cart->add_to_cart( $product_id ) ) :

				$woo_url = wc_get_checkout_url();
				re_ajax_exit( $woo_url, true );

			endif;
		else :

			$paypal_url = NooAgent::getMembershipPaymentURL( $agent_id, $package_id, $is_recurring, $recurring_time );
			if( !$paypal_url ) {
				re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
			}
			re_ajax_exit( $paypal_url, true );

		endif;
	}

	if( re_get_agent_setting('noo_membership_type', 'membership') == 'membership' ) {
		add_action( 'wp_ajax_nopriv_noo_ajax_membership_payment', 're_ajax_agent_membership_payment' );
		add_action( 'wp_ajax_noo_ajax_membership_payment', 're_ajax_agent_membership_payment' );
	}
endif;

if( !function_exists( 're_ajax_agent_listing_payment') ) :
	function re_ajax_agent_listing_payment() {
		global $woocommerce;
		if ( !is_user_logged_in() ) {
			re_ajax_exit( __( 'You are not logged in yet', 'noo' ) );
		}

		if( !check_ajax_referer('noo_listing_payment', '_noo_listing_nonce', false) ) {
			re_ajax_exit( __( 'Your session is expired or your action is invalid.', 'noo' ) );
		}

		$user_id	= get_current_user_id();
		$agent_id	= get_user_meta($user_id, '_associated_agent_id',true );

		// Agent checking
		$submit_agent_id	= intval( $_POST['agent_id'] );
		if( $agent_id != $submit_agent_id ) {
			re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
		}

		$prop_id	= intval( $_POST['prop_id'] );

		if( empty( $agent_id ) || empty( $prop_id ) ) {
			re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
		}

		if( !NooAgent::is_owner( $agent_id, $prop_id ) ) {
			re_ajax_exit( __('This is not your property', 'noo') );
		}

		$paid_listing	= (bool) get_post_meta( $prop_id, '_paid_listing', true );
		$featured		= get_post_meta( $prop_id, '_featured', true ) == 'yes';

		if( !NooMembership::is_submission() || ( $paid_listing && $featured ) ) {
			re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
		}

		$listing_price = floatval( esc_attr( re_get_agent_setting('noo_submission_listing_price') ) );
		$featured_price = floatval( esc_attr( re_get_agent_setting('noo_submission_featured_price') ) );

		$submit_featured = isset( $_POST['submission_featured'] ) ? (bool) $_POST['submission_featured'] : false;
		if( $paid_listing && !$submit_featured ) {
			re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
		}

		$total_price = 0;
		if( !$paid_listing ) $total_price += $listing_price;
		if( $submit_featured && !$featured ) $total_price += $featured_price;
		$type_payment = @$_POST['type_payment'];

		if ( $type_payment == 'woo' ) :

			$title_property = @$_POST['title_property'];
			$product_id = NooAgent::noo_create_product( $title_property, $total_price );
			$woocommerce->cart->empty_cart();
			if (  $woocommerce->cart->add_to_cart( $product_id ) ) :

				$woo_url = wc_get_checkout_url();
				re_ajax_exit( $woo_url, true );

			endif;
			//re_ajax_exit( __($total_price, 'noo') );

		else :

			$paypal_url = NooAgent::getListingPaymentURL( $agent_id, $prop_id,  $total_price, !$paid_listing, $submit_featured && !$featured );

			if( !$paypal_url ) {
				re_ajax_exit( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
			} else {
				re_ajax_exit( $paypal_url, true );
			}

		endif;
	}

	if( re_get_agent_setting('noo_membership_type', 'membership') == 'submission' ) {
		add_action( 'wp_ajax_nopriv_noo_ajax_listing_payment', 're_ajax_agent_listing_payment' );
		add_action( 'wp_ajax_noo_ajax_listing_payment', 're_ajax_agent_listing_payment' );
	}
endif;

