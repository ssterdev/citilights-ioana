<?php
if ( ! function_exists( 'noo_render_field' ) ) :
	function noo_render_field( $field = array(), $field_id = '', $value = '', $form_type = '' ) {
		switch ( $field['type'] ) {
			case "textarea":
				noo_render_textarea_field( $field, $field_id, $value, $form_type );
				break;
			case "select":
			case "multiple_select":
				noo_render_select_field( $field, $field_id, $value, $form_type );
				break;
			case "radio" :
				noo_render_radio_field( $field, $field_id, $value, $form_type );
				break;
			case "checkbox" :
				noo_render_checkbox_field( $field, $field_id, $value, $form_type );
				break;
			case "text" :
				noo_render_text_field( $field, $field_id, $value, $form_type );
				break;
			case "number" :
				noo_render_text_field( $field, $field_id, $value, $form_type );
				break;
			case "hidden" :
				noo_render_hidden_field( $field, $field_id, $value, $form_type );
				break;
			case "datepicker" :
				noo_render_datepicker_field( $field, $field_id, $value, $form_type );
				break;
			default :
				do_action( 'noo_render_field_' . $field['type'], $field, $field_id, $value, $form_type );
				break;
		}

    	if( $form_type != 'search' && isset( $field['desc'] ) && !empty( $field['desc'] ) ) : ?>
	    	<em><?php echo esc_html( $field['desc'] ); ?></em>
	    <?php endif;

		do_action( 'noo_after_render_field', $field, $field_id, $value, $form_type );
	}
endif;
if ( ! function_exists( 'noo_render_text_field' ) ) :
	function noo_render_text_field( $field = array(), $field_id = '', $value = '', $form_type = '' ) {
		$field_value = noo_convert_custom_field_setting_value( $field );
		$input_id = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		$class = isset($field['required']) && $field['required'] ? ' class="form-control required" required aria-required="true"' : ' class="form-control"';
		?>
		<input id="<?php echo esc_attr($input_id)?>" <?php echo $class; ?> type="text" name="<?php echo esc_attr($field_id)?>" value="<?php echo esc_attr($value); ?>" placeholder="<?php echo $field_value; ?>"/>
		<?php
	}
endif;
if ( ! function_exists( 'noo_render_textarea_field' ) ) :
	function noo_render_textarea_field( $field = array(), $field_id = '', $value = '', $form_type = '' ) {
		$field_value = noo_convert_custom_field_setting_value( $field );
		$input_id = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		$class = isset($field['required']) && $field['required'] ? ' class="form-control required" required aria-required="true"' : ' class="form-control"';
		?>
		<textarea <?php echo $class; ?> id="<?php echo esc_attr($input_id)?>"  name="<?php echo esc_attr($field_id)?>" placeholder="<?php echo $field_value; ?>" rows="8"><?php echo esc_html($value); ?></textarea>
		<?php
	}

endif;
if ( ! function_exists( 'noo_render_radio_field' ) ) :
	function noo_render_radio_field( $field = array(), $field_id = '', $value = '', $form_type = '' ) {
		$input_id = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		$class = isset($field['required']) && $field['required'] ? ' class="form-control required" required aria-required="true"' : ' class="form-control"';

		$field_value = noo_convert_custom_field_setting_value( $field );
		if( $form_type == 'search' ) {
			$field_value = array( '' => __('All', 'noo') ) + $field_value;
		}
		$value = is_array( $value ) ? reset( $value ) : $value;
		foreach ($field_value as $key => $label) :
			$checked = ( $key == $value ) ? 'checked="checked"' : '';
			?>
			<div class="form-control-flat">
				<label class="radio">
					<input type="radio" name="<?php echo esc_attr($field_id); ?>" value="<?php echo esc_attr($key); ?>" <?php echo $class; ?> <?php echo esc_attr($checked); ?>><i></i><?php echo esc_html($label); ?>
				</label>
			</div>
		<?php endforeach;
	}

endif;
if ( ! function_exists( 'noo_render_checkbox_field' ) ) :
	function noo_render_checkbox_field( $field = array(), $field_id = '', $value = '', $form_type = '' ) {
		$input_id = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		$class = isset($field['required']) && $field['required'] ? ' class="form-control required multiple" required aria-required="true"' : ' class="form-control multiple"';
		
		if( !is_array( $value ) ) $value = noo_json_decode( $value );

		$field_value = noo_convert_custom_field_setting_value( $field );
		foreach  ($field_value as $key => $label) :
			$checked = in_array($key, $value) ? 'checked="checked"' : '';
			?>
			<div class="form-control-flat">
				<label class="checkbox">
					<input name="<?php echo $field_id; ?>[]" type="checkbox" <?php echo $class; ?> <?php echo $checked; ?> value="<?php echo esc_attr( $key ); ?>" /><i></i> 
					<?php echo esc_html($label); ?>
				</label>
			</div>
		<?php endforeach;
	}

endif;
if ( ! function_exists( 'noo_render_select_field' ) ) :
	function noo_render_select_field( $field = array(), $field_id = '', $value = '', $form_type = '' ) {
		$input_id = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		$is_multiple_select = isset( $field['type'] ) && $field['type'] === 'multiple_select';

		$label = isset( $field['label_translated'] ) ? $field['label_translated'] : $field['label'];
		$value = ( $is_multiple_select && !is_array( $value ) ) ? noo_json_decode( $value ) : $value;

		$field_value = noo_convert_custom_field_setting_value( $field );
		$placeholder = $form_type != 'search' ? sprintf( __("Select %s",'noo'), $label ) : sprintf( __("All %s",'noo'), $label );
		if( !$is_multiple_select ) {
			$field_value = array( '0' => $placeholder ) + $field_value;
		}
		if ( $field_id == 'bedrooms' || $field_id == 'bathrooms') {
			asort($field_value, SORT_NUMERIC );
		}

		// if ( $field_id == '_noo_property_field__propertyid') {
		// 	asort($field_value, SORT_NUMERIC );
		// }
		$is_chosen = $is_multiple_select || ( count( $field_value ) > 1 );
		$is_chosen = apply_filters( 'noo_select_field_is_chosen', $is_chosen, $field, $field_id );

		$rtl_class = is_rtl() && $is_chosen ? ' chosen-rtl' : '';
		$chosen_class = $is_chosen ? ' form-control-chosen ignore-valid' : '';
		$chosen_class .= isset($field['required']) && $field['required'] ? ' jform-chosen-validate required' : '';

		$attrs = isset($field['required']) && $field['required'] ? ' class="form-control' . $rtl_class . $chosen_class . '" required aria-required="true"' : ' class="form-control ' . $rtl_class . $chosen_class . '"';

		?>
		<div class="noo-box-select">			
			<?php if( $is_multiple_select ) : ?>
				<select id="<?php echo esc_attr($input_id)?>" <?php echo $attrs; ?> name="<?php echo esc_attr($field_id); ?>[]" multiple="multiple" data-placeholder="<?php echo $placeholder; ?>">
			<?php else : ?>
				<select id="<?php echo esc_attr($input_id)?>" <?php echo $attrs; ?> name="<?php echo esc_attr($field_id); ?>" data-placeholder="<?php echo $placeholder; ?>">
			<?php endif; ?>
				<?php
					foreach  ($field_value as $key => $label) :
						if( is_array( $value ) ) {
							$selected = in_array($key, $value) ? 'selected="selected"' : '';
						} else {
							$selected = ( $key == $value ) ? 'selected="selected"' : '';
						}
						$class = !empty( $key ) ? $key : '';
						?>
						<option value="<?php echo $key; ?>" <?php echo $selected; ?> class="<?php echo esc_attr( $class ); ?>" ><?php echo esc_html($label); ?></option>
						<?php
					endforeach;
				?>
			</select>
		</div>
		<?php
	}
endif;
if ( ! function_exists( 'noo_render_hidden_field' ) ) :
	function noo_render_hidden_field( $field = array(), $field_id = '', $value = '', $form_type = '' ) {
		$field_value = noo_convert_custom_field_setting_value( $field );
		$input_id = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		?>
		<input id="<?php echo esc_attr($input_id)?>" class="form-control" type="hidden" name="<?php echo esc_attr($field_id)?>" value="<?php echo esc_attr($value); ?>" />
		<?php
	}
endif;
if ( ! function_exists( 'noo_render_datepicker_field' ) ) :
	function noo_render_datepicker_field( $field = array(), $field_id = '', $value = '', $form_type = '' ) {
		$class = isset($field['required']) && $field['required'] ? ' class="form-control jform-datepicker required" required aria-required="true"' : ' class="form-control jform-datepicker"';
		
		if( $form_type != 'search' ) : ?>
			<?php
				$date_value = is_numeric( $value ) ? date_i18n(get_option('date_format'), $value) : $value; 
				$value = is_numeric( $value ) ? $value : strtotime( $value );
			?>
    		<input type="text" value="<?php echo $date_value; ?>" <?php echo $class; ?> name="<?php echo esc_attr($field_id); ?>">
    		<input type="hidden" class="jform-datepicker_value" name="<?php echo esc_attr($field_id); ?>" value="<?php echo $value; ?>">
		<?php else : ?>
			<?php 
			$_start = isset($_GET[$field_id.'_start']) ? $_GET[$field_id.'_start'] : '';
			$_start_date = is_numeric( $_start ) ? date_i18n(get_option('date_format'), $_start) : $_start;
			$_start = is_numeric( $_start ) ? $_start : strtotime( $_start );

			$_end = isset($_GET[$field_id.'_end']) ? $_GET[$field_id.'_end'] : '';
			$_end_date = is_numeric( $_end ) ? date_i18n(get_option('date_format'), $_end) : $_end;
			$_end = is_numeric( $_end ) ? $_end : strtotime( $_end );
			?>
			<fieldset>
				<input type="text" value="<?php echo $_start_date; ?>" class="form-control half jform-datepicker_start" name="<?php echo esc_attr($field_id) . '_start';?>" placeholder="<?php echo __('Start', 'noo'); ?>">
    			<input type="hidden" class="jform-datepicker_start_value" class="form-control" name="<?php echo esc_attr($field_id) . '_start';?>" value="<?php echo $value; ?>">
				<input type="text" value="<?php echo $_end_date; ?>" class="form-control half jform-datepicker_end" name="<?php echo esc_attr($field_id) . '_end';?>" placeholder="<?php echo __('End', 'noo'); ?>">
    			<input type="hidden" class="jform-datepicker_end_value" class="form-control" name="<?php echo esc_attr($field_id) . '_end';?>" value="<?php echo $value; ?>">
			</fieldset>
		<?php endif;
	}
endif;
