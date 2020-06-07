<?php
/*
Template Name: Half-Map Property
*/
?>
<?php get_header(); ?>
<div class="container-wrap">	
	<div class="main-content container-fullwidth">
		<div class="row">
			<div class="<?php noo_main_class(); ?>" role="main">
				<!-- Begin The loop -->
				<?php if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : the_post(); ?>
						
						<div class="page-map-left">
							<?php echo do_shortcode( '[noo_advanced_search_property disable_search_form="true"]' ); ?>
						</div>

						<div class="page-map-right">
							<?php echo do_shortcode( '[noo_advanced_search_property disable_map="true" show_loading="false" advanced_search="true"]' ); ?>
							<div class="results-property-map">
								<?php 
								$current_page = ( !empty( $_POST['current_page'] ) && $_POST['current_page'] !== 'NaN' ) ? absint( $_POST['current_page'] ) : 1;
								$args = array(
									'posts_per_page' => 4,
									'post_status'    => 'publish',
									'post_type'      => 'noo_property',
									'paged'			 => $current_page
								);

								$args = re_property_query_from_request( $args, $_POST );

								$wp_query = new WP_Query( apply_filters( 'noo_query_ajax_filter_map', $args ) );

								

								/**
								 * Set default
								 */
									$title                 = !empty( $hide_head ) ? '' : esc_html__( 'Your search results', 'noo' );
									$display_mode          = !empty( $hide_head ) ? false : true;
									$show_remove_favorites = false;
									$mode                  = get_theme_mod( 'noo_property_listing_layout', 'grid' );
									$show_pagination       = false;
									$ajax_pagination       = true;
									$show_orderby          = true;
									$ajax_content          = false;
									$is_fullwidth          = false;
									$display_style 		   = get_theme_mod( 'noo_property_display_style', 'style-1' );
									if ( !empty( $_POST['hide_orderby'] ) ){
										$show_orderby          = false;
									}
								/**
								 * Check query and process
								 */
								    $prop_style = 'style-1';
									ob_start();
							        include(locate_template("layouts/noo-property-loop.php"));
							        echo ob_get_clean();

								wp_reset_postdata();
								wp_reset_query();
								?>
							</div>
						</div>

					<?php endwhile; ?>
				<?php endif; ?>
				<!-- End The loop -->
			</div> <!-- /.main -->
		</div><!--/.row-->
	</div><!--/.container-full-->
</div><!--/.container-wrap-->
	
<?php wp_footer(); ?>