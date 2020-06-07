<?php
if( !function_exists( 're_property_template_loader') ) :
	function re_property_template_loader($template){
		if(is_tax('property_category') || is_tax('property_status') || is_tax('property_location') || is_tax('property_sub_location') || is_tax('property_label')){
			$template       = locate_template( 'taxonomy-property_category.php' );
		}
		return $template;
	}
	add_filter( 'template_include', 're_property_template_loader' );
endif;

if( !function_exists( 're_property_post_class') ) :
	function re_property_post_class($output) {
		$post_id = get_the_ID();
		if( 'noo_property' == get_post_type($post_id) ) {
			if( 'yes' == get_post_meta( $post_id, '_featured', true ) ) {
				$output[] = 'featured-property';
			}
		}
		
		return $output;
	}
	add_filter( 'post_class', 're_property_post_class' );
endif;

if( !function_exists( 're_property_loop') ) :
	function re_property_loop( $args = array() ) {
		re_property_enqueue_gmap_script();
		wp_enqueue_script('noo-property');
		$defaults = array(
			'query'           => null,
			'title'           => '',
			'display_mode'    => true,
			'default_mode'    => get_theme_mod('noo_property_listing_layout','grid'),
			'show_pagination' => false,
			'ajax_pagination' => false,
			'show_orderby'    => false,
			'ajax_content'    => false,
			'default_orderby' => 'date',
			'prop_style' => 'style-1',
			'display_style'	  => get_theme_mod( 'noo_property_display_style', 'style-1' )
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args );
		global $wp_query;
		if(!empty($query)){
			$wp_query = $query;
		}

		$mode = (isset($_GET['mode']) ? $_GET['mode'] : $default_mode);
		$is_fullwidth = false;
		if(is_post_type_archive('noo_property')
				|| is_tax('property_status')
				|| is_tax('property_sub_location')
				|| is_tax('property_location')
				|| is_tax('property_category')){
			$noo_property_layout =  get_theme_mod('noo_property_layout','fullwidth');
			if($noo_property_layout == 'fullwidth'){
				$is_fullwidth = true;
			}
		}
		ob_start();
        include(locate_template("layouts/noo-property-loop.php"));
        echo ob_get_clean();
		wp_reset_query();
	}
endif;

if( !function_exists( 're_property_detail') ) :
	function re_property_detail($query=null){
		re_property_enqueue_gmap_script();
		wp_enqueue_script( 'noo-property' );
		wp_enqueue_script( 'vendor-nivo-lightbox-js' );
		wp_enqueue_style( 'vendor-nivo-lightbox-default-css' );

		if(empty($query)){
			global $wp_query;
			$query = $wp_query;
		}
		
		ob_start();
        include(locate_template("layouts/noo-property-detail.php"));
        echo ob_get_clean();
		wp_reset_query();
	}
endif;

if( !function_exists( 're_similar_property') ) :
	function re_similar_property() {
		ob_start();
        include(locate_template("layouts/noo-property-similar.php"));
        echo ob_get_clean();
		wp_reset_query();
		wp_reset_postdata();
	}
endif;

if( !function_exists( 're_property_social_share') ) :
	function re_property_social_share( $post_id = null ) {

		$post_id = (null === $post_id) ? get_the_id() : $post_id;
		$post_type =  get_post_type($post_id);

		if( $post_type != 'noo_property' ) {
			echo '';
			return false;
		}

		$prefix        = 'noo_property';

		$share_url     = urlencode( get_permalink() );
		$share_title   = urlencode( get_the_title() );
		$share_source  = urlencode( get_bloginfo( 'name' ) );
		$share_content = urlencode( get_the_content() );
		$share_media   = wp_get_attachment_thumb_url( get_post_thumbnail_id() );
		$popup_attr    = 'resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0';

		$social_enabled = get_theme_mod("noo_property_social", true );

		$facebook     = $social_enabled ? get_theme_mod( "{$prefix}_social_facebook", true ) : false;
		$twitter      = $social_enabled ? get_theme_mod( "{$prefix}_social_twitter", true ) : false;
		$google		  = $social_enabled ? get_theme_mod( "{$prefix}_social_google", true ) : false;
		$pinterest    = $social_enabled ? get_theme_mod( "{$prefix}_social_pinterest", false ) : false;
		$linkedin     = $social_enabled ? get_theme_mod( "{$prefix}_social_linkedin", false ) : false;
		$print        = get_theme_mod( "{$prefix}_print_button", false );
		$pdf_file     = noo_get_post_meta( $post_id, '_pdf_file', true );
		$pdf_file 	  = wp_get_attachment_url( $pdf_file ); 

		$html = array();

		if ( $facebook || $twitter || $google || $pinterest || $linkedin || $print || $pdf_file ) {
			$html[] = '<div class="property-share hidden-print clearfix">';
			
			if($pdf_file) {
				$html[] = '<a download href="' . esc_url($pdf_file) . '" class="fa fa-download action" title="' . __( 'Download File', 'noo' ) . '"></a>';
			}

			if($print && is_single()) {
				$html[] = '<a href="javascript:void(0)" onclick="return window.print();" class="fa fa-print action" title="' . __( 'Print this property', 'noo' ) . '"></a>';
			}

			if($facebook) {
				$html[] = '<a href="#share" data-toggle="tooltip" data-placement="bottom" data-trigger="hover" class="fa fa-facebook"'
							. ' title="' . __( 'Share on Facebook', 'noo' ) . '"'
							. ' onclick="window.open(' 
								. "'http://www.facebook.com/sharer.php?u={$share_url}&amp;t={$share_title}','popupFacebook','width=650,height=270,{$popup_attr}');"
								. ' return false;">';
				$html[] = '</a>';
			}

			if($twitter) {
				$html[] = '<a href="#share" class="fa fa-twitter"'
							. ' title="' . __( 'Share on Twitter', 'noo' ) . '"'
							. ' onclick="window.open('
								. "'https://twitter.com/intent/tweet?text={$share_title}&amp;url={$share_url}','popupTwitter','width=500,height=370,{$popup_attr}');"
								. ' return false;">';
				$html[] = '</a>';
			}

			if($google) {
				$html[] = '<a href="#share" class="fa fa-google-plus"'
							. ' title="' . __( 'Share on Google+', 'noo' ) . '"'
							. ' onclick="window.open('
								. "'https://plus.google.com/share?url={$share_url}','popupGooglePlus','width=650,height=226,{$popup_attr}');"
								. ' return false;">';
				$html[] = '</a>';
			}

			if($pinterest) {
				$html[] = '<a href="#share" class="fa fa-pinterest"'
							. ' title="' . __( 'Share on Pinterest', 'noo' ) . '"'
							. ' onclick="window.open('
								. "'http://pinterest.com/pin/create/button/?url={$share_url}&amp;media={$share_media}&amp;description={$share_title}','popupPinterest','width=750,height=265,{$popup_attr}');"
								. ' return false;">';
				$html[] = '</a>';
			}

			if($linkedin) {
				$html[] = '<a href="#share" class="fa fa-linkedin"'
							. ' title="' . __( 'Share on LinkedIn', 'noo' ) . '"'
							. ' onclick="window.open('
								. "'http://www.linkedin.com/shareArticle?mini=true&amp;url={$share_url}&amp;title={$share_title}&amp;summary={$share_content}&amp;source={$share_source}','popupLinkedIn','width=610,height=480,{$popup_attr}');"
								. ' return false;">';
				$html[] = '</a>';
			}

			$html[] = '</div>'; // .agent-social
		}

		echo implode("\n", $html);
	}
endif;

if( !function_exists( 're_property_contact_agent') ) :
	function re_property_contact_agent( $property_id = 0 ) {
		$property_id = empty( $property_id ) ? get_the_ID() : $property_id;
		$agent_id = get_post_meta($property_id,'_agent_responsible',true);
		if(empty($agent_id))
			return '';
	
		ob_start();
        include(locate_template("layouts/noo-property-contact.php"));
        echo ob_get_clean();
	}
endif;

