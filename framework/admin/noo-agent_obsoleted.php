<?php
/**
 * Register NOO Agent.
 * This file register Item and Category for NOO Agent.
 *
 * @package    NOO CitiLights
 * @subpackage NOO Agent
 * @version    1.0.0
 * @author     Kan Nguyen <khanhnq@nootheme.com>
 * @copyright  Copyright (c) 2014, NooTheme
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       http://nootheme.com
 */

if(!class_exists('NooAgent')) :
	class NooAgent {

		const AGENT_SLUG = 'agents';
		const AGENT_POST_TYPE = 'noo_agent';
		const AGENT_META_PREFIX = '_noo_agent';

		public function __construct() {
			// add_action('init', array(&$this,'register_post_type'));
			add_shortcode('noo_recent_agents', array(&$this,'recent_agents_shortcode'));
			add_shortcode('noo_login_register', array(&$this,'login_register_shortcode'));
			add_shortcode('noo_membership_packages', array(&$this,'noo_membership_packages_shortcode'));
			if( is_admin() ) {
				// add_action( 'add_meta_boxes', array (&$this, 'remove_meta_boxes' ), 20 );
				// add_action( 'add_meta_boxes', array (&$this, 'add_meta_boxes' ), 30 );
				// add_filter( 'enter_title_here', array (&$this,'custom_enter_title') );
				// add_action( 'admin_enqueue_scripts', array (&$this,'enqueue_style_script') );
				// add_action( 'admin_menu', array(&$this, 'settings_sub_menu') );

				// Hide IDX page template when the plugin is not installed
				// add_action( 'admin_footer', array(&$this, 'hide_IDX_page_template'), 10 );
				
				// add_filter('manage_edit-' . RE_AGENT_POST_TYPE . '_columns', array(&$this, 'manage_edit_columns'));
				// add_action('manage_posts_custom_column', array(&$this, 'manage_posts_custom_column'));

				// add_filter('re_setting_tabs', array(&$this,'add_setting_agent_tab'), 99);
				// add_action('re_setting_agent', array(&$this,'agent_settings'));
			}

			// if( re_get_agent_setting('noo_membership_type', 'membership') != 'none' ) {
				// add_action('save_post', array(&$this, 'save_post'), 10, 2);
				// add_action('admin_notices', array(&$this, 'save_post_admin_notices'));
				// add_action('user_register', array(&$this, 'hide_admin_bar_front'));
				// add_action('show_user_profile', array(&$this, 'edit_user_profile'));
				// add_action('edit_user_profile_update', array(&$this, 'edit_user_profile_update'));
				// add_action('personal_options_update', array(&$this, 'edit_user_profile_update'));

				// add_action('transition_post_status', array(&$this, 'transition_post_status'), 10, 3);

				// Remove admin bar and redirect profile page to site interface 
				// if( !current_user_can('activate_plugins') ) {
				// 	add_action( 'wp_before_admin_bar_render', array(&$this, 'stop_admin_bar_render') );
				// 	add_action( 'admin_init', array(&$this, 'stop_admin_profile') );
				// }
			// }


			// Ajax for frontend functions
			// add_action( 'wp_ajax_nopriv_noo_ajax_update_profile', array(&$this, 'ajax_update_profile') );
			// add_action( 'wp_ajax_noo_ajax_update_profile', array(&$this, 'ajax_update_profile') );

			// add_action( 'wp_ajax_nopriv_noo_ajax_change_password', array(&$this, 'ajax_change_password') );
			// add_action( 'wp_ajax_noo_ajax_change_password', array(&$this, 'ajax_change_password') );

			// add_action( 'wp_ajax_nopriv_noo_ajax_status_property', array(&$this, 'ajax_status_property') );
			// add_action( 'wp_ajax_noo_ajax_status_property', array(&$this, 'ajax_status_property') );

			// add_action( 'wp_ajax_nopriv_noo_ajax_featured_property', array(&$this, 'ajax_featured_property') );
			// add_action( 'wp_ajax_noo_ajax_featured_property', array(&$this, 'ajax_featured_property') );

			// add_action( 'wp_ajax_nopriv_noo_ajax_delete_property', array(&$this, 'ajax_delete_property') );
			// add_action( 'wp_ajax_noo_ajax_delete_property', array(&$this, 'ajax_delete_property') );

			// add_action( 'wp_ajax_nopriv_noo_ajax_login', array(&$this, 'ajax_login') );
			// add_action( 'wp_ajax_noo_ajax_login', array(&$this, 'ajax_login') );
			// add_action( 'wp_ajax_nopriv_noo_ajax_register', array(&$this, 'ajax_register') );
			// add_action( 'wp_ajax_noo_ajax_register', array(&$this, 'ajax_register') );

			// if( re_get_agent_setting('noo_membership_type', 'membership') == 'membership' ) {
			// 	add_action( 'wp_ajax_nopriv_noo_ajax_membership_payment', array(&$this, 'ajax_membership_payment') );
			// 	add_action( 'wp_ajax_noo_ajax_membership_payment', array(&$this, 'ajax_membership_payment') );
			// }

			// if( re_get_agent_setting('noo_membership_type', 'membership') == 'submission' ) {
			// 	add_action( 'wp_ajax_nopriv_noo_ajax_listing_payment', array(&$this, 'ajax_listing_payment') );
			// 	add_action( 'wp_ajax_noo_ajax_listing_payment', array(&$this, 'ajax_listing_payment') );
			// }
		}

		public static function render_metabox_fields( $post, $id, $type, $meta, $std = null, $field = null ) {
			re_render_agent_metabox_fields( $post, $id, $type, $meta, $std, $field );
		}

		public static function create_agent_from_user( $user_id = null ) {
			return re_create_agent_from_user( $user_id );
		}

		public static function get_login_url( $no_redirect = false ) {
			return re_get_login_url( $no_redirect );
		}

		public static function check_logged_in_user() {
			re_check_logged_in_user();
		}

		public static function display_content($query='',$title=''){
			global $wp_query;
			if(!empty($query)){
				$wp_query = $query;
			}
			if(empty($title) && is_tax()) {
				$title = single_term_title( "", false );
			}

			ob_start();
	        include(locate_template("layouts/noo-agent-loop.php"));
	        echo ob_get_clean();
		}
		
		public function noo_membership_packages_shortcode($atts, $content = null){
			ob_start();
			include(locate_template("layouts/shortcode-membership-packages.php"));
			return ob_get_clean();
		}
		
		public function recent_agents_shortcode($atts, $content = null){
            $autoplay = $slider_time = $slider_speed ='';
            extract(shortcode_atts(array(
                'title' => __('Recent Agents', 'noo'),
                'number' => '6',
                'columns' => '3',
                'visibility' => '',
                'class' => '',
                'custom_style' => '',
                'autoplay' => 'true',
                'slider_time' => '5000',
				'slider_speed' => '300',
				'subtitle' => '',
				'layout_style' => 'style-1',
            ), $atts));
			ob_start();

			include(locate_template("layouts/shortcode-recent-agents.php"));
			return ob_get_clean();
		}

		public function login_register_shortcode($atts, $content = null){
			extract( shortcode_atts( array(
				'mode'              => 'both',
				'login_text'        => __( 'Already a member of CitiLights. Please use the form below to log in site.', 'noo' ),
				'show_register_link'=> false,
				'register_text'     => __( 'Don\'t have an account? Please fill in the form below to create one.', 'noo' ),
				'redirect_to'       => '',
				'hide_for_login'    => false,
				'visibility'        => '',
				'class'             => '',
				'custom_style'      => ''
			), $atts ) );

			wp_enqueue_script('noo-dashboard');

			$visibility       = ( $visibility      != ''     ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
			$class            = ( $class           != ''     ) ? 'recent-agents ' . esc_attr( $class ) : 'recent-agents';
			$class           .= noo_visibility_class( $visibility );

			$class = ( $class != '' ) ? ' class="' . esc_attr( $class ) . '"' : '';
			$custom_style = ( $custom_style != '' ) ? ' style="' . $custom_style . '"' : '';
			$col_class = ( $mode == 'both' ) ? 'col-md-6 col-sm-6' : 'col-md-12';
			$redirect_to = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : $redirect_to;
			$redirect_to = !empty( $redirect_to ) ? $redirect_to : noo_get_page_link_by_template( 'agent_dashboard.php' );
			if( $hide_for_login == true || $hide_for_login == 'true' ) {
				if( is_user_logged_in() ) {
					return;
				}
			}
			ob_start();
			include(locate_template("layouts/shortcode-login-register.php"));
			return ob_get_clean();
		}

		public static function get_package_id( $agent_id = null ) {
			return re_get_membership_package_id( $agent_id );
		}

		public static function get_listing_remain( $agent_id = null ) {
			return re_get_membership_listing_remain( $agent_id );
		}

		public static function get_featured_remain( $agent_id = null ) {
			return re_get_membership_featured_remain( $agent_id );
		}

		public static function get_expired_date( $agent_id = null ) {
			return re_get_membership_expired_date( $agent_id );
		}

		public static function has_associated_user( $agent_id = null ) {
			return re_agent_has_associated_user( $agent_id );
		}

		public static function is_expired( $agent_id = null ) {
			return re_is_membership_expired( $agent_id );
		}

		public static function can_add( $agent_id = null ) {
			return re_agent_can_add( $agent_id );
		}

		public static function can_edit( $agent_id = null ) {
			return re_agent_can_edit( $agent_id );
		}

		public static function can_delete( $agent_id = null ) {
			return re_agent_can_delete( $agent_id );
		}

		public static function can_set_featured( $agent_id = null ) {
			return re_agent_can_set_featured( $agent_id );
		}

		public static function is_owner( $agent_id = null, $prop_id = null ) {
			return re_agent_is_owner( $agent_id, $prop_id );
		}

		public static function get_membership_info( $agent_id = null ) {
			return re_get_membership_info( $agent_id );
		}

		public static function set_agent_membership( $agent_id = null, $package_id = null, $activation_date = null, $is_admin_edit = false ) {
			re_set_agent_membership( $agent_id, $package_id, $activation_date, $is_admin_edit );
		}

		public static function revoke_agent_membership( $agent_id = null, $package_id = null ) {
			re_revoke_agent_membership( $agent_id, $package_id );
		}

		public static function set_property_status( $agent_id = null, $prop_id = null, $status_type = '' ) {
			re_set_submission_property_status( $agent_id, $prop_id, $status_type );
		}

		public static function revoke_property_status( $agent_id = null, $prop_id = null, $status_type = '' ) {
			re_revoke_submission_property_status( $agent_id, $prop_id, $status_type );
		}

		public static function decrease_listing_remain( $agent_id = null ) {
			re_decrease_membership_listing_remain( $agent_id );
		}

		public static function decrease_featured_remain( $agent_id = null ) {
			re_decrease_membership_featured_remain( $agent_id );
		}

		public static function is_dashboard() {
			return re_is_dashboard_page();
		}

		public static function get_default_avatar_uri() {
			return re_get_default_avatar_uri();
		}

		public function noo_create_product( $name_product, $price ) {
			global $wpdb;
	      	$post_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE `post_title` = '$name_product' AND `post_type` = 'product' AND `post_status` = 'publish'" );
	      	$id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE `post_title` = '$name_product' AND `post_type` = 'product' AND `post_status` = 'publish'" );
		    if ( $post_count > 0 ) :
		    	//exit($id);
		    	$post = array(
					'ID'         => $id,
					'post_title' => $name_product,
					'post_type'  => "product",
			    );
				$post_id = wp_update_post( $post, __('Cant not update product', 'noo') );
		    else :
		    	$post = array(
					'post_author'  => 1,
					'post_content' => '',
					'post_status'  => "publish",
					'post_title'   => $name_product,
					'post_parent'  => '',
					'post_type'    => "product",
			    );
		    	$terms_slug = get_option( 'noo_donate_slug' );
		    	//$terms = get_term($terms_id, 'product_cat');
	     		$post_id = wp_insert_post( $post, __('Cant not create product', 'noo') );
	     		wp_set_object_terms( $post_id, $terms_slug, 'product_cat' );
				wp_set_object_terms( $post_id, $terms_slug, 'product_type' );
				add_post_meta( $post_id, 'check_donate', 1);
				update_post_meta( $post_id, '_price', $price );
				update_post_meta( $post_id, '_stock_status', 'instock');
				update_post_meta( $post_id, '_virtual', 'yes');
				update_post_meta( $post_id, '_downloadable', 'yes');
				update_post_meta( $post_id, '_sku', "");
				update_post_meta( $post_id, '_product_attributes', array());
				update_post_meta( $post_id, '_sold_individually', "" );
				update_post_meta( $post_id, '_manage_stock', "no" );
				update_post_meta( $post_id, '_backorders', "no" );
				update_post_meta( $post_id, '_stock', "" );
	     	endif;

	     	return $post_id;
		}

		public static function getMembershipPaymentURL( $agent_id = null, $package_id = null, $is_recurring = false, $recurring_time = 0 ) {
			if( empty( $agent_id ) ) {
				$user_id = get_current_user_id();
				if( !empty($user_id) ) {
					$agent_id = get_user_meta( $user_id, '_associated_agent_id', true );
				}
			}

			if( empty( $agent_id ) || empty( $package_id ) ) {
				return false;
			}

			$agent		= get_post( $agent_id );
			$package	= get_post( $package_id );
			if( !$agent || !$package ) {
				return false;
			}

			$billing_type			= $is_recurring ? 'recurring' : 'onetime';
			$total_price			= floatval( get_post_meta( $package_id, '_noo_membership_price', true ) );
			$title					= $agent->post_title . ' - Purchase package: ' . $package->post_title;
			$new_order_ID			= NooPayment::create_new_order( 'membership', $billing_type, $package_id, $total_price, $agent_id, $title );

			if( !$new_order_ID ) {
				return false;
			}

			$order					= array( 'ID' => $new_order_ID );
			$order['name']			= $agent->post_title;
			$order['email']			= esc_attr( get_post_meta( $agent_id, '_noo_agent_email', true ) );
			$order['item_name']		= __( 'Membership Payment', 'noo' );
			$order['item_number']	= $package->post_title;
			$order['amount']		= $total_price;
			$order['return_url']	= noo_get_page_link_by_template( 'agent_dashboard.php' );
			$order['cancel_url']	= noo_get_page_link_by_template( 'agent_dashboard.php' );
			if( $is_recurring ) {
				$order['is_recurring']	= $is_recurring;
				$order['p3']			= intval( get_post_meta( $package_id, '_noo_membership_interval', true ) );
				$order['t3']			= esc_attr( get_post_meta( $package_id, '_noo_membership_interval_unit', true ) );
				switch( $order['t3'] ) {
					case 'day':
					$order['t3'] = 'D';
					break;
					case 'week':
					$order['t3'] = 'W';
					break;
					case 'month':
					$order['t3'] = 'M';
					break;
					case 'year':
					$order['t3'] = 'Y';
					break;
				}

				$order['src']		= 1;
				$order['srt']		= $recurring_time;
				$order['sra']		= 1;
			}

			$nooPayPalFramework = nooPayPalFramework::getInstance();

			return $nooPayPalFramework->getPaymentURL( $order );
		}

		public static function getListingPaymentURL( $agent_id = null, $prop_id = null, $total_price = 0, $is_publish = false, $is_featured = false ) {
			if( empty( $agent_id ) ) {
				$user_id = get_current_user_id();
				if( !empty($user_id) ) {
					$agent_id = get_user_meta( $user_id, '_associated_agent_id', true );
				}
			}

			if( empty( $agent_id ) || empty( $prop_id ) || empty( $total_price ) ) {
				return false;
			}

			if( !$is_publish && !$is_featured ) {
				return false;
			}

			if( !NooAgent::is_owner( $agent_id, $prop_id ) ) {
				return false;
			}

			$agent		= get_post( $agent_id );
			$property	= get_post( $prop_id );
			if( !$agent || !$property ) {
				return false;
			}

			$payment_type			= '';
			if( $is_publish && $is_featured ) {
				$payment_type		= 'both';
			} elseif( $is_publish ) {
				$payment_type		= 'listing';
			} elseif( $is_featured) {
				$payment_type		= 'featured';
			}

			$title					= $agent->post_title . ' - Payment for ' . $property->post_title;
			$new_order_ID			= NooPayment::create_new_order( $payment_type, '', $prop_id, floatval( $total_price ), $agent_id, $title );

			if( !$new_order_ID ) {
				return false;
			}

			$order					= array( 'ID' => $new_order_ID );
			$order['name']			= $agent->post_title;
			$order['email']			= esc_attr( get_post_meta( $agent_id, '_noo_agent_email', true ) );
			$order['item_name']		= sprintf( __( 'Listing Payment for %s', 'noo' ), $property->post_title );
			$order['item_number']	= '';
			if( $is_publish ) $order['item_number'] .= __('Publish listing', 'noo');
			if( $is_featured ) $order['item_number'] .= __(' and make it Featured', 'noo');
			$order['amount']		= floatval( $total_price );
			$order['return_url']	= noo_get_page_link_by_template( 'agent_dashboard.php' );
			$order['cancel_url']	= noo_get_page_link_by_template( 'agent_dashboard.php' );

			$nooPayPalFramework = nooPayPalFramework::getInstance();

			return $nooPayPalFramework->getPaymentURL( $order );
		}
	}
endif;

new NooAgent();
