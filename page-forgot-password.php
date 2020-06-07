<?php
/*
Template Name: Forgot Password
*/
?>
<?php get_header(); ?>
<div class="container-wrap">	
	<div class="main-content container">
		<div class="row">
			<div class="<?php noo_main_class(); ?>" role="main">
				<!-- Begin The loop -->
				<?php if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : the_post(); ?>

						<form method="POST" class="noo-box-forgot">

							<h1 class="noo-title"><?php echo esc_html__( 'Forgot Password', 'noo' ) ?></h1>

                        	<?php
                        	if ( isset( $_POST['forgot-password'] ) ) :

                        		$user_login    = !empty( $_POST['user_forgot'] ) ? sanitize_text_field( $_POST['user_forgot'] ) : '';

								if ( !empty( $user_login ) ) :

									if( ! is_email( $user_login ) ) :

										echo '<div class="error">' . esc_html__( 'Invalid username or e-mail address.', 'noo' ) . '</div>';

							        elseif( ! email_exists( $user_login ) ) :

										echo '<div class="success">' . esc_html__( 'There is no user registered with that email address.', 'noo' ) . '</div>';

							        else :
							        
							            /**
							             * lets generate our new password
							             */
							           		$random_password = wp_generate_password( 12, false );
							            
							            /**
							             * Get user data by field and data, other field are ID, slug, slug and login
							             */
							            	$user = get_user_by( 'email', $user_login );
							            
								            $update_user = wp_update_user( 
								            	array (
													'ID'        => $user->ID, 
													'user_pass' => $random_password
								                )
								            );
							            
							            /**
							             * if update user return true then lets send user an email containing the new password
							             */
							            if ( $update_user ) :

											$to      = $user_login;
											$subject = esc_html__( 'Your new password', 'noo' );
											$sender  = get_option('name');
							                
							                $message = esc_html__( 'Your new password is: ' . $random_password , 'noo' );
							                
							                $headers[] = 'MIME-Version: 1.0' . "\r\n";
							                $headers[] = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
							                $headers[] = "X-Mailer: PHP \r\n";
							                $headers[] = 'From: ' . $sender . ' < ' . $user_login . '>' . "\r\n";
							                
							                $mail = wp_mail( $to, $subject, $message, $headers );
							                
							                if ( $mail ) :
								             
								                echo esc_html__( 'Check your email address for you new password.', 'noo' );
											
											endif;
							                    
							            else :

							                echo '<div class="error">' . esc_html__( 'Oops something went wrong updaing your account.', 'noo' ) . '</div>';

							            endif;

							        endif;

								else :

							        echo '<div class="error">' . esc_html__( 'Enter a username or e-mail address..', 'noo' ) . '</div>';
								    
								endif;

							endif;

                        	?>

                        	<div class="forgot_form">

			               		<div class="notice">
			               			<?php echo esc_html__( 'Forgot your password ? Thank you enter your username or email address. You will receive a link to create a new password', 'noo' ); ?>
			               		</div>
               
				                <div class="noo-item user_forgot">
				                    <input type="text" name="user_forgot" class="form-control" placeholder="<?php echo esc_html__( 'Enter Username or Email', 'noo' ); ?>">
				                </div>

				                <div class="noo-form-action">

				                    <button type="submit" name="forgot-password">
				                    	<?php echo esc_html__( 'Reset Password', 'noo' ); ?>
			                    	</button>

		                        </div>

                        </form><!-- /.noo-box-forgot -->
						<?php the_content(); ?>
					<?php endwhile; ?>
				<?php endif; ?>
				<!-- End The loop -->
			</div> <!-- /.main -->
		</div><!--/.row-->
	</div><!--/.container-full-->
</div><!--/.container-wrap-->
	
<?php get_footer(); ?>