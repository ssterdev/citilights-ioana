<?php
if( !function_exists( 're_agent_admin_enqueue_scripts' ) ) :
	function re_agent_admin_enqueue_scripts( $hook ) {
		global $post;

		if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
			if ( RE_AGENT_POST_TYPE === $post->post_type || NooMembership::MEMBERSHIP_POST_TYPE === $post->post_type ) {
				wp_register_script( 'noo-agent-admin', NOO_FRAMEWORK_ADMIN_URI . '/assets/js/noo-agent-admin.js', null, null, true );
				wp_enqueue_script( 'noo-agent-admin' );
			}
		}
	}
	add_filter( 'admin_enqueue_scripts', 're_agent_admin_enqueue_scripts', 10, 2 );
endif;

if( !function_exists( 're_admin_agents_page_state' ) ) :
	function re_admin_agents_page_state( $states = array(), $post = null ) {
		if( !empty( $post ) && is_object( $post ) ) {
			$archive_slug = re_get_property_setting('noo_agent_archive_slug');
			if( !empty( $archive_slug ) && $archive_slug == $post->post_name ) {
				$states['property_page'] = __('Agents Page', 'noo');
			}
		}

		return $states;
	}
	add_filter( 'display_post_states', 're_admin_agents_page_state', 10, 2 );
endif;

if( !function_exists( 're_admin_agents_page_notice' ) ) :
	function re_admin_agents_page_notice( $post_type = '', $post = null ) {
		if( !empty( $post ) && is_object( $post ) ) {
			$archive_slug = re_get_property_setting('noo_agent_archive_slug');
			if ( !empty( $archive_slug ) && $archive_slug == $post->post_name && empty( $post->post_content ) ) {
				add_action( 'edit_form_after_title', '_re_admin_agents_page_notice' );
				remove_post_type_support( $post_type, 'editor' );
			}
		}
	}
	add_action( 'add_meta_boxes', 're_admin_agents_page_notice', 10, 2 );

	function _re_admin_agents_page_notice() {
		echo '<div class="notice notice-warning inline"><p>' . __( 'You are currently editing the page that shows all your agents.', 'noo' ) . '</p></div>';
	}
endif;

if( !function_exists( 're_save_post_admin_notices' ) ) :
	function re_save_post_admin_notices() {
		// If there are no errors, then we'll exit the function
		if ( ! ( $errors = get_transient( 'settings_errors' ) ) ) {
			return;
		}

		// Otherwise, build the list of errors that exist in the settings errores
		$message = '<div id="noo-save-post-message" class="error below-h2">';
		foreach ( $errors as $error ) {
			$message .= '<p>' . $error['message'] . '</p>';
		}
		$message .= '</div><!-- #error -->';

		// Write them out to the screen
		echo $message;

		// Clear and the transient and unhook any other notices so we don't see duplicate messages
		delete_transient( 'settings_errors' );
		remove_action( 'admin_notices', 're_save_post_admin_notices');
	}

	add_action('admin_notices', 're_save_post_admin_notices');
endif;