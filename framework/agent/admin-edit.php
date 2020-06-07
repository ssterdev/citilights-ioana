<?php
if( !function_exists( 're_agent_edit_enter_title' ) ) :
	function re_agent_edit_enter_title( $input ) {
		global $post_type;

		if ( RE_AGENT_POST_TYPE == $post_type )
			return __( 'Agent Name', 'noo' );

		return $input;
	}
	add_filter( 'enter_title_here', 're_agent_edit_enter_title' );
endif;

if( !function_exists( 're_agent_metabox' ) ) :
	function re_agent_metabox() {
		remove_meta_box( 'mymetabox_revslider_0', RE_AGENT_POST_TYPE, 'normal' );

		// Declare helper object
		$prefix = RE_AGENT_META_PREFIX;
		$helper = new NOO_Meta_Boxes_Helper( $prefix, array( 'page' => RE_AGENT_POST_TYPE ) );

		// New custom fields
		$meta_box = array(
				'id' => "detail",
				'title' => __('Detail Information', 'noo') ,
				'page' => 'noo_agent',
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array()
		);

		$fields = re_get_agent_custom_fields();
		if($fields){
			foreach ($fields as $field){
				if( !isset( $field['name'] ) || empty( $field['name'] ) ) continue;
				$field['type'] = !isset( $field['type'] ) || empty( $field['type'] ) ? 'text' : $field['type'];
				if( isset($field['is_default']) ) {
					if( isset( $field['is_disabled'] ) && ($field['is_disabled'] == 'yes') )
						continue;
					if( isset( $field['is_tax'] ) )
						continue;
					$id = $field['name'];
				} else {
					$id = re_agent_custom_fields_name($field['name']);
				}

				$type = $field['type'];
				if( $field['type'] == 'multiple_select' ) {
					$type = 'select';
					$field['multiple'] = true;
				}

				if( $field['type'] == 'number' ) {
					$type = 'text';
				}

				if( in_array( $field['type'], array( 'multiple_select', 'select', 'checkbox', 'radio', '') ) ) {
					$field['options'] = array();
					$field_value = noo_convert_custom_field_setting_value( $field );
					foreach ($field_value as $key => $label) {
						$field['options'][] = array(
							'label' => $label,
							'value' => $key
							);
					}

					if( $field['type'] == 'checkbox' ) {
						$type = 'multiple_checkbox';
					}
				}

				$new_field = array(
					'label' => isset( $field['label_translated'] ) ? $field['label_translated'] : @$field['label'] ,
					'id' => $id,
					'type' => $type,
					'options' => isset( $field['options'] ) ? $field['options'] : '',
					'std' => isset( $field['std'] ) ? $field['std'] : '',
				);

				if( isset( $field['multiple'] ) && $field['multiple'] ) {
					$new_field['multiple'] = true;
				}

				$meta_box['fields'][] = $new_field;
			}
		}

		$helper->add_meta_box($meta_box);

		

		$all_socials = noo_get_social_fields();
		$socials = re_get_agent_socials();

		if( $socials ) {
			// Social Network
			$meta_box = array(
				'id'           => "social_network",
				'title'        => __( 'Social Network', 'noo' ),
				'context'      => 'normal',
				'priority'     => 'core',
				'description'  => '',
				'fields'       => array()
			);

			foreach ($socials as $social) {
				if( !isset( $all_socials[$social] ) ) continue;

				$new_field = array(
					'label' => $all_socials[$social]['label'],
					'id' => $prefix . '_' . $social,
					'type' => 'text',
					'std' => ''
				);
				$meta_box['fields'][] = $new_field;
			}

			$helper->add_meta_box($meta_box);
		}

		if( NooMembership::is_membership() ) {
			$meta_box = array(
				'id'           => "{$prefix}_meta_box_membership_package",
				'title'        => __( 'Membership Package', 'noo' ),
				'context'      => 'side',
				'priority'     => 'default',
				'description'  => '',
				'fields'       => array(
					array(
						'id' => "_membership_package",
						'label' => __( 'Select Membership Package', 'noo' ),
						'desc' => '',
						'type' => 'membership_packages',
						'std' => '',
						'callback' => 're_render_agent_metabox_fields'
					)
				)
			);

			$helper->add_meta_box($meta_box);
		}

		// User metabox
		$meta_box = array(
			'id'           => "{$prefix}_meta_box_user",
			'title'        => __( 'Login Information', 'noo' ),
			'context'      => 'side',
			'priority'     => 'default',
			'description'  => __( 'Manage Login Information of this agent', 'noo' ),
			'fields'       => array(
				array(
					'id' => '_user_edit',
					'label' => ( re_agent_has_associated_user( get_the_ID() ) ? __( 'Edit Login Info', 'noo' ) : __( 'Create a Login Account', 'noo') ),
					'type' => 'checkbox',
					'std'  => 'off',
					'child-fields' => array(
						'on'   => '_user_username,_user_password'
					)
				),
				array(
					'id' => '_user_username',
					'label' => __( 'User Name', 'noo' ),
					'type' => 'username',
					'std' => '',
					'callback' => 're_render_agent_metabox_fields'
				),
				array(
					'id' => '_user_password',
					'label' => __( 'Password', 'noo' ),
					'type' => 'user_password',
					'std' => '',
					'callback' => 're_render_agent_metabox_fields'
				)
			),
		);

		$helper->add_meta_box($meta_box);
	}

	add_action( 'add_meta_boxes', 're_agent_metabox', 30 );
endif;

if( !function_exists( 're_render_agent_metabox_fields' ) ) :
	function re_render_agent_metabox_fields( $post, $id, $type, $meta, $std = null, $field = null ) {
		switch( $type ) {
			case 'agents':

				$value = $meta ? $meta : $std;
				$html = array();
				$html[] = '<select name="noo_meta_boxes[' . $id . ']" class="noo_agents_select" >';
				$html[] = '<option value=""' . selected( $value, '', false ) . '>'.__('- No Agent -', 'noo').'</option>';

				$args = array(
					'post_type'     => RE_AGENT_POST_TYPE,
					'posts_per_page' => -1,
					'post_status' => 'publish',
					'suppress_filters' => 0
				);
				
				$agents = get_posts($args); //new WP_Query($args);
				if(!empty($agents)){
					foreach ($agents as $agent){
						$html[] ='<option value="'.$agent->ID.'"' . selected( $value, $agent->ID, false ) . '>'.$agent->post_title.'</option>';
					}
				}
				$html[] = '</select>';
				echo implode( "\n", $html);
				break;

			case 'packages':

				$value = $meta ? $meta : $std;
				$html = array();
				$html[] = '<select name="noo_meta_boxes[' . $id . ']" class="noo_packages_select" >';
				$html[] = '<option value=""' . selected( $value, '', false ) . '></option>';

				$args = array(
					'post_type'     => NooMembership::MEMBERSHIP_POST_TYPE,
					'posts_per_page' => -1,
					'post_status' => 'publish',
					'suppress_filters' => 0
				);
				
				$packages = get_posts($args); //new WP_Query($args);
				if(!empty($packages)){
					foreach ($packages as $package){
						$html[] ='<option value="'.$package->ID.'"' . selected( $value, $package->ID, false ) . '>'.$package->post_title.'</option>';
					}
				}
				$html[] = '</select>';
				echo implode( "\n", $html);
				break;

			case 'username':
				$value = '';
				$disabled = '';
				
				if( re_agent_has_associated_user( get_the_ID() ) ) {
					$associated_user_id = get_post_meta( get_the_ID(), '_associated_user_id', true );
					$user = new WP_User( $associated_user_id );
					$value = ' value="' . $user->user_login . '"';
					$disabled = ' disabled="true"';
				}

				$value = empty( $value ) && ( $std != null && $std != '' ) ? ' placeholder="' . $std . '"' : $value;
				echo '<input id='.$id.' type="text" name="' . $id . '" ' . $value . $disabled . ' />';
				break;

			case 'user_password':
				$placeholder = re_agent_has_associated_user( get_the_ID() ) ? __( 'Unchanged', 'noo' ) : '';
				echo '<input id='.$id.' type="password" name="' . $id . '" placeholder="' . $placeholder . '" />';
				break;

			case 'membership_packages':
				if( !NooMembership::is_membership() ) {
					return;
				}

				$value = $meta ? $meta : $std;
				$html = array();
				if( $value != '' ) {
					$html[] = '<p>' . __('If you change agent\'s package, all the package information will be reset.','noo') . '</p>';
				}
				$html[] = '<select name="noo_meta_boxes[' . $id . ']" class="noo_package_select" >';
				if( re_get_agent_setting('noo_membership_freemium', true) ) {
					$html[] = '<option value=""' . selected( $value, '', false ) . '>'.__('Free Membership', 'noo').'</option>';
				}

				$args = array(
					'post_type'     => NooMembership::MEMBERSHIP_POST_TYPE,
					'posts_per_page' => -1,
					'post_status' => 'publish',
					'suppress_filters' => 0
				);
				$packages = get_posts($args);
				if(!empty($packages)){
					foreach ($packages as $package){
						$html[] ='<option value="'.$package->ID.'"' . selected( $value, $package->ID, false ) . '>'.$package->post_title.'</option>';
					}
				}

				$html[] = '</select>';

				$html[] = '<div id="noo-membership-packages-adder" class="noo-add-parent wp-hidden-children">';
				$html[] = '<h4> <a href="#noo-membership-packages-add" class="noo-add-toggle hide-if-no-js">';
				$html[] = __( '+ Add new Membership Package', 'noo' );
				$html[] = '</a></h4>';
				$html[] = '<p id="noo-membership-packages-add" class="category-add wp-hidden-child">';

				$html[] = '<label class="screen-reader-text" for="noo-membership-packages-title">' . __( 'Package Title', 'noo' ) . '</label>';
				$html[] = '<input type="text" name="noo-membership-packages-title" id="noo-membership-packages-title" class="form-required form-input-tip" placeholder="'.__( 'Package Title', 'noo' ) .'" aria-required="true"/>';
				$html[] = '<label class="screen-reader-text" for="noo-membership-packages-interval">' . __( 'Package Interval', 'noo' ) . '</label>';
				$html[] = '<input type="text" name="noo-membership-packages-interval" id="noo-membership-packages-interval" placeholder="'.__( 'Package Interval', 'noo' ) .'" style="width:64%;display: inline-block;float: left;margin-left: 0; margin-right: 0;height:28px;"/>';
				$html[] = '<select name="noo-membership-packages-interval_unit" id="noo-membership-packages-interval_unit" style="width:36%;display: inline-block;float: left; margin-left: 0; margin-right: 0;box-shadow: none;background-color:#ddd;">';
				$html[] = '<option value="day" selected="selected">' . __( 'Days', 'noo') . '</option>';
				$html[] = '<option value="week">' . __( 'Weeks', 'noo') . '</option>';
				$html[] = '<option value="month">' . __( 'Months', 'noo') . '</option>';
				$html[] = '<option value="year">' . __( 'Years', 'noo') . '</option>';
				$html[] = '</select>';
				$html[] = '<label class="screen-reader-text" for="noo-membership-packages-price">' . __( 'Package Price', 'noo' ) . '</label>';
				$html[] = '<input type="text" name="noo-membership-packages-price" id="noo-membership-packages-price" class="form-input-tip" placeholder="'.__( 'Package Price', 'noo' ) .'" aria-required="true"/>';
				$html[] = '<label class="screen-reader-text" for="noo-membership-packages-listing_num">' . __( 'Number of Listing', 'noo' ) . '</label>';
				$html[] = '<input type="text" name="noo-membership-packages-listing_num" id="noo-membership-packages-listing_num" class="form-input-tip" placeholder="'.__( 'Number of Listing', 'noo' ) .'" aria-required="true" style="width:64%;display: inline-block;" />';
				$html[] = '<label style="width:34%;display: inline-block;" for="noo-membership-packages-listing_num_unlimited"><input type="checkbox" name="noo-membership-packages-listing_num_unlimited" id="noo-membership-packages-listing_num_unlimited"/>' . __( 'Unlimited?', 'noo' ) . '</label>';
				$html[] = '<label class="screen-reader-text" for="noo-membership-packages-featured_num">' . __( 'Number of Featured', 'noo' ) . '</label>';
				$html[] = '<input type="text" name="noo-membership-packages-featured_num" id="noo-membership-packages-featured_num" class="form-input-tip" placeholder="'.__( 'Number of Featured', 'noo' ) .'" aria-required="true"/>';
				$html[] = '<input type="button" id="noo-membership-packages-add-submit" class="button" value="' . __( 'Add Membership Package', 'noo' ) . '" />';
				// $html[] = wp_nonce_field( 'noo-membership-packages_ajax_nonce', false );
				$html[] = '<span id="noo-membership-packages-ajax-response"></span>';
				$html[] = '</p>';

				$html[] = '</div>';

				echo implode( "\n", $html);
				break;
		}
	}
endif;

if( !function_exists( 're_agent_admin_save_post' ) ) :
	function re_agent_admin_save_post( $post_id, $post ) {
		if(!is_object($post) || !isset($post->post_type)) {
			return;
		}

		// Check if it's noo_agent
		if($post->post_type != RE_AGENT_POST_TYPE){
			return;
		}

		if( !isset( $_POST['noo_meta_boxes'] ) ) {
			return;
		}

		$noo_meta_boxes = $_POST['noo_meta_boxes'];

		$prefix = RE_AGENT_META_PREFIX;

		$email = !isset($noo_meta_boxes["{$prefix}_email"] ) ? '' : $noo_meta_boxes["{$prefix}_email"];
		$user_edit = isset($noo_meta_boxes['_user_edit'] ) && !empty( $noo_meta_boxes['_user_edit'] );

		if( re_agent_has_associated_user( $post_id ) ) {

			$associated_user_id = get_post_meta( $post_id, '_associated_user_id', true );

			// Update user
			$userdata = array();
			$user = new WP_User( $associated_user_id );
			if( $email != '' && $user->user_email != $email && !email_exists( $email ) ) {
				$userdata['user_email'] = $email;
			}

			if( $user_edit ) {
				if( isset($_POST['_user_password']) && !empty($_POST['_user_password']) ) {
					$userdata['user_pass'] = $_POST['_user_password'];
				}

				$noo_meta_boxes['_user_edit'] = '0';
				update_post_meta( $post_id, "{$prefix}_user_edit", '0' );
			}

			if( !empty($userdata) ) {
				$userdata['ID'] = $associated_user_id;
				wp_update_user( $userdata );
			}
		} elseif( $user_edit ) {

			$has_error = false;
			$err_message = array();
			$no_html = array();

			$user_login	= wp_kses ( $_POST['_user_username'], $no_html );

			$sanitized_user_login = sanitize_user( $user_login );

			// Check the username
			if ( $sanitized_user_login == '' ) {
				$has_error = true;
				$err_message[] = ( __( 'Please enter a username.', 'noo' ) );
			} elseif ( ! validate_username( $user_login ) ) {
				$has_error = true;
				$err_message[] = (  __( 'This username is invalid because it uses illegal characters. Please enter a valid username.', 'noo' ) );
			} elseif ( username_exists( $sanitized_user_login ) ) {
				$has_error = true;
				$err_message[] = ( __( 'This username is already registered. Please choose another one.', 'noo' ) );
			}

			if ( empty($email) ) {
				$has_error = true;
				$err_message[] = ( __( 'Please type your e-mail address.', 'noo' ) );
			} elseif ( !is_email( $email ) ) {
				$has_error = true;
				$err_message[] = ( __( 'The email address isn\'t correct.', 'noo' ) );
			} elseif ( email_exists( $email ) ) {
				$has_error = true;
				$err_message[] = ( __( 'This email is already registered, please choose another one.', 'noo' ) );
			}

			// Insert new user

			if( !$has_error ) {
				$pass = isset($_POST['_user_password']) && !empty($_POST['_user_password']) ? $_POST['_user_password'] : null;

				$arr = explode(' ',trim($post->post_title));
				$first_name = array_shift($arr);
				$last_name = implode($arr);

				$userdata = array(
					'user_login' => $_POST['_user_username'],
					'user_email' => $email,
					'user_pass' => $pass,
					'first_name' => $first_name,
					'last_name' => $last_name
				);

				$user_id = wp_insert_user( $userdata );

				if( is_wp_error( $user_id ) || empty( $user_id ) ) {
					$has_error = true;
					$err_message[] = ( __('There\'s an unknown error. Please retry or contact Administrator.', 'noo') );
				} else {
					update_post_meta( $post_id, '_associated_user_id', $user_id );
					update_user_meta( $user_id, '_associated_agent_id', $post_id);

					$noo_meta_boxes['_user_edit'] = '0';
					update_post_meta( $post_id, "{$prefix}_user_edit", '0' );
				}
			}

			if( $has_error ) {
				foreach ($err_message as $message) {
					add_settings_error(
						'create-account-for-agent',
						'create-account-for-agent',
						$message,
						'error'
						);
				}

				set_transient( 'settings_errors', get_settings_errors(), 30 );
			}
		}

		// Membership
		if( isset($noo_meta_boxes['_membership_package']) ) {
			NooAgent::set_agent_membership( $post_id, intval( $noo_meta_boxes['_membership_package']), time(), true);
		}
	}
	
	add_action('save_post', 're_agent_admin_save_post', 10, 2);
endif;