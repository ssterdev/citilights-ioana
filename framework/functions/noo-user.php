<?php
/**
 * Add/Remove user fields for NOO Framework.
 *
 * @package    NOO Framework
 * @subpackage NOO Function
 * @version    1.0.0
 * @author     Kan Nguyen <khanhnq@vietbrain.com>
 * @copyright  Copyright (c) 2014, NooTheme
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       http://nootheme.com
 */
// =============================================================================

if ( ! function_exists( 'noo_author_profile_fields' ) ) :
	function noo_author_profile_fields ( $contactmethods ) {
		
		$contactmethods['google_profile'] = __( 'Google+ Profile URL', 'noo');
		$contactmethods['twitter_profile'] = __( 'Twitter Profile URL', 'noo');
		$contactmethods['facebook_profile'] = __( 'Facebook Profile URL', 'noo');
		$contactmethods['linkedin_profile'] = __( 'LinkedIn Profile URL', 'noo');
		
		return $contactmethods;
	}
	add_filter( 'user_contactmethods', 'noo_author_profile_fields', 10, 1);
endif;


/**
 * This function using did process login/register form via ajax
 *
 * @package 	Citilights
 * @author 		KENT <tuanlv@vietbrain.com>
 * @version 	1.0
 */

if ( ! function_exists( 'noo_citilights_ajax_form_login' ) ) :
	
	function noo_citilights_ajax_form_login() {

		/**
		 * Check security
		 */
			check_ajax_referer( 'noo-security', 'security', esc_html__( 'Security Breach! Please contact admin!', 'noo' ) );

		/**
		 * Process
		 */

			if ( !empty( $_POST['process'] ) ) :

				if ( $_POST['process'] === 'login' ) :

					$user_login    = !empty( $_POST['user_login'] ) ? sanitize_text_field( $_POST['user_login'] ) : '';
					$user_password = !empty( $_POST['user_password'] ) ? sanitize_text_field( $_POST['user_password'] ) : '';
					$redirecturl    = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : '';

					if ( !empty( $user_login ) && !empty( $user_password ) ) :

						$info_user = array(
					        'user_login'    => $user_login,
					        'user_password' => $user_password,
					        'remember'      => true
					    );
					 
					    $user = wp_signon( $info_user, false );
					 
					    if ( is_wp_error( $user ) ) :

					        $response['msg']    = $user->get_error_message();
							$response['status'] = 'error';
					    
					    else :
					    	$response['redirecturl'] = $redirecturl;
					    	$response['msg']    = esc_html__( 'Login successfully! You are redirecting...', 'noo' );
							$response['status'] = 'success';	

					    endif;

					else :

				        $response['msg']    = esc_html__( 'Not empty filed user or password, please check again!', 'noo' );
						$response['status'] = 'error';
					    
					endif;

				elseif ( $_POST['process'] === 'register' ) :

					$user_login    = !empty( $_POST['user_login'] ) ? sanitize_text_field( $_POST['user_login'] ) : '';
					$user_password = !empty( $_POST['user_password'] ) ? sanitize_text_field( $_POST['user_password'] ) : '';
					$user_email    = !empty( $_POST['user_email'] ) ? sanitize_text_field( $_POST['user_email'] ) : '';
					$redirecturl    = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : '';


					$user_id = username_exists( $user_login );
					if ( !$user_id && email_exists($user_email) == false ) :

						$user_id = wp_create_user( $user_login, $user_password, $user_email );

						if ( !empty( $user_id ) ) :

							/**
							 * Create agent
							 */
								NooAgent::create_agent_from_user( $user_id );

							/**
							 * Login
							 */
							$info_user = array(
						        'user_login'    => $user_login,
						        'user_password' => $user_password,
						        'remember'      => true
						    );
						 
						    wp_signon( $info_user, false );

						    $response['redirecturl'] = $redirecturl;
							$response['msg']    = esc_html__( 'Create user successfully. You are logging into...', 'noo' );
							$response['status'] = 'success';							

						else :

							$response['msg']    = esc_html__( 'Not insert user to database, please contact admin!', 'noo' );
							$response['status'] = 'error';

						endif;

					elseif ( email_exists( $user_email ) ) :

						$response['msg']    = esc_html__( 'Email already exists, please check again!', 'noo' );
						$response['status'] = 'error';	

					elseif ( !empty( $user_id ) ) :

						$response['msg']    = esc_html__( 'User already exists, please check again!', 'noo' );
						$response['status'] = 'error';					

					else :

						$response['msg']    = esc_html__( 'Create user error, please contact admin!', 'noo' );
						$response['status'] = 'error';

					endif;

				
				elseif ( $_POST['process'] === 'forgot' ) :

					$user_login    = !empty( $_POST['user_forgot'] ) ? sanitize_text_field( $_POST['user_forgot'] ) : '';

					if ( !empty( $user_login ) ) :

						if( ! is_email( $user_login ) ) :

							$response['msg']    = esc_html__( 'Invalid username or e-mail address.', 'noo' );
							$response['status'] = 'error';

				        elseif( ! email_exists( $user_login ) ) :

							$response['msg']    = esc_html__( 'There is no user registered with that email address.', 'noo' );
							$response['status'] = 'error';

				        else :
				        
				            /**
				             * lets generate our new password
				             */
				           		$random_password = wp_generate_password( 12, false );
				            
				            /**
				             * Get user data by field and data, other field are ID, slug, slug and login
				             */
				            	$user = get_user_by( 'email', $user_login );
				            
					            $update_user = wp_update_user( 
					            	array (
										'ID'        => $user->ID, 
										'user_pass' => $random_password
					                )
					            );
				            
				            /**
				             * if update user return true then lets send user an email containing the new password
				             */
				            if ( $update_user ) :

								$to      = $user_login;
								$subject = esc_html__( 'Your new password', 'noo' );
								$sender  = get_option('name');


								$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
								if ( is_multisite() )
									$blogname = $GLOBALS['current_site']->site_name;

								$message = __( 'You reset your password successfully.<br /><br /> Your new password is: <b>%1$s</b><br /> Please notice that this is a password the system created automatically. You should change it after logging into your account. <br /><br /> Regards,%2$s' , 'noo' );
				                
				                $message = noo_citilights_html_content( sprintf( $message, $random_password, $blogname ) );
				                
				                // $headers[] = 'MIME-Version: 1.0' . "\r\n";
				                // $headers[] = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				                // $headers[] = "X-Mailer: PHP \r\n";
				                // $headers[] = 'From: ' . $sender . ' < ' . $user_login . '>' . "\r\n";
				                
				                $mail = noo_mail( $to, $subject, $message );
				                
				                if ( $mail ) :
					             
					                $response['msg']    = esc_html__( 'Check your email address for you new password.', 'noo' );
									$response['status'] = 'success';
								
								endif;
				                    
				            else :

				                $response['msg']    = esc_html__( 'Oops something went wrong updating your account.', 'noo' );
								$response['status'] = 'error';

				            endif;

				        endif;

					else :

				        $response['msg']    = esc_html__( 'Enter a username or e-mail address..', 'noo' );
						$response['status'] = 'error';
					    
					endif;

				else :

					$response['msg']    = esc_html__( 'Not support, please check again!', 'noo' );
					$response['status'] = 'error';

				endif;

			else :

				$response['msg']    = esc_html__( 'Lack of data, please check again!', 'noo' );
				$response['status'] = 'error';

			endif;

			wp_send_json( $response );

			die();

	}

	add_action( 'wp_ajax_noo_login', 'noo_citilights_ajax_form_login' );
	add_action( 'wp_ajax_nopriv_noo_login', 'noo_citilights_ajax_form_login' );

endif;