<?php
/**
 * Created by PhpStorm.
 * User: Vietbrain-09
 * Date: 17-Jul-18
 * Time: 8:52 AM
 */
if ($post_ids !== 'all') {
    $args = array(
        'orderby' => 'post__in',
        'post__in' => explode(",", $post_ids),
        'posts_per_page' => -1,
        'post_type' => 'post',
        'post_status' => 'publish',
    );
}
$args = array(
    'orderby' => 'date',
    'posts_per_page' => -1,
    'post_type' => 'post',
    'post_status' => 'publish',
);

if ($data_source == 'list_cat') {

    $args = array(
        'posts_per_page' => -1,
        'orderby' => 'post_date',
        'post_type' => 'post',
        'post_status' => 'publish',
    );
    if (!empty($category)) {
        $args['tax_query'] = array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'category',
                'field' => 'id',
                'terms' => explode(',', $category),
            ),
        );
    }
}
$query = new WP_Query($args);
$postList = $query->posts;
if (!function_exists('getUser')) {
    function getUser($ID) {
        $u = get_user_by('ID', $ID);
        $userInfo = $u->data;
        return $userInfo->display_name;
    }
}
if (!function_exists('parseDate')) {
    function parseDate($date) {
        $d = date_create($date);
        $dFormat = date_format($d, "d F");
        return $dFormat;
    }
}
$defaultIMG = get_template_directory_uri().'/assets/images/placeholder.jpg';
// $t = get_post_permalink(isset($postList[1]->ID) );
$recentPostID = uniqid('blog_');

?>
<div>
    <div id="<?php echo $recentPostID?>" class="owl-carousel owl-theme">
        <?php foreach ($postList as $post): ?>
            <article>
                <div class="recent-post">
                    <div class="recent-post-thumb">
                        <a class="content-thumb" href="<?php echo get_post_permalink($post->ID); ?>">
                            <?php
                            $img = get_the_post_thumbnail($post->ID);
                            if ($img != null) {
                                echo $img;
                            } if ($img == null) {
                                echo '<img src="'.$defaultIMG.'">';
                            } ?>
                        </a>
                        <div class="post-thumb-hover">
                            <a href="<?php echo get_post_permalink($post->ID); ?>"
                               title="<?php echo $post->post_title ?>">
                                <i class="fa fa-search"></i>
                            </a>
                        </div>
                    </div>
                    <div class="recent-post-wrap">
                        <div>
                            <div class="recent-post-title">
                                <h5><a href="<?php echo get_post_permalink($post->ID); ?>"
                                       title="<?php echo $post->post_title ?>">
                                        <?php echo $post->post_title?>
                                    </a>
                                </h5>
                                <small class="recent-post-author">
                                    <?php _e('Posted By ', 'noo'); ?><span><a href="<?php echo get_author_posts_url($post->post_author)?>">
                                            <?php echo getUser($post->post_author);?>
                                        </a></span>
                                </small>
                            </div>
                            <div class="recent-post-excerpt">
                                <?php
                                $num_word = 30;
                                $excerpt = strip_shortcodes($post->post_content);
                                echo '<p>' . wp_trim_words($excerpt, $num_word, '...') . '</p>';
                                ?>
                            </div>
                        </div>
                        <div class="recent-post-summary">
                            <i class="fa fa-calendar"></i> <?php echo ' '.parseDate($post->post_date)?>
                            <a href="<?php echo get_post_permalink($post->ID); ?>" style="float: right"><?php echo esc_html__("Read more", 'noo'); ?>
                                    <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <div class="recent-news-nav">
        <a class="owl2-next <?php echo $recentPostID ?>-next"></a>
        <a class="owl2-prev <?php echo $recentPostID ?>-prev"></a>
    </div>
</div>
<script type="text/javascript">
    jQuery('document').ready(function ($) {
        let blog = $('#<?php echo $recentPostID?>');
        blog.owlCarousel({
            items: <?php echo $post_per_page?>,
            loop: true,
            rtl: <?php echo is_rtl() ? 'true' : 'false'; ?>,
            autoplay: <?php echo esc_js($autoplay) ?>,
            autoplayTimeout: <?php echo esc_js($duration) ?>,
            autoplayHoverPause: true,
            lazyLoad: true,
            autoplaySpeed: 500,
            dots: false,
            responsiveClass: true,
            margin: 25,
            responsive: {
                0: {
                    items: 1,
                },
                600: {
                    items: 2,
                },
                1000: {
                    items: <?php echo $post_per_page?>,
                }
            }
        });
        $('.<?php echo $recentPostID ?>-next').click(function () {
            blog.trigger('next.owl.carousel');
        });
        $('.<?php echo $recentPostID ?>-prev').click(function () {
            blog.trigger('prev.owl.carousel');
        });

    });
</script>