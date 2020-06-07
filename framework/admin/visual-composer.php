<?php
/**
 * NOO Visual Composer Add-ons
 *
 * Customize Visual Composer to suite NOO Framework
 *
 * @package    NOO Framework
 * @subpackage NOO Visual Composer Add-ons
 * @version    1.0.0
 * @author     Kan Nguyen <khanhnq@nootheme.com>
 * @copyright  Copyright (c) 2014, NooTheme
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       http://nootheme.com
 */

// Set as theme - http://kb.wpbakery.com/index.php?title=Vc_set_as_theme
if (function_exists('vc_set_as_theme')) :
    vc_set_as_theme(true);
endif;


// Disable Frontend Editor
// http://kb.wpbakery.com/index.php?title=Vc_disable_frontend

// if (function_exists('vc_disable_frontend')) :
//     vc_disable_frontend();
// endif;

if (defined('WPB_VC_VERSION')) :

    function noo_dropdown_group_param($param, $param_value)
    {
        $css_option = vc_get_dropdown_option($param, $param_value);
        $param_line = '';
        $param_line .= '<select name="' . $param['param_name'] .
            '" class="dh-chosen-select wpb_vc_param_value wpb-input wpb-select ' . $param['param_name'] . ' ' .
            $param['type'] . ' ' . $css_option . '" data-option="' . $css_option . '">';
        foreach ($param['optgroup'] as $text_opt => $opt) {
            if (is_array($opt)) {
                $param_line .= '<optgroup label="' . $text_opt . '">';
                foreach ($opt as $text_val => $val) {
                    if (is_numeric($text_val) && (is_string($val) || is_numeric($val))) {
                        $text_val = $val;
                    }
                    $selected = '';
                    if ($param_value !== '' && (string)$val === (string)$param_value) {
                        $selected = ' selected="selected"';
                    }
                    $param_line .= '<option class="' . $val . '" value="' . $val . '"' . $selected . '>' .
                        htmlspecialchars($text_val) . '</option>';
                }
                $param_line .= '</optgroup>';
            } elseif (is_string($opt)) {
                if (is_numeric($text_opt) && (is_string($opt) || is_numeric($opt))) {
                    $text_opt = $opt;
                }
                $selected = '';
                if ($param_value !== '' && (string)$opt === (string)$param_value) {
                    $selected = ' selected="selected"';
                }
                $param_line .= '<option class="' . $opt . '" value="' . $opt . '"' . $selected . '>' .
                    htmlspecialchars($text_opt) . '</option>';
            }
        }
        $param_line .= '</select>';
        return $param_line;
    }

    if (function_exists('vc_add_shortcode_param')) {
        vc_add_shortcode_param('noo_dropdown_group', 'noo_dropdown_group_param');
    } elseif (function_exists('add_shortcode_param')) {
        add_shortcode_param('noo_dropdown_group', 'noo_dropdown_group_param');
    }


    // Categories select field type
    if (!function_exists('noo_vc_field_type_post_categories')) :

        function noo_vc_custom_param_post_categories($settings, $value)
        {
            $dependency = (function_exists('vc_generate_dependencies_attributes')) ? vc_generate_dependencies_attributes($settings) : '';
            $categories = get_categories(array(
                'orderby' => 'NAME',
                'order' => 'ASC'
            ));
            $class = 'wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . '_field';
            $selected_values = explode(',', $value);
            $html = array('<div class="noo_vc_custom_param post_categories">');
            $html[] = '  <input type="hidden" name="' . $settings['param_name'] . '" value="' . $value . '" class="wpb_vc_param_value" />';
            $html[] = '  <select name="' . $settings['param_name'] . '-select" multiple="true" class="' . $class . '" ' . $dependency . '>';
            $html[] = '    <option value="all" ' . (in_array('all', $selected_values) ? 'selected="true"' : '') . '>' . __('All', 'noo') . '</option>';
            foreach ($categories as $category) {
                $html[] = '    <option value="' . $category->term_id . '" ' . (in_array($category->term_id, $selected_values) ? 'selected="true"' : '') . '>';
                $html[] = '      ' . $category->name;
                $html[] = '    </option>';
            }

            $html[] = '  </select>';
            $html[] = '</div>';
            $html[] = '<script>';
            $html[] = '  jQuery("document").ready( function() {';
            $html[] = '	   jQuery( "select[name=\'' . $settings['param_name'] . '-select\']" ).click( function() {';
            $html[] = '      var selected_values = jQuery(this).find("option:selected").map(function(){ return this.value; }).get().join(",");';
            $html[] = '      jQuery( "input[name=\'' . $settings['param_name'] . '\']" ).val( selected_values );';
            $html[] = '	   } );';
            $html[] = '  } );';
            $html[] = '</script>';

            return implode("\n", $html);
        }

        if (function_exists('vc_add_shortcode_param')) {
            vc_add_shortcode_param('post_categories', 'noo_vc_custom_param_post_categories');
        } elseif (function_exists('add_shortcode_param')) {
            add_shortcode_param('post_categories', 'noo_vc_custom_param_post_categories');
        }

    endif;

    if (!function_exists('noo_vc_custom_param_user_list')) :
        function noo_vc_custom_param_user_list($settings, $value)
        {
            $dependency = (function_exists('vc_generate_dependencies_attributes')) ? vc_generate_dependencies_attributes($settings) : '';
            $users = get_users(array(
                'orderby' => 'NAME',
                'order' => 'ASC'
            ));
            $class = 'wpb_vc_param_value wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . '_field';
            $html = array('<div class="noo_vc_custom_param user_list">');
            // $html[] = '  <input type="hidden" name="'. $settings['param_name'] . '" value="'. $value . '" class="wpb_vc_param_value" />';
            $html[] = '  <select name="' . $settings['param_name'] . '" class="' . $class . '" ' . $dependency . '>';
            foreach ($users as $user) {
                $html[] = '    <option value="' . $user->ID . '" ' . (selected($value, $user->ID, false)) . '>';
                $html[] = '      ' . $user->display_name;
                $html[] = '    </option>';
            }

            $html[] = '  </select>';
            $html[] = '</div>';

            return implode("\n", $html);
        }

        if (function_exists('vc_add_shortcode_param')) {
            vc_add_shortcode_param('user_list', 'noo_vc_custom_param_user_list');
        } elseif (function_exists('add_shortcode_param')) {
            add_shortcode_param('user_list', 'noo_vc_custom_param_user_list');
        }

    endif;

    if (class_exists('RevSlider')) {
        if (!function_exists('noo_vc_rev_slider')) :
            function noo_vc_rev_slider($settings, $value)
            {
                $dependency = (function_exists('vc_generate_dependencies_attributes')) ? vc_generate_dependencies_attributes($settings) : '';
                $rev_slider = new RevSlider();
                $sliders = $rev_slider->getArrSliders();
                $class = 'wpb_vc_param_value wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . '_field';
                $html = array('<div class="noo_vc_custom_param noo_rev_slider">');
                $html[] = '  <select name="' . $settings['param_name'] . '" class="' . $class . '" ' . $dependency . '>';
                foreach ($sliders as $slider) {
                    $html[] = '    <option value="' . $slider->getAlias() . '"' . (selected($value, $slider->getAlias())) . '>' . $slider->getTitle() . '</option>';
                }
                $html[] = '  </select>';
                $html[] = '</div>';

                return implode("\n", $html);
            }

            if (function_exists('vc_add_shortcode_param')) {
                vc_add_shortcode_param('noo_rev_slider', 'noo_vc_rev_slider');
            } elseif (function_exists('add_shortcode_param')) {
                add_shortcode_param('noo_rev_slider', 'noo_vc_rev_slider');
            }

        endif;
    }

    if (!function_exists('noo_vc_custom_param_ui_slider')) :
        function noo_vc_custom_param_ui_slider($settings, $value)
        {
            $dependency = (function_exists('vc_generate_dependencies_attributes')) ? vc_generate_dependencies_attributes($settings) : '';
            $class = 'noo-slider wpb_vc_param_value wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . '_field';
            $data_min = (isset($settings['data_min']) && !empty($settings['data_min'])) ? 'data-min="' . $settings['data_min'] . '"' : 'data-min="0"';
            $data_max = (isset($settings['data_max']) && !empty($settings['data_max'])) ? 'data-max="' . $settings['data_max'] . '"' : 'data-max="100"';
            $data_step = (isset($settings['data_step']) && !empty($settings['data_step'])) ? 'data-step="' . $settings['data_step'] . '"' : 'data-step="1"';
            $html = array();

            $html[] = '	<div class="noo-control">';
            $html[] = '		<input type="text" id="' . $settings['param_name'] . '" name="' . $settings['param_name'] . '" class="' . $class . '" value="' . $value . '" ' . $data_min . ' ' . $data_max . ' ' . $data_step . '/>';
            $html[] = '	</div>';
            $html[] = '<script>';
            $html[] = 'jQuery("#' . $settings['param_name'] . '").each(function() {';
            $html[] = '	var $this = jQuery(this);';
            $html[] = '	var $slider = jQuery("<div>", {id: $this.attr("id") + "-slider"}).insertAfter($this);';
            $html[] = '	$slider.slider(';
            $html[] = '	{';
            $html[] = '		range: "min",';
            $html[] = '		value: $this.val() || $this.data("min") || 0,';
            $html[] = '		min: $this.data("min") || 0,';
            $html[] = '		max: $this.data("max") || 100,';
            $html[] = '		step: $this.data("step") || 1,';
            $html[] = '		slide: function(event, ui) {';
            $html[] = '			$this.val(ui.value).attr("value", ui.value);';
            $html[] = '		}';
            $html[] = '	}';
            $html[] = '	);';
            $html[] = '	$this.change(function() {';
            $html[] = '		$slider.slider( "option", "value", $this.val() );';
            $html[] = '	});';
            $html[] = '});';
            $html[] = '</script>';

            return implode("\n", $html);
        }

        if (function_exists('vc_add_shortcode_param')) {
            vc_add_shortcode_param('ui_slider', 'noo_vc_custom_param_ui_slider');
        } elseif (function_exists('add_shortcode_param')) {
            add_shortcode_param('ui_slider', 'noo_vc_custom_param_ui_slider');
        }
    endif;
endif;

if (defined('WPB_VC_VERSION')) :
    if (!function_exists('noo_vc_admin_enqueue_assets')) :
        function noo_vc_admin_enqueue_assets($hook)
        {

            if ($hook != 'edit.php' && $hook != 'post.php' && $hook != 'post-new.php') {
                return;
            }
            // Enqueue style for VC admin
            wp_register_style('noo-vc-admin-css', NOO_FRAMEWORK_ADMIN_URI . '/assets/css/noo-vc-admin.css');
            wp_enqueue_style('noo-vc-admin-css');

            // Enqueue script for VC admin
            wp_register_script('noo-vc-admin-js', NOO_FRAMEWORK_ADMIN_URI . '/assets/js/noo-vc-admin.js', null, null, false);
            wp_enqueue_script('noo-vc-admin-js');
        }
    endif;
    add_action('admin_enqueue_scripts', 'noo_vc_admin_enqueue_assets');
endif;

// Remove unused VC Metabox: Teaser Metabox
if (defined('WPB_VC_VERSION')) :
    if (!function_exists('noo_vs_remove_unused_metabox')) :
        function noo_vs_remove_unused_metabox()
        {
            if (is_admin()) {
                $post_types = get_post_types('', 'names');
                foreach ($post_types as $post_type) {
                    remove_meta_box('vc_teaser', $post_type, 'side');
                }
            }
        }

        add_action('do_meta_boxes', 'noo_vs_remove_unused_metabox');
    endif;
endif;

// Remove unused VC Shortcodes
if (defined('WPB_VC_VERSION')) :

    if (!function_exists('noo_vc_remove_unused_elements')) :
        function noo_vc_remove_unused_elements()
        {

            vc_remove_element('vc_text_separator');
            // vc_remove_element( 'vc_facebook' );
            // vc_remove_element( 'vc_tweetmeme' );
            // vc_remove_element( 'vc_googleplus' );
            // vc_remove_element( 'vc_pinterest' );
            vc_remove_element('vc_toggle');
            // vc_remove_element('rev_slider_vc');
            // vc_remove_element( 'vc_gallery' );
            vc_remove_element('vc_images_carousel');
            // vc_remove_element( 'vc_posts_grid' );
            vc_remove_element('vc_carousel');
            vc_remove_element('vc_posts_slider');
            vc_remove_element('vc_video');
            // vc_remove_element( 'vc_flickr' );
            vc_remove_element('vc_progress_bar');
            vc_remove_element('vc_wp_search');
            vc_remove_element('vc_wp_meta');
            vc_remove_element('vc_wp_recentcomments');
            vc_remove_element('vc_wp_calendar');
            vc_remove_element('vc_wp_pages');
            vc_remove_element('vc_wp_tagcloud');
            vc_remove_element('vc_wp_custommenu');
            vc_remove_element('vc_wp_text');
            vc_remove_element('vc_wp_posts');
            vc_remove_element('vc_wp_links');
            vc_remove_element('vc_wp_categories');
            vc_remove_element('vc_wp_archives');
            vc_remove_element('vc_wp_rss');
            vc_remove_element('vc_button2');
            vc_remove_element('vc_cta_button2');
            // vc_remove_element('vc_empty_space');
            // vc_remove_element( 'vc_custom_heading' );

        }

        add_action('init', 'noo_vc_remove_unused_elements');

    endif;
endif;
/*=========================================
    Type: Testimonial Category
    ===========================================*/
if ( is_plugin_active('js_composer/js_composer.php')) :
    if (!function_exists('noo_vc_field_type_testimonial_categories_services')) :

        function noo_vc_field_type_testimonial_categories_services($settings, $value)
        {
            if (taxonomy_exists('testimonial_category')) :
                $categories = get_categories(array('orderby' => 'NAME', 'order' => 'ASC', 'taxonomy' => 'testimonial_category'));
                $class = 'wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . '_field';
                $selected_values = explode(',', $value);
                $html = array('<div class="noo_vc_custom_param post_categories">');
                $html[] = '  <input type="hidden" name="' . $settings['param_name'] . '" value="' . $value .
                    '" class="wpb_vc_param_value" />';
                $html[] = '  <select name="' . $settings['param_name'] . '-select" multiple="true" class="' . $class . '" >';
                $html[] = '    <option value="all" ' . (in_array('all', $selected_values) ? 'selected="true"' : '') . '>' .
                    esc_html__('All', 'noo') . '</option>';
                foreach ($categories as $category) {
                    $html[] = '    <option value="' . $category->cat_ID . '" ' .
                        (in_array($category->cat_ID, $selected_values) ? 'selected="true"' : '') . '>';
                    $html[] = '      ' . $category->name;
                    $html[] = '    </option>';
                }

                $html[] = '  </select>';
                $html[] = '</div>';
                $html[] = '<script>';
                $html[] = '  jQuery("document").ready( function() {';
                $html[] = '       jQuery( "select[name=\'' . $settings['param_name'] . '-select\']" ).click( function() {';
                $html[] = '      var selected_values = jQuery(this).find("option:selected").map(function(){ return this.value; }).get().join(",");';
                $html[] = '      jQuery( "input[name=\'' . $settings['param_name'] . '\']" ).val( selected_values );';
                $html[] = '       } );';
                $html[] = '  } );';
                $html[] = '</script>';

                return implode("\n", $html);
            endif;
        }

        vc_add_shortcode_param('testimonial-cat', 'noo_vc_field_type_testimonial_categories_services');
    endif;
endif;    
/*=====================================
    Type: Testimonial Single
    ======================================*/
if ( is_plugin_active('js_composer/js_composer.php')) :
    if (!function_exists('noo_testimonial_single_settings_field_shortcode_param')) :

        function noo_testimonial_single_settings_field_shortcode_param($settings, $value)
        {
            if (taxonomy_exists('testimonial_category')) :
                $posts = get_posts(array(
                    'posts_per_page' => -1,
                    'post_type' => 'testimonial',
                    'post_status' => 'publish',
                ));
                $class = 'wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . '_field';
                $selected_values = explode(',', $value);
                $html = array('<div class="noo_vc_custom_param post_categories">');
                $html[] = '  <input type="hidden" name="' . $settings['param_name'] . '" value="' . $value .
                    '" class="wpb_vc_param_value" />';
                $html[] = '  <select name="' . $settings['param_name'] . '-select" multiple="true" class="' . $class . '" >';
                $html[] = '    <option value="all" ' . (in_array('all', $selected_values) ? 'selected="true"' : '') . '>' .
                    esc_html__('All', 'noo') . '</option>';
                foreach ($posts as $post) {
                    $html[] = '    <option value="' . $post->ID . '" ' .
                        (in_array($post->ID, $selected_values) ? 'selected="true"' : '') . '>';
                    $html[] = '      ' . $post->post_title;
                    $html[] = '    </option>';
                }

                $html[] = '  </select>';
                $html[] = '</div>';
                $html[] = '<script>';
                $html[] = '  jQuery("document").ready( function() {';
                $html[] = '       jQuery( "select[name=\'' . $settings['param_name'] . '-select\']" ).click( function() {';
                $html[] = '      var selected_values = jQuery(this).find("option:selected").map(function(){ return this.value; }).get().join(",");';
                $html[] = '      jQuery( "input[name=\'' . $settings['param_name'] . '\']" ).val( selected_values );';
                $html[] = '       } );';
                $html[] = '  } );';
                $html[] = '</script>';

                return implode("\n", $html);
            endif;
        }

        vc_add_shortcode_param('testimonial-single', 'noo_testimonial_single_settings_field_shortcode_param');
    endif;
endif;
/*=====================================
    Type: Post Single
    ======================================*/

if ( is_plugin_active('js_composer/js_composer.php')) :
    if (!function_exists('noo_post_single_settings_field_shortcode_param')) :

        function noo_post_single_settings_field_shortcode_param($settings, $value)
        {
            if (taxonomy_exists('testimonial_category')) :
                $posts = get_posts(array(
                    'posts_per_page' => -1,
                    'post_type' => 'post',
                    'post_status' => 'publish',
                ));
                $class = 'wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . '_field';
                $selected_values = explode(',', $value);
                $html = array('<div class="noo_vc_custom_param post_categories">');
                $html[] = '  <input type="hidden" name="' . $settings['param_name'] . '" value="' . $value .
                    '" class="wpb_vc_param_value" />';
                $html[] = '  <select name="' . $settings['param_name'] . '-select" multiple="true" class="' . $class . '" >';
                $html[] = '    <option value="all" ' . (in_array('all', $selected_values) ? 'selected="true"' : '') . '>' .
                    esc_html__('All', 'noo') . '</option>';
                foreach ($posts as $post) {
                    $html[] = '    <option value="' . $post->ID . '" ' .
                        (in_array($post->ID, $selected_values) ? 'selected="true"' : '') . '>';
                    $html[] = '      ' . $post->post_title;
                    $html[] = '    </option>';
                }

                $html[] = '  </select>';
                $html[] = '</div>';
                $html[] = '<script>';
                $html[] = '  jQuery("document").ready( function() {';
                $html[] = '       jQuery( "select[name=\'' . $settings['param_name'] . '-select\']" ).click( function() {';
                $html[] = '      var selected_values = jQuery(this).find("option:selected").map(function(){ return this.value; }).get().join(",");';
                $html[] = '      jQuery( "input[name=\'' . $settings['param_name'] . '\']" ).val( selected_values );';
                $html[] = '       } );';
                $html[] = '  } );';
                $html[] = '</script>';

                return implode("\n", $html);
            endif;
        }

        vc_add_shortcode_param('post-single', 'noo_post_single_settings_field_shortcode_param');
    endif;
endif;

// POST CAT
//==================================================================
if ( is_plugin_active('js_composer/js_composer.php')) :
    if (!function_exists('noo_vc_field_type_post_categories_services')) :

        function noo_vc_field_type_post_categories_services($settings, $value)
        {
            if (taxonomy_exists('testimonial_category')) :
                $categories = get_categories(array('orderby' => 'NAME', 'order' => 'ASC', 'taxonomy' => 'category'));

                $class = 'wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . '_field';
                $selected_values = explode(',', $value);
                $html = array('<div class="noo_vc_custom_param post_categories">');
                $html[] = '  <input type="hidden" name="' . $settings['param_name'] . '" value="' . $value .
                    '" class="wpb_vc_param_value" />';
                $html[] = '  <select name="' . $settings['param_name'] . '-select" multiple="true" class="' . $class . '" >';
                $html[] = '    <option value="all" ' . (in_array('all', $selected_values) ? 'selected="true"' : '') . '>' .
                    esc_html__('All', 'noo') . '</option>';
                foreach ($categories as $category) {
                    $html[] = '    <option value="' . $category->cat_ID . '" ' .
                        (in_array($category->cat_ID, $selected_values) ? 'selected="true"' : '') . '>';

                    $html[] = '      ' . $category->name;
                    $html[] = '    </option>';
                }

                $html[] = '  </select>';
                $html[] = '</div>';
                $html[] = '<script>';
                $html[] = '  jQuery("document").ready( function() {';
                $html[] = '       jQuery( "select[name=\'' . $settings['param_name'] . '-select\']" ).click( function() {';
                $html[] = '      var selected_values = jQuery(this).find("option:selected").map(function(){ return this.value; }).get().join(",");';
                $html[] = '      jQuery( "input[name=\'' . $settings['param_name'] . '\']" ).val( selected_values );';
                $html[] = '       } );';
                $html[] = '  } );';
                $html[] = '</script>';

                return implode("\n", $html);
            endif;
        }

        vc_add_shortcode_param('post-cat', 'noo_vc_field_type_post_categories_services');
    endif;
endif;


// TYPE PROPERTY
if ( is_plugin_active('js_composer/js_composer.php')) :
    if (!function_exists('noo_vc_field_type_property_categories')) :

        function noo_vc_field_type_property_categories($settings, $value)
        {
            if (taxonomy_exists('testimonial_category')) :
                $categories = get_categories(array('orderby' => 'NAME', 'order' => 'ASC', 'taxonomy' => 'property_category'));
                $class = 'wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . '_field';
                $selected_values = explode(',', $value);
                $html = array('<div class="noo_vc_custom_param post_categories">');
                $html[] = '  <input type="hidden" name="' . $settings['param_name'] . '" value="' . $value .
                    '" class="wpb_vc_param_value" />';
                $html[] = '  <select name="' . $settings['param_name'] . '" multiple="true" class="' . $class . '" >';
                $html[] = '    <option value="all" ' . (in_array('all', $selected_values) ? 'selected="true"' : '') . '>' .
                    esc_html__('All', 'noo') . '</option>';
                foreach ($categories as $category) {
                    $html[] = '    <option value="' . $category->cat_ID . '" ' .
                        (in_array($category->cat_ID, $selected_values) ? 'selected="true"' : '') . '>';
                    $html[] = '      ' . $category->name;
                    $html[] = '    </option>';
                }

                $html[] = '  </select>';
                $html[] = '</div>';
                $html[] = '<script>';
                $html[] = '  jQuery("document").ready( function() {';
                $html[] = '       jQuery( "select[name=\'' . $settings['param_name'] . '\']" ).click( function() {';
                $html[] = '      var selected_values = jQuery(this).find("option:selected").map(function(){ return this.value; }).get().join(",");';
                $html[] = '      jQuery( "input[name=\'' . $settings['param_name'] . '\']" ).val( selected_values );';
                $html[] = '       } );';
                $html[] = '  } );';
                $html[] = '</script>';

                return implode("\n", $html);
            endif;
        }

        vc_add_shortcode_param('property_type', 'noo_vc_field_type_property_categories');
    endif;
endif;
//=====================================================================================

// STATUS PROPERTY TYPE
if ( is_plugin_active('js_composer/js_composer.php')) :
    if (!function_exists('noo_vc_field_status_property_categories')) :

        function noo_vc_field_status_property_categories($settings, $value)
        {
            if (taxonomy_exists('testimonial_category')) :
                $categories = get_categories(array('orderby' => 'NAME', 'order' => 'ASC', 'taxonomy' => 'property_status'));
                $class = 'wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . '_field';
                $selected_values = explode(',', $value);
                $html = array('<div class="noo_vc_custom_param post_categories">');
                $html[] = '  <input type="hidden" name="' . $settings['param_name'] . '" value="' . $value .
                    '" class="wpb_vc_param_value" />';
                $html[] = '  <select name="' . $settings['param_name'] . '" multiple="true" class="' . $class . '" >';
                $html[] = '    <option value="all" ' . (in_array('all', $selected_values) ? 'selected="true"' : '') . '>' .
                    esc_html__('All', 'noo') . '</option>';
                foreach ($categories as $category) {
                    $html[] = '    <option value="' . $category->cat_ID . '" ' .
                        (in_array($category->cat_ID, $selected_values) ? 'selected="true"' : '') . '>';
                    $html[] = '      ' . $category->name;
                    $html[] = '    </option>';
                }

                $html[] = '  </select>';
                $html[] = '</div>';
                $html[] = '<script>';
                $html[] = '  jQuery("document").ready( function() {';
                $html[] = '       jQuery( "select[name=\'' . $settings['param_name'] . '\']" ).click( function() {';
                $html[] = '      var selected_values = jQuery(this).find("option:selected").map(function(){ return this.value; }).get().join(",");';
                $html[] = '      jQuery( "input[name=\'' . $settings['param_name'] . '\']" ).val( selected_values );';
                $html[] = '       } );';
                $html[] = '  } );';
                $html[] = '</script>';

                return implode("\n", $html);
            endif;
        }

        vc_add_shortcode_param('property_status', 'noo_vc_field_status_property_categories');
    endif;
endif;

// LOCATION PROPERTY TYPE
if ( is_plugin_active('js_composer/js_composer.php')) :
    if (!function_exists('noo_vc_field_location_property_categories')) :

        function noo_vc_field_location_property_categories($settings, $value)
        {
            if (taxonomy_exists('testimonial_category')) :
                $categories = get_categories(array('orderby' => 'NAME', 'order' => 'ASC', 'taxonomy' => 'property_location'));
                $class = 'wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . '_field';
                $selected_values = explode(',', $value);
                $html = array('<div class="noo_vc_custom_param post_categories">');
                $html[] = '  <input type="hidden" name="' . $settings['param_name'] . '" value="' . $value .
                    '" class="wpb_vc_param_value" />';
                $html[] = '  <select name="' . $settings['param_name'] . '" multiple="true" class="' . $class . '" >';
                $html[] = '    <option value="all" ' . (in_array('all', $selected_values) ? 'selected="true"' : '') . '>' .
                    esc_html__('All', 'noo') . '</option>';
                foreach ($categories as $category) {
                    $html[] = '    <option value="' . $category->cat_ID . '" ' .
                        (in_array($category->cat_ID, $selected_values) ? 'selected="true"' : '') . '>';
                    $html[] = '      ' . $category->name;
                    $html[] = '    </option>';
                }

                $html[] = '  </select>';
                $html[] = '</div>';
                $html[] = '<script>';
                $html[] = '  jQuery("document").ready( function() {';
                $html[] = '       jQuery( "select[name=\'' . $settings['param_name'] . '\']" ).click( function() {';
                $html[] = '      var selected_values = jQuery(this).find("option:selected").map(function(){ return this.value; }).get().join(",");';
                $html[] = '      jQuery( "input[name=\'' . $settings['param_name'] . '\']" ).val( selected_values );';
                $html[] = '       } );';
                $html[] = '  } );';
                $html[] = '</script>';

                return implode("\n", $html);
            endif;
        }

        vc_add_shortcode_param('property_location', 'noo_vc_field_location_property_categories');
    endif;
endif;
///SUB LOCATION PROPERTY TYPE
if ( is_plugin_active('js_composer/js_composer.php')) :
    if (!function_exists('noo_vc_field_sub_location_property_categories')) :

        function noo_vc_field_sub_location_property_categories($settings, $value)
        {
            if (taxonomy_exists('testimonial_category')) :
                $categories = get_categories(array('orderby' => 'NAME', 'order' => 'ASC', 'taxonomy' => 'property_sub_location'));
                $class = 'wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . '_field';
                $selected_values = explode(',', $value);
                $html = array('<div class="noo_vc_custom_param post_categories">');
                $html[] = '  <input type="hidden" name="' . $settings['param_name'] . '" value="' . $value .
                    '" class="wpb_vc_param_value" />';
                $html[] = '  <select name="' . $settings['param_name'] . '" multiple="true" class="' . $class . '" >';
                $html[] = '    <option value="all" ' . (in_array('all', $selected_values) ? 'selected="true"' : '') . '>' .
                    esc_html__('All', 'noo') . '</option>';
                foreach ($categories as $category) {
                    $html[] = '    <option value="' . $category->cat_ID . '" ' .
                        (in_array($category->cat_ID, $selected_values) ? 'selected="true"' : '') . '>';
                    $html[] = '      ' . $category->name;
                    $html[] = '    </option>';
                }

                $html[] = '  </select>';
                $html[] = '</div>';
                $html[] = '<script>';
                $html[] = '  jQuery("document").ready( function() {';
                $html[] = '       jQuery( "select[name=\'' . $settings['param_name'] . '\']" ).click( function() {';
                $html[] = '      var selected_values = jQuery(this).find("option:selected").map(function(){ return this.value; }).get().join(",");';
                $html[] = '      jQuery( "input[name=\'' . $settings['param_name'] . '\']" ).val( selected_values );';
                $html[] = '       } );';
                $html[] = '  } );';
                $html[] = '</script>';

                return implode("\n", $html);
            endif;
        }

        vc_add_shortcode_param('property_sub_location', 'noo_vc_field_sub_location_property_categories');
    endif;
endif;    
// NOO VC Shortcodes Base Element
// =========================================================================
if (defined('WPB_VC_VERSION')) :
    if (!function_exists('noo_vc_base_element')) :

        function noo_vc_base_element()
        {

            $category_base_element = __('Base Elements', 'noo');
            $category_typography = __('Typography', 'noo');
            $category_content = __('Content', 'noo');
            $category_wp_content = __('WordPress Content', 'noo');
            $category_media = __('Media', 'noo');
            $category_custom = __('Custom', 'noo');

            $param_content_name = 'content';
            $param_content_heading = __('Text', 'noo');
            $param_content_description = __('Enter your text.', 'noo');
            $param_content_type = 'textarea_html';
            $param_content_holder = 'div';
            $param_content_value = '';

            $param_visibility_name = 'visibility';
            $param_visibility_heading = __('Visibility', 'noo');
            $param_visibility_description = '';
            $param_visibility_type = 'dropdown';
            $param_visibility_holder = 'div';
            $param_visibility_value = array(
                __('All Devices', 'noo') => "all",
                __('Hidden Phone', 'noo') => "hidden-phone",
                __('Hidden Tablet', 'noo') => "hidden-tablet",
                __('Hidden PC', 'noo') => "hidden-pc",
                __('Visible Phone', 'noo') => "visible-phone",
                __('Visible Tablet', 'noo') => "visible-tablet",
                __('Visible PC', 'noo') => "visible-pc",
            );

            $param_class_name = 'class';
            $param_class_heading = __('Class', 'noo');
            $param_class_description = __('(Optional) Enter a unique class name.', 'noo');
            $param_class_type = 'textfield';
            $param_class_holder = 'div';

            $param_id_name = 'id';
            $param_id_heading = __('Row ID', 'noo');
            $param_id_description = __('(Optional) Enter an unique ID. You will need this ID when creating One Page layout.', 'noo');
            $param_id_type = 'textfield';
            $param_id_holder = 'div';

            $param_custom_style_name = 'custom_style';
            $param_custom_style_heading = __('Custom Style', 'noo');
            $param_custom_style_description = __('(Optional) Enter inline CSS.', 'noo');
            $param_custom_style_type = 'textfield';
            $param_custom_style_holder = 'div';

            $param_holder = 'div';

            $param_animation_value = array(
                "None" => "",
                "Bounce In" => "bounceIn",
                "Bounce In Right" => "bounceInRight",
                "Bounce In Left" => "bounceInLeft",
                "Bounce In Up" => "bounceInUp",
                "Bounce In Down" => "bounceInDown",
                "Fade In" => "fadeIn",
                "Grow In" => "growIn",
                "Shake" => "shake",
                "Shake Up" => "shakeUp",
                "Fade In Left" => "fadeInLeft",
                "Fade In Right" => "fadeInRight",
                "Fade In Up" => "fadeInUp",
                "Fade InDown" => "fadeInDown",
                "Rotate In" => "rotateIn",
                "Rotate In Up Left" => "rotateInUpLeft",
                "Rotate In Down Left" => "rotateInDownLeft",
                "Rotate In Up Right" => "rotateInUpRight",
                "Rotate In Down Right" => "rotateInDownRight",
                "Roll In" => "rollIn",
                "Wiggle" => "wiggle",
                "Swing" => "swing",
                "Tada" => "tada",
                "Wobble" => "wobble",
                "Pulse" => "pulse",
                "Light Speed In Right" => "lightSpeedInRight",
                "Light Speed In Left" => "lightSpeedInLeft",
                "Flip" => "flip",
                "Flip In X" => "flipInX",
                "Flip In Y" => "flipInY",
                // Out animation
                "Bounce Out" => "bounceOut",
                "Bounce Out Up" => "bounceOutUp",
                "Bounce Out Down" => "bounceOutDown",
                "Bounce Out Left" => "bounceOutLeft",
                "Bounce Out Right" => "bounceOutRight",
                "Fade Out" => "fadeOut",
                "Fade Out Up" => "fadeOutUp",
                "Fade Out Down" => "fadeOutDown",
                "Fade Out Left" => "fadeOutLeft",
                "Fade Out Right" => "fadeOutRight",
                "Flip Out X" => "flipOutX",
                "Flip Out Y" => "flipOutY",
                "Light Speed Out Right" => "lightSpeedOutLeft",
                "Rotate Out" => "rotateOut",
                "Rotate Out Up Left" => "rotateOutUpLeft",
                "Rotate Out Down Left" => "rotateOutDownLeft",
                "Rotate Out Up Right" => "rotateOutUpRight",
                "Roll Out" => "rollOut"
            );

            // [vc_row]
            // ============================
            vc_map_update('vc_row', array(
                'category' => $category_base_element,
                'weight' => 990,
                'class' => 'noo-vc-element noo-vc-element-row',
                'icon' => 'noo-vc-icon-row',
            ));

            vc_remove_param('vc_row', 'full_width');
            vc_remove_param('vc_row', 'gap');
            vc_remove_param('vc_row', 'full_height');
            vc_remove_param('vc_row', 'equal_height');
            vc_remove_param('vc_row', 'content_placement');
            vc_remove_param('vc_row', 'columns_placement');
            vc_remove_param('vc_row', 'video_bg');
            vc_remove_param('vc_row', 'video_bg_url');
            vc_remove_param('vc_row', 'video_bg_parallax');
            vc_remove_param('vc_row', 'bg_color');
            vc_remove_param('vc_row', 'font_color');
            vc_remove_param('vc_row', 'padding');
            vc_remove_param('vc_row', 'margin_bottom');
            vc_remove_param('vc_row', 'bg_image');
            vc_remove_param('vc_row', 'bg_image_repeat');
            vc_remove_param('vc_row', 'el_class');
            vc_remove_param('vc_row', 'el_id');
            vc_remove_param('vc_row', 'css');

            vc_add_param('vc_row', array(
                'param_name' => 'bg_color',
                'heading' => __('Background Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder,
            ));

            vc_add_param('vc_row', array(
                'param_name' => 'bg_image',
                'heading' => __('Background Image', 'noo'),
                'type' => 'attach_image',
                'holder' => $param_holder
            ));
            vc_add_param('vc_row', array(
                'param_name' => 'bg_color_overlay',
                'heading' => __('Background Color Overlay', 'noo'),
                'type' => 'colorpicker',
                'dependency' => array('element' => "bg_image", 'not_empty' => true),
                'holder' => $param_holder,
            ));
            vc_add_param('vc_row', array(
                'param_name' => 'bg_image_repeat',
                'heading' => __('Background Image Repeat', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array(
                    '' => 'false'),
                'dependency' => array('element' => "bg_image", 'not_empty' => true)
            ));

            vc_add_param('vc_row', array(
                'param_name' => 'parallax',
                'heading' => __('Parallax Background', 'noo'),
                'description' => __('Enable Parallax Background', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array(
                    '' => 'true'),
                'dependency' => array('element' => "bg_image", 'not_empty' => true)
            ));

            vc_add_param('vc_row', array(
                'param_name' => 'parallax_no_mobile',
                'heading' => __('Disable Parallax on Mobile', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array(
                    '' => 'true'),
                'dependency' => array('element' => "bg_image", 'not_empty' => true)
            ));

            vc_add_param('vc_row', array(
                'param_name' => 'parallax_velocity',
                'heading' => __('Parallax Velocity', 'noo'),
                'description' => __('The movement speed, value should be between -1.0 and 1.0', 'noo'),
                'type' => 'textfield',
                'holder' => $param_holder,
                'value' => '0.1',
                'dependency' => array('element' => "bg_image", 'not_empty' => true)
            ));

            vc_add_param('vc_row', array(
                'param_name' => 'bg_video',
                'heading' => __('Background Video', 'noo'),
                'description' => __('Enable Background Video, it will override Background Color and Background Image', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array(
                    __('Yes', 'noo') => 'true'
                )
            ));

            vc_add_param('vc_row', array(
                'param_name' => 'bg_video_url',
                'heading' => __('Video URL', 'noo'),
                'type' => 'textfield',
                'holder' => $param_holder,
                'dependency' => array('element' => "bg_video", 'value' => array('true'))
            ));

            vc_add_param('vc_row', array(
                'param_name' => 'bg_video_poster',
                'heading' => __('Video Poster Image', 'noo'),
                'type' => 'attach_image',
                'holder' => $param_holder,
                'dependency' => array('element' => 'bg_video', 'value' => array('true'))
            ));

            vc_add_param('vc_row', array(
                'param_name' => 'inner_container',
                'heading' => __('Has Container', 'noo'),
                'description' => __('If enable, this row will be placed inside a container.', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array(__('Yes', 'noo') => 'true'),
            ));

            vc_add_param('vc_row', array(
                'param_name' => 'border',
                'heading' => __('Border', 'noo'),
                'description' => '',
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => array(
                    __('None', 'noo') => '',
                    __('Top', 'noo') => 'top',
                    __('Right', 'noo') => 'right',
                    __('Left', 'noo') => 'left',
                    __('Bottom', 'noo') => 'bottom',
                    __('Vertical', 'noo') => 'vertical',
                    __('Horizontal', 'noo') => 'horizontal',
                    __('All', 'noo') => 'all'
                )
            ));

            vc_add_param('vc_row', array(
                'param_name' => 'padding_top',
                'heading' => __('Padding Top (px)', 'noo'),
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '20',
                'data_min' => '0',
                'data_max' => '100',
            ));

            vc_add_param('vc_row', array(
                'param_name' => 'padding_bottom',
                'heading' => __('Padding Bottom (px)', 'noo'),
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '20',
                'data_min' => '0',
                'data_max' => '100',
            ));

            vc_add_param('vc_row', array(
                'param_name' => $param_visibility_name,
                'heading' => $param_visibility_heading,
                'description' => $param_visibility_description,
                'type' => $param_visibility_type,
                'holder' => $param_visibility_holder,
                'value' => $param_visibility_value
            ));

            vc_add_param('vc_row', array(
                'param_name' => $param_class_name,
                'heading' => $param_class_heading,
                'description' => $param_class_description,
                'type' => $param_class_type,
                'holder' => $param_class_holder
            ));

            vc_add_param('vc_row', array(
                'param_name' => $param_id_name,
                'heading' => $param_id_heading,
                'description' => $param_id_description,
                'type' => $param_id_type,
                'holder' => $param_id_holder
            ));

            vc_add_param('vc_row', array(
                'param_name' => $param_custom_style_name,
                'heading' => $param_custom_style_heading,
                'description' => $param_custom_style_description,
                'type' => $param_custom_style_type,
                'holder' => $param_custom_style_holder
            ));

            // [vc_row_inner]
            // ============================
            vc_map_update('vc_row_inner', array(
                'category' => $category_base_element,
                'class' => 'noo-vc-element noo-vc-element-row',
                'icon' => 'noo-vc-icon-row',
            ));

            vc_remove_param('vc_row_inner', 'gap');
            vc_remove_param('vc_row_inner', 'equal_height');
            vc_remove_param('vc_row_inner', 'content_placement');
            vc_remove_param('vc_row_inner', 'columns_placement');
            vc_remove_param('vc_row_inner', 'el_class');
            vc_remove_param('vc_row_inner', 'el_id');
            vc_remove_param('vc_row_inner', 'css');

            vc_add_param('vc_row_inner', array(
                'param_name' => 'bg_color',
                'heading' => __('Background Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder,
            ));

            vc_add_param('vc_row_inner', array(
                'param_name' => 'bg_image',
                'heading' => __('Background Image', 'noo'),
                'type' => 'attach_image',
                'holder' => $param_holder
            ));
            vc_add_param('vc_row_inner', array(
                'param_name' => 'bg_color_overlay',
                'heading' => __('Background Color Overlay', 'noo'),
                'type' => 'colorpicker',
                'dependency' => array('element' => "bg_image", 'not_empty' => true),
                'holder' => $param_holder,
            ));
            vc_add_param('vc_row_inner', array(
                'param_name' => 'bg_image_repeat',
                'heading' => __('Background Image Repeat', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array(
                    '' => 'false'),
                'dependency' => array('element' => "bg_image", 'not_empty' => true)
            ));

            vc_add_param('vc_row_inner', array(
                'param_name' => 'parallax',
                'heading' => __('Parallax Background', 'noo'),
                'description' => __('Enable Parallax Background', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array(
                    '' => 'true'),
                'dependency' => array('element' => "bg_image", 'not_empty' => true)
            ));

            vc_add_param('vc_row_inner', array(
                'param_name' => 'parallax_no_mobile',
                'heading' => __('Disable Parallax on Mobile', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array(
                    '' => 'true'),
                'dependency' => array('element' => "bg_image", 'not_empty' => true)
            ));

            vc_add_param('vc_row_inner', array(
                'param_name' => 'parallax_velocity',
                'heading' => __('Parallax Velocity', 'noo'),
                'description' => __('The movement speed, value should be between 0.1 and 1.0', 'noo'),
                'type' => 'textfield',
                'holder' => $param_holder,
                'value' => '0.1',
                'dependency' => array('element' => "bg_image", 'not_empty' => true)
            ));

            vc_add_param('vc_row_inner', array(
                'param_name' => 'bg_video',
                'heading' => __('Background Video', 'noo'),
                'description' => __('Enable Background Video, it will override Background Color and Background Image', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array(
                    __('Yes', 'noo') => 'true'
                )
            ));

            vc_add_param('vc_row_inner', array(
                'param_name' => 'bg_video_url',
                'heading' => __('Video URL', 'noo'),
                'type' => 'textfield',
                'holder' => $param_holder,
                'dependency' => array('element' => "bg_video", 'value' => array('true'))
            ));

            vc_add_param('vc_row_inner', array(
                'param_name' => 'bg_video_poster',
                'heading' => __('Video Poster Image', 'noo'),
                'type' => 'attach_image',
                'holder' => $param_holder,
                'dependency' => array('element' => "bg_video", 'value' => array('true'))
            ));

            vc_add_param('vc_row_inner', array(
                'param_name' => 'inner_container',
                'heading' => __('Has Container', 'noo'),
                'description' => __('If enable, this row will be placed inside a container.', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array('' => 'true')
            ));

            vc_add_param('vc_row_inner', array(
                'param_name' => 'border',
                'heading' => __('Border', 'noo'),
                'description' => '',
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => array(
                    __('None', 'noo') => '',
                    __('Top', 'noo') => 'top',
                    __('Right', 'noo') => 'right',
                    __('Left', 'noo') => 'left',
                    __('Bottom', 'noo') => 'bottom',
                    __('Vertical', 'noo') => 'vertical',
                    __('Horizontal', 'noo') => 'horizontal',
                    __('All', 'noo') => 'all'
                )
            ));

            vc_add_param('vc_row_inner', array(
                'param_name' => 'padding_top',
                'heading' => __('Padding Top (px)', 'noo'),
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '20',
                'data_min' => '0',
                'data_max' => '100',
            ));

            vc_add_param('vc_row_inner', array(
                'param_name' => 'padding_bottom',
                'heading' => __('Padding Bottom (px)', 'noo'),
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '20',
                'data_min' => '0',
                'data_max' => '100',
            ));

            vc_add_param('vc_row_inner', array(
                'param_name' => $param_visibility_name,
                'heading' => $param_visibility_heading,
                'description' => $param_visibility_description,
                'type' => $param_visibility_type,
                'holder' => $param_visibility_holder,
                'value' => $param_visibility_value
            ));

            vc_add_param('vc_row_inner', array(
                'param_name' => $param_class_name,
                'heading' => $param_class_heading,
                'description' => $param_class_description,
                'type' => $param_class_type,
                'holder' => $param_class_holder
            ));
            vc_add_param('vc_row_inner', array(
                'param_name' => $param_id_name,
                'heading' => $param_id_heading,
                'description' => $param_id_description,
                'type' => $param_id_type,
                'holder' => $param_id_holder
            ));

            vc_add_param('vc_row_inner', array(
                'param_name' => $param_custom_style_name,
                'heading' => $param_custom_style_heading,
                'description' => $param_custom_style_description,
                'type' => $param_custom_style_type,
                'holder' => $param_custom_style_holder
            ));

            // [vc_column]
            // ============================
            vc_remove_param('vc_column', 'el_class');
            vc_remove_param('vc_column', 'css');

            vc_add_param('vc_column', array(
                'param_name' => 'alignment',
                'heading' => __('Text Alignment', 'noo'),
                'description' => '',
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => array(
                    __('None', 'noo') => '',
                    __('Left', 'noo') => 'left',
                    __('Center', 'noo') => 'center',
                    __('Right', 'noo') => 'right',
                )
            ));

            vc_add_param('vc_column', array(
                'param_name' => 'animation',
                'heading' => __('Select Animation', 'noo'),
                'description' => __('Choose animation effect for this column.', 'noo'),
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => $param_animation_value
            ));

            vc_add_param('vc_column', array(
                'param_name' => 'animation_offset',
                'heading' => __('Animation Offset', 'noo'),
                'description' => '',
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '40',
                'data_min' => '0',
                'data_max' => '200',
                'data_step' => '10',
                'dependency' => array('element' => "animation", 'not_empty' => true)
            ));

            vc_add_param('vc_column', array(
                'param_name' => 'animation_delay',
                'heading' => __('Animation Delay (ms)', 'noo'),
                'description' => '',
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '0',
                'data_min' => '0',
                'data_max' => '3000',
                'data_step' => '50',
                'dependency' => array('element' => "animation", 'not_empty' => true)
            ));

            vc_add_param('vc_column', array(
                'param_name' => 'animation_duration',
                'heading' => __('Animation Duration (ms)', 'noo'),
                'description' => '',
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '1000',
                'data_min' => '0',
                'data_max' => '3000',
                'data_step' => '50',
                'dependency' => array('element' => "animation", 'not_empty' => true)
            ));

            vc_add_param('vc_column', array(
                'param_name' => $param_visibility_name,
                'heading' => $param_visibility_heading,
                'description' => $param_visibility_description,
                'type' => $param_visibility_type,
                'holder' => $param_visibility_holder,
                'value' => $param_visibility_value
            ));

            vc_add_param('vc_column', array(
                'param_name' => $param_class_name,
                'heading' => $param_class_heading,
                'description' => $param_class_description,
                'type' => $param_class_type,
                'holder' => $param_class_holder
            ));

            vc_add_param('vc_column', array(
                'param_name' => $param_custom_style_name,
                'heading' => $param_custom_style_heading,
                'description' => $param_custom_style_description,
                'type' => $param_custom_style_type,
                'holder' => $param_custom_style_holder
            ));

            // [vc_column_inner]
            // ============================
            vc_remove_param('vc_column_inner', 'el_class');
            vc_remove_param('vc_column_inner', 'css');

            vc_add_param('vc_column', array(
                'param_name' => 'alignment',
                'heading' => __('Text Alignment', 'noo'),
                'description' => '',
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => array(
                    __('None', 'noo') => '',
                    __('Left', 'noo') => 'left',
                    __('Center', 'noo') => 'center',
                    __('Right', 'noo') => 'right',
                )
            ));

            vc_add_param('vc_column_inner', array(
                'param_name' => 'animation',
                'heading' => __('Select Animation', 'noo'),
                'description' => __('Choose animation effect for this column.', 'noo'),
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => $param_animation_value
            ));

            vc_add_param('vc_column_inner', array(
                'param_name' => 'animation_offset',
                'heading' => __('Animation Offset', 'noo'),
                'description' => '',
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '40',
                'data_min' => '0',
                'data_max' => '200',
                'data_step' => '10',
                'dependency' => array('element' => "animation", 'not_empty' => true)
            ));

            vc_add_param('vc_column_inner', array(
                'param_name' => 'animation_delay',
                'heading' => __('Animation Delay (ms)', 'noo'),
                'description' => '',
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '0',
                'data_min' => '0',
                'data_max' => '3000',
                'data_step' => '50',
                'dependency' => array('element' => "animation", 'not_empty' => true)
            ));

            vc_add_param('vc_column_inner', array(
                'param_name' => 'animation_duration',
                'heading' => __('Animation Duration (ms)', 'noo'),
                'description' => '',
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '1000',
                'data_min' => '0',
                'data_max' => '3000',
                'data_step' => '50',
                'dependency' => array('element' => "animation", 'not_empty' => true)
            ));

            vc_add_param('vc_column_inner', array(
                'param_name' => $param_visibility_name,
                'heading' => $param_visibility_heading,
                'description' => $param_visibility_description,
                'type' => $param_visibility_type,
                'holder' => $param_visibility_holder,
                'value' => $param_visibility_value
            ));

            vc_add_param('vc_column_inner', array(
                'param_name' => $param_class_name,
                'heading' => $param_class_heading,
                'description' => $param_class_description,
                'type' => $param_class_type,
                'holder' => $param_class_holder
            ));

            vc_add_param('vc_column_inner', array(
                'param_name' => $param_custom_style_name,
                'heading' => $param_custom_style_heading,
                'description' => $param_custom_style_description,
                'type' => $param_custom_style_type,
                'holder' => $param_custom_style_holder
            ));

            // [vc_separator]
            // ============================
            vc_map_update('vc_separator', array(
                'category' => $category_base_element,
                'weight' => 980,
                'class' => 'noo-vc-element noo-vc-element-separator',
                'icon' => 'noo-vc-icon-separator',
            ));

            vc_remove_param('vc_separator', 'color');
            vc_remove_param('vc_separator', 'accent_color');
            vc_remove_param('vc_separator', 'style');
            vc_remove_param('vc_separator', 'el_width');
            vc_remove_param('vc_separator', 'el_class');

            vc_add_param('vc_separator', array(
                'param_name' => 'type',
                'heading' => __('Type', 'noo'),
                'description' => __('Choose type of this seperator.', 'noo'),
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => array(
                    __('Line', 'noo') => 'line',
                    __('Line with Text', 'noo') => 'line-with-text'
                )
            ));

            vc_add_param('vc_separator', array(
                'param_name' => 'title',
                'heading' => __('Title', 'noo'),
                'description' => '',
                'type' => 'textfield',
                'holder' => $param_holder,
                'dependency' => array('element' => "type", 'value' => array('line-with-text'))
            ));

            vc_add_param('vc_separator', array(
                'param_name' => 'size',
                'heading' => __('Size', 'noo'),
                'description' => '',
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => array(
                    __('Full-Width', 'noo') => 'fullwidth',
                    __('Half', 'noo') => 'half'
                )
            ));

            vc_add_param('vc_separator', array(
                'param_name' => 'position',
                'heading' => __('Position', 'noo'),
                'description' => '',
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => array(
                    __('Center', 'noo') => 'center',
                    __('Left', 'noo') => 'left',
                    __('Right', 'noo') => 'right'
                )
            ));

            vc_add_param('vc_separator', array(
                'param_name' => 'color',
                'heading' => __('Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder,
                'value' => '2'
            ));

            vc_add_param('vc_separator', array(
                'param_name' => 'thickness',
                'heading' => __('LIne Thickness (px)', 'noo'),
                'description' => '',
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '2',
                'data_min' => '0',
                'data_max' => '10',
            ));

            vc_add_param('vc_separator', array(
                'param_name' => 'space_before',
                'heading' => __('Space Before (px)', 'noo'),
                'description' => '',
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '20',
                'data_min' => '0',
                'data_max' => '100',
                'data_step' => '5',
            ));

            vc_add_param('vc_separator', array(
                'param_name' => 'space_after',
                'heading' => __('Space After (px)', 'noo'),
                'description' => '',
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '20',
                'data_min' => '0',
                'data_max' => '100',
                'data_step' => '5',
            ));

            vc_add_param('vc_separator', array(
                'param_name' => $param_visibility_name,
                'heading' => $param_visibility_heading,
                'description' => $param_visibility_description,
                'type' => $param_visibility_type,
                'holder' => $param_visibility_holder,
                'value' => $param_visibility_value
            ));

            vc_add_param('vc_separator', array(
                'param_name' => $param_class_name,
                'heading' => $param_class_heading,
                'description' => $param_class_description,
                'type' => $param_class_type,
                'holder' => $param_class_holder
            ));

            vc_add_param('vc_separator', array(
                'param_name' => $param_custom_style_name,
                'heading' => $param_custom_style_heading,
                'description' => $param_custom_style_description,
                'type' => $param_custom_style_type,
                'holder' => $param_custom_style_holder
            ));

            //
            // Animation.
            //
            vc_map(
                array(
                    'base' => 'animation',
                    'name' => __('Animation Block', 'noo'),
                    'weight' => 985,
                    'class' => 'noo-vc-element noo-vc-element-animation',
                    'icon' => 'noo-vc-icon-animation',
                    'category' => $category_base_element,
                    'description' => __('Enable animation for serveral elements.', 'noo'),
                    'as_parent' => array('only' => 'vc_column_text,icon,icon_list,label,code,vc_button,vc_pie,vc_message,vc_widget_sidebar,vc_single_image,vc_gmaps,gap'),
                    'content_element' => true,
                    'js_view' => 'VcColumnView',
                    'show_settings_on_create' => false,
                    'params' => array()
                )
            );

            vc_add_param('animation', array(
                'param_name' => 'animation',
                'heading' => __('Select Animation', 'noo'),
                'description' => __('Choose animation effect for this column.', 'noo'),
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => $param_animation_value
            ));

            vc_add_param('animation', array(
                'param_name' => 'animation_offset',
                'heading' => __('Animation Offset', 'noo'),
                'description' => '',
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '40',
                'data_min' => '0',
                'data_max' => '200',
                'data_step' => '10',
                'dependency' => array('element' => "animation", 'not_empty' => true)
            ));

            vc_add_param('animation', array(
                'param_name' => 'animation_delay',
                'heading' => __('Animation Delay (ms)', 'noo'),
                'description' => '',
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '0',
                'data_min' => '0',
                'data_max' => '3000',
                'data_step' => '50',
                'dependency' => array('element' => "animation", 'not_empty' => true)
            ));

            vc_add_param('animation', array(
                'param_name' => 'animation_duration',
                'heading' => __('Animation Duration (ms)', 'noo'),
                'description' => '',
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '1000',
                'data_min' => '0',
                'data_max' => '3000',
                'data_step' => '50',
                'dependency' => array('element' => "animation", 'not_empty' => true)
            ));
            vc_add_param('animation', array(
                'param_name' => $param_class_name,
                'heading' => $param_class_heading,
                'description' => $param_class_description,
                'type' => $param_class_type,
                'holder' => $param_class_holder
            ));
            vc_add_param('animation', array(
                'param_name' => $param_custom_style_name,
                'heading' => $param_custom_style_heading,
                'description' => $param_custom_style_description,
                'type' => $param_custom_style_type,
                'holder' => $param_custom_style_holder
            ));

            // [gap]
            // ============================
            vc_map(
                array(
                    'base' => 'gap',
                    'name' => __('Gap', 'noo'),
                    'weight' => 960,
                    'class' => 'noo-vc-element noo-vc-element-gap',
                    'icon' => 'noo-vc-icon-gap',
                    'category' => $category_base_element,
                    'description' => __('Insert a vertical gap in your content', 'noo'),
                    'params' => array(
                        array(
                            'param_name' => 'size',
                            'heading' => __('Size (px)', 'noo'),
                            'description' => __('Enter in the size of your gap.', 'noo'),
                            'type' => 'ui_slider',
                            'holder' => $param_holder,
                            'value' => '50',
                            'data_min' => '20',
                            'data_max' => '200',
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        ),
                    )
                )
            );

            // [clear]
            // ============================
            vc_map(
                array(
                    'base' => 'clear',
                    'name' => __('Clear', 'noo'),
                    'weight' => 950,
                    'class' => 'noo-vc-element noo-vc-element-clear',
                    'icon' => 'noo-vc-icon-clear',
                    'category' => $category_base_element,
                    'description' => __('Clear help you fix the normal break style', 'noo'),
                    'params' => array(
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        ),
                    )
                )
            );

        }

        add_action('init', 'noo_vc_base_element');

        //
        // Extend container class (parents).
        //
        if (class_exists('WPBakeryShortCodesContainer')) {
            class WPBakeryShortCode_Animation extends WPBakeryShortCodesContainer
            {
            }
        }

    endif;

    if (!function_exists('noo_vc_typography')) :

        function noo_vc_typography()
        {

            //
            // Variables.
            //
            $category_base_element = __('Base Elements', 'noo');
            $category_typography = __('Typography', 'noo');
            $category_content = __('Content', 'noo');
            $category_wp_content = __('WordPress Content', 'noo');
            $category_media = __('Media', 'noo');
            $category_custom = __('Custom', 'noo');

            $param_content_name = 'content';
            $param_content_heading = __('Text', 'noo');
            $param_content_description = __('Enter your text.', 'noo');
            $param_content_type = 'textarea_html';
            $param_content_holder = 'div';
            $param_content_value = '';

            $param_visibility_name = 'visibility';
            $param_visibility_heading = __('Visibility', 'noo');
            $param_visibility_description = '';
            $param_visibility_type = 'dropdown';
            $param_visibility_holder = 'div';
            $param_visibility_value = array(
                __('All Devices', 'noo') => "all",
                __('Hidden Phone', 'noo') => "hidden-phone",
                __('Hidden Tablet', 'noo') => "hidden-tablet",
                __('Hidden PC', 'noo') => "hidden-pc",
                __('Visible Phone', 'noo') => "visible-phone",
                __('Visible Tablet', 'noo') => "visible-tablet",
                __('Visible PC', 'noo') => "visible-pc",
            );

            $param_class_name = 'class';
            $param_class_heading = __('Class', 'noo');
            $param_class_description = __('(Optional) Enter a unique class name.', 'noo');
            $param_class_type = 'textfield';
            $param_class_holder = 'div';

            $param_custom_style_name = 'custom_style';
            $param_custom_style_heading = __('Custom Style', 'noo');
            $param_custom_style_description = __('(Optional) Enter inline CSS.', 'noo');
            $param_custom_style_type = 'textfield';
            $param_custom_style_holder = 'div';

            $param_icon_value = array();

            $param_social_icon_value = array(
                '' => '',
                __('ADN', 'noo') => 'fa-adn',
                __('Android', 'noo') => 'fa-android',
                __('Apple', 'noo') => 'fa-apple',
                __('Bitbucket', 'noo') => 'fa-bitbucket',
                __('Bitbucket-Sign', 'noo') => 'fa-bitbucket-sign',
                __('Bitcoin', 'noo') => 'fa-bitcoin',
                __('BTC', 'noo') => 'fa-btc',
                __('CSS3', 'noo') => 'fa-css3',
                __('Dribbble', 'noo') => 'fa-dribbble',
                __('Dropbox', 'noo') => 'fa-dropbox',
                __('Facebook', 'noo') => 'fa-facebook',
                __('Facebook-Sign', 'noo') => 'fa-facebook-sign',
                __('Flickr', 'noo') => 'fa-flickr',
                __('Foursquare', 'noo') => 'fa-foursquare',
                __('GitHub', 'noo') => 'fa-github',
                __('GitHub-Alt', 'noo') => 'fa-github-alt',
                __('GitHub-Sign', 'noo') => 'fa-github-sign',
                __('Gittip', 'noo') => 'fa-gittip',
                __('Google Plus', 'noo') => 'fa-google-plus',
                __('Google Plus-Sign', 'noo') => 'fa-google-plus-sign',
                __('HTML5', 'noo') => 'fa-html5',
                __('Instagram', 'noo') => 'fa-instagram',
                __('LinkedIn', 'noo') => 'fa-linkedin',
                __('LinkedIn-Sign', 'noo') => 'fa-linkedin-sign',
                __('Linux', 'noo') => 'fa-linux',
                __('MaxCDN', 'noo') => 'fa-maxcdn',
                __('Pinterest', 'noo') => 'fa-pinterest',
                __('Pinterest-Sign', 'noo') => 'fa-pinterest-sign',
                __('Renren', 'noo') => 'fa-renren',
                __('Skype', 'noo') => 'fa-skype',
                __('StackExchange', 'noo') => 'fa-stackexchange',
                __('Trello', 'noo') => 'fa-trello',
                __('Tumblr', 'noo') => 'fa-tumblr',
                __('Tumblr-Sign', 'noo') => 'fa-tumblr-sign',
                __('Twitter', 'noo') => 'fa-twitter',
                __('Twitter-Sign', 'noo') => 'fa-twitter-sign',
                __('VK', 'noo') => 'fa-vk',
                __('Weibo', 'noo') => 'fa-weibo',
                __('Windows', 'noo') => 'fa-windows',
                __('Xing', 'noo') => 'fa-xing',
                __('Xing-Sign', 'noo') => 'fa-xing-sign',
                __('YouTube', 'noo') => 'fa-youtube',
                __('YouTube Play', 'noo') => 'fa-youtube-play',
                __('YouTube-Sign', 'noo') => 'fa-youtube-sign'
            );

            $param_holder = 'div';

            // [vc_column_text] ( Text Block )
            // ============================
            vc_map_update('vc_column_text', array(
                'category' => $category_typography,
                'class' => 'noo-vc-element noo-vc-element-text_block',
                'icon' => 'noo-vc-icon-text_block',
                'weight' => 890
            ));

            vc_remove_param('vc_column_text', 'css_animation');
            vc_remove_param('vc_column_text', 'el_class');
            vc_remove_param('vc_column_text', 'css');

            vc_add_param('vc_column_text', array(
                'param_name' => $param_visibility_name,
                'heading' => $param_visibility_heading,
                'description' => $param_visibility_description,
                'type' => $param_visibility_type,
                'holder' => $param_visibility_holder,
                'value' => $param_visibility_value
            ));

            vc_add_param('vc_column_text', array(
                'param_name' => $param_class_name,
                'heading' => $param_class_heading,
                'description' => $param_class_description,
                'type' => $param_class_type,
                'holder' => $param_class_holder
            ));

            vc_add_param('vc_column_text', array(
                'param_name' => $param_custom_style_name,
                'heading' => $param_custom_style_heading,
                'description' => $param_custom_style_description,
                'type' => $param_custom_style_type,
                'holder' => $param_custom_style_holder
            ));

            // [vc_button]
            // ============================
            vc_map_update('vc_button', array(
                'category' => $category_typography,
                'weight' => 880,
                'class' => 'noo-vc-element noo-vc-element-button',
                'icon' => 'noo-vc-icon-button',
            ));

            vc_remove_param('vc_button', 'color');
            vc_remove_param('vc_button', 'icon');
            vc_remove_param('vc_button', 'size');
            vc_remove_param('vc_button', 'el_class');

            vc_add_param('vc_button', array(
                'param_name' => 'target',
                'heading' => __('Open in new tab', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array('' => 'true'),
            ));

            vc_add_param('vc_button', array(
                'param_name' => 'size',
                'heading' => __('Size', 'noo'),
                'description' => '',
                'type' => 'dropdown',
                'holder' => $param_holder,
                'std' => 'medium',
                'value' => array(
                    __('Extra Small', 'noo') => 'x_small',
                    __('Small', 'noo') => 'small',
                    __('Medium', 'noo') => 'medium',
                    __('Large', 'noo') => 'large',
                    __('Custom', 'noo') => 'custom'
                )
            ));

            vc_add_param('vc_button', array(
                'param_name' => 'fullwidth',
                'heading' => __('Forge Full-Width', 'noo'),
                'description' => '',
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array(
                    '' => 'true'
                )
            ));

            vc_add_param('vc_button', array(
                'param_name' => 'vertical_padding',
                'heading' => __('Vertical Padding (px)', 'noo'),
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '10',
                'data_min' => '0',
                'data_max' => '50',
                'dependency' => array('element' => 'size', 'value' => array('custom'))
            ));

            vc_add_param('vc_button', array(
                'param_name' => 'horizontal_padding',
                'heading' => __('Horizontal Padding (px)', 'noo'),
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '10',
                'data_min' => '0',
                'data_max' => '50',
                'dependency' => array('element' => 'size', 'value' => array('custom'))
            ));

            vc_add_param('vc_button', array(
                'param_name' => 'icon',
                'heading' => __('Icon', 'noo'),
                'type' => 'iconpicker',
                'holder' => $param_holder,
            ));

            vc_add_param('vc_button', array(
                'param_name' => 'icon_right',
                'heading' => __('Right Icon', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array('' => 'true'),
                'dependency' => array('element' => 'icon', 'not_empty' => true)
            ));

            vc_add_param('vc_button', array(
                'param_name' => 'icon_only',
                'heading' => __('Show only Icon', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array('' => 'true'),
                'dependency' => array('element' => 'icon', 'not_empty' => true)
            ));

            vc_add_param('vc_button', array(
                'param_name' => 'icon_color',
                'heading' => __('Icon Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder,
                'value' => '',
                'dependency' => array('element' => 'icon', 'not_empty' => true)
            ));

            vc_add_param('vc_button', array(
                'param_name' => 'shape',
                'heading' => __('Shape', 'noo'),
                'description' => '',
                'type' => 'dropdown',
                'std' => 'rounded',
                'holder' => $param_holder,
                'value' => array(
                    __('Square', 'noo') => 'square',
                    __('Rounded', 'noo') => 'rounded',
                    __('Pill', 'noo') => 'pill',
                )
            ));

            vc_add_param('vc_button', array(
                'param_name' => 'style',
                'heading' => __('Style', 'noo'),
                'description' => '',
                'type' => 'dropdown',
                'std' => '',
                'holder' => $param_holder,
                'value' => array(
                    __('3D Pressable', 'noo') => 'pressable',
                    __('Metro', 'noo') => 'metro',
                    __('Blank', 'noo') => '',
                )
            ));

            vc_add_param('vc_button', array(
                'param_name' => 'skin',
                'heading' => __('Skin', 'noo'),
                'description' => '',
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => array(
                    __('Default', 'noo') => 'default',
                    __('Primary', 'noo') => 'primary',
                    __('Custom', 'noo') => 'custom',
                    __('White', 'noo') => 'white',
                    __('Black', 'noo') => 'black',
                    __('Success', 'noo') => 'success',
                    __('Info', 'noo') => 'info',
                    __('Warning', 'noo') => 'warning',
                    __('Danger', 'noo') => 'danger',
                    __('Link', 'noo') => 'link',
                )
            ));

            vc_add_param('vc_button', array(
                'param_name' => 'text_color',
                'heading' => __('Text Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder,
                'value' => '',
                'dependency' => array('element' => 'skin', 'value' => array('custom'))
            ));

            vc_add_param('vc_button', array(
                'param_name' => 'hover_text_color',
                'heading' => __('Hover Text Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder,
                'value' => '',
                'dependency' => array('element' => 'skin', 'value' => array('custom'))
            ));

            vc_add_param('vc_button', array(
                'param_name' => 'bg_color',
                'heading' => __('Background Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder,
                'value' => '',
                'dependency' => array('element' => 'skin', 'value' => array('custom'))
            ));

            vc_add_param('vc_button', array(
                'param_name' => 'hover_bg_color',
                'heading' => __('Hover Background Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder,
                'value' => '',
                'dependency' => array('element' => 'skin', 'value' => array('custom'))
            ));

            vc_add_param('vc_button', array(
                'param_name' => 'border_color',
                'heading' => __('Border Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder,
                'value' => '',
                'dependency' => array('element' => 'skin', 'value' => array('custom'))
            ));

            vc_add_param('vc_button', array(
                'param_name' => 'hover_border_color',
                'heading' => __('Hover Border Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder,
                'value' => '',
                'dependency' => array('element' => 'skin', 'value' => array('custom'))
            ));

            vc_add_param('vc_button', array(
                'param_name' => $param_visibility_name,
                'heading' => $param_visibility_heading,
                'description' => $param_visibility_description,
                'type' => $param_visibility_type,
                'holder' => $param_visibility_holder,
                'value' => $param_visibility_value
            ));

            vc_add_param('vc_button', array(
                'param_name' => $param_class_name,
                'heading' => $param_class_heading,
                'description' => $param_class_description,
                'type' => $param_class_type,
                'holder' => $param_class_holder
            ));

            vc_add_param('vc_button', array(
                'param_name' => $param_custom_style_name,
                'heading' => $param_custom_style_heading,
                'description' => $param_custom_style_description,
                'type' => $param_custom_style_type,
                'holder' => $param_custom_style_holder
            ));

            // [dropcap]
            // ============================
            vc_map(
                array(
                    'base' => 'dropcap',
                    'name' => __('Dropcap', 'noo'),
                    'weight' => 860,
                    'class' => 'noo-vc-element noo-vc-element-dropcap',
                    'icon' => 'noo-vc-icon-dropcap',
                    'category' => $category_typography,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'letter',
                            'heading' => __('Letter', 'noo'),
                            'description' => '',
                            'type' => 'textfield',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'color',
                            'heading' => __('Letter Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'style',
                            'heading' => __('Style', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Transparent', 'noo') => 'transparent',
                                __('Filled Square', 'noo') => 'square',
                                __('Filled Circle', 'noo') => 'circle',
                            )
                        ),
                        array(
                            'param_name' => 'bg_color',
                            'heading' => __('Background Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'style', 'value' => array('square', 'circle'))
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        ),
                    )
                )
            );

            // [quote]
            // ============================
            vc_map(
                array(
                    'base' => 'quote',
                    'name' => __('Quote', 'noo'),
                    'weight' => 850,
                    'class' => 'noo-vc-element noo-vc-element-quote',
                    'icon' => 'noo-vc-icon-quote',
                    'category' => $category_typography,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => $param_content_name,
                            'heading' => __('Quote', 'noo'),
                            'description' => $param_content_description,
                            'type' => $param_content_type,
                            'holder' => $param_content_holder,
                            'value' => $param_content_value
                        ),
                        array(
                            'param_name' => 'cite',
                            'heading' => __('Cite', 'noo'),
                            'description' => __('Who originally said this.', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'type',
                            'heading' => __('Type', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Block Quote', 'noo') => 'block',
                                __('Pull Quote', 'noo') => 'pull',
                            )
                        ),
                        array(
                            'param_name' => 'alignment',
                            'heading' => __('Alignment', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Left', 'noo') => 'left',
                                __('Center', 'noo') => 'center',
                                __('Right', 'noo') => 'right',
                            )
                        ),
                        array(
                            'param_name' => 'position',
                            'heading' => __('Position', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Left', 'noo') => 'left',
                                __('Right', 'noo') => 'right',
                            ),
                            'dependency' => array('element' => 'type', 'value' => array('pull'))
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        ),
                    )
                )
            );

            // [icon]
            // ============================
            vc_map(
                array(
                    'base' => 'icon',
                    'name' => __('Icon', 'noo'),
                    'weight' => 840,
                    'class' => 'noo-vc-element noo-vc-element-icon',
                    'icon' => 'noo-vc-icon-icon',
                    'category' => $category_typography,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'icon',
                            'heading' => __('Icon', 'noo'),
                            'description' => '',
                            'type' => 'iconpicker',
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'href',
                            'heading' => __('Icon URL', 'noo'),
                            'description' => '',
                            'type' => 'textfield',
                            'holder' => $param_holder),
                        array(
                            'param_name' => 'size',
                            'heading' => __('Size', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Normal', 'noo') => '',
                                __('Large', 'noo') => 'lg',
                                __('Double', 'noo') => '2x',
                                __('Triple', 'noo') => '3x',
                                __('Quadruple', 'noo') => '4x',
                                __('Quintuple', 'noo') => '5x',
                                __('Custom', 'noo') => 'custom',
                            )
                        ),
                        array(
                            'param_name' => 'custom_size',
                            'heading' => __('Custom Size', 'noo'),
                            'description' => '',
                            'type' => 'ui_slider',
                            'holder' => $param_holder,
                            'value' => '50',
                            'data_min' => '10',
                            'data_max' => '200',
                            'dependency' => array('element' => 'size', 'value' => array('custom'))
                        ),
                        array(
                            'param_name' => 'icon_color',
                            'heading' => __('Icon Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'hover_icon_color',
                            'heading' => __('Hover Icon Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'shape',
                            'heading' => __('Icon Shape', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Circle', 'noo') => 'circle',
                                __('Square', 'noo') => 'square',
                            )
                        ),
                        array(
                            'param_name' => 'style',
                            'heading' => __('Style', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Simple', 'noo') => 'simple',
                                __('Filled Stack', 'noo') => 'stack_filled',
                                __('Bordered Stack', 'noo') => 'stack_bordered',
                                __('Custom', 'noo') => 'custom',
                            )
                        ),
                        array(
                            'param_name' => 'bg_color',
                            'heading' => __('Background Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'style', 'value' => array('custom'))
                        ),
                        array(
                            'param_name' => 'hover_bg_color',
                            'heading' => __('Hover Background Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'style', 'value' => array('custom'))
                        ),
                        array(
                            'param_name' => 'border_color',
                            'heading' => __('Border Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'style', 'value' => array('custom'))
                        ),
                        array(
                            'param_name' => 'hover_border_color',
                            'heading' => __('Hover Border Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'style', 'value' => array('custom'))
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        ),
                    )
                )
            );

            // [social_icon]
            // ============================
            vc_map(
                array(
                    'base' => 'social_icon',
                    'name' => __('Social Icon', 'noo'),
                    'weight' => 835,
                    'class' => 'noo-vc-element noo-vc-element-social_icon',
                    'icon' => 'noo-vc-icon-social_icon',
                    'category' => $category_typography,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'icon',
                            'heading' => __('Select Icon', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => $param_social_icon_value
                        ),
                        array(
                            'param_name' => 'href',
                            'heading' => __('Social Profile URL', 'noo'),
                            'description' => '',
                            'type' => 'textfield',
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'target',
                            'heading' => __('Open in New Tab', 'noo'),
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array(
                                '' => 'true'),
                        ),
                        array(
                            'param_name' => 'size',
                            'heading' => __('Size', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Normal', 'noo') => '',
                                __('Large', 'noo') => 'large',
                                __('Double', 'noo') => '2x',
                                __('Triple', 'noo') => '3x',
                                __('Quadruple', 'noo') => '4x',
                                __('Quintuple', 'noo') => '5x',
                                __('Custom', 'noo') => 'custom',
                            )
                        ),
                        array(
                            'param_name' => 'custom_size',
                            'heading' => __('Custom Size', 'noo'),
                            'description' => '',
                            'type' => 'ui_slider',
                            'holder' => $param_holder,
                            'value' => '50',
                            'data_min' => '10',
                            'data_max' => '200',
                            'dependency' => array('element' => 'size', 'value' => array('custom'))
                        ),
                        array(
                            'param_name' => 'icon_color',
                            'heading' => __('Icon Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'hover_icon_color',
                            'heading' => __('Hover Icon Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'shape',
                            'heading' => __('Icon Shape', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Circle', 'noo') => 'circle',
                                __('Square', 'noo') => 'square',
                            )
                        ),
                        array(
                            'param_name' => 'style',
                            'heading' => __('Style', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Simple', 'noo') => 'simple',
                                __('Filled Stack', 'noo') => 'stack_filled',
                                __('Bordered Stack', 'noo') => 'stack_bordered',
                                __('Custom', 'noo') => 'custom',
                            )
                        ),
                        array(
                            'param_name' => 'bg_color',
                            'heading' => __('Background Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'style', 'value' => array('custom'))
                        ),
                        array(
                            'param_name' => 'hover_bg_color',
                            'heading' => __('Hover Background Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'style', 'value' => array('custom'))
                        ),
                        array(
                            'param_name' => 'border_color',
                            'heading' => __('Border Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'style', 'value' => array('custom'))
                        ),
                        array(
                            'param_name' => 'hover_border_color',
                            'heading' => __('Hover Border Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'style', 'value' => array('custom'))
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [icon_list]
            // ============================
            vc_map(
                array(
                    'base' => 'icon_list',
                    'name' => __('Icon List', 'noo'),
                    'weight' => 830,
                    'class' => 'noo-vc-element noo-vc-element-icon_list',
                    'icon' => 'noo-vc-icon-icon_list',
                    'category' => $category_typography,
                    'description' => '',
                    'show_settings_on_create' => false,
                    'as_parent' => array('only' => 'icon_list_item'),
                    'content_element' => true,
                    'js_view' => 'VcColumnView',
                    'params' => array(
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        ),
                    )
                )
            );

            // [icon_list_item]
            // ============================
            vc_map(
                array(
                    'base' => 'icon_list_item',
                    'name' => __('Icon List Item', 'noo'),
                    'weight' => 825,
                    'class' => 'noo-vc-element noo-vc-element-icon_list_item',
                    'icon' => 'noo-vc-icon-icon_list_item',
                    'category' => $category_typography,
                    'description' => '',
                    'as_child' => array('only' => 'icon_list'),
                    'content_element' => true,
                    'params' => array(
                        array(
                            'param_name' => 'icon',
                            'heading' => __('Icon', 'noo'),
                            'description' => '',
                            'type' => 'iconpicker',
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'icon_size',
                            'heading' => __('Icon Size (px)', 'noo'),
                            'description' => __('Leave it empty or 0 to use the base size of your theme.', 'noo'),
                            'type' => 'ui_slider',
                            'holder' => $param_holder,
                            'value' => '',
                            'data_min' => '0',
                            'data_max' => '60',
                        ),
                        array(
                            'param_name' => 'icon_color',
                            'heading' => __('Icon Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'text_same_size',
                            'heading' => __('Text has Same Size as Icon', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Yes', 'noo') => 'true',
                                __('No', 'noo') => 'false'
                            ),
                        ),
                        array(
                            'param_name' => 'text_size',
                            'heading' => __('Text Size (px)', 'noo'),
                            'description' => __('Leave it empty or 0 to use the base size of your theme.', 'noo'),
                            'type' => 'ui_slider',
                            'holder' => $param_holder,
                            'value' => '',
                            'data_min' => '0',
                            'data_max' => '60',
                            'dependency' => array('element' => 'text_same_size', 'value' => array('false'))
                        ),
                        array(
                            'param_name' => 'text_same_color',
                            'heading' => __('Text has Same Color as Icon', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Yes', 'noo') => 'true',
                                __('No', 'noo') => 'false'
                            ),
                        ),
                        array(
                            'param_name' => 'text_color',
                            'heading' => __('Text Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'text_same_color', 'value' => array('false'))
                        ),
                        array(
                            'param_name' => $param_content_name,
                            'heading' => $param_content_heading,
                            'description' => $param_content_description,
                            'type' => $param_content_type,
                            'holder' => $param_content_holder,
                            'value' => $param_content_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [label]
            // ============================
            vc_map(
                array(
                    'base' => 'label',
                    'name' => __('Label', 'noo'),
                    'weight' => 820,
                    'class' => 'noo-vc-element noo-vc-element-label',
                    'icon' => 'noo-vc-icon-label',
                    'category' => $category_typography,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'word',
                            'heading' => __('Word', 'noo'),
                            'description' => '',
                            'type' => 'textfield',
                            'holder' => $param_holder,
                        )
                    , array(
                            'param_name' => 'color',
                            'heading' => __('Color', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Default', 'noo') => 'default',
                                __('Custom', 'noo') => 'custom',
                                __('Primary', 'noo') => 'primary',
                                __('Success', 'noo') => 'success',
                                __('Info', 'noo') => 'info',
                                __('Warning', 'noo') => 'warning',
                                __('Danger', 'noo') => 'danger',
                            )
                        ),
                        array(
                            'param_name' => 'custom_color',
                            'heading' => __('Custom Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'color', 'value' => array('custom'))
                        ),
                        array(
                            'param_name' => 'rounded',
                            'heading' => __('Rounded Corner', 'noo'),
                            'description' => '',
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true')
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        ),
                    )
                )
            );

            // [code]
            // ============================
            vc_map(
                array(
                    'base' => 'code',
                    'name' => __('Code Block', 'noo'),
                    'weight' => 810,
                    'class' => 'noo-vc-element noo-vc-element-code',
                    'icon' => 'noo-vc-icon-code',
                    'category' => $category_typography,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => $param_content_name,
                            'heading' => __('Put your code here', 'noo'),
                            'description' => $param_content_description,
                            'type' => $param_content_type,
                            'holder' => $param_content_holder,
                            'value' => $param_content_value
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        ),
                    )
                )
            );

        }

        add_action('init', 'noo_vc_typography');

        //
        // Extend container class (parents).
        //
        if (class_exists('WPBakeryShortCodesContainer')) {
            class WPBakeryShortCode_Icon_List extends WPBakeryShortCodesContainer
            {
            }
        }

        //
        // Extend item class (children).
        //
        if (class_exists('WPBakeryShortCode')) {
            class WPBakeryShortCode_Icon_List_Item extends WPBakeryShortCode
            {
            }
        }

    endif;

    if (!function_exists('noo_vc_citilights')) {
        function noo_vc_citilights()
        {
            //
            // Variables.
            //
            $category_name = __('CitiLights', 'noo');

            $param_visibility_name = 'visibility';
            $param_visibility_heading = __('Visibility', 'noo');
            $param_visibility_description = '';
            $param_visibility_type = 'dropdown';
            $param_visibility_holder = 'div';
            $param_visibility_value = array(
                __('All Devices', 'noo') => "all",
                __('Hidden Phone', 'noo') => "hidden-phone",
                __('Hidden Tablet', 'noo') => "hidden-tablet",
                __('Hidden PC', 'noo') => "hidden-pc",
                __('Visible Phone', 'noo') => "visible-phone",
                __('Visible Tablet', 'noo') => "visible-tablet",
                __('Visible PC', 'noo') => "visible-pc",
            );

            $param_class_name = 'class';
            $param_class_heading = __('Class', 'noo');
            $param_class_description = __('(Optional) Enter a unique class name.', 'noo');
            $param_class_type = 'textfield';
            $param_class_holder = 'div';

            $param_custom_style_name = 'custom_style';
            $param_custom_style_heading = __('Custom Style', 'noo');
            $param_custom_style_description = __('(Optional) Enter inline CSS.', 'noo');
            $param_custom_style_type = 'textfield';
            $param_custom_style_holder = 'div';

            $param_holder = 'div';

            // Recent Properties
            // ============================

            //type
            $property_categories = array();
            $property_categories[''] = '';
            foreach ((array)get_terms('property_category', array('hide_empty' => 0)) as $category) {
                $property_categories[esc_html($category->name)] = $category->slug;
            }
            //status
            $property_status = array();
            $property_status[''] = '';
            foreach ((array)get_terms('property_status', array('hide_empty' => 0)) as $status) {
                $property_status[esc_html($status->name)] = $status->term_id;
            }
            //label
            $property_labels = array();
            $property_labels[''] = '';
            foreach ((array)get_terms('property_label', array('hide_empty' => 0)) as $label) {
                $property_labels[esc_html($label->name)] = $label->term_id;
            }
            //location
            $property_locations = array();
            $property_locations[''] = '';
            foreach ((array)get_terms('property_location', array('hide_empty' => 0)) as $location) {
                $property_locations[esc_html($location->name)] = $location->slug;
            }
            //sub-location
            $property_sub_locations = array();
            $property_sub_locations[''] = '';
            foreach ((array)get_terms('property_sub_location', array('hide_empty' => 0)) as $sub_location) {
                $property_sub_locations[esc_html($sub_location->name)] = $sub_location->slug;
            }
            // custom fields
            $fields = array_merge(array('_price' => re_get_property_price_field()), re_get_property_custom_fields());
            $field_params = array('' => '');
            foreach ($fields as $key => $field) {
                if (!isset($field['name']) || empty($field['name'])) continue;
                $field_params[$field['label']] = $field['name'];
            }


            //Recent Property
            //===============================
            vc_map(
                array(
                    'base' => 'noo_recent_properties',
                    'name' => __('Recent Properties', 'noo'),
                    'weight' => 809,
                    'class' => 'noo-vc-element noo-vc-element-noo_recent_properties',
                    'icon' => 'noo-vc-icon-noo_recent_properties',
                    'category' => $category_name,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'title',
                            'heading' => __('Title', 'noo'),
                            'description' => __('Enter text which will be used as element title. Leave blank if no title is needed.', 'noo'),
                            'type' => 'textfield',
                            'admin_label' => true,
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'description',
                            'heading' => __('Description', 'noo'),
                            'description' => __('Enter text which will be used as element description. Leave blank if no description is needed.', 'noo'),
                            'type' => 'textfield',
                            'admin_label' => true,
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'style',
                            'heading' => __('Layout Style', 'noo'),
                            'description' => __('Choose a layout style.', 'noo'),
                            'type' => 'dropdown',
                            'admin_label' => true,
                            'holder' => $param_holder,
                            'value' => array(
                                __('Grid', 'noo') => 'grid',
                                __('List', 'noo') => 'list',
                                __('Slider', 'noo') => 'slider',
                                __('Featured Style', 'noo') => 'featured',
                            )
                        ),
                        array(
                            'param_name' => 'prop_style',
                            'heading' => __('Property Style', 'noo'),
                            'description' => __('Choose a property style.', 'noo'),
                            'dependency'    => array( 'element' => 'style', 'value' => array( 'grid', 'featured' ) ),
                            'type' => 'dropdown',
                            'admin_label' => true,
                            'holder' => $param_holder,
                            'value' => array(
                                __('Property Style 1', 'noo') => 'style-1',
                                __('Property Style 2', 'noo') => 'style-2',
                            )
                        ),
                        array(
                            'param_name' => 'autoplay',
                            'heading' => __('Auto Play', 'noo'),
                            'dependency' => array('element' => 'style', 'value' => array('slider', 'featured', 'featured-2')),
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'edit_field_class' => 'vc_col-sm-6 vc_column vc_column-with-padding',
                            'value' => array(
                                __('Yes', 'noo') => 'true',
                                __('No', 'noo') => 'false',
                            )
                        ),
                        array(
                            'param_name' => 'item_per_slide',
                            'heading' => __('Property per slide', 'noo'),
                            'dependency' => array('element' => 'style', 'value' => array('featured'),),
                            'description' => 'Property display each slide',
                            'edit_field_class' => 'vc_col-sm-6 vc_column vc_column-with-padding',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(1, 2, 3),
                        ),
                        array(
                            'param_name' => 'slider_time',
                            'dependency' => array('element' => 'style', 'value' => array('slider', 'featured', 'featured-2')),
                            'heading' => __('Slide Time (ms)', 'noo'),
                            'description' => '',
                            'type' => 'ui_slider',
                            'holder' => $param_holder,
                            'value' => '3000',

                            'data_min' => '500',
                            'data_max' => '8000',
                            'data_step' => '100'
                        ),
                        array(
                            'param_name' => 'slider_speed',
                            'dependency' => array('element' => 'style', 'value' => array('slider', 'featured', 'featured-2')),
                            'heading' => __('Slide Speed (ms)', 'noo'),
                            'description' => '',
                            'type' => 'ui_slider',
                            'holder' => $param_holder,
                            'value' => '600',
                            'data_min' => '100',
                            'data_max' => '3000',
                            'data_step' => '100'
                        ),
                        array(
                            'param_name' => 'show_control',
                            'heading' => __('Show layout control', 'noo'),
                            'description' => __('Show/hide grid/list switching button.', 'noo'),
                            'dependency' => array('element' => 'prop_style', 'value' => array('style-1')),
                            'type' => 'dropdown',
                            'dependency' => array('element' => 'style', 'value' => array('grid', 'list')),
                            'value' => array(
                                __('Hide', 'noo') => false,
                                __('Show', 'noo') => true
                            )
                        ),
                        array(
                            'param_name' => 'show_pagination',
                            'heading' => __('Show Pagination', 'noo'),
                            'description' => __('Show/hide Pagination.', 'noo'),
                            'type' => 'dropdown',
                            'dependency' => array('element' => 'style', 'value' => array('grid', 'list')),
                            'value' => array(
                                __('Hide', 'noo') => 'no',
                                __('Show', 'noo') => 'yes'
                            )
                        ),
                        array(
                            'param_name' => 'show',
                            'heading' => __('Show', 'noo'),
                            'type' => 'dropdown',
                            'admin_label' => true,
                            'holder' => $param_holder,
                            'value' => array(
                                __('All Property', 'noo') => '',
                                __('Only Featured Property', 'noo') => 'featured',
                            )
                        ),
                        array(
                            'param_name' => 'property_category',
                            'heading' => __('Property Type', 'noo'),
                            'type' => 'property_type',
                            'description' => __('Select a type', 'noo'),
                            'admin_label' => true,
                            'holder' => $param_holder,
                            'value' => $property_categories
                        ),
                        array(
                            'param_name' => 'property_status',
                            'heading' => __('Property Status', 'noo'),
                            'type' => 'property_status',
                            'description' => __('Select a status', 'noo'),
                            'admin_label' => true,
                            'holder' => $param_holder,
                            'value' => $property_status
                        ),
                        array(
                            'param_name' => 'property_label',
                            'heading' => __('Property Label', 'noo'),
                            'type' => 'dropdown',
                            'description' => __('Select a label', 'noo'),
                            'holder' => $param_holder,
                            'value' => $property_labels
                        ),
                        array(
                            'param_name' => 'property_location',
                            'heading' => __('Property Location', 'noo'),
                            'type' => 'property_location',
                            'description' => __('Select a location', 'noo'),
                            'admin_label' => true,
                            'holder' => $param_holder,
                            'value' => $property_locations
                        ),
                        array(
                            'param_name' => 'property_sub_location',
                            'heading' => __('Property Sub Location', 'noo'),
                            'type' => 'property_sub_location',
                            'description' => __('Select a sub location', 'noo'),
                            'holder' => $param_holder,
                            'value' => $property_sub_locations
                        ),
                        array(
                            'param_name' => 'number',
                            'heading' => __('Number of properties to show', 'noo'),
                            'description' => __('Number of properties to show. Set value -1 to show all', 'noo'),
                            'type' => 'textfield',
                            'value' => '6',
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'order_by',
                            'heading' => __('Sort Properties by', 'noo'),
                            'description' => __('How to sort properties to show. Default is sorting by date', 'noo'),
                            'type' => 'dropdown',
                            'std' => 'date',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Featured', 'noo') => 'featured',
                                __('Date', 'noo') => 'date',
                                __('Price', 'noo') => 'price',
                                __('Name', 'noo') => 'name',
                                // __('Bath','noo') => 'bath',
                                // __('Bed','noo') => 'bed',
                                __('Area', 'noo') => 'area',
                                // __('Featured','noo') => 'featured',
                                __('Random', 'noo') => 'rand',
                            )
                        ),
                        array(
                            'param_name' => 'order',
                            'heading' => __('Sort Direction', 'noo'),
                            'dependency' => array(
                                'element' => 'order_by',
                                'value' => array('date', 'price', 'name')
                            ),
                            'description' => __('The direction to sort properties, increment or decrement. Default is decrement.', 'noo'),
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Decrement', 'noo') => 'desc',
                                __('Increment', 'noo') => 'asc',
                            )
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                ));

            // Map & Search
            // ============================
            $advanced_search_params = array(
                array(
                    'param_name' => 'title',
                    'heading' => __('Title', 'noo'),
                    'description' => __('Enter text which will be used as element title. Leave blank if no title is needed.', 'noo'),
                    'type' => 'textfield',
                    'holder' => $param_holder
                ),
                array(
                    'param_name' => 'source',
                    'heading' => __('Source', 'noo'),
                    'description' => __('The IDX map require using IDX Listing widget on the same page.', 'noo'),
                    'type' => 'dropdown',
                    'admin_label' => false,
                    'holder' => $param_holder,
                    'value' => array(
                        __('Property', 'noo') => 'property'
                    )
                ),
                array(
                    'param_name' => 'idx_map_search_form',
                    'heading' => __('Enable Search Form', 'noo'),
                    'description' => __('Enable search form to show only IDX page.', 'noo'),
                    'type' => 'checkbox',
                    'holder' => $param_holder,
                    'value' => array(
                        __('Yes, Enable it', 'noo') => 'true'
                    ),
                    'dependency' => array('element' => 'source', 'value' => array('IDX'))
                ),
                array(
                    'param_name' => 'map_height',
                    'heading' => __('Map Height (px)', 'noo'),
                    'description' => __('The maximum height of the map', 'noo'),
                    'type' => 'ui_slider',
                    'holder' => $param_holder,
                    'value' => '700',
                    'data_min' => '400',
                    'data_max' => '1200',
                    'data_step' => '10'
                ),
                array(
                    'param_name' => 'style',
                    'heading' => __('Search Layout', 'noo'),
                    'description' => __('Choose layout for Search form.', 'noo'),
                    'type' => 'dropdown',
                    'admin_label' => false,
                    'holder' => $param_holder,
                    'value' => array(
                        __('Search Horizontal', 'noo') => 'horizontal',
                        __('Search Vertical', 'noo') => 'vertical'
                    ),
                    'dependency' => array('element' => 'source', 'value' => array('property'))
                ),
                array(
                    'param_name' => 'form_layout',
                    'heading' => __('Searchbox Style', 'noo'),
                    'description' => __('Choose style for Search form.', 'noo'),
                    'type' => 'dropdown',
                    'admin_label' => false,
                    'holder' => $param_holder,
                    'value' => array(
                        __('Style 1', 'noo') => 'style-1',
                        __('Style 2', 'noo') => 'style-2',
                    ),
                    'dependency' => array('element' => 'style', 'value' => array('horizontal'))
                ),
                array(
                    'param_name' => 'disable_map',
                    'heading' => __('Diable Map', 'noo'),
                    'description' => __('Disable map to show only Property Search form.', 'noo'),
                    'type' => 'checkbox',
                    'holder' => $param_holder,
                    'value' => array(
                        __('Yes, disable it', 'noo') => 'true'
                    ),
                    'dependency' => array('element' => 'source', 'value' => array('property'))
                ),

                array(
                    'param_name' => 'disable_search_form',
                    'heading' => __('Diable Search', 'noo'),
                    'description' => __('Diable search form to show only map.', 'noo'),
                    'type' => 'checkbox',
                    'holder' => $param_holder,
                    'value' => array(
                        __('Yes, disable it', 'noo') => 'true'
                    ),
                    'dependency' => array('element' => 'source', 'value' => array('property'))
                ),
                array(
                    'param_name' => 'advanced_search',
                    'heading' => __('Show Advanced ( Amenities ) Search', 'noo'),
                    'description' => __('Enable Advanced search to search with Amenities (Only work with Horizontal Search).', 'noo'),
                    'type' => 'checkbox',
                    'holder' => $param_holder,
                    'value' => array(
                        __('Yes', 'noo') => 'true'
                    ),
                    'dependency' => array('element' => 'source', 'value' => array('property'))
                ),
                array(
                    'param_name' => 'no_search_container',
                    'heading' => __('Disable Search Container', 'noo'),
                    'description' => __('Disable search container will remove the container and background around Search form, it will help if you want to display search form inside other element.', 'noo'),
                    'type' => 'checkbox',
                    'holder' => $param_holder,
                    'value' => array(
                        __('Yes, disable it', 'noo') => 'true'
                    ),
                    'dependency' => array('element' => 'disable_map', 'not_empty' => true)
                ),
                array(
                    'param_name' => $param_visibility_name,
                    'heading' => $param_visibility_heading,
                    'description' => $param_visibility_description,
                    'type' => $param_visibility_type,
                    'holder' => $param_visibility_holder,
                    'value' => $param_visibility_value
                ),
                array(
                    'param_name' => $param_class_name,
                    'heading' => $param_class_heading,
                    'description' => $param_class_description,
                    'type' => $param_class_type,
                    'holder' => $param_class_holder
                ),
                array(
                    'param_name' => $param_custom_style_name,
                    'heading' => $param_custom_style_heading,
                    'description' => $param_custom_style_description,
                    'type' => $param_custom_style_type,
                    'holder' => $param_custom_style_holder
                )
            );
            if (!function_exists('is_plugin_active')) {
                include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }
            if (is_plugin_active('dsidxpress/dsidxpress.php')) {
                $advanced_search_params[1]['value'] = array(
                    __('Property', 'noo') => 'property',
                    __('IDX', 'noo') => 'IDX'
                );
            } else {
                unset($advanced_search_params[1]);
            }
            vc_map(
                array(
                    'base' => 'noo_advanced_search_property',
                    'name' => __('Property Map & Search', 'noo'),
                    'weight' => 808,
                    'class' => 'noo-vc-element noo-vc-element-noo_advanced_search_property',
                    'icon' => 'noo-vc-icon-noo_advanced_search_property',
                    'category' => $category_name,
                    'description' => '',
                    'params' => $advanced_search_params
                ));

            // Property Slider
            $property_source_group = __('Property Source', 'noo');
            $property_meta_group = __('Property Fields', 'noo');
            vc_map(array(
                'base' => 'property_slider',
                'name' => __('Property Slider', 'noo'),
                'weight' => 807,
                'class' => 'noo-vc-element noo-vc-element-slider',
                'icon' => 'noo-vc-icon-slider',
                'category' => $category_name,
                'description' => '',
                'as_parent' => array('only' => 'property_slide'),
                'content_element' => true,
                'js_view' => 'VcColumnView',
                'params' => array(

                    array(
                        'param_name' => 'animation',
                        'heading' => __('Animation', 'noo'),
                        'description' => '',
                        'type' => 'dropdown',
                        'holder' => $param_holder,
                        'value' => array(
                            __('Slide', 'noo') => 'slide',
                            __('Fade', 'noo') => 'fade'
                        )
                    ),
                    array(
                        'param_name' => 'slider_time',
                        'heading' => __('Slide Time (ms)', 'noo'),
                        'description' => '',
                        'type' => 'ui_slider',
                        'holder' => $param_holder,
                        'value' => '3000',
                        'data_min' => '500',
                        'data_max' => '8000',
                        'data_step' => '100'
                    ),
                    array(
                        'param_name' => 'slider_speed',
                        'heading' => __('Slide Speed (ms)', 'noo'),
                        'description' => '',
                        'type' => 'ui_slider',
                        'holder' => $param_holder,
                        'value' => '600',
                        'data_min' => '100',
                        'data_max' => '3000',
                        'data_step' => '100'
                    ),
                    array(
                        'param_name' => 'slider_height',
                        'heading' => __('Slider Height (px)', 'noo'),
                        'description' => __('The maximum height of the slider', 'noo'),
                        'type' => 'ui_slider',
                        'holder' => $param_holder,
                        'value' => '700',
                        'data_min' => '400',
                        'data_max' => '1200',
                        'data_step' => '10'
                    ),
                    array(
                        'param_name' => 'auto_play',
                        'heading' => __('Auto Play Slider', 'noo'),
                        'description' => '',
                        'type' => 'checkbox',
                        'holder' => $param_holder,
                        'value' => array(
                            '' => 'true'
                        )
                    ),
                    array(
                        'param_name' => 'indicator',
                        'heading' => __('Show Slide Indicator', 'noo'),
                        'description' => '',
                        'type' => 'checkbox',
                        'holder' => $param_holder,
                        'value' => array(
                            '' => 'true'
                        )
                    ),

                    array(
                        'param_name' => 'prev_next_control',
                        'heading' => __('Show Previous/Next Navigation', 'noo'),
                        'description' => '',
                        'type' => 'checkbox',
                        'holder' => $param_holder,
                        'value' => array(
                            '' => 'true'
                        )
                    ),
                    array(
                        'param_name' => 'show_search_form',
                        'heading' => __('Show Property Search', 'noo'),
                        'description' => __('Show Property Search below the slider.', 'noo'),
                        'type' => 'checkbox',
                        'holder' => $param_holder,
                        'value' => array(
                            '' => 'true'
                        )
                    ),
                    array(
                        'param_name' => 'advanced_search',
                        'heading' => __('Show Advanced ( Amenities ) Search', 'noo'),
                        'description' => __('Enable Advanced search to search with Amenities.', 'noo'),
                        'type' => 'checkbox',
                        'holder' => $param_holder,
                        'value' => array(
                            '' => 'true'
                        ),
                        'dependency' => array('element' => 'show_search_form', 'not_empty' => true)
                    ),
                    // array (
                    // 	'param_name' => 'show_search_info',
                    // 		'heading' => __( 'Show Search Info', 'noo' ),
                    // 		'description' => __( 'Show Info text on top of property search form.', 'noo' ),
                    // 		'type' => 'checkbox',
                    // 		'holder' => $param_holder,
                    // 		'value' => array (
                    // 				'' => 'true'
                    // 		),
                    // 		'dependency' => array( 'element' => 'show_search_form', 'not_empty' => true )
                    // ),
                    array(
                        'param_name' => 'search_info_title',
                        'heading' => __('Search Info Title', 'noo'),
                        'description' => __('The Title of Search Info box, leave it blank for no title.', 'noo'),
                        'type' => 'textfield',
                        'holder' => $param_holder,
                        'value' => '',
                        'dependency' => array('element' => 'show_search_form', 'not_empty' => true)
                    ),
                    array(
                        'param_name' => 'search_info_content',
                        'heading' => __('Search Info Content', 'noo'),
                        'description' => __('The Content of Search Info box, leave it blank for no content.', 'noo'),
                        'type' => 'textfield',
                        'holder' => $param_holder,
                        'value' => '',
                        'dependency' => array('element' => 'show_search_form', 'not_empty' => true)
                    ),
                    array(
                        'param_name' => 'property_source',
                        'heading' => __('Slider Source', 'noo'),
                        'type' => 'dropdown',
                        'admin_label' => true,
                        'holder' => $param_holder,
                        'value' => array(
                            __('Specific Properties ( have to add Property Slide elements )', 'noo') => 'specific',
                            __('Conditionally Select Properties ( no need for Property Slide elements )', 'noo') => 'auto',
                        ),
                        'group' => $property_source_group
                    ),
                    array(
                        'param_name' => 'show',
                        'heading' => __('Show', 'noo'),
                        'type' => 'dropdown',
                        'admin_label' => false,
                        'holder' => $param_holder,
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'value' => array(
                            __('All Property', 'noo') => '',
                            __('Only Featured Property', 'noo') => 'featured',
                        ),
                        'group' => $property_source_group
                    ),
                    array(
                        'param_name' => 'property_category',
                        'heading' => __('Property Type', 'noo'),
                        'type' => 'dropdown',
                        'description' => __('Select a type', 'noo'),
                        'admin_label' => false,
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'holder' => $param_holder,
                        'value' => $property_categories,
                        'group' => $property_source_group
                    ),
                    array(
                        'param_name' => 'property_status',
                        'heading' => __('Property Status', 'noo'),
                        'type' => 'dropdown',
                        'description' => __('Select a status', 'noo'),
                        'admin_label' => false,
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'holder' => $param_holder,
                        'value' => $property_status,
                        'group' => $property_source_group
                    ),
                    array(
                        'param_name' => 'property_label',
                        'heading' => __('Property Label', 'noo'),
                        'type' => 'dropdown',
                        'description' => __('Select a label', 'noo'),
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'holder' => $param_holder,
                        'value' => $property_labels,
                        'group' => $property_source_group
                    ),
                    array(
                        'param_name' => 'property_location',
                        'heading' => __('Property Location', 'noo'),
                        'type' => 'dropdown',
                        'description' => __('Select a location', 'noo'),
                        'admin_label' => false,
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'holder' => $param_holder,
                        'value' => $property_locations,
                        'group' => $property_source_group
                    ),
                    array(
                        'param_name' => 'property_sub_location',
                        'heading' => __('Property Sub Location', 'noo'),
                        'type' => 'dropdown',
                        'description' => __('Select a sub location', 'noo'),
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'holder' => $param_holder,
                        'value' => $property_sub_locations,
                        'group' => $property_source_group
                    ),
                    array(
                        'param_name' => 'property_sub_location',
                        'heading' => __('Property Sub Location', 'noo'),
                        'type' => 'dropdown',
                        'description' => __('Select a sub location', 'noo'),
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'holder' => $param_holder,
                        'value' => $property_sub_locations,
                        'group' => $property_source_group
                    ),
                    array(
                        'param_name' => 'number',
                        'heading' => __('Number of properties to show', 'noo'),
                        'description' => __('Number of properties to show. Set value -1 to show all', 'noo'),
                        'type' => 'textfield',
                        'value' => '4',
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'holder' => $param_holder,
                        'group' => $property_source_group
                    ),
                    array(
                        'param_name' => 'order_by',
                        'heading' => __('Sort Properties by', 'noo'),
                        'description' => __('How to sort properties to show. Default is sorting by date', 'noo'),
                        'type' => 'dropdown',
                        'std' => 'date',
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'holder' => $param_holder,
                        'value' => array(
                            __('Featured', 'noo') => 'featured',
                            __('Date', 'noo') => 'date',
                            __('Price', 'noo') => 'price',
                            __('Name', 'noo') => 'name',
                            // __('Bath','noo') => 'bath',
                            // __('Bed','noo') => 'bed',
                            __('Area', 'noo') => 'area',
                            // __('Featured','noo') => 'featured',
                            __('Random', 'noo') => 'rand',
                        ),
                        'group' => $property_source_group
                    ),
                    array(
                        'param_name' => 'order',
                        'heading' => __('Sort Direction', 'noo'),
                        'dependency' => array(
                            'element' => 'order_by',
                            'value' => array('date', 'price', 'name')
                        ),
                        'description' => __('The direction to sort properties, increment or decrement. Default is decrement.', 'noo'),
                        'type' => 'dropdown',
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'holder' => $param_holder,
                        'value' => array(
                            __('Decrement', 'noo') => 'desc',
                            __('Increment', 'noo') => 'asc',
                        ),
                        'group' => $property_source_group
                    ),
                    array(
                        'param_name' => 'field_1',
                        'heading' => __('Property field #1', 'noo'),
                        'description' => __('Select a fields to change the default settings', 'noo'),
                        'type' => 'dropdown',
                        'std' => '',
                        'value' => $field_params,
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'holder' => $param_holder,
                        'group' => $property_meta_group
                    ),
                    array(
                        'param_name' => 'field_icon_1',
                        'heading' => __('Property field #1 Icon', 'noo'),
                        'description' => __('Select an image to change the default icon', 'noo'),
                        'type' => 'attach_image',
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'holder' => $param_holder,
                        'group' => $property_meta_group
                    ),
                    array(
                        'param_name' => 'field_2',
                        'heading' => __('Property field #2', 'noo'),
                        'description' => __('Select a fields to change the default settings', 'noo'),
                        'type' => 'dropdown',
                        'std' => '',
                        'value' => $field_params,
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'holder' => $param_holder,
                        'group' => $property_meta_group
                    ),
                    array(
                        'param_name' => 'field_icon_2',
                        'heading' => __('Property field #2 Icon', 'noo'),
                        'description' => __('Select an image to change the default icon', 'noo'),
                        'type' => 'attach_image',
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'holder' => $param_holder,
                        'group' => $property_meta_group
                    ),
                    array(
                        'param_name' => 'field_3',
                        'heading' => __('Property field #3', 'noo'),
                        'description' => __('Select a fields to change the default settings', 'noo'),
                        'type' => 'dropdown',
                        'std' => '',
                        'value' => $field_params,
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'holder' => $param_holder,
                        'group' => $property_meta_group
                    ),
                    array(
                        'param_name' => 'field_icon_3',
                        'heading' => __('Property field #3 Icon', 'noo'),
                        'description' => __('Select an image to change the default icon', 'noo'),
                        'type' => 'attach_image',
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'holder' => $param_holder,
                        'group' => $property_meta_group
                    ),
                    array(
                        'param_name' => 'field_4',
                        'heading' => __('Property field #4', 'noo'),
                        'type' => 'dropdown',
                        'std' => '_price',
                        'value' => $field_params,
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'holder' => $param_holder,
                        'group' => $property_meta_group
                    ),
                    array(
                        'param_name' => 'field_icon_4',
                        'heading' => __('Property field #4 Icon', 'noo'),
                        'type' => 'attach_image',
                        'std' => '',
                        'dependency' => array('element' => 'property_source', 'value' => array('auto')),
                        'holder' => $param_holder,
                        'group' => $property_meta_group
                    ),
                    array(
                        'param_name' => $param_visibility_name,
                        'heading' => $param_visibility_heading,
                        'description' => $param_visibility_description,
                        'type' => $param_visibility_type,
                        'holder' => $param_visibility_holder,
                        'value' => $param_visibility_value
                    ),
                    array(
                        'param_name' => $param_class_name,
                        'heading' => $param_class_heading,
                        'description' => $param_class_description,
                        'type' => $param_class_type,
                        'holder' => $param_class_holder
                    ),
                    array(
                        'param_name' => $param_custom_style_name,
                        'heading' => $param_custom_style_heading,
                        'description' => $param_custom_style_description,
                        'type' => $param_custom_style_type,
                        'holder' => $param_custom_style_holder
                    )
                )
            ));

            // Clients
            // ============================
            $property_meta_group = __('Client', 'noo');
            vc_map(
                array(
                    'name' => esc_html__('Noo Clients', 'noo'),
                    'base' => 'noo_client',
                    'icon' => 'noo-vc-icon-noo_clients fa fa-users',
                    'category' => $category_name,
                    'description' => esc_html__('Display client logos', 'noo'),
                    'params' => array(
                        array(
                            'type' => 'param_group',
                            'heading' => esc_html__('Clients', 'noo'),
                            'param_name' => 'clients',

                            'description' => esc_html__('Enter values for client - name, image and url.', 'noo'),
                            'value' => urlencode(json_encode(array(
                                array(
                                    'name' => esc_html__('Client logo 1', 'noo'),
                                    'logo' => '',
                                    'url' => '#',
                                ),
                                array(
                                    'name' => esc_html__('Client logo 2', 'noo'),
                                    'value' => '',
                                    'url' => '#',
                                ),
                                array(
                                    'name' => esc_html__('Client logo 3', 'noo'),
                                    'value' => '',
                                    'url' => '#',
                                ),
                            ))),
                            'params' => array(
                                array(
                                    'type' => 'textfield',
                                    'heading' => esc_html__('Name', 'noo'),
                                    'param_name' => 'name',
                                    'description' => esc_html__('Enter name of client.', 'noo'),
                                    'admin_label' => true,
                                ),
                                array(
                                    'type' => 'attach_image',
                                    'heading' => esc_html__('Image', 'noo'),
                                    'param_name' => 'logo',
                                    'description' => esc_html__('Please select client\' logo.', 'noo'),
                                    'admin_label' => true,
                                ),
                                array(
                                    'type' => 'textfield',
                                    'heading' => esc_html__('Url', 'noo'),
                                    'param_name' => 'url',
                                    'description' => esc_html__('Please insert client\' link.', 'noo'),
                                    'admin_label' => true,
                                ),
                            ),
                        ),
                        array(
                            'param_name' => 'autoplay',
                            'heading' => __('Auto Play', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'edit_field_class' => 'vc_col-sm-6 vc_column vc_column-with-padding',
                            'value' => array(
                                __('Yes', 'noo') => 'true',
                                __('No', 'noo') => 'false')
                        ),
                        array(
                            'param_name' => 'duration',
                            'heading' => __('Slider Duration', 'noo'),
                            'type' => 'textfield',
                            'description' => __('With Milliseconds Unit (1000 = 1 second)', 'noo'),
                            'holder' => $param_holder,
                            'edit_field_class' => 'vc_col-sm-6 vc_column vc_column-with-padding',
                            'value' => '2000'),
                        array(
                            'param_name' => 'logo_per_slide',
                            'heading' => __('Item per page', 'noo'),
                            'type' => 'textfield',
                            'description' => __('Amount of item will display (Must lower than amount of clients)', 'noo'),
                            'holder' => $param_holder,
                            'value' => '5'),

                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )

                    ),
                ));
            // Testimonial
            // ============================
            $property_meta_group = __('Testimonial', 'noo');
            vc_map(
                array(
                    'base' => 'noo_testimonial',
                    'name' => __('Noo Testimonial', 'noo'),
                    'weight' => 809,
                    'class' => 'noo-vc-element noo-vc-element-noo_testimonial',
                    'icon' => 'noo-vc-icon-noo_recent_agents',
                    'category' => $category_name,
                    'description' => '',
                    'params' => array(
                        array(
                            'type' => 'dropdown',
                            'heading' => esc_html__('Style', 'noo'),
                            'admin_label' => true,
                            'param_name' => 'style',
                            'holder' => $param_holder,
                            'value' => array(
                                esc_html__('Style 1', 'noo') => 'style-1',
                                esc_html__('Style 2', 'noo') => 'style-2',
                                esc_html__('Style 3', 'noo') => 'style-3',
                            )
                        ),
                        /*array(
                            'type' => 'attach_image',
                            'heading' => __('Background Image', 'noo'),
                            'param_name' => 'background_image',
                            'holder' => $param_holder,
                            'dependency' => Array(
                                'element' => 'style',
                                'value' => array('style-2'),
                            ),
                        ),*/
                        array(
                            'type' => 'dropdown',
                            'heading' => esc_html__('Source', 'noo'),
                            'param_name' => 'data_source',
                            'admin_label' => true,
                            'holder' => $param_holder,
                            'value' => array(
                                esc_html__('From Category', 'noo') => 'list_cat',
                                esc_html__('From Testimonial IDs', 'noo') => 'list_id',
                            ),
                        ),
                        array(
                            'type' => 'testimonial-cat',
                            'heading' => __('Testimonial Category', 'noo'),
                            'param_name' => 'category',
                            'holder' => $param_holder,
                            'dependency' => Array(
                                'element' => 'data_source',
                                'value' => array('list_cat'),
                            ),
                        ),
                        array(
                            'type' => 'testimonial-single',
                            'heading' => __('Select Testimonial', 'noo'),
                            'param_name' => 'testimonial_ids',
                            'holder' => $param_holder,
                            'dependency' => Array(
                                'element' => 'data_source',
                                'value' => array('list_id'),
                            ),
                        ),
                        array(
                            'param_name' => 'autoplay',
                            'heading' => __('Auto Play', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Yes', 'noo') => 'true',
                                __('No', 'noo') => 'false')),
                        array(
                            'param_name' => 'duration',
                            'heading' => __('Slider Duration', 'noo'),
                            'type' => 'textfield',
                            'description' => __('With Milliseconds Unit (1000 = 1 second)', 'noo'),
                            'holder' => $param_holder,
                            'value' => '4000'),
                        array(
                            'param_name' => 'testimonial_image_per_page',
                            'heading' => __('Item per page', 'noo'),
                            'type' => 'textfield',
                            'description' => __('Amount of item will display', 'noo'),
                            'holder' => $param_holder,
                            'value' => '5'),

                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder)
                    )
                )
            );

            // Post Slider
            //=============================
            vc_map(
                array(
                    'base' => 'noo_post_slider',
                    'name' => __('Noo Recent News', 'noo'),
                    'weight' => 809,
                    'class' => 'noo-vc-element noo-vc-element-noo_testimonial',
                    'icon' => 'noo-vc-icon-noo_call_to_action fa fa-rss',
                    'category' => 'CitiLights',
                    'description' => '',
                    'params' => array(
                        array(
                            'type' => 'dropdown',
                            'heading' => esc_html__('Source', 'noo'),
                            'param_name' => 'data_source',
                            'admin_label' => true,
                            'holder' => $param_holder,
                            'value' => array(
                                esc_html__('From Category', 'noo') => 'list_cat',
                                esc_html__('From Post IDs', 'noo') => 'list_id',
                            ),
                        ),
                        array(
                            'type' => 'post-cat',
                            'heading' => __('Post Category', 'noo'),
                            'param_name' => 'category',
                            'holder' => $param_holder,
                            'dependency' => Array(
                                'element' => 'data_source',
                                'value' => array('list_cat'),
                            ),
                        ),
                        array(
                            'type' => 'post-single',
                            'heading' => __('Select Post', 'noo'),
                            'param_name' => 'post_ids',
                            'holder' => $param_holder,
                            'dependency' => Array(
                                'element' => 'data_source',
                                'value' => array('list_id'),
                            ),
                        ),
                        array(
                            'param_name' => 'autoplay',
                            'heading' => __('Auto Play', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'edit_field_class' => 'vc_col-sm-6 vc_column vc_column-with-padding',
                            'value' => array(
                                __('Yes', 'noo') => 'true',
                                __('No', 'noo') => 'false')
                        ),
                        array(
                            'param_name' => 'post_per_page',
                            'heading' => __('Item per page', 'noo'),
                            'type' => 'dropdown',
                            'description' => __('Amount of item will display per slide', 'noo'),
                            'holder' => $param_holder,
                            'edit_field_class' => 'vc_col-sm-6 vc_column vc_column-with-padding',
                            'value' => array(
                                __('3', 'noo') => '3',
                                __('1', 'noo') => '1',
                                __('2', 'noo') => '2',
                                __('4', 'noo') => '4')),
                        array(
                            'param_name' => 'duration',
                            'heading' => __('Slider Duration', 'noo'),
                            'type' => 'textfield',
                            'description' => __('With Milliseconds Unit (1000 = 1 second)', 'noo'),
                            'type' => 'ui_slider',
                            'holder' => $param_holder,
                            'value' => '5000',
                            'data_min' => '500',
                            'data_max' => '8000',
                            'data_step' => '100',
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder)

                    )
                )
            );
            // Video Slider
            //=============================
            vc_map(
                array(
                    'base' => 'noo_video_slider',
                    'name' => __('Noo Video Slider', 'noo'),
                    'weight' => 809,
                    'class' => 'noo-vc-element noo-vc-element-noo_testimonial',
                    'icon' => 'noo-vc-icon-noo_call_to_action fa fa-play-circle',
                    'category' => 'CitiLights',
                    'description' => '',
                    'params' => array(
                        array(
                            'type' => 'param_group',
                            'heading' => esc_html__('Clients', 'noo'),
                            'param_name' => 'items',
                            'description' => esc_html__('Enter values for each video - name, source and url.', 'noo'),
                            'value' => urlencode(json_encode(array(
                                array(
                                    'name' => esc_html__('Video 1', 'noo'),
                                    'video' => '',
                                    'description' => '',
                                    'thumbnail' => '',
                                ),
                                array(
                                    'name' => esc_html__('Video 2', 'noo'),
                                    'video' => '',
                                    'description' => '',
                                    'thumbnail' => '',
                                ),
                                array(
                                    'name' => esc_html__('Video 3', 'noo'),
                                    'video' => '',
                                    'description' => '',
                                    'thumbnail' => '',
                                ),
                            ))),
                            'params' => array(
                                array(
                                    'type' => 'textfield',
                                    'heading' => esc_html__('Title', 'noo'),
                                    'param_name' => 'title',
                                    'admin_label' => true,
                                ),
                                array(
                                    'type' => 'textfield',
                                    'heading' => esc_html__('Description', 'noo'),
                                    'param_name' => 'description',
                                ),
                                array(
                                    'param_name' => 'source',
                                    'type' => 'dropdown',
                                    'heading' => 'Source Type',
                                    'holder' => $param_holder,
                                    'admin_label' => true,
                                    'value' => array(
                                        __('Self hosted', 'noo') => 'type-s',
                                        //__('Youtube', 'noo') => 'type-y',
                                        __('Vimeo', 'noo') => 'type-v',
                                    )
                                ),
                                array(
                                    'type' => 'attach_image',
                                    'heading' => esc_html__('Video', 'noo'),
                                    'param_name' => 'video',
                                    'description' => 'MP4 file recommended',
                                    'dependency' => array('element' => 'source', 'value' => 'type-s'),
                                ),
                                array(
                                    'type' => 'textfield',
                                    'heading' => esc_html__('Youtube video url', 'noo'),
                                    'param_name' => 'youtube_url',
                                    'dependency' => array('element' => 'source', 'value' => 'type-y'),
                                    'value'=> '',
                                ),
                                array(
                                    'type' => 'textfield',
                                    'heading' => esc_html__('Vimeo video URL', 'noo'),
                                    'param_name' => 'vimeo_url',
                                    'dependency' => array('element' => 'source', 'value' => 'type-v'),
                                    'value'=> '',
                                ),
                                array(
                                    'type' => 'attach_image',
                                    'heading' => esc_html__('Video thumbnail', 'noo'),
                                    'param_name' => 'thumbnail',
                                    'admin_label' => true,
                                    //'dependency' => array('element' => 'source', 'value' => 'type-s'),
                                ),

                            ),
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder)

                    )
                )
            );
            // Counter
            // ============================
            vc_map(
                array(
                    'base' => 'noo_counter',
                    'name' => __('Noo Counter', 'noo'),
                    'weight' => 809,
                    'icon' => 'noo-vc-icon-noo_box_contents fa fa-tachometer',
                    'category' => $category_name,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'title',
                            'type' => 'textfield',
                            'heading' => __('Title', 'noo'),
                            'holder' => $param_holder,
                            'value' => '',
                            'admin_label' => true,

                        ),
                        array(
                            'param_name' => 'start_number',
                            'type' => 'textfield',
                            'heading' => __('Start number', 'noo'),
                            'holder' => $param_holder,
                            'value' => '0',
                            'edit_field_class' => 'vc_col-sm-6 vc_column vc_column-with-padding',
                        ),
                        array(
                            'param_name' => 'end_number',
                            'type' => 'textfield',
                            'heading' => __('End number', 'noo'),
                            'holder' => $param_holder,
                            'value' => '',
                            'edit_field_class' => 'vc_col-sm-6 vc_column vc_column-with-padding',

                        ),
                        array(
                            'param_name' => 'duration',
                            'type' => 'ui_slider',
                            'heading' => __('Duration', 'noo'),
                            'holder' => $param_holder,
                            'value' => '2000',
                            'data_min' => '500',
                            'data_max' => '5000',
                            'data_step' => '100'
                        ),
                        array(
                            'param_name' => 'count_unit',
                            'type' => 'textfield',
                            'heading' => __('Count Unit', 'noo'),
                            'holder' => $param_holder,
                            'value' => '',
                        ),
                        array(
                            'param_name' => 'color',
                            'heading' => __('Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder)
                    )
                )
            );
            // Box contents
            // ============================
            $property_meta_group = __('Box contents', 'noo');
            vc_map(
                array(
                    'base' => 'noo_box_contents',
                    'name' => __('Noo Box contents', 'noo'),
                    'weight' => 809,
                    'icon' => 'noo-vc-icon-noo_box_contents fa fa-cog',
                    'category' => $category_name,
                    'description' => '',
                    'params' => array(
                        array(
                            'type' => 'param_group',
                            'heading' => esc_html__('Contents', 'noo'),
                            'param_name' => 'contents',
                            'description' => esc_html__('Enter values for box content - Title, icon and url.', 'noo'),
                            'value' => urlencode(json_encode(array(
                                array(
                                    'name' => esc_html__('Box content 1', 'noo'),
                                    'logo' => '',
                                    'url' => '#',
                                ),
                                array(
                                    'name' => esc_html__('Box content 2', 'noo'),
                                    'value' => '',
                                    'url' => '#',
                                ),
                                array(
                                    'name' => esc_html__('Box content 3', 'noo'),
                                    'value' => '',
                                    'url' => '#',
                                ),
                            ))),
                            'params' => array(
                                array(
                                    'type' => 'textfield',
                                    'heading' => esc_html__('Title', 'noo'),
                                    'param_name' => 'title',
                                    'description' => esc_html__('Enter title of content.', 'noo'),
                                    'admin_label' => true,
                                    'value' => 'Box content'
                                ),
                                array(
                                    'type' => 'textarea',
                                    'heading' => esc_html__('Description', 'noo'),
                                    'param_name' => 'description',
                                    'description' => esc_html__('Enter feature text of box content.', 'noo'),

                                ),
                                array(
                                    'type' => 'iconpicker',
                                    'heading' => esc_html__('Image', 'noo'),
                                    'param_name' => 'icon',
                                    'description' => esc_html__('Please select box content\' logo.', 'noo'),
                                    'value' => 'fa fa-check',
                                    'admin_label' => true,
                                ),
                                array(
                                    'type' => 'textfield',
                                    'heading' => esc_html__('Url', 'noo'),
                                    'param_name' => 'url',
                                    'value' => '#',
                                    'description' => esc_html__('Please insert \' link.', 'noo'),
                                ),
                                array(
                                    'type' => 'textfield',
                                    'heading' => esc_html__('Url Mask', 'noo'),
                                    'param_name' => 'url_mask',
                                    'value' => 'See lastest posts',

                                ),
                            ),
                        ),
                        array(
                            'param_name' => 'text_color',
                            'heading' => __('Text Color', 'noo'),
                            'type' => 'colorpicker',
                            'holder' => $param_holder,
                            'value' => '#0a0a0a'
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder)
                    )
                )
            );
            //Call to action
            //=============================

            vc_map(
                array(
                    'base' => 'noo_call_to_action',
                    'name' => __('Noo Call To Action', 'noo'),
                    'weight' => 809,
                    'icon' => 'noo-vc-icon-noo_call_to_action fa fa-gift',
                    'category' => $category_name,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'style',
                            'type' => 'dropdown',
                            'heading' => 'Display style',
                            'holder' => $param_holder,
                            'admin_label' => true,
                            'value' => array(
                                __('Style 1', 'noo') => 'style-1',
                                __('Style 2', 'noo') => 'style-2',
                            )
                        ),
                        array(
                            'param_name' => 'title',
                            'type' => 'textfield',
                            'heading' => __('Title'),
                            'value' => 'Banner title here',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'description',
                            'type' => 'textfield',
                            'dependency' => array('element' => 'style', 'value' => 'style-1'),
                            'heading' => __('Description', 'noo'),
                            'holder' => $param_holder,
                            'value' => 'Banner description'
                        ),
                        array(
                            'param_name' => 'btn_txt',
                            'type' => 'textfield',
                            'heading' => __('Button label', 'noo'),
                            'description' => '',
                            'holder' => $param_holder,
                            'value' => 'A cool button',
                        ),
                        array(
                            'param_name' => 'btn_url',
                            'type' => 'textfield',
                            'heading' => __('URL button 1', 'noo'),
                            'description' => '',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'btn_txt_2',
                            'type' => 'textfield',
                            'dependency' => array('element' => 'style', 'value' => 'style-2'),
                            'heading' => __('Second button label', 'noo'),
                            'description' => '',
                            'holder' => $param_holder,
                            'value' => 'Another cool button',
                        ),
                        array(
                            'param_name' => 'btn_url_2',
                            'type' => 'textfield',
                            'dependency' => array('element' => 'style', 'value' => 'style-2'),
                            'heading' => __('URL button 2', 'noo'),
                            'description' => '',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'text_color',
                            'heading' => __('Text Color', 'noo'),
                            'type' => 'colorpicker',
                            'holder' => $param_holder,
                            'value' => '#0a0a0a',
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder)
                    )
                )
            );
            // City information
            // ============================
            vc_map(array(
                'name' => esc_html__('Noo City Information', 'noo'),
                'base' => 'noo_city_info',
                'icon' => 'noo-vc-icon-noo_clients fa fa-bookmark',
                'category' => 'CitiLights',
                'description' => '',
                'params' => array(
                    array(
                        'param_name' => 'style',
                        'type' => 'dropdown',
                        'heading' => esc_html__('Style', 'noo'),
                        'admin_label' => true,
                        'std' => 'grid',
                        'value' => array(
                            esc_html__('Style Masonry', 'noo') => 'masonry',
                        ),
                    ),
                    array(
                        'type' => 'param_group',
                        'heading' => esc_html__('Info', 'noo'),
                        'param_name' => 'items',
                        'params' => array(
                            array(
                                'type' => 'attach_image',
                                'heading' => esc_html__('Thumbnail', 'noo'),
                                'param_name' => 'thumbnail',
                            ),
                            array(
                                'type' => 'dropdown',
                                'heading' => esc_html__('Property Location', 'noo'),
                                'param_name' => 'location_id',
                                'admin_label' => true,
                                'value' => noo_get_list_location_property(),
                            ),
                        ),
                    ),
                    array(
                        'param_name' => $param_visibility_name,
                        'heading' => $param_visibility_heading,
                        'description' => $param_visibility_description,
                        'type' => $param_visibility_type,
                        'holder' => $param_visibility_holder,
                        'value' => $param_visibility_value
                    ),
                    array(
                        'param_name' => $param_class_name,
                        'heading' => $param_class_heading,
                        'description' => $param_class_description,
                        'type' => $param_class_type,
                        'holder' => $param_class_holder
                    ),
                    array(
                        'param_name' => $param_custom_style_name,
                        'heading' => $param_custom_style_heading,
                        'description' => $param_custom_style_description,
                        'type' => $param_custom_style_type,
                        'holder' => $param_custom_style_holder
                    )
                ),
            ));
            // Search by location
            // ============================
            vc_map(array(
                'name' => esc_html__('Noo Property search', 'noo'),
                'base' => 'noo_property_search',
                'icon' => 'noo-vc-icon-noo_clients fa fa-search',
                'category' => 'CitiLights',
                'description' => '',
                'params' => array(

                    array(
                        'param_name' => 'heading',
                        'type' => 'textfield',
                        'heading' => __('Title', 'noo'),
                        'description' => '',
                        'holder' => $param_holder,
                        'value' => 'title 1',
                    ),
                    array(
                        'param_name' => 'description',
                        'holder'=>$param_holder,
                        'heading' => 'Description',
                        'value' => '',
                        'type' => 'textfield',
                    ),
                    array(
                        'param_name' => 'enable_slider',
                        'type' => 'dropdown',
                        'heading' => 'Enable property type slider',
                        'description' => 'Display a property type slider',
                        'value' => array(
                            __('Yes', 'noo') => 'true',
                            __('No', 'noo') => 'false',
                        ),

                    ),
                    array(
                        'param_name' => 'autoplay',
                        'type' => 'dropdown',
                        'heading' => 'Slider autoplay',
                        'description' => 'Enable property type slider autoplay',
                        'edit_field_class' => 'vc_col-sm-6 vc_column vc_column-with-padding',
                        'dependency' => array('element'=> 'enable_slider', 'value'=>'true'),
                        'value' => array(
                            __('Yes', 'noo') => 'true',
                            __('No', 'noo') => 'false',
                        )
                    ),
                    array(
                        'param_name' => 'duration',
                        'type' => 'textfield',
                        'heading' => 'Slide duration',
                        'description' => 'Set property type slide duration',
                        'edit_field_class' => 'vc_col-sm-6 vc_column vc_column-with-padding',
                        'dependency' => array('element'=> 'enable_slider', 'value'=>'true'),
                        'value' => '5000'
                    ),
                    array(
                        'type' => 'param_group',
                        'heading' => esc_html__('Property type', 'noo'),
                        'param_name' => 'items',
                        'dependency' => array('element'=> 'enable_slider', 'value'=>'true'),
                        'description' => esc_html__('Enter values for Slide - Title, icon and url.', 'noo'),
                        'value' => urlencode(json_encode(array(
                            array(

                            ),
                            array(

                            ),
                            array(

                            ),
                        ))),
                        'params' => array(
                            array(
                                'param_name' => 'icon',
                                'heading' => __('Icon', 'noo'),
                                'description' => '',
                                'type' => 'attach_image',
                                'holder' => $param_holder,
                                'value' => '',
                            ),
                            array(
                                'param_name' => 'type_id',
                                'heading' => __('Property type', 'noo'),
                                'description' => __('Choose a property type', 'noo'),
                                'type' => 'dropdown',
                                'admin_label' => true,
                                'holder' => $param_holder,
                                'value' => $property_categories
                            )
                        ),
                    ),
                    array(
                        'param_name' => $param_visibility_name,
                        'heading' => $param_visibility_heading,
                        'description' => $param_visibility_description,
                        'type' => $param_visibility_type,
                        'holder' => $param_visibility_holder,
                        'value' => $param_visibility_value
                    ),
                    array(
                        'param_name' => $param_class_name,
                        'heading' => $param_class_heading,
                        'description' => $param_class_description,
                        'type' => $param_class_type,
                        'holder' => $param_class_holder
                    ),
                    array(
                        'param_name' => $param_custom_style_name,
                        'heading' => $param_custom_style_heading,
                        'description' => $param_custom_style_description,
                        'type' => $param_custom_style_type,
                        'holder' => $param_custom_style_holder
                    )
                ),
            ));

            // Recent Agents
            // ============================

            vc_map(
                array(
                    'base' => 'noo_recent_agents',
                    'name' => __('Recent Agents', 'noo'),
                    'weight' => 806,
                    'class' => 'noo-vc-element noo-vc-element-noo_recent_agents',
                    'icon' => 'noo-vc-icon-noo_recent_agents',
                    'category' => $category_name,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'layout_style',
                            'heading' => __('Style', 'noo'),
                            'type' => 'dropdown',
                            'admin_label' => true,
                            'holder' => $param_holder,
                            'value' => array(
                                __('Style 1', 'noo') => 'style-1',
                                __('Style 2', 'noo') => 'style-2',
                            )
                        ),
                        array(
                            'param_name' => 'title',
                            'heading' => __('Title', 'noo'),
                            'description' => __('Enter text which will be used as element title. Leave blank if no title is needed.', 'noo'),
                            'type' => 'textfield',
                            'admin_label' => true,
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'subtitle',
                            'heading' => __('Description', 'noo'),
                            'description' => __('Enter text which will be used as element description. Leave blank if no title is needed.', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'autoplay',
                            'heading' => __('Auto Play', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'edit_field_class' => 'vc_col-sm-6 vc_column vc_column-with-padding',
                            'value' => array(
                                __('Yes', 'noo') => 'true',
                                __('No', 'noo') => 'false')
                        ),
                        array(
                            'param_name' => 'slider_time',
                            'dependency' => array('element' => 'autoplay', 'value' => 'true'),
                            'heading' => __('Slide Time (ms)', 'noo'),
                            'description' => '',
                            'type' => 'ui_slider',
                            'holder' => $param_holder,
                            'value' => '5000',
                            'data_min' => '500',
                            'data_max' => '8000',
                            'data_step' => '100'
                        ),
                        array(
                            'param_name' => 'slider_speed',
                            'dependency' => array('element' => 'autoplay', 'value' => 'true'),
                            'heading' => __('Slide Speed (ms)', 'noo'),
                            'description' => '',
                            'type' => 'ui_slider',
                            'holder' => $param_holder,
                            'value' => '600',
                            'data_min' => '100',
                            'data_max' => '3000',
                            'data_step' => '100'
                        ),
                        array(
                            'param_name' => 'number',
                            'heading' => __('Number', 'noo'),
                            'type' => 'textfield',
                            'description' => __('Number of agents to show', 'noo'),
                            'value' => '6',
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'columns',
                            'heading' => __('Number of Columns', 'noo'),
                            'type' => 'dropdown',
                            'admin_label' => true,
                            'std' => 3,
                            'value' => array(2, 3, 4, 6),
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                ));

            // Membership Packages
            // ============================
            vc_map(
                array(
                    'base' => 'noo_membership_packages',
                    'name' => __('Membership Packages (Pricing Table)', 'noo'),
                    'weight' => 805,
                    'class' => 'noo-vc-element noo-vc-element-noo_membership_packages',
                    'icon' => 'noo-vc-icon-noo_membership_packages',
                    'category' => $category_name,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'style',
                            'heading' => __('Style', 'noo'),
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Ascending', 'noo') => 'ascending',
                                __('Classic', 'noo') => 'classic'
                            )
                        ),
                        array(
                            'param_name' => 'featured_item',
                            'heading' => __('Featured Item', 'noo'),
                            'description' => __('Enter the no. of the Package that is featured', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                            'value' => '2'
                        ),
                        array(
                            'param_name' => 'btn_text',
                            'heading' => __('Buttons Text', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                            'value' => __('Sign Up', 'noo')
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                ));

            // Login/Register
            // ============================
            vc_map(
                array(
                    'base' => 'noo_login_register',
                    'name' => __('Login/Register', 'noo'),
                    'weight' => 804,
                    'class' => 'noo-vc-element noo-vc-element-noo_login_register',
                    'icon' => 'noo-vc-icon-noo_login_register',
                    'category' => $category_name,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'mode',
                            'heading' => __('Mode', 'noo'),
                            'description' => __('You can choose to show either Login form, Register form or both.', 'noo'),
                            'type' => 'dropdown',
                            'std' => 'bot',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Only Login form', 'noo') => 'login',
                                __('Only Register form', 'noo') => 'register',
                                __('Login and Register', 'noo') => 'both',
                            )
                        ),
                        array(
                            'param_name' => 'login_text',
                            'heading' => __('Login Text', 'noo'),
                            'description' => __('Enter text which will be used as description for login form. Leave blank if not needed.', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                            'value' => __('Already a member of CitiLights. Please use the form below to log in site.', 'noo'),
                            'dependency' => array('element' => 'mode', 'value' => array('login', 'both'))
                        ),
                        array(
                            'param_name' => 'show_register_link',
                            'heading' => __('Show Register Link', 'noo'),
                            'description' => __('Show Register link on this form', 'noo'),
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true'),
                            'dependency' => array('element' => 'mode', 'value' => array('login'))
                        ),
                        array(
                            'param_name' => 'register_text',
                            'heading' => __('Register Text', 'noo'),
                            'description' => __('Enter text which will be used as description for register form. Leave blank if not needed.', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                            'value' => __('Don\'t have an account? Please fill in the form below to create one.', 'noo'),
                            'dependency' => array('element' => 'mode', 'value' => array('register', 'both'))
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                ));
            $query_args = array(
                'posts_per_page' => -1,
                'post_type' => 'noo_property',
                'suppress_filters' => false,
            );
            $properties = array();
            foreach ((array)get_posts($query_args) as $pr) {
                $properties[$pr->post_title] = $pr->ID;
            };

            //Single Property
            // ============================
            vc_map(array(
                'base' => 'noo_single_property',
                'name' => __('Single Property', 'noo'),
                'weight' => 803,
                'class' => 'noo-vc-element noo-vc-element-noo_recent_properties',
                'icon' => 'noo-vc-icon-noo_recent_properties',
                'category' => $category_name,
                'description' => __('Display one property', 'noo'),
                'content_element' => true,
                'params' => array(
                    array(
                        'param_name' => 'title',
                        'heading' => __('Title', 'noo'),
                        'description' => __('Enter text which will be used as element title. Leave blank if no title is needed.', 'noo'),
                        'type' => 'textfield',
                        'admin_label' => true,
                        'holder' => $param_holder
                    ),
                    array(
                        'param_name' => 'property_id',
                        'heading' => __('Property', 'noo'),
                        'description' => __('Choose a Property', 'noo'),
                        'type' => 'dropdown',
                        'admin_label' => true,
                        'holder' => $param_holder,
                        'value' => $properties
                    ),
                    array(
                        'param_name' => 'style',
                        'heading' => __('Style', 'noo'),
                        'description' => __('Choose a style.', 'noo'),
                        'type' => 'dropdown',
                        'admin_label' => true,
                        'holder' => $param_holder,
                        'value' => array(
                            __('Featured Style', 'noo') => 'featured',
                            __('List Item', 'noo') => 'list',
                            __('Grid Item', 'noo') => 'grid',
                            __('Detailed Property', 'noo') => 'detail',
                        )
                    ),
                    array(
                        'param_name' => $param_visibility_name,
                        'heading' => $param_visibility_heading,
                        'description' => $param_visibility_description,
                        'type' => $param_visibility_type,
                        'holder' => $param_visibility_holder,
                        'value' => $param_visibility_value
                    ),
                    array(
                        'param_name' => $param_class_name,
                        'heading' => $param_class_heading,
                        'description' => $param_class_description,
                        'type' => $param_class_type,
                        'holder' => $param_class_holder
                    ),
                    array(
                        'param_name' => $param_custom_style_name,
                        'heading' => $param_custom_style_heading,
                        'description' => $param_custom_style_description,
                        'type' => $param_custom_style_type,
                        'holder' => $param_custom_style_holder
                    )
                )
            ));

            // Property Slide
            // ============================
            vc_map(
                array(
                    'base' => 'property_slide',
                    'name' => __('Property Slide', 'noo'),
                    'weight' => 802,
                    'class' => 'noo-vc-element noo-vc-element-noo_property_slide',
                    'icon' => 'noo-vc-icon-noo_property_slide',
                    'category' => $category_name,
                    'description' => '',
                    'as_child' => array('only' => 'property_slider'),
                    'content_element' => true,
                    'params' => array(
                        array(
                            'param_name' => 'background_type',
                            'heading' => __('Background Type', 'noo'),
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Feature Image', 'noo') => 'thumbnail',
                                __('Custom Image', 'noo') => 'image',
                            )
                        ),
                        array(
                            'param_name' => 'image',
                            'heading' => __('Image', 'noo'),
                            'description' => '',
                            'type' => 'attach_image',
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'background_type', 'value' => array('image'))
                        ),
                        array(
                            'param_name' => 'field_1',
                            'heading' => __('Property field #1', 'noo'),
                            'description' => __('Select a fields to change the default settings', 'noo'),
                            'type' => 'dropdown',
                            'std' => '',
                            'value' => $field_params,
                            'holder' => $param_holder,
                            'group' => $property_meta_group
                        ),
                        array(
                            'param_name' => 'field_icon_1',
                            'heading' => __('Property field #1 Icon', 'noo'),
                            'description' => __('Select an image to change the default icon', 'noo'),
                            'type' => 'attach_image',
                            'holder' => $param_holder,
                            'group' => $property_meta_group
                        ),
                        array(
                            'param_name' => 'field_2',
                            'heading' => __('Property field #2', 'noo'),
                            'description' => __('Select a fields to change the default settings', 'noo'),
                            'type' => 'dropdown',
                            'std' => '',
                            'value' => $field_params,
                            'holder' => $param_holder,
                            'group' => $property_meta_group
                        ),
                        array(
                            'param_name' => 'field_icon_2',
                            'heading' => __('Property field #2 Icon', 'noo'),
                            'description' => __('Select an image to change the default icon', 'noo'),
                            'type' => 'attach_image',
                            'holder' => $param_holder,
                            'group' => $property_meta_group
                        ),
                        array(
                            'param_name' => 'field_3',
                            'heading' => __('Property field #3', 'noo'),
                            'description' => __('Select a fields to change the default settings', 'noo'),
                            'type' => 'dropdown',
                            'std' => '',
                            'value' => $field_params,
                            'holder' => $param_holder,
                            'group' => $property_meta_group
                        ),
                        array(
                            'param_name' => 'field_icon_3',
                            'heading' => __('Property field #3 Icon', 'noo'),
                            'description' => __('Select an image to change the default icon', 'noo'),
                            'type' => 'attach_image',
                            'holder' => $param_holder,
                            'group' => $property_meta_group
                        ),
                        array(
                            'param_name' => 'field_4',
                            'heading' => __('Property field #4', 'noo'),
                            'type' => 'dropdown',
                            'std' => '_price',
                            'value' => $field_params,
                            'holder' => $param_holder,
                            'group' => $property_meta_group
                        ),
                        array(
                            'param_name' => 'field_icon_4',
                            'heading' => __('Property field #4 Icon', 'noo'),
                            'type' => 'attach_image',
                            'std' => '',
                            'holder' => $param_holder,
                            'group' => $property_meta_group
                        ),
                        array(
                            'param_name' => 'property_id',
                            'heading' => __('Property', 'noo'),
                            'description' => __('Choose a property', 'noo'),
                            'type' => 'dropdown',
                            'admin_label' => true,
                            'holder' => $param_holder,
                            'value' => $properties
                        ),
                    )
                )
            );

            // Noo properties slider v2
            vc_map(
                array(
                    'base' => 'noo_property_slide_v2',
                    'name' => __('Noo Property Slide v2', 'noo'),
                    'weight' => 1000,
                    'icon' => 'noo-vc-icon-noo_clients fa fa-home',
                    'category' => 'CitiLights',
                    'description' => '',
                    'params' => array(
                        array(
                            'type' => 'param_group',
                            'heading' => esc_html__('Contents', 'noo'),
                            'param_name' => 'items',
                            'description' => esc_html__('Enter values for Slide - Title, icon and url.', 'noo'),
                            'value' => urlencode(json_encode(array(
                                array(
                                    'name' => esc_html__('Slide 1', 'noo'),
                                    'logo' => '',
                                    'url' => '#',
                                ),
                                array(
                                    'name' => esc_html__('Slide 2', 'noo'),
                                    'value' => '',
                                    'url' => '#',
                                ),
                                array(
                                    'name' => esc_html__('Slide 3', 'noo'),
                                    'value' => '',
                                    'url' => '#',
                                ),
                            ))),
                            'params' => array(
                                array(
                                    'param_name' => 'background_type',
                                    'heading' => __('Background Type', 'noo'),
                                    'type' => 'dropdown',
                                    'holder' => $param_holder,
                                    'value' => array(
                                        __('Feature Image', 'noo') => 'thumbnail',
                                        /*__('Custom Image', 'noo') => 'image',*/
                                    )
                                ),
                                /*array(
                                    'param_name' => 'image',
                                    'heading' => __('Image', 'noo'),
                                    'description' => '',
                                    'type' => 'attach_image',
                                    'holder' => $param_holder,
                                    'value' => '',
                                    'dependency' => array('element' => 'background_type', 'value' => array('image'))
                                ),*/
                                array(
                                    'param_name' => 'property_id',
                                    'heading' => __('Property', 'noo'),
                                    'description' => __('Choose a property', 'noo'),
                                    'type' => 'dropdown',
                                    'admin_label' => true,
                                    'holder' => $param_holder,
                                    'value' => $properties
                                )
                            ),
                        ),
                        array(
                            'param_name' => 'slider_time',
                            'heading' => __('Slide Time (ms)', 'noo'),
                            'description' => '',
                            'type' => 'ui_slider',
                            'holder' => $param_holder,
                            'value' => '3000',
                            'data_min' => '500',
                            'data_max' => '8000',
                            'data_step' => '100'
                        ),
                        array(
                            'param_name' => 'slider_speed',
                            'heading' => __('Slide Speed (ms)', 'noo'),
                            'description' => '',
                            'type' => 'ui_slider',
                            'holder' => $param_holder,
                            'value' => '600',
                            'data_min' => '100',
                            'data_max' => '3000',
                            'data_step' => '100'
                        ),
                        array(
                            'param_name' => 'auto_play',
                            'heading' => __('Autoplay', 'noo'),
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'edit_field_class' => 'vc_col-sm-6 vc_column vc_column-with-padding',
                            'value' => array(
                                __('Yes', 'noo') => 'true',
                                __('No', 'noo') => 'false',
                            )
                        ),

                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'edit_field_class' => 'vc_col-sm-6 vc_column vc_column-with-padding',
                            'value' => $param_visibility_value),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'edit_field_class' => 'vc_col-sm-6 vc_column vc_column-with-padding',
                            'holder' => $param_class_holder),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder)
                    )
                )
            );
        }

        add_action('init', 'noo_vc_citilights');
    }

    if (!function_exists('noo_vc_content')) :

        function noo_vc_content()
        {

            //
            // Variables.
            //
            $category_base_element = __('Base Elements', 'noo');
            $category_typography = __('Typography', 'noo');
            $category_content = __('Content', 'noo');
            $category_wp_content = __('WordPress Content', 'noo');
            $category_media = __('Media', 'noo');
            $category_custom = __('Custom', 'noo');

            $param_content_name = 'content';
            $param_content_heading = __('Text', 'noo');
            $param_content_description = __('Enter your text.', 'noo');
            $param_content_type = 'textarea_html';
            $param_content_holder = 'div';
            $param_content_value = '';

            $param_visibility_name = 'visibility';
            $param_visibility_heading = __('Visibility', 'noo');
            $param_visibility_description = '';
            $param_visibility_type = 'dropdown';
            $param_visibility_holder = 'div';
            $param_visibility_value = array(
                __('All Devices', 'noo') => "all",
                __('Hidden Phone', 'noo') => "hidden-phone",
                __('Hidden Tablet', 'noo') => "hidden-tablet",
                __('Hidden PC', 'noo') => "hidden-pc",
                __('Visible Phone', 'noo') => "visible-phone",
                __('Visible Tablet', 'noo') => "visible-tablet",
                __('Visible PC', 'noo') => "visible-pc",
            );

            $param_class_name = 'class';
            $param_class_heading = __('Class', 'noo');
            $param_class_description = __('(Optional) Enter a unique class name.', 'noo');
            $param_class_type = 'textfield';
            $param_class_holder = 'div';

            $param_custom_style_name = 'custom_style';
            $param_custom_style_heading = __('Custom Style', 'noo');
            $param_custom_style_description = __('(Optional) Enter inline CSS.', 'noo');
            $param_custom_style_type = 'textfield';
            $param_custom_style_holder = 'div';

            $param_icon_value = array();

            $param_holder = 'div';

            // [vc_accordion]
            // ============================
            vc_map_update('vc_accordion', array(
                'category' => $category_content,
                'weight' => 790
            ));

            vc_remove_param('vc_accordion', 'collapsible');
            vc_remove_param('vc_accordion', 'el_class');

            vc_add_param('vc_accordion', array(
                'param_name' => 'title',
                'heading' => __('Title (optional)', 'noo'),
                'type' => 'textfield',
                'holder' => $param_holder
            ));

            vc_add_param('vc_accordion', array(
                'param_name' => 'active_tab',
                'heading' => __('Active Tab', 'noo'),
                'description' => __('The tab number to be active on load, default is 1. Enter -1 to collapse all tabs.', 'noo'),
                'type' => 'textfield',
                'holder' => $param_holder
            ));

            vc_add_param('vc_accordion', array(
                'param_name' => 'icon_style',
                'heading' => __('Icon Style', 'noo'),
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => array(
                    __('Dark Circle', 'noo') => 'dark_circle',
                    __('Light Circle', 'noo') => 'light_circle',
                    __('Dark Square', 'noo') => 'dark_square',
                    __('Light Square', 'noo') => 'light_square',
                    __('Simple Icon', 'noo') => 'simple',
                    __('Left Arrow', 'noo') => 'left_arrow',
                    __('Right Arrow', 'noo') => 'right_arrow',
                )
            ));

            vc_add_param('vc_accordion', array(
                'param_name' => $param_visibility_name,
                'heading' => $param_visibility_heading,
                'description' => $param_visibility_description,
                'type' => $param_visibility_type,
                'holder' => $param_visibility_holder,
                'value' => $param_visibility_value
            ));

            vc_add_param('vc_accordion', array(
                'param_name' => $param_class_name,
                'heading' => $param_class_heading,
                'description' => $param_class_description,
                'type' => $param_class_type,
                'holder' => $param_class_holder
            ));

            vc_add_param('vc_accordion', array(
                'param_name' => $param_custom_style_name,
                'heading' => $param_custom_style_heading,
                'description' => $param_custom_style_description,
                'type' => $param_custom_style_type,
                'holder' => $param_custom_style_holder
            ));

            // [vc_tabs]
            // ============================
            vc_map_update('vc_tabs', array(
                'category' => $category_content,
                'weight' => 780
            ));

            vc_remove_param('vc_tabs', 'interval');
            vc_remove_param('vc_tabs', 'el_class');

            vc_add_param('vc_tabs', array(
                'param_name' => 'title',
                'heading' => __('Title (optional)', 'noo'),
                'type' => 'textfield',
                'holder' => $param_holder
            ));

            vc_add_param('vc_tabs', array(
                'param_name' => 'active_tab',
                'heading' => __('Active Tab', 'noo'),
                'description' => __('The tab number to be active on load, default is 1. Enter -1 to collapse all tabs.', 'noo'),
                'type' => 'textfield',
                'holder' => $param_holder
            ));

            vc_add_param('vc_tabs', array(
                'param_name' => $param_visibility_name,
                'heading' => $param_visibility_heading,
                'description' => $param_visibility_description,
                'type' => $param_visibility_type,
                'holder' => $param_visibility_holder,
                'value' => $param_visibility_value
            ));

            vc_add_param('vc_tabs', array(
                'param_name' => $param_class_name,
                'heading' => $param_class_heading,
                'description' => $param_class_description,
                'type' => $param_class_type,
                'holder' => $param_class_holder
            ));

            vc_add_param('vc_tabs', array(
                'param_name' => $param_custom_style_name,
                'heading' => $param_custom_style_heading,
                'description' => $param_custom_style_description,
                'type' => $param_custom_style_type,
                'holder' => $param_custom_style_holder
            ));

            // vc_add_param( 'vc_tab', array(
            // 	'param_name'	=> 'icon',
            // 	'heading'		=> __( 'Icon', 'noo' ),
            // 	'type'          => 'iconpicker',
            // 	'holder'        => $param_holder
            // 	) );

            // [vc_tour]
            // ============================
            vc_map_update('vc_tour', array(
                'category' => $category_content,
                'weight' => 770
            ));

            vc_remove_param('vc_tour', 'interval');
            vc_remove_param('vc_tour', 'el_class');

            vc_add_param('vc_tour', array(
                'param_name' => 'title',
                'heading' => __('Title (optional)', 'noo'),
                'type' => 'textfield',
                'holder' => $param_holder
            ));

            vc_add_param('vc_tour', array(
                'param_name' => $param_visibility_name,
                'heading' => $param_visibility_heading,
                'description' => $param_visibility_description,
                'type' => $param_visibility_type,
                'holder' => $param_visibility_holder,
                'value' => $param_visibility_value
            ));

            vc_add_param('vc_tour', array(
                'param_name' => $param_class_name,
                'heading' => $param_class_heading,
                'description' => $param_class_description,
                'type' => $param_class_type,
                'holder' => $param_class_holder
            ));

            vc_add_param('vc_tour', array(
                'param_name' => $param_custom_style_name,
                'heading' => $param_custom_style_heading,
                'description' => $param_custom_style_description,
                'type' => $param_custom_style_type,
                'holder' => $param_custom_style_holder
            ));

            // [block_grid]
            // ============================
            vc_map(
                array(
                    'base' => 'block_grid',
                    'name' => __('Block Grid', 'noo'),
                    'weight' => 760,
                    'class' => 'noo-vc-element noo-vc-element-block_grid',
                    'icon' => 'noo-vc-icon-block_grid',
                    'category' => $category_content,
                    'description' => '',
                    'as_parent' => array('only' => 'block_grid_item'),
                    'content_element' => true,
                    'js_view' => 'VcColumnView',
                    'params' => array(
                        array(
                            'param_name' => 'title',
                            'heading' => __('Title (optional)', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'columns',
                            'heading' => __('Number of Columns', 'noo'),
                            'type' => 'dropdown',
                            'std' => '3',
                            'holder' => $param_holder,
                            'value' => array(
                                __('One', 'noo') => '1',
                                __('Two', 'noo') => '2',
                                __('Three', 'noo') => '3',
                                __('Four', 'noo') => '4',
                                __('Five', 'noo') => '5',
                                __('Six', 'noo') => '6'
                            )
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [block_grid_item]
            // ============================
            vc_map(
                array(
                    'base' => 'block_grid_item',
                    'name' => __('Blog Grid Item', 'noo'),
                    'weight' => 755,
                    'class' => 'noo-vc-element noo-vc-element-block_grid_item',
                    'icon' => 'noo-vc-icon-block_grid_item',
                    'category' => $category_content,
                    'description' => '',
                    'as_child' => array('only' => 'block_grid'),
                    'content_element' => true,
                    'show_settings_on_create' => false,
                    'params' => array(
                        array(
                            'param_name' => $param_content_name,
                            'heading' => $param_content_heading,
                            'description' => $param_content_description,
                            'type' => $param_content_type,
                            'holder' => $param_content_holder,
                            'value' => $param_content_value
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [progress_bar]
            // ============================
            vc_map(
                array(
                    'base' => 'progress_bar',
                    'name' => __('Progress Bar', 'noo'),
                    'weight' => 750,
                    'class' => 'noo-vc-element noo-vc-element-progress_bar',
                    'icon' => 'noo-vc-icon-progress_bar',
                    'category' => $category_content,
                    'description' => '',
                    'as_parent' => array('only' => 'progress_bar_item'),
                    'content_element' => true,
                    'js_view' => 'VcColumnView',
                    'params' => array(
                        array(
                            'param_name' => 'title',
                            'heading' => __('Title (optional)', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'style',
                            'heading' => __('Bar Style', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Lean', 'noo') => 'lean',
                                __('Thick', 'noo') => 'thick'
                            )
                        ),
                        array(
                            'param_name' => 'rounded',
                            'heading' => __('Rounded Bar', 'noo'),
                            'description' => '',
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array(
                                '' => 'true',
                            )
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [progress_bar_item]
            // ============================
            vc_map(
                array(
                    'base' => 'progress_bar_item',
                    'name' => __('Progress Bar Item', 'noo'),
                    'weight' => 745,
                    'class' => 'noo-vc-element noo-vc-element-progress_bar_item',
                    'icon' => 'noo-vc-icon-progress_bar_item',
                    'category' => $category_content,
                    'description' => '',
                    'as_child' => array('only' => 'progress_bar'),
                    'content_element' => true,
                    'params' => array(
                        array(
                            'param_name' => 'title',
                            'heading' => __('Bar Title', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'progress',
                            'heading' => __('Progress ( out of 100 )', 'noo'),
                            'type' => 'ui_slider',
                            'holder' => $param_holder,
                            'value' => '50',
                            'data_min' => '1',
                            'data_max' => '100',
                        ),
                        array(
                            'param_name' => 'color',
                            'heading' => __('Color', 'noo'),
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Primary', 'noo') => 'primary',
                                __('Success', 'noo') => 'success',
                                __('Info', 'noo') => 'info',
                                __('Warning', 'noo') => 'warning',
                                __('Danger', 'noo') => 'danger',
                            )
                        ),
                        array(
                            'param_name' => 'color_effect',
                            'heading' => __('Color Effect', 'noo'),
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('None', 'noo') => '',
                                __('Striped', 'noo') => 'striped',
                                __('Striped with Animation', 'noo') => 'striped_animation',
                            )
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [pricing_table]
            // ============================
            vc_map(
                array(
                    'base' => 'pricing_table',
                    'name' => __('Pricing Table', 'noo'),
                    'weight' => 740,
                    'class' => 'noo-vc-element noo-vc-element-pricing_table',
                    'icon' => 'noo-vc-icon-pricing_table',
                    'category' => $category_content,
                    'description' => '',
                    'as_parent' => array('only' => 'pricing_table_column'),
                    'content_element' => true,
                    'js_view' => 'VcColumnView',
                    'params' => array(
                        array(
                            'param_name' => 'title',
                            'heading' => __('Title (optional)', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'columns',
                            'heading' => __('Number of Columns', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'std' => '3',
                            'holder' => $param_holder,
                            'value' => array(
                                __('One', 'noo') => '1',
                                __('Two', 'noo') => '2',
                                __('Three', 'noo') => '3',
                                __('Four', 'noo') => '4',
                                __('Five', 'noo') => '5',
                                __('Six', 'noo') => '6'
                            )
                        ),
                        array(
                            'param_name' => 'style',
                            'heading' => __('Style', 'noo'),
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'std' => 'classic',
                            'value' => array(
                                __('Ascending', 'noo') => 'ascending',
                                __('Classic', 'noo') => 'classic'
                            )
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [pricing_table_column]
            // ============================
            vc_map(
                array(
                    'base' => 'pricing_table_column',
                    'name' => __('Pricing Table Column', 'noo'),
                    'weight' => 735,
                    'class' => 'noo-vc-element noo-vc-element-pricing_table_column',
                    'icon' => 'noo-vc-icon-pricing_table_column',
                    'category' => $category_content,
                    'description' => '',
                    'as_child' => array('only' => 'pricing_table'),
                    'content_element' => true,
                    'params' => array(
                        array(
                            'param_name' => 'title',
                            'heading' => __('Title', 'noo'),
                            'description' => __('Column Title', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'featured',
                            'heading' => __('Featured Column', 'noo'),
                            'description' => '',
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array(
                                '' => 'true',
                            )
                        ),
                        array(
                            'param_name' => 'price',
                            'heading' => __('Price', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                            'value' => ''
                        ),
                        array(
                            'param_name' => 'symbol',
                            'heading' => __('Currency Symbol', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                            'value' => '$'
                        ),
                        array(
                            'param_name' => 'before_price',
                            'heading' => __('Text Before Price', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                            'value' => 'From'
                        ),
                        array(
                            'param_name' => 'after_price',
                            'heading' => __('Text After Price', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                            'value' => 'per Month'
                        ),
                        array(
                            'param_name' => $param_content_name,
                            'heading' => $param_content_heading,
                            'description' => $param_content_description,
                            'type' => $param_content_type,
                            'holder' => $param_content_holder,
                            'value' => '[icon_list][icon_list_item icon="fa fa-check"]Etiam rhoncus[/icon_list_item][icon_list_item icon="fa fa-times"]Donec mi[/icon_list_item][icon_list_item icon="fa fa-times"]Nam ipsum[/icon_list_item][/icon_list]'
                        ),
                        array(
                            'param_name' => 'button_text',
                            'heading' => __('Button Text', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                            'value' => __('Purchase', 'noo'),
                        ),
                        array(
                            'param_name' => 'href',
                            'heading' => __('URL (Link)', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'target',
                            'heading' => __('Open in new tab', 'noo'),
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true'),
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [vc_pie]
            // ============================
            vc_map_update('vc_pie', array(
                'category' => $category_content,
                'weight' => 730,
                'class' => 'noo-vc-element noo-vc-element-pie',
                'icon' => 'noo-vc-icon-pie',
            ));

            vc_remove_param('vc_pie', 'color');
            vc_remove_param('vc_pie', 'el_class');

            vc_add_param('vc_pie', array(
                'param_name' => 'style',
                'heading' => __('Style', 'noo'),
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => array(
                    __('Filled', 'noo') => 'filled',
                    __('Bordered', 'noo') => 'bordered',
                )
            ));

            vc_add_param('vc_pie', array(
                'param_name' => 'color',
                'heading' => __('Bar Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder
            ));

            vc_add_param('vc_pie', array(
                'param_name' => 'width',
                'heading' => __('Bar Width (px)', 'noo'),
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '1',
                'data_min' => '1',
                'data_max' => '20',
            ));

            vc_add_param('vc_pie', array(
                'param_name' => 'value_color',
                'heading' => __('Value Label Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder
            ));

            vc_add_param('vc_pie', array(
                'param_name' => $param_visibility_name,
                'heading' => $param_visibility_heading,
                'description' => $param_visibility_description,
                'type' => $param_visibility_type,
                'holder' => $param_visibility_holder,
                'value' => $param_visibility_value
            ));

            vc_add_param('vc_pie', array(
                'param_name' => $param_class_name,
                'heading' => $param_class_heading,
                'description' => $param_class_description,
                'type' => $param_class_type,
                'holder' => $param_class_holder
            ));

            vc_add_param('vc_pie', array(
                'param_name' => $param_custom_style_name,
                'heading' => $param_custom_style_heading,
                'description' => $param_custom_style_description,
                'type' => $param_custom_style_type,
                'holder' => $param_custom_style_holder
            ));

            // [vc_cta_button]
            // ============================
            vc_map_update('vc_cta_button', array(
                'category' => $category_content,
                'weight' => 720,
                'class' => 'noo-vc-element noo-vc-element-cta',
                'icon' => 'noo-vc-icon-cta',
            ));

            vc_remove_param('vc_cta_button', 'call_text');
            vc_remove_param('vc_cta_button', 'title');
            vc_remove_param('vc_cta_button', 'href');
            vc_remove_param('vc_cta_button', 'target');
            vc_remove_param('vc_cta_button', 'color');
            vc_remove_param('vc_cta_button', 'icon');
            vc_remove_param('vc_cta_button', 'size');
            vc_remove_param('vc_cta_button', 'position');
            vc_remove_param('vc_cta_button', 'css_animation');
            vc_remove_param('vc_cta_button', 'position');
            vc_remove_param('vc_cta_button', 'el_class');

            vc_add_param('vc_cta_button', array(
                'param_name' => 'title',
                'heading' => __('Title (Heading)', 'noo'),
                'type' => 'textfield',
                'holder' => $param_holder
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'message',
                'heading' => __('Message', 'noo'),
                'type' => 'textarea',
                'holder' => $param_holder
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'alignment',
                'heading' => __('Alignment', 'noo'),
                'description' => '',
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => array(
                    __('Center', 'noo') => 'center',
                    __('Left', 'noo') => 'left',
                    __('Right', 'noo') => 'right',
                )
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'button_text',
                'heading' => __('Button Text', 'noo'),
                'type' => 'textfield',
                'holder' => $param_holder
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'href',
                'heading' => __('URL (Link)', 'noo'),
                'type' => 'textfield',
                'holder' => $param_holder
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'target',
                'heading' => __('Open in new tab', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array('' => 'true'),
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'size',
                'heading' => __('Size', 'noo'),
                'description' => '',
                'type' => 'dropdown',
                'std' => 'medium',
                'holder' => $param_holder,
                'value' => array(
                    __('Extra Small', 'noo') => 'x_small',
                    __('Small', 'noo') => 'small',
                    __('Medium', 'noo') => 'medium',
                    __('Large', 'noo') => 'large',
                    __('Custom', 'noo') => 'custom'
                )
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'fullwidth',
                'heading' => __('Forge Full-Width', 'noo'),
                'description' => '',
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array(
                    '' => 'false'
                )
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'vertical_padding',
                'heading' => __('Vertical Padding (px)', 'noo'),
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '10',
                'data_min' => '0',
                'data_max' => '50',
                'dependency' => array('element' => 'size', 'value' => array('custom'))
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'horizontal_padding',
                'heading' => __('Horizontal Padding (px)', 'noo'),
                'type' => 'ui_slider',
                'holder' => $param_holder,
                'value' => '10',
                'data_min' => '0',
                'data_max' => '50',
                'dependency' => array('element' => 'size', 'value' => array('custom'))
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'icon',
                'heading' => __('Icon', 'noo'),
                'type' => 'iconpicker',
                'holder' => $param_holder
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'icon_right',
                'heading' => __('Right Icon', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array('' => 'true'),
                'dependency' => array('element' => 'icon', 'not_empty' => true)
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'icon_only',
                'heading' => __('Show only Icon', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array('' => 'true'),
                'dependency' => array('element' => 'icon', 'not_empty' => true)
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'icon_color',
                'heading' => __('Icon Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder,
                'value' => '',
                'dependency' => array('element' => 'icon', 'not_empty' => true)
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'shape',
                'heading' => __('Shape', 'noo'),
                'description' => '',
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => array(
                    __('Square', 'noo') => 'square',
                    __('Rounded', 'noo') => 'rounded',
                    __('Pill', 'noo') => 'pill',
                )
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'style',
                'heading' => __('Style', 'noo'),
                'description' => '',
                'type' => 'dropdown',
                'std' => '',
                'holder' => $param_holder,
                'value' => array(
                    __('3D Pressable', 'noo') => 'pressable',
                    __('Metro', 'noo') => 'metro',
                    __('Blank', 'noo') => '',
                )
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'skin',
                'heading' => __('Skin', 'noo'),
                'description' => '',
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => array(
                    __('Default', 'noo') => 'default',
                    __('Custom', 'noo') => 'custom',
                    __('Primary', 'noo') => 'primary',
                    __('Success', 'noo') => 'success',
                    __('Info', 'noo') => 'info',
                    __('Warning', 'noo') => 'warning',
                    __('Danger', 'noo') => 'danger',
                    __('Link', 'noo') => 'link',
                )
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'text_color',
                'heading' => __('Text Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder,
                'value' => '',
                'dependency' => array('element' => 'skin', 'value' => array('custom'))
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'hover_text_color',
                'heading' => __('Hover Text Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder,
                'value' => '',
                'dependency' => array('element' => 'skin', 'value' => array('custom'))
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'bg_color',
                'heading' => __('Background Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder,
                'value' => '',
                'dependency' => array('element' => 'skin', 'value' => array('custom'))
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'hover_bg_color',
                'heading' => __('Hover Background Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder,
                'value' => '',
                'dependency' => array('element' => 'skin', 'value' => array('custom'))
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'border_color',
                'heading' => __('Border Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder,
                'value' => '',
                'dependency' => array('element' => 'style', 'value' => array('custom'))
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => 'hover_border_color',
                'heading' => __('Hover Border Color', 'noo'),
                'type' => 'colorpicker',
                'holder' => $param_holder,
                'value' => '',
                'dependency' => array('element' => 'style', 'value' => array('custom'))
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => $param_visibility_name,
                'heading' => $param_visibility_heading,
                'description' => $param_visibility_description,
                'type' => $param_visibility_type,
                'holder' => $param_visibility_holder,
                'value' => $param_visibility_value
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => $param_class_name,
                'heading' => $param_class_heading,
                'description' => $param_class_description,
                'type' => $param_class_type,
                'holder' => $param_class_holder
            ));

            vc_add_param('vc_cta_button', array(
                'param_name' => $param_custom_style_name,
                'heading' => $param_custom_style_heading,
                'description' => $param_custom_style_description,
                'type' => $param_custom_style_type,
                'holder' => $param_custom_style_holder
            ));

            // [counter]
            // ============================
            vc_map(
                array(
                    'base' => 'counter',
                    'name' => __('Counter', 'noo'),
                    'weight' => 710,
                    'class' => 'noo-vc-element noo-vc-element-counter',
                    'icon' => 'noo-vc-icon-counter',
                    'category' => $category_content,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'number',
                            'heading' => __('Number', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder
                        ),
                        array(
                            'param_name' => 'size',
                            'heading' => __('Size (px)', 'noo'),
                            'type' => 'ui_slider',
                            'holder' => $param_holder,
                            'value' => '50',
                            'data_min' => '10',
                            'data_max' => '100',
                        ),
                        array(
                            'param_name' => 'color',
                            'heading' => __('Color', 'noo'),
                            'description' => '',
                            'type' => 'colorpicker',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'alignment',
                            'heading' => __('Alignment', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Center', 'noo') => 'center',
                                __('Left', 'noo') => 'left',
                                __('Right', 'noo') => 'right',
                            )
                        ),
                        array(
                            'param_name' => $param_content_name,
                            'heading' => $param_content_heading,
                            'description' => $param_content_description,
                            'type' => $param_content_type,
                            'holder' => $param_content_holder,
                            'value' => $param_content_value
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [vc_message]
            // ============================
            vc_map_update('vc_message', array(
                'category' => $category_content,
                'class' => 'noo-vc-element noo-vc-element-message',
                'icon' => 'noo-vc-icon-message',
                'weight' => 700
            ));

            vc_remove_param('vc_message', 'color');
            vc_remove_param('vc_message', 'style');
            vc_remove_param('vc_message', 'css_animation');
            vc_remove_param('vc_message', 'el_class');

            vc_add_param('vc_message', array(
                'param_name' => 'title',
                'heading' => __('Title (Heading)', 'noo'),
                'type' => 'textfield',
                'holder' => $param_holder
            ));

            vc_add_param('vc_message', array(
                'param_name' => $param_content_name,
                'heading' => $param_content_heading,
                'description' => $param_content_description,
                'type' => $param_content_type,
                'holder' => $param_content_holder,
                'value' => $param_content_value
            ));

            vc_add_param('vc_message', array(
                'param_name' => 'type',
                'heading' => __('Message Type', 'noo'),
                'description' => '',
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => array(
                    __('Success', 'noo') => 'success',
                    __('Info', 'noo') => 'info',
                    __('Warning', 'noo') => 'warning',
                    __('Danger', 'noo') => 'danger',
                )
            ));

            vc_add_param('vc_message', array(
                'param_name' => 'dismissible',
                'heading' => __('Dismissible', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array(
                    '' => 'true'
                )
            ));

            vc_add_param('vc_message', array(
                'param_name' => $param_visibility_name,
                'heading' => $param_visibility_heading,
                'description' => $param_visibility_description,
                'type' => $param_visibility_type,
                'holder' => $param_visibility_holder,
                'value' => $param_visibility_value
            ));

            vc_add_param('vc_message', array(
                'param_name' => $param_class_name,
                'heading' => $param_class_heading,
                'description' => $param_class_description,
                'type' => $param_class_type,
                'holder' => $param_class_holder
            ));

            vc_add_param('vc_message', array(
                'param_name' => $param_custom_style_name,
                'heading' => $param_custom_style_heading,
                'description' => $param_custom_style_description,
                'type' => $param_custom_style_type,
                'holder' => $param_custom_style_holder
            ));

        }

        add_action('init', 'noo_vc_content');

        //
        // Extend container class (parents).
        //
        if (class_exists('WPBakeryShortCodesContainer')) {
            class WPBakeryShortCode_Block_Grid extends WPBakeryShortCodesContainer
            {
            }

            class WPBakeryShortCode_Progress_Bar extends WPBakeryShortCodesContainer
            {
            }

            class WPBakeryShortCode_Pricing_Table extends WPBakeryShortCodesContainer
            {
            }
        }

        //
        // Extend item class (children).
        //
        if (class_exists('WPBakeryShortCode')) {
            class WPBakeryShortCode_Block_Grid_Item extends WPBakeryShortCode
            {
            }

            class WPBakeryShortCode_Progress_Bar_Item extends WPBakeryShortCode
            {
            }

            class WPBakeryShortCode_Pricing_Table_Column extends WPBakeryShortCode
            {
            }
        }

    endif;


    if (!function_exists('noo_vc_wp_content')) :

        function noo_vc_wp_content()
        {

            //
            // Variables.
            //
            $category_base_element = __('Base Elements', 'noo');
            $category_typography = __('Typography', 'noo');
            $category_content = __('Content', 'noo');
            $category_wp_content = __('WordPress Content', 'noo');
            $category_media = __('Media', 'noo');
            $category_custom = __('Custom', 'noo');

            $param_content_name = 'content';
            $param_content_heading = __('Text', 'noo');
            $param_content_description = __('Enter your text.', 'noo');
            $param_content_type = 'textarea_html';
            $param_content_holder = 'div';
            $param_content_value = '';

            $param_visibility_name = 'visibility';
            $param_visibility_heading = __('Visibility', 'noo');
            $param_visibility_description = '';
            $param_visibility_type = 'dropdown';
            $param_visibility_holder = 'div';
            $param_visibility_value = array(
                __('All Devices', 'noo') => "all",
                __('Hidden Phone', 'noo') => "hidden-phone",
                __('Hidden Tablet', 'noo') => "hidden-tablet",
                __('Hidden PC', 'noo') => "hidden-pc",
                __('Visible Phone', 'noo') => "visible-phone",
                __('Visible Tablet', 'noo') => "visible-tablet",
                __('Visible PC', 'noo') => "visible-pc",
            );

            $param_class_name = 'class';
            $param_class_heading = __('Class', 'noo');
            $param_class_description = __('(Optional) Enter a unique class name.', 'noo');
            $param_class_type = 'textfield';
            $param_class_holder = 'div';

            $param_custom_style_name = 'custom_style';
            $param_custom_style_heading = __('Custom Style', 'noo');
            $param_custom_style_description = __('(Optional) Enter inline CSS.', 'noo');
            $param_custom_style_type = 'textfield';
            $param_custom_style_holder = 'div';

            $param_holder = 'div';

            // [vc_widget_sidebar]
            // ============================
            vc_map_update('vc_widget_sidebar', array(
                'category' => $category_wp_content,
                'weight' => 690,
                'class' => 'noo-vc-element noo-vc-element-widget_sidebar',
                'icon' => 'noo-vc-icon-widget_sidebar'
            ));

            vc_remove_param('vc_widget_sidebar', 'el_class');

            vc_add_param('vc_widget_sidebar', array(
                'param_name' => $param_visibility_name,
                'heading' => $param_visibility_heading,
                'description' => $param_visibility_description,
                'type' => $param_visibility_type,
                'holder' => $param_visibility_holder,
                'value' => $param_visibility_value
            ));

            vc_add_param('vc_widget_sidebar', array(
                'param_name' => $param_class_name,
                'heading' => $param_class_heading,
                'description' => $param_class_description,
                'type' => $param_class_type,
                'holder' => $param_class_holder
            ));

            vc_add_param('vc_widget_sidebar', array(
                'param_name' => $param_custom_style_name,
                'heading' => $param_custom_style_heading,
                'description' => $param_custom_style_description,
                'type' => $param_custom_style_type,
                'holder' => $param_custom_style_holder
            ));

            // [blog]
            // ============================
            vc_map(
                array(
                    'base' => 'blog',
                    'name' => __('Post List', 'noo'),
                    'weight' => 680,
                    'class' => 'noo-vc-element noo-vc-element-blog',
                    'icon' => 'noo-vc-icon-blog',
                    'category' => $category_wp_content,
                    'description' => '',
                    'params' => array(
                        // array(
                        // 	'param_name'	=> 'layout',
                        // 	'heading'		=> __( 'Layout', 'noo' ),
                        // 	'description'   => '',
                        // 	'type'          => 'dropdown',
                        // 	'holder'        => $param_holder,
                        // 	'value'         => array(
                        // 		__( 'Default List', 'noo' ) => 'list',
                        // 		__( 'Masonry', 'noo' )      => 'masonry',
                        // 	)
                        // ),
                        // array(
                        // 	'param_name'	=> 'columns',
                        // 	'heading'		=> __( 'Columns', 'noo' ),
                        // 	'type'          => 'dropdown',
                        // 	'holder'        => $param_holder,
                        // 	'value'         => array(
                        // 		__( 'One', 'noo' )       => '1',
                        // 		__( 'Two', 'noo' )       => '2',
                        // 		__( 'Three', 'noo' )     => '3',
                        // 		__( 'Four', 'noo' )      => '4',
                        // 		__( 'Five', 'noo' )      => '5',
                        // 		__( 'Six', 'noo' )       => '6'
                        // 	),
                        // 	'dependency'    => array( 'element' => 'layout', 'value' => array( 'masonry' ) )
                        // ),
                        array(
                            'param_name' => 'categories',
                            'heading' => __('Blog Categories', 'noo'),
                            'description' => '',
                            'type' => 'post_categories',
                            'holder' => $param_holder,
                        ),
                        // array(
                        // 	'param_name'	=> 'filter',
                        // 	'heading'		=> __( 'Show Category Filter', 'noo' ),
                        // 	'type'          => 'checkbox',
                        // 	'holder'        => $param_holder,
                        // 	'value'         => array( '' => 'true' ),
                        // 	'dependency'    => array( 'element' => 'layout', 'value' => array( 'masonry' ) )
                        // ),
                        array(
                            'param_name' => 'orderby',
                            'heading' => __('Order By', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Recent First', 'noo') => 'latest',
                                __('Older First', 'noo') => 'oldest',
                                __('Title Alphabet', 'noo') => 'alphabet',
                                __('Title Reversed Alphabet', 'noo') => 'ralphabet',
                            )
                        ),
                        array(
                            'param_name' => 'post_count',
                            'heading' => __('Max Number of Post', 'noo'),
                            'type' => 'ui_slider',
                            'holder' => $param_holder,
                            'value' => '4',
                            'data_min' => '1',
                            'data_max' => '20'
                        ),
                        array(
                            'param_name' => 'hide_featured',
                            'heading' => __('Hide Featured Image(s)', 'noo'),
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true')
                        ),
                        array(
                            'param_name' => 'hide_post_meta',
                            'heading' => __('Hide Post Meta', 'noo'),
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true')
                        ),
                        // array(
                        // 	'param_name'	=> 'hide_category',
                        // 	'heading'		=> __( 'Hide Category Meta', 'noo' ),
                        // 	'type'          => 'checkbox',
                        // 	'holder'        => $param_holder,
                        // 	'value'         => array( '' => 'true' )
                        // ),
                        // array(
                        // 	'param_name'	=> 'hide_comment',
                        // 	'heading'		=> __( 'Hide Comment Meta', 'noo' ),
                        // 	'type'          => 'checkbox',
                        // 	'holder'        => $param_holder,
                        // 	'value'         => array( '' => 'true' )
                        // ),
                        array(
                            'param_name' => 'hide_readmore',
                            'heading' => __('Hide Readmore link', 'noo'),
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true')
                        ),
                        array(
                            'param_name' => 'excerpt_length',
                            'heading' => __('Excerpt length', 'noo'),
                            'type' => 'textfield',
                            'std' => 55,
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'title',
                            'heading' => __('Heading (optional)', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                        ),
                        // array(
                        // 	'param_name'	=> 'sub_title',
                        // 	'heading'		=> __( 'Sub-Heading (optional)', 'noo' ),
                        // 	'type'          => 'textfield',
                        // 	'holder'        => $param_holder,
                        // ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [team_member]
            // ============================
            vc_map(
                array(
                    'base' => 'team_member',
                    'name' => __('Team Member', 'noo'),
                    'weight' => 670,
                    'class' => 'noo-vc-element noo-vc-element-team_member',
                    'icon' => 'noo-vc-icon-team_member',
                    'category' => $category_wp_content,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'name',
                            'heading' => __('Member Name', 'noo'),
                            'description' => '',
                            'type' => 'textfield',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'avatar',
                            'heading' => __('Avatar', 'noo'),
                            'description' => '',
                            'type' => 'attach_image',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'role',
                            'heading' => __('Job Position', 'noo'),
                            'description' => '',
                            'type' => 'textfield',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'description',
                            'heading' => __('Description', 'noo'),
                            'description' => __('Input description here to override Author\'s description.', 'noo'),
                            'type' => 'textarea',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'facebook',
                            'heading' => __('Facebook Profile', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'twitter',
                            'heading' => __('Twitter Profile', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'googleplus',
                            'heading' => __('Google+ Profile', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'linkedin',
                            'heading' => __('LinkedIn Profile', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [contact-form-7]
            // ============================
            if (class_exists('WPCF7_ContactForm')) {
                vc_map_update('contact-form-7', array(
                    'category' => $category_wp_content,
                    'weight' => 650
                ));
            }
        }

        add_action('init', 'noo_vc_wp_content');

    endif;

    if (!function_exists('noo_vc_media')) :

        function noo_vc_media()
        {

            //
            // Variables.
            //
            $category_base_element = __('Base Elements', 'noo');
            $category_typography = __('Typography', 'noo');
            $category_content = __('Content', 'noo');
            $category_wp_content = __('WordPress Content', 'noo');
            $category_media = __('Media', 'noo');
            $category_custom = __('Custom', 'noo');

            $param_content_name = 'content';
            $param_content_heading = __('Text', 'noo');
            $param_content_description = __('Enter your text.', 'noo');
            $param_content_type = 'textarea_html';
            $param_content_holder = 'div';
            $param_content_value = '';

            $param_visibility_name = 'visibility';
            $param_visibility_heading = __('Visibility', 'noo');
            $param_visibility_description = '';
            $param_visibility_type = 'dropdown';
            $param_visibility_holder = 'div';
            $param_visibility_value = array(
                __('All Devices', 'noo') => "all",
                __('Hidden Phone', 'noo') => "hidden-phone",
                __('Hidden Tablet', 'noo') => "hidden-tablet",
                __('Hidden PC', 'noo') => "hidden-pc",
                __('Visible Phone', 'noo') => "visible-phone",
                __('Visible Tablet', 'noo') => "visible-tablet",
                __('Visible PC', 'noo') => "visible-pc",
            );

            $param_class_name = 'class';
            $param_class_heading = __('Class', 'noo');
            $param_class_description = __('(Optional) Enter a unique class name.', 'noo');
            $param_class_type = 'textfield';
            $param_class_holder = 'div';

            $param_custom_style_name = 'custom_style';
            $param_custom_style_heading = __('Custom Style', 'noo');
            $param_custom_style_description = __('(Optional) Enter inline CSS.', 'noo');
            $param_custom_style_type = 'textfield';
            $param_custom_style_holder = 'div';

            $param_holder = 'div';

            // [vc_single_image]
            // ============================
            vc_map_update('vc_single_image', array(
                'category' => $category_media,
                'class' => 'noo-vc-element noo-vc-element-image',
                'icon' => 'noo-vc-icon-image',
                'weight' => 590
            ));

            vc_remove_param('vc_single_image', 'title');
            vc_remove_param('vc_single_image', 'img_size');
            vc_remove_param('vc_single_image', 'alignment');
            vc_remove_param('vc_single_image', 'style');
            vc_remove_param('vc_single_image', 'border_color');
            vc_remove_param('vc_single_image', 'css_animation');
            vc_remove_param('vc_single_image', 'link');
            vc_remove_param('vc_single_image', 'img_link');
            vc_remove_param('vc_single_image', 'img_link_large');
            vc_remove_param('vc_single_image', 'img_link_target');
            vc_remove_param('vc_single_image', 'el_class');
            vc_remove_param('vc_single_image', 'css');

            vc_add_param('vc_single_image', array(
                'param_name' => 'alt',
                'heading' => __('Alt Text', 'noo'),
                'type' => 'textfield',
                'holder' => $param_holder
            ));

            vc_add_param('vc_single_image', array(
                'param_name' => 'style',
                'heading' => __('Image Style', 'noo'),
                'type' => 'dropdown',
                'holder' => $param_holder,
                'value' => array(
                    __('None', 'noo') => '',
                    __('Rounded', 'noo') => 'rounded',
                    __('Circle', 'noo') => 'circle',
                    __('Thumbnail', 'noo') => 'thumbnail',
                )
            ));

            vc_add_param('vc_single_image', array(
                'param_name' => 'href',
                'heading' => __('Image Link', 'noo'),
                'description' => __('Input the URL if you want the image to wrap inside an anchor.', 'noo'),
                'type' => 'textfield',
                'holder' => $param_holder
            ));

            vc_add_param('vc_single_image', array(
                'param_name' => 'target',
                'heading' => __('Open in New Tab', 'noo'),
                'type' => 'checkbox',
                'holder' => $param_holder,
                'value' => array(
                    '' => 'true'),
                'dependency' => array('element' => 'href', 'not_empty' => true)
            ));

            vc_add_param('vc_single_image', array(
                'param_name' => 'link_title',
                'heading' => __('Link Title', 'noo'),
                'type' => 'textfield',
                'holder' => $param_holder,
                'value' => '',
                'dependency' => array('element' => 'href', 'not_empty' => true)
            ));

            vc_add_param('vc_single_image', array(
                'param_name' => $param_visibility_name,
                'heading' => $param_visibility_heading,
                'description' => $param_visibility_description,
                'type' => $param_visibility_type,
                'holder' => $param_visibility_holder,
                'value' => $param_visibility_value
            ));

            vc_add_param('vc_single_image', array(
                'param_name' => $param_class_name,
                'heading' => $param_class_heading,
                'description' => $param_class_description,
                'type' => $param_class_type,
                'holder' => $param_class_holder
            ));

            vc_add_param('vc_single_image', array(
                'param_name' => $param_custom_style_name,
                'heading' => $param_custom_style_heading,
                'description' => $param_custom_style_description,
                'type' => $param_custom_style_type,
                'holder' => $param_custom_style_holder
            ));

            // [noo_rev_slider] Revolution Slider
            // ============================
            if (class_exists('RevSlider')) {
                vc_map(array(
                    'base' => 'noo_rev_slider',
                    'name' => __('Revolution Slider', 'noo'),
                    'weight' => 580,
                    'class' => 'noo-vc-element noo-vc-element-rev_slider',
                    'icon' => 'noo-vc-icon-rev_slider',
                    'category' => $category_media,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'slider',
                            'heading' => __('Revolution Slider', 'noo'),
                            'description' => '',
                            'type' => 'noo_rev_slider',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                ));
            }

            // [slider] Responsive Slider
            // ============================
            vc_map(
                array(
                    'base' => 'slider',
                    'name' => __('Responsive Slider', 'noo'),
                    'weight' => 570,
                    'class' => 'noo-vc-element noo-vc-element-slider',
                    'icon' => 'noo-vc-icon-slider',
                    'category' => $category_media,
                    'description' => '',
                    'as_parent' => array('only' => 'slide'),
                    'content_element' => true,
                    'js_view' => 'VcColumnView',
                    'params' => array(
                        array(
                            'param_name' => 'animation',
                            'heading' => __('Animation', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Slide', 'noo') => 'slide',
                                __('Fade', 'noo') => 'fade',
                            )
                        ),
                        // array(
                        // 	'param_name'  => 'visible_items',
                        // 	'heading'     => __( 'Max Number of Visible Item', 'noo' ),
                        // 	'description' => '',
                        // 	'type'        => 'ui_slider',
                        // 	'holder'      => $param_holder,
                        // 	'value'       => '1'
                        //	'data_min'    => '1',
                        //	'data_max'    => '10',
                        // ),
                        array(
                            'param_name' => 'slider_time',
                            'heading' => __('Slide Time (ms)', 'noo'),
                            'description' => '',
                            'type' => 'ui_slider',
                            'holder' => $param_holder,
                            'value' => '3000',
                            'data_min' => '500',
                            'data_max' => '8000',
                            'data_step' => '100',
                        ),
                        array(
                            'param_name' => 'slider_speed',
                            'heading' => __('Slide Speed (ms)', 'noo'),
                            'description' => '',
                            'type' => 'ui_slider',
                            'holder' => $param_holder,
                            'value' => '600',
                            'data_min' => '100',
                            'data_max' => '3000',
                            'data_step' => '100',
                        ),
                        array(
                            'param_name' => 'auto_play',
                            'heading' => __('Auto Play Slider', 'noo'),
                            'description' => '',
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true')
                        ),
                        array(
                            'param_name' => 'pause_on_hover',
                            'heading' => __('Pause on Hover', 'noo'),
                            'description' => __('If auto play, pause slider when mouse over it?', 'noo'),
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true')
                        ),
                        array(
                            'param_name' => 'random',
                            'heading' => __('Random Slider', 'noo'),
                            'description' => __('Random Choose Slide to Start', 'noo'),
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true')
                        ),
                        array(
                            'param_name' => 'indicator',
                            'heading' => __('Show Slide Indicator', 'noo'),
                            'description' => '',
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true')
                        ),
                        array(
                            'param_name' => 'indicator_position',
                            'heading' => __('Indicator Position', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Top', 'noo') => 'top',
                                __('Bottom', 'noo') => 'bottom'
                            )
                        ),
                        array(
                            'param_name' => 'prev_next_control',
                            'heading' => __('Show Previous/Next Navigation', 'noo'),
                            'description' => '',
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true')
                        ),
                        array(
                            'param_name' => 'timer',
                            'heading' => __('Show Timer', 'noo'),
                            'description' => '',
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true')
                        ),
                        array(
                            'param_name' => 'swipe',
                            'heading' => __('Enable Swipe on Mobile', 'noo'),
                            'description' => '',
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true')
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [slide] Responsive Slider Item
            // ============================
            vc_map(
                array(
                    'base' => 'slide',
                    'name' => __('Slide', 'noo'),
                    'weight' => 575,
                    'class' => 'noo-vc-element noo-vc-element-slide',
                    'icon' => 'noo-vc-icon-slide',
                    'category' => $category_media,
                    'description' => '',
                    'as_child' => array('only' => 'slider'),
                    'content_element' => true,
                    'params' => array(
                        array(
                            'param_name' => 'type',
                            'heading' => __('Type', 'noo'),
                            'description' => __('Choose the type of this slide: Image, Video or HTML Content', 'noo'),
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Image', 'noo') => 'image',
                                __('HTML Content', 'noo') => 'content',
                            )
                        ),
                        array(
                            'param_name' => 'image',
                            'heading' => __('Image', 'noo'),
                            'description' => '',
                            'type' => 'attach_image',
                            'admin_label' => true,
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'type', 'value' => array('image'))
                        ),
                        array(
                            'param_name' => 'caption',
                            'heading' => __('Image Caption', 'noo'),
                            'description' => '',
                            'type' => 'textarea',
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'type', 'value' => array('image'))
                        ),
                        // array(
                        // 	'param_name'  => 'video_url',
                        // 	'heading'     => __( 'Video URL', 'noo' ),
                        // 	'description' => '',
                        // 	'type'        => 'textfield',
                        // 	'holder'      => $param_holder,
                        // 	'dependency'  => array( 'element' => 'type', 'value' => array( 'video' ) )
                        // ),
                        // array(
                        // 	'param_name'  => 'video_poster',
                        // 	'heading'     => __( 'Video Poster Image', 'noo' ),
                        // 	'description' => __( 'Poster Image to show on Mobile or un-supported devices.', 'noo' ),
                        // 	'type'        => 'attach_image',
                        // 	'holder'      => $param_holder,
                        // 	'dependency'  => array( 'element' => 'type', 'value' => array( 'video' ) )
                        // ),
                        array(
                            'param_name' => $param_content_name,
                            'heading' => __('HTML Content (only for HTML Content slide)', 'noo'),
                            'description' => $param_content_description,
                            'type' => $param_content_type,
                            'holder' => $param_content_holder,
                            'value' => $param_content_value,
                            'dependency' => array('element' => 'type', 'value' => array('content'))
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [lightbox] Responsive Lightbox
            // ============================
            vc_map(
                array(
                    'base' => 'lightbox',
                    'name' => __('Responsive Lightbox', 'noo'),
                    'weight' => 560,
                    'class' => 'noo-vc-element noo-vc-element-lightbox',
                    'icon' => 'noo-vc-icon-lightbox',
                    'category' => $category_media,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'gallery_id',
                            'heading' => __('Gallery ID', 'noo'),
                            'description' => __('Lightbox elements with the same Gallery ID will be grouped to in the same slider lightbox.', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'type',
                            'heading' => __('Content Type', 'noo'),
                            'description' => __('Choose the content type of this slide. We support: Image, Iframe (for other site and embed video) and HTML Content', 'noo'),
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Image', 'noo') => 'image',
                                __('IFrame', 'noo') => 'iframe',
                                __('HTML Content', 'noo') => 'inline',
                            )
                        ),
                        array(
                            'param_name' => 'image',
                            'heading' => __('Image', 'noo'),
                            'description' => '',
                            'type' => 'attach_image',
                            'admin_label' => true,
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'type', 'value' => array('image'))
                        ),
                        array(
                            'param_name' => 'image_title',
                            'heading' => __('Image Title', 'noo'),
                            'description' => '',
                            'type' => 'textfield',
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'type', 'value' => array('image'))
                        ),
                        array(
                            'param_name' => 'iframe_url',
                            'heading' => __('Iframe URL', 'noo'),
                            'description' => __('You can input any link like http://wikipedia.com. Youtube and Vimeo link will be converted to embed video, other video site will need embeded link.', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'type', 'value' => array('iframe'))
                        ),
                        array(
                            'param_name' => $param_content_name,
                            'heading' => __('HTML Content (only for Inline HTML Lightbox)', 'noo'),
                            'description' => $param_content_description,
                            'type' => $param_content_type,
                            'holder' => $param_content_holder,
                            'value' => $param_content_value,
                            'dependency' => array('element' => 'type', 'value' => array('inline'))
                        ),
                        array(
                            'param_name' => 'thumbnail_type',
                            'heading' => __('Thumbnail Type', 'noo'),
                            'description' => '',
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('Image', 'noo') => 'image',
                                __('Link', 'noo') => 'link'
                            )
                        ),
                        array(
                            'param_name' => 'thumbnail_image',
                            'heading' => __('Thumbnail Image', 'noo'),
                            'description' => __('For Image lightbox, thumbnail of original Image is automatically created if you do not choose any thumbnail.', 'noo'),
                            'type' => 'attach_image',
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'thumbnail_type', 'value' => array('image'))
                        ),
                        array(
                            'param_name' => 'thumbnail_style',
                            'heading' => __('Thumbnail Style', 'noo'),
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                __('None', 'noo') => '',
                                __('Rounded', 'noo') => 'rounded',
                                __('Circle', 'noo') => 'circle',
                                __('Thumbnail', 'noo') => 'thumbnail',
                            ),
                            'dependency' => array('element' => 'thumbnail_type', 'value' => array('image')
                            )
                        ),
                        array(
                            'param_name' => 'thumbnail_title',
                            'heading' => __('Thumbnail Title', 'noo'),
                            'description' => __('Title for Thumbnail link.', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                            'dependency' => array('element' => 'thumbnail_type', 'value' => array('link'))
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [video_player] Video (Self Hosted)
            // ============================
            vc_map(
                array(
                    'base' => 'video_player',
                    'name' => __('Video (Self Hosted)', 'noo'),
                    'weight' => 555,
                    'class' => 'noo-vc-element noo-vc-element-video_player',
                    'icon' => 'noo-vc-icon-video_player',
                    'category' => $category_media,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'video_m4v',
                            'heading' => __('M4V File URL', 'noo'),
                            'description' => __('Place the URL to your .m4v video file here.', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'video_ogv',
                            'heading' => __('OGV File URL', 'noo'),
                            'description' => __('Place the URL to your .ogv video file here.', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'video_ratio',
                            'heading' => __('Video Aspect Ratio', 'noo'),
                            'description' => __('Choose the aspect ratio for your video.', 'noo'),
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                '16:9' => '16:9',
                                '5:3' => '5:3',
                                '5:4' => '5:4',
                                '4:3' => '4:3',
                                '3:2' => '3:2',
                            )
                        ),
                        array(
                            'param_name' => 'video_poster',
                            'heading' => __('Poster Image', 'noo'),
                            'description' => '',
                            'type' => 'attach_image',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'auto_play',
                            'heading' => __('Auto Play Video', 'noo'),
                            'description' => '',
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true')
                        ),
                        array(
                            'param_name' => 'hide_controls',
                            'heading' => __('Hide Player Controls', 'noo'),
                            'description' => '',
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true')
                        ),
                        array(
                            'param_name' => 'show_play_icon',
                            'heading' => __('Show Play Icon', 'noo'),
                            'description' => '',
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true')
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [video_embed] Video Embed
            // ============================
            vc_map(
                array(
                    'base' => 'video_embed',
                    'name' => __('Video Embed', 'noo'),
                    'weight' => 550,
                    'class' => 'noo-vc-element noo-vc-element-video_embed',
                    'icon' => 'noo-vc-icon-video_embed',
                    'category' => $category_media,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'video_ratio',
                            'heading' => __('Video Aspect Ratio', 'noo'),
                            'description' => __('Choose the aspect ratio for your video.', 'noo'),
                            'type' => 'dropdown',
                            'holder' => $param_holder,
                            'value' => array(
                                '16:9' => '16:9',
                                '5:3' => '5:3',
                                '5:4' => '5:4',
                                '4:3' => '4:3',
                                '3:2' => '3:2',
                            )
                        ),
                        array(
                            'param_name' => $param_content_name,
                            'heading' => __('Embed Code', 'noo'),
                            'description' => __('Input your &lt;iframe&gt; or &lt;embed&gt; code.', 'noo'),
                            'type' => 'textarea_safe',
                            'holder' => $param_content_holder,
                            'value' => $param_content_value,
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [audio_player] Audio (Self Hosted)
            // ============================
            vc_map(
                array(
                    'base' => 'audio_player',
                    'name' => __('Audio (Self Hosted)', 'noo'),
                    'weight' => 545,
                    'class' => 'noo-vc-element noo-vc-element-audio_player',
                    'icon' => 'noo-vc-icon-audio_player',
                    'category' => $category_media,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'audio_mp3',
                            'heading' => __('MP3 File URL', 'noo'),
                            'description' => __('Place the URL to your .mp3 audio file here.', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'audio_oga',
                            'heading' => __('OGA File URL', 'noo'),
                            'description' => __('Place the URL to your .oga audio file here.', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                        ),
                        array(
                            'param_name' => 'auto_play',
                            'heading' => __('Auto Play Audio', 'noo'),
                            'description' => '',
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true')
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [audio_embed] Audio Embed
            // ============================
            vc_map(
                array(
                    'base' => 'audio_embed',
                    'name' => __('Audio Embed', 'noo'),
                    'weight' => 540,
                    'class' => 'noo-vc-element noo-vc-element-audio_embed',
                    'icon' => 'noo-vc-icon-audio_embed',
                    'category' => $category_media,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => $param_content_name,
                            'heading' => __('Embed Code', 'noo'),
                            'description' => __('Input your &lt;iframe&gt; or &lt;embed&gt; code.', 'noo'),
                            'type' => 'textarea_safe',
                            'holder' => $param_content_holder,
                            'value' => $param_content_value,
                        ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );

            // [vc_gmaps]
            // ============================
            // vc_map_update( 'vc_gmaps', array(
            // 	'category'    => $category_media,
            // 	'class'       => 'noo-vc-element noo-vc-element-maps',
            // 	'icon'        => 'noo-vc-icon-maps',
            // 	'weight'      => 530
            // 	) );

            // vc_remove_param( 'vc_gmaps', 'link' );
            // vc_remove_param( 'vc_gmaps', 'title' );
            // vc_remove_param( 'vc_gmaps', 'size' );
            // vc_remove_param( 'vc_gmaps', 'el_class' );

            // vc_add_param( 'vc_gmaps', array(
            // 	'param_name'	=> 'link',
            // 	'heading'		=> __( 'Map Embed Iframe', 'noo' ),
            // 	'description'	=> sprintf( __( 'Visit <a href="%s" target="_blank">Google maps</a> and create your map with following steps: 1) Find a location 2) Click "Share" and make sure map is public on the web 3) Click folder icon to reveal "Embed on my site" link 4) Copy iframe code and paste it here.</span>', 'noo' ), 'http://maps.google.com/'),
            // 	'type'          => 'textarea_safe',
            // 	'holder'        => $param_holder
            // 	) );

            // vc_add_param( 'vc_gmaps', array(
            // 	'param_name'	=> 'size',
            // 	'heading'		=> __( 'Map Height', 'noo' ),
            // 	'description'	=> __( 'Enter map height in pixels. Example: 200 or leave it empty to make map responsive.', 'noo' ),
            // 	'type'          => 'textfield',
            // 	'holder'        => $param_holder
            // 	) );

            // vc_add_param( 'vc_gmaps', array(
            // 	'param_name'	=> 'disable_zooming',
            // 	'heading'		=> __( 'Diable Zooming', 'noo' ),
            // 	'description'	=> __( 'Disable zooming to prevent map accidentally zoom when mouse scroll over it.', 'noo' ),
            // 	'type'          => 'checkbox',
            // 	'holder'        => $param_holder,
            // 	'value'         => array( '' => 'true' )
            // 	) );

            // vc_add_param( 'vc_gmaps', array(
            // 	'param_name'  => $param_visibility_name,
            // 	'heading'     => $param_visibility_heading,
            // 	'description' => $param_visibility_description,
            // 	'type'        => $param_visibility_type,
            // 	'holder'      => $param_visibility_holder,
            // 	'value'       => $param_visibility_value
            // 	) );

            // vc_add_param( 'vc_gmaps', array(
            // 	'param_name'  => $param_class_name,
            // 	'heading'     => $param_class_heading,
            // 	'description' => $param_class_description,
            // 	'type'        => $param_class_type,
            // 	'holder'      => $param_class_holder
            // 	) );

            // vc_add_param( 'vc_gmaps', array(
            // 	'param_name'  => $param_custom_style_name,
            // 	'heading'     => $param_custom_style_heading,
            // 	'description' => $param_custom_style_description,
            // 	'type'        => $param_custom_style_type,
            // 	'holder'      => $param_custom_style_holder
            // 	) );

            /**
             * Create ShortCode: [noo_mailchimp]
             *
             * @package     Noo Library
             * @author      TuNguyen <tunguyen@vietbrain.com>
             * @version     1.0
             */
            vc_map(array(
                'name' => esc_html__('Noo Mailchimp', 'noo'),
                'base' => 'noo_mailchimp',
                'description' => esc_html__('Displays your MailChimp form', 'noo'),
                'class' => 'noo-vc-element noo-vc-element-mailchimp',
                'icon' => 'noo-vc-icon-mailchimp',
                'category' => $category_media,
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'holder' => 'div',
                        'heading' => esc_html__('Title', 'noo'),
                        'param_name' => 'title',
                        'value' => ''
                    ),
                    array(
                        'type' => 'textfield',
                        'holder' => 'div',
                        'heading' => esc_html__('Sub title', 'noo'),
                        'param_name' => 'sub_title',
                        'value' => ''
                    ),
                )
            ));

            // [social_share]
            // ============================
            vc_map(
                array(
                    'base' => 'social_share',
                    'name' => __('Social Sharing', 'noo'),
                    'weight' => 510,
                    'class' => 'noo-vc-element noo-vc-element-social_share',
                    'icon' => 'noo-vc-icon-social_share',
                    'category' => $category_media,
                    'description' => '',
                    'params' => array(
                        array(
                            'param_name' => 'title',
                            'heading' => __('Sharing Title', 'noo'),
                            'type' => 'textfield',
                            'holder' => $param_holder,
                            'value' => __('Share this Post', 'noo'),
                        ),
                        array(
                            'param_name' => 'facebook',
                            'heading' => __('Facebook', 'noo'),
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true'),
                        ),
                        array(
                            'param_name' => 'twitter',
                            'heading' => __('Twitter', 'noo'),
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true'),
                        ),
                        array(
                            'param_name' => 'googleplus',
                            'heading' => __('Google+', 'noo'),
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true'),
                        ),
                        array(
                            'param_name' => 'pinterest',
                            'heading' => __('Pinterest', 'noo'),
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true'),
                        ),
                        array(
                            'param_name' => 'linkedin',
                            'heading' => __('LinkedIn', 'noo'),
                            'type' => 'checkbox',
                            'holder' => $param_holder,
                            'value' => array('' => 'true'),
                        ),
                        // array(
                        // 	'param_name'  => 'reddit',
                        // 	'heading'     => __( 'Reddit', 'noo' ),
                        // 	'type'        => 'checkbox',
                        // 	'holder'      => $param_holder,
                        // 	'value'       => array( '' => 'true' ),
                        // ),
                        // array(
                        // 	'param_name'  => 'email',
                        // 	'heading'     => __( 'Email', 'noo' ),
                        // 	'type'        => 'checkbox',
                        // 	'holder'      => $param_holder,
                        // 	'value'       => array( '' => 'true' ),
                        // ),
                        array(
                            'param_name' => $param_visibility_name,
                            'heading' => $param_visibility_heading,
                            'description' => $param_visibility_description,
                            'type' => $param_visibility_type,
                            'holder' => $param_visibility_holder,
                            'value' => $param_visibility_value
                        ),
                        array(
                            'param_name' => $param_class_name,
                            'heading' => $param_class_heading,
                            'description' => $param_class_description,
                            'type' => $param_class_type,
                            'holder' => $param_class_holder
                        ),
                        array(
                            'param_name' => $param_custom_style_name,
                            'heading' => $param_custom_style_heading,
                            'description' => $param_custom_style_description,
                            'type' => $param_custom_style_type,
                            'holder' => $param_custom_style_holder
                        )
                    )
                )
            );
        }

        add_action('init', 'noo_vc_media');

        //
        // Extend container class (parents).
        //
        if (class_exists('WPBakeryShortCodesContainer')) {
            class WPBakeryShortCode_Slider extends WPBakeryShortCodesContainer
            {
            }

            class WPBakeryShortCode_Property_Slider extends WPBakeryShortCodesContainer
            {
            }
        }

        //
        // Extend item class (children).
        //
        if (class_exists('WPBakeryShortCode')) {
            class WPBakeryShortCode_Slide extends WPBakeryShortCode
            {
            }
        }

    endif;

    if (!function_exists('noo_vc_other')) :

        function noo_vc_other()
        {

            //
            // Variables.
            //
            $category_custom = __('Custom', 'noo');
            $param_holder = 'div';


            // [vc_raw_html]
            // ============================
            vc_map_update('vc_raw_html', array(
                'category' => $category_custom,
                'class' => 'noo-vc-element noo-vc-element-raw_html',
                'icon' => 'noo-vc-icon-raw_html',
                'weight' => 490
            ));

            // [vc_raw_js]
            // ============================
            vc_map_update(
                'vc_raw_js',
                array(
                    'category' => $category_custom,
                    'class' => 'noo-vc-element noo-vc-element-raw_js',
                    'icon' => 'noo-vc-icon-raw_js',
                    'weight' => 480));
        }

        add_action('init', 'noo_vc_other');

    endif;
endif;