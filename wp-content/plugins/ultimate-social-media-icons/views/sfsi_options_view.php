<!-- Loader Image section  -->
<div id="sfpageLoad">

</div>
<!-- END Loader Image section  -->

<!-- javascript error loader  -->
<div class="error" id="sfsi_onload_errors" style="margin-left: 60px;display: none;">
    <p>We found errors in your javascript which may cause the plugin to not work properly. Please fix the error:</p>
    <p id="sfsi_jerrors"></p>
</div> <!-- END javascript error loader  -->

<!-- START Admin view for plugin-->
<div class="wapper sfsi_mainContainer">

    <!-- Get notification bar-->
    <?php if (get_option("show_new_notification") == "yes") { ?>
        <script type="text/javascript">
            jQuery(document).ready(function(e) {
                jQuery(".sfsi_show_notification").click(function() {
                    SFSI.ajax({
                        url: sfsi_icon_ajax_object.ajax_url,
                        type: "post",
                        data: {
                            action: "notification_read",
                            nonce: "<?php echo wp_create_nonce('notification_read'); ?>"
                        },
                        success: function(msg) {
                            if (jQuery.trim(msg) == 'success') {
                                jQuery(".sfsi_show_notification").hide("fast");
                            }
                        }
                    });
                });
            });
        </script>
        <style type="text/css">
            .sfsi_show_notification {
                float: left;
                margin-bottom: 45px;
                padding: 12px 13px;
                width: 98%;
                background-image: url(<?php echo SFSI_PLUGURL; ?>images/notification-close.png);
                background-position: right 20px center;
                background-repeat: no-repeat;
                cursor: pointer;
                text-align: center;
            }
        </style>
        <!-- <div class="sfsi_show_notification" style="background-color: #38B54A; color: #fff; font-size: 18px;">
        New: You can now also show a subscription form on your site, increasing sign-ups! (Question 8)
        <p>
        (If question 8 gets displayed in a funny way then please reload the page by pressing Control+F5(PC) or Command+R(Mac))
        </p>
    </div> -->
    <?php } ?>
    <!-- Get notification bar-->
    <div class="sfsi_notificationBannner"></div>

    <!-- Get new_notification bar-->
    <script type="text/javascript">
        jQuery(document).ready(function() {

            jQuery("#floating").click(function() {
                jQuery("#ui-id-9").trigger("click");
                jQuery('html, body').animate({
                    scrollTop: jQuery("#ui-id-9").offset().top - jQuery("#ui-id-9").height()
                }, 2000);
            });

            jQuery("#afterposts").click(function() {
                if ("none" == jQuery("#ui-id-12").css('display')) {
                    jQuery("#ui-id-11").trigger("click");
                }
                jQuery('html, body').animate({
                    scrollTop: jQuery("#ui-id-11").offset().top - jQuery("#ui-id-11").height()
                }, 2000);
            });

        });
    </script>

    <!-- Top content area of plugin -->
    <div class="main_contant">
        <div class="row">
            <div class="col-7 col-md-9 col-lg-12 ">
                <h1>Welcome to the Ultimate Social Icons and Share Plugin!</h1>

                <div class="">
                    <div class="row">
                        <div class="col-12 col-lg-8 col-xxl-10">
                            <p class='sfsi-top-header-subheading font-italic'>Simply answer the questions below <span class='sfsi-top-banner-no-decoration'>(at least the first 3)</span> - that`s it!</p>
                            <p class="">If you face any issue, please ask in <a target="_blank" href="http://bit.ly/USM_SUPPORT_FORUM" class="sfsi-top-banner-no-decoration text-success">Support Forum</a>. We'll try to respond quickly. Thank you!</p>
                            <div class="d-none d-lg-flex row">
                                <div class="col-9 col-xxl-10">
                                    <p class="sfsi-top-banner-higligted-text">If you want <span class='font-weight-bold font-italic'>more likes & shares</span>, more placement options, better sharing features (eg: define the text and image that gets shared), optimization for mobile, <a target="_blank" href="https://www.ultimatelysocial.com/extra-icon-styles/?utm_source=usmi_settings_page&utm_campaign=top_banner&utm_medium=link" class="font-italic text-success" style="font-family: helvetica-light;">more icon design styles,</a> <a target="_blank" href="https://www.ultimatelysocial.com/animated-social-media-icons/?utm_source=usmi_settings_page&utm_campaign=top_banner&utm_medium=link" class=" text-success font-italic" style="font-family:helvetica-light">animated icons,</a> <a target="_blank" href="https://www.ultimatelysocial.com/themed-icons-search/?utm_source=usmi_settings_page&utm_campaign=top_banner&utm_medium=link" class=' text-success font-italic' style="font-family: helvetica-light;">themed icons,</a> and <a href="https://www.ultimatelysocial.com/themed-icons-search/?utm_source=usmi_settings_page&utm_campaign=top_banner&utm_medium=link" target="_blank" class=" text-success font-italic" style="font-family: helvetica-light;">much more</a>, then <a href="https://www.ultimatelysocial.com/usm-premium/?withqp=1&utm_source=usmi_settings_page&utm_campaign=top_banner&utm_medium=link" style="cursor:pointer; color: #12a252 !important;border-bottom: 1px solid #12a252;text-decoration: none;font-weight: bold;" target="_blank">
                                        go premium</a>.</p>
                                </div>
                                <div class="col-3 text-center px-0 col-xxl-2">
                                    <div class='d-table' style='width:100%;height:100%'>
                                        <div class='d-table-row'>
                                            <div class='d-table-cell align-middle'>
                                                <div class='sfsi-premium-btn'>
                                                    <a target="_blank" href="https://www.ultimatelysocial.com/usm-premium/?withqp=1&utm_source=usmi_settings_page&utm_campaign=top_banner&utm_medium=link" class="btn btn-success" style="font-family:helveticabold;font-size: 17px;text-decoration: none;">Go Premium</a>
                                                </div>
                                                <a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=top_banner&utm_medium=link" style="text-decoration: none;color:#414951;font-family: helveticaneue-light;" target='_blank'>Learn More</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-none d-lg-flex col-4 col-lg-4 col-xxl-2">
                            <div class='d-table' style='width:100%;height:100%'>
                                <div class='d-table-row'>
                                    <div class='d-table-cell align-bottom'>
                                        <a href="https://www.ultimatelysocial.com/usm-premium/?playvideo=1&utm_source=usmi_settings_page&utm_campaign=top_banner&utm_medium=link" target="_blank"><img target="_blank" src="<?php echo SFSI_PLUGURL; ?>images/sfsi-video-play.png" style='width:100%'></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-5 col-md-3 d-lg-none">
                <div style="position:relative;padding-top:56.25%;">
                    <iframe src="https://video.inchev.com/videos/embed/c952d896-34be-45bc-8142-ba14694c1bd0" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen="" style="position:absolute;top:0;left:0;width:100%;height:100%;"></iframe>
                </div>
                <div class="text-center mt-5">
                    <div class='sfsi-premium-btn'>
                        <button class="btn btn-success ">Go Premium</button>
                    </div>
                    <span>Learn more</span>
                </div>
            </div>
        </div>
        <div class="d-lg-none row">
            <div class="col">
                <p class="sfsi-top-banner-higligted-text">If you want <span class='font-weight-bold font-italic'>more likes & shares</span>, more placement options, better sharing features (eg: define the text and image that gets shared), optimization for mobile, <a target="_blank" href="https://www.ultimatelysocial.com/extra-icon-styles/?utm_source=usmi_settings_page&utm_campaign=top_banner&utm_medium=link" class="font-italic text-success">more icon design styles,</a> <a target="_blank" href="https://www.ultimatelysocial.com/animated-social-media-icons/?utm_source=usmi_settings_page&utm_campaign=top_banner&utm_medium=link" class=" text-success font-italic">animated icons,</a> <a target="_blank" href="https://www.ultimatelysocial.com/themed-icons-search/" class=' text-success font-italic'>themed icons,</a> and <a href="https://www.ultimatelysocial.com/themed-icons-search/?utm_source=usmi_settings_page&utm_campaign=top_banner&utm_medium=link" target="_blank" class=" text-success font-italic">much more</a>, then ...</p>
            </div>
        </div>
    </div><!-- END Top content area of plugin -->

    <!-- step 1 end  here -->
    <div id="accordion">

        <h3><span>1</span>Which icons do you want to show on your site? </h3>
        <!-- step 1 end  here -->
        <?php include(SFSI_DOCROOT . '/views/sfsi_option_view1.php'); ?>
        <!-- step 1 end here -->

        <!-- step 2 start here -->
        <h3><span>2</span>What do you want the icons to do? </h3>
        <?php include(SFSI_DOCROOT . '/views/sfsi_option_view2.php'); ?>
        <!-- step 2 END here -->

        <!-- step 3 start here -->
        <h3><span>3</span>Where shall they be displayed? </h3>
        <?php include(SFSI_DOCROOT . '/views/sfsi_question3.php'); ?>
        <!-- step 3 end here -->


    </div>

    <h2 class="optional">Optional</h2>

    <div id="accordion1">

        <!-- step 4 start here -->
        <h3><span>4</span>What design &amp; animation do you want to give your icons?</h3>
        <?php include(SFSI_DOCROOT . '/views/sfsi_option_view3.php'); ?>
        <!-- step 4 END here -->

        <!-- step 5 Start here -->
        <h3><span>5</span>Do you want to display "counts" next to your icons?</h3>
        <?php include(SFSI_DOCROOT . '/views/sfsi_option_view4.php'); ?>
        <!-- step 5 END here -->

        <!-- step 6 Start here -->
        <h3><span>6</span>Any other wishes for your main icons?</h3>
        <?php include(SFSI_DOCROOT . '/views/sfsi_option_view5.php'); ?>
        <!-- step 6 END here -->

        <!-- step 7 Start here -->
        <h3><span>7</span>Do you want to display a pop-up, asking people to subscribe?</h3>
        <?php include(SFSI_DOCROOT . '/views/sfsi_option_view7.php'); ?>
        <!-- step 7 END here -->

        <!-- step 8 Start here -->
        <h3><span>8</span>Do you want to show a subscription form (<b>increases sign ups</b>)?</h3>
        <?php include(SFSI_DOCROOT . '/views/sfsi_option_view8.php'); ?>
        <!-- step 8 END here -->

    </div>

    <div class="tab10">
        <div class="save_export">
            <div class="save_button">

                <img src="<?php echo SFSI_PLUGURL; ?>images/ajax-loader.gif" class="loader-img" alt="error" />

                <a href="javascript:;" id="save_all_settings" title="Save All Settings">Save All Settings</a>

            </div>
            <?php $nonce = wp_create_nonce("sfsi_save_export"); ?>

            <div class="export_selections">
                <div class="export" id="sfsi_save_export" data-nonce="<?php echo $nonce; ?>">
                    Export
                </div>

                <div>selections</div>

            </div>
        </div>
        <p class="red_txt errorMsg" style="display:none;font-size:21px"> </p>
        <p class="green_txt sucMsg" style="display:none;font-size:21px"> </p>

        <?php // include(SFSI_DOCROOT . '/views/sfsi_affiliate_banner.php'); 
        ?><?php include(SFSI_DOCROOT . '/views/sfsi_section_for_premium.php'); ?>

        <!--<p class="bldtxtmsg">Need top-notch Wordpress development work at a competitive price? Visit us at <a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=footer_credit&utm_medium=banner">ultimatelysocial.com</a></p>-->
    </div>
    <!-- all pops of plugin under sfsi_pop_content.php file -->
    <?php include(SFSI_DOCROOT . '/views/sfsi_pop_content.php'); ?>

</div> <!-- START Admin view for plugin-->
<?php if (in_array(get_site_url(), array('http://www.managingio.com', 'http://blog-latest.socialshare.com'))) : ?>
    <div style="text-align:center">
        <input type="text" name="domain" id="sfsi_domain_input" style="width:40%;min-height: :40px;text-align:center;margin:0 auto" placeholder="Enter Domian to check its theme" />
        <input type="text" name="sfsi_domain_input_nonce" value="<?php echo wp_create_nonce('bannerOption'); ?>">
        <div class="save_button">
            <img src="<?php echo SFSI_PLUGURL; ?>images/ajax-loader.gif" class="loader-img" alt="error" />
            <a href="javascript:;" id="sfsi_check_theme_of_domain_btn" title="Check">Check the Theme</a>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#sfsi_check_theme_of_domain_btn').click(function() {
                    jQuery.ajax({
                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                        type: "post",
                        data: {
                            'action': 'bannerOption',
                            'domain': $('#sfsi_domain_input').val(),
                            'nonce': $('#sfsi_domain_input_nonce').val(),
                        },
                        success: function(s) {
                            var sfsi_container = $("html,body");
                            var sfsi_scrollTo = $('.sfsi_notificationBannner');
                            $('.sfsi_notificationBannner').attr('tabindex', $('.sfsi_notificationBannner').attr('tabindex') || -1);
                            jQuery(".sfsi_notificationBannner").html(s).focus();
                            sfsi_container.animate({
                                scrollTop: (sfsi_scrollTo.offset().top - sfsi_container.offset().top + sfsi_container.scrollTop()),
                                scrollLeft: 0
                            }, 300);

                        }
                    });
                });
            })
        </script>
    <?php endif; ?>
    <script type="text/javascript">
        var e = {
            action: "bannerOption",
            'nonce': '<?php echo wp_create_nonce('bannerOption'); ?>',

        };
        jQuery.ajax({
            url: "<?php echo admin_url('admin-ajax.php'); ?>",
            type: "post",
            data: e,
            success: function(s) {
                jQuery(".sfsi_notificationBannner").html(s);
            }
        });
    </script>
    <?php include(SFSI_DOCROOT . '/views/sfsi_chat_on_admin_pannel.php'); ?>