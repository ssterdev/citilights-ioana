<?php
/*
Template Name: Agent Dashboard Profile
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
$title  = '';
$agent  = empty( $agent_id ) ? '' : get_post($agent_id);
$avatar = '';
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

get_header(); ?>

<div class="container-wrap">
	<div class="main-content container-boxed max offset">
		<div class="row">
			<div class="noo-sidebar col-md-4">
				<div class="noo-sidebar-wrapper">
				<?php get_template_part( 'layouts/' . 'agent_menu' );  ?>
				</div>
			</div>
			<div class="<?php noo_main_class(); ?> col-sm-12" role="main">   
				<div class="submit-header">
					<h1 class="page-title"><?php _e('My Profile', 'noo'); ?></h1>
				</div>
				<div class="submit-content">
					<form id="profile_form" name="profile_form" class="noo-form profile-form" role="form">
						<div class="noo-control-group">
							<div class="group-title">
								<?php echo sprintf( __('Welcome back, %s', 'noo'), $title ); ?>
							</div>
							<div class="group-container row">
								<div class="form-message">
								</div>
								<div class="col-md-12">
									<div class="form-group s-profile-title">
										<label for="title"><?php _e('Name','noo'); ?>&nbsp;*</label>
										<input type="text" id="title" class="form-control" value="<?php echo $title; ?>" name="title" required />
									</div>
								</div>
								<div class="col-md-6">
									<?php if(!empty($fields)) : ?>
										<?php foreach ($fields as $field) {
											re_agent_render_form_field( $field, $agent_id );
										} ?>
									<?php endif; ?>
								</div>
								<div class="col-md-6">
									<div class="form-group s-profile-desc">
										<label for="desc"><?php _e('About me','noo'); ?></label>
										<textarea id="desc" class="form-control" name="desc" rows="8"><?php echo $desc; ?></textarea>
									</div>
									<div id="upload-container">
										<label><?php _e('Avatar','noo'); ?></label>
										<div id="aaiu-upload-container" class="row">
											<div class="col-md-6">
											<?php noo_upload_form( $avatar ); ?>
											</div>
											<div class="col-md-6">
												<p><?php _e('Recommended size: 370x500','noo');?></p>
												<a id="aaiu-uploader" class="btn btn-secondary btn-lg" href="#"><?php _e('Choose Image','noo');?></a>
											</div>
											<input type="hidden" name="avatar" id="avatar" value="<?php echo $avatar;?>">
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php if(!empty($socials)) : ?>
							<div class="noo-control-group">
								<div class="group-title">
									<?php _e('Social Network', 'noo'); ?>
								</div>
								<div class="group-container row">
									<?php foreach ($socials as $social) : ?>
										<div class="col-md-6">
											<?php re_agent_render_social_field( $social, $agent_id ); ?>
										</div>
									<?php endforeach; ?>
									<div class="col-md-12">
										<div class="noo-submit">
											<input type="submit" class="btn btn-primary btn-lg" id="profile_submit" value="<?php _e('Update', 'noo'); ?>" />
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>
						<input type="hidden" name="action" value="noo_ajax_update_profile">
						<input type="hidden" name="agent_id" value="<?php echo $agent_id;?>">
						<input type="hidden" name="user_id" value="<?php echo $user_id;?>">
						<?php wp_nonce_field('submit_profile','_noo_profile_nonce'); ?>
					</form>
					<form id="password_form" name="password_form" class="noo-form profile-form" role="form">
						<div class="noo-control-group">
							<div class="group-title">
								<?php _e('Change Password', 'noo'); ?>
							</div>
							<div class="group-container row">
								<div class="form-message">
								</div>
								<div class="col-md-6">
									<div class="form-group s-profile-old_pass">
										<label for="old_pass"><?php _e('Old Password','noo'); ?></label>
										<input type="password" id="old_pass" class="form-control" value="" name="old_pass" />
									</div>
									<div class="form-group s-profile-new_pass">
										<label for="new_pass"><?php _e('New Password','noo'); ?></label>
										<input type="password" id="new_pass" class="form-control" value="" name="new_pass" />
									</div>
									<div class="form-group s-profile-new_pass_confirm">
										<label for="new_pass_confirm"><?php _e('Confirm New Password','noo'); ?></label>
										<input type="password" id="new_pass_confirm" class="form-control" value="" name="new_pass_confirm" />
									</div>
								</div>
								<div class="col-md-12">
									<div class="noo-submit">
										<input type="submit" class="btn btn-primary btn-lg" id="password_submit" value="<?php _e('Change Password', 'noo'); ?>" />
									</div>
								</div>
							</div>
						</div>
						<input type="hidden" name="action" value="noo_ajax_change_password"/>
						<input type="hidden" name="user_id" value="<?php echo $user_id;?>">
						<?php wp_nonce_field('submit_profile_password','_noo_profile_password_nonce'); ?>
					</form>
				</div><!-- /.submit-content -->
			</div> <!-- /.main -->
		</div><!--/.row-->
	</div><!--/.container-boxed-->
</div><!--/.container-wrap-->  
<?php get_footer(); ?>