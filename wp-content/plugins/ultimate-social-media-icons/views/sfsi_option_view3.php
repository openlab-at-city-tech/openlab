<?php

/* unserialize all saved option for second section options */

$option3 =  unserialize(get_option('sfsi_section3_options', false));

$option1 =  unserialize(get_option('sfsi_section1_options', false));

/*

 * Sanitize, escape and validate values

 */

$option3['sfsi_actvite_theme']             = (isset($option3['sfsi_actvite_theme'])) ? sanitize_text_field($option3['sfsi_actvite_theme']) : '';

$option3['sfsi_mouseOver']                 = (isset($option3['sfsi_mouseOver'])) ? sanitize_text_field($option3['sfsi_mouseOver']) : '';

$option3['sfsi_mouseOver_effect']         = (isset($option3['sfsi_mouseOver_effect'])) ? sanitize_text_field($option3['sfsi_mouseOver_effect']) : '';

$option3['sfsi_shuffle_icons']             = (isset($option3['sfsi_shuffle_icons'])) ? sanitize_text_field($option3['sfsi_shuffle_icons']) : '';

$option3['sfsi_shuffle_Firstload']         = (isset($option3['sfsi_shuffle_Firstload'])) ? sanitize_text_field($option3['sfsi_shuffle_Firstload']) : '';

$option3['sfsi_shuffle_interval']         = (isset($option3['sfsi_shuffle_interval'])) ? sanitize_text_field($option3['sfsi_shuffle_interval']) : '';

$option3['sfsi_shuffle_intervalTime']     = (isset($option3['sfsi_shuffle_intervalTime'])) ? intval($option3['sfsi_shuffle_intervalTime']) : '';

$option3['sfsi_mouseOver_effect_type'] = (isset($option3['sfsi_mouseOver_effect_type'])) ? sanitize_text_field($option3['sfsi_mouseOver_effect_type']) : 'same_icons';

$mouseover_other_icons_transition_effect = (isset($option3['mouseover_other_icons_transition_effect'])) ? sanitize_text_field($option3['mouseover_other_icons_transition_effect']) : 'flip';

?>

<!-- Section 3 "What design & animation do you want to give your icons?" main div Start -->

<div class="tab3">

    <p>

        A good & well-fitting design is not only nice to look at, but it increases chances that people will subscribe and/or share your site with friends:

    </p>

    <ul class="tab_3_list">

        <li>It comes across as <span>more professional</span> and gives your site <span>more “credit”</span></li>

        <li>A smart automatic animation can <span>make your visitors aware of your icons</span> in an unintrusive manner</li>

    </ul>

    <p style="padding:0px;">

        The icons have been compressed by <a href="https://goo.gl/IV5Q3z" target="_blank">Shortpixel.com</a> for faster loading of your site. Thank you Shortpixel!

    </p>

    <div class="row">

        <!--<h3>Theme options</h3>-->
        <h4>Theme options</h4>
        <!--icon themes section start -->

        <ul class="tab_3_icns">

            <li><input name="sfsi_actvite_theme" <?php echo ($option3['sfsi_actvite_theme'] == 'default') ?  'checked="true"' : ''; ?> type="radio" value="default" class="styled" /><label>Default</label>
                <div class="icns_tab_3"><span class="row_1_1 rss_section"></span><span class="row_1_2 email_section"></span><span class="row_1_3 facebook_section"></span><span class="row_1_5 twitter_section"></span><span class="row_1_7 youtube_section"></span><span class="row_1_8 pinterest_section"></span><span class="row_1_9 linkedin_section"></span> <span class="row_1_10 instagram_section"></span><span class="row_1_14 telegram_section"></span><span class="row_1_15 vk_section"></span><span class="row_1_16 ok_section"></span><span class="row_1_17 weibo_section"></span><span class="row_1_18 wechat_section"></span>
                    <!--<span class="row_1_11 sf_section"></span>-->
                </div>
            </li>

            <li class="sfsi_notificationBannner" style="display:none"></li>

            
            <li class="sfsi_webtheme" style='display:none'>
                <span class="radio" style="opacity: 0.5;"></span>    
                <label class="sfsi_premium_ad_lable" style="text-transform: capitalize;">Default</label>
                <div class="icns_tab_3 sfsi_premium_ad"><span class="premium_col_1 rss_section"></span><span class="premium_col_2 email_section"></span><span class="premium_col_3 facebook_section"></span><span class="premium_col_4 twitter_section"></span><div class="sfis_premium_ad_name">(Premium)</div>
                    <!--<span class="row_1_11 sf_section"></span>-->
                </div>
            </li>

            <li><input name="sfsi_actvite_theme" <?php echo ($option3['sfsi_actvite_theme'] == 'flat') ?  'checked="true"' : ''; ?> type="radio" value="flat" class="styled" /><label>Flat</label>
                <div class="icns_tab_3"><span class="row_2_1 rss_section"></span><span class="row_2_2 email_section"></span><span class="row_2_3 facebook_section"></span><span class="row_2_5 twitter_section"></span><span class="row_2_7 youtube_section"></span><span class="row_2_8 pinterest_section"></span><span class="row_2_9 linkedin_section"></span><span class="row_2_10 instagram_section"></span><span class="row_2_14 telegram_section"></span><span class="row_2_15 vk_section"></span><span class="row_2_16 ok_section"></span><span class="row_2_17 weibo_section"></span><span class="row_2_18 wechat_section"></span>
                    <!--<span class="row_2_11 sf_section"></span>-->
                </div>
            </li>

            <li><input name="sfsi_actvite_theme" <?php echo ($option3['sfsi_actvite_theme'] == 'thin') ?  'checked="true"' : ''; ?> type="radio" value="thin" class="styled" /><label>Thin</label>
                <div class="icns_tab_3"><span class="row_3_1 rss_section"></span><span class="row_3_2 email_section"></span><span class="row_3_3 facebook_section"></span><span class="row_3_5 twitter_section"></span><span class="row_3_7 youtube_section"></span><span class="row_3_8 pinterest_section"></span><span class="row_3_9 linkedin_section"></span><span class="row_3_10 instagram_section"></span><span class="row_3_14 telegram_section"></span><span class="row_3_15 vk_section"></span><span class="row_3_16 ok_section"></span><span class="row_3_17 weibo_section"></span><span class="row_3_18 wechat_section"></span>
                    <!--<span class="row_3_11 sf_section"></span>-->
                </div>
            </li>

            <li><input name="sfsi_actvite_theme" <?php echo ($option3['sfsi_actvite_theme'] == 'cute') ?  'checked="true"' : ''; ?> type="radio" value="cute" class="styled" /><label>Cute</label>
                <div class="icns_tab_3"><span class="row_4_1 rss_section"></span><span class="row_4_2 email_section"></span><span class="row_4_3 facebook_section"></span><span class="row_4_5  twitter_section"></span><span class="row_4_7 youtube_section"></span><span class="row_4_8 pinterest_section"></span><span class="row_4_9 linkedin_section"></span><span class="row_4_10 instagram_section"></span><span class="row_4_14 telegram_section"></span><span class="row_4_15 vk_section"></span><span class="row_4_16 ok_section"></span><span class="row_4_17 weibo_section"></span><span class="row_4_18 wechat_section"></span>
                    <!--<span class="row_4_11 sf_section"></span>-->
                </div>
            </li>

            <!--start next four-->

            <li><input name="sfsi_actvite_theme" <?php echo ($option3['sfsi_actvite_theme'] == 'cubes') ?  'checked="true"' : ''; ?> type="radio" value="cubes" class="styled" /><label>Cubes</label>
                <div class="icns_tab_3"><span class="row_5_1 rss_section"></span><span class="row_5_2 email_section"></span><span class="row_5_3 facebook_section"></span><span class="row_5_5 twitter_section"></span><span class="row_5_7 youtube_section"></span><span class="row_5_8 pinterest_section"></span><span class="row_5_9 linkedin_section"></span><span class="row_5_10 instagram_section"></span><span class="row_5_14 telegram_section"></span><span class="row_5_15 vk_section"></span><span class="row_5_16 ok_section"></span><span class="row_5_17 weibo_section"></span><span class="row_5_18 wechat_section"></span>
                    <!--<span class="row_5_11 sf_section"></span>-->
                </div>
            </li>

            <li><input name="sfsi_actvite_theme" <?php echo ($option3['sfsi_actvite_theme'] == 'chrome_blue') ?  'checked="true"' : ''; ?> type="radio" value="chrome_blue" class="styled" /><label>Chrome Blue</label>
                <div class="icns_tab_3"><span class="row_6_1 rss_section"></span><span class="row_6_2 email_section"></span><span class="row_6_3 facebook_section"></span><span class="row_6_5 twitter_section"></span><span class="row_6_7 youtube_section"></span><span class="row_6_8 pinterest_section"></span><span class="row_6_9 linkedin_section"></span><span class="row_6_10 instagram_section"></span><span class="row_6_14 telegram_section"></span><span class="row_6_15 vk_section"></span><span class="row_6_16 ok_section"></span><span class="row_6_17 weibo_section"></span><span class="row_6_18 wechat_section"></span>
                    <!--<span class="row_6_11 sf_section"></span>-->
                </div>
            </li>

            <li><input name="sfsi_actvite_theme" <?php echo ($option3['sfsi_actvite_theme'] == 'chrome_grey') ?  'checked="true"' : ''; ?> type="radio" value="chrome_grey" class="styled" /><label>Chrome Grey</label>
                <div class="icns_tab_3"><span class="row_7_1 rss_section"></span><span class="row_7_2 email_section"></span><span class="row_7_3 facebook_section"></span><span class="row_7_5 twitter_section"></span><span class="row_7_7 youtube_section"></span><span class="row_7_8 pinterest_section"></span><span class="row_7_9 linkedin_section"></span><span class="row_7_10 instagram_section"></span><span class="row_7_14 telegram_section"></span><span class="row_7_15 vk_section"></span><span class="row_7_16 ok_section"></span><span class="row_7_17 weibo_section"></span><span class="row_7_18 wechat_section"></span>
                    <!--<span class="row_7_11 sf_section"></span>-->
                </div>
            </li>

            <li><input name="sfsi_actvite_theme" <?php echo ($option3['sfsi_actvite_theme'] == 'splash') ?  'checked="true"' : ''; ?> type="radio" value="splash" class="styled" /><label>Splash</label>
                <div class="icns_tab_3"><span class="row_8_1 rss_section"></span><span class="row_8_2 email_section"></span><span class="row_8_3 facebook_section"></span><span class="row_8_5  twitter_section"></span><span class="row_8_7 youtube_section"></span><span class="row_8_8 pinterest_section"></span><span class="row_8_9 linkedin_section"></span><span class="row_8_10 instagram_section"></span><span class="row_8_14 telegram_section"></span><span class="row_8_15 vk_section"></span><span class="row_8_16 ok_section"></span><span class="row_8_17 weibo_section"></span><span class="row_8_18 wechat_section"></span>
                    <!--<span class="row_8_11 sf_section"></span>-->
                </div>
            </li>

            <!--start second four-->

            <li><input name="sfsi_actvite_theme" <?php echo ($option3['sfsi_actvite_theme'] == 'orange') ?  'checked="true"' : ''; ?> type="radio" value="orange" class="styled" /><label>Orange</label>
                <div class="icns_tab_3"><span class="row_9_1 rss_section"></span><span class="row_9_2 email_section"></span><span class="row_9_3 facebook_section"></span><span class="row_9_5 twitter_section"></span><span class="row_9_7 youtube_section"></span><span class="row_9_8 pinterest_section"></span><span class="row_9_9 linkedin_section"></span><span class="row_9_10 instagram_section"></span><span class="row_9_14 telegram_section"></span><span class="row_9_15 vk_section"></span><span class="row_9_16 ok_section"></span><span class="row_9_17 weibo_section"></span><span class="row_9_18 wechat_section"></span>
                    <!--<span class="row_9_11 sf_section"></span>-->
                </div>
            </li>

            <li><input name="sfsi_actvite_theme" <?php echo ($option3['sfsi_actvite_theme'] == 'crystal') ?  'checked="true"' : ''; ?> type="radio" value="crystal" class="styled" /><label>Crystal</label>
                <div class="icns_tab_3"><span class="row_10_1 rss_section"></span><span class="row_10_2 email_section"></span><span class="row_10_3 facebook_section"></span><span class="row_10_5 twitter_section"></span><span class="row_10_7 youtube_section"></span><span class="row_10_8 pinterest_section"></span><span class="row_10_9 linkedin_section"></span><span class="row_10_10 instagram_section"></span><span class="row_10_14 telegram_section"></span><span class="row_10_15 vk_section"></span><span class="row_10_16 ok_section"></span><span class="row_10_17 weibo_section"></span><span class="row_10_18 wechat_section"></span>
                    <!--<span class="row_10_11 sf_section"></span>-->
                </div>
            </li>

            <li><input name="sfsi_actvite_theme" <?php echo ($option3['sfsi_actvite_theme'] == 'glossy') ?  'checked="true"' : ''; ?> type="radio" value="glossy" class="styled" /><label>Glossy</label>
                <div class="icns_tab_3"><span class="row_11_1 rss_section"></span><span class="row_11_2 email_section"></span><span class="row_11_3 facebook_section"></span><span class="row_11_5 twitter_section"></span><span class="row_11_7 youtube_section"></span><span class="row_11_8 pinterest_section"></span><span class="row_11_9 linkedin_section"></span><span class="row_11_10 instagram_section"></span><span class="row_11_14 telegram_section"></span><span class="row_11_15 vk_section"></span><span class="row_11_16 ok_section"></span><span class="row_11_17 weibo_section"></span><span class="row_11_18 wechat_section"></span>
                    <!--<span class="row_11_11 sf_section"></span>-->
                </div>
            </li>

            <li><input name="sfsi_actvite_theme" <?php echo ($option3['sfsi_actvite_theme'] == 'black') ?  'checked="true"' : ''; ?> type="radio" value="black" class="styled" /><label>Black</label>
                <div class="icns_tab_3"><span class="row_12_1 rss_section"></span><span class="row_12_2 email_section"></span><span class="row_12_3 facebook_section"></span><span class="row_12_5  twitter_section"></span><span class="row_12_7 youtube_section"></span><span class="row_12_8 pinterest_section"></span><span class="row_12_9 linkedin_section"></span><span class="row_12_10 instagram_section"></span><span class="row_12_14 telegram_section"></span><span class="row_12_15 vk_section"></span><span class="row_12_16 ok_section"></span><span class="row_12_17 weibo_section"></span><span class="row_12_18 wechat_section"></span>
                    <!--<span class="row_12_11 sf_section"></span>-->
                </div>
            </li>

            <!--start last four-->

            <li><input name="sfsi_actvite_theme" <?php echo ($option3['sfsi_actvite_theme'] == 'silver') ?  'checked="true"' : ''; ?> type="radio" value="silver" class="styled" /><label>Silver</label>
                <div class="icns_tab_3"><span class="row_13_1 rss_section"></span><span class="row_13_2 email_section"></span><span class="row_13_3 facebook_section"></span><span class="row_13_5 twitter_section"></span><span class="row_13_7 youtube_section"></span><span class="row_13_8 pinterest_section"></span><span class="row_13_9 linkedin_section"></span><span class="row_13_10 instagram_section"></span><span class="row_13_14 telegram_section"></span><span class="row_13_15 vk_section"></span><span class="row_13_16 ok_section"></span><span class="row_13_17 weibo_section"></span><span class="row_13_18 wechat_section"></span>
                    <!--<span class="row_13_11 sf_section"></span>-->
                </div>
            </li>

            <li><input name="sfsi_actvite_theme" <?php echo ($option3['sfsi_actvite_theme'] == 'shaded_dark') ?  'checked="true"' : ''; ?> type="radio" value="shaded_dark" class="styled" /><label>Shaded Dark</label>
                <div class="icns_tab_3"><span class="row_14_1 rss_section"></span><span class="row_14_2 email_section"></span><span class="row_14_3 facebook_section"></span><span class="row_14_5 twitter_section"></span><span class="row_14_7 youtube_section"></span><span class="row_14_8 pinterest_section"></span><span class="row_14_9 linkedin_section"></span><span class="row_14_10 instagram_section"></span><span class="row_14_14 telegram_section"></span><span class="row_14_15 vk_section"></span><span class="row_14_16 ok_section"></span><span class="row_14_17 weibo_section"></span><span class="row_14_18 wechat_section"></span>
                    <!--<span class="row_14_11 sf_section"></span>-->
                </div>
            </li>

            <li><input name="sfsi_actvite_theme" <?php echo ($option3['sfsi_actvite_theme'] == 'shaded_light') ?  'checked="true"' : ''; ?> type="radio" value="shaded_light" class="styled" /><label>Shaded Light</label>
                <div class="icns_tab_3"><span class="row_15_1 rss_section"></span><span class="row_15_2 email_section"></span><span class="row_15_3 facebook_section"></span><span class="row_15_5 twitter_section"></span><span class="row_15_7 youtube_section"></span><span class="row_15_8 pinterest_section"></span><span class="row_15_9 linkedin_section"></span><span class="row_15_10 instagram_section"></span><span class="row_15_14 telegram_section"></span><span class="row_15_15 vk_section"></span><span class="row_15_16 ok_section"></span><span class="row_15_17 weibo_section"></span><span class="row_15_18 wechat_section"></span>
                    <!--<span class="row_15_11 sf_section"></span>-->
                </div>
            </li>

            <li><input name="sfsi_actvite_theme" <?php echo ($option3['sfsi_actvite_theme'] == 'transparent') ?  'checked="true"' : ''; ?> type="radio" value="transparent" class="styled" /><label style="line-height:20px !important;margin-top:15px;  ">Transparent <br /><span style="font-size: 9px;">(for dark backgrounds)</span></label>
                <div class="icns_tab_3 trans_bg" style="padding-left: 6px;"><span class="row_16_1 rss_section"></span><span class="row_16_2 email_section"></span><span class="row_16_3 facebook_section"></span><span class="row_16_5  twitter_section"></span><span class="row_16_7 youtube_section"></span><span class="row_16_8 pinterest_section"></span><span class="row_16_9 linkedin_section"></span><span class="row_16_10 instagram_section"></span><span class="row_16_14 telegram_section"></span><span class="row_16_15 vk_section"></span><span class="row_16_16 ok_section"></span><span class="row_16_17 weibo_section"></span><span class="row_16_18 wechat_section"></span>
                    <!--<span class="row_16_11 sf_section"></span>-->
                </div>
            </li>

            <!--Custom Icon Support {Monad}-->
            <?php if (get_option('sfsi_custom_icons') == 'yes') { ?>

                <li class="cstomskins_upload">

                    <input name="sfsi_actvite_theme" <?php echo ($option3['sfsi_actvite_theme'] == 'custom_support') ?  'checked="true"' : ''; ?> type="radio" value="custom_support" class="styled" />

                    <label style="line-height:20px !important;margin-top:15px;  ">Custom Icons <br /></label>

                    <div class="icns_tab_3" style="padding-left: 6px;">

                        <?php

                            if (get_option("rss_skin")) {

                                $icon = get_option("rss_skin");

                                echo '<span class="row_17_1 rss_section sfsi-bgimage" style="background: url(' . $icon . ') no-repeat;"></span>';
                            } else {

                                echo '<span class="row_17_1 rss_section" style="background-position:-1px 0;"></span>';
                            }

                            if (get_option("email_skin")) {

                                $icon = get_option("email_skin");

                                echo '<span class="row_17_2 email_section sfsi-bgimage" style="background: url(' . $icon . ') no-repeat;"></span>';
                            } else {

                                echo '<span class="row_17_2 email_section" style="background-position:-58px 0;"></span>';
                            }

                            if (get_option("facebook_skin")) {

                                $icon = get_option("facebook_skin");

                                echo '<span class="row_17_3 facebook_section sfsi-bgimage" style="background: url(' . $icon . ') no-repeat;"></span>';
                            } else {

                                echo '<span class="row_17_3 facebook_section" style="background-position:-118px 0;"></span>';
                            }
                            if (get_option("twitter_skin")) {

                                $icon = get_option("twitter_skin");

                                echo '<span class="row_17_5 twitter_section sfsi-bgimage" style="background: url(' . $icon . ') no-repeat;"></span>';
                            } else {

                                echo '<span class="row_17_5 twitter_section" style="background-position:-235px 0;"></span>';
                            }

                            if (get_option("youtube_skin")) {

                                $icon = get_option("youtube_skin");

                                echo '<span class="row_17_7 youtube_section sfsi-bgimage" style="background: url(' . $icon . ') no-repeat;"></span>';
                            } else {

                                echo '<span class="row_17_7 youtube_section" style="background-position:-350px 0;"></span>';
                            }

                            if (get_option("pintrest_skin")) {

                                $icon = get_option("pintrest_skin");

                                echo '<span class="row_17_8 pinterest_section sfsi-bgimage" style="background: url(' . $icon . ') no-repeat;"></span>';
                            } else {

                                echo '<span class="row_17_8 pinterest_section" style="background-position:-409px 0;"></span>';
                            }

                            if (get_option("linkedin_skin")) {

                                $icon = get_option("linkedin_skin");

                                echo '<span class="row_17_9 linkedin_section sfsi-bgimage" style="background: url(' . $icon . ') no-repeat;"></span>';
                            } else {

                                echo '<span class="row_17_9 linkedin_section" style="background-position:-467px 0;"></span>';
                            }

                            if (get_option("instagram_skin")) {

                                $icon = get_option("instagram_skin");

                                echo '<span class="row_17_10 instagram_section sfsi-bgimage" style="background: url(' . $icon . ') no-repeat;"></span>';
                            } else {

                                echo '<span class="row_17_10 instagram_section" style="background-position:-526px 0;"></span>';
                            }

                            if (get_option("telegram_skin")) {

                                $icon = get_option("telegram_skin");

                                echo '<span class="row_17_10 telegram_section sfsi-bgimage" style="background: url(' . $icon . ') no-repeat;"></span>';
                            } else {

                                echo '<span class="row_17_10 telegram_section" style="background-position:-773px 0;"></span>';
                            }

                            if (get_option("vk_skin")) {

                                $icon = get_option("vk_skin");

                                echo '<span class="row_17_10 vk_section sfsi-bgimage" style="background: url(' . $icon . ') no-repeat;"></span>';
                            } else {

                                echo '<span class="row_17_10 vk_section" style="background-position:-838px 0;"></span>';
                            }

                            if (get_option("ok_skin")) {

                                $icon = get_option("ok_skin");

                                echo '<span class="row_17_10 ok_section sfsi-bgimage" style="background: url(' . $icon . ') no-repeat;"></span>';
                            } else {

                                echo '<span class="row_17_10 ok_section" style="background-position:-909px 0;"></span>';
                            }

                            if (get_option("weibo_skin")) {

                                $icon = get_option("weibo_skin");

                                echo '<span class="row_17_10 weibo_section sfsi-bgimage" style="background: url(' . $icon . ') no-repeat;"></span>';
                            } else {

                                echo '<span class="row_17_10 weibo_section" style="background-position:-977px 0;"></span>';
                            }

                            if (get_option("wechat_skin")) {

                                $icon = get_option("wechat_skin");

                                echo '<span class="row_17_10 wechat_section sfsi-bgimage" style="background: url(' . $icon . ') no-repeat;"></span>';
                            } else {

                                echo '<span class="row_1_18  wechat_section"></span>';
                            }

                            ?>

                    </div>
                </li>
                <?php
                }
                ?><?php if (get_option('sfsi_custom_icons') == 'no') { ?>

                <li class="sfsi_custom_icons_q4">
                    <div style="float: left;">
                        <a class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" target="_blank" style="display: flex;">

                            <input type="radio" class="styled" />

                            <label style="line-height:20px !important;margin-top:15px;opacity:0.5">Custom Icons -</label>
                        </a>
                    </div>
                    <div>
                        <p style="margin-top: 3px;">
                            <a style="color: #12a252 !important;font-weight: bold; border-bottom:none;text-decoration: none;">
                                Premium Feature:
                            </a>
                            <span>Upload any icons you want <a class="pop-up" style="cursor:pointer; color: #12a252 !important;border-bottom: 1px solid #12a252;text-decoration: none;font-weight: bold;" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" target="_blank">
                                Get it now. 
                            <!-- </a>&nbsp;  Custom design for the social media platforms implemented in the plugin under question number 1 - </span> -->
                            <!-- <a class="pop-up" style="cursor:pointer; color: #12a252 !important;border-bottom: 1px solid #12a252;text-decoration: none;font-weight: bold;" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" target="_blank">
                                Get it now.
                            </a> -->
                        </p>
                    </div>
                </li>
                <li class="sfsi_custom_icons_q4">
                    <div style="float: left;">
                        <a class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" target="_blank" style="display: flex;">

                            <input type="radio" class="styled" />

                            <label style="line-height:20px !important;margin-top:15px;opacity: 0.5;min-width: 79px;">Tailored -</label>
                        </a>
                    </div>
                    <div>
                        <p style="margin-top: 3px;">
                            <span>Let us create icons matching your site perfectly (both in terms of colors and shape). </span>
                            <a href="https://sellcodes.com/0AL0J23f" style="cursor:pointer; color: #12a252 !important;border-bottom: 1px solid #12a252;text-decoration: none;font-weight: bold;" target="_blank">
                                Learn more.
                            </a>
                        </p>
                    </div>
                </li>
            <?php
            }
            ?>
            <li>

                <?php include_once(SFSI_DOCROOT . '/views/subviews/que4/banner.php'); ?>

            </li>

            <li>

                <p style="font-weight: bold; margin: 12px 0 0;">

                    Need icons for another theme? Let us know in the

                    <a target="_blank" href="https://wordpress.org/support/plugin/ultimate-social-media-icons/#new-topic-0" style="color:#8E81BD; text-decoration:underline;">

                        Support Forum

                    </a>

                </p>

            </li>

        </ul>

        <!--icon themes section start -->

        <?php include_once(SFSI_DOCROOT . '/views/subviews/que4/animatethem.php'); ?>

    </div>

    <?php sfsi_ask_for_help(3); ?>

    <!-- SAVE BUTTON SECTION   -->

    <div class="save_button tab_3_sav">

        <img src="<?php echo SFSI_PLUGURL ?>images/ajax-loader.gif" class="loader-img" alt="error" />

        <?php $nonce = wp_create_nonce("update_step3"); ?>

        <a href="javascript:;" id="sfsi_save3" title="Save" data-nonce="<?php echo $nonce; ?>">Save</a>

    </div>

    <!-- END SAVE BUTTON SECTION   -->

    <a class="sfsiColbtn closeSec" href="javascript:;">Collapse area</a>

    <label class="closeSec"></label>

    <!-- ERROR AND SUCCESS MESSAGE AREA-->

    <p class="red_txt errorMsg" style="display:none"> </p>

    <p class="green_txt sucMsg" style="display:none"> </p>

</div>

<!-- END Section 3 "What design & animation do you want to give your icons?" main div  -->