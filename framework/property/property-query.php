<?php
if( !function_exists( 're_is_property_query') ) :
	function re_is_property_query( $query = null ) {
		if( empty( $query ) ) return false;

		if( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] === 'noo_property' )
			return true;

		if( $query->is_tax ) {
			if( ( isset( $query->query_vars['property_category'] ) && !empty( $query->query_vars['property_category'] ) )
				|| ( isset( $query->query_vars['property_status'] ) && !empty( $query->query_vars['property_status'] ) )
				|| ( isset( $query->query_vars['property_location'] ) && !empty( $query->query_vars['property_location'] ) )
				|| ( isset( $query->query_vars['property_sub_location'] ) && !empty( $query->query_vars['property_sub_location'] ) ) 
			) {
				return true;
			}
		}

		return false;
	}
endif;

if( !function_exists( 're_property_pre_get_posts') ) :
	function re_property_pre_get_posts( $q ) {
		if( is_admin()&& is_post_type_archive('noo_agent') ) {
			return $q;
		}

		global $wpdb, $noo_show_sold;


		if( $q->is_main_query() && $q->is_singular ) {
			return;
		}

		if( re_is_property_query( $q ) ) {
			$property_sold = noo_get_option('noo_property_listing_sold',1);
			if(apply_filters('noo_hide_sold_property', true) && empty($property_sold)) {
				if (!empty($property_sold)) {
					$noo_show_sold = true;
				}
				if( !is_tax('property_status') ) {
					$is_query_status = false;
					if( !empty( $q->tax_query->queries ) ) {
						foreach ( $q->tax_query->queries as $tax_q ) {
							if( isset($tax_q['taxonomy']) && $tax_q['taxonomy'] == 'property_status' ) {
								$is_query_status = true;
								break;
							}
						}
					}
					if( !$is_query_status ) {
						$sold = get_option('default_property_status');
						$tax_query = array(
								'taxonomy' => 'property_status',
								'terms'    => array( $sold ),
								'operator' => 'NOT IN',
						);
						$q->tax_query->queries[] = $tax_query;
						$q->query_vars['tax_query'] = $q->tax_query->queries;
					}
				}
			}
			// if(isset($_GET['orderby'])){
				$default_orderby = isset( $q->query_vars['orderby'] ) ? $q->query_vars['orderby'] : get_theme_mod('noo_property_listing_orderby_default');
				$default_orderby = !empty( $default_orderby	 ) ? $default_orderby : 'date';
				$orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : $default_orderby;
				$orderby = strtolower( $orderby );
				$order   = isset( $q->query_vars['order'] ) ? $q->query_vars['order'] : 'DESC';
				$args    = array();
				$args['orderby']  = $orderby;
				$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
				$args['meta_key'] = '';
				
				switch ( $orderby ) {
					case 'rand' :
						$args['orderby']  = 'rand';
						break;
					case 'date' :
						$args['orderby']  = 'date';
						$args['order']    = $order == 'ASC' ? 'ASC' : 'DESC';
						break;
					case 'bath' :
						$args['orderby']  = "meta_value_num meta_value";
						$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
						$args['meta_key'] = '_bathrooms';
						break;
					case 'bed' :
						$args['orderby']  = "meta_value_num meta_value";
						$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
						$args['meta_key'] = '_bedrooms';
						break;
					case 'area' :
						$args['orderby']  = "meta_value_num meta_value";
						$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
						$args['meta_key'] = '_area';
						break;
					case 'price' :
						$args['orderby']  = "meta_value_num meta_value";
						$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
						$args['meta_key'] = '_price';
						break;
					case 'featured' :
						$args['orderby']  = "meta_value";
						$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
						$args['meta_key'] = '_featured';
						break;
					case 'name' :
						$args['orderby']  = 'title';
						$args['order']    = 'ASC'; // $order == 'DESC' ? 'DESC' : 'ASC';
						break;
				}

				$q->set( 'orderby', $args['orderby'] );
				$q->set( 'order', $args['order'] );

				if ( isset( $args['meta_key'] ) && !empty( $args['meta_key'] ) ) {
					$q->set( 'meta_key', $args['meta_key'] );
				}
				if ( isset( $args['meta_value'] ) && !empty( $args['meta_value'] ) ) {
					$q->set( 'meta_value', $args['meta_value'] );
				}
			// }

			$q = re_property_query_from_request( $q, $_GET );
		}
	}
	add_action( 'pre_get_posts', 're_property_pre_get_posts' );
endif;

if( !function_exists( 're_build_properties_query') ) :
	function re_build_properties_query( $params = array() ) {
		extract( wp_parse_args( $params,
			array(
				'type'					=> 'list',
				'property_id'           => '',
				'property_category'     => '',
				'property_status'       => '',
				'property_label'        => '',
				'property_location'     => '',
				'property_sub_location' =>'',
				'number'                => '6',
				'show'                  => '',
				'order_by'              => 'date',
				'order'                 => 'desc',
				'return'				=> 'query',
			)
		) );

		global $noo_show_sold;

		if( is_front_page() || is_home()) {
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );
		} else {
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		}
		
		$args = array(
			'paged'          => $paged,
			'posts_per_page' => $number,
			'post_status'    => 'publish',
			'post_type'      => 'noo_property',
		);
		if($type == 'list') {
			$args['tax_query'] = array('relation' => 'AND');
			if(!empty($property_category)){
				$args['tax_query'][] = array(
						'taxonomy'     => 'property_category',
						'field'        => 'id',
						'terms'        =>  explode(',', $property_category),
					);
			}
			if(!empty($property_status)){
				$noo_show_sold = true;
				$args['tax_query'][] = array(
						'taxonomy'     => 'property_status',
						'field'        => 'term_id',
						'terms'        => explode(',', $property_status),
				);
			}
			if( !empty($property_location)){
				$args['tax_query'][] = array(
						'taxonomy'     => 'property_location',
						'field'        => 'tern_id',
						'terms'        => explode(',', $property_location),
					);
			}
			if( !empty($property_sub_location)){
				$args['tax_query'][] = array(
						'taxonomy'     => 'property_sub_location',
						'field'        => 'tern_id',
						'terms'        => explode(',', $property_sub_location),
					);
			}
			if(!empty($property_label)){
			$property_label = absint($property_label);
				$args['meta_query'][] = array(
					'key'   => '_label',
					'value' => $property_label
				);
			}
			if($show === 'featured'){
				$args['meta_query'][] = array(
						'key'   => '_featured',
						'value' => 'yes'
				);
			}

			$args['orderby']  = $order_by;
			$order    = strtoupper( $order );
			$args['meta_key'] = '';
			switch ( $order_by ) {
				case 'rand' :
					$args['orderby']  = 'rand';
					break;
				case 'date' :
					$args['orderby']  = 'date';
					$args['order']    = $order;
					break;
				case 'bath' :
					$args['orderby']  = "meta_value_num meta_value";
					$args['order']    = $order;
					$args['meta_key'] = '_bathrooms';
					break;
				case 'bed' :
					$args['orderby']  = "meta_value_num meta_value";
					$args['order']    = $order;
					$args['meta_key'] = '_bedrooms';
					break;
				case 'area' :
					$args['orderby']  = "meta_value_num meta_value";
					$args['order']    = $order;
					$args['meta_key'] = '_area';
					break;
				case 'price' :
					$args['orderby']  = "meta_value_num meta_value";
					$args['order']    = $order;
					$args['meta_key'] = '_price';
					break;
				case 'featured' :
					$args['orderby']  = "meta_value";
					$args['order']    = $order;
					$args['meta_key'] = '_featured';
					break;
				case 'name' :
					$args['orderby']  = 'title';
					$args['order']    = $order;
					break;
			}
		
		} elseif ($type == 'single') {
			$args['p'] = absint( $property_id );
		}

		// ===== <<< [ Create Query ] >>> ===== //
		$args = apply_filters( 're_build_properties_query', $args, $params );
		if( $return == 'query' ) {
			$query = new WP_Query( $args );
			$noo_show_sold = false;

			return $query;
		} else {
			return $args;
		}
	}
endif;

if( !function_exists( 're_property_query_from_request') ) :
	function re_property_query_from_request( &$query, $REQUEST = array() ) {
		if( empty( $query ) || empty( $REQUEST ) ) {
			return $query;
		}

		$tax_query = array();
		$tax_list = array(
			'location' => 'property_location',
			'sub_location' => 'property_sub_location',
			'category' => 'property_category',
			'status' => 'property_status',
		);
		$tax_list = apply_filters( 're_property_query_tax_list', $tax_list );
		if ( !empty( $tax_list ) ) {

			foreach ($tax_list as $tax_key => $term) {
				if( isset( $REQUEST[$tax_key] ) && !empty( $REQUEST[$tax_key] ) ) {
					$tax_query[] = array(
						'taxonomy'     => $term,
						'field'        => 'slug',
						'terms'        => $REQUEST[$tax_key]
					);
				}
			}

		}

		$tax_query = apply_filters( 're_property_search_tax_query', $tax_query, $REQUEST );

		if( !empty( $tax_query ) ) {
			$tax_query['relation'] = 'AND';
			if( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
				$query->tax_query->queries = $tax_query;
				$query->query_vars['tax_query'] = $query->tax_query->queries;
			} elseif( is_array( $query ) ) {
				$query['tax_query'] = $tax_query;
			}
		}

		if(isset( $REQUEST['keyword'] ) && !empty( $REQUEST['keyword'] )){
			if( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
				$query->set( 's', esc_html( $REQUEST['keyword'] ) );
			} elseif( is_array( $query ) ) {
				$query['s'] = esc_html( $REQUEST['keyword'] );
			}
		}

		$meta_query = array();
		$p_style = re_get_property_search_setting('p_style','slide');
		if($p_style == 'dropdown'){
			if(isset( $REQUEST['p_area_range'] ) && !empty( $REQUEST['p_area_range'] )){
				$area_range = explode('-',$REQUEST['p_area_range']);
				$area_min = isset($area_range[0]) ? $area_range[0] : 0;
				$area_max = isset($area_range[1]) ? $area_range[1] : 0;

				$min_area['key']      = '_area';
				$min_area['value']    = floatval($area_min);
				$min_area['type']     = 'NUMERIC';
				$min_area['compare']  = '>=';
				$meta_query[]     	  = $min_area;

				$max_area['key']      = '_area';
				$max_area['value']    = floatval($area_max);
				$max_area['type']     = 'NUMERIC';
				$max_area['compare']  = '<=';
				$meta_query[]     	  = $max_area;
			}
		}else{
			if(isset( $REQUEST['min_area'] ) && !empty( $REQUEST['min_area'] )){
				$min_area['key']      = '_area';
				$min_area['value']    = intval($REQUEST['min_area']);
				$min_area['type']     = 'NUMERIC';
				$min_area['compare']  = '>=';
				$meta_query[]     = $min_area;
			}
			if(isset( $REQUEST['max_area'] ) && !empty( $REQUEST['max_area'] )){
				$max_area['key']      = '_area';
				$max_area['value']    = intval($REQUEST['max_area']);
				$max_area['type']     = 'NUMERIC';
				$max_area['compare']  = '<=';
				$meta_query[]     = $max_area;
			}
		}

		if($p_style == 'dropdown'){
			if(isset( $REQUEST['p_price_range'] ) && !empty( $REQUEST['p_price_range'] )){
				$price_range = explode('-',$REQUEST['p_price_range']);
				$price_min = isset($price_range[0]) ? $price_range[0] : 0;
				$price_max = isset($price_range[1]) ? $price_range[1] : 0;

				$min_price['key']      = '_price';
				$min_price['value']    = floatval($price_min);
				$min_price['type']     = 'NUMERIC';
				$min_price['compare']  = '>=';
				$meta_query[]     = $min_price;

				$max_price['key']      = '_price';
				$max_price['value']    = floatval($price_max);
				$max_price['type']     = 'NUMERIC';
				$max_price['compare']  = '<=';
				$meta_query[]     	   = $max_price;
			}
		}else{
			if(isset( $REQUEST['min_price'] ) && !empty( $REQUEST['min_price'] )){
				$min_price['key']      = '_price';
				$min_price['value']    = floatval($REQUEST['min_price']);
				$min_price['type']     = 'NUMERIC';
				$min_price['compare']  = '>=';
				$meta_query[]     = $min_price;
			}
			if(isset( $REQUEST['max_price'] ) && !empty( $REQUEST['max_price'] )){
				$max_price['key']      = '_price';
				$max_price['value']    = floatval($REQUEST['max_price']);
				$max_price['type']     = 'NUMERIC';
				$max_price['compare']  = '<=';
				$meta_query[]     	   = $max_price;
			}
		}


		/**
		 * Process request Bedrooms
		 */
		if ( isset( $REQUEST['bedrooms'] ) && !empty( $REQUEST['bedrooms'] ) ) {
			$bedrooms['key']      = '_bedrooms';
			$bedrooms['value']    = esc_attr( $REQUEST['bedrooms'] );
			$bedrooms['compare']  = '=';
			$meta_query[]     	  = $bedrooms;
		}

		/**
		 * Process request Agent
		 */
		
		if ( isset( $REQUEST['agent_search'] ) && !empty( $REQUEST['agent_search'] ) ) {
			$agent_search['key']      = '_agent_responsible';
			$agent_search['value']    = esc_attr( $REQUEST['agent_search'] );
			$agent_search['compare']  = '=';
			$meta_query[]     	  = $agent_search;
		}

		/**
		 * Process request Bathrooms
		 */
		if ( isset( $REQUEST['bathrooms'] ) && !empty( $REQUEST['bathrooms'] ) ) {
			$bathrooms['key']      = '_bathrooms';
			$bathrooms['value']    = esc_attr( $REQUEST['bathrooms'] );
			$bathrooms['compare']  = '=';
			$meta_query[]     	  = $bathrooms;
		}
		
		$property_fields = re_get_property_search_custom_fields();
		if ( !empty( $property_fields ) ) {

			foreach ($property_fields as $field) {
				$field_id = isset( $field['is_default'] ) && $field['is_default'] ? ( $field['name'][0] == '_' ? substr($field['name'], 1) : $field['name'] ) : re_property_custom_fields_name( $field['name'] );

				if ($field_id == 'bathrooms' || $field_id == 'bedrooms') {
					continue;
				}

				if( isset( $REQUEST[$field_id] ) && !empty( $REQUEST[$field_id]) ) {
					$value = noo_sanitize_field( $REQUEST[$field_id], $field );
					$field_id = isset( $field['is_default'] ) && $field['is_default'] ? $field['name'] : $field_id;
					if(is_array($value)){
						$temp_meta_query = array( 'relation' => 'OR' );
						foreach ($value as $v) {
							if( empty( $v ) ) continue;
							$temp_meta_query[]	= array(
								'key'     => $field_id,
								'value'   => '"'.$v.'"',
								'compare' => 'LIKE'
							);
						}
						$meta_query[] = $temp_meta_query;
					} else {
						$meta_query[]	= array(
							'key'     => $field_id,
							'value'   => str_replace( '-', ' ', $value ),
							// 'value'	  => $value,
							'compare' => 'LIKE'
						);
					}
				} elseif( ( isset( $field['type'] ) && $field['type'] == 'datepicker' ) && ( isset( $REQUEST[$field_id.'_start'] ) || isset( $REQUEST[$field_id.'_end'] ) ) ) {
					if( $field_id == 'date' ) {
						$date_query = array();
						if( isset( $REQUEST[$field_id.'_start'] ) && !empty( $REQUEST[$field_id.'_start'] ) ) {
							$start = is_numeric( $REQUEST[$field_id.'_start'] ) ? date('Y-m-d', $REQUEST[$field_id.'_start']) : $REQUEST[$field_id.'_start'];
							$date_query['after'] = date('Y-m-d', strtotime( $start . ' -1 day' ) );
						}
						if( isset( $REQUEST[$field_id.'_end'] ) && !empty( $REQUEST[$field_id.'_end'] ) ) {
							$end = is_numeric( $REQUEST[$field_id.'_end'] ) ? date('Y-m-d', $REQUEST[$field_id.'_end']) : $REQUEST[$field_id.'_end'];
							$date_query['before'] = date('Y-m-d', strtotime( $end . ' +1 day' ) );
						}

						if( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
							$query->query_vars['date_query'][] = $date_query;
						} elseif( is_array( $query ) ) {
							$query['date_query'] = $date_query;
						}
					} else {
						$value_start = isset( $REQUEST[$field_id.'_start'] ) && !empty( $REQUEST[$field_id.'_start'] ) ? noo_sanitize_field( $REQUEST[$field_id.'_start'], $field ) : 0;
						$value_start = !empty( $value_start ) ? strtotime("midnight", $value_start) : 0;
						$value_end = isset( $REQUEST[$field_id.'_end'] ) && !empty( $REQUEST[$field_id.'_end'] ) ? noo_sanitize_field( $REQUEST[$field_id.'_end'], $field ) : 0;
						$value_end = !empty( $value_end ) ? strtotime("tomorrow", strtotime("midnight", $value_end)) - 1 : strtotime( '2090/12/31');

						$meta_query[]	= array(
							'key'     => $field_id,
							'value'   => array( $value_start, $value_end ),
							'compare' => 'BETWEEN',
							'type' => 'NUMERIC'
						);
					}
				}
			}

		}
		$property_features = re_get_property_feature_fields();
		if ( !empty( $property_features ) ) {

			foreach ($property_features as $key => $feature) {
				$field_id = '_noo_property_feature_' . sanitize_title( $key );
				if( isset( $REQUEST[$field_id] ) && !empty( $REQUEST[$field_id]) ) {
					$meta_query[]	= array(
						'key' => $field_id,
						'value' => '1',
					);
				}
			}

		}

		$meta_query = apply_filters( 're_property_search_meta_query', $meta_query, $REQUEST );

		if( !empty( $meta_query ) ) {
			$meta_query['relation'] = 'AND';
			if( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
				$query->query_vars['meta_query'][] = $meta_query;
			} elseif( is_array( $query ) ) {
				$query['meta_query'] = $meta_query;
			}
		}
		return $query;
	}
endif;

if( !function_exists( 're_properties_as_front_page' ) ) :
	function re_properties_as_front_page( $wp_query ) {
		//Ensure this filter isn't applied to the admin area
		if( is_admin() ) {
			return;
		}

		if( $wp_query->get('page_id') == get_option('page_on_front') ) {
			if( get_post_field( 'post_name', $wp_query->get('page_id') ) == re_get_property_setting('archive_slug') ) {
				$wp_query->set('post_type', 'noo_property');
		        $wp_query->set('page_id', ''); //Empty

		        //Set properties that describe the page to reflect that
		        //we aren't really displaying a static page
		        $wp_query->is_page = 0;
		        $wp_query->is_singular = 0;
		        // $wp_query->is_post_type_archive = 1;
		        $wp_query->is_archive = 1;

		        // Correct the pagination
		        $paged = $wp_query->get( 'paged' );
		        $page = $wp_query->get( 'page' );
		        if( empty( $paged ) && !empty( $page ) ) {
		        	$wp_query->set( 'paged', $page );
		        }
			}
        }
	}

	add_action('pre_get_posts', 're_properties_as_front_page', 9); // priority is 9 so that other functions can works.
endif;
