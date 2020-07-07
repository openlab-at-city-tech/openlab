<!------------------------------------------------------ Global Banners ----------------------------------------------------------->
<?php
// sfsi_has_gdpr_plugin_activated($gdpr_plugins);
if (!is_plugin_active('Ultimate-Premium-Plugin/usm_premium_icons.php')) {
    $sfsi_banner_global_firsttime_offer = unserialize(get_option('sfsi_banner_global_firsttime_offer', false));
    $sfsi_banner_global_pinterest = unserialize(get_option('sfsi_banner_global_pinterest', false));
    $sfsi_banner_global_social = unserialize(get_option('sfsi_banner_global_social', false));
    $sfsi_banner_global_load_faster = unserialize(get_option('sfsi_banner_global_load_faster', false));
    $sfsi_banner_global_shares = unserialize(get_option('sfsi_banner_global_shares', false));
    $sfsi_banner_global_gdpr = unserialize(get_option('sfsi_banner_global_gdpr', false));
    $sfsi_banner_global_http = unserialize(get_option('sfsi_banner_global_http', false));
    $sfsi_banner_global_upgrade = unserialize(get_option('sfsi_banner_global_upgrade', false));

    // var_dump(
    //     $sfsi_banner_global_firsttime_offer,
    //     $sfsi_banner_global_pinterest,
    //     $sfsi_banner_global_social,
    //     $sfsi_banner_global_load_faster,
    //     $sfsi_banner_global_shares,
    //     $sfsi_banner_global_gdpr,
    //     $sfsi_banner_global_http,
    //     $sfsi_banner_global_upgrade
    // );

    $sfsi_install_time = strtotime(get_option('sfsi_installDate'));
    $sfsi_max_show_time = $sfsi_install_time + (60 * 60);
    $sfsi_current_time = (date('Y-m-d h:i:s'));

    $sfsi_loyalty = get_option("sfsi_loyaltyDate");

    $sfsi_min_loyalty_time = date('Y-m-d H:i:s', strtotime($sfsi_loyalty . get_option('sfsi_installDate')));


    $socialObj = new sfsi_SocialHelper();
    $current_url =  site_url();
    $fb_data = $socialObj->sfsi_get_fb($current_url);
    $check_fb_count_more_than_one = $fb_data > 0 || $socialObj->sfsi_get_pinterest($current_url) > 0;



    if (
        $sfsi_banner_global_firsttime_offer['is_active'] == "yes"
    ) {
        if ($sfsi_max_show_time >= strtotime(date('Y-m-d h:i:s')) && (!sfsi_check_not_show_other_plugin_settings_page($gallery_plugins, $optimization_plugins, $sharecount_plugins, $google_analytics, $gdpr_plugins))) :
            ?>
            <!---------------New installs discount--------------->
            <div id="sfsi_firsttime_offer" class="sfsi_new_prmium_follw  sfsi_banner_body">
                <div>
                    <p style="margin-bottom: 12px !important;">You seem to have installed the Ultimate Social media plugin for the first time – Thank you & Welcome!</p>
                    <p style="font-size:18px !important">
                        For newbies we have a special offer: get the Premium Plugin within the <span class='sfsi_new_premium_counter' style="text-decoration: underline;">next <?php echo ceil(($sfsi_max_show_time - strtotime(date('Y-m-d h:i:s'))) / 60) ?> minutes</span>  and benefit from a discount of 30%! <a href="https://www.ultimatelysocial.com/usm-premium/?withqp=1&discount=NEWINSTALL&utm_source=usmi_global&utm_campaign=new_installs&utm_medium=banner" class="sfsi_font_inherit" target="_blank" style="color:#1a1d20 !important;font-weight: bold;"><span>&#10151;</span> <span style="text-decoration: underline;">Get it now</span></a>
                    </p>
                </div>
                <div style="text-align:right;">

                    <form method="post" class="sfsi_premiumNoticeDismiss" style="padding-bottom:8px;">

                        <input type="hidden" name="sfsi-banner-global-firsttime-offer" value="true">
                        <input type="submit" name="dismiss" value="Dismiss" />

                    </form>

                </div>
            </div>
            <script>
                window.sfsi_firsttime_timerstart = <?php echo ceil(($sfsi_max_show_time - strtotime(date('Y-m-d h:i:s'))) / 60) ?>;
                window.sfsi_firsttime_timerId = window.setInterval(function() {
                    if (window.sfsi_firsttime_timerstart <= 0) {
                        var sfsi_firsttime_offer_banners = document.getElementsByClassName("sfsi_firsttime_offer");
                        if (sfsi_firsttime_offer_banners.length > 0) {
                            sfsi_firsttime_offer_banners[0].style.display = "none";
                            window.clearInterval(window.sfsi_firsttime_timerstart);
                        }
                    } else {
                        var counters = document.getElementsByClassName("sfsi_new_premium_counter");
                        if (counters.length > 0) {
                            var counter = counters[0];
                            window.sfsi_firsttime_timerstart = window.sfsi_firsttime_timerstart - 1;
                            counter.innerText = window.sfsi_firsttime_timerstart;
                        }
                    }
                }, 60 * 1000);
            </script>
            <!---------------End New installs discount--------------->
    <?php endif;
        }
        ?>

    <!--------------- Show Pinterest on mouse-over--------------->
    <?php


        if (sfsi_check_banner_criteria($sfsi_banner_global_pinterest, $gallery_plugins, $optimization_plugins, $sharecount_plugins, $google_analytics, $gdpr_plugins, $sfsi_current_time)) { ?>
        <div class="sfsi_new_prmium_follw sfsi_banner_body">
            <div>
                <p style="margin-bottom: 12px !important;"><b>Get more traffic from your pictures </b>– The Ultimate Social Media Premium Plugin allows to show a Pinterest save-icon after users move over your pictures, increasing sharing activity significantly.
                    <a href="https://www.ultimatelysocial.com/usm-premium/?withqp=1&discount=PINTERESTICON&utm_source=usmi_global&utm_campaign=pinterest_on_mouse_over&utm_medium=banner" class="sfsi_font_inherit" target="_blank" style="color:#1a1d20 !important;font-weight: bold;"><span>&#10151;</span> <span style="text-decoration: underline;"></span> <span style="text-decoration: underline;">Get it now at 20% discount</span></a>
                </p>
            </div>

            <div style="text-align:right;">

                <form method="post" class="sfsi_premiumNoticeDismiss" style="padding-bottom:8px;">

                    <input type="hidden" name="sfsi-banner-global-pinterest" value="true">

                    <input type="submit" name="dismiss" value="Dismiss" />

                </form>

            </div>
        </div>
    <?php
        }
        ?>
    <!---------------End  Show Pinterest on mouse-over--------------->

    <!--------------- Show Icons don’t show on mobile--------------->
    <?php

        if (sfsi_check_banner_criteria($sfsi_banner_global_social, $gallery_plugins, $optimization_plugins, $sharecount_plugins, $google_analytics, $gdpr_plugins, $sfsi_current_time)) { ?>
        <div class="sfsi_new_prmium_follw sfsi_banner_body sfsi_warning_banner">
            <div>
                <p style="margin-bottom: 12px !important;">Your social media & sharing icons<b> don’t seem to show on mobile.</b> If you want to increase sharing & traffic to your site it is very important that they do (>50% of traffic is from mobile). </p>
                <p style="font-size:18px !important">
                    Please go to the <a href="<?php echo admin_url('/admin.php?page=sfsi-options'); ?>" class="sfsi_font_inherit" target="_blank" style="color:#1a1d20 !important;text-decoration: underline;"><span></span> Ultimate Social Media plugin page</a> and ensure you made the right selections. If they still don’t show it could be an issue with your theme. Our premium plugin allows to place the icons separately for mobile, which always fixes this issue. <a href="https://www.ultimatelysocial.com/usm-premium/?withqp=1&discount=MOBILEICONS&utm_source=usmi_global&utm_campaign=mobile_icons_banner&utm_medium=banner" class="sfsi_font_inherit" target="_blank" style="color:#1a1d20 !important;font-weight: bold;"><span>&#10151;</span> <span style="text-decoration: underline;"></span> <span style="text-decoration: underline;">Get it now at 20% discount</span></a>
                </p>
            </div>
            <div style="text-align:right;">

                <form method="post" class="sfsi_premiumNoticeDismiss" style="padding-bottom:8px;">

                    <input type="hidden" name="sfsi-banner-global-social" value="true">

                    <input type="submit" name="dismiss" value="Dismiss" />

                </form>

            </div>
        </div>
    <?php
        }
        ?>
    <!---------------End  Show Icons don’t show on mobile--------------->

    <!--------------- Improve your website speed--------------->
    <?php
        if (sfsi_check_banner_criteria($sfsi_banner_global_load_faster, $gallery_plugins, $optimization_plugins, $sharecount_plugins, $google_analytics, $gdpr_plugins, $sfsi_current_time)) {
            ?>
        <div class="sfsi_new_prmium_follw sfsi_banner_body">
            <div>
                <p style="font-size:18px !important">
                    <b>Make your website load faster</b> – the Ultimate Social Media Premium Plugin is the most optimized sharing plugin for speed. It also includes support to help you optimize it for minimizing loading time.<a href="https://www.ultimatelysocial.com/usm-premium/?withqp=1&discount=IMPROVESPEED&utm_source=usmi_global&utm_campaign=improve_website_speed&utm_medium=banner" class="sfsi_font_inherit" target="_blank" style="color:#1a1d20 !important;font-weight: bold;"><span>&#10151;</span> <span style="text-decoration: underline;"></span> <span style="text-decoration: underline;">Get it now at 20% discount</span></a>
                </p>
            </div>
            <div style="text-align:right;">

                <form method="post" class="sfsi_premiumNoticeDismiss" style="padding-bottom:8px;">

                    <input type="hidden" name="sfsi-banner-global-load_faster" value="true">

                    <input type="submit" name="dismiss" value="Dismiss" />

                </form>

            </div>
        </div>
    <?php
        }
        ?>
    <!---------------End Improve your website speed--------------->

    <!--------------- Get more traffic--------------->
    <?php
        if (sfsi_check_banner_criteria($sfsi_banner_global_shares, $gallery_plugins, $optimization_plugins, $sharecount_plugins, $google_analytics, $gdpr_plugins, $sfsi_current_time)) { ?>
        <div class="sfsi_new_prmium_follw sfsi_banner_body">
            <div>
                <p style="font-size:18px !important">
                    <b>Get 20%+ more traffic </b> from more likes & shares with the Ultimatelysocial Premium Plugin. Or get a refund within 20 days. <a href="https://www.ultimatelysocial.com/usm-premium/?withqp=1&discount=MORETRAFFIC2&utm_source=usmi_global&utm_campaign=more_traffic&utm_medium=banner" class="sfsi_font_inherit" target="_blank" style="color:#1a1d20 !important;font-weight: bold;"><span>&#10151;</span> <span style="text-decoration: underline;"></span> <span style="text-decoration: underline;">Get it now at 20% discount</span></a>
                </p>
            </div>
            <div style="text-align:right;">

                <form method="post" class="sfsi_premiumNoticeDismiss" style="padding-bottom:8px;">

                    <input type="hidden" name="sfsi-banner-global-shares" value="true">

                    <input type="submit" name="dismiss" value="Dismiss" />

                </form>

            </div>
        </div>
    <?php
        }
        ?>
    <!---------------End Get more traffic--------------->

    <!--------------- GDPR compliance--------------->
    <?php

        if (sfsi_check_banner_criteria($sfsi_banner_global_gdpr, $gallery_plugins, $optimization_plugins, $sharecount_plugins, $google_analytics, $gdpr_plugins, $sfsi_current_time)) { ?>
        <div class="sfsi_new_prmium_follw sfsi_banner_body">
            <div>
                <p style="margin-bottom: 12px !important;"><b>Make sure your social media icons are GDPR compliant. </b> You are using the Ultimate Social Media Plugin – see more information about GDPR <a href="http://ultimatelysocial.com/gdpr/?utm_source=usmi_global&utm_campaign=gdpr_page&utm_medium=banner" style="color:#1a1d20 !important;text-decoration: underline;">here.</a></p>
                <p style="font-size:18px !important">
                    If you don’t want to check GDPR compliance yourself: As part of the Ultimate Social Media <a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_global&discount=GDPRCOMPLIANCE2&utm_campaign=gdpr&utm_medium=banner" target="_blank" style="color:#1a1d20 !important;text-decoration: underline;"> Premium Plugin</a> a GDPR review is done for you (at no extra charge) <a href="https://www.ultimatelysocial.com/usm-premium/?withqp=1&discount=GDPRCOMPLIANCE2&utm_source=usmi_global&utm_campaign=gdpr&utm_medium=banner" class="sfsi_font_inherit" target="_blank" style="color:#1a1d20 !important;font-weight: bold;"><span>&#10151;</span> <span style="text-decoration: underline;"></span> <span style="text-decoration: underline;">Get it now</span></a>
                </p>
            </div>
            <div style="text-align:right;">

                <form method="post" class="sfsi_premiumNoticeDismiss" style="padding-bottom:8px;">

                    <input type="hidden" name="sfsi-banner-global-gdpr" value="true">

                    <input type="submit" name="dismiss" value="Dismiss" />

                </form>

            </div>
        </div>
    <?php
        }
        ?>
    <!---------------End GDPR compliance--------------->

    <!--------------- Share counts--------------->

    <?php
        if (sfsi_check_banner_criteria($sfsi_banner_global_http, $gallery_plugins, $optimization_plugins, $sharecount_plugins, $google_analytics, $gdpr_plugins, $sfsi_current_time)) {  ?>
        <div class="sfsi_new_prmium_follw sfsi_banner_body">
            <div>
                <p style="margin-bottom: 12px !important;"><b>Important: </b> Your website used to be on http (before you enabled an SSL certificate to switch to https). We found share counts for your URLs on http which usually get lost after switch to https (because Facebook etc. provide the counts per url, and an url on https is a different url then one on http).<b> We found a solution for that </b> so that your share counts on http and https will be aggregated and your full number of share counts is restored. It is implemented in the Premium Plugin – <a href="https://www.ultimatelysocial.com/usm-premium/?withqp=1&discount=SHARECOUNTS&utm_source=usmi_global&utm_campaign=share_counts_banner&utm_medium=banner" class="sfsi_font_inherit" target="_blank" style="color:#1a1d20 !important;font-weight: bold;"><span></span> <span style="text-decoration: underline;"></span> <span style="text-decoration: underline;">Get it now at 20% discount</span></a></p>
            </div>
            <div style="text-align:right;">

                <form method="post" class="sfsi_premiumNoticeDismiss" style="padding-bottom:8px;">

                    <input type="hidden" name="sfsi-banner-global-http" value="true">

                    <input type="submit" name="dismiss" value="Dismiss" />

                </form>

            </div>
        </div>
    <?php
        }
        ?>
    <!---------------End Share counts--------------->

    <!--------------- Loyalty discount--------------->
    <?php
        if (sfsi_check_banner_criteria($sfsi_banner_global_upgrade, $gallery_plugins, $optimization_plugins, $sharecount_plugins, $google_analytics, $gdpr_plugins, $sfsi_current_time) &&  $sfsi_current_time >= $sfsi_min_loyalty_time) {

            ?>
        <div class="sfsi_new_prmium_follw sfsi_banner_body">
            <div>
                <p style="margin-bottom: 12px !important;">You’ve been using the Ultimate Social media plugin for <b>over half a year</b>. That’s a long time!</p>
                <p style="font-size:18px !important">
                    Why not give yourself a treat and upgrade to premium? It has <a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_global&utm_campaign=loyalty_banner&utm_medium=banner" class="sfsi_font_inherit" target="_blank" style="color:#1a1d20 !important;text-decoration: underline;"><span></span> tons of benefits</a>. As a THANK YOU for your loyalty we’re happy to give you a <b>20% discount</b>.
                    <a href="https://www.ultimatelysocial.com/usm-premium/?withqp=1&discount=LOYALTYDISCOUNT&utm_source=usmi_global&utm_campaign=loyalty_banner&utm_medium=banner" class="sfsi_font_inherit" target="_blank" style="color:#1a1d20 !important;text-decoration: underline;"><span></span> Apply it now</a>
                </p>
            </div>
            <!-- https://www.ultimatelysocial.com/usm-premium/?withqp=1&discount=20&utm_source=usmi_global&utm_campaign=loyalty_banner&utm_medium=banner -->
            <div style="text-align:right;">

                <form method="post" class="sfsi_premiumNoticeDismiss" style="padding-bottom:8px;">

                    <input type="hidden" name="sfsi-banner-global-upgrade" value="true">

                    <input type="submit" name="dismiss" value="Dismiss" />

                </form>

            </div>
        </div>
<?php
    }
}
?>
<!---------------End Loyalty discount--------------->

<!------------------------------------------------------End Global Banners ----------------------------------------------------------->