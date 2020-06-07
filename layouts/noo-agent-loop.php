<?php 
$default_avatar = NooAgent::get_default_avatar_uri();
$prefix = RE_AGENT_META_PREFIX;
$fields = re_get_agent_custom_fields();
$all_socials = noo_get_social_fields();
$socials = re_get_agent_socials();

$cols = noo_get_option( 'noo_agent_columns', '2' );
$col_class = '';
switch( $cols ) {
	// case '3': $col_class = 'col-md-4'; break;
	// case '4': $col_class = 'col-md-3'; break;
	default: $col_class = 'col-md-6'; break;
}

$col_class .= ' col-xs-6';
$grid_style = noo_get_option( 'noo_agent_grid_style', 'ava-left' );

if($wp_query->have_posts()):
?>
	<div class="agents <?php echo (isset($_GET['mode']) ? $_GET['mode'] : 'grid') ?> container-fluid <?php echo esc_attr( $grid_style ); ?>">
		<div class="agents-header">
			<h1 class="page-title"><?php echo $title ?></h1>
		</div>
		<div class="row agents-list">
			<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); global $post; ?>
				<?php 
					// Variables

					$avatar_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
					if( empty($avatar_src) ) {
						$avatar_src		= $default_avatar;
					} else {
						$avatar_src		= $avatar_src[0];
					}
				?>
				<article id="agent-<?php the_ID(); ?>" <?php post_class( $col_class ); ?>>
				    <div class="agent-featured">
				        <a class="content-thumb" href="<?php the_permalink() ?>">
							<img src="<?php echo $avatar_src; ?>" alt="<?php the_title(); ?>"/>
						</a>
				    </div>
					<div class="agent-wrap">
						<div class="agent-summary">
							<div class="agent-info">
								<?php  $count = 0;
								foreach ($fields as $field) {
									if( isset( $field['is_default'] ) && isset( $field['is_disabled'] ) && ($field['is_disabled'] == 'yes') )
										continue;
									if( ++$count > 6 ) break; // 6 is the maximum fields;

									re_agent_display_field( $field, get_the_ID(), array( 'label_tag' => '' ) );
								}
								?>
							</div>
							<div class="agent-desc">
								<div class="agent-social">
									<?php 
									foreach ($socials as $social) {
										if( !isset( $all_socials[$social] ) ) continue;
										$value = get_post_meta( get_the_ID(), "{$prefix}_{$social}", true );
										echo ( !empty( $value ) ? '<a class="fa ' . $all_socials[$social]['icon'] . '" href="' . $value . '"></a>' : '' );
									}?>
								</div>
								<div class="agent-action">
									<a href="<?php the_permalink()?>"><?php the_title(); ?></a>
								</div>
							</div>
							
						</div>
					</div>
				</article> <!-- /#post- -->
			<?php endwhile; ?>
		</div>
	</div>
<?php endif;