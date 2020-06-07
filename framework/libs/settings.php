<?php
if( !function_exists( 'noo_get_setting' ) ) :
	function noo_get_setting($group, $id = null ,$default = null){
		global $noo_setting_group;
		if(!isset($noo_setting_group[$group])){
			$noo_setting_group[$group] = get_option($group);
		}
		$group_setting_value = $noo_setting_group[$group];
		if(empty($id)) {
			return $group_setting_value;
		}

		if(isset($group_setting_value[$id])) {
			return $group_setting_value[$id];
		}

		return $default;
	}
endif;

if( !function_exists( 'noo_render_setting_form' ) ) :
	function noo_render_setting_form( $fields = array(), $option_group = '', $title = '' ){
		if( empty( $fields ) || !is_array( $fields ) || empty( $option_group ) ) {
			return;
		}

		settings_fields($option_group);
		?>
		<?php if( !empty( $title ) ) : ?>
			<h3><?php echo esc_html( $title ); ?></h3>
		<?php endif; ?>
		<table class="form-table" cellspacing="0">
			<tbody>
				<?php foreach ( $fields as $field ) : ?>
					<tr class="<?php echo $field['id']; ?>">
						<th>
							<?php esc_html($field['label']); ?>
							<?php if( isset( $field['label_desc'] ) && !empty( $field['label_desc'] ) ) : ?>
								<p><small><?php esc_html($field['label_desc']); ?></small></p>
							<?php endif; ?>
						</th>
						<td>
							<?php 
							if( isset( $field['callback'] ) ) {
								call_user_func($field['callback'], $field);
							} else {
								echo noo_render_setting_field( $field, $option_group );
							}
							if( !empty( $field['desc'] ) ) {
								echo '<p><small>' . $field['desc'] . '</small></p>';
							}
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
endif;

if( !function_exists( 'noo_render_setting_field' ) ) :
	function noo_render_setting_field( $args = null, $option_group = '' ) {
		$defaults = array(
			'id'=>'',
			'type'=>'',
			'default'=>'',
			'options'=>array(),
			'args'=>array()
		);
		$r = wp_parse_args($args,$defaults);
		extract($r);

		if( empty( $id ) || empty( $type ) ) {
			return '';
		}
		$value = !empty( $option_group ) ? noo_get_setting( $option_group, $id, $default) : get_option( $id, $default );
		$option_name = !empty( $option_group ) ? $option_group . '[' . $id . ']' : $id;

		$html = array();
		switch( $type ) {
			case 'text':
				$value = ( $value !== null && $value !== false ) ? ' value="' . $value . '"' : '';
				$value = empty( $value ) && ( $default != null && $default != '' ) ? ' placeholder="' . $default . '"' : $value;
				$html[] = '<input id="'.$id.'" type="text" name="' . $option_name . '" ' . $value . ' />';
				break;

			case 'textarea':
				$html[] = '<textarea id='.$id.' name="' . $option_name . '" placeholder="' . $default . '">' . ( $value ? $value : $default ) . '</textarea>';
				if( !empty( $desc ) ) {
					$html[] = '<p><small>' . $desc . '</small></p>';
				}
				break;

			case 'select':
				if( !is_array( $options ) ) {
					break;
				}

				$html[] ='<select id='.$id.' name="' . $option_name . '" >';
				foreach ( $options as $index => $option ) {
					$opt_value		= $option['value'];
					$opt_label		= $option['label'];
					$opt_selected	= ( $value == $opt_value ) ? ' selected="selected"' : '';

					$opt_id			= isset( $option['id'] ) ? ' '.$option['id'] : $id . '_' . $index;
					$opt_class		= isset( $option['class'] ) ? ' class="'.$option['class'].'"' : '';
					$opt_for = '';
					$html[] = '<option value="' . $opt_value  .'"' . $opt_for . $opt_class . $opt_selected . '>';
					$html[] = $opt_label;
					$html[] = '</option>';
				}
				$html[] = '</select>';
				break;
			case 'radio':
				if( !is_array( $options ) ) {
					break;
				}
				$html[] = '<fieldset>';
				foreach ( $options as $index => $option ) {
					$opt_value		= $option['value'];
					$opt_label		= $option['label'];
					$opt_checked	= ( $value == $opt_value ) ? ' checked="checked"' : '';

					$opt_id			= isset( $option['id'] ) ? ' '.$option['id'] : $id . '_' . $index;
					$opt_for		= ' for="' . $opt_id . '"';
					$opt_class		= isset( $option['class'] ) ? ' class="'.$option['class'].'"' : '';
					$html[] = '<label' . $opt_for . $opt_class . '>';
					$html[] = '<input id="' . $opt_id . '" type="radio" name="' . $option_name . '" value="' . $opt_value . '" class="radio"' . $opt_checked .'/>';
					$html[] = $opt_label . '</label>';
					$html[] = '<br/>';
				}
				$html[] = '</fieldset>';

				break;
			case 'checkbox':
				$checked = ( $value ) ? ' checked="checked"' : '';

				echo '<input type="hidden" name="' . $option_name . '" value="0" />';
				echo '<input type="checkbox" id="' . $id . '" name="' . $option_name . '" value="1"' . $checked . ' /> ';

				if ( isset( $child_fields ) && !empty( $child_fields ) && is_array( $child_fields ) ) : ?>
					<script>
						jQuery(document).ready(function($) {
							<?php
							foreach ( $child_fields as $option_value => $fields ) :
								if ( empty( $fields ) ) continue;
								$fields = explode( ',', $fields );
								foreach ( $fields as $child_field ) :
									if ( trim( $child_field ) == "" ) continue;
									?>
									$('.<?php echo trim( $child_field ); ?>').addClass('child_<?php echo esc_attr($id); ?> <?php echo esc_attr($id); ?>_val_<?php echo esc_attr($option_value); ?>');
								<?php endforeach;
							endforeach;
							?>

							var control    = jQuery('#<?php echo esc_attr($id); ?>');

							// Bind the toggle event, we use this event to enable unlimitedly chained child toggle.
							control.bind("toggle_children", function() {
								$this = jQuery(this);
								if($this.parents('.<?php echo esc_attr($id); ?>').hasClass('hide-option')) {
									jQuery('.child_<?php echo esc_attr($id); ?>').addClass("hide-option").find('input, select').trigger("toggle_children");

									return;
								}

								if($this.is( ':checked' )) {
									jQuery('.<?php echo esc_attr($id); ?>_val_off').addClass("hide-option").find('input, select').trigger("toggle_children");
									jQuery('.<?php echo esc_attr($id); ?>_val_on').removeClass("hide-option").find('input, select').trigger("toggle_children");
								} else {
									jQuery('.<?php echo esc_attr($id); ?>_val_on').addClass("hide-option").find('input, select').trigger("toggle_children");
									jQuery('.<?php echo esc_attr($id); ?>_val_off').removeClass("hide-option").find('input, select').trigger("toggle_children");
								}
							});

							// Trigger toggle event the first time
							control.trigger("toggle_children");

							// Trigger the toggle event when there's click
							control.click( function() {
								control.trigger("toggle_children");
							});
						});
					</script>
				<?php endif;

				break;
			case 'label':
				echo '<p>' . $default . '</p>';
				break;
			case 'image':
				$html[] = '<input type="text" id='.$id.' name="' . $option_name . '" value="' . $value . '" style="margin-bottom: 5px;">';
				if(function_exists( 'wp_enqueue_media' )){
					wp_enqueue_media();
				} else{
					wp_enqueue_style('thickbox');
					wp_enqueue_script('media-upload');
					wp_enqueue_script('thickbox');
				}
				$html[] = '<br>';
				$html[] = '<input id="'.$id.'_upload" class="button button-primary" type="button" value="' . __('Select Image','noo') . '">';
				$html[] = '<input id="'.$id.'_clear" class="button" type="button" value="' . __('Clear Image','noo') . '">';
				$html[] = '<br>';
				$html[] = '<div class="noo-thumb-wrapper">';
				if(!empty($value)) {
					$html[] = '	<img alt="" src="' . $value . '">';
				}
				$html[] = '</div>';
				$html[] = '<script>';
				$html[] = 'jQuery(document).ready(function($) {';
				if ( empty ( $value ) ) {
					$html[] = '	$("#'.$id.'_clear").css("display", "none");';
				}
				$html[] = '	$("#'.$id.'_upload").on("click", function(event) {';
				$html[] = '		event.preventDefault();';
				$html[] = '		var noo_upload_btn   = $(this);';
				$html[] = '		if(wp_media_frame) {';
				$html[] = '			wp_media_frame.open();';
				$html[] = '			return;';
				$html[] = '		}';

				$html[] = '		var wp_media_frame = wp.media.frames.wp_media_frame = wp.media({';
				$html[] = '			title: "' . __( 'Select or Upload your Image', 'noo' ) . '",';
				$html[] = '			button: {';
				$html[] = '				text: "' . __( 'Select', 'noo' ) . '"';
				$html[] = '			},';
				$html[] = '			library: { type: "image" },';
				$html[] = '			multiple: false';
				$html[] = '		});';

				$html[] = '		wp_media_frame.on("select", function(){';
				$html[] = '			var attachment = wp_media_frame.state().get("selection").first().toJSON();';
				$html[] = '			noo_upload_btn.siblings("#'.$id.'").val(attachment.url);';
				$html[] = '			noo_thumb_wraper = noo_upload_btn.siblings("noo-thumb-wrapper");';
				$html[] = '			noo_thumb_wraper.html("");';
				$html[] = '			noo_thumb_wraper.append(\'<img src="\' + attachment.url + \'" alt="" />\');';
				$html[] = '			noo_upload_btn.attr("value", "' . __( 'Change Image', 'noo' ) . '");';
				$html[] = '			$("#'.$id.'_clear").css("display", "inline-block");';
				$html[] = '		});';

				$html[] = '		wp_media_frame.open();';
				$html[] = '	});';

				$html[] = '	$("#noo_donate_modal_header_clear").on("click", function(event) {';
				$html[] = '		var noo_clear_btn = $(this);';
				$html[] = '		noo_clear_btn.hide();';
				$html[] = '		$("#'.$id.'_upload").attr("value", " ' . __( 'Select Image', 'noo' ) . '");';
				$html[] = '		noo_clear_btn.siblings("#'.$id.'").val("");';
				$html[] = '		noo_clear_btn.siblings(".noo-thumb-wrapper").html("");';
				$html[] = '	});';
				$html[] = '});';
				$html[] = '</script>';

				break;
		}

		return implode("\n", $html);
	}
endif;
