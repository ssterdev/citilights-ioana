<?php
if(!class_exists('NooProperty')):
	class NooProperty{
		
		public static function enqueue_gmap_js( $load_map_data = false ) {
			re_property_enqueue_gmap_script( $load_map_data );
		}

		public static function is_noo_property_query( $query = null ) {
			return re_is_property_query( $query );
		}
		
		public static function get_general_option($id,$default = null){
			return re_get_property_setting( $id, $default );
		}
		
		public static function get_contact_option($id,$default = null){
			return re_get_property_contact_setting( $id, $default );
		}
		
		public static function get_custom_field_option($id,$default = null){
			return re_get_property_cf_setting( $id, $default );
		}
		
		public static function get_feature_option($id,$default = null){
			return re_get_property_feature_setting( $id, $default );
		}

		public static function get_custom_features( $translated = true ) {
			return re_get_property_feature_fields( $translated );
		}
		
		public static function get_advanced_search_option($id,$default = null){
			return re_get_property_search_setting( $id, $default );
		}
		
		public static function get_google_map_option($id,$default = null){
			return re_get_property_map_setting( $id, $default );
		}
		
		public static function get_similar_property(){
			re_similar_property();
		}
		
		public static function get_price_html($post_id,$label = true){
			return re_get_property_price_html( $post_id, $label );
		}
		
		public static function get_area_html($post_id){
			return re_get_property_area_html( $post_id );
		}
		
		/**
		 * Format the price with a currency symbol.
		 * @param float $price
		 * @return string
		 */
		public static function format_price($price,$html = true){
			return re_format_price( $price, $html );
		}

		private static function inr_comma($input, $thousands_sep = ',') {
			return _re_price_inr_comma( $input, $thousands_sep );
		}

		// Create custom function because some currency need special treat
		private static function number_format($num, $num_decimals = 2, $decimal_sep = '.', $thousands_sep = ',', $currency_code = '' ) {
			return _re_price_number_format( $num, $num_decimals, $decimal_sep, $thousands_sep, $currency_code );
		}
		
		public static function get_properties_markers($args = array()){
			return re_get_property_markers( $args );
		}
		
		public static function advanced_map_search_field($field='',$show_status=false){
			return re_property_advanced_search_field( $field );
		}
		
		public static function advanced_map($args = array()){
			re_property_advanced_map( $args );
		}
		
		public static function get_currencies() {
			return re_get_currencies();
		}
		
		public static function get_currency_symbol( $currency = '' ) {
			return re_get_currency_symbol( $currency );
		}
		
		public static function contact_agent(){
			re_property_contact_agent();
		}

		public static function social_share( $post_id = null ) {
			re_property_social_share( $post_id );
		}
		
		public static function display_detail($query=null){
			re_property_detail( $query );
		}

		public static function display_content($query='',$title='',$display_mode  = true,$default_mode = '',$show_pagination = false,$ajax_pagination=false,$show_orderby=false,$ajax_content=false,$default_orderby='date'){
			re_property_loop( compact( 'query', 'title', 'display_mode', 'default_mode', 'show_pagination', 'ajax_pagination', 'show_orderby', 'ajax_content', 'default_orderby' ) );
		}

		public static function get_property_summary( $args = '' ) {
			return re_property_summary( $args );
		}
	}
	new NooProperty();	
endif;