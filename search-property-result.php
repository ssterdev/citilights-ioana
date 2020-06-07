<?php
/*
Template Name: Search Property Results
*/
?>
<?php 
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
if( is_front_page() && empty( $paged ) ) {
	$paged = get_query_var( 'page' );
}
$args = array(
		'post_type'     => 'noo_property',
		'post_status'   => 'publish',
		// 'posts_per_page' => -1,
		// 'nopaging'      => true,
		'paged'			=> $paged,
);
if( function_exists( 'pll_current_language' ) ) {
	$args['lang'] = pll_current_language();
}

$r = new WP_Query($args);

$show_map = get_theme_mod('noo_property_listing_map',1);
$show_search = get_theme_mod('noo_property_listing_search',1);
$disable_map = ( ! $show_map && $show_search ) ? ' disable_map="true"' : '';
$disable_search_form = ( $show_map && ! $show_search )  ? ' disable_search_form="true"' : '';
$search_layout = get_theme_mod('noo_property_listing_map_layout','horizontal');
$advanced_search = ($show_search && get_theme_mod('noo_property_listing_advanced_search',0)) ? ' advanced_search="true"' : '';
?>
<?php get_header(); ?>
<div class="container-wrap">
	<?php if(!empty($show_map) || !empty($show_search)):?>
	<?php echo do_shortcode('[noo_advanced_search_property style="'.$search_layout.'"' . $disable_map . $disable_search_form . $advanced_search . ']');?>
	<?php endif;?>
	<div class="main-content container-boxed max offset">
		<div class="row">
			<div class="<?php noo_main_class(); ?>" role="main">
				<?php if ( $r->have_posts() ) : ?>
					<?php 
					$args = array(
						'query'           => $r,
						'title'           => get_the_title(),
						'display_mode'    => true,
						'default_mode'    => get_theme_mod('noo_property_listing_layout','grid'),
						'show_pagination' => true,
						'ajax_pagination' => false,
						'show_orderby'    => get_theme_mod('noo_property_listing_orderby', 1)
					);
					re_property_loop( $args ); ?>
				<?php else : ?>
					<?php get_template_part( 'layouts/' . 'no-content' ); ?>
				<?php endif; ?>
				<?php 
					wp_reset_query();
					wp_reset_postdata();
				?>
			</div> <!-- /.main -->
			<?php get_sidebar(); ?>
		</div><!--/.row-->
	</div><!--/.container-boxed-->
</div><!--/.container-wrap-->
<?php get_footer(); ?>