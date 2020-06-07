<?php
if($agent = get_post($agent_id)) :
	// Variables
	$prefix = RE_AGENT_META_PREFIX;
	$fields = re_get_agent_custom_fields();
	$all_socials = noo_get_social_fields();
	$socials = re_get_agent_socials();

	$avatar_src = wp_get_attachment_image_src( get_post_thumbnail_id( $agent->ID ), 'full' );
	if( empty($avatar_src) ) {
		$avatar_src		= NooAgent::get_default_avatar_uri();
	} else {
		$avatar_src		= $avatar_src[0];
	}
	?>
	<div class="agent-property">
		<div class="agent-property-title">
			<h3><?php echo __('Contact Agent','noo')?></h3>
		</div>
		<div class="agents grid container-fluid">
			<div class="row agents-list">
				<div <?php post_class( 'col-md-6 col-xs-6', $agent->ID ); ?>>
				    <div class="agent-featured hidden-print">
				        <a class="content-thumb" href="<?php the_permalink($agent->ID) ?>">
							<img src="<?php echo $avatar_src; ?>" alt="<?php echo get_the_title($agent->ID); ?>"/>
						</a>
				    </div>
					<div class="agent-wrap">
						<div class="agent-summary">
							<div class="agent-info">
								<?php  $count = 0;
								foreach ($fields as $field) {
									if( isset( $field['is_default'] ) && isset( $field['is_disabled'] ) && ($field['is_disabled'] == 'yes') )
										continue;
									if( ++$count > 7 ) break; // 6 is the maximum fields;

									re_agent_display_field( $field, $agent->ID, array( 'label_tag' => '' ) );
								}
								?>
							</div>
							<div class="agent-desc">
								<div class="agent-social hidden-print">
									<?php 
									foreach ($socials as $social) {
										if( !isset( $all_socials[$social] ) ) continue;
										$value = get_post_meta( $agent->ID, "{$prefix}_{$social}", true );
										echo ( !empty( $value ) ? '<a class="fa ' . $all_socials[$social]['icon'] . '" href="' . $value . '"></a>' : '' );
									}?>
								</div>
								<div class="agent-action">
									<a href="<?php echo get_permalink($agent->ID)?>">
										<?php echo get_the_title($agent->ID); ?>
									</a>
								</div>
							</div>
							
						</div>
					</div>
				</div> <!-- /#post- -->
				<div class="conact-agent col-xs-6 hidden-print">
					<?php
						$cf7_id = re_get_property_contact_setting('property_contact_form');
						$cf7_id = apply_filters( 'wpml_object_id', $cf7_id, 'wpcf7_contact_form' );
						
						do_action( 'wpml_register_single_string', 'Noo Contact Form', 'Property Contact Form', $cf7_id );
						$cf7_id = apply_filters( 'wpml_translate_single_string', $cf7_id, 'Noo Contact Form', 'Property Contact Form', apply_filters( 'wpml_current_language', null ) );

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

							$hidden_fields[] = '<input type="hidden" name="_wpcf7_agent_id" value="' . $agent->ID . '">';
							$hidden_fields[] = '<input type="hidden" name="_wpcf7_property_id" value="' . $property_id . '">';

							$form_html = str_replace('</form></div>', implode('', $hidden_fields) . '</form></div>', $form_html);

							echo $form_html;
						?>

					<?php else : ?>
						<form role="form" id="conactagentform" method="post">
							<div style="display: none;">
								<input type="hidden" name="action" value="noo_contact_agent_property">
								<input type="hidden" name="agent_id" value="<?php echo $agent->ID?>">
								<input type="hidden" name="property_id" value="<?php echo $property_id?>">
								<input type="hidden" name="security" value="<?php echo wp_create_nonce('noo-contact-agent-'.$agent->ID)?>">
							</div>
							<?php do_action('before_noo_agent_contact_form')?>
							<?php do_action( 'noo_agent_contact_form_before_fields' ); ?>
							<?php 
							$keysite 	= '';
							$recaptcha  = re_get_property_contact_setting('recaptcha',false);
							if ( $recaptcha == 1 ) {
								$keysite =re_get_property_contact_setting('key_recaptcha');

								$fields = array(
									'name'=>'<div class="form-group"><input type="text" name="name" class="form-control addclass" placeholder="'.__('Your Name *','noo').'"></div>',
									'email'=>'<div class="form-group"><input type="email" name="email" class="form-control addclass" placeholder="'.__('Your Email *','noo').'"></div>',
									'message'=>'<div class="form-group"><textarea name="message" class="form-control addclass" rows="5" placeholder="'.__('Message *','noo').'"></textarea></div>',
									'recaptcha' =>'<div class="form-group"><div class="g-recaptcha addclass" data-sitekey="'.$keysite.'"></div></div>'
								);
							}
							else{
								$fields = array(
									'name'=>'<div class="form-group"><input type="text" name="name" class="form-control addclass" placeholder="'.__('Your Name *','noo').'"></div>',
									'email'=>'<div class="form-group"><input type="email" name="email" class="form-control addclass" placeholder="'.__('Your Email *','noo').'"></div>',
									'message'=>'<div class="form-group"><textarea name="message" class="form-control addclass" rows="5" placeholder="'.__('Message *','noo').'"></textarea></div>',
								);
							}
							$fields = apply_filters( 'noo_property_agent_contact_form_default_fields', $fields );
							foreach ($fields as $field):
								echo $field;
							endforeach;
							do_action( 'noo_agent_contact_form_after_fields' );
							?>
							<script src="https://www.google.com/recaptcha/api.js" async defer></script>
							
							<div class="form-action col-md-12 col-sm-12">
								<img class="ajax-loader" src="<?php echo NOO_ASSETS_URI ?>/images/ajax-loader.gif" alt="<?php _e('Sending ...','noo')?>" style="visibility: hidden;">
								<button type="submit" class="btn btn-default"><?php _e('Send a Message','noo')?></button>
							</div>
							<?php do_action('after_noo_agent_contact_form')?>
						</form>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
<?php
class GoogleRecaptcha 
{
    /* Google recaptcha API url */
    private $google_url = "https://www.google.com/recaptcha/api/siteverify";
    private $secret = '6LcoRm0UAAAAAMz9i3SDxLuKwb43QCwORAQijtE0';
  
    public function VerifyCaptcha($response)
    {
        $url = $this->google_url."?secret=".$this->secret.
               "&response=".$response;
  
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, TRUE); 
        $curlData = curl_exec($curl);
  
        curl_close($curl);
  
        $res = json_decode($curlData, TRUE);
        if($res['success'] == 'true') 
            return TRUE;
        else
            return FALSE;
    }
  
}
  
$message = 'Google reCaptcha';
  
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $response = $_POST['g-recaptcha-response'];
  
    if(!empty($response))
    {
          $cap = new GoogleRecaptcha();
          $verified = $cap->VerifyCaptcha($response);
  
          if($verified) {
            $message = "Captcha Success!";
          } else {
            $message = "Please reenter captcha";
          }
    }
}
?>