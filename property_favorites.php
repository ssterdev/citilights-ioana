<?php
/*
Template Name: Property Favorites
*/

NooAgent::check_logged_in_user();

$current_user = wp_get_current_user();

$user_id  = $current_user->ID;
$agent_id = get_user_meta($user_id, '_associated_agent_id', true );
apply_filters( 'wpml_object_id', $agent_id, 'noo_agent' );

$has_err            = false;
$err_message        = array();
$success            = false;

$agent_prefix = '_noo_agent';

// Default Value
// Basic Information
$title		= '';
$agent			= empty( $agent_id ) ? '' : get_post($agent_id);

if( empty( $agent ) ) {
	$title		= $current_user->first_name . ' ' . $current_user->last_name;
	$title		= ( trim($title) == '' ) ? $current_user->user_login : $title;

	$desc		= get_user_meta($user_id, 'description', true);
} else {
	$title				= $agent->post_title;
	$desc				= $agent->post_content;
	$avatar				= get_post_thumbnail_id($agent_id);
}
$prefix = RE_AGENT_META_PREFIX;
$fields = re_get_agent_custom_fields();
$socials = re_get_agent_socials();

$current_user = wp_get_current_user();
$user_id  = $current_user->ID;

$is_favorites       = get_user_meta( $user_id, 'is_favorites', true );
$check_is_favorites = ( !empty( $is_favorites ) && is_array( $is_favorites ) ) ? true : false;

get_header(); ?>

<div class="container-wrap">
	<div class="main-content container-boxed max offset">
		<div class="row">
			<div class="noo-sidebar col-md-4">
				<div class="noo-sidebar-wrapper">
				<?php get_template_part( 'layouts/' . 'agent_menu' );  ?>
				</div>
			</div>
			<div class="<?php noo_main_class(); ?>" role="main">   
				<?php if ( !$check_is_favorites ) : ?>

					<div class="favorites-header">

						<h1 class="page-title"><?php _e('My Favorites', 'noo'); ?></h1>

					</div>

				<?php endif; ?>
				<div class="favorites-content">
					<?php
						if ( $check_is_favorites )  :

							/**
							 * Set default
							 */
								$title                 = esc_html__( 'My Favorites', 'noo' );
								$display_mode          = true;
								$show_remove_favorites = true;
								$default_mode          = get_theme_mod('noo_property_listing_layout','grid');
								$show_pagination       = true;
								$ajax_pagination       = false;
								$show_orderby          = true;
								$ajax_content          = false;
								$is_fullwidth          = false;
								$mode                  = false;
								$display_style 		   = get_theme_mod( 'noo_property_display_style', 'style-1' );
								$prop_style = 'style-1';
							/**
							 * Create query
							 */
								$args = array(
									'post_type'   => 'noo_property',
									'post__in'    => $is_favorites,
								);

								$wp_query = new WP_Query( apply_filters( 'noo_query_page_favorites', $args ) );

							/**
							 * Check query and process
							 */
								ob_start();
						        include(locate_template("layouts/noo-property-loop.php"));
						        echo ob_get_clean();

						else :

							echo esc_html__( 'You don\'t have any favorite properties yet!', 'noo' );

						endif;

					?>
				</div><!-- /.favorites-content -->
			</div> <!-- /.main -->
		</div><!--/.row-->
	</div><!--/.container-boxed-->
</div><!--/.container-wrap-->  
<?php get_footer(); ?>