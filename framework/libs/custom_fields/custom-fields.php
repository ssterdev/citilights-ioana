<?php

if ( ! function_exists( 'noo_custom_fields_type' ) ) :
	function noo_custom_fields_type() {
		
		$types = array(
			'text'				=> __( 'Text', 'noo' ),
			'number'			=> __( 'Number', 'noo' ),
			'url'				=> __( 'URL', 'noo' ),
			'textarea'			=> __( 'Textarea', 'noo' ),
			'select'			=> __( 'Select', 'noo' ),
			'multiple_select'	=> __( 'Multiple Select', 'noo' ),
			'radio'				=> __( 'Radio', 'noo' ),
			'checkbox'			=> __( 'Checkbox', 'noo' ),
		);

		return apply_filters( 'noo_custom_fields_type', $types );
	}
endif;

if ( ! function_exists( 'noo_multiple_value_field_type' ) ) :
	function noo_multiple_value_field_type() {
		
		$types = array(
			'select',
			'multiple_select',
			'radio',
			'checkbox',
		);

		return apply_filters( 'noo_multiple_value_field_type', $types );
	}
endif;

if ( ! function_exists( 'noo_get_custom_fields' ) ) :
	function noo_get_custom_fields( $setting_name = '', $wpml_prefix = '' ) {
		
		$custom_fields = array();
		if( is_string( $setting_name ) ) {
			$custom_fields = noo_get_setting($setting_name, array());
			$custom_fields = isset( $custom_fields['custom_field'] ) ? $custom_fields['custom_field'] : $custom_fields;
		} elseif ( is_array( $setting_name ) ) {
			$custom_fields = isset( $setting_name['custom_field'] ) ? $setting_name['custom_field'] : $setting_name;
		}
		
		if( !$custom_fields || !is_array($custom_fields) ) {
			$custom_fields = array();
		}

		// __option__ is reserved for other setting
		if( isset( $custom_fields['__options__'] ) ) {
			unset( $custom_fields['__options__'] );
		}

		if( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			$wpml_prefix = empty( $wpml_prefix ) ? $setting_name . '_' : $wpml_prefix;
			foreach ($custom_fields as $index => $custom_field) {
				if( !is_array($custom_field) || !isset( $custom_field['name'] ) ) continue;
				
				$custom_fields[$index]['label_translated'] = isset( $custom_field['label'] ) ? apply_filters('wpml_translate_single_string', $custom_field['label'], 'NOO Custom Fields', $wpml_prefix. sanitize_title($custom_field['name']), apply_filters( 'wpml_current_language', null ) ) : '';
			}
		}

		return $custom_fields;
	}
endif;

if ( ! function_exists( 'noo_merge_custom_fields' ) ) :
	function noo_merge_custom_fields( $default_fields = array(), $custom_fields = array() ) {
		
		// $custom_fields = array_merge( array_diff_key($default_fields, $custom_fields), $custom_fields );
		foreach ( array_reverse( $default_fields ) as $key => $field ) {
			if( array_key_exists( $key, $custom_fields ) ) {
				$diff_keys = array_diff_key( $field, $custom_fields[$key] );
				foreach ($diff_keys as $index => $diff) {
					$custom_fields[$key][$index] = $diff;
				}
				$custom_fields[$key]['is_default'] = true;
				if( isset( $field['is_tax'] ) && $field['is_tax'] ) {
					// Not allow changing label with tax fields
					$custom_fields[$key]['label'] = isset( $field['label'] ) ? $field['label'] : $custom_fields[$key]['label'];
					unset( $custom_fields[$key]['label_translated']  );
				}
			} else {
				$custom_fields = array( $key => $field ) + $custom_fields;
			}
		}

		return $custom_fields;
	}
endif;

if( !function_exists( 'noo_custom_fields_admin_script' ) ) :
	function noo_custom_fields_admin_script(){
		wp_register_style( 'jquery-ui', NOO_FRAMEWORK_ADMIN_URI . '/assets/css/jquery-ui.tooltip.css' );
		wp_register_style( 'noo-custom-fields', NOO_FRAMEWORK_ADMIN_URI . '/assets/css/noo-custom-fields.css' , array( 'jquery-ui' ) );

		$custom_field_tmpl = '';
		$custom_field_tmpl.= '<tr>';
		$custom_field_tmpl.= '<td>';
		$custom_field_tmpl.= '<input type="text" value="" placeholder="'.esc_attr__('Field Name','noo').'" name="__name__[__i__][name]" class="field-name">';
		$custom_field_tmpl.= '</td>';
		$custom_field_tmpl.= '<td>';
		$custom_field_tmpl.= '<input type="text" value="" placeholder="'.esc_attr__('Field Label','noo').'" name="__name__[__i__][label]" class="field-label">';
		$custom_field_tmpl.= '</td>';
		$custom_field_tmpl.= '<td>';
		$custom_field_tmpl.= '<select name="__name__[__i__][type]" class="field-type">';
		$types = noo_custom_fields_type();
		if( !empty( $types ) ) {
			foreach ($types as $key => $label) {
				$custom_field_tmpl.= '<option value="' . $key . '">' . $label . '</option>';
			}
		}
		$custom_field_tmpl.= '</select>';
		$custom_field_tmpl.= '</td>';
		$custom_field_tmpl.= '<td>';
		$custom_field_tmpl.= '<textarea placeholder="'.esc_attr__('Field Value','noo').'" name="__name__[__i__][value]" class="field-value"></textarea>';
		$custom_field_tmpl.= '</td>';
		$custom_field_tmpl.= '<td>';
		$custom_field_tmpl.= '<input type="checkbox" name="__name__[__i__][required]" class="field-required" /> '.esc_attr__('Yes','noo');
		$custom_field_tmpl.= '</td>';
		$custom_field_tmpl.= '<td>';
		$custom_field_tmpl.= '<input class="button button-primary" onclick="return delete_custom_field(this);" type="button" value="'.esc_attr__('Delete','noo').'">';
		$custom_field_tmpl.= '</td>';
		$custom_field_tmpl.= '</tr>';

		$custom_field_tmpl = apply_filters( 'noo_custom_field_setting_template', $custom_field_tmpl );

		$nooCustomFieldL10n = array(
			'custom_field_tmpl'=>$custom_field_tmpl,
			'disable_text'=>__('Disable', 'noo'),
			'enable_text'=>__('Enable', 'noo'),
		);
		
		wp_register_script( 'noo-custom-fields', NOO_FRAMEWORK_ADMIN_URI . '/assets/js/noo-custom-fields.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-tooltip' ), null, true );
		wp_localize_script('noo-custom-fields', 'nooCustomFieldL10n', $nooCustomFieldL10n);
	}
	add_filter( 'admin_enqueue_scripts', 'noo_custom_fields_admin_script', 10, 2 );
endif;

if ( ! function_exists( 'noo_custom_fields_setting' ) ) :
	
	function noo_custom_fields_setting( $setting_name, $wpml_prefix = '', $custom_fields = array(), $default_fields = array() ) {
		if(isset($_GET['settings-updated']) && $_GET['settings-updated']) {
			if( defined( 'ICL_SITEPRESS_VERSION' ) ) {
				$wpml_prefix = empty( $wpml_prefix ) ? $setting_name . '_' : $wpml_prefix;
				foreach ($custom_fields as $field) {
					if( !isset($field['name']) || empty( $field['name'] ) ) continue;
					if( !isset($field['label']) || empty( $field['label'] ) ) continue;
					do_action( 'wpml_register_single_string', 'NOO Custom Fields', $wpml_prefix.sanitize_title($field['name']), $field['label'] );

					if( in_array( $field['type'], noo_multiple_value_field_type() ) ) {
						$list_option = explode( "\n", $field['value'] );
						$field_value = array();
						foreach ($list_option as $index => $option) {
							$option_key = explode( '|', $option );
							$option_key[0] = trim( $option_key[0] );
							if( empty( $option_key[0] ) ) continue;
							$option_key[1] = isset( $option_key[1] ) ? $option_key[1] : $option_key[0];
							$option_key[0] = sanitize_title( $option_key[0] );

							do_action( 'wpml_register_single_string', 'NOO Custom Fields Value', sanitize_title($field['name']).'_value_'.$option_key[0], $option_key[1] );
						}
					} else {
						if( isset($field['value']) || empty( $field['value'] ) ) {
							do_action( 'wpml_register_single_string', 'NOO Custom Fields Value', sanitize_title($field['name']).'_value', $field['value'] );
						}
					}
				}
			}
		}
		
		settings_fields( $setting_name );

		$blank_field = array( 'name' => '', 'label' => '', 'type' => 'text', 'value' => '', 'required' => '', 'is_disabled' => '' );
		
		// -- Check value
			$custom_fields = $custom_fields ? $custom_fields : array();
			$default_fields = $default_fields ? $default_fields : array();

		$fields = !empty( $default_fields ) ? noo_merge_custom_fields( $default_fields, $custom_fields ) : $custom_fields;
		$cf_types = noo_custom_fields_type();
		$key_types = array_keys( $cf_types );

		?>
		<h3><?php echo __('Custom Fields','noo')?></h3>
		<table class="form-table" cellspacing="0">
			<tbody>
				<tr>
					<td>
						<?php 
							$num_arr = count($fields) ? array_map( 'absint', array_keys($fields) ) : array();
							$num = !empty($num_arr) ? end($num_arr) : 1;
						?>
						<table class="widefat noo_custom_field_table" data-num="<?php echo esc_attr( $num ); ?>" data-field_name="<?php echo $setting_name; ?>" cellspacing="0" >
							<thead>
								<tr>
									<th style="padding: 9px 7px">
										<?php esc_html_e('Field Key','noo')?>
										<span class="help">
											<a href="#" title="<?php echo esc_attr__('The key used to save this field to database.<br/>Should only includes lower characters with no space.', 'noo' ); ?>" class="help_tip"><i class="dashicons dashicons-editor-help"></i></a>
									</th>
									<th style="padding: 9px 7px">
										<?php esc_html_e('Field Label','noo')?>
									</th>
									<th style="padding: 9px 7px">
										<?php esc_html_e('Field Type','noo')?>
									</th>
									<th style="padding: 9px 7px">
										<?php esc_html_e('Field Value/Params','noo')?>
										<span class="help">
											<a href="#" title="<?php echo esc_attr__('Default value or options for this field.<br/>
 - Field text, number or textarea use this Value as placeholder<br/>
 - Field select, multiple select, radio or checkbox generate the options from this Value using line break as separator. Sample options:<br/>
	value_1|Option 1<br/>
	Option 2 ( value can be obmitted )<br/>
	value_3|Option 3', 'noo' ); ?>" class="help_tip"><i class="dashicons dashicons-editor-help"></i></a>
										</span>
									</th>
									<th style="padding: 9px 7px">
										<?php esc_html_e('Is Mandatory?','noo')?>
									</th>
									<th style="padding: 9px 7px">
										<?php esc_html_e('Action','noo')?>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php  if(!empty($fields)): ?>
									<?php foreach ($fields as $field):
										$field = is_array( $field ) ? array_merge( $blank_field, $field ) : $blank_field; 
										if( !isset($field['name']) || empty($field['name'])) continue;
										$field['name'] = sanitize_title($field['name']);

										$key = $field['name'];
										$is_default = isset( $field['is_default'] );
										$is_disabled = $is_default && isset( $field['is_disabled'] ) && ($field['is_disabled'] == 'yes');
										$is_tax = $is_default && isset( $field['is_tax'] ) && $field['is_tax'];
										$field['type'] = isset($field['type']) && !empty( $field['type'] ) ? $field['type'] : 'text';
										$allowed_types = $is_default && isset($field['allowed_type']) ? $field['allowed_type'] : false;
										$required = !empty($field['required']) ? 'checked' : '';
									?>
									<tr data-stt="<?php echo esc_attr($key)?>" <?php echo ($is_disabled ? 'class="noo-disable-field"' : ''); ?>>
										<td>
											<input type="text" value="<?php echo esc_attr($field['name'])?>" <?php echo ( $is_default ? 'readonly="readonly"' : '' ); ?> placeholder="<?php _e('Field Key','noo')?>" name="<?php echo $setting_name; ?>[<?php echo esc_attr($key)?>][name]" class="field-name">
										</td>
										<td>
											<input type="text" value="<?php echo esc_attr($field['label'])?>" <?php echo ( $is_tax ? 'readonly="readonly"' : '' ); ?> placeholder="<?php _e('Field Label','noo')?>" name="<?php echo $setting_name; ?>[<?php echo esc_attr($key)?>][label]" class="field-label">
										</td>
										<td>
											<?php if( !empty( $allowed_types ) && is_array( $allowed_types ) ) : ?>
												<?php if( count( $allowed_types ) > 1 ) : ?>
													<select name="<?php echo $setting_name; ?>[<?php echo esc_attr($key)?>][type]" class="field-type">
														<?php foreach ($allowed_types as $value => $label) : ?>
															<option value="<?php echo $value; ?>" <?php selected( $field['type'], $value ); ?>><?php echo $label; ?></option>
														<?php endforeach; ?>
													</select>
												<?php else : ?>
													<?php $first_value = reset($allowed_types); ?>
													<input type="hidden"  name="<?php echo $setting_name; ?>[<?php echo esc_attr($key)?>][type]" value="<?php echo key($allowed_types); ?>">
													<?php echo $first_value; ?>
												<?php endif; ?>
											<?php elseif( in_array( $field['type'], $key_types ) ): ?>
												<select name="<?php echo $setting_name; ?>[<?php echo esc_attr($key)?>][type]" class="field-type">
													<?php foreach ($cf_types as $value => $label) : ?>
														<option value="<?php echo $value; ?>" <?php selected( $field['type'], $value ); ?>><?php echo $label; ?></option>
													<?php endforeach; ?>
												</select>
											<?php else : ?>
												<?php echo $field_['type']; ?>
											<?php endif; ?>
										</td>
										<td>
											<textarea <?php echo ( $is_tax ? ' disabled' : '' ); ?> placeholder="<?php _e('Field Value','noo')?>" name="<?php echo $setting_name; ?>[<?php echo esc_attr($key)?>][value]" class="field-value"><?php echo $field['value'];?></textarea>
										</td>
										<td>
											<input type="checkbox" value="true" name="<?php echo $setting_name; ?>[<?php echo esc_attr($key)?>][required]" <?php echo $required ?> class="field-required"/>
											<?php _e('Yes','noo')?>
										</td>
										<td>
											<?php if( $is_default ) : ?>
												<input type="hidden" value="<?php echo ($is_disabled ? 'yes' : 'no'); ?>" name="<?php echo $setting_name; ?>[<?php echo esc_attr($key)?>][is_disabled]">
												<input class="button button-primary" onclick="return toggle_disable_custom_field(this);" type="button" value="<?php echo ( $is_disabled ? __('Enable','noo') : __('Disable','noo') );?>">
											<?php else : ?>
												<input class="button button-primary" onclick="return delete_custom_field(this);" type="button" value="<?php _e('Delete','noo')?>">
											<?php endif; ?>
										</td>
									</tr>
									<?php endforeach; ?>
								<?php endif;?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="6">
										<input class="button button-primary" id="add_custom_field" type="button" value="<?php esc_attr_e('Add','noo')?>">
									</td>
								</tr>
							</tfoot>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}
endif;

if ( ! function_exists( 'noo_render_custom_field' ) ) :
	function noo_render_custom_field( $field = array(), $post_id = 0 ) {
		if( empty( $field_id ) ) {
			return;
		}

		$blank_field = array( 'name' => '', 'label' => '', 'type' => 'text', 'value' => '', 'required' => '' );
		$field = is_array( $field ) ? array_merge( $blank_field, $field ) : $blank_field;

		$value = !empty( $post_id ) ? get_post_meta( $post_id, $field_id, true ) : '';
		$value = !is_array($value) ? trim($value) : $value;

		$params = apply_filters( 'noo_render_custom_field_params', compact( 'field', 'field_id', 'value' ), $post_id );
		extract($params);

		$html = apply_filters( 'noo_render_custom_field', '', $field_id, $value, $field, $post_id );
		if( !empty( $html ) ) {
			echo $html;

			return;
		}
		
		noo_render_field( $field, $field_id, $value );
	}
endif;

if ( ! function_exists( 'noo_render_search_custom_field' ) ) :
	function noo_render_search_custom_field( $field = array(), $field_id = '' ) {
		$blank_field = array( 'name' => '', 'label' => '', 'type' => 'text', 'value' => '' );
		$field = is_array( $field ) ? array_merge( $blank_field, $field ) : $blank_field;

		if( empty( $field_id ) ) {
			return;
		}

		$params = apply_filters( 'noo_render_search_custom_field_params', compact( 'field', 'field_id', 'value' ), $post_id );
		extract($params);

		$field['required'] = ''; // no need for required fields in search form

		$value = isset($_GET[$field_id]) ? $_GET[$field_id] : '';
		$value = !is_array($value) ? trim($value) : $value;

		$html = apply_filters( 'noo_render_search_custom_field', '', $field_id, $value, $field );
		if( !empty( $html ) ) {
			echo $html;

			return;
		}
		
		noo_render_field( $field, $field_id, $value, 'search' );
	}
endif;

if ( ! function_exists( 'noo_convert_custom_field_setting_value' ) ) :
	
	function noo_convert_custom_field_setting_value( $field = array() ) {

		$type = isset($field['type'] ) ? $field['type'] : '';

		$field_value = isset( $field['value'] ) ? $field['value'] : '';
		if( in_array( $type, noo_multiple_value_field_type() ) ) {
			$list_option = is_array( $field_value ) ? $field_value : explode( "\n", $field_value );
			$field_value = array();
			foreach ($list_option as $index => $option) {
				$option_key = explode( '|', $option );
				$option_key[0] = trim( $option_key[0] );
				if( empty( $option_key[0] ) ) continue;
				$option_key[1] = isset( $option_key[1] ) ? $option_key[1] : $option_key[0];
				$option_key[0] = sanitize_title( $option_key[0] );

				if( isset( $field['no_translate'] ) && $field['no_translate'] ) {
					$field_value[$option_key[0]] = $option_key[1];
				} else {
					$field_value[$option_key[0]] = apply_filters('wpml_translate_single_string', $option_key[1], 'NOO Custom Fields Value', sanitize_title($field['name']).'_value_'.$option_key[0], apply_filters( 'wpml_current_language', null ) );
				}
			}
		} else {
			if( !isset( $field['no_translate'] ) || !$field['no_translate'] ) {
				$field_value = apply_filters('wpml_translate_single_string', $field_value, 'NOO Custom Fields Value', sanitize_title($field['name']).'_value', apply_filters( 'wpml_current_language', null ) );
			}
		}

		return apply_filters( 'noo_convert_custom_field_setting_value', $field_value, $field );
	}

endif;

if ( ! function_exists( 'noo_convert_custom_field_value' ) ) :
	
	function noo_convert_custom_field_value( $field = array(), $value = '' ) {

		$type = isset($field['type'] ) ? $field['type'] : '';
		$new_value = !is_array($value) ? trim($value) : $value;

		if( empty( $type ) || empty( $new_value ) ) return '';

		if( in_array( $type, noo_multiple_value_field_type() ) ) {
			$field_value = noo_convert_custom_field_setting_value( $field );

			if( in_array( $type, array( 'multiple_select', 'checkbox', 'radio' ) ) ) {
				$new_value = !is_array( $new_value ) ? noo_json_decode( $new_value ) : $new_value;

				foreach ($new_value as $index => $v) {
					if( empty( $v ) ) {
						unset( $new_value[$index] );
					} elseif( isset( $field_value[$v] ) ) {
						$new_value[$index] = $field_value[$v];
					}
				}
			} else { // select
				if(is_array($new_value)){
					foreach ($new_value as $key => $value) {
						$new_value[$key] = $field_value[$value];
					}
				}else{
					if( isset( $field_value[$new_value] ) ) {
						$new_value = $field_value[$new_value];
					}
				}
			}
		}

		if( $type == 'datepicker' ) {
			$new_value = date_i18n( get_option('date_format'), $new_value );
		}

		if( $type == 'url' ) {
			$new_value = esc_url( $new_value );
		}

		return apply_filters( 'noo_convert_custom_field_value', $new_value, $field, $value );
	}

endif;
