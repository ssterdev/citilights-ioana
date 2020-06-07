<?php
if( !function_exists( 're_property_label_add_location_parent' ) ) :
	function re_property_label_add_location_parent() {
		wp_enqueue_style( 'wp-color-picker');
		wp_enqueue_script( 'wp-color-picker');
		?>
		<div class="form-field">
			<label><?php _e( 'Color', 'noo' ); ?></label>
			<input id="noo_property_label_color" type="text" size="40" value="" name="noo_property_label_color">
			<script type="text/javascript">
				jQuery(document).ready(function($){
				    $("#noo_property_label_color").wpColorPicker();
				});
			 </script>
		</div>
		<?php
	}
	add_action( 'property_label_add_form_fields', 're_property_label_add_location_parent' );
endif;

if( !function_exists( 're_property_label_edit_location_parent' ) ) :
	function re_property_label_edit_location_parent( $term, $taxonomy ) {
		wp_enqueue_style( 'wp-color-picker');
		wp_enqueue_script( 'wp-color-picker');
		$noo_property_label_colors = get_option('noo_property_label_colors');
		$color 	= isset($noo_property_label_colors[$term->term_id]) ? $noo_property_label_colors[$term->term_id] : '';
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e('Color', 'noo'); ?></label></th>
			<td>
				<input id="noo_property_label_color" type="text" size="40" value="<?php echo $color?>" name="noo_property_label_color">
				<script type="text/javascript">
					jQuery(document).ready(function($){
					    $("#noo_property_label_color").wpColorPicker();
					});
				 </script>
			</td>
		</tr>
		<?php
	}
	add_action( 'property_label_edit_form_fields', 're_property_label_edit_location_parent', 10, 2 );
endif;

if( !function_exists( 're_property_label_save_location_parent' ) ) :
	function re_property_label_save_location_parent($term_id, $tt_id, $taxonomy){
		if ( isset( $_POST['noo_property_label_color'] ) ){
			$noo_property_label_colors = get_option( 'noo_property_label_colors' );
			if ( ! $noo_property_label_colors )
				$noo_property_label_colors = array();
			$noo_property_label_colors[$term_id] = $_POST['noo_property_label_color'];
			update_option('noo_property_label_colors', $noo_property_label_colors);
		}
	}
	add_action( 'created_term', 're_property_label_save_location_parent', 10,3 );
	add_action( 'edit_term', 're_property_label_save_location_parent', 10,3 );
endif;
