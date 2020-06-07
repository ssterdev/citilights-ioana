<?php

if($wp_query->have_posts()):

	if(!$ajax_content) :
		$compare_listings   = noo_get_page_link_by_template( 'compare-listings.php' );
		$property_favorites = noo_get_page_link_by_template( 'property_favorites.php' );
		$social_enabled     = get_theme_mod("noo_property_social", true );

		/**
		 * Process ajax only item
		 */
		if ( empty( $ajax_only_item ) ) :?>

		<div class="properties <?php echo $mode ?>" <?php echo $ajax_pagination ? 'data-paginate="loadmore"':''?>>
			<div class="properties-header">
				<h1 class="page-title"><?php echo $title ?></h1>
					<div class="properties-toolbar" <?php if (!empty($prop_style) && $prop_style === 'style-2') {echo 'style="display: none"';} ?>>
						<a class="<?php echo $mode == 'grid' ?'selected':'' ?>" data-mode="grid" href="<?php echo esc_url(add_query_arg( 'mode','grid'))?>" title="<?php echo esc_attr__('Grid','noo')?>"><i class="fa fa-th-large"></i></a>
						<a class="<?php echo $mode == 'list' ?'selected':'' ?>" data-mode="list" href="<?php echo esc_url(add_query_arg( 'mode','list'))?>" title="<?php echo esc_attr__('List','noo')?>"><i class="fa fa-list"></i></a>
						<?php if($show_orderby):?>
							<form class="properties-ordering" method="get">
								<div class="properties-ordering-label"><?php _e('Sorted by','noo')?></div>
								<div class="form-group properties-ordering-select">
									<div class="dropdown">
										<?php 
										$order_arr = array(
											'date'=>__('Date','noo'),
											'price'=>__('Price','noo'),
											'name'=>__('Name','noo'),
											'area'=>__('Area','noo'),
											// 'bath'=>__('Bath','noo'),
											// 'bed'=>__('Bed','noo'),
											'rand'=>__('Random','noo'),
										);
										$setting_orderby = get_theme_mod('noo_property_listing_orderby_default');
										$default_orderby = !empty( $setting_orderby ) ? $setting_orderby : $default_orderby;
										$default_orderby = isset($_GET['orderby']) ? $_GET['orderby'] : $default_orderby;
										$ordered = array_key_exists($default_orderby, $order_arr) ? $order_arr[$default_orderby] : __('Date','noo');
										?>
										<span data-toggle="dropdown"><?php echo $ordered ?></span>
										<ul class="dropdown-menu">
										<?php foreach ($order_arr as $k=>$v):?>
											<li><a  data-value="<?php echo esc_attr($k)?>"><?php echo $v ?></a></li>
										<?php endforeach;?>
										</ul>
									</div>
								</div>
								<input type="hidden" name="orderby" value="">
								<?php
									foreach ( $_GET as $key => $val ) {
										if ( 'orderby' === $key || 'submit' === $key )
											continue;
										
										if ( is_array( $val ) ) {
											foreach( $val as $innerVal ) {
												echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
											}
										
										} else {
											echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
										}
									}
								?>
							</form>
						<?php endif;?>
					</div>
			</div>

			<?php 
				$tax = $wp_query->get_queried_object();
				if (isset($tax->taxonomy) && $tax->taxonomy == 'property_location') {
				  	echo '<h6>'.$tax->description .'</h6>'; 
				}
			?>
			
			<?php if ( !empty( $compare_listings ) ) : ?>

				<div class="noo-property-compare">
					
					<form style="display: none" method="POST" action="<?php echo esc_url( $compare_listings ); ?>" class="<?php echo $id_compare = uniqid( 'submit-compare-' ); ?>">
						
						<h3 class="title-compare">
							<?php echo esc_html__( 'Compare properties', 'noo' ); ?>
						</h3>

						<div class="list-compare"></div>

						<input type="submit" name="submit-compare" value="<?php echo esc_html__( 'Compare', 'noo' ); ?>" />

					</form>

				</div><!-- /.noo-property-compare -->

			<?php endif; ?>

			<div class="properties-content<?php echo $ajax_pagination ? ' loadmore-wrap':''?>">
		<?php endif; ?>
	<?php endif; ?>
    <?php if ($prop_style==='style-1'): ?>
	<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); global $post; 
		if( function_exists( 'pll_get_post' ) ) {
			if($post_id = pll_get_post($post->ID, pll_current_language())) { // get translated post (in current language) if exists
				$post = get_post($post_id);
				setup_postdata($post);
			}
		}
		$current_user = wp_get_current_user();
		$user_id      = $current_user->ID;
	?>
			<article id="property-<?php the_ID(); ?>" <?php post_class(); ?>>
			    <div class="property-featured">
			    	<?php if('yes' === get_post_meta($post->ID,'_featured',true)):?>
			    		<span class="featured"><i class="fa fa-star"></i></span>
			    	<?php endif;?>
			    	<?php 
			    	$defaultIMG = get_template_directory_uri().'/assets/images/placeholder.jpg'; 
					$img = get_the_post_thumbnail(get_the_ID(),'property-thumb');
			    	 ?>
			        <a class="content-thumb xx" href="<?php the_permalink() ?>">
						<?php 
							if ($img != null) {
				                echo $img;
				            } if ($img == null) {
				                echo '<img src="'.$defaultIMG.'">';
				            }
						?>
					</a>
					<?php 
					$_label = get_post_meta($post->ID,'_label',true);
					if(!empty($_label) && ($property_label = get_term($_label, 'property_label'))):
						$noo_property_label_colors = get_option('noo_property_label_colors');
						$color 	= isset($noo_property_label_colors[$property_label->term_id]) ? $noo_property_label_colors[$property_label->term_id] : '';
					?>
						<span class="property-label" <?php echo (!empty($color) ? ' style="background-color:'.$color.'"':'')?>><?php echo $property_label->name?></span>
					<?php endif;?>
					<?php $status = get_the_terms( get_the_ID(), 'property_status' ); ?>
					<?php if( !empty($status) && !is_wp_error( $status ) ) : ?>
						<span class="property-label sold"><?php echo $status[0]->name; ?></span>
					<?php endif; ?>
					<?php echo get_the_term_list(get_the_ID(), 'property_category', '<span class="property-category">', ', ', '</span>') ?>

					<?php if ( !empty( $show_remove_favorites ) ) : ?>

						<span class="remove_favorites" data-user="<?php echo $user_id; ?>" data-id="<?php echo get_the_ID(); ?>" data-div="property-<?php the_ID(); ?>">
							<i class="fa fa-trash-o property-action-button" aria-hidden="true" data-original-title="<?php echo __('Remove favorite', 'noo' ); ?>"></i>
						</span>

					<?php endif; ?>
					<a class="out-link" title="" href="<?php the_permalink() ?>">
						<i class="fa fa-link" aria-hidden="true"></i>
					</a>
			    </div>
				<div class="property-wrap">
					<h2 class="property-title">
						<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permanent link to: "%s"','noo' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a>
						<?php if($is_fullwidth):?>
						<small><?php echo get_post_meta($post->ID,'_address',true)?></small>
						<?php endif;?>
					</h2>
					<div class="property-excerpt">
						<?php if($excerpt = get_the_excerpt()):?>
							<?php 
							$num_word = 15;
							$excerpt = strip_shortcodes($excerpt);
							echo '<p>' . wp_trim_words($excerpt,$num_word,'...') . '</p>';
							echo '<p class="property-fullwidth-excerpt">' . wp_trim_words($excerpt,25,'...') . '</p>';
							?>
						<?php endif;?>
					</div>
					<div class="property-summary">
						<?php echo re_property_summary(); ?>
						<div class="property-info <?php echo esc_attr( $display_style ); ?>">
							<div class="property-price">
								<span><?php echo re_get_property_price_html(get_the_ID())?></span>
							</div>

							<?php if ( !empty( $display_style ) && $display_style === 'style-1' ) : ?>
								<?php if ( !empty( $social_enabled ) ) : ?>
									<div class="property-sharing <?php echo $uid = uniqid( 'sharing' ); ?>">
										<?php echo re_property_social_share(); ?>
									</div>
								<?php endif; ?>
								<div class="property-action">
									<?php

										$is_favorites       = get_user_meta( $user_id, 'is_favorites', true );
										$check_is_favorites = ( !empty( $is_favorites ) && in_array( get_the_ID(), $is_favorites ) ) ? true : false;
										$class_favorites    = $check_is_favorites ? 'is_favorites' : 'add_favorites';
										$text_favorites     = $check_is_favorites ? esc_html__( 'View favorites', 'noo' ) : esc_html__( 'Add to favorites', 'noo' );
										$icon_favorites     = $check_is_favorites ? 'fa-heart' : 'fa-heart-o';
									?>
									<i title="<?php echo esc_html__( 'Compare', 'noo' ); ?>"
									data-user="<?php echo $user_id; ?>"
									data-id="<?php echo get_the_ID(); ?>"
									data-action="compare"
									data-thumbnail="<?php the_post_thumbnail_url( 'property-floor' ); ?>"
									data-div="<?php echo esc_attr( $id_compare ); ?>" class="property-action-button fa fa-plus compare-<?php echo get_the_ID(); ?>" aria-hidden="true"></i>
									<?php if( ( re_get_agent_setting('users_can_register', true) && !is_user_logged_in() ) || is_user_logged_in() ) : ?>
										<i title="<?php echo esc_html( $text_favorites ); ?>" data-user="<?php echo $user_id; ?>" data-id="<?php echo get_the_ID(); ?>" data-action="favorites" data-status="<?php echo esc_attr( $class_favorites ); ?>" data-url="<?php echo esc_attr( $property_favorites ); ?>" class="property-action-button fa <?php echo esc_attr( $icon_favorites ); ?>" aria-hidden="true"></i>
									<?php endif; ?>
									<?php if ( !empty( $social_enabled ) ) : ?>
										<i title="<?php echo esc_html__( 'Share', 'noo' ); ?>" data-user="<?php echo $user_id; ?>" data-id="<?php echo get_the_ID(); ?>" data-action="sharing" data-class="<?php echo esc_attr( $uid ); ?>" class="property-action-button fa fa-share-alt" aria-hidden="true"></i>
									<?php endif; ?>
								</div>
							<?php elseif ( $display_style === 'style-2' ) : ?>
								<div class="property-action readmore-property">
								 	<a class="readmore-link" href="<?php the_permalink(); ?>" title="<?php the_title() ?>">
								 		<?php echo esc_html__( 'More Details', 'noo' ); ?>
								 	</a>
								</div>
							<?php endif; ?>
						</div>
						<div class="property-info property-fullwidth-info">
							<div class="property-price">
								<span><?php echo re_get_property_price_html(get_the_ID())?></span>
							</div>
							<?php echo re_property_summary( array('container_class'=>'') ); ?>
						</div>
					</div>
				</div>
				<div class="property-action property-fullwidth-action">
					<a href="<?php the_permalink()?>"><?php echo __('More Details','noo')?></a>
				</div>
			</article> <!-- /#post- -->
	<?php endwhile; ?>
<?php endif; ?>
    <?php if ($prop_style === 'style-2'): ?>
    <div class="property-exclusives">
         <div class="exclusives-content">
            <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); global $post;
                    if( function_exists( 'pll_get_post' ) ) {
                        if($post_id = pll_get_post($post->ID, pll_current_language())) { // get translated post (in current language) if exists
                            $post = get_post($post_id);
                            setup_postdata($post);
                        }
                    }
                    $current_user = wp_get_current_user();
                    $user_id      = $current_user->ID;
                ?>
                    <div class="exclusive-items">
                        <?php include(locate_template('/framework/property/property-style-2.php'));?>
                    </div>
            <?php endwhile; ?>
         </div>
    </div>
<?php endif; ?>
	<?php 
		/**
		 * Process ajax only item
		 */
		if ( empty( $ajax_only_item ) ) :
			if ( !$ajax_content ) :
				echo '</div>';
			endif;
		endif;

		if (defined('DOING_AJAX') && DOING_AJAX) {
		    $ajax_pagination = false;
		    $show_pagination = 'no';
		}

		if( $ajax_pagination && ( 1 < $wp_query->max_num_pages ) ) :
			$current_page = !empty( $current_page ) ? absint( $current_page ) : 1;
			$class_loadmore = uniqid( 'loadmore-' );
			?>
			<div class="loadmore-action <?php echo esc_attr( $class_loadmore ); ?>">
				<div class="noo-loader loadmore-loading">
		            <div class="rect1"></div>
		            <div class="rect2"></div>
		            <div class="rect3"></div>
		            <div class="rect4"></div>
		            <div class="rect5"></div>
		        </div>
				<button type="button" data-class-wrap="<?php echo esc_attr( $class_loadmore ); ?>" data-current-page="<?php echo absint( $current_page + 1 ); ?>" class="btn btn-default btn-block btn-loadmore"><?php _e('Load More','noo')?></button>
			</div>
		<?php endif;

		if ( empty( $ajax_only_item ) ) :
			if( ( !empty( $show_pagination ) && $show_pagination == 'yes' ) || $ajax_pagination ){
				noo_pagination( array(), $wp_query );
			}
			echo '</div>';
		endif;

endif;