<?php
if (!function_exists('re_recent_properties_shortcode')) :
    function re_recent_properties_shortcode($atts, $content = null)
    {
        wp_enqueue_script('noo-property');
        extract(shortcode_atts(array(
            'title' => '',
            'type' => 'list',
            'property_id' => '',
            'property_category' => '',
            'property_status' => '',
            'property_label' => '',
            'property_location' => '',
            'property_sub_location' => '',
            'number' => '6',
            'show' => '',
            'style' => 'grid',
            'slider_time' => '3000',
            'slider_speed' => '600',
            'show_control' => false,
            'show_pagination' => 'no',
            'visibility' => '',
            'class' => '',
            'order_by' => 'date',
            'order' => 'desc',
            'custom_style' => '',
            'autoplay' => 'true',
            'item_per_slide' => '3',
            'description' => '',
            'prop_style' => 'style-1',
        ), $atts));
        $visibility = ($visibility != '') && ($visibility != 'all') ? esc_attr($visibility) : '';
        $class = ($class != '') ? 'recent-properties ' . esc_attr($class) : 'recent-properties';
        $class .= noo_visibility_class($visibility);
        $class = ($class != '') ? ' class="' . esc_attr($class) . '"' : '';
        $custom_style = ($custom_style != '') ? ' style="' . $custom_style . '"' : '';

        $params = array(
            'type' => $type,
            'property_id' => $property_id,
            'property_category' => $property_category,
            'property_status' => $property_status,
            'property_label' => $property_label,
            'property_location' => $property_location,
            'property_sub_location' => $property_sub_location,
            'number' => $number,
            'show' => $show,
            'order_by' => $order_by,
            'order' => $order,
        );
        $q = re_build_properties_query($params);

        ob_start();
        wp_enqueue_style('owlcarousel2');
        wp_enqueue_script('owlcarousel2');
        include(locate_template("layouts/shortcode-recent-properties.php"));
        return ob_get_clean();
    }

    add_shortcode('noo_recent_properties', 're_recent_properties_shortcode');
endif;

if (!function_exists('re_single_property_shortcode')) :
    function re_single_property_shortcode($atts, $content = null)
    {
        $atts = wp_parse_args($atts, array(
            'title' => '',
            'type' => 'single',
            'property_id' => '',
            'style' => 'featured',
            'visibility' => '',
            'class' => '',
            'custom_style' => ''
        ));

        if (empty($atts['property_id'])) return '';

        if ($atts['style'] == 'detail') {
            extract($atts);

            $args = array(
                'p' => $atts['property_id'],
                'post_type' => 'noo_property'
            );

            $query = new WP_Query($args);

            if (!empty($atts['title'])) : ?>
                <div class="recent-properties-title"><h3><?php echo esc_html($atts['title']); ?></h3></div>
            <?php endif;

            return re_property_detail($query);
        }

        return re_recent_properties_shortcode($atts, $content);
    }

    add_shortcode('noo_single_property', 're_single_property_shortcode');
endif;

if (!function_exists('re_property_slider_shortcode')) :
    function re_property_slider_shortcode($atts, $content = null)
    {
        re_property_enqueue_gmap_script();
        wp_enqueue_script('noo-property');
        $default_fields = re_property_summary_fields();
        $default_field_icons = re_property_summary_field_icons_2();
        extract(shortcode_atts(array(
            'visibility' => '',
            'class' => '',
            'id' => '',
            'custom_style' => '',
            'animation' => 'slide',
            'visible_items' => '1',
            'slider_time' => '3000',
            'slider_speed' => '600',
            'slider_height' => '700',
            'auto_play' => '',
            'indicator' => '',
            'prev_next_control' => '',
            'show_search_form' => '',
            'advanced_search' => '',
            'show_search_info' => 'true',
            'search_info_title' => null,
            'search_info_content' => null,
            'property_source' => 'specific',
            'property_category' => '',
            'property_status' => '',
            'property_label' => '',
            'property_location' => '',
            'property_sub_location' => '',
            'number' => '4',
            'show' => '',
            'field_1' => '',
            'field_2' => '',
            'field_3' => '',
            'field_4' => '_price',
            'field_icon_1' => '',
            'field_icon_2' => '',
            'field_icon_3' => '',
            'field_icon_4' => '',

        ), $atts));

        wp_enqueue_script('vendor-carouFredSel');

        $show_search_form = ($show_search_form == 'true');
        if (!$show_search_form) {
            $search_info_title = '';
            $search_info_content = '';
        }
        $show_search_info = $show_search_form ? ($show_search_info == 'true') : false;
        $class = ($class != '') ? esc_attr($class) : '';
        $visibility = ($visibility != '') && ($visibility != 'all') ? esc_attr($visibility) : '';
        $class .= noo_visibility_class($visibility);
        ob_start();

            $html = array();

            $id = ($id != '') ? esc_attr($id) : 'noo-slider-' . noo_vc_elements_id_increment();

            $class .= ' property-slider';

            $class = ($class != '') ? 'class="' . $class . '"' : '';
            $custom_style = ($custom_style != '') ? 'style="' . $custom_style . '"' : '';

            $indicator_html = array();
            $indicator_js = array();
            if ($indicator == 'true') {
                $indicator_js[] = '    pagination: {';
                $indicator_js[] = '      container: "#' . $id . '-pagination"';
                $indicator_js[] = '    },';

                $indicator_html[] = '  <div id="' . $id . '-pagination" class="slider-indicators"></div>';
            }

            $prev_next_control_html = array();
            $prev_next_control_js = array();
            if ($prev_next_control == 'true') {
                $prev_next_control_js[] = '    prev: {';
                $prev_next_control_js[] = '      button: "#' . $id . '-prev"';
                $prev_next_control_js[] = '    },';
                $prev_next_control_js[] = '    next: {';
                $prev_next_control_js[] = '      button: "#' . $id . '-next"';
                $prev_next_control_js[] = '    },';

                $prev_next_control_html[] = '  <a id="' . $id . '-prev" class="slider-control prev-btn" role="button" href="#"><span class="slider-icon-prev"></span></a>';
                $prev_next_control_html[] = '  <a id="' . $id . '-next" class="slider-control next-btn" role="button" href="#"><span class="slider-icon-next"></span></a>';
            }

            $swipe = $pause_on_hover = 'true';
            $animation = ($animation == 'slide') ? 'scroll' : $animation; // Not allow fading with carousel

            $slider_content = '';
            if ($property_source == 'specific') {
                $slider_content = do_shortcode($content);
            } elseif ($property_source == 'auto') {
                $params = array(
                    'property_category' => $property_category,
                    'property_status' => $property_status,
                    'property_label' => $property_label,
                    'property_location' => $property_location,
                    'property_sub_location' => $property_sub_location,
                    'number' => $number,
                    'show' => $show,
                    'return' => 'args'
                );
                $args = re_build_properties_query($params);
                $args['fields'] = 'ids';
                $args['suppress_filters'] = false;

                $property_ids = get_posts($args);
                if (!empty($property_ids)) {
                    $slider_content = array();
                    foreach ($property_ids as $id) {
                            $slider_content[] = do_shortcode('[property_slide property_id="' . $id . '" field_1="' . $field_1 . '" field_icon_1="' . $field_icon_1 . '" field_2="' . $field_2 . '" field_icon_2="' . $field_icon_2 . '" field_3="' . $field_3 . '" field_icon_3="' . $field_icon_3 . '" field_4="' . $field_4 . '" field_icon_4="' . $field_icon_4 . '"]');

                    }
                    $slider_content = implode("\n", $slider_content);
                }
            }

            $html[] = '<div ' . $class . ' ' . $custom_style . '>';
            $html[] = "<div id=\"{$id}\" class=\"noo-slider noo-property-slide-wrap\">";
            $html[] = '  <ul class="sliders">';

            $html[] = $slider_content;
            $html[] = '  </ul>';
            $html[] = '  <div class="clearfix"></div>';
            $html[] = implode("\n", $indicator_html);
            $html[] = implode("\n", $prev_next_control_html);
            $html[] = '</div>';
            if ($show_search_form) {
                ob_start();
                $args = array(
                    'gmap' => false,
                    'search_info' => $show_search_info,
                    'show_advanced_search_field' => !!$advanced_search,
                    'search_info_title' => $search_info_title,
                    'search_info_content' => $search_info_content,
                );
                re_property_advanced_map($args);
                $html[] = ob_get_clean();
            }
            $html[] = '</div>';

// slider script
            $html[] = '<script>';
            $html[] = "jQuery('document').ready(function ($) {";
            $html[] = " $('#{$id} .sliders').each(function(){";
            $html[] = '  var _this = $(this);';
            $html[] = '  imagesLoaded(_this,function(){';
            $html[] = "   _this.carouFredSel({";
            $html[] = "    infinite: true,";
            $html[] = "    circular: true,";
            $html[] = "    direction: true,";
            $html[] = "    debug : false,";
            $html[] = '    scroll: {';
            $html[] = '      items: 1,';
            $html[] = ($slider_speed != '') ? '      duration: ' . $slider_speed . ',' : '';
            $html[] = ($pause_on_hover == 'true') ? '      pauseOnHover: "resume",' : '';
            $html[] = '      fx: "' . $animation . '"';
            $html[] = '    },';
            $html[] = '    auto: {';
            $html[] = ($slider_time != '') ? '      timeoutDuration: ' . $slider_time . ',' : '';
            $html[] = ($auto_play == 'true') ? '      play: true' : '      play: false';
            $html[] = '    },';
            $html[] = implode("\n", $prev_next_control_js);
            $html[] = implode("\n", $indicator_js);
            $html[] = '    swipe: {';
            $html[] = "      onTouch: {$swipe},";
            $html[] = "      onMouse: {$swipe}";
            $html[] = '    }';
            $html[] = '   });';
            $html[] = '  });';
            $html[] = ' });';
            $html[] = '});';
            $html[] = '</script>';
            if (!empty($slider_height)) {
                $html[] = '<style>';
                $html[] = "  #{$id}.noo-slider > .sliders { height: {$slider_height}px; }";
                $html[] = "  #{$id}.noo-slider .caroufredsel_wrapper .sliders .slide-item.noo-property-slide { max-height: {$slider_height}px; }";
                $html[] = '</style>';
            }

            return implode("\n", $html);

        return ob_get_clean();
    }

    add_shortcode('property_slider', 're_property_slider_shortcode');
endif;

if (!function_exists('re_property_slide_shortcode')) :
    function re_property_slide_shortcode($atts, $content = null)
    {

        $default_fields = re_property_summary_fields();
        $default_field_icons = re_property_summary_field_icons_2();

        extract(shortcode_atts(array(
            'property_id' => '',
            'background_type' => 'thumbnail',
            'image' => '',
            'field_1' => isset($default_fields[0]) ? $default_fields[0] : '',
            'field_2' => isset($default_fields[1]) ? $default_fields[1] : '',
            'field_3' => isset($default_fields[2]) ? $default_fields[2] : '',
            'field_4' => '_price',
            'field_icon_1' => '',
            'field_icon_2' => '',
            'field_icon_3' => '',
            'field_icon_4' => '',

        ), $atts));
        if (empty($property_id))
            return '';

        $fields = array();
        $field_icons = array();
        if (!empty($field_1)) {
            $fields[] = $field_1;
            $field_icons[] = empty($field_icon_1) ? $default_field_icons[0] : wp_get_attachment_image_url($field_icon_1);
        }
        if (!empty($field_2)) {
            $fields[] = $field_2;
            $field_icons[] = empty($field_icon_2) ? $default_field_icons[1] : wp_get_attachment_image_url($field_icon_2);
        }
        if (!empty($field_3)) {
            $fields[] = $field_3;
            $field_icons[] = empty($field_icon_3) ? $default_field_icons[2] : wp_get_attachment_image_url($field_icon_3);
        }
        if (!empty($field_4)) {
            $fields[] = $field_4;
            $field_icons[] = empty($field_icon_4) ? '' : wp_get_attachment_image_url($field_icon_4);
        }

        $property = get_post($property_id);
        if (empty($property))
            return '';

        ob_start();
        include(locate_template("layouts/shortcode-property-slide.php"));
        return ob_get_clean();
    }

    add_shortcode('property_slide', 're_property_slide_shortcode');
endif;


if (!function_exists('re_advanced_search_property_shortcode')) :
    function re_advanced_search_property_shortcode($atts, $content = null)
    {
        extract(shortcode_atts(array(
            'title' => '',
            'source' => 'property',
            'map_height' => '',
            'style' => 'horizontal',
            'idx_map_search_form' => '',
            'disable_map' => '',
            'disable_search_form' => '',
            'advanced_search' => '',
            'no_search_container' => '',
            'show_loading' => 'true',
            'visibility' => '',
            'class' => '',
            'custom_style' => '',
            'form_layout' => 'style-1',
        ), $atts));
        $style = !!$disable_search_form ? '' : $style;
        $show_advanced_search_field = ($style == 'horizontal') ? !!$advanced_search : false;
        $map_class = ($style == 'vertical') ? 'search-vertical' : '';
        $disable_map = ($disable_map == 'true');
        re_property_enqueue_gmap_script(!$disable_map && $source == 'property');

        $no_search_container = $disable_map ? ($no_search_container == 'true') : false;
        if ($source == 'IDX') {
            $disable_search_form = true;
            $advanced_search = false;
            if ($idx_map_search_form == 'true') $idx_map_search_form = true;
            else $idx_map_search_form = false;
        }

        $visibility = ($visibility != '') && ($visibility != 'all') ? esc_attr($visibility) : '';
        $class = ($class != '') ? esc_attr($class) : '';
        $class .= noo_visibility_class($visibility);
        $class .= ' hidden-print';
        $custom_style = ($custom_style != '') ? ' style="' . $custom_style . '"' : '';
        ob_start();
        ?>
        <div class="noo_advanced_search_property <?php echo $style . ' ' . $class ?>" <?php echo $custom_style ?>>
            <?php
            $args = array(
                'gmap' => !$disable_map,
                'map_class' => $map_class,
                'show_status' => true,
                'no_search_container' => $no_search_container,
                'source' => $source,
                'idx_map_search_form' => $idx_map_search_form,
                'disable_search_form' => $disable_search_form,
                'show_advanced_search_field' => $show_advanced_search_field,
                'map_height' => $map_height,
                'show_loading' => $show_loading,
                'form_layout' => $form_layout,
                'search_info_title' => $title,
            );
            re_property_advanced_map($args);
            ?>
        </div>
        <?php
        return ob_get_clean();
    }


    add_shortcode('noo_advanced_search_property', 're_advanced_search_property_shortcode');
endif;
