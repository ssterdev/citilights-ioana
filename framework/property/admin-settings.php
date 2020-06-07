<?php
if( !function_exists( 're_property_setting_register' ) ) :
	function re_property_setting_register() {
		register_setting( 'noo_property_general', 'noo_property_general' );
		register_setting( 'noo_property_contact', 'noo_property_contact' );
		register_setting( 'noo_property_custom_filed', 'noo_property_custom_filed');
		register_setting( 'noo_property_feature', 'noo_property_feature' );
		register_setting( 'noo_property_advanced_search', 'noo_property_advanced_search' );
		register_setting( 'noo_property_google_map', 'noo_property_google_map' );
		register_setting( 'noo_property_near_by' , 'noo_property_near_by');
		
		add_action( 're_setting_general', 're_property_general_setting_form' );
		add_action( 're_setting_contact', 're_property_contact_setting_form' );
		add_action( 're_setting_advanced_search', 're_property_search_setting_form' );
		add_action( 're_setting_google_map', 're_property_map_setting_form' );
		add_action( 're_setting_near_by', 're_property_near_by_setting_form');
	}
	
	add_filter('admin_init', 're_property_setting_register' );
endif;
if( !function_exists( 're_property_setting_tabs' ) ) :
	function re_property_setting_tabs( $tabs = array() ) {
		return array_merge( array(
							'general'			=> __('Property','noo'),
							'contact'			=> __('Contact & Email','noo'),
					'advanced_search'	=> __('Advanced Search','noo'),
						'google_map'		=> __('Google Map','noo'),
							'near_by'			=> __('Nearby Places','noo')
			), $tabs
		);
	}
	
	add_filter('re_setting_tabs', 're_property_setting_tabs' );
endif;
if( !function_exists( 're_property_general_setting_form' ) ) :
	function re_property_general_setting_form() {
		if(isset($_GET['settings-updated']) && $_GET['settings-updated'])
		{
			flush_rewrite_rules();
		}
		$currency_code_options = re_get_currencies();
		$archive_slug = re_get_property_setting('archive_slug','properties');
		$area_unit = re_get_property_setting('area_unit');
		$currency = re_get_property_setting('currency');
		$currency_position = re_get_property_setting('currency_position');
		$price_thousand_sep = re_get_property_setting('price_thousand_sep');
		$price_decimal_sep = re_get_property_setting('price_decimal_sep');
		$price_num_decimals = re_get_property_setting('price_num_decimals');
		foreach ( $currency_code_options as $code => $name ) {
			$currency_code_options[ $code ] = $name . ' (' . re_get_currency_symbol( $code ) . ')';
		}
		$floor_plan = re_get_property_setting('floor_plan', 'admin');
		$property_sub_listing = re_get_property_setting('property_sub_listing','yes');
		$virtual_tour = re_get_property_setting('virtual_tour','admin');
?>
<?php settings_fields('noo_property_general'); ?>
<h3><?php echo __('General Options','noo')?></h3>
<table class="form-table" cellspacing="0">
	<tbody>
		<tr>
			<th>
				<?php esc_html_e('Property Archive base (slug)','noo')?>
			</th>
			<td>
				<input type="text" name="noo_property_general[archive_slug]" value="<?php echo ($archive_slug ? $archive_slug :'properties') ?>">
				<p><small><?php echo sprintf( __( 'This option will affect the URL structure on your site. If you made change on it and see an 404 Error, you will have to go to <a href="%s" target="_blank">Permalink Settings</a><br/> and click "Save Changes" button for reseting WordPress link structure.', 'noo' ), admin_url( '/options-permalink.php' ) ); ?></small></p>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e('Area Unit','noo')?>
			</th>
			<td>
				<input type="text" name="noo_property_general[area_unit]" value="<?php echo ($area_unit ? $area_unit :'m') ?>">
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e('Currency','noo')?>
			</th>
			<td>
				<select name="noo_property_general[currency]">
					<?php foreach ($currency_code_options as $key=>$label):?>
					<option value="<?php echo esc_attr($key)?>" <?php selected($currency,$key)?>><?php echo esc_html($label)?></option>
					<?php endforeach;?>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e('Currency Position','noo')?>
			</th>
			<td>
				<?php
				$current_currency_symbol = re_get_currency_symbol();
				$position = array(
						'left'        => __( 'Left', 'noo' ) . ' (' . $current_currency_symbol . '99.99)',
						'right'       => __( 'Right', 'noo' ) . ' (99.99' . $current_currency_symbol . ')',
						'left_space'  => __( 'Left with space', 'noo' ) . ' (' . $current_currency_symbol . ' 99.99)',
						'right_space' => __( 'Right with space', 'noo' ) . ' (99.99 ' . $current_currency_symbol . ')'
				)
				?>
				<select name="noo_property_general[currency_position]">
					<?php foreach ($position as $key=>$label):?>
					<option value="<?php echo esc_attr($key)?>" <?php selected($currency_position,$key)?>><?php echo esc_html($label)?></option>
					<?php endforeach;?>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e('Thousand Separator','noo')?>
			</th>
			<td>
				<input type="text" name="noo_property_general[price_thousand_sep]" value="<?php echo ($price_thousand_sep ? $price_thousand_sep :',') ?>">
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e('Decimal Separator','noo')?>
			</th>
			<td>
				<input type="text" name="noo_property_general[price_decimal_sep]" value="<?php echo ($price_decimal_sep ? $price_decimal_sep :'.') ?>">
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e('Number of Decimals','noo')?>
			</th>
			<td>
				<input type="number" step="1" min="0" name="noo_property_general[price_num_decimals]" value="<?php echo ($price_num_decimals !=='' && $price_num_decimals !== null && $price_num_decimals !== array() ? $price_num_decimals :'2') ?>">
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e('Enable Floor Plan','noo')?>
			</th>
			<td>
				<fieldset>
					<label title="none">
						<input type="radio" name="noo_property_general[floor_plan]" value="none" <?php checked( $floor_plan, 'none'); ?>>
						<span><?php _e( 'No', 'noo'); ?></span>
					</label>
					<br/>
					<label title="admin">
						<input type="radio" name="noo_property_general[floor_plan]" value="admin" <?php checked( $floor_plan, 'admin'); ?>>
						<span><?php _e( 'Yes, for Admin', 'noo'); ?></span>
					</label>
					<br/>
					<label title="agent">
						<input type="radio" name="noo_property_general[floor_plan]" value="agent" <?php checked( $floor_plan, 'agent'); ?>>
						<span><?php _e( 'Yes, for Admin and Agents', 'noo'); ?></span>
					</label>
					<br/>
				</fieldset>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e('Enable Sub Listing','noo')?>
			</th>
			<td>
				<fieldset>
					<label title="none">
						<input type="radio" name="noo_property_general[property_sub_listing]" value="none" <?php checked( $property_sub_listing, 'none'); ?>>
						<span><?php _e( 'No', 'noo'); ?></span>
					</label>
					<br/>
					<label title="admin">
						<input type="radio" name="noo_property_general[property_sub_listing]" value="admin" <?php checked( $property_sub_listing, 'admin'); ?>>
						<span><?php _e( 'Yes, for Admin', 'noo'); ?></span>
					</label>
					<br/>
					<label title="agent">
						<input type="radio" name="noo_property_general[property_sub_listing]" value="agent" <?php checked( $property_sub_listing, 'agent'); ?>>
						<span><?php _e( 'Yes, for Admin and Agents', 'noo'); ?></span>
					</label>
					<br/>
				</fieldset>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e('Enable 360° Virtual Tour','noo')?>
			</th>
			<td>
				<fieldset>
					<label title="none">
						<input type="radio" name="noo_property_general[virtual_tour]" value="none" <?php checked( $virtual_tour, 'none'); ?>>
						<span><?php _e( 'No', 'noo'); ?></span>
					</label>
					<br/>
					<label title="admin">
						<input type="radio" name="noo_property_general[virtual_tour]" value="yes" <?php checked( $virtual_tour, 'yes'); ?>>
						<span><?php _e( 'Yes', 'noo'); ?></span>
					</label>
					<br/>
				</fieldset>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e('Allowed Upload File Types','noo') ?>
			</th>
			<td>
				<input type="text"  value="<?php echo re_get_property_setting('check_file','pdf,docx,doc') ?>" name="noo_property_general[check_file]">
				<p><small><?php echo sprintf( __( 'File types that are allowed upload Document . Default only allows Words and PDF files', 'noo' ) ); ?></small></p>
			</td>
		</tr>
	</tbody>
</table>
<?php
}
endif;
if( !function_exists( 're_property_contact_setting_form' ) ) :
function re_property_contact_setting_form() {
$property_contact_form = re_get_property_contact_setting('property_contact_form');
$agent_contact_form = re_get_property_contact_setting('agent_contact_form');
$noo_cc_mail_to = re_get_property_contact_setting('cc_mail_to', '' );
if ( defined( 'ICL_SITEPRESS_VERSION' ) ){
	do_action( 'wpml_register_single_string', 'Noo Contact Form', 'Agent Contact Form', $agent_contact_form );
	$agent_contact_form = apply_filters( 'wpml_translate_single_string', $agent_contact_form, 'Noo Contact Form', 'Agent Contact Form', apply_filters( 'wpml_current_language', null ) );
	do_action( 'wpml_register_single_string', 'Noo Contact Form', 'Property Contact Form', $property_contact_form );
	$property_contact_form = apply_filters( 'wpml_translate_single_string', $property_contact_form, 'Noo Contact Form', 'Property Contact Form', apply_filters( 'wpml_current_language', null ) );
}
?>
<?php settings_fields('noo_property_contact'); ?>
<h3><?php echo __('Contact & Email Options','noo')?></h3>
<table class="form-table" cellspacing="0">
	<tbody>
		<?php
		if( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) || defined( 'WPCF7_PLUGIN' ) ) :
			$cf7 = get_posts( 'post_type="wpcf7_contact_form"&numberposts=-1' );
			$contact_forms = array();
			if ( $cf7 ) :
		?>
		<tr>
			<th>
				<?php esc_html_e('Custom Property Contact Form','noo')?>
			</th>
			<td>
				<select name="noo_property_contact[property_contact_form]">
					<option value="" <?php selected( $property_contact_form, '' ); ?>><?php echo __('none', 'noo'); ?></option>
					<?php foreach ( $cf7 as $cform ) : ?>
					$contact_forms[ $cform->post_title ] = $cform->ID;
					<option value="<?php echo $cform->ID; ?>" <?php selected( $property_contact_form, $cform->ID ); ?>><?php echo $cform->post_title; ?></option>
					<?php endforeach; ?>
				</select>
				<p><small><?php echo __( 'Select a form you created with Contact Form 7 plugin to use for contact and send email on Property page.', 'noo' ); ?></small></p>
				<p><small><?php echo __( 'Note:', 'noo' ); ?></small></p>
				<p><small><?php echo __( ' - The contact form must include the fields: [your-name], [your-email] and [your-message]', 'noo' ); ?></small></p>
				<p><small><?php echo __( ' - You can use the following tags in the email of that form: [property-id], [property-name], [property-url], [agent-id], [agent-name] and [agent-url].', 'noo' ); ?></small></p>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e('Custom Agent Contact Form','noo')?>
			</th>
			<td>
				<select name="noo_property_contact[agent_contact_form]">
					<option value="" <?php selected( $agent_contact_form, '' ); ?>><?php echo __('none', 'noo'); ?></option>
					<?php foreach ( $cf7 as $cform ) : ?>
					$contact_forms[ $cform->post_title ] = $cform->ID;
					<option value="<?php echo $cform->ID; ?>" <?php selected( $agent_contact_form, $cform->ID ); ?>><?php echo $cform->post_title; ?></option>
					<?php endforeach; ?>
				</select>
				<p><small><?php echo __( 'Select a form you created with Contact Form 7 plugin to use for contact and send email on agent profile page.', 'noo' ); ?></small></p>
				<p><small><?php echo __( 'Note:', 'noo' ); ?></small></p>
				<p><small><?php echo __( ' - The contact form must include the fields: [your-name], [your-email] and [your-message]', 'noo' ); ?></small></p>
				<p><small><?php echo __( ' - You can use the following tags in the email of that form: [agent-id], [agent-name] and [agent-url].', 'noo' ); ?></small></p>
			</td>
		</tr>
		<?php endif; ?>
		<?php endif; ?>
		<tr valign="top" class="noo_cc_mail_to">
			<th scope="row"><label for="noo_cc_mail_to"><?php _e( 'CC all Property Emails to', 'noo' ); ?></label></th>
			<td>
				<input name="noo_property_contact[cc_mail_to]" type="hidden" value="" />
				<input id="noo_cc_mail_to" name="noo_property_contact[cc_mail_to]" class="regular-text code" type="text" value="<?php echo esc_attr( $noo_cc_mail_to ); ?>" placeholder="<?php esc_html_e( 'Enter your email...', 'noo' ); ?>" />
			</td>
		</tr>
		<tr>
			<th>
				<?php  esc_html_e('Google reCAPTCHA','noo'); ?>
			</th>
			<td>
				<input type="hidden" value="0" name="noo_property_contact[recaptcha]">
				<input type="checkbox" value="1" <?php checked(re_get_property_contact_setting('recaptcha','1'), '1'); ?> name="noo_property_contact[recaptcha]">
				<small><?php _e('Enable the Google reCAPTCHA for contact agent form.', 'noo'); ?></small>
			</td>
		</tr>
		<tr>
			<th>
				<?php  esc_html_e('Google reCAPTCHA Key','noo'); ?>
			</th>
			<td>
				<input type="text" class="regular-text" value="<?php echo re_get_property_contact_setting('key_recaptcha','')?>" name="noo_property_contact[key_recaptcha]"><br>
				<small><?php _e('Please enter your SITE KEY reCAPTCHA', 'noo'); ?></small><br>
			</td>
			
		</tr>
		<tr>
			<th>
				
			</th>
			<td>
				<input type="text" class="regular-text" value="<?php echo re_get_property_contact_setting('key_secret','')?>" name="noo_property_contact[key_secret]"><br>
				<small><?php _e('Please enter your SECRET KEY reCAPTCHA', 'noo'); ?></small><br>
			</td>
		</tr>
	</tbody>
</table>
<?php
}
endif;
if( !function_exists( 're_property_search_setting_form' ) ) :
function re_property_search_setting_form() {
$fields = array(
	''                      => __( 'None',  'noo' ),
	'property_location'     => __( 'Property Location', 'noo' ),
	'property_sub_location' => __( 'Property Sub Location', 'noo' ),
	'property_status'       => __( 'Property Status', 'noo' ),
	'property_category'     => __( 'Property Types', 'noo' ),
	'keyword'               => __( 'Keyword', 'noo' ),
	'_price'                => __( 'Price Meta', 'noo' ),
	'_agent_responsible'    => __( 'Agent', 'noo' ),
);
$custom_fields = re_get_property_cf_setting('custom_field');
if($custom_fields){
	foreach ($custom_fields as $k=>$custom_field){
		$label = __('Custom Field: ','noo').( isset( $custom_field['label_translated'] ) ? $custom_field['label_translated'] : (isset($custom_field['label']) ? $custom_field['label'] : $k));
		$id = $k;
		$fields[$id] = $label;
	}
}
$fields = apply_filters( 're_property_search_fields', $fields );
$pos1 = re_get_property_search_setting('pos1','property_location');
$pos2 = re_get_property_search_setting('pos2','property_sub_location');
$pos3 = re_get_property_search_setting('pos3','property_status');
$pos4 = re_get_property_search_setting('pos4','property_category');
$pos5 = re_get_property_search_setting('pos5','_bedrooms');
$pos6 = re_get_property_search_setting('pos6','_bathrooms');
$pos7 = re_get_property_search_setting('pos7','_price');
$pos8 = re_get_property_search_setting('pos8','_area');

$p_style = re_get_property_search_setting('p_style','slide');
$price_range_size = re_get_property_search_setting('price_range_size',10000);
$area_range_size = re_get_property_search_setting('area_range_size',50);

wp_enqueue_style('vendor-chosen-css');
wp_enqueue_script('vendor-chosen-js');

?>
<?php settings_fields('noo_property_advanced_search'); ?>
<h3><?php echo __('Search Field Position','noo')?></h3>
<table class="form-table" cellspacing="0">
	<tbody>
		<tr>
			<th>
				<?php _e('Position #1','noo')?>
			</th>
			<td>
				<select name="noo_property_advanced_search[pos1]">
					<?php foreach ($fields as $key=>$field):?>
					<option value="<?php echo esc_attr($key)?>" <?php selected($pos1,esc_attr($key))?>><?php echo $field?></option>
					<?php endforeach;?>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<?php _e('Position #2','noo')?>
			</th>
			<td>
				<select name="noo_property_advanced_search[pos2]">
					<?php foreach ($fields as $key=>$field):?>
					<option value="<?php echo esc_attr($key)?>" <?php selected($pos2,esc_attr($key))?>><?php echo $field?></option>
					<?php endforeach;?>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<?php _e('Position #3','noo')?>
			</th>
			<td>
				<select name="noo_property_advanced_search[pos3]">
					<?php foreach ($fields as $key=>$field):?>
					<option value="<?php echo esc_attr($key)?>" <?php selected($pos3,esc_attr($key))?>><?php echo $field?></option>
					<?php endforeach;?>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<?php _e('Position #4','noo')?>
			</th>
			<td>
				<select name="noo_property_advanced_search[pos4]">
					<?php foreach ($fields as $key=>$field):?>
					<option value="<?php echo esc_attr($key)?>" <?php selected($pos4,esc_attr($key))?>><?php echo $field?></option>
					<?php endforeach;?>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<?php _e('Position #5','noo')?>
			</th>
			<td>
				<select name="noo_property_advanced_search[pos5]">
					<?php foreach ($fields as $key=>$field):?>
					<option value="<?php echo esc_attr($key)?>" <?php selected($pos5,esc_attr($key))?>><?php echo $field?></option>
					<?php endforeach;?>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<?php _e('Position #6','noo')?>
			</th>
			<td>
				<select name="noo_property_advanced_search[pos6]">
					<?php foreach ($fields as $key=>$field):?>
					<option value="<?php echo esc_attr($key)?>" <?php selected($pos6,esc_attr($key))?>><?php echo $field?></option>
					<?php endforeach;?>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<?php _e('Position #7','noo')?>
			</th>
			<td>
				<select name="noo_property_advanced_search[pos7]">
					<?php foreach ($fields as $key=>$field):?>
					<option value="<?php echo esc_attr($key)?>" <?php selected($pos7,esc_attr($key))?>><?php echo $field?></option>
					<?php endforeach;?>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<?php _e('Position #8','noo')?>
			</th>
			<td>
				<select name="noo_property_advanced_search[pos8]">
					<?php foreach ($fields as $key=>$field):?>
					<option value="<?php echo esc_attr($key)?>" <?php selected($pos8,esc_attr($key))?>><?php echo $field?></option>
					<?php endforeach;?>
				</select>
			</td>
		</tr>		
	</tbody>
</table>
<h3><?php echo __('Price, Area Fields','noo')?></h3>
<table class="form-table" cellspacing="0">
	<tbody>
		<tr>
			<th>
				<?php _e('Style','noo')?>
			</th>
			<td>
				<select name="noo_property_advanced_search[p_style]">
					<option value="slide" <?php selected($p_style, 'slide')?>><?php echo esc_html__('Slide','noo');?></option>
					<option value="dropdown" <?php selected($p_style,'dropdown')?>><?php echo esc_html__('Dropdown','noo');?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<?php _e('Price Range size (Dropdown)','noo')?>
			</th>
			<td>
				<input type="text" value="<?php echo esc_attr($price_range_size);?>" name="noo_property_advanced_search[price_range_size]"/>
			</td>
		</tr>
		<tr>
			<th>
				<?php _e('Area Range size (Dropdown)','noo')?>
			</th>
			<td>
				<input type="text" value="<?php echo esc_attr($area_range_size);?>" name="noo_property_advanced_search[area_range_size]"/>
			</td>
		</tr>
	</tbody>
</table>
<h3><?php echo __('Advanced Search Field','noo')?></h3>
<?php
$features = re_get_property_feature_fields();
$feature_selected = re_get_property_search_setting('advanced_search_field',array());
?>
<table class="form-table" cellspacing="0">
	<tbody>
		<tr>
			<th>
				<?php _e('Select Advanced Search Field','noo')?>
			</th>
			<td>
				<?php if($features): ?>
				<select class="advanced_search_field" name="noo_property_advanced_search[advanced_search_field][]" multiple="multiple" style="min-width: 300px;">
					<?php foreach ((array)$features as $key=>$feature): ?>
					<option value="<?php echo esc_attr($key)?>" <?php if(in_array($key, $feature_selected)):?> selected<?php endif;?>><?php echo ucfirst($feature)?></option>
					<?php endforeach;?>
				</select>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						jQuery("select.advanced_search_field").chosen({
							"disable_search_threshold":20
						});
					});
				</script>
				<style type="text/css">
				.chosen-container input[type="text"]{
					height: auto !important;
				}
				</style>
				<?php else : ?>
				<p><?php _e('You have no Amenities ( Listing Features ). Please create some if you want to search with Amenities.', 'noo'); ?></p>
				<p><a href="<?php echo admin_url( 'edit.php?post_type=noo_property&page=features-amenities' ); ?>"><?php _e('Switch to Listings Features & Amenities', 'noo'); ?></a></p>
				<?php endif; ?>
			</td>			
		</tr>
	</tbody>
</table>
<?php
}
endif;
if( !function_exists( 're_property_map_setting_form' ) ) :
function re_property_map_setting_form() {
$map_type = re_get_property_map_setting('map_type','');
settings_fields('noo_property_google_map'); ?>
<h3><?php echo __('Google Map','noo')?></h3>
<table class="form-table" cellspacing="0">
	<tbody>
		<tr>
			<th>
				<?php esc_html_e('Select Map Type','noo')?>
			</th>
			<td id="check-type-map">
				<input type="radio" name="noo_property_google_map[map_type]" value="none" <?php checked( $map_type, 'none'); ?>>
				<span><?php _e( 'None', 'noo'); ?></span>
				<input type="radio" name="noo_property_google_map[map_type]" value="google" <?php checked( $map_type, 'google'); ?>>
				<span><?php _e( 'Google Map', 'noo'); ?></span>
				<input type="radio" name="noo_property_google_map[map_type]" value="bing" <?php checked( $map_type, 'bing'); ?>>
				<span><?php _e( 'Bing map', 'noo'); ?></span>
			</td>
			
		</tr>
		<tr class="google-map">
			<th>
				<?php esc_html_e('Google Maps API','noo')?>
			</th>
			<td>
				<input type="text" class="regular-text" value="<?php echo re_get_property_map_setting('google_api','')?>" name="noo_property_google_map[google_api]">
				<p class="noo-help" data-class-wrap="<?php echo $class_get_api = uniqid( 'class-get-api-' ); ?>">
					<?php echo noo_citilights_html_content( __( '<strong>Google</strong> requires that you register an API Key to display <strong>Maps</strong> on from your website. To know how to create this application, <span>click here and follow the steps</span>.', 'noo' ) ); ?>
				</p>
				<ul class="content-help <?php echo esc_attr( $class_get_api ); ?>">
					
					<li>
						<p><?php echo noo_citilights_html_content( sprintf( __( 'Follow <a target="_blank" href="%s" title="this link">this link</a> and click on Get a key:', 'noo' ), 'https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key' ) ); ?></p>
						<div class="item-image-wrap">
							<img src="http://wp.nootheme.com/citilights/wp-content/uploads/2016/07/Google-Maps-API-Get-a-key.jpg" alt="Google Maps APIs – Get a key" />
							<p><?php echo esc_html__( 'Google Maps APIs – Get a key', 'noo' ); ?></p>
						</div>
					</li>
					<li>
						<p><?php echo esc_html__( 'Agree with the service Terms of Service:', 'noo' ) ?></p>
						<div class="item-image-wrap">
							<img src="http://wp.nootheme.com/citilights/wp-content/uploads/2016/07/Google-Maps-API-Agree-with-the-terms.jpg" alt="Google Maps APIs – Agree with the terms" />
							<p><?php echo esc_html__( 'Google Maps APIs – Agree with the terms', 'noo' ); ?></p>
						</div>
					</li>
					
					<li>
						<p><?php echo noo_citilights_html_content( __( 'Choose a <strong>name</strong> for your new key and specify the <strong>websites on which the key usage will be allowed</strong>. If you don’t need any website restriction, just put an * in that field (but don’t leave it blank, unless you are having issues with *! See comments for further information). Then click on <strong>Create</strong>:', 'noo' ) ); ?></p>
						<div class="item-image-wrap">
							<img src="http://wp.nootheme.com/citilights/wp-content/uploads/2016/07/Google-Maps-API-Generate-a-key.jpg" alt="Google Maps APIs – Generate a key" />
							<p><?php echo esc_html__( 'Google Maps APIs – Generate a key', 'noo' ); ?></p>
						</div>
					</li>
					
					<li>
						<p><?php echo noo_citilights_html_content( __( 'Write down your brand new API key, and click <strong>OK</strong>', 'noo' ) ); ?></p>
						<div class="item-image-wrap">
							<img src="http://wp.nootheme.com/citilights/wp-content/uploads/2016/07/Google-Maps-API-Generated-key.jpg" alt="Google Maps APIs – Generated key" />
							<p><?php echo esc_html__( 'Google Maps APIs – Generated key', 'noo' ); ?></p>
						</div>
					</li>
				</ul>
				<div class="content-help <?php echo esc_attr( $class_get_api ); ?>">
					
					<h3><?php echo esc_html__( 'You can follow the video instructions', 'noo' ) ?></h3>
					<iframe width="560" height="315" src="https://www.youtube.com/embed/rCMY0gxX_jI" frameborder="0" allowfullscreen></iframe>
				</div>
			</td>
		</tr>
		<tr class="bing-map">
			<th>
				<?php esc_html_e('Bing Maps API','noo')?>
			</th>
			<td>
				<input type="text" class="regular-text" value="<?php echo re_get_property_map_setting('bing_api','')?>" name="noo_property_google_map[bing_api]">
				<p class="noo-help" data-class-wrap="<?php echo $class_get_api = uniqid( 'class-get-api-' ); ?>">
					<?php echo noo_citilights_html_content( __( '<strong>Bing </strong> requires that you register an API Key to display <strong>Maps</strong> on from your website. To know how to create this application, <a href="https://www.microsoft.com/en-us/maps/"><span>click here and follow the steps</span></a>.', 'noo' ) ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e('Starting Point Latitude','noo')?>
			</th>
			<td>
				<input type="text" class="regular-text" value="<?php echo re_get_property_map_setting('latitude','40.714398')?>" name="noo_property_google_map[latitude]">
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e('Starting Point Longitude','noo')?>
			</th>
			<td>
				<input type="text" class="regular-text"  value="<?php echo re_get_property_map_setting('longitude','-74.005279')?>" name="noo_property_google_map[longitude]">
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e('Default Zoom Level','noo')?>
			</th>
			<td>
				<input type="text" class="regular-text"  value="<?php echo re_get_property_map_setting('zoom','12')?>" name="noo_property_google_map[zoom]">
			</td>
		</tr>
		<tr class="google-map">
			<th>
				<?php esc_html_e('Automatically Fit all Properties','noo')?>
			</th>
			<td>
				<input type="hidden" value="0" name="noo_property_google_map[fitbounds]">
				<input type="checkbox" value="1" <?php checked(re_get_property_map_setting('fitbounds','1'), '1'); ?> name="noo_property_google_map[fitbounds]">
				<small><?php _e('Enable this option and all your listings will fit your map automatically.', 'noo'); ?></small>
			</td>
		</tr>
		<tr class="google-map">
			<th>
				<?php esc_html_e('Default Map Height (px)','noo')?>
			</th>
			<td>
				<input type="text" class="regular-text"  value="<?php echo re_get_property_map_setting('height','700')?>" name="noo_property_google_map[height]">
			</td>
		</tr>
		<tr class="google-map">
			<th>
				<?php esc_html_e('Drag Map','noo')?>
			</th>
			<td>
				<input type="hidden" value="0" name="noo_property_google_map[draggable]">
				<input type="checkbox" value="1" <?php checked(re_get_property_map_setting('draggable','1'), '1'); ?> name="noo_property_google_map[draggable]">
				<small><?php _e('Tick this box to make map draggable', 'noo'); ?></small>
			</td>
		</tr>
		<tr class="google-map">
			<th>
				<?php esc_html_e('Map Background Image','noo')?><br/>
				<small><?php _e('The background image that displays when map loading.', 'noo'); ?></small>
			</th>
			<td>
				<?php
				$default_background = get_stylesheet_directory_uri() . '/assets/images/bg-map.jpg';
				$field = array(
					'id'=>'map_background',
					'type'=>'image',
					'default'=>'',
				);
				echo noo_render_setting_field( $field, 'noo_property_google_map' );
				?>
			</td>
		</tr>
		<script>
			jQuery(document).ready(function($) {
				var default_type = $('#check-type-map input[type=radio]:checked').val();
				console.log(default_type);
				if ( default_type == 'google' ){
				 // $('.google-map').show();
				 $('.bing-map').hide();
				}
				else{
					$('.bing-map').show();
					$('.google-map').hide();
				}	
				$("#check-type-map input[type=radio]").change(function(e){
					var type_payment = $('#check-type-map input[type=radio]:checked').val();	
					console.log(type_payment);
				    if(type_payment == 'google') {
				       	$('.google-map').show();
				    } else {
				        $('.google-map').hide();
				    }
				    if (type_payment == 'bing') {
				    	$('.bing-map').show();
				    }else {
				        $('.bing-map').hide();
				    }
				});
			});
			</script>
	</tbody>
</table>
<?php
}
endif;
if( !function_exists( 're_property_near_by_setting_form' ) ) :
function re_property_near_by_setting_form() {
settings_fields('noo_property_near_by');
$term = array(
'active'             => esc_html__( 'Active Life', 'noo' ),
'arts'               => esc_html__( 'Arts & Entertainment', 'noo' ),
'auto'               => esc_html__( 'Automotive', 'noo' ),
'beautysvc'          => esc_html__( 'Beauty & Spas', 'noo' ),
'education'          => esc_html__( 'Education', 'noo' ),
'eventservices'      => esc_html__( 'Event Planning & Services', 'noo' ),
'financialservices'  => esc_html__( 'Financial Services', 'noo' ),
'food'               => esc_html__( 'Food', 'noo' ),
'health'             => esc_html__( 'Health & Medical', 'noo' ),
'homeservices'       => esc_html__( 'Home Services ', 'noo' ),
'hotelstravel'       => esc_html__( 'Hotels & Travel', 'noo' ),
'localflavor'        => esc_html__( 'Local Flavor', 'noo' ),
'localservices'      => esc_html__( 'Local Services', 'noo' ),
'massmedia'          => esc_html__( 'Mass Media', 'noo' ),
'nightlife'          => esc_html__( 'Nightlife', 'noo' ),
'pets'               => esc_html__( 'Pets', 'noo' ),
'professional'       => esc_html__( 'Professional Services', 'noo' ),
'publicservicesgovt' => esc_html__( 'Public Services & Government', 'noo' ),
'realestate'         => esc_html__( 'Real Estate', 'noo' ),
'religiousorgs'      => esc_html__( 'Religious Organizations', 'noo' ),
'restaurants'        => esc_html__( 'Restaurants', 'noo' ),
'shopping'           => esc_html__( 'Shopping', 'noo' ),
'transport'          => esc_html__( 'Transportation', 'noo' ),
'trainstations'      => esc_html__( 'Train Stations', 'noo' )
);
$term_selected = re_get_property_near_by_setting('yelp_term',array());
wp_enqueue_style('vendor-chosen-css');
wp_enqueue_script('vendor-chosen-js');

?>
<h3><?php echo __('Nearby Places Settings','noo');?></h3>
<table class="form-table" cellspacing="0">
	<tbody>
		<tr>
			<th>
				<?php  esc_html_e('Enable/Disable','noo'); ?>
			</th>
			<td>
				<input type="hidden" value="0" name="noo_property_near_by[yelp_on]">
				<input type="checkbox" value="1" <?php checked(re_get_property_near_by_setting('yelp_on','1'), '1'); ?> name="noo_property_near_by[yelp_on]">
				<small><?php _e('Show yelp on property detail page.', 'noo'); ?></small>
			</td>
		</tr>
		<tr>
			<th>
				<?php  esc_html_e('Yelp API Key','noo'); ?>
			</th>
			<td>
				<input type="text" class="regular-text" value="<?php echo re_get_property_near_by_setting('yelp_api_key','')?>" name="noo_property_near_by[yelp_api_key]">
			</td>
		</tr>
		<tr>
			<th>
				<?php  esc_html_e('Select Term','noo'); ?>
			</th>
			<td>
				<?php if($term): ?>
				<select class="yelp_term" name="noo_property_near_by[yelp_term][]" multiple="multiple" style="min-width: 300px;">
					<?php foreach ((array)$term as $key=>$term): ?>
					<option value="<?php echo esc_attr($key)?>" <?php if(in_array($key, $term_selected)):?> selected<?php endif;?>><?php echo ucfirst($term)?></option>
					<?php endforeach;?>
				</select>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						jQuery("select.yelp_term").chosen({
							"disable_search_threshold":20
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
		<tr>
			<th>
				<?php  esc_html_e('Result Limit','noo'); ?>
			</th>
			<td>
				<input type="text" class="regular-text" value="<?php echo re_get_property_near_by_setting('yelp_limit','')?>" name="noo_property_near_by[yelp_limit]">
			</td>
			
		</tr>
		<tr>
			<th>
				<?php  esc_html_e('Show/Hidden','noo'); ?>
			</th>
			<td>
				<input type="hidden" value="0" name="noo_property_near_by[yelp_term_img]">
				<input type="checkbox" value="1" <?php checked(re_get_property_near_by_setting('yelp_term_img','1'), '1'); ?> name="noo_property_near_by[yelp_term_img]">
				<p><small><?php echo __( 'Show images Yelp place on property detail page.', 'noo' ); ?></small></p>
			</td>
		</tr>
		
	</tbody>
</table>

<h3><?php echo __('Walkscore API','noo');?></h3>
<table class="form-table" cellspacing="0">
	<tbody>
		<tr>
			<th>
				<?php  esc_html_e('Enable/Disable','noo'); ?>
			</th>
			<td>
				<input type="hidden" value="0" name="noo_property_near_by[walkscore_on]">
				<input type="checkbox" value="1" <?php checked(re_get_property_near_by_setting('walkscore_on','1'), '1'); ?> name="noo_property_near_by[walkscore_on]">
				<small><?php _e('Show Walkscore on property detail page.', 'noo'); ?></small>
			</td>
		</tr>
		<tr>
			<th>
				<?php  esc_html_e('Walkscore API Key','noo'); ?>
			</th>
			<td>
				<input type="text" class="regular-text" value="<?php echo re_get_property_near_by_setting('walkscore_api_key','')?>" name="noo_property_near_by[walkscore_api_key]"><br>
				<small><?php _e('Please enter your Walkscore API Key', 'noo'); ?></small><br>
				<small><?php _e('<strong>Walk Score</strong> requires that you register an API Key to display <strong>Walk Score</strong> on from your website. To know how to create this application, <a href="https://www.walkscore.com/professional/api.php">click here and follow the steps</a>.', 'noo'); ?></small>
			</td>
		</tr>
	</tbody>
</table>
<?php
}
endif;