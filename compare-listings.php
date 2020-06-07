<?php
/*
Template Name: Compare Listings
*/
$list_compare       = ( !empty( $_POST['list_compare'] ) && is_array( $_POST['list_compare'] ) ) ? $_POST['list_compare'] : 0;
$total_list_compare = count($list_compare);
$class_compare		= ( ($total_list_compare === 4 ) ? 'col-md-15' : ($total_list_compare === 3 ? 'col-md-3' : ($total_list_compare === 2 ? 'col-md-4' : 'col-md-6' ) )  );
?>
<?php get_header(); ?>
<div class="container-wrap">	
	<div class="main-content container-boxed">
		<div class="row">
			<div class="page-compare <?php noo_main_class(); ?>" role="main">
				
				<?php if ( !empty( $_POST['submit-compare'] ) && $total_list_compare > 0 ) : ?>

					<?php while ( have_posts() ) : the_post(); ?>
						<h1 class="page-title"><?php the_title(); ?></h1>
					<?php endwhile; ?>

					<div class="noo-compare-wrap row">
						
						<div class="noo-compare-item <?php echo esc_attr( $class_compare ) ?>">
							
						</div>

						<?php

							foreach ( $list_compare as $item_id ) :
								$title = get_the_title( $item_id );
								$permalink = get_permalink( $item_id );
								?>
								<div class="noo-compare-item <?php echo esc_attr( $class_compare ) ?>">

									<a class="content-thumb" title="<?php echo $title; ?>" href="<?php echo $permalink ?>">
										<?php echo get_the_post_thumbnail( $item_id, array( 250, 180 ) ) ?>
									</a>

									<h4 class="noo-compare-title">
										<a class="content-thumb" title="<?php echo $title; ?>" href="<?php echo $permalink ?>">
											<?php echo $title; ?>
										</a>
									</h4>

									<div class="property-price">
										<span><?php echo re_get_property_price_html( $item_id )?></span>
									</div>

									<?php echo get_the_term_list( $item_id, 'property_category', '<div class="property-type">' . esc_html__( 'Type: ', 'noo' ), ', ', '</div>'); ?>

									<?php echo get_the_term_list( $item_id, 'property_location', '<div class="property-location">' . esc_html__( 'Location: ', 'noo' ), ', ', '</div>'); ?>

									<?php echo get_the_term_list( $item_id, 'property_status', '<div class="property-status">' . esc_html__( 'Status: ', 'noo' ), ', ', '</div>'); ?>

								</div><!-- /.noo-compare-item -->
								<?php

							endforeach;

						?>

					</div>

					<div class="noo-compare-list row">

						<?php
							/**
							 * Show custom fields
							 */
							$custom_fields = re_get_property_custom_fields();
							foreach ( $custom_fields as $item ) :

								?>
								<div class="compare-list-item row">
									
									<div class="<?php echo esc_attr( $class_compare ) ?> item-label"><?php echo esc_html( $item['label'] ); ?></div>
									<?php
										foreach ( $list_compare as $id_property ) :
											
											echo '<div class="' . esc_attr( $class_compare ) . ' item-value">';

											if ( $item['name'] === '_area' ) :

												echo trim( re_get_property_area_html( $id_property ) );

											else :

												if ( $item['name'] === '_bedrooms' || $item['name'] === '_bathrooms' ) :

													$meta_key = $item['name'];

												else :

													$meta_key = '_noo_property_field_' . $item['name'];

												endif;
												$value = get_post_meta( $id_property, $meta_key, true );
												if ( !empty( $value ) ) :

													if ( is_array( $value ) ) :

														echo implode( ', ', $value );

													else :

														echo esc_html( $value );

													endif;

												endif;

											endif;

											echo '</div>';

										endforeach;
									?>

								</div><?php

							endforeach;

						?>

						<?php
							/**
							 * Show featured fields
							 */
							$features = (array) re_get_property_feature_fields();

							if( !empty( $features ) && is_array( $features ) ) :

								$show_no_feature = ( re_get_property_feature_setting('show_no_feature') == 'yes' );
							
								foreach ( $features as $key => $feature ) :

									if ( empty( $feature ) ) continue;

									echo '<div class="compare-list-item row">';
									
									echo '<div class="' . esc_attr( $class_compare ) . ' item-label">' . esc_html( $feature ) . '</div>';

									foreach ( $list_compare as $id_property ) :

										echo '<div class="' . esc_attr( $class_compare ) . ' item-value">';

											if( noo_get_post_meta( $id_property, '_noo_property_feature_' . $key ) ) :
											
												echo '<i class="fa fa-check" aria-hidden="true"></i>';

											else :

												echo '<i class="fa fa-times" aria-hidden="true"></i>';

											endif;

										echo '</div>';

									endforeach;

									echo '</div>';

								endforeach;
							endif;

						?>

					</div>

				<?php else : ?>
					
					<?php echo esc_html__( 'Page should be accesible only via the compare button', 'noo' ); ?>

				<?php endif; ?>
				

			</div> <!-- /.main -->
		</div><!--/.row-->
	</div><!--/.container-full-->
</div><!--/.container-wrap-->
	
<?php get_footer(); ?>