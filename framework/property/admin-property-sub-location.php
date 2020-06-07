<?php
if( !function_exists( 're_property_sub_location_add_location_parent' ) ) :
	function re_property_sub_location_add_location_parent() {
		$locations = get_terms('property_location', array( 'hide_empty' => false ));
		?>
		<div class="form-field">
			<label><?php _e('Location','noo')?></label>
			<select name="noo_location_parent">
				<option value=""></option>
				<?php foreach ((array)$locations as $location):?>
					<option value="<?php echo $location->term_id ?>"><?php echo $location->name?></option>
				<?php endforeach;?>
			</select>
		</div>
		<?php
	}
	add_action( 'property_sub_location_add_form_fields', 're_property_sub_location_add_location_parent' );
endif;

if( !function_exists( 're_property_sub_location_edit_location_parent' ) ) :
	function re_property_sub_location_edit_location_parent( $term, $taxonomy ) {
		$locations = get_terms('property_location', array( 'hide_empty' => false ));
		$sub_location_parent_options = get_option('noo_sub_location_parent');
		$selected = isset($sub_location_parent_options[$term->term_id]) ? $sub_location_parent_options[$term->term_id] : 0;
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e('Location', 'noo'); ?></label></th>
			<td>
				<select name="noo_location_parent">
					<option value=""></option>
					<?php foreach ((array)$locations as $location):?>
						<option value="<?php echo $location->term_id ?>" <?php selected($selected,$location->term_id)?>><?php echo $location->name?></option>
					<?php endforeach;?>
				</select>
			</td>
		</tr>
		<?php
	}
	add_action( 'property_sub_location_edit_form_fields', 're_property_sub_location_edit_location_parent', 10, 2 );
endif;

if( !function_exists( 're_property_sub_location_save_location_parent' ) ) :
	function re_property_sub_location_save_location_parent($term_id, $tt_id, $taxonomy){
		if ( isset( $_POST['noo_location_parent'] ) ){
			$parents = get_option( 'noo_sub_location_parent' );
			if ( ! $parents )
				$parents = array();
			$parents[$term_id] = absint($_POST['noo_location_parent']);
			update_option('noo_sub_location_parent', $parents);
		}
	}
	add_action( 'created_term', 're_property_sub_location_save_location_parent', 10,3 );
	add_action( 'edit_term', 're_property_sub_location_save_location_parent', 10,3 );
endif;

if( !function_exists( 're_property_sub_location_list_location_column_header' ) ) :
	function re_property_sub_location_list_location_column_header( $columns ) {
		$part1 = array_slice($columns, 0, 2);
		$part2 = array_slice($columns, 2);
		$new_columns = array( 'location_id' => __( 'Location', 'noo' ) );
	
		return array_merge( $part1, $new_columns, $part2 );
	}
	add_filter( 'manage_edit-property_sub_location_columns', 're_property_sub_location_list_location_column_header' );
endif;

if( !function_exists( 're_property_sub_location_list_location_column_data' ) ) :
	function re_property_sub_location_list_location_column_data( $columns, $column, $id ) {
		if ( $column == 'location_id' ) {
			$sub_location_parent_options = get_option('noo_sub_location_parent');
			$selected = isset($sub_location_parent_options[$id]) ? $sub_location_parent_options[$id] : '';
			if($selected && $location = get_term($selected, 'property_location')){
				edit_term_link( $location->name, '', '', $location );
			} else {
				echo '<span class="na">&ndash;</span>';
			}
		}
		return $columns;
	}
	add_filter( 'manage_property_sub_location_custom_column', 're_property_sub_location_list_location_column_data', 10, 3 );
endif;
