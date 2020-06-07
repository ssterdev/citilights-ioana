<?php
if( !function_exists( 're_property_admin_enqueue_scripts' ) ) :
	function re_property_admin_enqueue_scripts() {
		$custom_field_type= apply_filters('noo_property_custom_field_type', array(
			'text'=>__('Short text','noo'),
			'textarea'	=>__('Long text','noo'),
			'date'		=>__('Date','noo')
		
		));
		/*
		ob_start();
		?>
		<select name="noo_property_custom_filed[custom_field][__i__][type]">
			<?php foreach ($custom_field_type as $value=>$type):?>
				<option value="<?php echo esc_attr($value)?>"><?php esc_html($type)?></option>
			<?php endforeach;?>
		</select>
		<?php
		$type_html = ob_get_clean();
		*/
		$feature_tmpl='';
		$feature_tmpl .= '<tr>';
		$feature_tmpl .= '<td>';
		$feature_tmpl .= '<input type="text" value="" placeholder="'.esc_attr__('Auto generate','noo').'" name="noo_property_feature[features][code][]" disabled>';
		$feature_tmpl .= '</td>';
		$feature_tmpl .= '<td>';
		$feature_tmpl .= '<input type="text" value="" placeholder="'.esc_attr__('Feature Name','noo').'" name="noo_property_feature[features][label][]" class="regular-text">';
		$feature_tmpl .= '</td>';
		$feature_tmpl .= '<td>';
		$feature_tmpl .= '<input class="button button-primary" onclick="return delete_noo_property_feature(this);" type="button" value="'.esc_attr__('Delete','noo').'">';
		$feature_tmpl .= '</td>';
		$feature_tmpl .= '</tr>';
		
		$custom_field_tmpl = '';
		$custom_field_tmpl.= '<tr>';
		$custom_field_tmpl.= '<td>';
		$custom_field_tmpl.= '<input type="text" value="" placeholder="'.esc_attr__('Field Name','noo').'" name="noo_property_custom_filed[custom_field][__i__][name]">';
		$custom_field_tmpl.= '</td>';
		$custom_field_tmpl.= '<td>';
		$custom_field_tmpl.= '<input type="text" value="" placeholder="'.esc_attr__('Field Label','noo').'" name="noo_property_custom_filed[custom_field][__i__][label]">';
		$custom_field_tmpl.= '</td>';
		// $custom_field_tmpl.= '<td>';
		// $custom_field_tmpl.= ''.$type_html;
		// $custom_field_tmpl.= '</td>';
		$custom_field_tmpl.= '<td>';
		$custom_field_tmpl.= '<input class="button button-primary" onclick="return delete_noo_property_custom_field(this);" type="button" value="'.esc_attr__('Delete','noo').'">';
		$custom_field_tmpl.= '</td>';
		$custom_field_tmpl.= '</tr>';
		
		$noopropertyL10n = array(
			'feature_tmpl'=>$feature_tmpl,
			'custom_field_tmpl'=>$custom_field_tmpl,
		);
		wp_enqueue_style( 'noo-property', NOO_FRAMEWORK_ADMIN_URI . '/assets/css/noo-property-admin.css');
		wp_register_script( 'noo-property', NOO_FRAMEWORK_ADMIN_URI . '/assets/js/noo-property-admin.js', array( 'jquery','jquery-ui-sortable'), null, true );
		wp_localize_script('noo-property', 'noopropertyL10n', $noopropertyL10n);
		wp_enqueue_script('noo-property');
	}
	add_filter( 'admin_enqueue_scripts', 're_property_admin_enqueue_scripts', 10, 2 );
endif;

if( !function_exists( 're_property_admin_map_scripts') ) :
	function re_property_admin_map_scripts(){
		global $post;
		if(get_post_type() === 'noo_property'){
			re_property_enqueue_map_picker_script();
		}
	}

	add_action( 'admin_print_scripts-post.php', 're_property_admin_map_scripts' );
	add_action( 'admin_print_scripts-post-new.php', 're_property_admin_map_scripts' );
endif;

if( !function_exists( 're_admin_properties_page_state' ) ) :
	function re_admin_properties_page_state( $states = array(), $post = null ) {
		if( !empty( $post ) && is_object( $post ) ) {
			$archive_slug = re_get_property_setting('archive_slug');
			if( !empty( $archive_slug ) && $archive_slug == $post->post_name ) {
				$states['property_page'] = __('Properties Page', 'noo');
			}
		}

		return $states;
	}
	add_filter( 'display_post_states', 're_admin_properties_page_state', 10, 2 );
endif;

if( !function_exists( 're_admin_properties_page_notice' ) ) :
	function re_admin_properties_page_notice( $post_type = '', $post = null ) {
		if( !empty( $post ) && is_object( $post ) ) {
			$archive_slug = re_get_property_setting('archive_slug');
			if ( !empty( $archive_slug ) && $archive_slug == $post->post_name && empty( $post->post_content ) ) {
				add_action( 'edit_form_after_title', '_re_admin_properties_page_notice' );
				remove_post_type_support( $post_type, 'editor' );
			}
		}
	}
	add_action( 'add_meta_boxes', 're_admin_properties_page_notice', 10, 2 );

	function _re_admin_properties_page_notice() {
		echo '<div class="notice notice-warning inline"><p>' . __( 'You are currently editing the page that shows all your properties.', 'noo' ) . '</p></div>';
	}
endif;
