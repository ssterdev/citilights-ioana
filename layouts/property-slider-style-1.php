<?php
/**
 * Created by PhpStorm.
 * User: Vietbrain-09
 * Date: 19-Jul-18
 * Time: 11:59 AM
 */
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
$html[] = "    responsive: true,";
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
?>