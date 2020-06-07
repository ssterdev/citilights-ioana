<?php

if( !function_exists( 're_get_property_custom_fields' ) ) :
	function re_get_property_custom_fields( $all = true ) {
		$custom_fields = noo_get_custom_fields( 'noo_property_custom_filed', 'noo_property_field_');

		if( $all ) {
			$default_fields = re_get_property_default_fields();
			// $custom_fields = array_merge( array_diff_key($default_fields, $custom_fields), $custom_fields );
			$custom_fields = noo_merge_custom_fields( $default_fields, $custom_fields );
		}

		return apply_filters('re_property_custom_fields' . ( $all ? '_all' : '' ), $custom_fields );
	}
endif;

if( !function_exists( 're_get_property_search_custom_fields' ) ) :
	function re_get_property_search_custom_fields() {
		$custom_fields = re_get_property_custom_fields();

		return apply_filters( 're_property_search_custom_fields', $custom_fields );
	}
endif;

if( !function_exists( 're_property_custom_fields_prefix' ) ) :
	function re_property_custom_fields_prefix() {
		return apply_filters( 're_property_custom_fields_prefix', '_noo_property_field_' );
	}
endif;

if ( ! function_exists( 're_get_property_field' ) ) :
	function re_get_property_field( $field_name = '' ) {
		
		$custom_fields = re_get_property_custom_fields();
		if( isset( $custom_fields[$field_name] ) ) {
			return $custom_fields[$field_name];
		}

		$field_name = re_property_custom_fields_name($field_name);
		if( isset( $custom_fields[$field_name] ) ) {
			return $custom_fields[$field_name];
		}

		return array();
	}
endif;

if( !function_exists( 're_property_custom_fields_name' ) ) :
	function re_property_custom_fields_name( $field_name = '' ) {
		if( empty( $field_name ) ) return '';

		return apply_filters( 're_property_custom_fields_name', re_property_custom_fields_prefix() . sanitize_title( $field_name ) );
	}
endif;

if( !function_exists( 're_get_property_custom_fields_option' ) ) :
	function re_get_property_custom_fields_option($key = '', $default = null){
		$custom_fields = noo_get_setting('noo_property_custom_filed', array());
		
		if( !$custom_fields || !is_array($custom_fields) ) {
			return $custom_fields = array();
		}

		if( isset($custom_fields['__options__']) && isset($custom_fields['__options__'][$key]) ) {

			return $custom_fields['__options__'][$key];
		}
	
		return $default;
	}
endif;

if( !function_exists( 're_property_custom_fields_menu' ) ) :
	function re_property_custom_fields_menu() {
		add_submenu_page(
			'edit.php?post_type=noo_property',
			__( 'Custom Fields', 'noo' ),
			__( 'Custom Fields', 'noo' ),
			'edit_theme_options', 'property_custom_field',
			're_property_custom_fields_setting' );
	}
	
	add_action( 'admin_menu', 're_property_custom_fields_menu' );
endif;

if( !function_exists( 're_property_custom_fields_setting' ) ) :
	function re_property_custom_fields_setting(){
		wp_enqueue_style('noo-custom-fields');
		wp_enqueue_script('noo-custom-fields');

		if(function_exists( 'wp_enqueue_media' )){
			wp_enqueue_media();
		}else{
			wp_enqueue_style('thickbox');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
		}

		$custom_fields = re_get_property_custom_fields();
		?>
		<div class="wrap">
			<form action="options.php" method="post">
				<?php 
				noo_custom_fields_setting( 
					'noo_property_custom_filed',
					'noo_property_field_',
					$custom_fields
				);
				$default_primary = apply_filters( 're_property_primary_fields', array( '_area', '_bedrooms', '_bathrooms' ) );

				$primary_fields = re_property_summary_fields();
				$primary_field_icons = re_property_summary_field_icons();
				?>
				<h3><?php echo __('Primary Fields', 'noo'); ?></h3>
				<p><?php echo __('Primary fields which will be show on the property listing page', 'noo'); ?></p>
				<table class="form-table" cellspacing="0">
					<tbody>
						<?php foreach ($default_primary as $index => $primary) :
							$field_val = isset( $primary_fields[$index] ) ? $primary_fields[$index] : $primary;
							$icon_val = isset( $primary_field_icons[$index] ) ? $primary_field_icons[$index] : $default_icons[$index];
						?>
							<tr>
								<th>
									<?php echo sprintf( __('Primary Field #%d','noo'), $index + 1 ); ?><br/><br/>
									<small><?php echo __('Image Icon', 'noo'); ?></small>
								</th>
								<td>
									<select class="large-text custom-field-select" name="noo_property_custom_filed[__options__][primary_fields][]">
										<?php foreach ( $custom_fields as $field ) : if( empty( $field['name'] ) ) continue; ?>
											<option <?php selected( $field_val, $field['name'] )?> value="<?php echo $field['name']; ?>"><?php echo $field['label']; ?></option>
										<?php endforeach; ?>
									</select><br/>
									<input name="noo_property_custom_filed[__options__][primary_field_icons][]" class="regular-text field-icon" type="text" value="<?php echo $icon_val; ?>"/>
									<input type="button" class="button button-primary select-icon-btn" name="" value="<?php _e( 'Select File', 'noo' ); ?>" />
								</td>
							</tr>
						<?php endforeach; ?>
						<script>
							jQuery(document).ready(function($) {

								$('.select-icon-btn').on('click', function(event) {
									event.preventDefault();

									var noo_upload_btn   = $(this);

									// if media frame exists, reopen
									if(wp_media_frame) {
										wp_media_frame.open();
										return;
									}

									// create new media frame
									// I decided to create new frame every time to control the selected images
									var wp_media_frame = wp.media.frames.wp_media_frame = wp.media({
										title: "<?php echo __( 'Select or Upload your File', 'noo' ); ?>",
										button: {
											text: "<?php echo __( 'Select', 'noo' ); ?>"
										},
										library: { type: 'image' },
										multiple: false
									});

									// when image selected, run callback
									wp_media_frame.on('select', function(){
										var attachment = wp_media_frame.state().get('selection').first().toJSON();
										noo_upload_btn.siblings('.field-icon').val(attachment.url);
									});

									// open media frame
									wp_media_frame.open();
								});
							});
						</script>
					</tbody>
				</table>
				<?php submit_button(__('Save Changes','noo')); ?>
			</form>
		</div>
		<?php
	}
endif;

if( !function_exists( 're_property_render_form_field') ) :
	function re_property_render_form_field( $field = array(), $property_id = 0 ) {
		$blank_field = array( 'name' => '', 'label' => '', 'type' => 'text', 'value' => '', 'required' => '', 'is_disabled' => '' );
		$field = is_array( $field ) ? array_merge( $blank_field, $field ) : $blank_field; 
		if( !isset( $field['name'] ) || empty( $field['name'] ) ) return;

		$field_id = '';
		if( isset( $field['is_default'] ) ) {
			if( isset( $field['is_disabled'] ) && ($field['is_disabled'] == 'yes') ) {
				return;
			}

			$field_id = $field['name'];
		} else {
			$field_id = re_property_custom_fields_name( $field['name'] );
		}

		$value = !empty( $property_id ) ? get_post_meta( $property_id, $field_id, true ) : '';
		$value = isset( $_REQUEST[$field_id] ) ? $_REQUEST[$field_id] : $value;
		$value = !is_array($value) ? trim($value) : $value;

		$params = apply_filters( 're_property_render_form_field_params', compact( 'field', 'field_id', 'value' ), $property_id );
		extract($params);

		$field_id = esc_attr($field_id);
		?>
		<div class="col-md-4">
			<div class="form-group s-prop-<?php echo $field_id; ?>">
				<label for="<?php echo $field_id; ?>"><?php echo(isset( $field['label_translated'] ) ? $field['label_translated'] : $field['label'])  ?></label>
				<?php noo_render_field( $field, $field_id, $value ); ?>
			</div>
		</div>
		<?php
	}
endif;

if( !function_exists( 're_property_render_search_field') ) :
	function re_property_render_search_field( $field = array() ) {
		$blank_field = array( 'name' => '', 'label' => '', 'type' => 'text', 'value' => '', 'required' => '', 'is_disabled' => '' );
		$field = is_array( $field ) ? array_merge( $blank_field, $field ) : $blank_field; 
		if( !isset( $field['name'] ) || empty( $field['name'] ) ) return;

		$field_id = isset( $field['is_default'] ) && $field['is_default'] ? ( $field['name'][0] == '_' ? substr($field['name'], 1) : $field['name'] ) : re_property_custom_fields_name( $field['name'] );

		$field['required'] = ''; // no need for required fields in search form

		$value = isset($_GET[$field_id]) ? $_GET[$field_id] : '';
		$value = !is_array($value) ? trim($value) : $value;

		$params = apply_filters( 're_property_render_search_field_params', compact( 'field', 'field_id', 'value' ) );
		extract($params);
			if ( $field['type'] == "text" ) {
				$field_meta_key = isset( $field['is_default'] ) && $field['is_default'] ? $field['name'] : $field_id;
				global $wpdb;
				$field['value'] = $wpdb->get_col('
						SELECT DISTINCT meta_value
						FROM '.$wpdb->postmeta.'
						LEFT JOIN '.$wpdb->posts.' ON '.$wpdb->postmeta.'.post_id = '.$wpdb->posts.'.ID
						WHERE meta_key = \''.$field_meta_key.'\' AND post_type = \'noo_property\' AND post_status = \'publish\'
						ORDER BY meta_value ');
				$field['type'] = 'select';
				$field['no_translate'] = true;
			}
			noo_render_field( $field, $field_id, $value, 'search' );
	}
endif;

if( !function_exists( 're_property_advanced_search_field' ) ) :
	function re_property_advanced_search_field( $field_id = '') {
		if( empty( $field_id ) ) return '';
		wp_enqueue_script( 'vendor-chosen' );
		wp_enqueue_style( 'vendor-chosen' );
		$field_count = 0;
   		if( re_get_property_search_setting('pos1','property_location') ) {
   			$field_count++;
   		}
   		if( re_get_property_search_setting('pos2','property_sub_location') ) {
   			$field_count++;
   		}
   		if( re_get_property_search_setting('pos3','property_status') ) {
   			$field_count++;
   		}
   		if( re_get_property_search_setting('pos4','property_category') ) {
   			$field_count++;
   		}
   		if( re_get_property_search_setting('pos5','_bedrooms') ) {
   			$field_count++;
   		}
   		if( re_get_property_search_setting('pos6','_bathrooms') ) {
   			$field_count++;
   		}
   		if( re_get_property_search_setting('pos7','_price') ) {
   			$field_count++;
   		}
   		if( re_get_property_search_setting('pos8','_area') ) {
   			$field_count++;
   		}
   		$style_field = 'style=width:25%';
   		
   		switch ($field_count) {
   			case '3':
   				$style_field = 'style=width:33.33% ';
   				break;
   			case '2':
   			case '1':
   				$style_field = 'style=width:50%';
   				break;
   			
   			default:
   				$style_field = 'style=width:25%';
   				break;
   		}

		$tax_fields = re_get_property_tax_fields();
		if( in_array( $field_id, array_keys( $tax_fields ) ) ) : ?>
			<div class="form-group s-prop-<?php echo $tax_fields[$field_id]['name']; ?>" <?php echo esc_attr($style_field) ?>>
				<?php
					$value = isset($_GET[$field_id]) ? $_GET[$field_id] : '';
					re_property_render_taxonomy_field( $field_id, $value, $tax_fields[$field_id]['label'], 'search' );
				?>
			</div>
		<?php else : ?>
				<?php
				switch ($field_id) {
					case '_price':
						re_property_render_price_search_field();
						break;
					case '_area':
						re_property_render_area_search_field();
						break;
					case 'keyword':
						$keyword_field = array(
							'name' => 'keyword',
							'label' => __('Keyword','noo'),
							'type' => 'text',
							'value' => __('Keyword','noo'),
							'is_default' => true,
							'is_disabled' => 'no'
							);
						$value = isset($_GET['keyword']) ? $_GET['keyword'] : '';
						?>
						<div class="form-group" <?php echo esc_attr($style_field) ?>>
							<?php noo_render_field( $keyword_field, 'keyword', $value, 'search' ); ?>
						</div>
						<?php break;
					case'_agent_responsible':
						$_agent_responsible = array(
							'post_type'     => RE_AGENT_POST_TYPE,
							'posts_per_page' => -1,
							'post_status' => 'publish',
							'suppress_filters' => 0
						);
						$agents = get_posts($_agent_responsible); //new WP_Query($args);
						$val= isset($_GET['agent_search']) ? $_GET['agent_search'] : '';
						?>
						<div class="form-group" <?php echo esc_attr($style_field) ?>>
							<div class="noo-box-select">			
								<select class="form-control  form-control-chosen ignore-valid" name="agent_search" data-placeholder="All Agent" >
									<option value=""><?php echo __('All Agent','noo') ?></option>
								<?php
									foreach  ($agents as  $agent ) :
											$selected = ( $agent->ID == $val ) ? 'selected="selected"' : '';
										?>
										<option value="<?php echo esc_attr($agent->ID) ?>" <?php echo $selected ?> ><?php echo esc_html($agent->post_title); ?></option>
										<?php
									endforeach;
								?>
								</select>

							</div>
						</div>
						<?php

					break;	
					default:
						$field = re_get_property_field( $field_id );
						if ( !empty( $field ) ) {
							echo '<div class="form-group">';
								if( ( $field['type'] == 'checkbox' || $field['type'] == 'radio' ) && !empty( $field['label'] ) ) {
									echo '<div class="noo-label" style="margin-bottom: 5px;">' . esc_html( $field['label'] ) . '</div>';
								}
								re_property_render_search_field( $field );
							echo '</div>';
						}
				} ?>
		<?php
		endif;
	}
endif;

if( !function_exists( 're_property_save_custom_fields') ) :
	function re_property_save_custom_fields( $post_id = 0, $args = array() ) {
		if( empty( $post_id ) ) return;

		// Update custom fields
		$fields = re_get_property_custom_fields();
		if(!empty($fields)) {
			foreach ($fields as $field) {
				if( !isset( $field['name'] ) || empty( $field['name'] )) {
					continue;
				}

				$id = re_property_custom_fields_name($field['name']);
				if( isset( $field['is_default'] ) ) {
					if( isset( $field['is_disabled'] ) && ($field['is_disabled'] == 'yes') ) {
						continue;
					}
					if( isset( $field['is_tax'] ) && $field['is_tax'] ) {
						continue;
					}

					$id = $field['name'];
				}

				if( isset( $args[$id] ) ) {
					noo_save_field( $post_id, $id, $args[$id], $field );
				}
			}
		}
	}
endif;
