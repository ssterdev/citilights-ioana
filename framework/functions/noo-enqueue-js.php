<?php
/**
 * NOO Framework Site Package.
 *
 * Register Script
 * This file register & enqueue scripts used in NOO Themes.
 *
 * @package    NOO Framework
 * @version    1.0.0
 * @author     Kan Nguyen <khanhnq@nootheme.com>
 * @copyright  Copyright (c) 2014, NooTheme
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       http://nootheme.com
 */
// =============================================================================

//
// Site scripts
//
if ( ! function_exists( 'noo_enqueue_site_scripts' ) ) :
	function noo_enqueue_site_scripts() {

		$js_folder_uri = SCRIPT_DEBUG ? NOO_ASSETS_URI . '/js' : NOO_ASSETS_URI . '/js/min';
		$js_suffix = SCRIPT_DEBUG ? '' : '.min';

		// vendor script
		wp_register_script( 'vendor-modernizr', NOO_FRAMEWORK_URI . '/vendor/modernizr-2.7.1.min.js', null, null, false );
		
		wp_register_script( 'vendor-bootstrap', NOO_FRAMEWORK_URI . '/vendor/bootstrap.min.js', array( 'jquery' ), null, true );
		wp_register_script( 'vendor-hoverIntent', NOO_FRAMEWORK_URI . '/vendor/hoverIntent-r7.min.js', array( 'jquery' ), null, true );
		wp_register_script( 'vendor-superfish', NOO_FRAMEWORK_URI . '/vendor/superfish-1.7.4.min.js', array( 'jquery', 'vendor-hoverIntent' ), null, true );
    	wp_register_script( 'vendor-jplayer', NOO_FRAMEWORK_URI . '/vendor/jplayer/jplayer-2.5.0.min.js', array( 'jquery' ), null, true );
		
		wp_register_script( 'vendor-imagesloaded', NOO_FRAMEWORK_URI . '/vendor/imagesloaded.pkgd.min.js', null, null, true );
		wp_register_script( 'vendor-isotope', NOO_FRAMEWORK_URI . '/vendor/isotope-2.0.0.min.js', array('vendor-imagesloaded'), null, true );
		wp_register_script( 'vendor-infinitescroll', NOO_FRAMEWORK_URI . '/vendor/infinitescroll-2.0.2.min.js', null, null, true );
		wp_register_script( 'vendor-TouchSwipe', NOO_FRAMEWORK_URI . '/vendor/TouchSwipe/jquery.touchSwipe.min.js', array( 'jquery' ), null, true );
		wp_register_script( 'vendor-carouFredSel', NOO_FRAMEWORK_URI . '/vendor/carouFredSel/jquery.carouFredSel-6.2.1-packed.js', array( 'jquery', 'vendor-TouchSwipe' ), null, true );
		
		wp_register_script( 'vendor-easing', NOO_FRAMEWORK_URI . '/vendor/easing-1.3.0.min.js', array( 'jquery' ), null, true );
		wp_register_script( 'vendor-appear', NOO_FRAMEWORK_URI . '/vendor/jquery.appear.js', array( 'jquery','vendor-easing' ), null, true );
		wp_register_script( 'vendor-countTo', NOO_FRAMEWORK_URI . '/vendor/jquery.countTo.js', array( 'jquery', 'vendor-appear' ), null, true );
		wp_register_script( 'vc_pie_custom', "{$js_folder_uri}/jquery.vc_chart.custom{$js_suffix}.js",array('jquery','progressCircle','vendor-appear'), null, true);
		wp_enqueue_script('vendor-appear');
		/**
		 * Register owl carousel
		 */
		wp_register_script( 'owlcarousel', NOO_FRAMEWORK_URI . '/vendor/owl-carousel/owl.carousel.min.js', array( 'jquery' ), null, true );
		wp_register_style( 'owlcarousel', NOO_FRAMEWORK_URI . '/vendor/owl-carousel/owl.carousel.css', array(), NULL, 'all' );
		// owl Carousel 2
		wp_register_script( 'owlcarousel2', NOO_FRAMEWORK_URI . '/vendor/owl-carousel-2/owl.carousel.min.js', array( 'jquery' ), null, true );
		wp_register_style( 'owlcarousel2', NOO_FRAMEWORK_URI . '/vendor/owl-carousel-2/owl.carousel.css', array(), NULL, 'all' );
		wp_register_style( 'owlcarousel2-theme', NOO_FRAMEWORK_URI . '/vendor/owl-carousel-2/owl.theme.default.min.css', array(), NULL, 'all' );

        wp_register_script( '3dcarousel', NOO_FRAMEWORK_URI . '/vendor/video-slider.js', array( 'jquery' ), null, true );
        wp_register_style( '3dcarousel', NOO_FRAMEWORK_URI . '/vendor/jquery-feature-carousel/carousel.css', array(), NULL, 'all' );
        //wp_register_script( 'mousewheelAgent', NOO_FRAMEWORK_URI . '/vendor/jquery-feature-carousel/jquery.min.js', array( 'jquery' ), null, true );
        wp_register_script( 'counter', NOO_FRAMEWORK_URI . '/vendor/counter.js', array( 'jquery' ), null, true );


        wp_register_script( 'afterglow', NOO_FRAMEWORK_URI . '/vendor/afterglow/afterglow.min.js', array( 'jquery' ), null, true );

        //masonry Grid
        wp_register_script( 'masonry', NOO_FRAMEWORK_URI . '/vendor/masonry/masonry.pkgd.min.js', array( 'jquery' ), null, true );
        wp_register_script( 'isotope', NOO_FRAMEWORK_URI . '/vendor/masonry/isotope.pkgd.min.js', array( 'jquery' ), null, true );
		/**
		 * Register tooltipster
		 */
		wp_register_script( 'tooltipster', NOO_FRAMEWORK_URI . '/vendor/tooltipster/tooltipster.bundle.min.js', array( 'jquery' ), null, true );
		wp_register_style( 'tooltipster', NOO_FRAMEWORK_URI . '/vendor/tooltipster/tooltipster.bundle.min.css', array(), NULL, 'all' );

		wp_register_style('bootstrap-335','https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css');
		
		wp_register_script( 'vendor-nivo-lightbox-js', NOO_FRAMEWORK_URI . '/vendor/nivo-lightbox/nivo-lightbox.min.js', array( 'jquery' ), null, true );
		
		wp_register_script( 'vendor-parallax', NOO_FRAMEWORK_URI . '/vendor/jquery.parallax-1.1.3.js', array( 'jquery'), null, true );
		wp_register_script( 'vendor-nicescroll', NOO_FRAMEWORK_URI . '/vendor/nicescroll-3.5.4.min.js', array( 'jquery' ), null, true );
		
		// BigVideo scripts.
		wp_register_script( 'vendor-bigvideo-video',        NOO_FRAMEWORK_URI . '/vendor/bigvideo/video-4.1.0.min.js',        array( 'jquery', 'jquery-ui-slider', 'vendor-imagesloaded' ), NULL, true );
		wp_register_script( 'vendor-bigvideo-bigvideo',     NOO_FRAMEWORK_URI . '/vendor/bigvideo/bigvideo-1.0.0.min.js',     array( 'jquery', 'jquery-ui-slider', 'vendor-imagesloaded', 'vendor-bigvideo-video' ), NULL, true );

		// Bootstrap WYSIHTML5
		wp_register_script( 'vendor-bootstrap-wysihtml5', NOO_FRAMEWORK_URI . '/vendor/bootstrap-wysihtml5/bootstrap3-wysihtml5.custom.min.js', array( 'jquery', 'vendor-bootstrap'), null, true );
		
		wp_register_script( 'noo-script', "{$js_folder_uri}/noo{$js_suffix}.js", array( 'jquery','vendor-bootstrap', 'vendor-superfish', 'vendor-jplayer', 'jquery-ui-slider', 'jquery-touch-punch' ), null, true );

		// Bing map
		
		$latitude      = re_get_property_map_setting('latitude','40.714398');
		$longitude     = re_get_property_map_setting('longitude','-74.005279');
		$zoom 		   = re_get_property_map_setting('zoom','17');
		$bing_api	   = re_get_property_map_setting( 'bing_api', '' );
		wp_register_script( 'bing-map-api', 'https://www.bing.com/api/maps/mapcontrol?key='.$bing_api.'&callback=Noo_Bing_Map', array( 'jquery' ), null, true );
		wp_register_script( 'bing-map', "{$js_folder_uri}/bing-map{$js_suffix}.js", array( 'jquery'), null, true );
		$nooBingMap = array(
			'latitude'=>$latitude,
			'longitude'=>$longitude,
			'zoom' 	=> $zoom,
		);
		wp_localize_script('bing-map','nooBingMap',$nooBingMap);

		wp_localize_script( 'bing-map', 'NooPropertyBingMap', array(
			'ajax_url'           => admin_url('admin-ajax.php'),
			'security'           => wp_create_nonce('noo-property-map'),
			'loading'            => get_template_directory_uri() . '/assets/images/loading.gif',
			'results_search'     => esc_html__( 'We found %d results. Do you want to load the results now?', 'noo' ),
			'no_results_search'  => esc_html__( 'We found no results', 'noo' ),
			'icon_bedrooms'		 => get_stylesheet_directory_uri() . '/assets/images/bedroom-icon.png',
			'icon_bathrooms'     => get_stylesheet_directory_uri() . '/assets/images/bathroom-icon.png',
			'icon_area'          => get_stylesheet_directory_uri() . '/assets/images/size-icon.png'
		) );
				
		$google_api = re_get_property_map_setting( 'google_api', '' );
		$name_script_map = 'googleapis';
        // if ( defined( 'DSIDXPRESS_PLUGIN_URL' ) ) {
        //     $name_script_map = 'googlemaps3';
        // }
		wp_register_script( $name_script_map,'http'.(is_ssl() ? 's':'').'://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places' . ( !empty( $google_api ) ? '&key=' .$google_api : '' ), array('jquery'), null , true);
		
		wp_register_script( 'google-map-infobox', "{$js_folder_uri}/infobox{$js_suffix}.js", array( 'jquery' , $name_script_map ), null, true );
		wp_register_script( 'vendor-form', NOO_FRAMEWORK_URI . '/vendor/jquery.form.min.js', array( 'jquery' ), null, true );
		wp_register_script( 'google-map-markerclusterer', "{$js_folder_uri}/markerclusterer{$js_suffix}.js", array( 'jquery' , $name_script_map ), null, true );

		wp_register_script( 'noo-property-map', "{$js_folder_uri}/property-map{$js_suffix}.js", array( 'jquery', 'vendor-form', 'google-map-infobox', 'google-map-markerclusterer'), null, true );
		wp_register_script( 'noo-property', "{$js_folder_uri}/property{$js_suffix}.js", array( 'jquery','vendor-carouFredSel','vendor-imagesloaded'), null, true );
		
		wp_register_script( 'vendor-chosen', NOO_FRAMEWORK_URI . '/vendor/chosen/chosen.jquery.min.js', array( 'jquery'), null, true );

		wp_localize_script( 'vendor-chosen', 'noo_chosen', array(
			'multiple_text'		=> __('Select Some Options', 'noo'),
			'single_text'		=> __('Select an Option', 'noo'),
			'no_result_text'	=> __('No results match', 'noo')
		));

		wp_register_script( 'noo-img-uploader', "{$js_folder_uri}/noo-img-uploader{$js_suffix}.js", array( 'jquery', 'plupload-all', 'jquery-ui-sortable' ), null, true );
		
		wp_localize_script('noo-img-uploader', 'noo_img_upload', array(
			'ajaxurl'        => admin_url('admin-ajax.php'),
			'nonce'          => wp_create_nonce('aaiu_upload'),
			'remove'         => wp_create_nonce('aaiu_remove'),
			'max_files'      =>0,
			'upload_enabled' => true,
			'confirmMsg'     => __('Are you sure you want to delete this?', 'noo'),
			'file_ext_thumbnail'     => get_stylesheet_directory_uri() . '/assets/images/file-icon.png',
			'plupload'       => array(
				'runtimes'         => 'html5,flash,html4',
				'browse_button'    => 'aaiu-uploader',
				'container'        => 'aaiu-upload-container',
				'file_data_name'   => 'aaiu_upload_file',
				'max_file_size'    => (100 * 1000 * 1000) . 'b',
				'url'              => admin_url('admin-ajax.php') . '?action=noo_upload&nonce=' . wp_create_nonce('aaiu_allow'),
				'flash_swf_url'    => includes_url('js/plupload/plupload.flash.swf'),
				'filters'          => array(array('title' => __('Allowed Files', 'noo'), 'extensions' => "jpg,jpeg,gif,png")),
				'multipart'        => true,
				'urlstream_upload' => true,
			)
		));

		/**
		 * Localize map
		 */
		wp_localize_script( 'noo-property-map', 'NooPropertyMap', array(
			'ajax_url'           => admin_url('admin-ajax.php'),
			'security'           => wp_create_nonce('noo-property-map'),
			'loading'            => get_template_directory_uri() . '/assets/images/loading.gif',
			'results_search'     => esc_html__( 'We found %d results. Do you want to load the results now?', 'noo' ),
			'no_results_search'  => esc_html__( 'We found no results', 'noo' ),
			'icon_bedrooms'		 => get_stylesheet_directory_uri() . '/assets/images/bedroom-icon.png',
			'icon_bathrooms'     => get_stylesheet_directory_uri() . '/assets/images/bathroom-icon.png',
			'icon_area'          => get_stylesheet_directory_uri() . '/assets/images/size-icon.png'
		) );

		if ( ! is_admin() ) {
			// Post type Property
			wp_enqueue_script( 'vendor-modernizr' );
			
			if( noo_get_option( 'noo_smooth_scrolling', true ) ) {
				wp_enqueue_script('vendor-nicescroll');
			}			

			// Required for nested reply function that moves reply inline with JS
			if ( is_singular() ) wp_enqueue_script( 'comment-reply' );

			//if ( is_masonry_style() ) {
			//	wp_enqueue_script('vendor-infinitescroll');
			//	wp_enqueue_script('vendor-isotope');
			//}

			$is_agents			= is_post_type_archive( 'noo_agent' );
			$is_properties		= is_post_type_archive( 'noo_property' );
			$is_property		= is_singular( 'noo_property' );
			$is_shop			= NOO_WOOCOMMERCE_EXIST && is_shop();
			$is_product			= NOO_WOOCOMMERCE_EXIST && is_product();
			$nooL10n = array(
				'ajax_url'        => admin_url( 'admin-ajax.php', 'relative' ),
				'security' 		  => wp_create_nonce( 'noo-security' ),
				'home_url'        => noo_citilights_get_current_url(),
				'theme_dir'		  => get_template_directory(),
				'theme_uri'		  => get_template_directory_uri(),
				'is_logged_in'    => is_user_logged_in() ? 'true' : 'false',
				'is_blog'         => is_home() ? 'true' : 'false',
				'is_archive'      => is_post_type_archive('post') ? 'true' : 'false',
				'is_single'       => is_single() ? 'true' : 'false',
				'is_agents'       => $is_agents ? 'true' : 'false',
				'is_properties'   => $is_properties ? 'true' : 'false',
				'is_property'     => $is_property ? 'true' : 'false',
				'is_shop'   	  => $is_shop ? 'true' : 'false',
				'is_product'      => $is_product ? 'true' : 'false',
				'wrong_pass'	  => esc_html__( 'Password do not match', 'noo' ),
				'notice_empty'	  => esc_html__( 'Not an empty value, please enter a value', 'noo' )
			);
			
			wp_localize_script('noo-script', 'nooL10n', $nooL10n);
			wp_enqueue_script( 'noo-script' );

			if( class_exists( 'NooProperty' ) ) {
				$nooPropertyL10n = array(
					'ajax_url'           => admin_url( 'admin-ajax.php', 'relative' ),
					'ajax_finishedMsg'   =>__('All posts displayed','noo'),
					'security'           => wp_create_nonce( 'property_security' ),
					'notice_max_compare' => esc_html__( 'The maximum number of properties compared to the main property is 4', 'noo' )
				);
				wp_localize_script('noo-property', 'nooPropertyL10n', $nooPropertyL10n);
				wp_enqueue_script( 'noo-property' );

				wp_register_script( 'noo-dashboard', "{$js_folder_uri}/dashboard{$js_suffix}.js", array( 'jquery', 'vendor-bootstrap-wysihtml5', 'noo-img-uploader', 'vendor-chosen' ), null, true );
				wp_localize_script( 'noo-dashboard', 'noo_dashboard', array(
					'delete_property'    => wp_create_nonce('noo_delete_property'),
					'featured_property'  => wp_create_nonce('noo_featured_property'),
					'status_property'    => wp_create_nonce('noo_status_property'),
					'listing_payment'    => wp_create_nonce('noo_listing_payment'),
					'confirmDeleteMsg'   => __('Are you sure you want to delete this Property? This action can\'t be undone.', 'noo'),
					'confirmFeaturedMsg' => __('The number of featured items will be subtracted from your package. This action can\'t be undone. Are you sure you want to do it?', 'noo'),
					'confirmStatusMsg'   => __('Are you sure you want to mark this property as Sold/Rent? This action can\'t be undone.', 'noo'),
					'style_rtl'			 => is_rtl() ? NOO_FRAMEWORK_URI . '/vendor/bootstrap-wysihtml5/stylesheet-rtl.css' : '',
					'chosen_multiple_text'		=> __('Select Some Options', 'noo'),
					'chosen_single_text'		=> __('Select an Option', 'noo'),
					'chosen_no_result_text'		=> __('No results match', 'noo')
				));

				if( re_is_dashboard_page() ) {
					wp_enqueue_script('noo-dashboard');
				}
			}
		}

		/**
		 * Upload
		 */
			wp_register_style( 'noo-upload', NOO_ASSETS_URI . '/css/noo-upload.css', NULL, NULL, 'all' );

			wp_register_script( 'noo-upload', NOO_ASSETS_URI . '/js/noo-upload.js', array( 'jquery', 'plupload-all', 'jquery-ui-sortable' ), null, false );
			wp_localize_script( 'noo-upload', 'NooUpload', array(
				'ajax_url'             => admin_url( 'admin-ajax.php' ),
				'security'             => wp_create_nonce( 'noo-upload' ),
				'text_max_size_upload' => wp_create_nonce( 'noo-upload' ),
				'remove_image'		   => esc_html__( 'Remove image', 'noo' ),
				'allow_format'		   => 'jpg,jpeg,gif,png',
				'flash_swf_url'        => includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url'  => includes_url('js/plupload/plupload.silverlight.xap'),
			) );

	}
add_action( 'wp_enqueue_scripts', 'noo_enqueue_site_scripts' );
endif;
