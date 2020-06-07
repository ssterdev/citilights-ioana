<?php
/**
 * Theme functions for NOO Framework.
 * This file include the framework functions, it should remain intact between themes.
 * For theme specified functions, see file functions-<theme name>.php
 *
 * @package    NOO Framework
 * @version    1.0.0
 * @author     Kan Nguyen <khanhnq@nootheme.com>
 * @copyright  Copyright (c) 2014, NooTheme
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       http://nootheme.com
 */

if( !function_exists('is_plugin_active') )
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Set global constance
define( 'NOO_FRAMEWORK', get_template_directory() . '/framework' );
define( 'NOO_FRAMEWORK_ADMIN', NOO_FRAMEWORK . '/admin' );
define( 'NOO_FRAMEWORK_FUNCTION', NOO_FRAMEWORK . '/functions' );
define( 'NOO_FRAMEWORK_VENDOR', NOO_FRAMEWORK . '/vendor' );
define( 'NOO_FRAMEWORK_URI', get_template_directory_uri() . '/framework' );
define( 'NOO_FRAMEWORK_ADMIN_URI', NOO_FRAMEWORK_URI . '/admin' );
define('NOO_SUPPORT_TRAINER', true);
if ( !defined( 'NOO_ASSETS' ) ) {
	define( 'NOO_ASSETS', get_template_directory() . '/assets' );
}

if ( !defined( 'NOO_ASSETS_URI' ) ) {
	define( 'NOO_ASSETS_URI', get_template_directory_uri() . '/assets' );
}

define( 'NOO_WOOCOMMERCE_EXIST', is_plugin_active( 'woocommerce/woocommerce.php' ) );

if ( !defined( 'NOO_SUPPORT_PORTFOLIO' ) ) {
	define( 'NOO_SUPPORT_PORTFOLIO', false );
}

if( !function_exists('is_plugin_active') ) require_once ABSPATH . 'wp-admin/includes/plugin.php';

// Functions for specific theme
$theme_name = 'custom';
if ( file_exists( get_template_directory() . '/functions_' . $theme_name . '.php' ) ) {
	require_once get_template_directory() . '/functions_' . $theme_name . '.php';
}

//
// Helper functions.
//
require_once NOO_FRAMEWORK_FUNCTION . '/noo-theme.php';
require_once NOO_FRAMEWORK_FUNCTION . '/noo-utilities.php';
require_once NOO_FRAMEWORK_FUNCTION . '/noo-html.php';
require_once NOO_FRAMEWORK_FUNCTION . '/noo-style.php';
require_once NOO_FRAMEWORK_FUNCTION . '/noo-wp-style.php';
require_once NOO_FRAMEWORK_FUNCTION . '/noo-css.php';

require_once NOO_FRAMEWORK_FUNCTION . '/noo-user.php';

require_once NOO_FRAMEWORK_FUNCTION . '/noo-ajax-upload.php';
require_once NOO_FRAMEWORK_FUNCTION . '/noo-upload.php';

require_once NOO_FRAMEWORK_ADMIN . '/noo-setup-install.php';

//
// Enqueue assets
//
require_once NOO_FRAMEWORK_FUNCTION . '/noo-enqueue-css.php';
require_once NOO_FRAMEWORK_FUNCTION . '/noo-enqueue-js.php';

//
// Admin panel
//


// Initialize theme
require_once NOO_FRAMEWORK_ADMIN . '/_init.php';

// Initialize NOO Customizer
require_once NOO_FRAMEWORK_ADMIN . '/customizer/_init.php';

// WooCommerce
if( NOO_WOOCOMMERCE_EXIST ) {
	require_once NOO_FRAMEWORK_FUNCTION . '/woocommerce.php';
}

// Initialize CitiLights function
require_once NOO_FRAMEWORK . '/common/loader.php';
// require_once NOO_FRAMEWORK_ADMIN . '/noo-agent.php';
// require_once NOO_FRAMEWORK_ADMIN . '/noo-property.php';
require_once NOO_FRAMEWORK_ADMIN . '/noo-membership.php';
require_once NOO_FRAMEWORK_ADMIN . '/noo-testimonial.php';

require_once NOO_FRAMEWORK_FUNCTION . '/class-paypal-framework.php';
require_once NOO_FRAMEWORK_ADMIN . '/noo-payment.php';

// Initialize NOO Shortcodes
require_once NOO_FRAMEWORK_ADMIN . '/shortcodes/_init.php';

// Meta Boxes
require_once NOO_FRAMEWORK_ADMIN . '/meta-boxes/_init.php';

// Taxonomy Meta Fields
// require_once NOO_FRAMEWORK_ADMIN . '/taxonomy-meta.php';

// Mega Menu
require_once NOO_FRAMEWORK_ADMIN . '/mega-menu.php';

// SMK Sidebar Generator
if ( !defined( 'SMK_SBG_PATH' ) ) define( 'SMK_SBG_PATH', NOO_FRAMEWORK_ADMIN . '/smk-sidebar-generator/' );
if ( !defined( 'SMK_SBG_URI' ) ) define( 'SMK_SBG_URI', NOO_FRAMEWORK_ADMIN_URI . '/smk-sidebar-generator/' );
require_once SMK_SBG_PATH . 'smk-sidebar-generator.php';

// Visual Composer
require_once NOO_FRAMEWORK_ADMIN . '/visual-composer.php';

//
// Widgets
//
$widget_path = get_template_directory() . '/widgets';

if ( file_exists( $widget_path . '/widgets_init.php' ) ) {
	require_once $widget_path . '/widgets_init.php';
}

// CitiLights Widgets
require_once $widget_path . '/citilights_widgets.php';

//
// Plugins
// First we'll check if there's any plugins inluded
//
$plugin_path = get_template_directory() . '/plugins';
if ( file_exists( $plugin_path . '/tgmpa_register.php' ) ) {
	require_once NOO_FRAMEWORK_ADMIN . '/class-tgm-plugin-activation.php';
	require_once $plugin_path . '/tgmpa_register.php';
}

/**
 * Check active plugin IDX Optima Express
 *
 * @package 	Citilights
 * @author 		KENT <tuanlv@vietbrain.com>
 * @version 	1.0
 */

if ( ! function_exists( 'noo_citilights_is_active_IDX' ) ) :
	
	function noo_citilights_is_active_IDX() {

		$status = is_plugin_active( 'optima-express/iHomefinder.php' );
		return $status;

	}

endif;


if ( ! function_exists( 'noo_get_video' ) ) :
    
    function noo_get_video( $video_url = '' ) {

        
        if( !empty( $video_url ) ) {
            
            $wp_embed = new WP_Embed();
            $protocol = is_ssl() ? 'https' : 'http';

            if( !is_ssl() ) {
                $video_url = str_replace ( 'https://', 'http://', $video_url );
            }
            $video_output = $wp_embed->run_shortcode('[embed width="660" height="371.25"]' . esc_url( $video_url ) . '[/embed]');

            if( $video_output == '<a href="'.$video_url.'">'.$video_url.'</a>' ) :
                $width  = '660' ;
                $height = '371.25';
                $video_link = @parse_url($video_url);
                if ( empty( $video_link['host'] ) ) {
                    return false;
                }

                if ( $video_link['host'] == 'www.youtube.com' || $video_link['host']  == 'youtube.com' ) :
                    parse_str( @parse_url( $video_url, PHP_URL_QUERY ), $my_array_of_vars );
                    $video =  $my_array_of_vars['v'] ;
                    $video_output ='<iframe width="'.$width.'" height="'.$height.'" src="'.$protocol.'://www.youtube.com/embed/'.$video.'?rel=0&wmode=opaque" frameborder="0" allowfullscreen></iframe>';
                
                elseif( $video_link['host'] == 'www.youtu.be' || $video_link['host']  == 'youtu.be' ) :
                    $video = substr(@parse_url($video_url, PHP_URL_PATH), 1);
                    $video_output ='<iframe width="'.$width.'" height="'.$height.'" src="'.$protocol.'://www.youtube.com/embed/'.$video.'?rel=0&wmode=opaque" frameborder="0" allowfullscreen></iframe>';

                elseif( $video_link['host'] == 'www.vimeo.com' || $video_link['host']  == 'vimeo.com' ) :
                    $video = (int) substr(@parse_url($video_url, PHP_URL_PATH), 1);
                    $video_output='<iframe src="'.$protocol.'://player.vimeo.com/video/'.$video.'?wmode=opaque" width="'.$width.'" height="'.$height.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
                
                elseif( $video_link['host'] == 'www.my.matterport.com' || $video_link['host']  == 'my.matterport.com' ) :
                    $video = substr(@parse_url($video_url, PHP_URL_PATH), 7);
                    $video_id = strtok($video, 'm');
                    $video_output='<iframe frameborder="0" width="'.$width.'" height="'.$height.'" src="'.$protocol.'://my.matterport.com/showcase-beta/embed/'.$video_id.'"></iframe>';
                endif;
            endif;

            echo $video_output;

        }

    }

endif;