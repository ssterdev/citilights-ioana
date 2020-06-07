<?php
if( !function_exists( 're_get_property_area_html' ) ) :
	function re_get_property_area_html($post_id){
		$area = get_post_meta($post_id,'_area',true);
		$area_unit = re_get_property_setting('area_unit');
		return empty( $area ) ? '' : $area.' '.$area_unit;
	}
endif;



if( !function_exists('re_property_render_area_search_field') ):
	function re_property_render_area_search_field() {
		global $wpdb;

		$min_area = $max_area = 0;
		$min_area = ceil( $wpdb->get_var('
				SELECT min(meta_value + 0)
				FROM '.$wpdb->posts.'
				LEFT JOIN '.$wpdb->postmeta.' ON '.$wpdb->posts.'.ID = '.$wpdb->postmeta.'.post_id AND post_status = \'publish\'
				WHERE meta_key = \'_area\' AND post_type = \'noo_property\'') );
		$max_area = ceil( $wpdb->get_var('
				SELECT max(meta_value + 0)
				FROM '.$wpdb->posts.'
				LEFT JOIN '.$wpdb->postmeta.' ON '.$wpdb->posts.'.ID = '.$wpdb->postmeta.'.post_id AND post_status = \'publish\'
				WHERE meta_key = \'_area\' AND post_type = \'noo_property\'') );
		
		$g_min_area = isset( $_GET['min_area'] ) ? esc_attr( $_GET['min_area'] ) : $min_area;
		$g_max_area = isset( $_GET['max_area'] ) ? esc_attr( $_GET['max_area'] ) : $max_area;
		$p_style = re_get_property_search_setting('p_style','slide');
		?>
		<?php if('slide' == $p_style):?>
			<div class="form-group garea">
				<span class="garea-label"><?php _e('Area','noo')?></span>
				<div class="garea-slider-range"></div>
				<input type="hidden" class="garea_min" name="min_area" data-min="<?php echo $min_area ?>" value="<?php echo $g_min_area ?>">
				<input type="hidden" class="garea_max" name="max_area" data-max="<?php echo $max_area ?>" value="<?php echo $g_max_area ?>">
			</div>
		<?php else: ?>
			<div class="form-group">
				<div class="noo-box-select">
					<?php noo_area_range_dropdown($min_area,$max_area);?>
				</div>
			</div>
		<?php
		endif;
	}
endif;
