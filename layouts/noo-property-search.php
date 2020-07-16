	<div class="gsearch property" >
		<?php if( !$no_search_container ) : ?>
		<div class="container-boxed">
		<?php endif; ?>
			<?php if($search_info) :
				$search_info_title = is_null($search_info_title) ? __('Find Your Place','noo') : $search_info_title;
				$search_info_content = is_null($search_info_content) ? __('Instantly find your desired place with your expected location, price and other criteria just by starting your search now','noo') : $search_info_content;
			?>
				<?php if( !empty( $search_info_title ) || !empty( $search_info_content ) ) :
					?>
					<div class="gsearch-info">
						<?php if( !empty( $search_info_title ) ) : ?>
							<h4 class="gsearch-info-title"><?php echo esc_html($search_info_title);?></h4>
						<?php endif; ?>
						<?php if( !empty( $search_info_content ) ) : ?>
							<div class="gsearch-info-content"><?php echo esc_html($search_info_content);?></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		   	<div class="gsearch-wrap">

			   	<form action="<?php echo $result_page_url ?>" class="gsearchform" method="get" role="search">
		   		<?php if($gmap):?>
			   		<h3 class="gsearch-title"><i class="fa fa-search"></i><span><?php echo __('SEARCH FOR PROPERTY','noo')?></span></h3>
			   		<button type="submit" class="show-filter-property"></button>
			   	<?php endif;?>
			   		<?php if( get_option('permalink_structure') == '' && !empty($result_page_url) ) : ?>
			   		<?php endif; ?>
			   		<div class="gsearch-content">
			   			<div class="gsearch-field">

					   		<?php 
					   		// count number of search fields 
					   		$field_count = 0;
                            if( re_get_property_search_setting('pos1','property_location') ) {
                                re_property_advanced_search_field(re_get_property_search_setting('pos1','property_location'),$show_status);
                                $field_count++;
                            }
					   		if( re_get_property_search_setting('pos2','property_sub_location') ) {
					   			re_property_advanced_search_field(re_get_property_search_setting('pos2','property_sub_location'),$show_status);
					   			$field_count++;
					   		}
					   		if( re_get_property_search_setting('pos3','property_status') ) {
					   			re_property_advanced_search_field(re_get_property_search_setting('pos3','property_status'),$show_status);
					   			$field_count++;
					   		}
					   		if( re_get_property_search_setting('pos4','property_category') ) {
					   			re_property_advanced_search_field(re_get_property_search_setting('pos4','property_category'),$show_status);
					   			$field_count++;
					   		}
					   		if( re_get_property_search_setting('pos5','_bedrooms') ) {
					   			re_property_advanced_search_field(re_get_property_search_setting('pos5','_bedrooms'),$show_status);
					   			$field_count++;
					   		}
					   		if( re_get_property_search_setting('pos6','_bathrooms') ) {
					   			re_property_advanced_search_field(re_get_property_search_setting('pos6','_bathrooms'),$show_status);
					   			$field_count++;
					   		}
					   		if( re_get_property_search_setting('pos7','_price') ) {
					   			re_property_advanced_search_field(re_get_property_search_setting('pos7','_price'),$show_status);
					   			$field_count++;
					   		}
					   		if( re_get_property_search_setting('pos8','_area') ) {
					   			re_property_advanced_search_field(re_get_property_search_setting('pos8','_area'),$show_status);
					   			$field_count++;
					   		}
					   		?>
					   		<?php 
					   		if($show_advanced_search_field) {
						   		$advanced_search_field = re_get_property_search_setting('advanced_search_field',array());
						   		if(!empty($advanced_search_field) && is_array($advanced_search_field) && ($features = re_get_property_feature_fields())){
						   			echo '<div class="gsearch-feature">';
						   			echo '<a href="#gsearch-feature" class="gsearch-feature-control" data-parent="#gsearch-feature" data-toggle="collapse">'.__('Advanced Search','noo').'</a>';
						   			echo '<div id="gsearch-feature" class="panel-collapse collapse row">';
						   			foreach ($features as $key => $feature){
						   				if(in_array($key, $advanced_search_field)){
							   				$id = '_noo_property_feature_'.$key;
							   				$cheked = isset($_GET[$id]) ? true : false;
							   				echo '<div class="col-sm-3">';
							   				echo '<label class="checkbox-label" for="'.$id.'"><input '.($cheked && $show_status ? ' checked="checked"':'').' type="checkbox" value="1" class="" name="'.$id.'" id="'.$id.'">&nbsp;'.ucfirst($feature).'</label>';
							   				echo '</div>';
						   				}
						   			}
						   			echo '</div>';
						   			echo '</div>';
						   		}
					   		}
					   		?>
					   	</div>
					   	<div class="gsearch-action">
					   		<div  class="gsubmit <?php if( $field_count <= 4 ) echo 'one-line'; ?>">
					   			<button type="submit"><?php echo $btn_label ?></button>
					   		</div>
					   	</div>
			   		</div>
			   	</form>
			</div>
		<?php if( !$no_search_container ) : ?>
		</div>
		<?php endif; ?>
	</div>