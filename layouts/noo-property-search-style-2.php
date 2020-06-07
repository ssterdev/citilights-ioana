<?php
/**
 * Created by PhpStorm.
 * User: Vietbrain-09
 * Date: 16-Jul-18
 * Time: 9:16 AM
 */
?>
	<div class="gsearch property flex-column" style="background: rgba(0,0,0,0); min-height: <?php echo $map_height?>px;">
		<?php if( !$no_search_container ) : ?>
		<div class="container-boxed">

		<?php endif; ?>

				<?php if( !empty( $search_info_title ) ) :
					?>
					<div class="gsearch-info-style-2" >
						<?php if( !empty( $search_info_title ) ) : ?>
							<span class="gsearch-info-title" id="infoTitle"><?php echo esc_html($search_info_title);?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

		   	<div class="gsearch-wrap">

			   	<form action="<?php echo $result_page_url ?>" class="gsearchform" method="get" role="search">
		   		<?php if($gmap):?>
			   		<h3 class="gsearch-title"><i class="fa fa-search"></i><span><?php echo __('SEARCH FOR PROPERTY','noo')?></span></h3>
			   		<button type="submit" class="show-filter-property"></button>
			   	<?php endif;?>
			   		<?php if( get_option('permalink_structure') == '' && !empty($result_page_url) ) : ?>
			   		<input type="hidden" name="page_id" value="<?php echo $first_page->ID; ?>" >
			   		<?php endif; ?>
			   		<div class="gsearch-content" style="display: block">
			   			<div class="gsearch-field bg-white-opacity b-lr search_style_2">
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
						   			echo '<a id="gsearchFeature" href="#gsearch-feature" class="gsearch-feature-control" data-parent="#gsearch-feature" data-toggle="collapse">'.__('Advanced Search','noo').'</a>';
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

                        <div class="gsearch-action-style-2">
                            <div class="gsubmit <?php if( $field_count <= 4 ) echo 'one-line'; ?>">
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
<script type="text/javascript">
    jQuery('document').ready(function ($) {
        $('#gsearchFeature').click(function () {
            $('#infoTitle').toggleClass('toggle-title');
        })

    })
</Script>