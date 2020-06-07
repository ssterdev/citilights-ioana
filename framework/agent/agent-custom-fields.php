<?php
if( !function_exists( 're_agent_custom_field_setting_register' ) ) :
	function re_agent_custom_field_setting_register() {
		register_setting( 'noo_agent_custom_field', 'noo_agent_custom_field');
	}
	
	add_filter('admin_init', 're_agent_custom_field_setting_register' );
endif;

if( !function_exists( 're_get_agent_custom_fields' ) ) :
	function re_get_agent_custom_fields( $all = true ) {
		$custom_fields = noo_get_custom_fields( 'noo_agent_custom_field', 'noo_agent_fields_');

		if( $all ) {
			$default_fields = re_get_agent_default_fields();
			// $custom_fields = array_merge( array_diff_key($default_fields, $custom_fields), $custom_fields );
			$custom_fields = noo_merge_custom_fields( $default_fields, $custom_fields );
		}

		return apply_filters('re_agent_custom_fields' . ( $all ? '_all' : '' ), $custom_fields );
	}
endif;

if( !function_exists( 're_agent_custom_fields_prefix' ) ) :
	function re_agent_custom_fields_prefix() {
		return apply_filters( 're_agent_custom_fields_prefix', '_noo_agent_field_' );
	}
endif;

if ( ! function_exists( 're_get_agent_field' ) ) :
	function re_get_agent_field( $field_name = '' ) {
		
		$custom_fields = re_get_agent_custom_fields();
		if( isset( $custom_fields[$field_name] ) ) {
			return $custom_fields[$field_name];
		}

		$field_name = re_agent_custom_fields_name($field_name);
		if( isset( $custom_fields[$field_name] ) ) {
			return $custom_fields[$field_name];
		}

		return array();
	}
endif;

if( !function_exists( 're_agent_custom_fields_name' ) ) :
	function re_agent_custom_fields_name( $field_name = '' ) {
		if( empty( $field_name ) ) return '';

		return apply_filters( 're_agent_custom_fields_name', re_agent_custom_fields_prefix() . sanitize_title( $field_name ) );
	}
endif;

if( !function_exists( 're_get_agent_custom_fields_option' ) ) :
	function re_get_agent_custom_fields_option($key = '', $default = null){
		$custom_fields = noo_get_setting('noo_agent_custom_field', array());
		
		if( !$custom_fields || !is_array($custom_fields) ) {
			return $default;
		}

		if( isset($custom_fields['__options__']) && isset($custom_fields['__options__'][$key]) ) {

			return $custom_fields['__options__'][$key];
		}
	
		return $default;
	}
endif;

if( !function_exists( 're_get_agent_socials' ) ) :
	function re_get_agent_socials() {
		$socials = re_get_agent_custom_fields_option('socials','facebook,twitter,pinterest,linkedin,google_plus');
		$socials = explode(',', $socials);

		return apply_filters( 're_get_agent_socials', $socials );
	}
endif;

if( !function_exists( 're_agent_custom_fields_menu' ) ) :
	function re_agent_custom_fields_menu() {
		add_submenu_page(
			'edit.php?post_type=noo_agent',
			__( 'Custom Fields', 'noo' ),
			__( 'Custom Fields', 'noo' ),
			'edit_theme_options', 'agent_custom_field',
			're_agent_custom_fields_setting' );
	}
	
	add_action( 'admin_menu', 're_agent_custom_fields_menu' );
endif;

if( !function_exists( 're_agent_custom_fields_setting' ) ) :
	function re_agent_custom_fields_setting(){
		wp_enqueue_style('noo-custom-fields');
		wp_enqueue_script('noo-custom-fields');

		if(function_exists( 'wp_enqueue_media' )){
			wp_enqueue_media();
		}else{
			wp_enqueue_style('thickbox');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
		}
		wp_enqueue_style('vendor-chosen-css');
		wp_enqueue_script('vendor-chosen-js');

		$custom_fields = re_get_agent_custom_fields();
		$all_socials = noo_get_social_fields();
		$selected_arr = re_get_agent_socials();


		?>
		<div class="wrap">
			<form action="options.php" method="post">
				<?php 
				noo_custom_fields_setting( 
					'noo_agent_custom_field',
					'noo_agent_fields_',
					$custom_fields
				); ?>
				<h3><?php echo __('Social Networks','noo')?></h3>
				<table class="form-table" cellspacing="0">
					<tbody>
						<tr>
							<th>
								<?php _e('Select Social Networks','noo')?>
							</th>
							<td>
								<?php if($all_socials): ?>
									<select class="social_list_field" name="noo_agent_custom_field[__options__][socials]" multiple="multiple" style="min-width: 300px;">
										<?php if($selected_arr): ?>
											<?php foreach ((array)$selected_arr as $index => $key): ?>
												<?php if( isset( $all_socials[$key] ) ) : ?>
													<option value="<?php echo esc_attr($key)?>" selected ><?php echo esc_html($all_socials[$key]['label'] ); ?></option>
													<?php unset( $all_socials[$key]); ?>
												<?php else : unset( $selected_arr[$index]); ?>
												<?php endif; ?>
											<?php endforeach;?>
										<?php endif; ?>
										<?php foreach ($all_socials as $key=>$social): ?>
											<option value="<?php echo esc_attr($key)?>" ><?php echo esc_html($social['label'] ); ?></option>
										<?php endforeach;?>
									</select>
									<input name="noo_agent_custom_field[__options__][socials]" type="hidden" value="<?php echo implode(',', $selected_arr ); ?>"/>
									<script type="text/javascript">
										jQuery(document).ready(function ($) {
			                                $("select.social_list_field").chosen({
			                                    placeholder_text_multiple: "<?php echo __( 'Select social networks', 'noo' ); ?>"
			                                }).change(function (e, params) {
			                                    var $this = $(this);
			                                    var values = $(this).siblings('input').val();
			                                    values = values !== "" ? values.split(',') : [];

			                                    if( typeof params.deselected !== "undefined" ) {
			                                    	values = $.grep(values, function(value) {
			                                    		return value != params.deselected;
			                                    	});
			                                    } else if( typeof params.selected !== "undefined" ) {
			                                    	values.push( params.selected );
			                                    }

			                                    $(this).siblings('input').val(values.join());
			                                });
			                            });
									</script>
									<style type="text/css">
									.chosen-container input[type="text"]{
										height: auto !important;
									}
									</style>
								<?php endif; ?>
							</td>
						</tr>
					</tbody>
				</table>
				<?php submit_button(__('Save Changes','noo')); ?>
			</form>
		</div>
		<?php
	}
endif;

if( !function_exists( 're_agent_render_form_field') ) :
	function re_agent_render_form_field( $field = array(), $agent_id = 0 ) {
		$blank_field = array( 'name' => '', 'label' => '', 'type' => 'text', 'value' => '', 'required' => '', 'is_disabled' => '' );
		$field = is_array( $field ) ? array_merge( $blank_field, $field ) : $blank_field; 
		if( !isset( $field['name'] ) || empty( $field['name'] ) ) return;

		$field_id = '';
		if( isset( $field['is_default'] ) ) {
			if( isset( $field['is_disabled'] ) && ($field['is_disabled'] == 'yes') ) {
				do_action( 're_agent_disabled_form_field', $field, $agent_id );

				return;
			}

			$field_id = $field['name'];
		} else {
			$field_id = re_agent_custom_fields_name( $field['name'] );
		}

		$value = !empty( $agent_id ) ? get_post_meta( $agent_id, $field_id, true ) : '';
		$value = isset( $_REQUEST[$field_id] ) ? $_REQUEST[$field_id] : $value;
		$value = !is_array($value) ? trim($value) : $value;

		$params = apply_filters( 're_agent_render_form_field_params', compact( 'field', 'field_id', 'value' ), $agent_id );
		extract($params);

		$field_id = esc_attr($field_id);
		?>
		<div class="form-group s-profile-<?php echo $field_id; ?>">
			<label for="<?php echo $field_id; ?>"><?php echo(isset( $field['label_translated'] ) ? $field['label_translated'] : $field['label'])  ?></label>
			<?php noo_render_field( $field, $field_id, $value ); ?>
		</div>
		<?php
	}
endif;

if( !function_exists( 're_agent_save_custom_fields') ) :
	function re_agent_save_custom_fields( $post_id = 0, $args = array() ) {
		if( empty( $post_id ) ) return;

		// Update custom fields
		$fields = re_get_agent_custom_fields();

		if(!empty($fields)) {
			foreach ($fields as $field) {
				if( !isset( $field['name'] ) || empty( $field['name'] )) {
					continue;
				}

				$id = re_agent_custom_fields_name($field['name']);
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
if ( ! function_exists( 're_agent_display_field' ) ) :
	function re_agent_display_field( $field = array(), $agent_id = '', $args = array() ) {
		if( empty( $agent_id ) || !isset( $field['name'] ) || empty( $field['name'] )) return;

		$field['type'] = isset( $field['type'] ) ? $field['type'] : 'text';

		$id = re_agent_custom_fields_name($field['name']);
		if( isset( $field['is_default'] ) ) {
			if( isset( $field['is_disabled'] ) && ($field['is_disabled'] == 'yes') )
				return;
			if( isset( $field['is_tax'] ) )
				return;
			$id = $field['name'];
		}

		$value = get_post_meta($agent_id, $id, true);

		if( empty( $value ) ) return;

		$args = array_merge( array(
				'label_tag' => 'span',
				'label_class' => '',
				'value_tag' => '',
				'value_class' => '',
			), $args );

		$icon = isset( $field['icon'] ) ? '<i class="fa ' . $field['icon'] . '""></i>' : '';
		$label = '';
		if( !empty( $args['label_tag'] ) ) {
			$label = isset( $field['label_translated'] ) ? $field['label_translated'] : $field['label'];
			$label = "<{$args['label_tag']} class='label-{$id} {$args['label_class']}'>". esc_html( $label ) . ":</{$args['label_tag']}>";
		}

		$atts = $id == RE_AGENT_META_PREFIX . '_email' ? "data-original-title='{$value}'" : '';
		$field_name = str_replace( RE_AGENT_META_PREFIX . '_', '', $id )
		?> 
		<div class="agent-<?php echo esc_attr( $field_name ); ?>" <?php echo $atts; ?>>
			<?php echo $icon . $label; ?>
			<?php if ( $field_name === 'website' ) : ?>
				<a target="_blank" href="<?php echo esc_attr( $value ) ?>" title="<?php echo esc_attr( $value ) ?>">
					<?php noo_display_field_value( $field, $id, $value, $args ); ?>
				</a>
			<?php elseif ( $field_name === 'email' ) : ?>
				<a target="_top" href="mailto:<?php echo esc_attr( $value ) ?>" title="<?php echo esc_attr( $value ) ?>">
					<?php noo_display_field_value( $field, $id, $value, $args ); ?>
				</a>
			<?php 
			else :
				noo_display_field_value( $field, $id, $value, $args );
			endif; ?>
		</div>
		<?php
	}
endif;