<?php if (noo_get_option('noo_header_top_bar', false)) :
    $topbar_content = 'html'; // noo_get_option( 'noo_top_bar', 'html' );
    $topbar_social = noo_get_option('noo_top_bar_social', true);
    $topbar_layout = $topbar_social != '' ? noo_get_option('noo_top_bar_social_layout', 'content_left') : noo_get_option('noo_top_bar_layout', 'content_left');

    $topbar_email = $topbar_content == 'html' ? esc_html(noo_get_option('noo_top_bar_email', '')) : '';

    $current_url = noo_current_url();

    if (re_get_agent_setting('users_can_register', true)) {

        $topbar_show_register = $topbar_content == 'html' ? noo_get_option('noo_top_bar_show_register', true) : false;
        $topbar_register_page = !empty($topbar_show_register) ? noo_get_option('noo_top_bar_register_page', '') : '';
        $topbar_register_page = !empty($topbar_register_page) ? get_permalink($topbar_register_page) : '';
        $topbar_register_page = !empty($topbar_register_page) ? $topbar_register_page : wp_registration_url();

        $topbar_show_login = $topbar_content == 'html' ? noo_get_option('noo_top_bar_show_login', true) : false;
        $topbar_login_page = !empty($topbar_show_login) ? noo_get_option('noo_top_bar_login_page', '') : '';
        $topbar_login_page = !empty($topbar_login_page) ? get_permalink($topbar_login_page) : '';
        $topbar_login_page = !empty($topbar_login_page) ? esc_url(add_query_arg('redirect_to', urlencode($current_url), $topbar_login_page)) : wp_login_url($current_url);

        $topbar_logout_url = wp_logout_url($current_url);

    }
    $cUser = wp_get_current_user();
    $url = site_url();
    $profile_url = $url . '/agent-profile/';
    $dashboard_url = $url . '/agent-dashboard';
    $submit_url = $url . '/submit-property';
    $fav_url = $url . '/my-favorites/';
    ?>

    <div class="noo-topbar">
        <div class="topbar-inner <?php echo $topbar_layout; ?> container-boxed max">
            <?php if ($topbar_social != '') : ?>
                <?php noo_social_icons('topbar'); ?>
            <?php endif; ?>
            <?php if ($topbar_content == 'menu') : // Top Menu  ?>
                <div class="topbar-content">
                    <?php if (has_nav_menu('top-menu')) :
                        wp_nav_menu(array(
                            'theme_location' => 'top-menu',
                            'container' => false,
                            'depth' => 1,
                            'menu_class' => 'noo-menu'
                        ));
                    else :
                        echo '<ul class="noo-menu"><li><a href="' . home_url('/') . 'wp-admin/nav-menus.php">' . __('Assign a menu', 'noo') . '</a></li></ul>';
                    endif; ?>
                </div>
            <?php elseif ($topbar_content == 'html') : // HTML content  ?>
                <p class="topbar-content"><?php echo noo_get_option('noo_top_bar_content', ''); ?></p>
            <?php if (!empty($topbar_email) || $topbar_show_register || $topbar_show_login) : ?>
                <ul class="topbar-content">
                    <?php if (!empty($topbar_email)) : ?>
                        <li class="noo-li-icon"><a href="mailto:<?php echo $topbar_email; ?>"><i
                                        class="fa fa-envelope-o"></i>&nbsp;<?php echo __('Email:', 'noo') . $topbar_email; ?>
                            </a></li>
                    <?php endif; ?>
                    <?php if (!empty($topbar_show_register) || !empty($topbar_show_login) && re_get_agent_setting('users_can_register', true)) : ?>
                    <?php if (!is_user_logged_in()) : ?>

                        <?php if (!empty($topbar_show_login)) : ?>
                            <li class="noo-li-icon"><a class="open-form-login" href="<?php echo $topbar_login_page; ?>"><i
                                            class="fa fa-sign-in"></i>&nbsp;<?php echo __('Login', 'noo'); ?></a></li>
                        <?php endif; ?>

                        <?php if (!empty($topbar_show_register)) : ?>
                            <li class="noo-li-icon"><a class="open-form-register"
                                                       href="<?php echo $topbar_register_page; ?>"><i
                                            class="fa fa-key"></i>&nbsp;<?php echo __('Register', 'noo'); ?></a></li>
                        <?php endif; ?>
                        
                    <?php else : ?>
                    <li class="noo-li-icon noo-logout">
                        <a href="#" id="p1">Hello <i><?php echo esc_html($cUser->data->display_name)?></i></a>
                                <ul class=" noo-sub-menu">
                                    <li>
                                        <a href="<?php echo esc_url($profile_url)?>"><i class="fa fa-user"></i> <?php echo __('My Profile', 'noo') ?></a>
                                    </li>
                                    <li>
                                        <a href="<?php echo esc_url($dashboard_url)?>"><i class="fa fa-file-text-o"></i> <?php echo __('My Properties', 'noo') ?></a>
                                    </li>
                                    <li>
                                        <a href="<?php echo esc_url($fav_url)?>"><i class="fa fa-heart"></i> <?php echo __('My Favorites', 'noo') ?></a>
                                    </li>
                                    <li>
                                        <a href="<?php echo esc_url($submit_url)?>"><i class="fa fa-rocket"></i> <?php echo __('Submit Property', 'noo') ?></a>
                                    </li>
                                    <li>
                                        <a href="<?php echo esc_url($topbar_logout_url);  ?>"><i class="fa fa-sign-out"></i> <?php echo __('Logout', 'noo') ?></a>
                                    </li>
                                </ul>
                    </li>
            <?php endif; ?>
            <?php endif; ?>
                </ul>

                <script type="text/javascript">

                </script>
            <?php endif; ?>
            <?php endif; ?>
        </div> <!-- /.topbar-inner -->
    </div> <!-- /.noo-topbar -->

<?php endif; ?>
