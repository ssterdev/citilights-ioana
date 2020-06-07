<?php
if( !function_exists( 're_property_enqueue_map_picker_script') ) :
	function re_property_enqueue_map_picker_script(){
		$js_folder_uri = SCRIPT_DEBUG ? NOO_ASSETS_URI . '/js' : NOO_ASSETS_URI . '/js/min';
		$js_suffix     = SCRIPT_DEBUG ? '' : '.min';
		
		$google_api    = re_get_property_map_setting( 'google_api', '' );
		$bing_api	   = re_get_property_map_setting( 'bing_api', '' );

		$latitude      = re_get_property_map_setting('latitude','40.714398');
		$longitude     = re_get_property_map_setting('longitude','-74.005279');
		$zoom 		   = re_get_property_map_setting('zoom','17');
		
		$lat           = $long = '';
		if( is_page() && ( 'agent_dashboard_submit.php' == get_page_template() ) ) {
			if( isset( $_GET['prop_edit'] ) && is_numeric( $_GET['prop_edit'] ) ) {
				$edit_id =  intval ($_GET['prop_edit']);
				$lat     = get_post_meta($edit_id,'_noo_property_gmap_latitude',true);
				$long    = get_post_meta($edit_id,'_noo_property_gmap_longitude',true);
			}
			$no_html     = array();
			if( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
				$lat	= wp_kses( $_POST['lat'], $no_html );
				$long	= wp_kses( $_POST['long'], $no_html );
			}
		} elseif( 'noo_property' == get_post_type() ) {
			global $post;
			$lat     = get_post_meta($post->ID,'_noo_property_gmap_latitude',true);
			$long    = get_post_meta($post->ID,'_noo_property_gmap_longitude',true);
		}
		
		$latitude = !empty( $lat ) ? $lat : $latitude;
		$longitude = !empty( $long ) ? $long : $longitude;
		$nooGoogleMap = array(
			'latitude'=>$latitude,
			'longitude'=>$longitude,
		);

		// Bing map
		wp_register_script( 'bing-map-api', 'https://www.bing.com/api/maps/mapcontrol?key='.$bing_api.'&callback=Noo_Bing_Map', array( 'jquery' ), null, true );
		wp_register_script( 'bing-map', "{$js_folder_uri}/bing-map{$js_suffix}.js", array( 'jquery'), null, true );
		$nooBingMap = array(
			'latitude'=>$latitude,
			'longitude'=>$longitude,
			'zoom' 	=> $zoom,
		);
		wp_localize_script('bing-map','nooBingMap',$nooBingMap);

		// Google map
		wp_localize_script('noo-property-google-map', 'nooGoogleMap', $nooGoogleMap);

		wp_enqueue_script('noo-property-google-map');

		
		wp_register_script( 'google-map','http'.(is_ssl() ? 's':'').'://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places' . ( !empty( $google_api ) ? '&key=' .$google_api : '' ),array('jquery'), '1.0', false );
		wp_register_script( 'noo-property-google-map', "{$js_folder_uri}/map-picker{$js_suffix}.js", array( 'google-map'), null, true );
		
		wp_localize_script('noo-property-google-map', 'nooGoogleMap', $nooGoogleMap);
		wp_enqueue_script('noo-property-google-map');
	}
endif;

if( !function_exists( 're_property_enqueue_gmap_script') ) :
	function re_property_enqueue_gmap_script( $load_map_data = false ) {
		global $has_map_data;

		if( wp_script_is( 'noo-property-map', 'enqueued' ) ) {
			// return if loaded and no need for reload
			if( $has_map_data || !$load_map_data ) {
				return;
			} else {
				wp_dequeue_script( 'google-map');
				wp_dequeue_script( 'noo-property-map');
			}
		}

		if( !$has_map_data ) {
			$has_map_data = $load_map_data;
		}

		$latitude = re_get_property_map_setting('latitude','40.714398');
		$longitude = re_get_property_map_setting('longitude','-74.005279');

		/**
		 * Show custom fields
		 */
		$list_custom_fields = re_get_property_custom_fields();
		$custom_fields  = array();
		foreach ( $list_custom_fields as $item ) :

			if ( $item['name'] !== '_area' && $item['name'] !== '_bedrooms' && $item['name'] !== '_bathrooms' ) :

				$field_item[$item['name']] = '';
				
				$custom_fields = array_merge( $custom_fields, $field_item );
					
			endif;

		endforeach;

		$nooGmapL10n = array(
			'ajax_url'                => admin_url( 'admin-ajax.php', 'relative' ),
			'home_url'                => get_site_url(),
			'url_idx'          	   	  => home_url( '/idx/' ),
			'theme_dir'               => get_template_directory(),
			'theme_uri'               => is_file( get_stylesheet_directory_uri() . '/assets/images/cloud.png' ) ? get_stylesheet_directory_uri() : get_template_directory_uri(),
			'latitude'                =>$latitude,
			'longitude'               =>$longitude,
			'maxZoom_MarkerClusterer' =>5,
			'zoom'                    =>re_get_property_map_setting('zoom',12),
			'fitbounds'               =>re_get_property_map_setting('fitbounds','1') ? true : false,
			'draggable'               =>re_get_property_map_setting('draggable','1') ? true : false,
			'area_unit'               => re_get_property_setting('area_unit'),
			'thousands_sep'           => wp_specialchars_decode( stripslashes(re_get_property_setting('price_thousand_sep')),ENT_QUOTES),
			'decimal_sep'             => wp_specialchars_decode( stripslashes(re_get_property_setting('price_decimal_sep')),ENT_QUOTES),
			'num_decimals'            => re_get_property_setting('price_num_decimals'),
			'currency'                =>re_get_currency_symbol(re_get_property_setting('currency')),
			'currency_position'       =>re_get_property_setting('currency_position','left'),
			'default_label'           =>'',
			'fullscreen_label'        =>'',
			'no_geolocation_pos'      =>__("The browser couldn't detect your position!",'noo'),
			'no_geolocation_msg'      =>__("Geolocation is not supported by this browser.",'noo'),
			'markers'                 => ( $has_map_data ? noo_citilights_get_list_markers() : json_encode(array()) ),
			'ajax_finishedMsg'        =>__('All posts displayed','noo'),
			'custom_fields'			  => $custom_fields
		);
		wp_localize_script('noo-property-map', 'nooGmapL10n', $nooGmapL10n);

		// Remove conflict with dsIDXpress
		if( !wp_script_is( 'googlemaps3', 'enqueued' ) ) {
			wp_enqueue_script('google-map');
		}
		wp_enqueue_script( 'noo-property-map' );
		wp_enqueue_script('bing-map-api');
		wp_enqueue_script('bing-map');
	}
endif;
