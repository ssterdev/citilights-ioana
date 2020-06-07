<?php 
// Variables
$prefix = '_noo_agent';

$avatar_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
if( empty($avatar_src) ) {
	$avatar_src		= NooAgent::get_default_avatar_uri();
} else {
	$avatar_src		= $avatar_src[0];
}

$prefix = RE_AGENT_META_PREFIX;
$fields = re_get_agent_custom_fields();
$all_socials = noo_get_social_fields();
$socials = re_get_agent_socials();

?>

<?php get_header(); ?>
<div class="container-wrap">
	<div class="main-content container-boxed max offset">
		<div class="row">
			<div class="<?php noo_main_class(); ?>" role="main">
				<?php while ( have_posts() ) : the_post(); ?>
					<article id="post-<?php the_ID(); ?>" class="noo-agent">
						<h1 class="content-title agent-name">
							<?php the_title(); ?>
							<?php if( !empty($position) ) : ?>
								<small class="agent-position"><?php echo $position; ?></small>
							<?php endif; ?>
						</h1>
						<div class="agent-social clearfix">
							<?php 
								foreach ($socials as $social) {
									if( !isset( $all_socials[$social] ) ) continue;
									$value = noo_get_post_meta( get_the_ID(), "{$prefix}_{$social}", '' );
									echo ( !empty( $value ) ? '<a class="fa ' . $all_socials[$social]['icon'] . '" href="' . $value . '"></a>' : '' );
								}?>
						</div>
						<div class="agent-info">
							<div class="content-featured">
						        <div class="content-thumb">
						        	<img src="<?php echo $avatar_src; ?>" alt="<?php the_title(); ?>"/>
						        </div>
						    </div>
							<div class="agent-detail">
								<h4 class="agent-detail-title"><?php _e('Contact Info','noo')?></h4>
								<div class="agent-detail-info">
									<?php
									foreach ($fields as $field) {
										re_agent_display_field( $field, get_the_ID() );
									}
									?>
								</div>
								<div class="agent-desc">
									<h4 class="agent-detail-title"><?php _e('About Me','noo')?></h4>
									<?php the_content();?>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="conact-agent row">
							<h2 class="content-title col-md-12">
								<?php _e('Contact Me','noo')?>
							</h2>
							<?php
								$cf7_id = re_get_property_contact_setting('agent_contact_form');
								$contact_form = !empty( $cf7_id ) ? wpcf7_contact_form( $cf7_id ) : false;
							?>
							<?php if( $contact_form ) : ?>
								<?php
									$atts = array(
									'id' => $cf7_id,
									'title' => '',
									'html_id' => '',
									'html_name' => '',
									'html_class' => '',
									'output' => 'form' );
									$form_html = $contact_form->form_html( $atts );

									$hidden_fields[] = '<input type="hidden" name="_wpcf7_agent_id" value="' . get_the_ID() . '">';
									// $hidden_fields[] = '<input type="hidden" name="_wpcf7_property_id" value="">';

									$form_html = str_replace('</form></div>', implode('', $hidden_fields) . '</form></div>', $form_html);

									echo $form_html;
								?>

							<?php else : ?>
								<form role="form" id="conactagentform" method="post">
									<div style="display: none;">
										<input type="hidden" name="action" value="noo_contact_agent">
										<input type="hidden" name="agent_id" value="<?php echo get_the_ID()?>">
										<input type="hidden" name="security" value="<?php echo wp_create_nonce('noo-contact-agent-'.get_the_ID())?>">
									</div>
									<?php do_action('before_noo_agent_contact_form')?>
									<?php do_action( 'noo_agent_contact_form_before_fields' ); ?>
									<?php 
									$recaptcha  = re_get_property_contact_setting('recaptcha',false);
									if ( $recaptcha == 1 ) {
										$keysite =re_get_property_contact_setting('key_recaptcha');
									}
									$fields = array(
										'name'=>'<div class="form-group col-md-6 col-sm-6"><input type="text" name="name" class="form-control addclass" placeholder="'.__('Your Name *','noo').'"></div>',
										'email'=>'<div class="form-group  col-md-6 col-sm-6"><input type="email" name="email" class="form-control addclass" placeholder="'.__('Your Email *','noo').'"></div>',
										'message'=>'<div class="form-group message col-md-12 col-sm-12"><textarea name="message" class="form-control addclass" rows="5" placeholder="'.__('Message *','noo').'"></textarea></div>',
										'recaptcha' =>'<div class="form-group col-md-12 col-sm-12"><div class="g-recaptcha addclass" data-sitekey="'.$keysite.'"></div></div>',
									);
									$fields = apply_filters( 'noo_agent_contact_form_default_fields', $fields );
									foreach ($fields as $field):
										echo $field;
									endforeach;
									do_action( 'noo_agent_contact_form_after_fields' );
									?>
									<script src="https://www.google.com/recaptcha/api.js" async defer></script>
									<div class="form-action col-md-12 col-sm-12">
										<img class="ajax-loader" src="<?php echo NOO_ASSETS_URI ?>/images/ajax-loader.gif" alt="<?php _e('Sending ...','noo')?>" style="visibility: hidden;">
										<button type="submit" class="btn btn-default"><?php _e('Send Me','noo')?></button>
									</div>
									<?php do_action('before_noo_agent_contact_form')?>
								</form>
							<?php endif; ?>
						</div>
						<div class="agent-properties" data-agent-id="<?php the_ID()?>">
							<?php
							$args = array(
									'paged'=> 1,
									'posts_per_page' => 4,
									'post_type'=>'noo_property',
									'meta_query' => array(
										array(
											'key' => '_agent_responsible',
											'value' => get_the_ID(),
										),
									),
							);
							$args = apply_filters('noo_agent_property_query', $args);
							$r = new WP_Query($args);
							$loop_args = array(
								'query' => $r,
								'title' => __('My Properties','noo'),
								'display_mode' => true,
								'show_pagination' => false,
								'ajax_pagination' => true
							);
							re_property_loop($loop_args);
							wp_reset_query();
							wp_reset_postdata();
							?>
						</div>
					</article> <!-- /#post- -->
				<?php endwhile; ?>
			</div> <!-- /.noo_main_class() -->
			<?php get_sidebar(); ?>
		</div> <!-- /.row -->
	</div> <!-- /.container-boxed.max.offset -->
</div><!--/.container-wrap-->
<?php get_footer(); ?>