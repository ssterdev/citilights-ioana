<?php
if( !function_exists( 're_get_property_feature_fields' ) ) :
	function re_get_property_feature_fields( $translated = true ) {
		$features = re_get_property_feature_setting('features');
		$codes = array();
		if( isset( $features['code'] ) ) {
			$codes = $features['code'];
		}

		if( isset( $features['label'] ) ) {
			$features = $features['label'];
		}
		$translated_features = array();	
		if( !empty( $features ) && count( $features ) > 0 ) {
			foreach ($features as $index => $feature){
				if( empty( $feature ) ) continue;

				$key = isset( $codes[$index] ) && !empty( $codes[$index] ) ? $codes[$index] : sanitize_title( $feature );
				$translated_features[$key] = $translated ? apply_filters('wpml_translate_single_string', $feature, 'Property Custom Features', 'noo_property_features_' . $key, apply_filters( 'wpml_current_language', null ) ) : $feature;
			}
			$features = $translated_features;
		}

		return $features;
	}
endif;

if( !function_exists( 're_get_property_feature_setting' ) ) :
	function re_get_property_feature_setting( $id = null, $default = null ) {
		return noo_get_setting('noo_property_feature', $id, $default);
	}
endif;

if( !function_exists( 're_property_features_menu' ) ) :
	function re_property_features_menu() {
		add_submenu_page('edit.php?post_type=noo_property', __( 'Features & Amenities', 'noo' ), __( 'Features & Amenities', 'noo' ), 'edit_posts', 'features-amenities', 're_property_features_setting' );
	}
	
	add_action( 'admin_menu', 're_property_features_menu' );
endif;

if( !function_exists( 're_property_features_setting' ) ) :
	function re_property_features_setting(){
		wp_enqueue_style('noo-custom-fields');
		wp_enqueue_script('noo-custom-fields');

		$features = re_get_property_feature_fields(false);
		if(isset($_GET['settings-updated']) && $_GET['settings-updated']) {
			if( defined( 'ICL_SITEPRESS_VERSION' ) ) {
				foreach ($features as $k=>$feature) {
					do_action( 'wpml_register_single_string', 'Property Custom Features', 'noo_property_features_'.sanitize_title($feature), $feature );
				}
			}
		}
		?>
		<div class="wrap">
			<form action="options.php" method="post">
			<?php settings_fields('noo_property_feature'); ?>
				<h2><?php echo __('Listings Features & Amenities','noo')?></h2>
				<table class="form-table" cellspacing="0">
					<tbody>
						<tr>
							<th>
								<?php esc_html_e('Add New Element in Features and Amenities ','noo')?>
							</th>
							<td>
								<table class="widefat noo_property_feature_table" cellspacing="0" >
									<thead>
										<tr>
											<th class="feature-code">
												<?php esc_html_e('Feature Code','noo')?>
												<span class="help">
													<a href="#" title="<?php echo esc_attr__('Be careful when you change the code, you could loose this field value.', 'noo' ); ?>" class="help_tip"><i class="dashicons dashicons-editor-help"></i></a>
												</span>
											</th>
											<th class="feature-name">
												<?php esc_html_e('Feature Name','noo')?>
											</th>
											<th class="feature-action">
												<?php esc_html_e('Action','noo')?>
											</th>
										</tr>
									</thead>
									<tbody>
										<?php  if(!empty($features)): ?>
											<?php foreach ($features as $code=>$feature) : ?>
												<?php if( empty( $feature ) ) continue; ?>
												<tr>
													<td>
														<input type="text" value="<?php echo esc_attr($code)?>" placeholder="<?php esc_attr_e('Feature Code','noo')?>" name="noo_property_feature[features][code][]">
													</td>
													<td>
														<input type="text" value="<?php echo esc_attr($feature)?>" placeholder="<?php esc_attr_e('Feature Name','noo')?>" name="noo_property_feature[features][label][]" class="regular-text">
													</td>
													<td>
														<input class="button button-primary" onclick="return delete_noo_property_feature(this);" type="button" value="<?php esc_attr_e('Delete','noo')?>">
													</td>
												</tr>
											<?php endforeach;?>
										<?php endif;?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="3">
												<input class="button button-primary" id="add_noo_property_feature" type="button" value="<?php esc_attr_e('Add','noo')?>">
											</td>
										</tr>
									</tfoot>
								</table>
							</td>
						</tr>
						<tr>
							<th>
								<?php esc_html_e('Show the Features and Amenities that are not available','noo')?>
							</th>
							<td>
								<?php $show_no_feature = re_get_property_feature_setting('show_no_feature')?>
								<select name="noo_property_feature[show_no_feature]">
									<option <?php selected($show_no_feature,'yes')?> value="yes"><?php esc_html_e("Yes",'noo')?></option>
									<option <?php selected($show_no_feature,'no')?> value="no"><?php esc_html_e("No",'noo')?></option>
								</select>
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
