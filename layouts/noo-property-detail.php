<?php 
$current_user       = wp_get_current_user();
$user_id            = $current_user->ID;
$property_favorites = noo_get_page_link_by_template( 'property_favorites.php' );
$property_id = empty( $property_id ) ? get_the_ID() : $property_id;

	while ($query->have_posts()): $query->the_post(); global $post;
	$is_favorites       = get_user_meta( $user_id, 'is_favorites', true );
	$check_is_favorites = ( !empty( $is_favorites ) && in_array( get_the_ID(), $is_favorites ) ) ? true : false;
	$class_favorites    = $check_is_favorites ? 'is_favorites' : 'add_favorites';
	$text_favorites     = $check_is_favorites ? esc_html__( 'View favorites', 'noo' ) : esc_html__( 'Add to favorites', 'noo' );
	$icon_favorites     = $check_is_favorites ? 'fa-heart' : 'fa-heart-o';

	?>


<?php
	    if(array_key_exists('button1', $_POST)) { 
            button1(); 
        } 
        else if(array_key_exists('button2', $_POST)) { 
            button2(); 
        }  
		else if(array_key_exists('button3', $_POST)) { 
            button3(); 
        }  
		else if(array_key_exists('button4', $_POST)) { 
            button4(); 
        } 
		else if(array_key_exists('button5', $_POST)) {
			button5();
		}
?>
<!--
  <form method="post"> 
        <input type="submit" name="button1"
                class="button" value="create" /> 
          
        <input type="submit" name="button2"
                class="button" value="excel" /> 
	  
	  	<input type="submit" name="button3"
                class="button" value="update_post_meta" /> 
	  
	  	<input type="submit" name="button4"
                class="button" value="get_post_meta" />
	  	<input type="submit" name="button5"
                class="button" value="add_post_meta" />
	  
    </form> 
-->


	<article id="post-<?php the_ID(); ?>" class="property">
		<div class="property-title-wrap clearfix">
			<h1 class="property-title">
				<?php the_title(); ?>
				<small><?php echo noo_get_post_meta(null,'_address')?></small>
			</h1>
			<?php re_property_social_share( get_the_id() ); ?>
		</div>
		<?php 
		$gallery        = noo_get_post_meta( get_the_ID(), '_gallery', '' );
		$gallery_ids    = explode(',',$gallery);
		$gallery_ids    = array_filter($gallery_ids);
		$featured_image = get_post_thumbnail_id();
		
		$floor_plan     = noo_get_post_meta( get_the_ID(), '_floor_plan', '' );
		$floor_plan_ids = explode( ',', $floor_plan );
		$floor_plan_ids = array_filter( $floor_plan_ids );


		$_pdf_file     = noo_get_post_meta( get_the_ID(), '_pdf_file', '' );
		$_pdf_file_ids = explode( ',', $_pdf_file );
		$_pdf_file_ids = array_filter( $_pdf_file_ids );


		$property_category     = get_the_term_list(get_the_ID(), 'property_category', '<span class="col-sm-7 detail-field-value type-value">', ', ', '</span>');
		$property_status       = get_the_term_list(get_the_ID(), 'property_status', '<span class="col-sm-7 detail-field-value status-value">', ', ', '</span>');
		$property_location     = get_the_term_list(get_the_ID(), 'property_location', '', ', ');
		$property_sub_location = get_the_term_list(get_the_ID(), 'property_sub_location', '', ', ');
		$property_price        = re_get_property_price_html( get_the_ID() );
		$property_area         = trim( re_get_property_area_html( get_the_ID() ) );
		$property_bedrooms     = noo_get_post_meta(get_the_ID(), '_bedrooms' );
		$property_bathrooms    = noo_get_post_meta(get_the_ID(), '_bathrooms' );

		if( !empty($featured_image) || !empty( $gallery_ids ) ) :
		?>
		    <div class="property-featured clearfix">
		    	<div class="images">
		    		<div class="caroufredsel-wrap">
			    		<ul>
				    		<?php 
				    		if(!empty($featured_image) && empty( $gallery_ids )) :
				    			$image = wp_get_attachment_image_src($featured_image,'full');
				    		?>
					    		<li>
									<a class="noo-lightbox-item" data-lightbox-gallery="gallert_<?php the_ID()?>" href="<?php echo $image[0]?>"><?php echo get_the_post_thumbnail(get_the_ID(), 'full' ) ?></a>

					    		</li>
					    	<?php endif;?>
					    	<?php if(!empty($gallery_ids)): ?>
					    		<?php foreach ($gallery_ids as $gallery_id):
					    			$gallery_image = wp_get_attachment_image_src($gallery_id,'full'); 
					    			if( $gallery_image ) : ?>
							    		<li>
							    			<a class="noo-lightbox-item" data-lightbox-gallery="gallert_<?php the_ID()?>" href="<?php echo $gallery_image[0]?>"><?php echo wp_get_attachment_image( $gallery_id, 'full' ); ?></a>
							    		</li>
					    			<?php endif;?>
					    		<?php endforeach;?>
					    	<?php endif;?>
			    		</ul>
				    	<a class="slider-control prev-btn" role="button" href="#"><span class="slider-icon-prev"></span></a>
				    	<a class="slider-control next-btn" role="button" href="#"><span class="slider-icon-next"></span></a>
		    		</div>

					<?php if( ( re_get_agent_setting('users_can_register', true) && !is_user_logged_in() ) || is_user_logged_in() ) : ?>
		    			<div class="property-action favorites-property">
						<i title="<?php echo esc_html( $text_favorites ); ?>" data-user="<?php echo $user_id; ?>" data-id="<?php echo get_the_ID(); ?>" data-action="favorites" data-status="<?php echo esc_attr( $class_favorites ); ?>" data-url="<?php echo esc_attr( $property_favorites ); ?>" class="property-action-button fa <?php echo esc_attr( $icon_favorites ); ?>" aria-hidden="true"></i>
						</div>
					<?php endif; ?>
						
		    	</div>
		    	<?php if(!empty($gallery_ids)) : ?>
			    	<div class="thumbnails">
			    		<div class="thumbnails-wrap">
				    		<ul>
					    	<?php $i = 0; ?>
				    		<?php if( !empty($featured_image) && empty( $gallery_ids )) : $i++;?>
					    		<li>
					    			<a data-rel="0" href="<?php echo $image[0]?>"><?php echo get_the_post_thumbnail(get_the_ID(), 'property-thumb') ?></a>
					    		</li>
					    	<?php endif; ?>
				    		<?php foreach ($gallery_ids as $index => $gallery_id):
				    			$thumbnail_image = wp_get_attachment_image($gallery_id, 'property-thumb'); 
					    		if( !empty( $thumbnail_image ) ) : ?>
						    		<li>
						    			<a data-rel="<?php echo $i++; ?>" href="#"><?php echo $thumbnail_image; ?></a>
						    		</li>
				    			<?php endif;?>
				    		<?php endforeach;?>
				    		</ul>
				    	</div>
				    	<a class="caroufredsel-prev" href="#"></a>
				    	<a class="caroufredsel-next" href="#"></a>
			    	</div>
		    	<?php endif;?>
		    	<?php 
				$_label = noo_get_post_meta(get_the_ID(),'_label');
				if(!empty($_label) && ($property_label = get_term($_label, 'property_label'))):
					$noo_property_label_colors = get_option('noo_property_label_colors');
					$color 	= isset($noo_property_label_colors[$property_label->term_id]) ? $noo_property_label_colors[$property_label->term_id] : '';
				?>
					<span class="property-label" <?php echo (!empty($color) ? ' style="background-color:'.$color.'"':'')?>><?php echo $property_label->name?></span>
				<?php endif;?>
		    </div>
		<?php endif;?>

		<?php 
			// Sub Property info
			sub_listing_property_detail($property_id); 
		?>

		<div class="property-summary clearfix">
			<div class="row">
				<div class="property-detail col-md-4 col-sm-4">
					<h4 class="property-detail-title"><?php _e('Property Detail','noo')?></h4>
					<div class="property-detail-content">
						<div class="detail-field row">
							<?php if( !empty($property_category) ) : ?>
								<span class="col-sm-5 detail-field-label type-label"><?php echo __('Type','noo')?></span>
								<?php echo $property_category?>
							<?php endif; ?>
							<?php if( !empty($property_status) ) : ?>
								<span class="col-sm-5 detail-field-label status-label"><?php echo __('Status','noo')?></span>
								<?php echo $property_status?>
							<?php endif; ?>
							<?php if( !empty($property_location) ) : ?>
								<span class="col-sm-5 detail-field-label location-label"><?php echo __('Location','noo')?></span>
								<span class="col-sm-7 detail-field-value location-value"><?php echo $property_location?></span>
							<?php endif; ?>
							<?php if( !empty($property_sub_location) ) : ?>
								<span class="col-sm-5 detail-field-label sub_location-label"><?php echo __('Sub Location','noo')?></span>
								<span class="col-sm-7 detail-field-value sub_location-value"><?php echo $property_sub_location?></span>
							<?php endif; ?>
							<?php if( !empty($property_price) ) : ?>
								<span class="col-sm-5 detail-field-label price-label"><?php echo __('Price','noo')?></span>
								<span class="col-sm-7 detail-field-value price-value"><?php echo $property_price?></span>
							<?php endif; ?>
							<?php $custom_fields = re_get_property_custom_fields();
								$property_id = get_the_ID();
								if( function_exists('pll_get_post') ) $property_id = pll_get_post( $property_id );
							?>
							<?php foreach ((array)$custom_fields as $field) {
								
								if( !isset( $field['name'] ) || empty( $field['name'] )) continue;
								$field['type'] = isset( $field['type'] ) ? $field['type'] : 'text';
								$id = re_property_custom_fields_name($field['name']);
								if( isset( $field['is_default'] ) ) {
									if( isset( $field['is_disabled'] ) && ($field['is_disabled'] == 'yes') )
										continue;
									if( isset( $field['is_tax'] ) )
										continue;
									$id = $field['name'];
									
									
								}
								
								$value = noo_get_post_meta($property_id,$id,null);

								$args = array(
										'label_tag' => 'span',
										'label_class' => 'col-sm-5 detail-field-label',
										'value_tag' => 'span',
										'value_class' => 'col-sm-7 detail-field-value'
									);
								
								
								if ($id == "_area"){
                                noo_display_field( $field, $id, number_format($value), $args );                        
                                } elseif ($id == "_noo_property_field_lot_area") {
                                    noo_display_field( $field, $id, number_format($value), $args );                            
                                } elseif ($id != "_noo_property_field_strn1" && $id != "_noo_property_field_strn2" && $id != "_noo_property_field_strn3" && $id != "_noo_property_field_strn4" && $id != "_noo_property_field_strn5" && $id != "_noo_property_field_strn6" && $id != "_noo_property_field_ff1" && $id != "_noo_property_field_ff2" && $id != "_noo_property_field_ff3" && $id != "_noo_property_field_ff4") {
                                    noo_display_field( $field, $id, $value, $args );
                                }
                            } ?>
						</div>
					</div>
				</div>
				<div class="property-desc col-md-8 col-sm-8">
					<h4 class="property-detail-title"><?php _e('Property Description','noo')?></h4>
					<div class="property-content">
						<?php the_content();?>
					</div>
				</div>
				
			
				
				
			</div>
				
		</div>


		
<script type="text/javascript">
function sam_click()
{

	var x="<?php trimitere_mail(test); ?>";
	alert(x);
	
}
	

</script>
		
			

	<div class = "sergiu-collapse-btn">
		<div class = "collapse-wrap">
	
		<p>
  
  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
    Contact Owner
  </button>
</p>
<div class="collapse" id="collapseExample">
  <form id="frminvestormail" action="#" method="post" onsubmit="return sam_click();" class="" style="display: block;">
                                                        <input type="hidden" name="ContactEmail" id="ContactEmail">
                                                        <p class="uk-margin-small uk-text-small"><strong>Subject</strong></p>
                                                        <input type="text" name="txtSubject" value="7900 Westheimer Rd 120" class="sergiu-uk-input uk-display-block rounded uk-text-small">

                                                        <p class="uk-margin-small uk-text-small"><strong>Body</strong></p>
                                                        <textarea id="txtMailBody" name="txtMailBody" rows="4" cols="10" class="sergiu-uk-textarea rounded uk-text-small" onfocus="replaceMessage(this);" onblur="replaceMessage(this);" placeholder="Type your message here..."></textarea>

                                                        <input type="hidden" name="property" value="WD176924">
	  													<div class="sergiu-rounded-wrap">
															
	  
                                                        <input type="submit" onclick="" name="sub" value="Submit Message" class="sergiu-rounded uk-display-inline-block alt-blue uk-button-large btn-submit uk-margin-auto uk-width-1-1 uk-text-center uk-margin-top">
</div>
                                                        <div class="SuccessMessage uk-text-small uk-margin-top">
                                                        
                                                            <!-- <strong>Note:</strong> The footer of your email will read, "<em>The person who sent you this message is a free member. They have not chosen to pay for a Premium Membership, so they may or may not be a serious investor.</em>"<br /><br /> -->
                                                            <p><a href="/account/upgrade/step1.asp?leadtype=1&amp;propertyId=176924">Upgrade to Premium now</a>, and it will read, "<em>The person who sent you this email is a Premium Member, meaning that he or she pays to access the properties before other investors. This person is clearly serious about their investing.</em>"</p>

                                                            <p>The recipient will reply to the email address that you have on file in the "My Account" section.</p>

                                                            <p>Use this form for property-specific inquiries only. No solicitations allowed. All messages are scanned for inappropriate content.</p>
                                                        
                                                        </div>
                                                    </form>
	
	
	
</div>
		</div>
		</div>	
		
		
	   <?php
        $array = array(
            "1" => "unu",
            "2" => "doi",
            "3" => "Think some of these sellers want too much money? If so, you're right. But what they want and what they get are two different things. Make enough offers, even if they're well below the sellers' asking price, and you'll get some accepted.",
            "4" => "Most sellers are not experts at determining the cost of repairs. It's best that you visit the property to calculate what your actual cost is likely to be.",
            "5" => "cinci",
            "6" => "You can use this figure to quickly find the deals that interest you. It is not meant to be an estimate or guarantee of your profits if you buy the property. It does not factor in closing costs, carrying costs, Realtor fees, etc. We do not include these amounts because they are different for each investor and for each buying and selling strategy.",
        );
        $array_title = array(
            "1" => "unu",
            "2" => "doi",
            "3" => "Seller's Asking Price",
            "4" => "Estimated Cost of Repairs",
            "5" => "cinci",
            "6" => "Estimated Equity Spread",
        );
        $counter = 1;
        ?>
        <div class= "sergiu">
            <h4 class="font_2">
            Short term rental numbers
            </h4>
                <table>
                   
           
            <?php foreach ((array)$custom_fields as $field) {
                               
                               
                                $id = re_property_custom_fields_name($field['name']);
                           
                               
                                $value = noo_get_post_meta($property_id,$id,null);
 
                                $args = array(
                                        'label_tag' => 'td',
                                        'label_class' => 'sergiu_label',
                                        'value_tag' => 'td',
                                        'value_class' => 'sergiu_val'
                                    );
                                    if ($id == "_noo_property_field_strn1" || $id == "_noo_property_field_strn2" || $id == "_noo_property_field_strn3" || $id == "_noo_property_field_strn4" || $id == "_noo_property_field_strn5" || $id == "_noo_property_field_strn6") {
                                       
                                       
                                        echo "<tr>";
                                   
                                    noo_display_field( $field, $id, $value, $args );
                                    echo '<td><a href="#/" class="sergiu_badge" data-toggle="popover" data-trigger="hover" title="'.$array_title[$counter].'" data-content="'.$array[$counter].'"><img src="https://d3ldi349qj6gnw.cloudfront.net/images/details/Question.svg" alt="Zestimate" width="25" height="25"></a></td>';
                                        echo "</tr>";
                                        $counter++;
                                    }
                                   
                            } ?>
                   
                   
                   
                        </table>
               
               
                <script>
                    jQuery(function () {
                      jQuery('[data-toggle="popover"]').popover({ trigger: "hover" });
                    })
                </script>
        </div>
       
            <?php
        $array1 = array(
            "1" => "Some sellers are truthful about the After Repaired Values. Some sellers exaggerate. It's best that you ignore the sellers' repair and ARV estimates and draw your own conclusions. And then make them an offer based on YOUR numbers, even if your offer is $10,000 less than their asking price. That's what the successful members of myHouseDeals.com are doing. They don't give up on these deals just because one or more sellers exaggerate their numbers. They make offers.",
            "2" => "Think some of these sellers want too much money? If so, you're right. But what they want and what they get are two different things. Make enough offers, even if they're well below the sellers' asking price, and you'll get some accepted.",
            "3" => "Most sellers are not experts at determining the cost of repairs. It's best that you visit the property to calculate what your actual cost is likely to be.",
            "4" => "You can use this figure to quickly find the deals that interest you. It is not meant to be an estimate or guarantee of your profits if you buy the property. It does not factor in closing costs, carrying costs, Realtor fees, etc. We do not include these amounts because they are different for each investor and for each buying and selling strategy.",
           
        );
        $array_title1 = array(
            "1" => "Est. After Repair Value (ARV)",
            "2" => "Seller's Asking Price",
            "3" => "Estimated Cost of Repairs",
            "4" => "Estimated Equity Spread",
           
        );
        $counter1 = 1;
        ?>
       
        <div class = "sergiu2">
            <h4 class="font_2">
            Fix & Flip numbers
            </h4>
            <table>
               
                    <?php foreach ((array)$custom_fields as $field) {
                               
                               
                                $id = re_property_custom_fields_name($field['name']);
                           
                               
                                $value = noo_get_post_meta($property_id,$id,null);
 
                                $args = array(
                                        'label_tag' => 'td',
                                        'label_class' => 'sergiu_label',
                                        'value_tag' => 'td',
                                        'value_class' => 'sergiu_val'
                                    );
                                   
                                    if ($id == "_noo_property_field_ff1" || $id == "_noo_property_field_ff2" || $id == "_noo_property_field_ff3" || $id == "_noo_property_field_ff4") {
                                       
                                        echo "<tr>";
                                   
                                    noo_display_field( $field, $id, $value, $args );
                                    echo '<td><a href="#/" class="sergiu_badge" data-toggle="popover" data-trigger="hover" title="'.$array_title1[$counter1].'" data-content="'.$array1[$counter1].'"><img src="https://d3ldi349qj6gnw.cloudfront.net/images/details/Question.svg" alt="Zestimate" width="25" height="25"></a></td>';
                                        echo "</tr>";
                                        $counter1++;
                                       
                                    }
                                   
                            } ?>
            </table>
           
        </div>
       
        <div class="sergiu3">
           
            <table>
                <tr>
                <td id= "sergiu_funding" class= "sergiu_need_funding"> Need funding: </td>
                <td>
                   
                 <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
  Get Pre-Qualified
</button>  
                </td>
                </tr>
                <tr>
                <td>
                </td>
                    <td>
                   <a href="#" data-fancybox-type="iframe" data-toggle="modal" data-target="#exampleModalCenter2">
  View Other Funding Options
</a> 
                    </td>
                </tr>
            </table>
            <p class = "sergiu_paragraph">
               
            This information is provided by the seller. All numbers, including Estimated After Repair Value (ARV) and Estimated Cost of Repairs are estimates. Please do your own due diligence.</p>
        </div>
   
		<?php if( !empty( $_pdf_file_ids ) ) :
				echo '<div class="document-container">';
				
	    		echo '</div><!-- /.document-container -->';
    		endif;
    	?>

		<?php $features = (array) re_get_property_feature_fields();
		if( !empty( $features ) && is_array( $features ) ) : ?>
		<div class="property-feature">
			<h4 class="property-feature-title"><?php _e('Property Features','noo')?></h4>
			<div class="property-feature-content">
				<?php $show_no_feature = ( re_get_property_feature_setting('show_no_feature') == 'yes' ); ?>
				<?php foreach ($features as $key => $feature) : ?>
					<?php if(noo_get_post_meta(get_the_ID(),'_noo_property_feature_'.$key)) : ?>
						<div class="has">
							<i class="fa fa-check-circle"></i> <?php echo $feature; ?>
						</div>
					<?php elseif( $show_no_feature ) : ?>
						<div class="no-has">
							<i class="fa fa-times-circle"></i> <?php echo $feature; ?>
						</div>
					<?php endif; ?>
				<?php endforeach;?>
			</div>
			
			<?php 
			additional_feature($property_id);
			?>
		</div>
		<?php endif; ?>
		<?php if($_video_embedded = noo_get_post_meta(get_the_ID(),'_video_embedded','')):?>
			<div class="property-video hidden-print">
				<h4 class="property-video-title"><?php _e('Property Video','noo')?></h4>
				<div class="property-video-content">
					<?php echo noo_get_video( $_video_embedded ); ?>
				</div>
			</div>
		<?php endif;?>

		<?php if($_virtual_tour = noo_get_post_meta(get_the_ID(),'_virtual_tour','')):?>
			<div class="property-video hidden-print">
				<h4 class="property-video-title"><?php _e('360° Virtual Tour','noo')?></h4>
				<div class="property-video-content">
					<?php echo htmlspecialchars_decode($_virtual_tour); ?>
				</div>
			</div>
		<?php endif;?>
		
		<?php 
			if( !empty( $floor_plan_ids ) ) :
    			wp_enqueue_style( 'owlcarousel' );
    			wp_enqueue_script( 'owlcarousel' );
    			
				echo '<div class="floor-plan-container">';

					echo '<h4 class="floor-plan-title">' . esc_html__( 'Floor Plan', 'noo' ) . '</h4>';
					echo '<div class="floor-plan-wrap noo-row">';

	    			foreach ( $floor_plan_ids as $floor_plan_id ) :

		    			$floor_plan_image = wp_get_attachment_image_src( $floor_plan_id, 'full' ); 
		    			if( $floor_plan_image ) : ?>
				    		
			    			<a class="floor-plan-item noo-lightbox-item" data-lightbox-gallery="floor_plan_<?php the_ID()?>" href="<?php echo $floor_plan_image[0]?>">
			    				<?php echo wp_get_attachment_image( $floor_plan_id, 'property-floor' ); ?>
		    				</a>

		    			<?php endif;

		    		endforeach;

		    		echo '</div><!-- /.floor-plan-wrap -->';

	    		echo '</div><!-- /.floor-plan-container -->';

    		endif;

    	?>


		<?php
		$map_type = re_get_property_map_setting('map_type','');
		if($map_type == "google"):
			$latitude = noo_get_post_meta(get_the_ID(),'_noo_property_gmap_latitude');
			$longitude = noo_get_post_meta(get_the_ID(),'_noo_property_gmap_longitude');
			if( !empty( $latitude ) && !empty( $longitude ) ) :
			?>
				<div class="property-map">
					<h4 class="property-map-title"><?php _e('Location on map','noo')?></h4>
					<div class="property-map-content">
						<div class="property-map-search">
							<input placeholder="<?php echo __('Search your map','noo')?>" type="text" autocomplete="off" id="property_map_search_input">
						</div>
						<?php 
						$property_category_terms          =   get_the_terms(get_the_ID(),'property_category' );
						$property_category_marker = '';
						if($property_category_terms && !is_wp_error($property_category_terms)){
							$map_markers = get_option( 'noo_category_map_markers' );
							foreach($property_category_terms as $category_term){
								if(empty($category_term->slug))
									continue;
								$property_category = $category_term->slug;
								if(isset($map_markers[$category_term->term_id]) && !empty($map_markers[$category_term->term_id])){
									$property_category_marker = wp_get_attachment_url($map_markers[$category_term->term_id]);
								}
								break;
							}
						}
						?>
						<div id="property-map-<?php echo get_the_ID()?>" class="property-map-box" data-marker="<?php echo esc_attr($property_category_marker)?>" data-zoom="<?php echo esc_attr(noo_get_post_meta(get_the_ID(), '_noo_property_gmap_zoom', '16'))?>" data-latitude="<?php echo esc_attr($latitude)?>" data-longitude="<?php echo esc_attr($longitude)?>"></div>
					</div>
				</div>
			<?php endif;
		elseif($map_type == "bing"):
			$latitude = noo_get_post_meta(get_the_ID(),'_noo_property_gmap_latitude');
			$longitude = noo_get_post_meta(get_the_ID(),'_noo_property_gmap_longitude');
			wp_enqueue_script('bing-map-api');
			wp_enqueue_script('bing-map'); ?>
			<div class="property-map">
				<h4 class="property-map-title"><?php _e('Location on map','noo')?></h4>
				<div data-id="noo_property_bing_map" class="noo_property_bing_map" style='width: 100%; height:500px;'>
					<div id='noo_property_bing_map'></div>
					<input type="hidden" id="latitude" name="latitude"
				       value="<?php echo esc_attr($latitude) ?>"/>
					<input type="hidden" id="longitude" name="longitude"
					       value="<?php echo esc_attr($longitude); ?>"/>
				</div>
			</div>	
			<?php
		endif;
		?>
	</article> <!-- /#post- -->


<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div id= "modal-sergiu" class="modal-dialog modal-dialog-centered" role="document">
    <div id="modal-sergiu-content" class="modal-content">
      <div id= "modal-header-sergiu" class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Get Funding</h5>
        <button id="sergiu-button-modal-close" type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <div class="row">
			
			<div class="noo-main col-md-12  noo-page" role="main">
				<!-- Begin The loop -->
																														
																			
<p>&nbsp;</p>
<h4 class="uk-text-center" style="text-align: center;"><strong>Ready to get funding for your deal? Get lenders competing to finance your deal and get the best rate available anywhere.</strong></h4>
<p style="text-align: center;">Answer a few quick questions and get contacted by lenders who are ready and willing to fund your deal fast. To get started, select your investment strategy below:</p>
<p>&nbsp;</p>
<h4 class="uk-margin-bottom uk-margin-top uk-text-center" style="text-align: center;"><strong>What’s your investment strategy?</strong></h4>
<div class="uk-child-width-1-3@s uk-grid-small uk-margin-bottom uk-text-center uk-grid">
<h4 class="uk-first-column" style="text-align: center;"></h4>
<div class="">
<table width="1162"style="
    width: auto;">
<tbody>
<tr>
<td width="387">
<p style="text-align: center;"><strong><a href="https://wp.me/PbTAzN-3Aj"><img class="aligncenter wp-image-13765 size-full" src="https://hyperionbnb.com/wp-content/uploads/2020/06/buy-hold.png" alt="" width="126" height="126"></a>Buy &amp; Hold</strong></p>
</td>
<td style="text-align: center;" width="387"><strong><a href="https://wp.me/PbTAzN-3Aj"><img class="aligncenter wp-image-13766 size-full" src="https://hyperionbnb.com/wp-content/uploads/2020/06/fix-flip.png" alt="" width="126" height="126"></a>Fix &amp; Flip</strong></td>
<td style="text-align: center;" width="387"><strong><a href="https://wp.me/PbTAzN-3Aj"><img class="aligncenter wp-image-13767 size-full" src="https://hyperionbnb.com/wp-content/uploads/2020/06/both.png" alt="" width="126" height="126"></a>A little bit of both</strong></td>
</tr>
</tbody>
</table>
</div>
</div>
													<!-- End The loop -->
			</div> <!-- /.main -->
		</div>
      </div>
      
    </div>
  </div>
</div>


<!-- Button trigger modal -->



<!-- Modal -->
<div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div id= "modal-sergiu-2" class="modal-dialog modal-dialog-centered" role="document">
    <div id="modal-sergiu-content" class="modal-content">
      <div id= "modal-header-sergiu" class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Funding options</h5>
        <button id="sergiu-button-modal-close" type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    <div class="row">
			
			<div class="noo-main col-md-12  noo-page" role="main">
				<!-- Begin The loop -->
																														<h1 class="page-title"></h1>
																			<h2 style="text-align: center;">View Other Funding Options</h2>
<p>&nbsp;</p>
<p>&nbsp;</p>
<table>
<tbody>
<tr>
<td width="497">
<h4><img class="wp-image-13708 alignleft" src="https://hyperionbnb.com/wp-content/uploads/2020/06/hard-money-150x150.png" alt="" width="100" height="100">Hard Money</h4>
<p>Borrow from a Hard Money lender. These loans are easier to quality for, have low rates and fees, and close in as little as 4 days.</p>
<p>&nbsp;</p>
<p>&nbsp;</p></td>
<td width="60">
<h4></h4>
</td>
<td width="542">
<h4><img class="wp-image-13710 alignleft" src="https://hyperionbnb.com/wp-content/uploads/2020/06/crowdfunding-150x150.png" alt="" width="100" height="100" srcset="https://hyperionbnb.com/wp-content/uploads/2020/06/crowdfunding-150x150.png 150w, https://hyperionbnb.com/wp-content/uploads/2020/06/crowdfunding-300x300.png 300w, https://hyperionbnb.com/wp-content/uploads/2020/06/crowdfunding-200x200.png 200w, https://hyperionbnb.com/wp-content/uploads/2020/06/crowdfunding.png 350w" sizes="(max-width: 100px) 100vw, 100px">Crowdfunding</h4>
<p>Borrow up to 80% of the ARV with no origination fees up front. Crowdfunding is faster and more flexible than traditional funding, and less money is needed to close.</p>
<p>&nbsp;</p></td>
</tr>
<tr>
<td width="497">
<h4><img class="wp-image-13711 alignleft" src="https://hyperionbnb.com/wp-content/uploads/2020/06/long-term-loans-150x150.png" alt="" width="100" height="100" srcset="https://hyperionbnb.com/wp-content/uploads/2020/06/long-term-loans-150x150.png 150w, https://hyperionbnb.com/wp-content/uploads/2020/06/long-term-loans-300x300.png 300w, https://hyperionbnb.com/wp-content/uploads/2020/06/long-term-loans-200x200.png 200w, https://hyperionbnb.com/wp-content/uploads/2020/06/long-term-loans.png 350w" sizes="(max-width: 100px) 100vw, 100px">Long–Term Loans</h4>
<p>Take out a traditional long-term loan from a mortgage company or bank.</p>
<p>&nbsp;</p></td>
<td width="60">
<h4></h4>
</td>
<td width="542">
<h4><img class="wp-image-13713 alignleft" src="https://hyperionbnb.com/wp-content/uploads/2020/06/private-money-150x150.png" alt="" width="100" height="100" srcset="https://hyperionbnb.com/wp-content/uploads/2020/06/private-money-150x150.png 150w, https://hyperionbnb.com/wp-content/uploads/2020/06/private-money-300x300.png 300w, https://hyperionbnb.com/wp-content/uploads/2020/06/private-money-200x200.png 200w, https://hyperionbnb.com/wp-content/uploads/2020/06/private-money.png 350w" sizes="(max-width: 100px) 100vw, 100px">Private Money</h4>
<p>Borrow money from a private lender. Each private lender is different, but these individuals typically offer lower rates than hard money lenders, and more flexible terms.</p>
<p>&nbsp;</p>
<p>&nbsp;</p></td>
</tr>
<tr>
<td width="497">
<h4><img class="wp-image-13714 alignleft" src="https://hyperionbnb.com/wp-content/uploads/2020/06/your-own-cash-150x150.png" alt="" width="100" height="100" srcset="https://hyperionbnb.com/wp-content/uploads/2020/06/your-own-cash-150x150.png 150w, https://hyperionbnb.com/wp-content/uploads/2020/06/your-own-cash-300x300.png 300w, https://hyperionbnb.com/wp-content/uploads/2020/06/your-own-cash-200x200.png 200w, https://hyperionbnb.com/wp-content/uploads/2020/06/your-own-cash.png 350w" sizes="(max-width: 100px) 100vw, 100px">Your Own Cash</h4>
<p>Use your own cash. Those who have cash generally prefer this method because it eliminates borrowing costs and speeds up the closing process.</p>
<p>&nbsp;</p></td>
<td width="60">
<h4></h4>
</td>
<td width="542">
<h4><img class="wp-image-13715 alignleft" src="https://hyperionbnb.com/wp-content/uploads/2020/06/somone-elses-cash-150x150.png" alt="" width="100" height="100" srcset="https://hyperionbnb.com/wp-content/uploads/2020/06/somone-elses-cash-150x150.png 150w, https://hyperionbnb.com/wp-content/uploads/2020/06/somone-elses-cash-300x300.png 300w, https://hyperionbnb.com/wp-content/uploads/2020/06/somone-elses-cash-200x200.png 200w, https://hyperionbnb.com/wp-content/uploads/2020/06/somone-elses-cash.png 350w" sizes="(max-width: 100px) 100vw, 100px">Someone Else’s Cash</h4>
<p>Bring another individual into the deal who will either partner with you or be your private lender. Partners receive a portion of your profits.</p>
<p>&nbsp;</p>
<p>&nbsp;</p></td>
</tr>
<tr>
<td width="497">
<table>
<tbody>
<tr>
<td width="504">
<h4><img class="wp-image-13716 alignleft" src="https://hyperionbnb.com/wp-content/uploads/2020/06/take-over-payments-150x150.png" alt="" width="100" height="100" srcset="https://hyperionbnb.com/wp-content/uploads/2020/06/take-over-payments-150x150.png 150w, https://hyperionbnb.com/wp-content/uploads/2020/06/take-over-payments-300x300.png 300w, https://hyperionbnb.com/wp-content/uploads/2020/06/take-over-payments-200x200.png 200w, https://hyperionbnb.com/wp-content/uploads/2020/06/take-over-payments.png 350w" sizes="(max-width: 100px) 100vw, 100px">Take Over Payments</h4>
<p>Take over the existing mortgage “Subject To” the existing financing. This is much more likely to be effective for motivated seller leads as opposed to wholesale deals.</p>
<p>&nbsp;</p></td>
</tr>
</tbody>
</table>
</td>
<td width="60">
<h4></h4>
</td>
<td width="542">
<h4><img class="wp-image-13718 alignleft" src="https://hyperionbnb.com/wp-content/uploads/2020/06/no-money-needed-150x150.png" alt="" width="100" height="100" srcset="https://hyperionbnb.com/wp-content/uploads/2020/06/no-money-needed-150x150.png 150w, https://hyperionbnb.com/wp-content/uploads/2020/06/no-money-needed-300x300.png 300w, https://hyperionbnb.com/wp-content/uploads/2020/06/no-money-needed-200x200.png 200w, https://hyperionbnb.com/wp-content/uploads/2020/06/no-money-needed.png 350w" sizes="(max-width: 100px) 100vw, 100px">No Money Needed</h4>
<p>If you primarily pursue motivated seller leads and your plan is to wholesale those properties, then you don’t need cash or financing.</p>
<p>&nbsp;</p></td>
</tr>
</tbody>
</table>
													<!-- End The loop -->
			</div> <!-- /.main -->
		</div>
      </div>
      
    </div>
  </div>
</div>
	<?php 
	rp_addons_nearby_places_yelp_nearby($property_id);
	rp_addons_nearby_places_walkscore($property_id);
	 ?>
	<?php re_property_contact_agent()?>
	<?php re_similar_property();?>
<?php endwhile;
