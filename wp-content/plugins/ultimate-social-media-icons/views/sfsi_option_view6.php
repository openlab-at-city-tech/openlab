<?php
/* unserialize all saved option for  section 6 options */

$option6 =  unserialize(get_option('sfsi_section6_options', false));

/**

 * Sanitize, escape and validate values

 */

$option6['sfsi_show_Onposts']     = (isset($option6['sfsi_show_Onposts'])) ? sanitize_text_field($option6['sfsi_show_Onposts']) : 'no';

$option6['sfsi_show_Onbottom']     = (isset($option6['sfsi_show_Onbottom'])) ? sanitize_text_field($option6['sfsi_show_Onbottom']) : '';

$option6['sfsi_icons_postPositon']   = (isset($option6['sfsi_icons_postPositon'])) ? sanitize_text_field($option6['sfsi_icons_postPositon']) : '';

$option6['sfsi_icons_alignment']   = (isset($option6['sfsi_icons_alignment'])) ? sanitize_text_field($option6['sfsi_icons_alignment']) : '';

$option6['sfsi_rss_countsDisplay']   = (isset($option6['sfsi_rss_countsDisplay'])) ? sanitize_text_field($option6['sfsi_rss_countsDisplay']) : '';

$option6['sfsi_textBefor_icons']   = (isset($option6['sfsi_textBefor_icons'])) ? sanitize_text_field($option6['sfsi_textBefor_icons']) : '';

$option6['sfsi_rectsub']       = (isset($option6['sfsi_rectsub'])) ? sanitize_text_field($option6['sfsi_rectsub']) : '';

$option6['sfsi_rectfb']       = (isset($option6['sfsi_rectfb'])) ? sanitize_text_field($option6['sfsi_rectfb']) : '';

$option6['sfsi_rectshr']       = (isset($option6['sfsi_rectshr'])) ? sanitize_text_field($option6['sfsi_rectshr']) : '';

$option6['sfsi_recttwtr']       = (isset($option6['sfsi_recttwtr'])) ? sanitize_text_field($option6['sfsi_recttwtr']) : '';

$option6['sfsi_rectpinit']       = (isset($option6['sfsi_rectpinit'])) ? sanitize_text_field($option6['sfsi_rectpinit']) : '';

$option6['sfsi_rectfbshare']       = (isset($option6['sfsi_rectfbshare'])) ? sanitize_text_field($option6['sfsi_rectfbshare']) : '';

$option6['sfsi_display_button_type']     = (isset($option6['sfsi_display_button_type']))
  ? sanitize_text_field($option6['sfsi_display_button_type'])
  : '';
$option6['sfsi_show_premium_placement_box'] = (isset($option6['sfsi_show_premium_placement_box']))
  ? sanitize_text_field($option6['sfsi_show_premium_placement_box'])
  : 'yes';
$option6['sfsi_responsive_icons_end_post'] = (isset($option6['sfsi_responsive_icons_end_post']))
  ? sanitize_text_field($option6['sfsi_responsive_icons_end_post'])
  : 'no';


$sfsi_responsive_icons_default = array(
  "default_icons" => array(
    "facebook" => array("active" => "yes", "text" => "Share on Facebook", "url" => ""),
    "Twitter" => array("active" => "yes", "text" => "Tweet", "url" => ""),
    "Follow" => array("active" => "yes", "text" => "Follow us", "url" => ""),
  ),
  "custom_icons" => array(),
  "settings" => array(
    "icon_size" => "Medium",
    "icon_width_type" => "Fully responsive",
    "icon_width_size" => 240,
    "edge_type" => "Round",
    "edge_radius" => 5,
    "style" => "Gradient",
    "margin" => 10,
    "text_align" => "Centered",
    "show_count" => "no",
    "counter_color" => "#aaaaaa",
    "counter_bg_color" => "#fff",
    "share_count_text" => "SHARES"
  )
);
$sfsi_responsive_icons = (isset($option6["sfsi_responsive_icons"]) ? $option6["sfsi_responsive_icons"] : $sfsi_responsive_icons_default);
if (!isset($option6['sfsi_rectsub'])) {
  $option6['sfsi_rectsub'] = 'no';
}

if (!isset($option6['sfsi_rectfb'])) {
  $option6['sfsi_rectfb'] = 'yes';
}

if (!isset($option6['sfsi_recttwtr'])) {
  $option6['sfsi_recttwtr'] = 'no';
}

if (!isset($option6['sfsi_rectpinit'])) {
  $option6['sfsi_rectpinit'] = 'no';
}

if (!isset($option6['sfsi_rectfbshare'])) {
  $option6['sfsi_rectfbshare'] = 'no';
}
?>
<!-- Section 6 "Do you want to display icons at the end of every post?" main div Start -->
<div>
  <p class="clear" style="margin-top:0 !important">Here you have two options:</p>

  <div class="tab6">
    <ul class="sfsi_icn_listing8">

      <li class="sfsibeforeafterpostselector" style="max-width: none">
        <div class="radio_section tb_4_ck"></div>
        <div class="sfsi_right_info">
          <ul class="sfsi_tab_3_icns sfsi_shwthmbfraftr" style="margin:0">
            <li onclick="sfsi_togglbtmsection('sfsi_toggleonlyrspvshrng, .sfsi_responsive_show', 'sfsi_toggleonlystndrshrng, .sfsi_responsive_hide', this);sfsi_responsive_icon_show_responsive_options();" class="clckbltglcls sfsi_border_left_0" style="width:29%!important">
              <input name="sfsi_display_button_type" <?php echo ($option6['sfsi_display_button_type'] == 'responsive_button') ?  'checked="true"' : ''; ?> type="radio" value="responsive_button" class="styled" />
              <label class="labelhdng4" style="margin-top:2px">
                Responsive icons
              </label>
            </li>
            <li onclick="sfsi_togglbtmsection('sfsi_toggleonlystndrshrng, .sfsi_responsive_hide', 'sfsi_toggleonlyrspvshrng, .sfsi_responsive_show', this);" class="clckbltglcls sfsi_border_left_0" style="width:29%!important">
              <input name="sfsi_display_button_type" <?php echo ($option6['sfsi_display_button_type'] == 'standard_buttons') ?  'checked="true"' : ''; ?> type="radio" value="standard_buttons" class="styled" />
              <label class="labelhdng4" style="margin-top:2px">
                Original icons
              </label>
            </li>
            <li class="clckbltglcls sfsi_border_left_0 sfsi_disable_radio" style="width: 42% !important">
              <input type="radio" class="styled" />
              <label class="labelhdng4"  style="margin-top:2px" >
                Display the icons I selected above
              </label>
            </li>
            <p class="clear sfsi_border_left_0" style="width: 80%;width:calc( 100% - 102px );font-family:helveticaneue-light;font-size: 18px;color: #5a6570!important;margin: 10px 27px;margin-bottom: 0!important;margin-left:22px!important;padding-left: 0!important">Greyed-out options are available only in the <a class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" class="sfisi_font_bold" style="border-bottom: 1px solid #12a252;color: #12a252 !important;cursor:pointer;font-size:18px;" target="_blank">Premium Plugin</a></p>
            <?php $display = ($option6['sfsi_display_button_type'] == 'responsive_button') ? "display:block;border-left:0!important":"display:none;border-left:0!important"; ?>
            <li class="sfsi_toggleonlyrspvshrng" style="margin-left:20px;<?php echo $display; ?>">
              <label style="width: 80%;width:calc( 100% - 102px );font-family: helveticaneue-light;font-size: 18px;color: #5a6570;margin: 10px 0px;margin-top:-15px!important; padding-top:0!important">These
                are responsive & independent from the icons you selected elsewhere in the plugin.
                Preview:</label>
              <div style="width: 80%; margin-left:5px;  width:calc( 100% - 102px );">
                <div class="sfsi_responsive_icon_preview" style="width:calc( 100% - 50px );margin-left:-15px">

                  <?php echo sfsi_social_responsive_buttons(null, $option6, true); ?>
                </div> <!-- end sfsi_responsive_icon_preview -->
              </div>
              <ul>
                <li class="sfsi_responsive_default_icon_container sfsi_border_left_0 " style="margin: 10px 0px">
                  <label class="heading-label select-icons">
                    Select Icons
                  </label>
                </li>
                <?php foreach ($sfsi_responsive_icons['default_icons'] as $icon => $icon_config) :
                  ?>
                  <li class="sfsi_responsive_default_icon_container sfsi_vertical_center sfsi_border_left_0">
                    <div class="radio_section tb_4_ck">
                      <input name="sfsi_responsive_<?php echo $icon; ?>_display" <?php echo ($icon_config['active'] == 'yes') ?  'checked="true"' : ''; ?> id="sfsi_responsive_<?php echo $icon; ?>_display" type="checkbox" value="yes" class="styled" data-icon="<?php echo $icon; ?>" />
                    </div>
                    <span class="sfsi_icon_container">
                      <div class="sfsi_responsive_icon_item_container sfsi_responsive_icon_<?php echo strtolower($icon); ?>_container" style="word-break:break-all;padding-left:0">
                        <div style="display: inline-block;height: 40px;width: 40px;text-align: center;vertical-align: middle!important;float: left;">
                          <img style="float:none" src="<?php echo SFSI_PLUGURL; ?>images/responsive-icon/<?php echo $icon; ?><?php echo 'Follow' === $icon ? '.png' : '.svg'; ?>">
                        </div>
                        <span> <?php echo $icon_config["text"];  ?> </span>
                      </div>
                    </span>
                    <input type="text" class="sfsi_responsive_input" name="sfsi_responsive_<?php echo $icon ?>_input" value="<?php echo $icon_config["text"]; ?>" />
                    <a href="#" class="sfsi_responsive_default_url_toggler" style="text-decoration: none;">Define URL*</a>
                    <input style="display:none" class="sfsi_responsive_url_input" type="text" placeholder="Enter url" name="sfsi_responsive_<?php echo $icon ?>_url_input" value="<?php echo $icon_config["url"]; ?>" />
                    <a href="#" class="sfsi_responsive_default_url_hide" style="display:none"><span class="sfsi_cancel_text">Cancel</span><span class="sfsi_cancel_icon">&times;</span></a>
                  </li>

                <?php endforeach; ?>
              </ul>
              &nbsp;
              <p style="font-size:16px !important;padding-top: 0px;">
                <span>* All icons have «sharing» feature enabled by default. If you want to give them a
                  different function (e.g link to your Facebook page) then please click on «Define
                  url» next to the icon.</span>
              </p>
              <?php if ($option6['sfsi_show_premium_placement_box'] == 'yes') { ?>
                <div class="sfsi_new_prmium_follw" style="width: 91%;">
                  <p style="font-size:20px !important">
                    <b>New: </b>In the Premium Plugin, we also added: Pinterest, Linkedin, WhatsApp, VK,
                    OK, Telegram, Weibo, WeChat, Xing and the option to add custom icons. There are more placement options too, e.g.
                    place the responsive icons before/after posts/pages, show them only on
                    desktop/mobile, insert them manually (via shortcode).<a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=responsive_icons&utm_medium=banner" class="sfsi_font_inherit" target="_blank"> See all features</a>
                  </p>
                </div>
              <?php } ?>

              <div class="options">
                <label class="heading-label" style="width:auto!important;margin-top: 11px;margin-right: 11px;">
                  <b>So: do you want to display those at the end of every post?</b>
                </label>
                <ul style="display:flex">
                  <li style="min-width: 200px">
                    <input name="sfsi_responsive_icons_end_post" <?php echo ($option6['sfsi_responsive_icons_end_post'] == 'yes') ?  'checked="true"' : ''; ?> type="radio" value="yes" class="styled" />
                    <label class="labelhdng4" style="width: auto;">
                      Yes
                    </label>
                  </li>
                  <li>
                    <input name="sfsi_responsive_icons_end_post" <?php echo ($option6['sfsi_responsive_icons_end_post'] == 'no') ?  'checked="true"' : ''; ?> type="radio" value="no" class="styled" />
                    <label class="labelhdng4" style="width: auto;">
                      No
                    </label>
                  </li>
              </div>
            </li>
            <?php if ($option6['sfsi_display_button_type'] == 'standard_buttons') : $display = "display:block";
            else :  $display = "display:none";
            endif; ?>
            <li class="sfsi_toggleonlystndrshrng" style="margin-left:20px;<?php echo $display; ?>">
              <div class="radiodisplaysection" style="<?php echo $display; ?>">



                <!-- icons example  section -->
                <div class="social_icon_like1 cstmdsplyulwpr">

                  <ul>
                    <li>
                      <div class="radio_section tb_4_ck"><input name="sfsi_rectsub" <?php echo ($option6['sfsi_rectsub'] == 'yes') ?  'checked="true"' : ''; ?> id="sfsi_rectsub" type="checkbox" value="yes" class="styled" />
                      </div>

                      <a href="#" title="Subscribe Follow" class="cstmdsplsub">
                        <img src="<?php echo SFSI_PLUGURL; ?>images/follow_subscribe.png" alt="Subscribe Follow" />
                      </a>
                    </li>
                    <li>
                      <div class="radio_section tb_4_ck"><input name="sfsi_rectfb" <?php echo ($option6['sfsi_rectfb'] == 'yes') ?  'checked="true"' : ''; ?> id="sfsi_rectfb" type="checkbox" value="yes" class="styled" /></div>

                      <a href="#" title="Facebook Like">
                        <img src="<?php echo SFSI_PLUGURL; ?>images/like.jpg" alt="Facebook Like" />
                      </a>
                    </li>
                    <li>
                      <div class="radio_section tb_4_ck"><input name="sfsi_rectfbshare" <?php echo ($option6['sfsi_rectfbshare'] == 'yes') ?  'checked="true"' : ''; ?> id="sfsi_rectfbshare" type="checkbox" value="yes" class="styled" />
                      </div>
                      <a href="#" title="Facebook Share">
                        <img src="<?php echo SFSI_PLUGURL; ?>images/fbshare.png" alt="Facebook Share" />
                      </a>
                    </li>

                    <li>

                      <div class="radio_section tb_4_ck"><input name="sfsi_recttwtr" <?php echo ($option6['sfsi_recttwtr'] == 'yes') ?  'checked="true"' : ''; ?> id="sfsi_recttwtr" type="checkbox" value="yes" class="styled" />
                      </div>

                      <a href="#" title="twitter" class="cstmdspltwtr">

                        <img src="<?php echo SFSI_PLUGURL; ?>images/twiiter.png" alt="Twitter like" />
                      </a>

                    </li>

                    <li>

                      <div class="radio_section tb_4_ck"><input name="sfsi_rectpinit" <?php echo ($option6['sfsi_rectpinit'] == 'yes') ?  'checked="true"' : ''; ?> id="sfsi_rectpinit" type="checkbox" value="yes" class="styled" />
                      </div>

                      <a href="#" title="Pin It">
                        <img src="<?php echo SFSI_PLUGURL; ?>images/pinit.png" alt="Pin It" />
                      </a>
                    </li>
                  </ul>
                </div><!-- icons position section -->

                <p class="clear">Those are usually all you need: </p>

                <ul class="usually">
                  <li>1. The follow-icon ensures that your visitors subscribe to your newsletter</li>
                  <li>2. Facebook is No.1 in «liking», so it’s a must have</li>
                  <li>3. The Tweet-button allows quick tweeting of your article</li>
                  <li></li>
                  <li></li>
                </ul>
                <?php if ($option6['sfsi_show_premium_placement_box'] == 'yes') { ?>
                  <p class="sfsi_prem_plu_desc " style="float:left">
                    <b>New: </b>We also added a Linkedin share-icon in the Premium Plugin. <a class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" class="sfisi_font_bold" style="border-bottom: 1px solid #12a252;color: #12a252 !important;cursor:pointer;font-size:18px;" target="_blank">Go premium now</a><a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usm_settings_page&utm_campaign=linkedin_icon&utm_medium=banner" class="sfsi_font_inherit" style="color: #12a252 !important" target="_blank"> or
                      learn more</a>
                  </p>
                <?php } ?>
                <div class="options">
                  <label class="heading-label" style="width:auto!important;margin-top: 11px;margin-right: 11px;">
                    <b>So: do you want to display those at the end of every post?</b>
                  </label>
                  <ul style="display:flex">
                    <li style="min-width: 200px">
                      <input name="sfsi_show_Onposts" <?php echo ($option6['sfsi_show_Onposts'] == 'yes') ?  'checked="true"' : ''; ?> type="radio" value="yes" class="styled" />
                      <label class="labelhdng4" style="width: auto;">
                        Yes
                      </label>
                    </li>
                    <li>
                      <input name="sfsi_show_Onposts" <?php echo ($option6['sfsi_show_Onposts'] == 'no') ?  'checked="true"' : ''; ?> type="radio" value="no" class="styled" />
                      <label class="labelhdng4" style="width: auto;">
                        No
                      </label>
                    </li>

                </div>
                <div class="row PostsSettings_section">

                  <h4>Options:</h4>

                  <div class="options">

                    <label class="first">Text to appear before the sharing icons:</label><input name="sfsi_textBefor_icons" type="text" value="<?php echo ($option6['sfsi_textBefor_icons'] != '') ?  $option6['sfsi_textBefor_icons'] : ''; ?>" />

                  </div>

                  <!-- by developer - 28-05-2019 -->

                  <div class="options">
                    <p><b>New:</b> In the Premium Plugin you can choose to display the text before
                      the sharing icons in a font of your choice. You can also define the<b> font
                        size, type</b>, and the <b>margins below/above the icons</b>. <a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=more_placement_options&utm_medium=banner" target="_blank" style="color:#00a0d2 !important; text-decoration: none !important;">Check
                        it out.</a></p>
                  </div>

                  <!-- end  -->
                  <div class="options">
                    <label>Alignment of share icons: </label>
                    <div class="field"><select name="sfsi_icons_alignment" id="sfsi_icons_alignment" class="styled">
                        <option value="left" <?php echo ($option6['sfsi_icons_alignment'] == 'left') ?  'selected="selected"' : ''; ?>>
                          Left</option>
                        <!--<option value="center" <?php //echo ($option6['sfsi_icons_alignment']=='center') ?  'selected="selected"' : '' ;
                                                    ?>>Center</option>-->
                        <option value="right" <?php echo ($option6['sfsi_icons_alignment'] == 'right') ?  'selected="selected"' : ''; ?>>
                          Right</option>
                      </select>
                    </div>
                  </div>


                </div>
                <!-- by developer - 28-5-2019 -->

                <div class="sfsi_new_prmium_follw">
                  <p><b>New:</b> In our Premium Plugin you have many more placement options, e.g.
                    place the icons you selected under question 1, place them also on your homepage
                    (instead of only post’s pages), place them before posts (instead of only after
                    posts) etc. <a style="cursor:pointer" class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" class="sfisi_font_bold" target="_blank">See all features</a>
                    <!-- <a href="https://www.ultimatelysocial.com/usm-premium/?https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=more_placement_options&utm_medium=banner" class="sfsi_font_inherit" target="_blank"> or learn more.</a> -->
                  </p>
                </div>
              </div>
            </li>
            
            <?php $display2 = ($option6['sfsi_display_button_type'] == 'responsive_button' && $option6['sfsi_responsive_icons_end_post'] == 'yes' ) ? "display:block;border-left:0!important":"display:none;border-left:0!important"; ?>
            <!-- sfsi_responsive_icons_end_post -->
            <li class="sfsi_responsive_icon_option_li sfsi_responsive_show " style="margin-left:20px;border-left:0;<?php echo $display2 ?>">
              <label class="options heading-label" style="margin: 0px 0px 12px 0px;">
                Design options
              </label>
              <div class="options sfsi_margin_top_0 ">
                <label class="first">
                  Icons size:
                </label>
                <div class="field">
                  <div style="display:inline-block">
                    <select name="sfsi_responsive_icons_settings_icon_size" class="styled">
                      <option value="Small" <?php echo (isset($sfsi_responsive_icons["settings"]) && isset($sfsi_responsive_icons["settings"]["icon_size"]) && $sfsi_responsive_icons["settings"]["icon_size"] === "Small") ? 'selected="selected"' : ""; ?>>
                        Small
                      </option>
                      <option value="Medium" <?php echo (isset($sfsi_responsive_icons["settings"]) && isset($sfsi_responsive_icons["settings"]["icon_size"]) && $sfsi_responsive_icons["settings"]["icon_size"] === "Medium") ? 'selected="selected"' : ""; ?>>
                        Medium
                      </option>
                      <option value="Large" <?php echo (isset($sfsi_responsive_icons["settings"]) && isset($sfsi_responsive_icons["settings"]["icon_size"]) && $sfsi_responsive_icons["settings"]["icon_size"] === "Large") ? 'selected="selected"' : ""; ?>>
                        Large
                      </option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="options sfsi_margin_top_0 ">
                <label class="first">
                  Icons width:
                </label>
                <div class="field">
                  <div style="display:inline-block">
                    <select name="sfsi_responsive_icons_settings_icon_width_type" class="styled">
                      <option value="Fixed icon width" <?php echo (isset($sfsi_responsive_icons["settings"]) && isset($sfsi_responsive_icons["settings"]["icon_width_type"]) && $sfsi_responsive_icons["settings"]["icon_width_type"] === "Fixed icon width") ? 'selected="selected"' : ""; ?>>
                        Fixed icon width
                      </option>
                      <option value="Fully responsive" <?php echo (isset($sfsi_responsive_icons["settings"]) && isset($sfsi_responsive_icons["settings"]["icon_width_type"]) && $sfsi_responsive_icons["settings"]["icon_width_type"] === "Fully responsive") ? 'selected="selected"' : ""; ?>>
                        Fully responsive
                      </option>
                    </select>
                  </div>
                  <div class="sfsi_responsive_icons_icon_width sfsi_inputSec" style='display:<?php echo (isset($sfsi_responsive_icons["settings"]["icon_width_type"]) && $sfsi_responsive_icons["settings"]["icon_width_type"] == 'Fully responsive') ? 'none' : 'inline-block'; ?>'>
                    <span style="width:auto!important">of</span>
                    <input type="number" value="<?php echo isset($sfsi_responsive_icons["settings"]) && isset($sfsi_responsive_icons["settings"]["icon_width_size"]) ? $sfsi_responsive_icons["settings"]["icon_width_size"] : 140;  ?>" name="sfsi_responsive_icons_sttings_icon_width_size" style="float:none" />
                    </select>
                    <span class="sfsi_span_after_input">pixels</span>
                  </div>
                </div>
              </div>
              <div class="options sfsi_inputSec textBefor_icons_fontcolor sfsi_margin_top_0">
                <label class="first">
                  Edges:
                </label>
                <div class="field">
                  <div style="display:inline-block">
                    <select name="sfsi_responsive_icons_settings_edge_type" class="styled">
                      <option value="Round" <?php echo (isset($sfsi_responsive_icons["settings"]) && isset($sfsi_responsive_icons["settings"]["edge_type"]) && $sfsi_responsive_icons["settings"]["edge_type"] === "Round") ? 'selected="selected"' : ""; ?>>
                        Round
                      </option>
                      <option value="Sharp" <?php echo (isset($sfsi_responsive_icons["settings"]) && isset($sfsi_responsive_icons["settings"]["edge_type"]) && $sfsi_responsive_icons["settings"]["edge_type"] === "Sharp") ? 'selected="selected"' : ""; ?>>
                        Sharp
                      </option>
                    </select>
                  </div>
                  <span style="width:auto!important;font-size: 17px;color: #5A6570; <?php echo (isset($sfsi_responsive_icons["settings"]["edge_type"]) && $sfsi_responsive_icons["settings"]["edge_type"] == 'Sharp') ? 'display:none' : ''; ?>">with
                    border radius</span>
                </div>
                <div class="field-sfsi_responsive_icons_settings_edge_radius" style="position:absolute;margin-left: 6px;<?php echo (isset($sfsi_responsive_icons["settings"]["edge_type"]) && $sfsi_responsive_icons["settings"]["edge_type"] == 'Sharp') ? 'display:none' : 'display:inline-block'; ?>">
                  <select name="sfsi_responsive_icons_settings_edge_radius" id="sfsi_icons_alignment" class="styled">
                    <?php for ($i = 1; $i <= 20; $i++) : ?>
                      <option value="<?php echo $i; ?>" <?php echo (isset($sfsi_responsive_icons["settings"]) && isset($sfsi_responsive_icons["settings"]["edge_radius"]) && $sfsi_responsive_icons["settings"]["edge_radius"] == $i) ?  'selected="selected"' : ''; ?>>
                        <?php echo $i; ?>
                      </option>
                    <?php endfor; ?>
                  </select>
                </div>
                <!-- <span style=" <?php echo (isset($sfsi_responsive_icons["settings"]["edge_type"]) && $sfsi_responsive_icons["settings"]["edge_type"] == 'Sharp') ? 'display:none' : ''; ?>">pixels</span> -->

              </div>

              <div class="options sfsi_margin_top_0">
                <label class="first">
                  Style:
                </label>
                <div class="field">
                  <div style="display:inline-block">
                    <select name="sfsi_responsive_icons_settings_style" class="styled">
                      <option value="Flat" <?php echo (isset($sfsi_responsive_icons["settings"]) && isset($sfsi_responsive_icons["settings"]["style"]) && $sfsi_responsive_icons["settings"]["style"] === "Flat") ? 'selected="selected"' : ""; ?>>
                        Flat
                      </option>
                      <option value="Gradient" <?php echo (isset($sfsi_responsive_icons["settings"]) && isset($sfsi_responsive_icons["settings"]["style"]) && $sfsi_responsive_icons["settings"]["style"] === "Gradient") ? 'selected="selected"' : ""; ?>>
                        Gradient
                      </option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="options sfsi_margin_top_0 sfsi_inputSec">
                <label class="first">
                  Margin between icons:
                </label>
                <div class="field">
                  <input type="number" value="<?php echo isset($sfsi_responsive_icons["settings"]) && isset($sfsi_responsive_icons["settings"]["margin"]) ? $sfsi_responsive_icons["settings"]["margin"] : 0;  ?>" name="sfsi_responsive_icons_settings_margin" style="float:none" />
                  <span class="span_after_input">pixels</span>
                </div>
              </div>

              <div class="options sfsi_margin_top_0 sfsi_inputSec">
                <label class="first">
                  Margins:
                </label>
                <div class="field" style="float: none;">
                <span class="span_before_input" style="width: 120px;">Above Icon</span>
                  <input type="number" value="<?php echo isset($sfsi_responsive_icons["settings"]) && isset($sfsi_responsive_icons["settings"]["margin_above"]) ? $sfsi_responsive_icons["settings"]["margin_above"] : 0;  ?>" name="sfsi_responsive_icons_settings_margin_above" style="float:none" />
                  <span class="span_after_input">px</span>
                </div>
                <div class="field" style="float: none;">
                <span class="span_before_input" style="width: 120px;">Below Icon</span>
                  <input type="number" value="<?php echo isset($sfsi_responsive_icons["settings"]) && isset($sfsi_responsive_icons["settings"]["margin_below"]) ? $sfsi_responsive_icons["settings"]["margin_below"] : 0;  ?>" name="sfsi_responsive_icons_settings_margin_below" style="float:none" />
                  <span class="span_after_input">px</span>
                </div>
              </div>

              <div class="options sfsi_margin_top_0">
                <label class="first">
                  Text on icons:
                </label>
                <div class="field">
                  <div style="display:inline-block">
                    <select name="sfsi_responsive_icons_settings_text_align" class="styled">
                      <option value="Left aligned" <?php echo (isset($sfsi_responsive_icons["settings"]) && isset($sfsi_responsive_icons["settings"]["text_align"]) && $sfsi_responsive_icons["settings"]["text_align"] === "Left aligned") ? 'selected="selected"' : ""; ?>>
                        Left aligned
                      </option>
                      <option value="Centered" <?php echo (isset($sfsi_responsive_icons["settings"]) && isset($sfsi_responsive_icons["settings"]["text_align"]) && $sfsi_responsive_icons["settings"]["text_align"] === "Centered") ? 'selected="selected"' : ""; ?>>
                        Centered
                      </option>
                    </select>
                  </div>
                </div>
              </div>
            </li>
            
          </ul>
        </div>
      </li>
    </ul>

    

    <a class="sfsiColbtn closeSec" href="javascript:;">
      Collapse area
    </a>
    <label class="closeSec"></label>

    <!-- ERROR AND SUCCESS MESSAGE AREA-->
    <p class="red_txt errorMsg" style="display:none"> </p>
    <p class="green_txt sucMsg" style="display:none"> </p>
    <div class="clear"></div>

  </div>
</div>
<!-- END Section 6 "Do you want to display icons at the end of every post?" -->