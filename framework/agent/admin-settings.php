<?php
if( !function_exists( 're_agent_setting_register' ) ) :
	function re_agent_setting_register() {
		//register our settings
		register_setting( 'noo_agent_settings', 'noo_agent_settings' );

		add_action( 're_setting_agent', 're_agent_setting_form' );
	}
	
	add_filter('admin_init', 're_agent_setting_register' );
endif;

if( !function_exists( 're_agent_setting_sub_menu' ) ) :
	function re_agent_setting_sub_menu( $tabs = array() ) {
		global $submenu;
		$permalink = re_setting_page_url('agent');
		$submenu['edit.php?post_type=' . RE_AGENT_POST_TYPE][] = array( __('Settings', 'noo'), 'edit_posts', $permalink );
	}
	
	add_filter('admin_menu', 're_agent_setting_sub_menu', 11 );
endif;

if( !function_exists( 're_agent_setting_tabs' ) ) :
	function re_agent_setting_tabs( $tabs = array() ) {
		$temp1 = array_slice($tabs, 0, 1);
		$temp2 = array_slice($tabs, 1);

		$resume_tab = array( 'agent' => __('Agents & Membership','noo') );
		return array_merge($temp1, $resume_tab, $temp2);
	}
	
	add_filter('re_setting_tabs', 're_agent_setting_tabs' );
endif;

if( !function_exists( 're_agent_setting_form' ) ) :
	function re_agent_setting_form() {
		if(isset($_GET['settings-updated']) && $_GET['settings-updated'])
		{
			flush_rewrite_rules();
		}
		?>
		<h3><?php _e('Agents &amp; Membership Settings', 'noo'); ?></h3>
		<?php settings_fields( 'noo_agent_settings' ); ?>
		<?php
			$noo_membership_type = NooMembership::get_membership_type();
		?>
		<table class="form-table">
			<tr valign="top" class="noo_agent_archive_slug">
				<th scope="row"><label for="noo_agent_archive_slug"><?php _e( 'Agent Archive base (slug)', 'noo' ); ?></label></th>
				<td>
					<input id="noo_agent_archive_slug" name="noo_agent_settings[noo_agent_archive_slug]" type="text" class="regular-text code" value="<?php echo esc_attr( re_get_agent_setting('noo_agent_archive_slug', '') ); ?>" placeholder="<?php echo _x('agents', 'slug', 'noo') ?>" />
					<p><small><?php echo sprintf( __( 'This option will affect the URL structure on your site. If you made change on it and see an 404 Error, you will have to go to <a href="%s" target="_blank">Permalink Settings</a><br/> and click "Save Changes" button for reseting WordPress link structure.', 'noo' ), admin_url( '/options-permalink.php' ) ); ?></small></p>
				</td>
			</tr>
			<tr valign="top" class="noo_agent_must_has_property">
				<th scope="row"><label for="noo_agent_must_has_property"><?php _e( 'Only Show Agent with Property', 'noo' ); ?></label></th>
				<td>
					<input name="noo_agent_settings[noo_agent_must_has_property]" type="hidden" value="" />
					<input id="noo_agent_must_has_property" name="noo_agent_settings[noo_agent_must_has_property]" type="checkbox" <?php checked( re_get_agent_setting('noo_agent_must_has_property'), '1' ); ?> value="1" />
					<p><small><?php _e( 'If selected, only agent with at least one property can be show on Agent listing.', 'noo' ); ?></small></p>
				</td>
			</tr>

		</table>
		<hr/>
		<table class="form-table">
			<tr valign="top" class="noo_membership_type">
				<th scope="row"><?php _e( 'Membership Type', 'noo' ); ?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><?php _e( 'Membership Type', 'noo' ); ?></legend>
						<label title="none">
							<input type="radio" name="noo_agent_settings[noo_membership_type]" value="none" <?php checked( $noo_membership_type, 'none'); ?>>
							<span><?php _e( 'No Membership (Agents created by Admin can still submit Property)', 'noo'); ?></span>
						</label>
						<br/>
						<label title="free">
							<input type="radio" name="noo_agent_settings[noo_membership_type]" value="free" <?php checked( $noo_membership_type, 'free'); ?>>
							<span><?php _e( 'Free for all Users', 'noo'); ?></span>
						</label>
						<br/>
						<label title="membership">
							<input type="radio" name="noo_agent_settings[noo_membership_type]" value="membership" <?php checked( $noo_membership_type, 'membership'); ?>>
							<span><?php _e( 'Membership Packages', 'noo'); ?></span>
						</label>
						<br/>
						<label title="submission">
							<input type="radio" name="noo_agent_settings[noo_membership_type]" value="submission" <?php checked( $noo_membership_type, 'submission'); ?>>
							<span><?php _e( 'Pay per Submission', 'noo'); ?></span>
						</label>
						<br/>
					</fieldset>
				</td>
			</tr>
			<tr valign="top" class="noo_membership_type-child noo_membership_type-membership-child noo_membership_freemium">
				<th scope="row"><label for="noo_membership_freemium"><?php _e( 'Enable Freemium Membership', 'noo' ); ?></label></th>
				<td><input type="checkbox" name="noo_agent_settings[noo_membership_freemium]" <?php checked( re_get_agent_setting('noo_membership_freemium', false) );?> value="1" /></td>
			</tr>
			<tr valign="top" class="noo_membership_freemium-child">
				<th scope="row"><label for="noo_membership_freemium_listing_num"><?php _e( 'Number of Free Listing', 'noo' ); ?></label></th>
				<td>
					<input type="text" name="noo_agent_settings[noo_membership_freemium_listing_num]" value="<?php echo esc_attr( re_get_agent_setting('noo_membership_freemium_listing_num', '1') ); ?>" <?php disabled( re_get_agent_setting('noo_membership_freemium_listing_unlimited', false) ); ?> />
					<input type="checkbox" name="noo_agent_settings[noo_membership_freemium_listing_unlimited]" <?php checked( re_get_agent_setting('noo_membership_freemium_listing_unlimited', false) ); ?> value="1" />
					<label for="noo_membership_freemium_listing_unlimited"><?php _e( 'Unlimited Listing?', 'noo' ); ?></label>
				</td>
			</tr>
			<tr valign="top" class="noo_membership_freemium-child">
				<th scope="row"><label for="noo_membership_freemium_featured_num"><?php _e( 'Number of Free Featured Properties', 'noo' ); ?></label></th>
				<td>
					<input type="text" name="noo_agent_settings[noo_membership_freemium_featured_num]" value="<?php echo esc_attr( re_get_agent_setting('noo_membership_freemium_featured_num', '0') ); ?>" />
				</td>
			</tr>
			<tr valign="top" class="noo_membership_type-child noo_membership_type-membership-child">
				<th scope="row"><label for="noo_membership_page"><?php _e( 'Membership listing page (Page with pricing table)', 'noo' ); ?></label></th>
				<td>
					<?php wp_dropdown_pages(
						array(
						'name'              => 'noo_agent_settings[noo_membership_page]',
						'echo'              => 1,
						'show_option_none'  => ' ',
						'option_none_value' => '',
						'selected'          => re_get_agent_setting('noo_membership_page', ''),
						)
					);
					?>
				</td>
			</tr>
			<tr valign="top" class="noo_membership_type-child noo_membership_type-submission-child">
				<th scope="row"><label for="noo_submission_listing_price"><?php _e( 'Price per Submission', 'noo' ); ?></label></th>
				<td>
					<input type="text" name="noo_agent_settings[noo_submission_listing_price]" value="<?php echo esc_attr( re_get_agent_setting('noo_submission_listing_price', '20.00') ); ?>" />
				</td>
			</tr>
			<tr valign="top" class="noo_membership_type-child noo_membership_type-submission-child">
				<th scope="row"><label for="noo_submission_featured_price"><?php _e( 'Price for Featured Property', 'noo' ); ?></label></th>
				<td>
					<input type="text" name="noo_agent_settings[noo_submission_featured_price]" value="<?php echo esc_attr( re_get_agent_setting('noo_submission_featured_price', '20.00') ); ?>" />
				</td>
			</tr>
			<tr valign="top" class="noo_membership_type-child noo_membership_type-onetime-child">
				<th scope="row"><label for="noo_onetime_price"><?php _e( 'Membership Price (onetime)', 'noo' ); ?></label></th>
				<td>
					<input type="text" name="noo_agent_settings[noo_onetime_price]" value="<?php echo esc_attr( re_get_agent_setting('noo_onetime_price', '20.00') ); ?>" />
				</td>
			</tr>
			<tr valign="top" class="noo_membership_type-child noo_membership_type-free-child noo_membership_type-membership-child noo_membership_type-submission-child noo_membership_type-onetime-child">
				<?php $noo_admin_approve = re_get_agent_setting('noo_admin_approve', 'add'); ?>
				<th scope="row"><label for="noo_admin_approve"><?php _e( 'Submitted Properties need approve from admin?', 'noo' ); ?></label></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><?php _e( 'Submitted Properties need approve from admin?', 'noo' ); ?></legend>
						<label title="all">
							<input type="radio" name="noo_agent_settings[noo_admin_approve]" value="all" <?php checked( $noo_admin_approve, 'all'); ?>>
							<span><?php _e( 'Yes, all newly added and edited properties', 'noo'); ?></span>
						</label>
						<br/>
						<label title="add">
							<input type="radio" name="noo_agent_settings[noo_admin_approve]" value="add" <?php checked( $noo_admin_approve, 'add'); ?>>
							<span><?php _e( 'Yes, but only newly submitted properties', 'noo'); ?></span>
						</label>
						<br/>
						<label title="none">
							<input type="radio" name="noo_agent_settings[noo_admin_approve]" value="none" <?php checked( $noo_admin_approve, 'none'); ?>>
							<span><?php _e( 'Don\'t need Admin approval', 'noo'); ?></span>
						</label>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php if( re_get_agent_setting('users_can_register', true) ) : ?>
		<hr/>
		<table class="form-table noo_membership_type-child noo_membership_type-free-child noo_membership_type-membership-child noo_membership_type-submission-child noo_membership_type-onetime-child">
			<tr valign="top">
				<th scope="row"><label for="noo_login_page"><?php _e( 'Custom Login Page', 'noo' ); ?></label></th>
				<td>
					<?php wp_dropdown_pages(
						array(
						'name'              => 'noo_agent_settings[noo_login_page]',
						'echo'              => 1,
						'show_option_none'  => ' ',
						'option_none_value' => '',
						'selected'          => re_get_agent_setting('noo_login_page', ''),
						)
					);
					?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="noo_redirect_page"><?php _e( 'Redirect Page', 'noo' ); ?></label></th>
				<td>
					<?php wp_dropdown_pages(
						array(
						'name'              => 'noo_agent_settings[noo_redirect_page]',
						'echo'              => 1,
						'show_option_none'  => ' ',
						'option_none_value' => '',
						'selected'          => re_get_agent_setting('noo_redirect_page', ''),
						)
					);
					?>
				</td>
			</tr>
		</table>
		<hr/>
		<table class="form-table noo_membership_type-child noo_membership_type-free-child noo_membership_type-membership-child noo_membership_type-submission-child noo_membership_type-onetime-child">
			<tr valign="top">
				<th scope="row">
					<label for="terms_conditions">
						<?php _e( 'Terms & Conditions Page', 'noo' ); ?>
					</label>
				</th>
				<td>
					<?php wp_dropdown_pages(
						array(
						'name'              => 'noo_agent_settings[terms_conditions]',
						'echo'              => 1,
						'show_option_none'  => ' ',
						'option_none_value' => '',
						'selected'          => re_get_agent_setting('terms_conditions', ''),
						)
					);
					?>
				</td>
			</tr>
		</table>
		<?php else : ?>
		<h3 class="noo_membership_type-child noo_membership_type-free-child noo_membership_type-membership-child noo_membership_type-submission-child noo_membership_type-onetime-child"><?php echo sprintf( __( 'Registration is not enable on this site. Go to %s to Enable it.', 'noo' ), '<a href="' . admin_url('options-general.php') . '">' . __( 'General Setting', 'noo' ) . '</a>' ); ?></h3>
		<?php endif; ?>
		<script>
			jQuery( document ).ready( function ( $ ) {
				var $membership_type = $( '.noo_membership_type' );
				$membership_type.bind('toggle_children', function() {
					$this = $(this);
					if(!$this.is(':visible')) {
						$('.noo_membership_type-child').hide().trigger("toggle_children");

						return;
					}

					var value = $this.find( 'input[name="noo_agent_settings[noo_membership_type]"]:checked' ).val();
					$('.noo_membership_type-child').hide().trigger('toggle_children');
					$('.noo_membership_type-' + value + '-child').show().trigger('toggle_children');
				});

				$membership_type.trigger('toggle_children');
				$membership_type.find('input').click( function() {
					$membership_type.trigger("toggle_children");
				});

				var $membership_freemium = $( '.noo_membership_freemium' );
				$membership_freemium.bind('toggle_children', function(){
					$this = $(this);
					if(!$this.is(':visible')) {
						$('.noo_membership_freemium-child').hide().trigger("toggle_children");

						return;
					}

					var value = $this.find( 'input[type=checkbox]' ).is(':checked');
					if( value ) {
						$('.noo_membership_freemium-child').show().trigger('toggle_children');
					} else {
						$('.noo_membership_freemium-child').hide().trigger('toggle_children');
					}
				});

				$membership_freemium.trigger('toggle_children');
				$membership_freemium.find('input').click( function() {
					$membership_freemium.trigger("toggle_children");
				});

				$('input[name="noo_agent_settings[noo_membership_freemium_listing_unlimited]"]').click( function() {
					if( $(this).is(':checked') ) {
						$('input[name="noo_agent_settings[noo_membership_freemium_listing_num]"]').prop('disabled', true);
					} else {
						$('input[name="noo_agent_settings[noo_membership_freemium_listing_num]"]').prop('disabled', false);
					}
				});

			} );
		</script>
		<?php
	}
endif;
