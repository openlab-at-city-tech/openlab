<?php

/* show a pop on the as per user chose under section 7 */
function sfsi_frontPopUp()
{
    ob_start();
    echo sfsi_FrontPopupDiv();
    echo  $output = ob_get_clean();
}
/* check where to be pop-shown */
function sfsi_check_PopUp($content)
{
    global $post;
    global $wpdb;

    $content = '';

    $sfsi_section7_options =  unserialize(get_option('sfsi_section7_options', false));

    if (isset($sfsi_section7_options['sfsi_Show_popupOn']) && !empty($sfsi_section7_options['sfsi_Show_popupOn'])) {

        if ($sfsi_section7_options['sfsi_Show_popupOn'] == "blogpage") {
            if (!is_feed() && !is_home() && !is_page()) {
                $content =  sfsi_frontPopUp() . $content;
            }
        } else if ($sfsi_section7_options['sfsi_Show_popupOn'] == "selectedpage") {
            if (!empty($post->ID) && !empty($sfsi_section7_options['sfsi_Show_popupOn_PageIDs'])) {
                if (is_page() && in_array($post->ID,  unserialize($sfsi_section7_options['sfsi_Show_popupOn_PageIDs']))) {
                    $content =  sfsi_frontPopUp() . $content;
                }
            }
        } else if ($sfsi_section7_options['sfsi_Show_popupOn'] == "everypage") {
            $content = sfsi_frontPopUp() . $content;
        }
    }

    /* check for pop times */
    if (isset($sfsi_section7_options['sfsi_Shown_pop']) && !empty($sfsi_section7_options['sfsi_Shown_pop']) && $sfsi_section7_options['sfsi_Shown_pop'] == "once") {
        $time_popUp = (int) $sfsi_section7_options['sfsi_Shown_popupOnceTime'];
        $time_popUp = $time_popUp * 1000;
        ob_start();
        ?>
<script>

window.addEventListener('sfsi_functions_loaded', function() {
    if (typeof sfsi_time_pop_up == 'function') {
        sfsi_time_pop_up(<?php echo $time_popUp ?>);
    }
})
</script>
<?php
        echo ob_get_clean();
        return $content;
    }

    if (isset($sfsi_section7_options['sfsi_Shown_pop']) && !empty($sfsi_section7_options['sfsi_Shown_pop'])) {

        if ($sfsi_section7_options['sfsi_Shown_pop'] == "ETscroll") {
            $time_popUp = (int) $sfsi_section7_options['sfsi_Shown_popupOnceTime'];
            $time_popUp = $time_popUp * 1000;
            ob_start();
            ?>
<script>
window.addEventListener('sfsi_functions_loaded', function() {
    if (typeof sfsi_responsive_toggle == 'function') {
        sfsi_responsive_toggle(<?php echo $time_popUp ?>);
        // console.log('sfsi_responsive_toggle');

    }
})
</script>
<?php
            echo ob_get_clean();
        }
        if ($sfsi_section7_options['sfsi_Shown_pop'] == "LimitPopUp") {
            $time_popUp   = (int) $sfsi_section7_options['sfsi_Shown_popuplimitPerUserTime'];
            $end_time     = (int) $_COOKIE['sfsi_socialPopUp'] + ($time_popUp * 60);
            $time_popUp   = $time_popUp * 1000;

            if (!empty($end_time)) {
                if ($end_time < time()) {
                    ?>
<script>


window.addEventListener('sfsi_functions_loaded', function() {
    if (typeof sfsi_social_pop_up == 'function') {
        sfsi_social_pop_up(<?php echo $time_popUp ?>);
        // console.log('sfsi_social_pop_up');
    }
})
</script>
<?php
                }
            }
            echo ob_get_clean();
        }
    }
    return $content;
}
/* make front end pop div */
function sfsi_FrontPopupDiv()
{
    global $wpdb;
    /* get all settings for icons saved in admin */
    $sfsi_section1_options =  unserialize(get_option('sfsi_section1_options', false));
    $custom_i = unserialize($sfsi_section1_options['sfsi_custom_files']);
    if ($sfsi_section1_options['sfsi_rss_display'] == 'no' &&  $sfsi_section1_options['sfsi_email_display'] == 'no' && $sfsi_section1_options['sfsi_facebook_display'] == 'no' && $sfsi_section1_options['sfsi_twitter_display'] == 'no' && $sfsi_section1_options['sfsi_youtube_display'] == 'no' && $sfsi_section1_options['sfsi_pinterest_display'] == 'no' && $sfsi_section1_options['sfsi_linkedin_display'] == 'no' && empty($custom_i)) {
        $icons = '';
        return $icons;
        exit;
    }
    $sfsi_section7_options =  unserialize(get_option('sfsi_section7_options', false));
    $sfsi_section5 =  unserialize(get_option('sfsi_section5_options', false));
    $sfsi_section4 =  unserialize(get_option('sfsi_section4_options', false));
    /* calculate the width and icons display alignments */
    $heading_text = (isset($sfsi_section7_options['sfsi_popup_text'])) ? $sfsi_section7_options['sfsi_popup_text'] : 'Enjoy this site? Please follow and like us!';
    $div_bgColor = (isset($sfsi_section7_options['sfsi_popup_background_color'])) ? $sfsi_section7_options['sfsi_popup_background_color'] : '#fff';
    $div_FontFamily = (isset($sfsi_section7_options['sfsi_popup_font'])) ? $sfsi_section7_options['sfsi_popup_font'] : 'Arial';
    $div_BorderColor = (isset($sfsi_section7_options['sfsi_popup_border_color'])) ? $sfsi_section7_options['sfsi_popup_border_color'] : '#d3d3d3';
    $div_Fonttyle = (isset($sfsi_section7_options['sfsi_popup_fontStyle'])) ? $sfsi_section7_options['sfsi_popup_fontStyle'] : 'normal';
    $div_FontColor = (isset($sfsi_section7_options['sfsi_popup_fontColor'])) ? $sfsi_section7_options['sfsi_popup_fontColor'] : '#000';
    $div_FontSize = (isset($sfsi_section7_options['sfsi_popup_fontSize'])) ? $sfsi_section7_options['sfsi_popup_fontSize'] : '26';
    $div_BorderTheekness = (isset($sfsi_section7_options['sfsi_popup_border_thickness'])) ? $sfsi_section7_options['sfsi_popup_border_thickness'] : '1';
    $div_Shadow = (isset($sfsi_section7_options['sfsi_popup_border_shadow']) && $sfsi_section7_options['sfsi_popup_border_shadow'] == "yes") ? $sfsi_section7_options['sfsi_popup_border_thickness'] : 'no';

    $style = "background-color:" . $div_bgColor . ";border:" . $div_BorderTheekness . "px solid" . $div_BorderColor . "; font-style:" . $div_Fonttyle . ";color:" . $div_FontColor;
    if ($sfsi_section7_options['sfsi_popup_border_shadow'] == "yes") {
        $style .= ";box-shadow:12px 30px 18px #CCCCCC;";
    }
    $h_style = "font-family:" . $div_FontFamily . ";font-style:" . $div_Fonttyle . ";color:" . $div_FontColor . ";font-size:" . $div_FontSize . "px";
    /* get all icons including custom icons */
    $custom_icons_order = unserialize($sfsi_section5['sfsi_CustomIcons_order']);
    $icons_order = array(
        $sfsi_section5['sfsi_rssIcon_order'] => 'rss',
        $sfsi_section5['sfsi_emailIcon_order'] => 'email',
        $sfsi_section5['sfsi_facebookIcon_order'] => 'facebook',
        $sfsi_section5['sfsi_twitterIcon_order'] => 'twitter',
        $sfsi_section5['sfsi_youtubeIcon_order'] => 'youtube',
        $sfsi_section5['sfsi_pinterestIcon_order'] => 'pinterest',
        $sfsi_section5['sfsi_linkedinIcon_order'] => 'linkedin',
        $sfsi_section5['sfsi_instagramIcon_order'] => 'instagram',
        $sfsi_section5['sfsi_telegramIcon_order'] => 'telegram',
        $sfsi_section5['sfsi_vkIcon_order'] => 'vk',
        $sfsi_section5['sfsi_okIcon_order'] => 'ok',
        $sfsi_section5['sfsi_weiboIcon_order'] => 'weibo',
        $sfsi_section5['sfsi_wechatIcon_order'] => 'wechat',

    );
    $icons = array();
    $elements = array();
    $icons =  unserialize($sfsi_section1_options['sfsi_custom_files']);
    if (is_array($icons))  $elements = array_keys($icons);
    $cnt = 0;
    $total = isset($custom_icons_order) && is_array($custom_icons_order) ? count($custom_icons_order) : 0;
    if (!empty($icons) && is_array($icons)) :
        foreach ($icons as $cn => $c_icons) {
            if (is_array($custom_icons_order)) :
                if (in_array($custom_icons_order[$cnt]['ele'], $elements)) :
                    $key = key($elements);
                    unset($elements[$key]);

                    $icons_order[$custom_icons_order[$cnt]['order']] = array('ele' => $cn, 'img' => $c_icons);
                else :
                    $icons_order[] = array('ele' => $cn, 'img' => $c_icons);
                endif;

                $cnt++;
            else :
                $icons_order[] = array('ele' => $cn, 'img' => $c_icons);
            endif;
        }
    endif;
    ksort($icons_order);     /* short icons in order to display */
    $icons = '<div class="sfsi_outr_div" > <div class="sfsi_FrntInner_chg" style="' . $style . '">';
    //adding close button
    $icons .= '<div class="sfsiclpupwpr" onclick="sfsihidemepopup();"><img src="' . SFSI_PLUGURL . 'images/close.png" alt="error" /></div>';

    if (!empty($heading_text)) {
        $icons .= '<h2 style="' . $h_style . '">' . $heading_text . '</h2>';
    }
    $ulmargin = "";
    if ($sfsi_section4['sfsi_display_counts'] == "no") {
        $ulmargin = "margin-bottom:0px";
    }
    /* make icons with all settings saved in admin  */
    $icons .= '<ul style="' . $ulmargin . '">';
    foreach ($icons_order  as $index => $icn) :

        if (is_array($icn)) {
            $icon_arry = $icn;
            $icn = "custom";
        }
        switch ($icn): case 'rss':
                if ($sfsi_section1_options['sfsi_rss_display'] == 'yes')  $icons .= "<li>" . sfsi_prepairIcons('rss', 1) . "</li>";
                break;
            case 'email':
                if ($sfsi_section1_options['sfsi_email_display'] == 'yes')   $icons .= "<li>" . sfsi_prepairIcons('email', 1) . "</li>";
                break;
            case 'facebook':
                if ($sfsi_section1_options['sfsi_facebook_display'] == 'yes') $icons .= "<li>" . sfsi_prepairIcons('facebook', 1) . "</li>";
                break;
            case 'twitter':
                if ($sfsi_section1_options['sfsi_twitter_display'] == 'yes')    $icons .= "<li>" . sfsi_prepairIcons('twitter', 1) . "</li>";
                break;
            case 'youtube':
                if ($sfsi_section1_options['sfsi_youtube_display'] == 'yes')     $icons .= "<li>" . sfsi_prepairIcons('youtube', 1) . "</li>";
                break;
            case 'pinterest':
                if ($sfsi_section1_options['sfsi_pinterest_display'] == 'yes')     $icons .= "<li>" . sfsi_prepairIcons('pinterest', 1) . "</li>";
                break;
            case 'linkedin':
                if ($sfsi_section1_options['sfsi_linkedin_display'] == 'yes')    $icons .= "<li>" . sfsi_prepairIcons('linkedin', 1) . "</li>";
                break;
            case 'instagram':
                if ($sfsi_section1_options['sfsi_instagram_display'] == 'yes')    $icons .= "<li>" . sfsi_prepairIcons('instagram', 1) . "</li>";
                break;
            case 'telegram':
                if ($sfsi_section1_options['sfsi_telegram_display'] == 'yes')    $icons .= "<li>" . sfsi_prepairIcons('telegram', 1) . "</li>";
                break;
            case 'vk':
                if ($sfsi_section1_options['sfsi_vk_display'] == 'yes')    $icons .= "<li>" . sfsi_prepairIcons('vk', 1) . "</li>";
                break;
            case 'ok':
                if ($sfsi_section1_options['sfsi_ok_display'] == 'yes')    $icons .= "<li>" . sfsi_prepairIcons('ok', 1) . "</li>";
                break;
            case 'weibo':
                if ($sfsi_section1_options['sfsi_weibo_display'] == 'yes')    $icons .= "<li>" . sfsi_prepairIcons('weibo', 1) . "</li>";
                break;
            case 'wechat':
                if ($sfsi_section1_options['sfsi_wechat_display'] == 'yes')    $icons .= "<li>" . sfsi_prepairIcons('wechat', 1) . "</li>";
                break;
            case 'custom':
                $icons .= "<li>" . sfsi_prepairIcons($icon_arry['ele'], 1) . "</li>";
                break;
        endswitch;
    endforeach;
    $icons .= '</ul></div ></div >';

    return $icons;
}
?>