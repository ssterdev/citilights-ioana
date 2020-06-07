<?php
$full_path = __FILE__;
$path = explode('wp-content', $pull_path);
require_once ( $path[0] . '/wp-load.php');

?>
<div id="noo-slider-6" class="noo-slider top-indicators testimonial-slide">
    <div class="caroufredsel_wrapper">

        <ul class="sliders">
            <li class="slide-item" style="width: 1170px;">
                <div class="slide-content">
                    <div class="testimonial-desc">
                        <?php echo implode("\n", $quote_list); ?>
                    </div>
                    <div class="our-customer-info">
                        <p>
                            <a class="col-sm-6" href="#">
                                <img data-source="' . $count . '" class="grayscale" src="' . wp_get_attachment_url(esc_attr($url)) . '" alt="' . esc_html($post->post_title) . '" width="90" height="100"/>'
                            </a>

                        </p>
                        <div class="custom-desc col-sm-6">
                            <h4>.$name.</h4>
                            <p>.$position.</p>
                        </div>
                    </div>
                </div>

            </li>
        </ul>
    </div>
    <div class="clearfix">

    </div>
</div>
