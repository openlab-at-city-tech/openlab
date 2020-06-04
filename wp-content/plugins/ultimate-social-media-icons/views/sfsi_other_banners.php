   <!------------------------------------------------------Banners on other plugins’ settings pages ----------------------------------------------------------->


   <!---------------recovering sharedcount Check sharecount plugins is active --------------->
   <?php
    if (!is_plugin_active('Ultimate-Premium-Plugin/usm_premium_icons.php')) {
        $current_site_url = 0 . $_SERVER['REQUEST_URI'];
        $sfsi_dismiss_sharecount = unserialize(get_option('sfsi_dismiss_sharecount', false));
        $sfsi_dismiss_gallery = unserialize(get_option('sfsi_dismiss_gallery', false));
        $sfsi_dismiss_optimization = unserialize(get_option('sfsi_dismiss_optimization', false));
        $sfsi_dismiss_gdpr = unserialize(get_option('sfsi_dismiss_gdpr', false));
        $sfsi_dismiss_google_analytic = unserialize(get_option('sfsi_dismiss_google_analytic', false));
        // var_dump($sfsi_dismiss_sharecount,$sfsi_dismiss_gallery,$sfsi_dismiss_optimization,$sfsi_dismiss_gdpr,$sfsi_dismiss_google_analytic);
        foreach ($gallery_plugins as $key => $gallery_plugin) {
            $sfsi_show_gallery_banner = sfsi_check_on_plugin_page($gallery_plugin['dir_slug'], $gallery_plugin['option_name'], $current_site_url);
            if( $gallery_plugin['option_name'] == 'robo-gallery-settings'){
                // var_dump(($sfsi_show_gallery_banner),'lfjgdjkf');
            }
            // var_dump($sfsi_show_gallery_banner,$gallery_plugin['option_name'] );
        
        }
        $socialObj = new sfsi_SocialHelper();
            $current_url = site_url();
            $fb_data = $socialObj->sfsi_get_fb($current_url);
            $check_fb_count_more_than_one = ((!empty($socialObj->format_num($fb_data['like_count'])) || !empty($socialObj->format_num($fb_data['share_count']))) && !empty($socialObj->sfsi_get_pinterest($current_url)));
        ?>
       <?php
            if (is_ssl() && $check_fb_count_more_than_one && ($sfsi_dismiss_sharecount['show_banner'] == "yes" || false == $sfsi_dismiss_sharecount)) {
                // also check if there is likes on http page 
                foreach ($google_analytics as $key => $sharecount_plugin) {
                    $sfsi_show_sharecount_banner = sfsi_check_on_plugin_page($sharecount_plugin['dir_slug'], $sharecount_plugin['option_name'], $current_site_url);
                    if ($sfsi_show_sharecount_banner) {
                        ?>
                   <div class="sfsi_new_prmium_follw sfsi_banner_body">
                       <p style="font-size:18px !important">
                           <b>You’re on https, that’s great! </b>– However: we noticed that you still have share & like counts (from social media) on your old (http://) urls. If you don’t want to lose them, check out <a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_other_plugins_settings_page&utm_campaign=sharedcount_recovery_banner&utm_medium=banner" class="sfsi_font_inherit" target="_blank" style="color:#1a1d20 !important;text-decoration: underline;"><span></span> this plugin</a> which has a share count recovery feature. <a href="https://www.ultimatelysocial.com/usm-premium/?withqp=1&discount=RECOVERSHARECOUNT&utm_source=usmi_other_plugins_settings_page&utm_campaign=sharedcount_recovery_banner&utm_medium=banner" class="sfsi_font_inherit" target="_blank" style="color:#1a1d20 !important;font-weight: bold;"><span>&#10151;</span> <span style="text-decoration: underline;"></span> <span style="text-decoration: underline;">Get it now at 20% discount</span> </a>
                       </p>

                       <div style="text-align:right;">

                           <form method="post" class="sfsi_premiumNoticeDismiss" style="padding-bottom:8px;">

                               <input type="hidden" name="sfsi-dismiss-sharecount" value="true">

                               <input type="submit" name="dismiss" value="Dismiss" />

                           </form>

                       </div>
                   </div>
       <?php
                    }
                    if ($sfsi_show_sharecount_banner) {
                        break;
                    }
                }
            }
            ?>
       <!---------------End check optimization plugins is active--------------->

       <!---------------Pinterest on mouse-over Check gallery plugins is active --------------->
       <?php
            if ($sfsi_dismiss_gallery['show_banner'] == "yes" || false == $sfsi_dismiss_gallery) {
                foreach ($gallery_plugins as $key => $gallery_plugin) {
                    $sfsi_show_gallery_banner = sfsi_check_on_plugin_page($gallery_plugin['dir_slug'], $gallery_plugin['option_name'], $current_site_url);

                    if ($sfsi_show_gallery_banner) {
                        $plugin = sfsi_get_plugin($gallery_plugin['dir_slug']);
                        ?>
                   <div class="sfsi_new_prmium_follw sfsi_banner_body">
                       <div>
                           <p style="margin-bottom: 12px !important;"><b>Get more traffic from your pictures </b>– The Ultimate Social Media Premium Plugin allows to show a Pinterest save-icon after users move over your pictures, increasing sharing activity significantly.
                           </p>
                           <p style="font-size:18px !important">
                               It works very well with the <b><?php echo ($plugin["Name"]); ?> plugin</b> which you are using, resulting in more traffic for your site.
                               <a href="https://www.ultimatelysocial.com/usm-premium/?withqp=1&discount=PINTERESTDISCOUNT&utm_source=usmi_other_plugins_settings_page&utm_campaign=pinterest_mouse_over&utm_medium=banner" class="sfsi_font_inherit" target="_blank" style="color:#1a1d20 !important;font-weight: bold;"><span>&#10151;</span> <span style="text-decoration: underline;"></span>
                                   <span style="text-decoration: underline;">Get it now at 20% discount</span>
                               </a>
                           </p>
                       </div>

                       <div style="text-align:right;">

                           <form method="post" class="sfsi_premiumNoticeDismiss" style="padding-bottom:8px;">

                               <input type="hidden" name="sfsi-dismiss-gallery" value="true">

                               <input type="submit" name="dismiss" value="Dismiss" />

                           </form>

                       </div>
                   </div>
       <?php
                    }
                    if ($sfsi_show_gallery_banner) {
                        break;
                    }
                }
            }
            ?>
       <!---------------End check gallery plugins is active --------------->


       <!---------------Website speed Check optimization plugins is active --------------->
       <?php
            if ($sfsi_dismiss_optimization['show_banner'] == "yes" || false == $sfsi_dismiss_optimization) {
                foreach ($optimization_plugins as $key => $optimization_plugin) {
                    $sfsi_show_optimization_banner = sfsi_check_on_plugin_page($optimization_plugin['dir_slug'], $optimization_plugin['option_name']);
                    if ($sfsi_show_optimization_banner) {
                        ?>
                   <div class="sfsi_new_prmium_follw sfsi_banner_body">
                       <p style="font-size:18px !important">
                           <b>Make your website load faster </b>– the Ultimate Social Media <a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_other_plugins_settings_page&utm_campaign=website_load_faster&utm_medium=banner" class="sfsi_font_inherit" target="_blank" style="color:#1a1d20 !important;text-decoration: underline;"><span></span> Premium Plugin</a> is the most optimized sharing plugin for speed. It also includes support to help you optimize it for minimizing loading time.<a href="https://www.ultimatelysocial.com/usm-premium/?withqp=1&discount=MORESPEEED&utm_source=usmi_other_plugins_settings_page&utm_campaign=website_load_faster&utm_medium=banner" class="sfsi_font_inherit" target="_blank" style="color:#1a1d20 !important;font-weight: bold;"><span>&#10151;</span> <span style="text-decoration: underline;"></span> <span style="text-decoration: underline;">Get it now at 20% discount</span> </a>
                       </p>
                       <div style="text-align:right;">
                           <form method="post" class="sfsi_premiumNoticeDismiss" style="padding-bottom:8px;">

                               <input type="hidden" name="sfsi-dismiss-optimization" value="true">

                               <input type="submit" name="dismiss" value="Dismiss" />

                           </form>

                       </div>
                   </div>

       <?php
                    }
                    if ($sfsi_show_optimization_banner) {
                        break;
                    }
                }
            }
            ?>
       <!---------------End check optimization plugins is active--------------->


       <!---------------GDPR compliance Check GDPR plugins is active--------------->
       <?php
            if ($sfsi_dismiss_gdpr['show_banner'] == "yes" || false == $sfsi_dismiss_gdpr) {

                foreach ($gdpr_plugins as $key => $gdpr_plugin) {
                    $sfsi_show_gdpr_banner = sfsi_check_on_plugin_page($gdpr_plugin['dir_slug'], $gdpr_plugin['option_name'], $current_site_url);
                    if ($sfsi_show_gdpr_banner) {
                        ?>
                   <div class="sfsi_new_prmium_follw sfsi_banner_body">
                       <p style="font-size:18px !important">
                           <b>Make sure your site is GDPR compliant </b>– As part of the Ultimate Social Media Premium Plugin you can request a review (at no extra charge) to check if your sharing icons are GDPR compliant. <a href="https://www.ultimatelysocial.com/usm-premium/?withqp=1&discount=GDPRCOMPLIANT&utm_source=usmi_other_plugins_settings_page&utm_campaign=gdpr_compliance&utm_medium=banner" class="sfsi_font_inherit" target="_blank" style="color:#1a1d20 !important;font-weight: bold;"><span>&#10151;</span> <span style="text-decoration: underline;"></span> <span style="text-decoration: underline;">Get it now at 20% discount</span> </a>
                       </p>
                       <div style="text-align:right;">

                           <form method="post" class="sfsi_premiumNoticeDismiss" style="padding-bottom:8px;">

                               <input type="hidden" name="sfsi-dismiss-gdpr" value="true">

                               <input type="submit" name="dismiss" value="Dismiss" />

                           </form>

                       </div>
                   </div>
       <?php
                    }
                    if ($sfsi_show_gdpr_banner) {
                        break;
                    }
                }
            }
            ?>
       <!---------------End check GDPR plugins is active--------------->


       <!---------------More traffic Check Google analytics plugin is active--------------->
       <?php
            if ($sfsi_dismiss_google_analytic['show_banner'] == "yes" || false == $sfsi_dismiss_google_analytic) {
                foreach ($sharecount_plugins as $key => $google_analytic) {
                    $sfsi_show_google_analytic_banner = sfsi_check_on_plugin_page($google_analytic['dir_slug'], $google_analytic['option_name'], $current_site_url);
                    if ($sfsi_show_google_analytic_banner) {
                        ?>
                   <div class="sfsi_new_prmium_follw sfsi_banner_body">
                       <div>
                           <p style="font-size:18px !important">
                               <b>Get 20%+ more traffic </b>– from more likes & shares with the Ultimatelysocial Premium Plugin. Or get a refund within 20 days. <a href="https://www.ultimatelysocial.com/usm-premium/?withqp=1&discount=MORETRAFFIC&utm_source=usmi_other_plugins_settings_page&utm_campaign=more_traffic&utm_medium=banner" class="sfsi_font_inherit" target="_blank" style="color:#1a1d20 !important;font-weight: bold;"><span>&#10151;</span> <span style="text-decoration: underline;"></span> <span style="text-decoration: underline;">Get it now at 20% discount</span> </a>
                           </p>
                       </div>
                       <div style="text-align:right;">

                           <form method="post" class="sfsi_premiumNoticeDismiss" style="padding-bottom:8px;">

                               <input type="hidden" name="sfsi-dismiss-google-analytic" value="true">

                               <input type="submit" name="dismiss" value="Dismiss" />

                           </form>

                       </div>
                   </div>
   <?php
                }
                if ($sfsi_show_google_analytic_banner) {
                    break;
                }
            }
        }
    }
    ?>
   <!---------------End Check Google analytics plugin is active--------------->


   <!------------------------------------------------------End Banners on other plugins’ settings pages ----------------------------------------------------------->