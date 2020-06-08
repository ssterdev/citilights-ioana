<?php
if(!class_exists('NooPropertyFilterDropdown')):
	class NooPropertyFilterDropdown extends Walker {

		var $tree_type = 'category';
		var $db_fields = array ('parent' => 'parent', 'id' => 'term_id', 'slug' => 'slug' );

		public function start_el( &$output, $cat, $depth = 0, $args = array(), $current_object_id = 0 ) {

			if ( ! empty( $args['hierarchical'] ) ) {
				$pad = str_repeat('-', $depth * 2);
				$pad = !empty( $pad ) ? $pad . ' ' : '';
			} else {
				$pad = '';
			}

			$cat_name = $cat->name;

			$value = isset( $args['value'] ) && $args['value'] == 'id' ? $cat->term_id : $cat->slug;
			$parent = '';
			if( $args['taxonomy'] == 'property_sub_location' ) {
				$parent_data = get_option( 'noo_sub_location_parent' );
				if( isset( $parent_data[$cat->term_id] ) ) {
					$parent_location = get_term_by('id',$parent_data[$cat->term_id],'property_location');
					$parent .= ' data-parent-location="' . ( !empty( $parent_location ) ? $parent_location->slug : '' ) . '"';
				}
			}

			$output .= "\t<option class=\"level-$depth\" $parent value=\"" . $value . "\"";

			if ( $value == $args['selected'] || ( is_array( $args['selected'] ) && in_array( $value, $args['selected'] ) ) )
				$output .= ' selected="selected"';

			$output .= '>';

			$output .= $pad . $cat_name;

			$output .= "</option>\n";
		}
	}
endif;

if( !function_exists( 're_get_property_tax_fields' ) ) :
	function re_get_property_tax_fields() {
		$tax_fields = array(
			'property_category' => array(
					'name' => 'category',
					'label' => __('Type','noo'),
				),
			'property_status' => array(
					'name' => 'status',
					'label' => __('Status','noo'),
				),
			'property_location' => array(
					'name' => 'location',
					'label' => __('Location','noo'),
				),
			'property_sub_location' => array(
					'name' => 'sub_location',
					'label' => __('Sub Location','noo'),
				),
			);

		return apply_filters( 're_property_tax_fields', $tax_fields );
	}
endif;

if( !function_exists('re_property_render_taxonomy_field') ):
	function re_property_render_taxonomy_field( $taxonomy = '', $value = array(), $label = '', $form_type = '', $args = array() ) {
		if( empty( $label ) ) {
			$tax_obj = get_taxonomy( $taxonomy );
			$label = $tax_obj->label;
		}

		/**
		 * Support chosen rtl
		 */
			$class_chosen = '';
			if ( is_rtl() ) $class_chosen = ' chosen-rtl';

		$field_name = ( $form_type == 'search' ? str_replace('property_', '', $taxonomy) : str_replace('property_', '_', $taxonomy) );
		$value = is_wp_error( $value ) ? '' : ( is_array( $value ) ? reset( $value ) : $value );

		// $field = array(
		// 		'name' => $field_name,
		// 		'label' => $label,
		// 		'type' => 'select',
		// 		'is_default' => true,
		// 		'is_disabled' => 'no',
		// 		'is_tax' => true,
		// 		'no_translate' => true
		// 	);

		// $args = array_merge( array( 'hide_empty' => ( $form_type == 'search' ) ), $args );
		// $terms = get_terms( $taxonomy, $args );
		// foreach ($terms as $term) {
		// 	$field_value[] = $term->slug . '|' . $term->name;
		// }

		// $field['value'] = $field_value;
		$val = isset($_GET[$field_name]) ? $_GET[$field_name] : '';
		// noo_render_select_field( $field, $field_name, $val, $form_type );
		$placeholder = $form_type != 'search' ? sprintf( __("%s",'noo'), $label ) : sprintf( __("All %s",'noo'), $label );
		if (substr($placeholder, 4, 3) == 'Loc' ) {
			$placeholder = "Markets";
		}
		$dropdown = wp_dropdown_categories( array( 
			'taxonomy'          => $taxonomy, 
			'name'              => $field_name, 
			'hierarchical'      => true, 
			'hide_empty'        => $form_type == 'search', 
			'value_field'       => 'slug', 
			'selected'          => $val, 
			'class'             => 'form-control form-control-chosen ignore-valid' . esc_attr( $class_chosen ), 
			'show_option_none'  => $placeholder, 
			'option_none_value' => '', 
			'orderby'			=> 'name',
			'walker'            => new NooPropertyFilterDropdown, 
			'echo'              => false, 
		) );
		echo str_replace('<select', '<select ' . 'data-placeholder="' . $placeholder . '"', $dropdown);
	}
endif;

if(!class_exists('NooPropertySearchDropdown')):
	class NooPropertySearchDropdown extends Walker {

		var $tree_type = 'category';
		var $db_fields = array ('parent' => 'parent', 'id' => 'term_id', 'slug' => 'slug' );

		public function start_el( &$output, $term, $depth = 0, $args = array(), $current_object_id = 0 ) {

			if ( ! empty( $args['hierarchical'] ) ) {
				$pad = str_repeat('-', $depth * 2);
				$pad = !empty( $pad ) ? $pad . '&nbsp;' : '';
			} else {
				$pad = '';
			}

			$cat_name = $term->name;

			$value = isset( $args['value'] ) && $args['value'] == 'id' ? $term->term_id : $term->slug;
			$parent = '';
			if( $args['taxonomy'] == 'property_sub_location' ) {
				$parent_data = get_option( 'noo_sub_location_parent' );
				if( isset( $parent_data[$term->term_id] ) ) {
					$parent_location = get_term_by('id',$parent_data[$term->term_id],'property_location');
					$parent .= ' data-parent-location="' . ( !empty( $parent_location ) ? $parent_location->slug : '' ) . '"';
				}
			}

			$output .= "\t<li class=\"level-$depth\" $parent><a href=\"#\" data-value=\"" . $value . "\">";
			$output .= $pad . $cat_name;
			// if ( ! empty( $args['show_count'] ) ) {
			// 	$output .= '&nbsp;(' . $term->count . ')';
			// }
			$output .= "</a></li>\n";
		}

		public function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
			if ( ! $element ) {
				return;
			}
			parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
		}
	}
endif;

if(!function_exists('noo_dropdown_search')):
	function noo_dropdown_search($args = ''){
		$defaults = array(
			'show_option_all' => '', 'show_option_none' => '',
			'orderby' => 'name', 'order' => 'ASC',
			'show_count' => 1,
			'hide_empty' => 0, 'child_of' => 0,
			'exclude' => '', 'echo' => 1,
			'hierarchical' => 1,
			'depth' => 0,
			'taxonomy' => 'category',
			'hide_if_empty' => false,
			'option_none_value' => '',
			'meta' => '',
			'walker'=>new NooPropertySearchDropdown
		);
		$defaults['selected'] = ( is_category() ) ? get_query_var( 'cat' ) : 0;
		$r = wp_parse_args( $args, $defaults );
		$taxonomies = get_terms( $r['taxonomy'], $r );
		if ( ! $r['hide_if_empty'] || ! empty( $taxonomies ) ) {
			$output = "<ul class=\"dropdown-menu\">\n";
		} else {
			$output = '';
		}
		
		if ( empty( $taxonomies ) && ! $r['hide_if_empty'] && ! empty( $r['show_option_none'] ) ) {
			$show_option_none = $r['show_option_none'];
			$output .= "\t<li><a data-value=\"\" href=\"#\">$show_option_none</a></li>\n";
		}
		if ( $r['show_option_none'] ) {
			$show_option_none = $r['show_option_none'];
			$output .= "\t<li><a data-value=\"\" href=\"#\">$show_option_none</a></li>\n";
		}
		
		if ( $r['hierarchical'] ) {
			$depth = $r['depth'];  // Walk the full depth.
		} else {
			$depth = -1; // Flat.
		}
		$output .= walk_category_dropdown_tree( $taxonomies, $depth, $r );
		
		if ( ! $r['hide_if_empty'] || ! empty( $taxonomies ) ) {
			$output .= "</ul>\n";
		}
		if ( $r['echo'] ) {
			echo $output;
		}
		return $output;
	}
endif;

/* -------------------------------------------------------
 * Create functions noo_dropdown_taxonomy
 * ------------------------------------------------------- */

if ( ! function_exists( 'noo_dropdown_taxonomy' ) ) :
	
	function noo_dropdown_taxonomy( $type = '', $hide_id = null, $tag = 'ul', $tag_child = 'li', $class = null, $class_child = null ) {
		
		if ( empty( $type ) ) return false;
		$args = array(
		    'hide_empty' => 0 
		); 
		$terms = get_terms( $type, $args);
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
		    echo "<{$tag}" . ( ( $class != null ) ? " class=\"{$class}\"" : "" ) . ">";
		    echo "	<{$tag_child}>";
		    echo "		<a href=\"#\" data-value=\"\">None</a>";
		    echo "	</{$tag_child}>";
		    foreach ( $terms as $term ) {
		    	if ( $term->term_id != $hide_id ) {
			    	echo "<{$tag_child}" . ( ( $class_child != null ) ? " class=\"{$class_child}\"" : "" ) . ">";
			       	echo "<a href=\"#\" data-value=\"$term->slug\">$term->name</a>";
			        echo "</{$tag_child}>";
			    }
		    }
		     echo "</{$tag}>";
		 }

	}

endif;

/** ====== END noo_dropdown_taxonomy ====== **/
