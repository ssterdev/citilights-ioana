<?php

wp_enqueue_script('noo-property');
wp_enqueue_script('owlcarousel2');
wp_enqueue_style('owlcarousel2');
$visibility = ($visibility != '') && ($visibility != 'all') ? esc_attr($visibility) : '';
$class = ($class != '') ? 'recent-agents ' . esc_attr($class) : 'recent-agents';
$class .= noo_visibility_class($visibility);

$class = ($class != '') ? ' class="' . esc_attr($class) . '"' : '';
$custom_style = ($custom_style != '') ? ' style="' . $custom_style . '"' : '';
$args = array(
    'posts_per_page' => $number,
    'no_found_rows' => true,
    'post_status' => 'publish',
    'ignore_sticky_posts' => true,
    'post_type' => NooAgent::AGENT_POST_TYPE,
);
$agentShortcodeID = 'agent' . uniqid();
$q = new WP_Query($args);
?>
<?php if ($q->have_posts()): ?>
    <div class="recent-agents recent-agents-slider">
        <?php if ($layout_style == 'style-1'): ?>

            <?php if (!empty($title)): ?>
                <div class="recent-agents-title"><h3><?php echo $title ?></h3></div>
            <?php endif; ?>
            <?php if (!empty($subtitle)): ?>
                <div class="recent-agents-subtitle">
                    <p><?php echo $subtitle ?></p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($layout_style == 'style-2'): ?>
            <div style="text-align: center">
                <?php if (!empty($title)): ?>
                    <div class="recent-agents-title recent-agents-style-2"><h3><?php echo $title ?></h3></div>
                <?php endif; ?>
                <?php if (!empty($subtitle)): ?>
                    <div class="recent-agents-subtitle">
                        <p><?php echo $subtitle ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="recent-agents-content">
            <div id="<?php echo esc_html($agentShortcodeID) ?>">
                <ul class="owl-carousel owl-theme" style="list-style: none; padding: 0">
                    <?php while ($q->have_posts()): $q->the_post();
                        global $post; ?>
                        <?php
                        // Variables
                        $prefix = NooAgent::AGENT_META_PREFIX;
                        $avatar_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');
                        if (empty($avatar_src)) {
                            $avatar_src = NooAgent::get_default_avatar_uri();
                        } else {
                            $avatar_src = $avatar_src[0];
                        }

                        // Agent's info
                        $phone = noo_get_post_meta(get_the_ID(), "{$prefix}_phone", '');
                        $mobile = noo_get_post_meta(get_the_ID(), "{$prefix}_mobile", '');
                        $email = noo_get_post_meta(get_the_ID(), "{$prefix}_email", '');
                        $skype = noo_get_post_meta(get_the_ID(), "{$prefix}_skype", '');
                        $facebook = noo_get_post_meta(get_the_ID(), "{$prefix}_facebook", '');
                        $twitter = noo_get_post_meta(get_the_ID(), "{$prefix}_twitter", '');
                        $google_plus = noo_get_post_meta(get_the_ID(), "{$prefix}_google_plus", '');
                        $linkedin = noo_get_post_meta(get_the_ID(), "{$prefix}_linkedin", '');
                        $pinterest = noo_get_post_meta(get_the_ID(), "{$prefix}_pinterest", '');

                        $column_class = floor(12 / $columns);
                        ?>
                        <li>
                            <?php if ($layout_style == 'style-1'): ?>
                                <article id="agent-<?php the_ID(); ?>" <?php post_class(); ?> >
                                        <div class="agent-featured">
                                            <a class="content-thumb" href="<?php the_permalink() ?>">
                                                <img src="<?php echo $avatar_src; ?>" alt="<?php the_title(); ?>"/>
                                            </a>
                                        </div>
                                        <div class="agent-wrap">
                                            <h2 class="agent-title">
                                                <a href="<?php the_permalink(); ?>"
                                                   title="<?php the_title(); ?>"><?php the_title(); ?></a>
                                            </h2>
                                            <div class="agent-excerpt">
                                                <?php if ($excerpt = apply_filters('the_content', $post->post_content)): ?>
                                                    <?php
                                                    $num_word = 20;
                                                    $excerpt = strip_shortcodes($excerpt);
                                                    echo '<p>' . wp_trim_words($excerpt, $num_word, '...') . '</p>';
                                                    ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="agent-social">
                                                <?php echo(!empty($facebook) ? '<a class="fa fa-facebook" href="' . $facebook . '"></a>' : ''); ?>
                                                <?php echo(!empty($twitter) ? '<a class="fa fa-twitter" href="' . $twitter . '"></a>' : ''); ?>
                                                <?php echo(!empty($google_plus) ? '<a class="fa fa-google-plus" href="' . $google_plus . '"></a>' : ''); ?>
                                                <?php echo(!empty($linkedin) ? '<a class="fa fa-linkedin" href="' . $linkedin . '"></a>' : ''); ?>
                                                <?php echo(!empty($pinterest) ? '<a class="fa fa-pinterest" href="' . $pinterest . '"></a>' : ''); ?>
                                            </div>
                                    </div>
                                </article>
                            <?php endif; ?>
                            <?php if ($layout_style == 'style-2'): ?>
                                <article id="agent-<?php the_ID(); ?>" <?php post_class(); ?> >
                                    <div id="agent-content" style="border: 1px solid rgba(0,0,0,.1)">
                                        <div class="agent-featured-2">
                                            <a class="content-thumb" href="<?php the_permalink() ?>">
                                                <img src="<?php echo $avatar_src; ?>" alt="<?php the_title(); ?>"/>
                                            </a>
                                        </div>
                                        <div class="agent-wrap">
                                            <h2 class="agent-title">
                                                <a href="<?php the_permalink(); ?>"
                                                   title="<?php the_title(); ?>"><?php the_title(); ?></a>
                                            </h2>
                                            <div class="agent-excerpt">
                                                <?php if ($excerpt = apply_filters('the_content', $post->post_content)): ?>
                                                    <?php
                                                    $num_word = 20;
                                                    $excerpt = strip_shortcodes($excerpt);
                                                    echo '<p>' . wp_trim_words($excerpt, $num_word, '...') . '</p>';
                                                    ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="agent-social">
                                                <?php echo(!empty($facebook) ? '<a class="fa fa-facebook" href="' . $facebook . '"></a>' : ''); ?>
                                                <?php echo(!empty($twitter) ? '<a class="fa fa-twitter" href="' . $twitter . '"></a>' : ''); ?>
                                                <?php echo(!empty($google_plus) ? '<a class="fa fa-google-plus" href="' . $google_plus . '"></a>' : ''); ?>
                                                <?php echo(!empty($linkedin) ? '<a class="fa fa-linkedin" href="' . $linkedin . '"></a>' : ''); ?>
                                                <?php echo(!empty($pinterest) ? '<a class="fa fa-pinterest" href="' . $pinterest . '"></a>' : ''); ?>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <?php if ($layout_style == 'style-1'): ?>
                <a class="owl2-prev" id="agent-prev"></a>
                <a class="owl2-next" id="agent-next"></a>
            <?php endif; ?>
        </div>
    </div>
    <script type="text/javascript">
        jQuery('document').ready(function ($) {
            let agent = $('#<?php echo $agentShortcodeID ?> ul');
            agent.owlCarousel({
                items: <?php echo esc_js($columns)?>,
                loop: true,
                rtl: <?php echo is_rtl() ? 'true' : 'false'; ?>,
                autoplay: <?php echo esc_js($autoplay)?>,
                autoplayTimeout: <?php echo esc_js($slider_time)?>,
                autoplayHoverPause: true,
                autoplaySpeed: <?php echo esc_js($slider_speed)?>,
                lazyLoad: true,
                responsiveClass: true,
                dots: false,
                margin: 20,
                responsive: {
                    0: {
                        items: 1,
                    },
                    600: {
                        items: 2,
                    },
                    1000: {
                        items: <?php echo esc_js($columns)?>,
                    }
                }
            })
            $('#agent-prev').click(function () {
                agent.trigger('prev.owl.carousel');
            })
            $('#agent-next').click(function () {
                agent.trigger('next.owl.carousel');
            })
        })

    </script>
    <?php
    wp_reset_query();
    wp_reset_postdata();
endif;