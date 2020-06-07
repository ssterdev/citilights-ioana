<?php
/**
 * Created by PhpStorm.
 * User: Vietbrain-09
 * Date: 06-thg 8-18
 * Time: 3:00 CH
 */
$counter_uid = uniqid("counter_")
?>
<div class="noo-counter <?php echo $class?>" id="<?php echo $counter_uid?>" <?php echo $custom_style?>>
    <p style="color: <?php echo esc_attr($color)?>">
        <span class="timer"></span>
        <span><?php echo $count_unit?></span>
    </p>
    <h4  style="color: <?php echo esc_attr($color)?>"><?php echo $title?></h4>
</div>
<script type="text/javascript">
    jQuery(function($) {
        let counter = '#<?php echo $counter_uid?> .timer';
        let watcher = 0;
        $(window).scroll(function() {
            var hT = $(counter).offset().top,
                hH = $(counter).outerHeight(),
                wH = $(window).height(),
                total = hT + hH - wH,
                wS = $(this).scrollTop();
            if (watcher < 2 && wS > total) {
                $(counter).countTo({
                    from: <?php echo esc_js($start_number)?>,
                    to: <?php echo esc_js($end_number)?>,
                    speed: <?php echo esc_js($duration) ?>,
                    refreshInterval: 50,
                    onComplete: function(value) {
                        console.debug(this);
                    }
                });
                watcher++;
            }
        });
    });
</script>
