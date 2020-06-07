<?php
if( !function_exists( 're_ajax_contact_agent') ) :
	function re_ajax_contact_agent( $is_property_contact = false ){
		$response = '';
		$_POST = stripslashes_deep($_POST);
		$no_html	= array();

		$nonce = $_POST['security'];
		$agent_id = isset( $_POST['agent_id'] ) ? wp_kses( $_POST['agent_id'], $no_html ) : '';
		$property_id = isset( $_POST['property_id'] ) ? wp_kses( $_POST['property_id'], $no_html ) : '';
		$verify = wp_verify_nonce( $nonce, 'noo-contact-agent-'.$agent_id );
		if( $is_property_contact && ( empty( $property_id ) || !is_numeric( $property_id ) ) ) {
			$verify = false;
		}

		$is_recaptcha  = re_get_property_contact_setting('recaptcha',false);

		if(false != $verify){
			$error = array();
			$name = isset( $_POST['name'] ) ? wp_kses( $_POST['name'], $no_html ) : '';
			$email = isset( $_POST['email'] ) ? wp_kses( $_POST['email'], $no_html ) : '';
			$message = isset( $_POST['message'] ) ? wp_kses( $_POST['message'], $no_html ) : '';
			$recaptcha = isset($_POST['g-recaptcha-response'] ) ? wp_kses($_POST['g-recaptcha-response'] ,$no_html) : '';
			if($name===null || $name===array() || $name==='' || empty($name) && is_scalar($name) && trim($name)===''){
				$error[] = array(
					'field'=>'name',
					'message'=>__("Please fill the required field.",'noo')
				);
			}
			if($email===null || $email===array() || $email==='' || empty($email) && is_scalar($email) && trim($email)===''){
				$error[] = array(
						'field'=>'email',
						'message'=>__("Please fill the required field.",'noo')
				);
			}else{
				$pattern='/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/';
				$valid=is_string($email) && strlen($email)<=254 && (preg_match($pattern,$email));
				if(!$valid){
					$error[] = array(
						'field'=>'email',
						'message'=>__("Email address seems invalid.",'noo')
					);
				}
			}
			if($message===null || $message===array() || $message==='' || empty($message) && is_scalar($message) && trim($message)===''){
				$error[] = array(
					'field'=>'message',
					'message'=>__("Please fill the required field.",'noo')
				);
			}
			if ($is_recaptcha) {
				if ($recaptcha===null || $recaptcha===array() || $recaptcha ==='' || empty($recaptcha) && is_scalar($recaptcha) && trim($recaptcha)==='') {
					$error[] = array(
						'field' => 'g-recaptcha-response',
						'message' => __('Please enter reCAPTCHA','noo')
					);
				}
				else{
					$secret = $keysite =re_get_property_contact_setting('key_secret');
					$verifyResponse = file_get_contents( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $recaptcha );
					$responseData   = json_decode( $verifyResponse );
				}
			}
			$response = array('error'=>$error,'msg'=>'');
			if(!empty($error)){
				wp_send_json($response);
			}
			if($agent = get_post($agent_id) || $responseData->success){
				$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
				$agent_email = get_post_meta($agent_id,'_noo_agent_email',true);

				$headers = 'From: ' . $name . ' <' . $email . '>' . "\r\n";
				$email_content = '';
					
				if( $is_property_contact ) {
					$property_title = get_the_title( $property_id );
					$property_link = get_permalink( $property_id );

					$email_content = sprintf( __("%s just sent you a message via %s's page", 'noo'), $name, $property_title) . "<br/><br/>";
					$email_content .= __("----------------------------------------------", 'noo') . "<br/><br/>";
					$email_content .= $message . "<br/><br/>";
					$email_content .= __("----------------------------------------------", 'noo') . "<br/><br/>";
					$email_content .= sprintf( __("You can reply to this email to respond or send email to %s", 'noo'), $email) . "<br/><br/>";
					$email_content .= sprintf( __("Check %s's details at %s", 'noo'), $property_title, $property_link) . "<br/><br/>";
				} else {
					$agent_link = get_permalink( $agent_id );

					$email_content = sprintf( __("%s just sent you a message via your profile", 'noo'), $name) . "<br/><br/>";
					$email_content .= __("----------------------------------------------", 'noo') . "<br/><br/>";
					$email_content .= $message . "<br/><br/>";
					$email_content .= __("----------------------------------------------", 'noo') . "<br/><br/>";
					$email_content .= sprintf( __("You can reply to this email to respond or send email to %s", 'noo'), $email) . "<br/><br/>";
					$email_content .= sprintf( __("Check your details at %s", 'noo'), $agent_link) . "<br/><br/>";
				}

				$email_content = apply_filters('noo_agent_contact_message', $email_content, $agent_id, $name, $email, $message ,$recaptcha);
					
				do_action('before_noo_agent_contact_send_mail', $agent_id, $name, $email, $message , $recaptcha);

				$noo_cc_mail_to = re_get_property_contact_setting('cc_mail_to', get_option('noo_cc_mail_to', '') );
				if ( !empty( $noo_cc_mail_to ) ) {
					$agent_email = $agent_email . ',' . $noo_cc_mail_to;
					$temp_headers = array();
					$temp_headers[] = $headers;
					$temp_headers[] = 'Cc: ' . $noo_cc_mail_to;
					$headers = $temp_headers;
				}

				noo_mail($agent_email,
					sprintf( __("[%s] New message from [%s]", 'noo'), $blogname, $name),
					$email_content, $headers);

				do_action('after_noo_agent_contact_send_mail', $agent_id, $name, $email, $message ,$recaptcha);
			}

			$response['msg'] = __('Your message was sent successfully. Thanks.','noo');
			wp_send_json($response);
		}
		die;
	}

	//Ajax Contact Agent
	add_action('wp_ajax_noo_contact_agent', 're_ajax_contact_agent');
	add_action('wp_ajax_nopriv_noo_contact_agent', 're_ajax_contact_agent');
endif;

if( !function_exists( 're_ajax_contact_agent_property') ) :
	function re_ajax_contact_agent_property(){
		re_ajax_contact_agent( true );
	}
	add_action('wp_ajax_noo_contact_agent_property', 're_ajax_contact_agent_property');
	add_action('wp_ajax_nopriv_noo_contact_agent_property', 're_ajax_contact_agent_property');
endif;
