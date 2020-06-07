<?php
if( !function_exists( 're_get_property_markers') ) :
	function re_get_property_markers( $args = array() ) {
		$defaults = array(
				'post_type'     =>  'noo_property',
				'post_status'   =>  'publish',
				'nopaging'      =>  'true'
		);
		$markers = array();
		$args = wp_parse_args($args,$defaults);
		// global $noo_show_sold;
		// $noo_show_sold  = true;
		$properties = new WP_Query($args);
		// $noo_show_sold = false;
		if($properties->have_posts()){
			while ($properties->have_posts()): $properties->the_post();
				$post_id =  get_the_ID();
				$lat     =  esc_html(get_post_meta($post_id, '_noo_property_gmap_latitude', true));
				$long    =  esc_html(get_post_meta($post_id, '_noo_property_gmap_longitude', true));
				if( empty( $lat ) || empty( $long ) ) {
					continue;
				}
				$title   =  wp_trim_words(get_the_title($post_id),7);
				$image   = '<img width="190" height="160" class="attachment-property-infobox size-property-infobox wp-post-image" src="'.get_template_directory_uri().'/assets/images/placeholder.jpg">';
				if(has_post_thumbnail($post_id))
					$image   =  get_the_post_thumbnail($post_id,'property-infobox');
				$bedrooms	 	= get_post_meta(get_the_ID(),'_bedrooms',true);
				$bathrooms		= get_post_meta(get_the_ID(),'_bathrooms',true);
				$price			= get_post_meta($post_id,'_price',true);
				$agent_id 		= get_post_meta($post_id,'_agent_responsible',true);
				
				$property_location     = array();
				$property_sub_location = array();
				$property_status       = array();
				$property_category     = array();
				$property_location_terms   		=   get_the_terms($post_id,'property_location' );
				if($property_location_terms && !is_wp_error($property_location_terms)){
					foreach($property_location_terms as $location_term){
						if(empty($location_term->slug))
							continue;
						$property_location[] = $location_term->slug;
						// break;
					}
				}
				$property_sub_location_terms   	=   get_the_terms($post_id,'property_sub_location' );
				if($property_sub_location_terms && !is_wp_error($property_sub_location_terms)){
					foreach($property_sub_location_terms as $sub_location_term){
						if(empty($sub_location_term->slug))
							continue;
						$property_sub_location[] = $sub_location_term->slug;
						// break;
					}
				}
				
				$property_status_terms   		=   get_the_terms($post_id,'property_status' );
				if($property_status_terms && !is_wp_error($property_status_terms)){
					foreach($property_status_terms as $status_term){
						if(empty($status_term->slug))
							continue;
						$property_status[] = $status_term->slug;
						// break;
					}
				}
				$property_category_terms          =   get_the_terms($post_id,'property_category' );
				$property_category_marker = '';
				if($property_category_terms && !is_wp_error($property_category_terms)){
					$map_markers = get_option( 'noo_category_map_markers' );
					foreach($property_category_terms as $category_term){
						if(empty($category_term->slug))
							continue;
						$property_category[] = $category_term->slug;
						if(isset($map_markers[$category_term->term_id]) && !empty($map_markers[$category_term->term_id])){
							$property_category_marker = wp_get_attachment_url($map_markers[$category_term->term_id]);
						}
						// break;
					}
				}

				$marker = array(
					'latitude'     => $lat,
					'longitude'    => $long,
					'image'        => $image,
					'title'        => $title,
					'area'         => re_get_property_area_html($post_id),
					'bedrooms'     => absint($bedrooms),
					'bathrooms'    => absint($bathrooms),
					'agent_search' => absint($agent_id),
					'price'        => re_format_price($price,false),
					'price_html'   => re_get_property_price_html($post_id),
					'info_summary' => re_property_summary( array( 'property_id' => $post_id, 'container_class' => 'info-detail' ) ),
					'url'          => get_permalink($post_id), 
					'location'     => $property_location,
					'sub_location' => $property_sub_location,
					'status'       => $property_status,
					'category'     => $property_category,
					'icon'         => $property_category_marker,
				);

				/**
				 * Show custom fields
				 */
				$custom_fields = re_get_property_custom_fields();
				$marker_merge  = array();
				foreach ( $custom_fields as $item ) :

					if ( $item['name'] !== '_area' && $item['name'] !== '_bedrooms' && $item['name'] !== '_bathrooms' ) :

						$meta_key = '_noo_property_field_' . $item['name'];

						$value = get_post_meta( $post_id, $meta_key, true );
						
						if ( !is_array( $value ) ) {
							$marker_merge[ $item['name'] ] = sanitize_title( $value );
						} else {
							$marker_merge[ $item['name'] ] = $value;
						}
						
					endif;

				endforeach;

				$marker = array_merge( $marker, $marker_merge );

				$markers[] = $marker;
			endwhile;
		}
		wp_reset_query();
		wp_reset_postdata();
		return json_encode($markers);
	}
endif;

if( !function_exists( 're_property_advanced_map') ) :
	function re_property_advanced_map( $args = array() ) {
		$defaults = array( 
			'gmap'						=> true,
			'btn_label'					=> '',
			'show_status'				=> false, 
			'map_class'					=> '',
			'search_info'				=> false,
			'no_search_container'		=> false, 
			'source'					=> 'property', 
			'idx_map_search_form'		=> false,
			'disable_search_form'		=> false,
			'show_advanced_search_field'=> false,
			'map_height'				=> '',
			'show_loading'				=> 'true',
			'search_info_title'			=> null,
			'search_info_content'		=> null,
			'form_layout'               => 'style-1',
			
		);
		$p = wp_parse_args($args,$defaults);
		extract($p);
		$result_pages = get_pages(
			array(
					'meta_key' => '_wp_page_template',
					'meta_value' => 'search-property-result.php'
			)
		);
		if($result_pages){
			$first_page = reset($result_pages);
			$result_page_url = get_permalink($first_page->ID);
			if(is_page($first_page->ID)){
				$show_status = true;
			}
		}else{
			$result_page_url = get_post_type_archive_link( 'noo_property' );
		}
		
		if(empty($btn_label))
			$btn_label=__('Search Property','noo');

		$map_class = !$gmap ? 'no-map ' . $map_class : $map_class;
		$map_class = $no_search_container ? 'no-container ' . $map_class : $map_class;
		$map_height = empty( $map_height ) ? re_get_property_map_setting('height', 400) : $map_height;
		$map_background = re_get_property_map_setting('map_background');
		$background_style = '';
		if( !empty( $map_background ) && $form_layout === 'style-1' ) {
			$background_style = 'style="background: url(' . esc_url_raw( $map_background ) . ') repeat-x scroll 0 center transparent;"';
		} elseif ($form_layout === 'style-2') {
            $background_style = 'style="background: none"';
        }
		?>
		<div class="noo-map <?php echo esc_attr($map_class)?>" <?php echo $background_style; ?>>
			<?php $map_type = re_get_property_map_setting('map_type',''); ?>
			<?php if ($map_type == 'google'): ?>
				<?php if ($gmap): ?>
					<div id="gmap" data-source="<?php echo $source?>" style="height: <?php echo $map_height; ?>px;" ></div>
					<div class="gmap-search">
						<input placeholder="<?php echo __('Search your map','noo')?>" type="text" autocomplete="off" id="gmap_search_input">
					</div>
					<div class="gmap-control">
						<a class="gmap-mylocation" href="#"><i class="fa fa-map-marker"></i><?php echo __('My Location','noo')?></a>
						<a class="gmap-full" href="#" data-mobile='true'><i class="fa fa-expand"></i></a>
						<a class="gmap-prev" href="#"><i class="fa fa-angle-left"></i></a>
						<a class="gmap-next" href="#"><i class="fa fa-angle-right"></i></a>
					</div>
					<div class="gmap-zoom">
						<a href="#" class="zoom-in"><i class="fa fa-plus"></i></a>
						<a href="#" class="zoom-out"><i class="fa fa-minus"></i></a>
					</div>
					<?php if ( !empty( $show_loading ) && $show_loading === 'true' ) : ?>
						<div class="gmap-loading"><?php _e('Loading Maps','noo');?>
					         <div class="gmap-loader">
					            <div class="rect1"></div>
					            <div class="rect2"></div>
					            <div class="rect3"></div>
					            <div class="rect4"></div>
					            <div class="rect5"></div>
					        </div>
					   </div>
				<?php endif;?>
				<?php endif ?>
			<?php elseif($map_type == 'bing'): ?>
				<?php if ($gmap): ?>
				<div class="noo-map-search">
	                 <div data-id="bmap" class="bmap" data-source="<?php echo $source?>" style="height: <?php echo esc_attr( $map_height ); ?>px;">
	                 	<div id="bmap"></div>
	                 </div>
	             </div>
	         <?php endif; ?>
			<?php endif ?>
			
			<?php if(!$disable_search_form && $source != 'IDX' && $form_layout === 'style-1') :
				ob_start();
				include(locate_template("layouts/noo-property-search.php"));
				echo ob_get_clean();
			endif;?>
            <?php if(!$disable_search_form && $source != 'IDX' && $form_layout === 'style-2') :
                ob_start();
                include(locate_template("layouts/noo-property-search-style-2.php"));
                echo ob_get_clean();
            endif;?>
			<?php if( $source == "IDX" && $idx_map_search_form == true) :
				ob_start();
				include(locate_template("layouts/noo-property-idx-search.php"));
				echo ob_get_clean();
			endif;?>

		</div>
		<?php
	}
endif;
