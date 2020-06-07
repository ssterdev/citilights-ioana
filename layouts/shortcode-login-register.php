<?php 
$terms_conditions_url = re_get_agent_setting( 'terms_conditions' );
$terms_conditions_url = ( !empty($terms_conditions_url) ) ? get_permalink( $terms_conditions_url ) : '';

$noo_login_page_url   = re_get_agent_setting( 'noo_login_page' );
$noo_login_page_url   = ( !empty($noo_login_page_url) ) ? get_permalink( $noo_login_page_url ) : '';

$redirect_url         = re_get_agent_setting( 'noo_redirect_page' );
$redirect_url         = !empty($redirect_url) ? get_permalink( $redirect_url ) : home_url('/');

$forgot_pass_url      = noo_get_page_link_by_template( 'page-forgot-password.php' );
?>
<div class="noo-logreg <?php echo $mode; ?>">
	<div class="logreg-container">
		<div class="logreg-content row">
			<?php if( $mode == 'both' || $mode == 'login' ) : ?>
			<div class="login-form <?php echo $col_class; ?>">
				
				<form method="POST" action="<?php echo wp_login_url(); ?>" class="shortcode-login noo-box noo-box-login <?php echo $class_login_wrap = uniqid( 'class-login-wrap-' ); ?> show">

          <div class="logreg-title">
            <?php echo esc_html__( 'Login Form', 'noo' ); ?>
          </div>
          <div class="login_form">
            <div class="loginalert login_message_area"></div>
  					<?php if( !empty( $login_text ) ) : ?>
  						<p class="logreg-desc">
  							<?php echo $login_text; ?>
  						</p>
  					<?php endif; ?>
             <div class="noo-item user_log">
                 <input type="text" class="form-control" name="user_login" placeholder="<?php echo esc_html__( 'Username *', 'noo' ); ?>">
             </div>

            <div class="noo-item user_pass">
                <input type="password" class="form-control" name="user_password" placeholder="<?php echo esc_html__( 'Password *', 'noo' ); ?>">
                <input type="hidden" class="form-control" name="redirect_to" value="<?php echo esc_url($redirect_url);?>">
            </div>           
            

            <div class="noo-form-action">
        		  <button type="submit" class="btn btn-secondary btn-lg noo-login" data-class-wrap=".<?php echo esc_attr( $class_login_wrap ); ?>">
        			  <?php echo esc_html__( 'Login', 'noo' ); ?>
    			    </button>

        			<div class="forgot-password">
                <?php echo esc_html__( 'Lost your password?', 'noo' ) ?>
        				<a href="<?php echo esc_url( $forgot_pass_url ) ?>" class="open-forgot" title="<?php echo esc_html__( 'Click here to reset', 'noo' ); ?>" data-title="<?php echo esc_html__( 'Click here to reset', 'noo' ); ?>" >
                  		<?php echo esc_html__( 'Click here to reset', 'noo' ); ?>
              		</a>
        			</div>
            </div>
  		    </div><!-- end login div-->   
   	    </form><!-- /#noo-box-login -->
			</div>
			<?php endif; ?>

			<?php if( $mode == 'both' || $mode == 'register' ) : ?>
  			<div  class="register-form <?php echo $col_class; ?>">
  				<form method="POST" action="<?php echo wp_registration_url(); ?>" class="shortcode-login noo-box noo-box-register <?php echo $class_register_wrap = uniqid( 'class-register-wrap-' ); ?> show">
  					<div class="logreg-title"><?php _e('Register Form', 'noo'); ?></div>
  			      <div class="register_form">
               	<div class="loginalert register_message_area"></div>
  						  <?php if( !empty($register_text) ) : ?>
    							<p class="logreg-desc">
    								<?php echo $register_text; ?>
    							</p>
  						  <?php endif; ?>
  		          
                <div class="noo-item user_login">
  		            <input type="text" name="user_login" class="user_login form-control" placeholder="<?php echo esc_html__( 'Username *', 'noo' ); ?>">
  		          </div>
  		          <div class="noo-item user_email">
  		            <input type="text" name="user_email" class="user_email form-control" placeholder="<?php echo esc_html__( 'Your Email *', 'noo' ); ?>">
  		          </div>
  	            <div class="noo-item user_password">
  	              <input type="password" name="user_password" class="user_password form-control" placeholder="<?php echo esc_html__( 'Password', 'noo' ); ?>">
  	            </div>
              	<div class="noo-item user_password_retype">
                  <input type="password" name="user_password_retype" class="user_password_retype form-control" placeholder="<?php echo esc_html__( 'Retype Password', 'noo' ); ?>">
              	</div>
                      	
                <div class="noo-item-checkbox">
              		<input type="checkbox" name="terms" class="user_terms" data-id-register="<?php echo $id_register = uniqid( 'id_register' ); ?>" id="<?php echo $user_terms = uniqid( 'user_terms' ); ?>">
              		<label class="user_terms_register_label" for="<?php echo esc_attr( $user_terms ); ?>">
          					<?php echo noo_citilights_html_content( sprintf( __( 'I agree with <a href="%s" target="_blank" id="user_terms_topbar_link">terms &amp; conditions</a>', 'noo' ), $terms_conditions_url ) ); ?>
              		</label>
                  <input type="hidden" class="form-control" name="redirect_to" value="<?php echo esc_url($redirect_url);?>">
              	</div>
              	
                <?php // is Simple reCAPTCHA active?
  						    if ( function_exists( 'wpmsrc_display' ) ) { 
  							    echo wpmsrc_display();
  						    }
  						  ?>
     						<div class="noo-form-action">
                  <button type="submit" class="btn btn-secondary btn-lg noo-register" data-class-wrap=".<?php echo esc_attr( $class_register_wrap ); ?>" id="<?php echo esc_attr( $id_register ); ?>"  disabled="disabled"><?php echo esc_html__( 'Register Account', 'noo' ); ?></button>
                </div>
  					  </div>
          </form><!-- /#noo-box-register -->
  			</div>
			<?php endif; ?>
		</div>
	</div>
</div>