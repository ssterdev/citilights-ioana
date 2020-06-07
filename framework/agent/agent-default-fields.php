<?php
if( !function_exists( 're_get_agent_default_fields' ) ) :
	function re_get_agent_default_fields() {
		$prefix = RE_AGENT_META_PREFIX;
		$default_fields = array(
			"{$prefix}_email" => array(
					'name' => "{$prefix}_email",
					'label' => __('Email','noo'),
					'type' => 'text',
					'value' => '',
					'std' => 'yourname@email.com',
					'icon' => 'fa-envelope-square',
					'is_default' => true,
					'required' => false
				),
			"{$prefix}_position" => array(
					'name' => "{$prefix}_position",
					'label' => __('Position','noo'),
					'type' => 'text',
					'value' => '',
					'std' => __( 'Real Estate agent', 'noo' ),
					'icon' => 'fa-user',
					'is_default' => true,
					'required' => false
				),
			"{$prefix}_phone" => array(
					'name' => "{$prefix}_phone",
					'label' => __('Phone','noo'),
					'type' => 'text',
					'value' => '',
					'std' => '(123) 456789',
					'icon' => 'fa-phone',
					'is_default' => true,
					'required' => false
				),
			"{$prefix}_mobile" => array(
					'name' => "{$prefix}_mobile",
					'label' => __('Mobile','noo'),
					'type' => 'text',
					'value' => '',
					'std' => '(123) 456789',
					'icon' => 'fa-tablet',
					'is_default' => true,
					'required' => false
				),
			"{$prefix}_skype" => array(
					'name' => "{$prefix}_skype",
					'label' => __('Skype','noo'),
					'type' => 'text',
					'value' => '',
					'icon' => 'fa-skype',
					'is_default' => true,
					'required' => false
				),
			"{$prefix}_whatsapp" => array(
					'name' => "{$prefix}_whatsapp",
					'label' => __('Whatsapp','noo'),
					'type' => 'text',
					'value' => '',
					'std' => '',
					'icon' => 'fa-whatsapp',
					'is_default' => true,
					'is_disabled' => 'yes',
					'required' => false
				),
			"{$prefix}_website" => array(
					'name' => "{$prefix}_website",
					'label' => __('Website','noo'),
					'type' => 'text',
					'value' => '',
					'std' => 'http://',
					'icon' => 'fa-globe',
					'is_default' => true,
					'required' => false
				),
			"{$prefix}_address" => array(
					'name' => "{$prefix}_address",
					'label' => __('Address','noo'),
					'type' => 'text',
					'value' => '',
					'icon' => 'fa-map-marker',
					'is_default' => true,
					'required' => false
				),
			);

		return apply_filters( 're_agent_default_fields', $default_fields );
	}
endif;

if( !function_exists( 're_agent_hidden_email_form_field' ) ) :
	function re_agent_hidden_email_form_field( $field = array(), $agent_id = 0 )  {
		$field_id = esc_attr( $field['name'] );
		if( $field_id == '_noo_agent_email' ) {
			$current_user = wp_get_current_user();
			$value = !empty( $current_user->ID ) ? $current_user->user_email : '';

			$field['type'] = 'hidden';
			noo_render_field( $field, $field_id, $value );
		}
	}
	
	add_filter( 're_agent_disabled_form_field', 're_agent_hidden_email_form_field', 10, 2 );
endif;

if( !function_exists( 're_agent_email_form_field_params' ) ) :
	function re_agent_email_form_field_params( $args = array(), $agent_id = 0 )  {
		if( $args['field_id'] == '_noo_agent_email' ) {

			if( empty( $value ) ) {
				$current_user = wp_get_current_user();

				$args['value'] = !empty( $current_user->ID ) ? $current_user->user_email : '';
			}
		}

		return $args;
	}
	
	add_filter( 're_agent_render_form_field_params', 're_agent_email_form_field_params', 10, 2 );
endif;

