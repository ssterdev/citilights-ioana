<?php
/**
 * Created by PhpStorm.
 * User: Vietbrain-09
 * Date: 17-Jul-18
 * Time: 4:30 PM
 */
wp_enqueue_script('3dcarousel');
wp_enqueue_script('afterglow');
function get_video_src($type, $url)
{
    if (!empty($type) && $type == 'youtube') {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        return $id = $match[1];
    }
    if (!empty($type) && $type == 'vimeo') {
        preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $regs);
        return $id = $regs[3];
    }

}
function getVimeoVideoIdFromUrl($url = '') {

    $regs = array();

    $id = '';

    if (preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $regs)) {
        $id = $regs[3];
    }

    return $id;

}
?>
<div class="noo-video-slider">
    <div class="slider-container">
        <div class="slider-content">
            <?php foreach ($items as $i): ?>
                <div class="slider-single">
                    <?php
                    //console_log($i);
                    $src = '';
                    switch ($i['source']){
                        case 'type-s':
                            $src = $i['video'];
                            break;
                        case 'type-y':
                            $src = get_video_src('youtube', $i['youtube_url']);
                            //console_log($src);
                            break;
                        case 'type-v':
                            $src = getVimeoVideoIdFromUrl($i['vimeo_url']);

                            break;
                        default:
                            $src = $i['video'];
                    }
                    ?>
                    <video width="800" height="400" id="noo-player" class="afterglow slider-single-image" controls
                           playsinline
                        <?php if ($i['source'] === 'type-y') {echo " data-youtube-id=".$src;} ?>
                        <?php if ($i['source'] === 'type-v') {echo " data-vimeo-id=".$src;} ?>
                        <?php if (!empty($i['thumbnail'])) {echo " poster=".wp_get_attachment_url($i['thumbnail']);} ?>
                          >
                        <?php if ($i['source'] === 'type-s'): ?>
                            <source src="<?php echo wp_get_attachment_url($i['video']) ?>">
                            Your browser does not support the video tag.
                        <?php endif; ?>
                        <?php if (!empty($i['video'])): ?>
                            <source src="<?php echo wp_get_attachment_url($i['video']) ?>">
                            Your browser does not support the video tag.
                        <?php endif; ?>
                    </video>
                    <div class="slider-single-title">
                        <h5>
                            <?php if (!empty($i['title'])) {
                                echo $i['title'];
                            } ?>
                        </h5>
                        <p><?php if (!empty($i['description'])) {
                                echo $i['description'];
                            } ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div>
            <a class="slider-left" href="javascript:void(0)"><i class="fa fa-angle-left"></i></a>
            <a class="slider-right" href="javascript:void(0)"><i class="fa fa-angle-right"></i></a>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery('document').ready(function ($) {
        //let vid = $('.slider-single-image');
        let vid = $('video');
        let title = $('.slider-single-title');
        vid.on('play', function () {
            title.addClass('disabled');
        })
        vid.on('pause', function () {
            title.removeClass('disabled')
        })

    })

</script>




