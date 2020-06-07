<?php

if( !function_exists( 're_admin_property_feature_action' ) ) :
	function re_admin_property_feature_action(){
		if(isset($_GET['action']) && $_GET['action'] == 'noo_property_feature'){
			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.', 'noo' ), '', array( 'response' => 403 ) );
			}
			
			if ( ! check_admin_referer( 'noo-property-feature' ) ) {
				wp_die( __( 'You have taken too long. Please go back and retry.', 'noo' ), '', array( 'response' => 403 ) );
			}
			
			$post_id = ! empty( $_GET['property_id'] ) ? (int) $_GET['property_id'] : '';
			
			if ( ! $post_id || get_post_type( $post_id ) !== 'noo_property' ) {
				die;
			}
			
			$featured = get_post_meta( $post_id, '_featured', true );
			
			if ( 'yes' === $featured ) {
				update_post_meta( $post_id, '_featured', 'no' );
			} else {
				update_post_meta( $post_id, '_featured', 'yes' );
			}
			
			
			wp_safe_redirect( esc_url_raw( remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() ) ) );
			die();
		}
	}
	add_action( 'admin_init', 're_admin_property_feature_action' );
endif;

if( !function_exists( 're_property_admin_list_columns_header' ) ) :
	function re_property_admin_list_columns_header( $columns ) {
		$part1 = array_slice($columns, 0, 1);
		$part2 = array_slice($columns, 1, 1);
		$part3 = array_slice($columns, 2);
		$add1 = $add2 = $add3 = array();
		$add1['featured'] = __( 'Featured', 'noo' );

		$add2['type'] = __( 'Type', 'noo' );
		$add2['location'] = __( 'Location', 'noo' );
		$add2['sub-location'] = __( 'Sub-Location', 'noo' );
		$add2['status'] = __( 'Status', 'noo' );
		$add2['agent_responsible'] = __('Agent', 'noo');

		$add3['property_id'] = __( 'ID', 'noo' );
	
		return array_merge( $part1, $add1, $part2, $add2, $part3, $add3 );
	}
	add_filter( 'manage_edit-noo_property_columns', 're_property_admin_list_columns_header' );
endif;

if( !function_exists( 're_property_admin_list_columns_data' ) ) :
	function re_property_admin_list_columns_data($column){
		global $post;
		
		if ( $column == 'featured' ) {
			$featured = get_post_meta($post->ID,'_featured',true);
			$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=noo_property_feature&property_id=' . $post->ID ), 'noo-property-feature' );
			echo '<a href="' . esc_url( $url ) . '" title="'. __( 'Toggle featured', 'noo' ) . '">';
			if ( 'yes' === $featured ) {
				echo '<span class="noo-property-feature" title="'.esc_attr__('Yes','noo').'"><i class="dashicons dashicons-star-filled "></i></span>';
			} else {
				echo '<span class="noo-property-feature not-featured"  title="'.esc_attr__('No','noo').'"><i class="dashicons dashicons-star-empty"></i></span>';
			}
			echo '</a>';
		} elseif ($column == 'type'){
			if ( ! $terms = wp_get_object_terms( $post->ID, 'property_category', array("orderby"=>"term_order") ) ) {
				echo '<span class="na">&ndash;</span>';
			} else {
				$types = array();
				foreach( $terms as $term ) {
					$types[] = edit_term_link( $term->name, '', '', $term, false );
				}
				echo implode(', ', $types);
			}
		} elseif ($column == 'location'){
			if ( ! $terms = wp_get_object_terms( $post->ID, 'property_location', array("orderby"=>"term_order") ) ) {
				echo '<span class="na">&ndash;</span>';
			} else {
				$locations = array();
				foreach( $terms as $term ) {
					$locations[] = edit_term_link( $term->name, '', '', $term, false );
				}
				echo implode(', ', $locations);
			}
		} elseif ($column == 'sub-location'){
			if ( ! $terms = wp_get_object_terms( $post->ID, 'property_sub_location', array("orderby"=>"term_order") ) ) {
				echo '<span class="na">&ndash;</span>';
			} else {
				$sub_locations = array();
				foreach( $terms as $term ) {
					$sub_locations[] = edit_term_link( $term->name, '', '', $term, false );
				}
				echo implode(', ', $sub_locations);
			}
		} elseif ($column == 'status'){
			if ( ! $terms = wp_get_object_terms( $post->ID, 'property_status', array("orderby"=>"term_order") ) ) {
				echo '<span class="na">&ndash;</span>';
			} else {
				$status = array();
				foreach( $terms as $term ) {
					$status[] = edit_term_link( $term->name, '', '', $term, false );
				}
				echo implode(', ', $status);
			}
		} elseif ($column == 'agent_responsible'){
			$agent_id = get_post_meta( $post->ID, '_agent_responsible', true );
			$agent = false;
			if( !empty( $agent_id ) ) {
				$agent = get_post( $agent_id );
			}
			if( !empty( $agent ) ) {
				edit_post_link( $agent->post_title, '', '', $agent->ID );
			} else {
				echo '<span class="na">&ndash;</span>';
			}
		} elseif ($column == 'property_id'){
			echo $post->ID;
		}
		return $column;
	}
	add_filter( 'manage_noo_property_posts_custom_column', 're_property_admin_list_columns_data' );
endif;

if( !function_exists( 're_property_admin_list_filter' ) ) :
	function re_property_admin_list_filter() {
		global $typenow, $wp_query;
		if ( $typenow == 'noo_property' ) {
			$current_property_category = isset( $wp_query->query['property_category'] ) ? $wp_query->query['property_category'] : '';
			wp_dropdown_categories(array(
				'taxonomy'          =>'property_category',
				'orderby'           => 'NAME', 
				'order'             => 'ASC',
				'name'              =>'property_category',
				'echo'              =>true,
				'show_count'        =>true,
				'show_option_none'  =>__('All Types','noo'),
				'option_none_value' =>0,
				'selected'          =>$current_property_category,
				'walker'            =>new NooPropertyFilterDropdown
			));
			
			
			$current_property_location = isset( $wp_query->query['property_location'] ) ? $wp_query->query['property_location'] : '';
			wp_dropdown_categories(array(
				'taxonomy'          =>'property_location',
				'orderby'           => 'NAME', 
				'order'             => 'ASC',
				'name'              =>'property_location',
				'echo'              =>true,
				'show_count'        =>true,
				'show_option_none'  =>__('All Locations','noo'),
				'option_none_value' =>0,
				'selected'          =>$current_property_location,
				'walker'            =>new NooPropertyFilterDropdown
			));
			
			$current_property_sub_location = isset( $wp_query->query['property_sub_location'] ) ? $wp_query->query['property_sub_location'] : '';
			wp_dropdown_categories(array(
				'taxonomy'          =>'property_sub_location',
				'orderby'           => 'NAME', 
				'order'             => 'ASC',
				'name'              =>'property_sub_location',
				'echo'              =>true,
				'show_count'        =>true,
				'show_option_none'  =>__('All Sub-Locations','noo'),
				'option_none_value' =>0,
				'hierarchical'      =>true,
				'selected'          =>$current_property_sub_location,
				'walker'            =>new NooPropertyFilterDropdown
			));
			
			$current_property_status = isset( $wp_query->query['property_status'] ) ? $wp_query->query['property_status'] : '';
			wp_dropdown_categories(array(
				'taxonomy'          =>'property_status',
				'orderby'           => 'NAME', 
				'order'             => 'ASC',
				'name'              =>'property_status',
				'echo'              =>true,
				'show_count'        =>true,
				'show_option_none'  =>__('All Statuses','noo'),
				'option_none_value' =>0,
				'selected'          =>$current_property_status,
				'walker'            =>new NooPropertyFilterDropdown
			));
			
			// Agents
			global $wpdb;
			$agent_ids = $wpdb->get_col('
							SELECT DISTINCT meta_value
							FROM '.$wpdb->postmeta.'
							WHERE meta_key = \'_agent_responsible\' AND meta_value IS NOT NULL
							');
			$agent_ids = array_merge(array_map( 'intval', $agent_ids ), array(0) );
			$agents = get_posts( array(
				'post_type'      => 'noo_agent',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'include'        => $agent_ids
			));
			?>
			<select name="agent">
				<option value=""><?php _e('All Agents', 'noo'); ?></option>
				<?php
				$current_v = isset($_GET['agent'])? $_GET['agent']:'';
				foreach ($agents as $agent) {
					printf
					(
						'<option value="%s"%s>%s</option>',
						$agent->ID,
						$agent->ID == $current_v ? ' selected="selected"':'',
						$agent->post_title
					);
				}
				?>
			</select>
			<?php
		}
	}
	add_action( 'restrict_manage_posts', 're_property_admin_list_filter' );
endif;

if( !function_exists( 're_property_admin_list_filter_action' ) ) :
	function re_property_admin_list_filter_action( $query ){
		global $pagenow;
		$type = 'post';
		if (isset($_GET['post_type'])) {
			$type = $_GET['post_type'];
		}
		if ( 'noo_property' == $type && is_admin() && $pagenow=='edit.php' ) {
			if( !isset($query->query_vars['post_type']) || $query->query_vars['post_type'] == 'noo_property' ) {
				if( isset($_GET['agent']) && !empty( $_GET['agent'] ) ) {
					$agent_id = $_GET['agent'];

					$meta_query = isset( $query->meta_query ) && !empty( $query->meta_query ) ? $query->meta_query : array();
					$meta_query[] = array(
							'key' => '_agent_responsible',
							'value' => $agent_id,
						);

					$query->set('meta_query', $meta_query);
				}
			}
		}
	}

	add_filter( 'parse_query', 're_property_admin_list_filter_action' );
endif;
