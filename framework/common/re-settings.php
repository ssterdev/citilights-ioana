<?php

if( !function_exists( 're_setting_menu' ) ) :
	function re_setting_menu() {
		add_submenu_page(
			'edit.php?post_type=noo_property',
			__( 'Settings', 'noo' ),
			__( 'Settings', 'noo' ),
			'edit_theme_options',
			'manage_real_estate',
			're_setting_page' );
	}
	add_action( 'admin_menu', 're_setting_menu', 99 );

	if( !function_exists( 're_setting_page' ) ) :
		function re_setting_page() {
			$tabs = array();
			$tabs = apply_filters( 're_setting_tabs', $tabs );
			$tab_keys = array_keys( $tabs );
			$current_tab = empty( $_GET['tab'] ) ? reset( $tab_keys ) : sanitize_title( $_GET['tab'] );
			?>
			<div class="wrap">
				<form action="options.php" method="post">
					<h2 class="nav-tab-wrapper">
						<?php
							foreach ( $tabs as $name => $label )
								echo '<a href="' . re_setting_page_url($name) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
						?>
					</h2>
					<?php
					do_action( 're_setting_' . $current_tab );
					submit_button(__('Save Changes','noo'));
					?>
				</form>
			</div>
			<?php
		}
	endif;

	if( !function_exists( 're_setting_page_url' ) ) :
		function re_setting_page_url( $tab = '' ) {
			$args = array( 
					'post_type' => 'noo_property',
					'page' => 'manage_real_estate',
					'tab' => $tab
				);
			return esc_url( add_query_arg( $args, admin_url( 'edit.php' ) ) );
		}
	endif;
endif;
