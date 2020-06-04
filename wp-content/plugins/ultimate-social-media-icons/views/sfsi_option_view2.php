<?php
/* unserialize all saved option for second section options */
$option4 =  unserialize(get_option('sfsi_section4_options', false));
$option2 =  unserialize(get_option('sfsi_section2_options', false));

/*
 * Sanitize, escape and validate values
 */
$option2['sfsi_rss_url']                 = (isset($option2['sfsi_rss_url']))
    ? esc_url($option2['sfsi_rss_url'])
    : '';
$option2['sfsi_rss_icons']                 = (isset($option2['sfsi_rss_icons']))
    ? sanitize_text_field($option2['sfsi_rss_icons'])
    : '';
$option2['sfsi_email_url']                 = (isset($option2['sfsi_email_url']))
    ? esc_url($option2['sfsi_email_url'])
    : '';

$option2['sfsi_facebookPage_option']    = (isset($option2['sfsi_facebookPage_option']))
    ? sanitize_text_field($option2['sfsi_facebookPage_option'])
    : '';
$option2['sfsi_facebookPage_url']         = (isset($option2['sfsi_facebookPage_url']))
    ? esc_url($option2['sfsi_facebookPage_url'])
    : '';
$option2['sfsi_facebookLike_option']    = (isset($option2['sfsi_facebookLike_option']))
    ? sanitize_text_field($option2['sfsi_facebookLike_option'])
    : '';
$option2['sfsi_facebookShare_option']     = (isset($option2['sfsi_facebookShare_option']))
    ? sanitize_text_field($option2['sfsi_facebookShare_option'])
    : '';

$option2['sfsi_twitter_followme']         = (isset($option2['sfsi_twitter_followme']))
    ? sanitize_text_field($option2['sfsi_twitter_followme'])
    : '';
$option2['sfsi_twitter_followUserName'] = (isset($option2['sfsi_twitter_followUserName']))
    ? sanitize_text_field($option2['sfsi_twitter_followUserName'])
    : '';
$option2['sfsi_twitter_aboutPage']         = (isset($option2['sfsi_twitter_aboutPage']))
    ? sanitize_text_field($option2['sfsi_twitter_aboutPage'])
    : '';
$option2['sfsi_twitter_page']             = (isset($option2['sfsi_twitter_page']))
    ? sanitize_text_field($option2['sfsi_twitter_page'])
    : '';
$option2['sfsi_twitter_pageURL']         = (isset($option2['sfsi_twitter_pageURL']))
    ? esc_url($option2['sfsi_twitter_pageURL'])
    : '';
$option2['sfsi_twitter_aboutPageText']     = (isset($option2['sfsi_twitter_aboutPageText']))
    ? sanitize_text_field($option2['sfsi_twitter_aboutPageText'])
    : '';

$option2['sfsi_youtube_pageUrl']         = (isset($option2['sfsi_youtube_pageUrl']))
    ? esc_url($option2['sfsi_youtube_pageUrl'])
    : '';
$option2['sfsi_youtube_page']             = (isset($option2['sfsi_youtube_page']))
    ? sanitize_text_field($option2['sfsi_youtube_page'])
    : '';
$option2['sfsi_youtube_follow']         = (isset($option2['sfsi_youtube_follow']))
    ? sanitize_text_field($option2['sfsi_youtube_follow'])
    : '';

$option2['sfsi_pinterest_page']         = (isset($option2['sfsi_pinterest_page']))
    ? sanitize_text_field($option2['sfsi_pinterest_page'])
    : '';
$option2['sfsi_pinterest_pageUrl']        = (isset($option2['sfsi_pinterest_pageUrl']))
    ? esc_url($option2['sfsi_pinterest_pageUrl'])
    : '';
$option2['sfsi_pinterest_pingBlog']     = (isset($option2['sfsi_pinterest_pingBlog']))
    ? sanitize_text_field($option2['sfsi_pinterest_pingBlog'])
    : '';
$option2['sfsi_instagram_pageUrl']        = (isset($option2['sfsi_instagram_pageUrl']))
    ? esc_url($option2['sfsi_instagram_pageUrl'])
    : '';

$option2['sfsi_linkedin_page']             = (isset($option2['sfsi_linkedin_page']))
    ? sanitize_text_field($option2['sfsi_linkedin_page'])
    : '';
$option2['sfsi_linkedin_pageURL']         = (isset($option2['sfsi_linkedin_pageURL']))
    ? esc_url($option2['sfsi_linkedin_pageURL'])
    : '';
$option2['sfsi_linkedin_follow']         = (isset($option2['sfsi_linkedin_follow']))
    ? sanitize_text_field($option2['sfsi_linkedin_follow'])
    : '';
$option2['sfsi_linkedin_followCompany']    = (isset($option2['sfsi_linkedin_followCompany']))
    ? intval($option2['sfsi_linkedin_followCompany'])
    : '';
$option2['sfsi_linkedin_SharePage']     = (isset($option2['sfsi_linkedin_SharePage']))
    ? sanitize_text_field($option2['sfsi_linkedin_SharePage'])
    : '';

$option2['sfsi_linkedin_recommendBusines'] = (isset($option2['sfsi_linkedin_recommendBusines']))
    ? sanitize_text_field($option2['sfsi_linkedin_recommendBusines'])
    : '';
$option2['sfsi_linkedin_recommendCompany'] = (isset($option2['sfsi_linkedin_recommendCompany']))
    ? sanitize_text_field($option2['sfsi_linkedin_recommendCompany'])
    : '';
$option2['sfsi_linkedin_recommendProductId'] = (isset($option2['sfsi_linkedin_recommendProductId']))
    ? intval($option2['sfsi_linkedin_recommendProductId'])
    : '';
$option2['sfsi_telegram_message']                 = (isset($option2['sfsi_telegram_message']))
    ? sanitize_text_field($option2['sfsi_telegram_message'])
    : '';
$option2['sfsi_telegram_username']             = (isset($option2['sfsi_telegram_username']))
    ? sanitize_text_field($option2['sfsi_telegram_username'])
    : '';
$option2['sfsi_telegram_page']                    = (isset($option2['sfsi_telegram_page']))
    ? sanitize_text_field($option2['sfsi_telegram_page'])
    : '';
$option2['sfsi_telegram_pageURL']                 = (isset($option2['sfsi_telegram_pageURL']))
    ? esc_url($option2['sfsi_telegram_pageURL'])
    : '';
$option2['sfsi_vk_page']                        = (isset($option2['sfsi_vk_page']))
    ? sanitize_text_field($option2['sfsi_vk_page'])
    : '';
$option2['sfsi_vk_pageURL']                 = (isset($option2['sfsi_vk_pageURL']))
    ? esc_url($option2['sfsi_vk_pageURL'])
    : '';
$option2['sfsi_weibo_page']                 = (isset($option2['sfsi_weibo_page']))
    ? sanitize_text_field($option2['sfsi_weibo_page'])
    : '';
$option2['sfsi_weibo_pageURL']                 = (isset($option2['sfsi_weibo_pageURL']))
    ? esc_url($option2['sfsi_weibo_pageURL'])
    : '';
$option2['sfsi_ok_page']                 = (isset($option2['sfsi_ok_page']))
    ? sanitize_text_field($option2['sfsi_ok_page'])
    : '';
$option2['sfsi_ok_pageURL']                 = (isset($option2['sfsi_ok_pageURL']))
    ? esc_url($option2['sfsi_ok_pageURL'])
    : '';

if ( isset($option2['sfsi_youtubeusernameorid']) && "name" == $option2['sfsi_youtubeusernameorid'] && !empty($option2['sfsi_youtubeusernameorid'])) {

    if (isset($option2['sfsi_ytube_user']) && !empty($option2['sfsi_ytube_user'])) {
        $option2['sfsi_ytube_user']   = sfsi_sanitize_field($option2['sfsi_ytube_user']);
    } else {
        $option2['sfsi_ytube_user']   = isset($option4['sfsi_youtube_user']) && !empty($option4['sfsi_youtube_user']) ?  $option4['sfsi_youtube_user'] : "";
    }
}

if ( isset($option2['sfsi_youtubeusernameorid']) && "id" == $option2['sfsi_youtubeusernameorid']  && !empty($option2['sfsi_youtubeusernameorid'])) {

    if (isset($option2['sfsi_ytube_chnlid']) && !empty($option2['sfsi_ytube_chnlid'])) {
        $option2['sfsi_ytube_chnlid']   = sfsi_sanitize_field($option2['sfsi_ytube_chnlid']);
    } else {
        $option2['sfsi_ytube_chnlid']   = isset($option4['sfsi_youtube_channelId']) && !empty($option4['sfsi_youtube_channelId']) ?  $option4['sfsi_youtube_channelId'] : "";
    }
}
?>

<!-- Section 2 "What do you want the icons to do?" main div Start -->
<div class="tab2">
    <!-- RSS ICON -->
    <div class="row bdr_top rss_section">
        <h2 class="sfsicls_rs_s">RSS</h2>
        <div class="inr_cont">
            <p>When clicked on, users can subscribe via RSS</p>
            <div class="rss_url_row">
                <h4>RSS URL:</h4>
                <input name="sfsi_rss_url" id="sfsi_rss_url" class="add" type="url" value="<?php echo ($option2['sfsi_rss_url'] != '') ?  $option2['sfsi_rss_url'] : ''; ?>" placeholder="E.g http://www.yoursite.com/feed" />
                <span class="sfrsTxt">For most blogs it’s <strong>http://blogname.com/feed</strong></span>
            </div>
        </div>
    </div>
    <!-- END RSS ICON -->

    <!-- EMAIL ICON -->
    <?php
    $feedId         = sanitize_text_field(get_option('sfsi_feed_id', false));
    $connectToFeed     = "http://api.follow.it/?" . base64_encode("userprofile=wordpress&feed_id=" . $feedId);
    ?>
    <div class="row email_section">
        <h2 class="sfsicls_email">Email</h2>

        <div class="inr_cont">
            <p>
                It allows your visitors to subscribe to your site (on <a href="http://api.follow.it/widgets/emailSubscribeEncFeed/<?php echo $feedId; ?>/<?php echo base64_encode(8); ?>" target="new">this screen</a>) and receive new posts automatically by email.
            </p>
            <p>Please pick which icon type you want to use:</p>
            <ul class="tab_2_email_sec">
                <li>
                    <div class="sfsiicnsdvwrp">
                        <input name="sfsi_rss_icons" <?php echo ($option2['sfsi_rss_icons'] == 'email') ?  'checked="true"' : ''; ?> type="radio" value="email" class="styled" /><span class="email_icn"></span>
                    </div>
                    <label>Email icon</label>
                </li>
                <li>
                    <div class="sfsiicnsdvwrp">
                        <input name="sfsi_rss_icons" <?php echo ($option2['sfsi_rss_icons'] == 'subscribe') ?  'checked="true"' : ''; ?> type="radio" value="subscribe" class="styled" /><span class="subscribe_icn"></span>
                    </div>
                    <label>Email + Follow text<span class="sficndesc"> </span></label>
                </li>
                <li>
                    <div class="sfsiicnsdvwrp">
                        <input name="sfsi_rss_icons" <?php echo ($option2['sfsi_rss_icons'] == 'sfsi') ?  'checked="true"' : ''; ?> type="radio" value="sfsi" class="styled" /><span class="sf_arow"></span>
                    </div>
                    <label>follow.it icon<span class="sfplsdesc"> (provider of the service)</span></label>
                </li>
            </ul>
            <p>The service offers many (more) advantages: </p>
            <div class='sfsi_service_row'>
                <div class='sfsi_service_column'>
                    <ul>
                        <li><span>More people come back</span> to your site</li>
                        <li>See your<span> subscribers’ emails</span> & <span>interesting statistics</span></li>
                        <li>Automatically post on<span> Facebook & Twitter</span></li>
                    </ul>
                </div>
                <div class='sfsi_service_column'>
                    <ul>
                        <li><span>Get more traffic</span> by being listed in the follow.it directory</li>
                        <li><span>Get alerts</span> when people subscribe or unsubscribe</li>
                        <li><span>Tailor the sender name & subject line</span> of the emails </li>
                    </ul>
                </div>
            </div>

            <form id="calimingOptimizationForm" method="get" action="https://api.follow.it/wpclaimfeeds/getFullAccess" target="_blank">
                <div class="sfsi_inputbtn">
                    <input type="hidden" name="feed_id" value="<?php echo sanitize_text_field(get_option('sfsi_feed_id', false)); ?>" />
                    <input type="email" name="email" value="<?php echo bloginfo('admin_email'); ?>" />
                </div>
                <div class='sfsi_more_services_link'>
                    <a href="javascript:;" id="sfsi_getMeFullAccess" class="sfsi_getMeFullAccess_class" title="Give me access" data-nonce-fetch-feed-id="<?php echo wp_create_nonce( 'sfsi_get_feed_id' );?>" >
                        Click here to benefit from all advantages >
                    </a>
                </div>
            </form>

            <p class='sfsi_email_last_paragraph'>
                This will create your FREE account on follow.it, using above email. <br>
                All data will be treated highly confidentially, see the
                <a href="https://follow.it/info/privacy" target="new">
                    Privacy Policy.
                </a>
            </p>
            <div class="sfsi_new_prmium_follw">
                <p><b>New:</b> In our Premium Plugin you can now give your email icon other functions too, e.g. <b>contact you </b>(email), <b>share by email,</b> and <b>link to a certain page </b>(e.g. your contact form or newsletter sign-up site). <a style="cursor:pointer" class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" class="sfisi_font_bold" target="_blank">Go premium now</a><a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=more_functions_email_icon&utm_medium=banner" class="sfsi_font_inherit" target="_blank"> or learn more.</a>
            </div>
        </div>
    </div>
    <!-- END EMAIL ICON -->

    <!-- FACEBOOK ICON -->
    <div class="row facebook_section">
        <h2 class="sfsicls_facebook">Facebook</h2>
        <div class="inr_cont">
            <p>The facebook icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do <a class="rit_link pop-up" href="javascript:;" data-id="fbex-s2">(see an example)</a>.</p>
            <p>The facebook icon should allow users to...</p>

            <p class="radio_section fb_url"><input name="sfsi_facebookPage_option" <?php echo ($option2['sfsi_facebookPage_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /><label>Visit my Facebook page at:</label><input class="add" name="sfsi_facebookPage_url" type="url" value="<?php echo ($option2['sfsi_facebookPage_url'] != '') ?  $option2['sfsi_facebookPage_url'] : ''; ?>" placeholder="E.g https://www.facebook.com/your_page_name" /></p>

            <p class="radio_section fb_url extra_sp"><input name="sfsi_facebookLike_option" <?php echo ($option2['sfsi_facebookLike_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /><label>Like my blog on Facebook (+1)</label></p>

            <p class="radio_section fb_url extra_sp"><input name="sfsi_facebookShare_option" <?php echo ($option2['sfsi_facebookShare_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /><label>Share my blog with friends (on Facebook)</label></p>
            <div class="sfsi_new_prmium_follw">
                <p><b>New:</b> In our Premium Plugin you can also allow users to follow you on Facebook <b>directly from your site</b> (without leaving your page, increasing followers). <a style="cursor:pointer" class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" class="sfisi_font_bold" target="_blank">Go premium now</a><a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=direct_follow_facebook&utm_medium=banner" class="sfsi_font_inherit" target="_blank"> or learn more.</a></p>
            </div>
        </div>
    </div>
    <!-- END FACEBOOK ICON -->

    <!-- TWITTER ICON -->
    <div class="row twitter_section">
        <h2 class="sfsicls_twt">Twitter</h2>
        <div class="inr_cont twt_tab_2">
            <p>The Twitter icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do <a class="rit_link pop-up" href="javascript:;" data-id="twex-s2">(see an example)</a>.</p>
            <p>The Twitter icon should allow users to...</p>

            <p class="radio_section fb_url"><input name="sfsi_twitter_page" <?php echo ($option2['sfsi_twitter_page'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /><label>Visit me on Twitter:</label><input name="sfsi_twitter_pageURL" type="url" placeholder="http://" value="<?php echo ($option2['sfsi_twitter_pageURL'] != '') ?  $option2['sfsi_twitter_pageURL'] : ''; ?>" class="add" /></p>

            <div class="radio_section fb_url twt_fld"><input name="sfsi_twitter_followme" <?php echo ($option2['sfsi_twitter_followme'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /><label>Follow me on Twitter:</label><input name="sfsi_twitter_followUserName" type="text" value="<?php echo ($option2['sfsi_twitter_followUserName'] != '') ?  $option2['sfsi_twitter_followUserName'] : ''; ?>" placeholder="my_twitter_name" class="add" /></div>
            <?php 
            $twitter_text = isset($option2['sfsi_twitter_aboutPageText']) && !empty($option2['sfsi_twitter_aboutPageText']) ? $option2['sfsi_twitter_aboutPageText'] : '';
            ?>
            <div class="radio_section fb_url twt_fld_2"><input name="sfsi_twitter_aboutPage" <?php echo ($option2['sfsi_twitter_aboutPage'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /><label>Tweet about my page:</label><textarea name="sfsi_twitter_aboutPageText" id="sfsi_twitter_aboutPageText" class="add_txt" placeholder="Hey, check out this cool site I found: www.yourname.com #Topic via@my_twitter_name"><?php echo $twitter_text; ?></textarea><?php /*echo ($option2['sfsi_twitter_aboutPageText']!='') ?  stripslashes($option2['sfsi_twitter_aboutPageText']) : '' ;*/?></div>
            <div class="sfsi_new_prmium_follw">
                <p><b>New: </b>Tweeting becomes really fun in the Premium Plugin – you can insert tags to automatically pull the title of the story & link to the story, attach pictures & snippets to the Tweets (‘Twitter cards’) and user Url-shorteners, all leading to more Tweets and traffic for your site! <a style="cursor:pointer" class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" class="sfisi_font_bold" target="_blank">Go premium now</a><a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=better_tweets&utm_medium=banner" class="sfsi_font_inherit" target="_blank"> or learn more.</a></p>
            </div>
        </div>
    </div>
    <!-- END TWITTER ICON -->

    <!-- YOUTUBE ICON -->
    <div class="row youtube_section">
        <h2 class="sfsicls_utube">Youtube</h2>
        <div class="inr_cont utube_inn">
            <p>The Youtube icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do <a class="rit_link pop-up" href="javascript:;" data-id="ytex-s2">(see an example)</a>.</p>

            <p>The youtube icon should allow users to... </p>

            <p class="radio_section fb_url"><input name="sfsi_youtube_page" <?php echo ($option2['sfsi_youtube_page'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /><label>Visit my Youtube page at:</label><input name="sfsi_youtube_pageUrl" type="url" placeholder="http://" value="<?php echo ($option2['sfsi_youtube_pageUrl'] != '') ?  $option2['sfsi_youtube_pageUrl'] : ''; ?>" class="add" /></p>

            <p class="radio_section fb_url"><input name="sfsi_youtube_follow" <?php echo ($option2['sfsi_youtube_follow'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /><label>Subscribe to me on Youtube <span>(allows people to subscribe to you directly, without leaving your blog)</span></label></p>

            <!--Adding Code for Channel Id and Channel Name-->
            <div class="cstmutbewpr">
                <ul class="enough_waffling">
                    <li onclick="showhideutube(this);"><input name="sfsi_youtubeusernameorid" <?php echo (isset($option2['sfsi_youtubeusernameorid']) && $option2['sfsi_youtubeusernameorid'] == 'name') ?  'checked="true"' : ''; ?> type="radio" value="name" class="styled" /><label>User Name</label></li>
                    <li onclick="showhideutube(this);"><input name="sfsi_youtubeusernameorid" <?php echo (isset($option2['sfsi_youtubeusernameorid']) && $option2['sfsi_youtubeusernameorid'] == 'id') ?  'checked="true"' : ''; ?> type="radio" value="id" class="styled" /><label>Channel Id</label></li>
                </ul>
                <div class="cstmutbtxtwpr">
                    <?php
                    $sfsi_youtubeusernameorid = isset($option2['sfsi_youtubeusernameorid']) ? $option2['sfsi_youtubeusernameorid'] : "";
                    ?>
                    <div class="cstmutbchnlnmewpr" <?php if ($sfsi_youtubeusernameorid != 'id') {
                                                        echo 'style="display: block;"';
                                                    } ?>>
                        <p class="extra_pp"><label>UserName:</label><input name="sfsi_ytube_user" type="url" value="<?php echo (isset($option2['sfsi_ytube_user']) && $option2['sfsi_ytube_user'] != '') ?  $option2['sfsi_ytube_user'] : ''; ?>" placeholder="Youtube username" class="add" /></p>
                        <div class="utbe_instruction">
                            To find your User ID/Channel ID, login to your YouTube account, click the user icon at the top right corner and select "Settings", then click "Advanced" under "Name" and you will find both your "Channel ID" and "User ID" under "Account Information".
                        </div>
                    </div>
                    <div class="cstmutbchnlidwpr" <?php if ($sfsi_youtubeusernameorid == 'id') {
                                                        echo 'style="display: block;"';
                                                    } ?>>
                        <p class="extra_pp"><label>ChannelId:</label><input name="sfsi_ytube_chnlid" type="url" value="<?php echo (isset($option2['sfsi_ytube_chnlid']) && $option2['sfsi_ytube_chnlid'] != '') ?  $option2['sfsi_ytube_chnlid'] : ''; ?>" placeholder="youtube_channel_id" class="add" /></p>
                        <div class="utbe_instruction">
                            To find your User ID/Channel ID, login to your YouTube account, click the user icon at the top right corner and select "Settings", then click "Advanced" under "Name" and you will find both your "Channel ID" and "User ID" under "Account Information".
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- END YOUTUBE ICON -->

    <!-- PINTEREST ICON -->
    <div class="row pinterest_section">
        <h2 class="sfsicls_pinterest">Pinterest</h2>
        <div class="inr_cont">
            <p>The Pinterest icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do <a class="rit_link pop-up" href="javascript:;" data-id="pinex-s2">(see an example)</a>.</p>
            <p>The Pinterest icon should allow users to... </p>
            <p class="radio_section fb_url"><input name="sfsi_pinterest_page" <?php echo ($option2['sfsi_pinterest_page'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /><label>Visit my Pinterest page at:</label><input name="sfsi_pinterest_pageUrl" type="url" placeholder="http://" value="<?php echo ($option2['sfsi_pinterest_pageUrl'] != '') ?  $option2['sfsi_pinterest_pageUrl'] : ''; ?>" class="add" /></p>
            <div class="pint_url">
                <p class="radio_section fb_url"><input name="sfsi_pinterest_pingBlog" <?php echo ($option2['sfsi_pinterest_pingBlog'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /><label>Pin my blog on Pinterest (+1)</label></p>
            </div>
        </div>
    </div>
    <!-- END PINTEREST ICON -->

    <!-- INSTAGRAM ICON -->
    <div class="row instagram_section">
        <h2 class="sfsicls_instagram">Instagram</h2>
        <div class="inr_cont">
            <p>When clicked on, users will get directed to your Instagram page.</p>
            <p class="radio_section fb_url  cus_link instagram_space"><label>URL:</label><input name="sfsi_instagram_pageUrl" type="text" value="<?php echo (isset($option2['sfsi_instagram_pageUrl']) && $option2['sfsi_instagram_pageUrl'] != '') ?  $option2['sfsi_instagram_pageUrl'] : ''; ?>" placeholder="http://" class="add" /></p>
        </div>
    </div>
    <!-- END INSTAGRAM ICON -->

    <!-- LINKEDIN ICON -->
    <div class="row linkedin_section">
        <h2 class="sfsicls_linkdin">LinkedIn</h2>
        <div class="inr_cont linked_tab_2 link_in">
            <p>The LinkedIn icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do <a class="rit_link pop-up" href="javascript:;" data-id="linkex-s2">(see an example)</a>.</p>
            <p>You find your ID in the link of your profile page, e.g. https://www.linkedin.com/profile/view?id=<b>8539887</b>&trk=nav_responsive_tab_profile_pic</p>
            <p style="margin-bottom:20px;">The LinkedIn icon should allow users to... </p>
            <div class="radio_section fb_url link_1"><input name="sfsi_linkedin_page" <?php echo ($option2['sfsi_linkedin_page'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /><label>Visit my Linkedin page at:</label><input name="sfsi_linkedin_pageURL" type="text" placeholder="http://" value="<?php echo ($option2['sfsi_linkedin_pageURL'] != '') ?  $option2['sfsi_linkedin_pageURL'] : ''; ?>" class="add" /></div>
            <div class="radio_section fb_url link_2"><input name="sfsi_linkedin_follow" <?php echo ($option2['sfsi_linkedin_follow'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /><label>Follow me on Linkedin: </label><input name="sfsi_linkedin_followCompany" type="text" value="<?php echo ($option2['sfsi_linkedin_followCompany'] != '') ?  $option2['sfsi_linkedin_followCompany'] : ''; ?>" class="add" placeholder="Enter company ID, e.g. 123456" /></div>

            <div class="radio_section fb_url link_3"><input name="sfsi_linkedin_SharePage" <?php echo ($option2['sfsi_linkedin_SharePage'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /><label>Share my page on LinkedIn</label></div>

            <div class="radio_section fb_url link_4"><input name="sfsi_linkedin_recommendBusines" <?php echo ($option2['sfsi_linkedin_recommendBusines'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /><label class="anthr_labl">Recommend my business or product on Linkedin:</label><input name="sfsi_linkedin_recommendProductId" type="text" value="<?php echo ($option2['sfsi_linkedin_recommendProductId'] != '') ?  $option2['sfsi_linkedin_recommendProductId'] : ''; ?>" class="add link_dbl" placeholder="Enter Product ID, e.g. 1441" /> <input name="sfsi_linkedin_recommendCompany" type="text" value="<?php echo ($option2['sfsi_linkedin_recommendCompany'] != '') ?  $option2['sfsi_linkedin_recommendCompany'] : ''; ?>" class="add" placeholder="Enter company name, e.g. Google”" /></div>

            <div class="lnkdin_instruction">
                To find your Product or Company ID, you can use their ID lookup tool at <a target="_blank" href="https://developer.linkedin.com/apply-getting-started#company-lookup">https://developer.linkedin.com/apply-getting-started#company-lookup</a>. You need to be logged in to Linkedin to be able to use it.
            </div>
        </div>
    </div>

    <!-- TELEGRAM ICON -->
    <div class="row telegram_section">
        <h2 class="sfsicls_telegram">Telegram</h2>
        <div class="inr_cont telegram_tab_2">
            <p>Clicking on this icon will allow users to contact you on Telegram.</p>
            <!-- <input name="sfsi_telegram_message"  checked="true" type="checkbox" value="yes" class=""  style="display:none"/> -->

            <p class="radio_section fb_url no_check ">
                <label>Pre-filled message</label>
                <input name="sfsi_telegram_message" type="text" value="<?php echo ($option2['sfsi_telegram_message'] != '') ?  $option2['sfsi_telegram_message'] : ''; ?>" placeholder="my_telegram_name" class="add" />
            </p>

            <p class="radio_section fb_url no_check">
                <label>My Telegram username</label>
                <input name="sfsi_telegram_username" type="url" placeholder="username" value="<?php echo ($option2['sfsi_telegram_username'] != '') ?  $option2['sfsi_telegram_username'] : ''; ?>" class="add" />
            </p>

            <div class="sfsi_new_prmium_follw">
                <p><b>New: </b>In our Premium Plugin you can now give your Telegram icon sharing functionality too, e.g. <b> share your website/blog with friends. </b><a style="cursor:pointer" class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" class="sfisi_font_bold" target="_blank">Go premium now</a><a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=telegram_sharing&utm_medium=banner" class="sfsi_font_inherit" target="_blank"> or learn more.</a></p>
            </div>
        </div>
    </div>

    <!-- END TELEGRAM ICON -->

    <!-- WECHAT ICON -->
    <div class="row wechat_section">
        <h2 class="sfsicls_wechat">WeChat</h2>
        <div class="inr_cont wechat_tab_2">
            <p>When clicked on, your website/blog will be shared on WeChat.</p>
            <input name="sfsi_wechatShare_option" checked="true" type="checkbox" value="yes" class="" style="display:none" />
            <div class="sfsi_new_prmium_follw">
                <p><b>New: </b>In our Premium Plugin you can also allow users to <b>follow you </b> on WeChat <a style="cursor:pointer" class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" class="sfisi_font_bold" target="_blank">Go premium now</a><a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=wechat_sharing&utm_medium=banner" class="sfsi_font_inherit" target="_blank"> or learn more.</a></p>
            </div>
        </div>
    </div>
    <!-- END WECHAT ICON -->
    <!-- WEIBO ICON -->
    <div class="row weibo_section">
        <h2 class="sfsicls_weibo">Weibo</h2>
        <div class="inr_cont weibo_tab_2">
            <p>When clicked on, users will get directed to your Weibo page.</p>

            <p class="radio_section fb_url no_check"><input name="sfsi_weibo_page" checked="true" type="checkbox" value="yes" class="" style="display:none" /><label>Visit me on Weibo:</label><input name="sfsi_weibo_pageURL" type="url" placeholder="http://" value="<?php echo ($option2['sfsi_weibo_pageURL'] != '') ?  $option2['sfsi_weibo_pageURL'] : ''; ?>" class="add" /></p>

            <div class="sfsi_new_prmium_follw">
                <p><b>New: </b> In our Premium Plugin you can now give Weibo icon other functions too, e.g. <b>your website/blog, share your website/blog</b> on Weibo. <a style="cursor:pointer" class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" class="sfisi_font_bold" target="_blank">Go premium now</a><a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=weibo_like_and_share&utm_medium=banner" class="sfsi_font_inherit" target="_blank"> or learn more.</a></p>
            </div>
        </div>
    </div>
    <!-- END WEIBO ICON -->

    <!-- VK ICON -->
    <div class="row vk_section">
        <h2 class="sfsicls_vk">VK</h2>
        <div class="inr_cont vk_tab_2">
            <p>When clicked on, users will get directed to your VK page.</p>

            <p class="radio_section fb_url no_check"><input name="sfsi_vk_page" checked="true" type="checkbox" value="yes" class="" style="display:none" /><label>Visit me on VK:</label><input name="sfsi_vk_pageURL" type="url" placeholder="http://" value="<?php echo ($option2['sfsi_vk_pageURL'] != '') ?  $option2['sfsi_vk_pageURL'] : ''; ?>" class="add" /></p>

            <div class="sfsi_new_prmium_follw">
                <p><b>New: </b>In our Premium Plugin you can now give your VK icon sharing functionality too, e.g. <b>share your website/blog </b> with friends. <a style="cursor:pointer" class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" class="sfisi_font_bold" target="_blank">Go premium now</a><a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=vk_share&utm_medium=banner" class="sfsi_font_inherit" target="_blank"> or learn more.</a></p>
            </div>
        </div>
    </div>
    <!-- END VK ICON -->
    <!-- OK ICON -->
    <div class="row ok_section">
        <h2 class="sfsicls_ok">OK</h2>
        <div class="inr_cont ok_tab_2">
            <p>When clicked on, users will get directed to your OK page.</p>

            <p class="radio_section fb_url no_check"><input name="sfsi_ok_page" checked="true" type="checkbox" value="yes" class="" style="display:none" /><label>Visit me on OK:</label><input name="sfsi_ok_pageURL" type="url" placeholder="http://" value="<?php echo ($option2['sfsi_ok_pageURL'] != '') ?  $option2['sfsi_ok_pageURL'] : ''; ?>" class="add" /></p>

            <div class="sfsi_new_prmium_follw">
                <p><b>New: </b>In our Premium Plugin you can now give OK icon other functions too, e.g. <b> like your website/blog </b>, subscribe/follow you on OK. <a style="cursor:pointer" class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" class="sfisi_font_bold" target="_blank">Go premium now</a><a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=ok_like_and_subscribe&utm_medium=banner" class="sfsi_font_inherit" target="_blank"> or learn more.</a></p>
            </div>
        </div>
    </div>
    <!-- END OK ICON -->
    <!-- END LINKEDIN ICON -->

    <!-- Custom icon section start here -->
    <div class="custom-links custom_section">
        <?php
        $costom_links    =    unserialize($option2['sfsi_CustomIcon_links']);
        $count        =    1;
        $bannerDisplay =   "display:none;";
        for ($i = $first_key; $i <= $endkey; $i++) :
            ?>
            <?php if (!empty($icons[$i])) :
                $bannerDisplay =   "display:block;";
                ?>
                <div class="row  sfsiICON_<?php echo $i; ?> cm_lnk">
                    <h2 class="custom">
                        <span class="customstep2-img">
                            <img src="<?php echo (!empty($icons[$i])) ? esc_url($icons[$i]) : SFSI_PLUGURL . 'images/custom.png'; ?>" id="CImg_<?php echo $new_element; ?>" style="border-radius:48%" alt="error" />
                        </span>
                        <span class="sfsiCtxt">Custom <?php echo $count; ?></span>
                    </h2>
                    <div class="inr_cont ">
                        <p>Where do you want this icon to link to?</p>
                        <p class="radio_section fb_url custom_section cus_link ">
                            <label>Link :</label>
                            <input name="sfsi_CustomIcon_links[]" type="text" value="<?php echo (isset($costom_links[$i]) && $costom_links[$i] != '') ?  esc_url($costom_links[$i]) : ''; ?>" placeholder="http://" class="add" file-id="<?php echo $i; ?>" />
                        </p>
                    </div>
                </div>
                <?php $count++;
            endif;
        endfor; ?>

        <div class="notice_custom_icons_premium sfsi_new_prmium_follw" style="<?php echo $bannerDisplay; ?>">
            <p><b>New: </b>In the Premium Plugin you can also give custom icons the feature that when people click on it, they can call you, or send you an SMS. <a style="cursor:pointer" class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" class="sfisi_font_bold" target="_blank">Go premium now</a><a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=call_or_sms_feature_custom_icons&utm_medium=banner" class="sfsi_font_inherit" target="_blank"> or learn more.</a></p>
        </div>
    </div>
    <!-- END Custom icon section here -->

    <?php sfsi_ask_for_help(2); ?>    <!-- SAVE BUTTON SECTION   -->
    <div class="save_button tab_2_sav">
        <img src="<?php echo SFSI_PLUGURL; ?>images/ajax-loader.gif" class="loader-img" alt="error" />
        <?php $nonce = wp_create_nonce("update_step2"); ?>
        <a href="javascript:;" id="sfsi_save2" title="Save" data-nonce="<?php echo $nonce; ?>">Save</a>
    </div>
    <!-- END SAVE BUTTON SECTION   -->

    <a class="sfsiColbtn closeSec" href="javascript:;">Collapse area</a>
    <label class="closeSec"></label>

    <!-- ERROR AND SUCCESS MESSAGE AREA-->
    <p class="red_txt errorMsg" style="display:none"> </p>
    <p class="green_txt sucMsg" style="display:none"> </p>

</div><!-- END Section 2 "What do you want the icons to do?" main div -->