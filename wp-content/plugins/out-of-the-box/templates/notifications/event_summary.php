<?php
/** Don't edit this file directly as it will automatically be overwritten
 * when updating the plugin. Instead, use the 'outofthebox_events_set_summary_template' filter
 * to modify the location of the template that needs to be used.
 *
 * E.g.
 *
 * add_filter('outofthebox_events_set_summary_template','change_event_summary_email', 10, 1);
 *
 * public function change_event_summary_email($template_location, $events){
 *   return WP_CONTENT_DIR .'/custom_notifications/event_summary.php'
 * }
 */
?><!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
  <head>
    <title>
      <?php echo $subject; ?>
    </title>
    <!--[if !mso]><!-- -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--<![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
      #outlook a {
        padding: 0;
      }
      .ReadMsgBody {
        width: 100%;
      }
      .ExternalClass {
        width: 100%;
      }
      .ExternalClass * {
        line-height: 100%;
      }
      body {
        margin: 0;
        padding: 0;
        -webkit-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%;
      }
      table,
      td {
        border-collapse: collapse;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
      }
      img {
        border: 0;
        height: auto;
        line-height: 100%;
        outline: none;
        text-decoration: none;
        -ms-interpolation-mode: bicubic;
      }
      p {
        display: block;
        margin: 13px 0;
      }
    </style>
    <!--[if !mso]><!-->
    <style type="text/css">
      @media only screen and (max-width:480px) {
        @-ms-viewport {
          width: 320px;
        }
        @viewport {
          width: 320px;
        }
      }
    </style>
    <!--<![endif]-->
    <!--[if mso]>
<xml>
<o:OfficeDocumentSettings>
<o:AllowPNG/>
<o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml>
<![endif]-->
    <!--[if lte mso 11]>
<style type="text/css">
.outlook-group-fix { width:100% !important; }
</style>
<![endif]-->
    <style type="text/css">
      @media only screen and (min-width:480px) {
        .mj-column-per-100 {
          width: 100% !important;
          max-width: 100%;
        }
      }
    </style>
    <style type="text/css">
      @media only screen and (max-width:480px) {
        table.full-width-mobile {
          width: 100% !important;
        }
        td.full-width-mobile {
          width: auto !important;
        }
      }
    </style>
  </head>
  <body style="background-color:#ECECEC;">
    <div style="background-color:#ECECEC;">
      <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
          <tr>
            <td>
              <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
              <div style="Margin:0px auto;max-width:600px;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                  <tbody>
                    <tr>
                      <td style="direction:ltr;font-size:0px;padding:20px 0;padding-bottom:0;text-align:center;vertical-align:top;">
                        <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;"><![endif]-->
                        <div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                          <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                            <tr>
                              <td style="font-size:0px;word-break:break-word;">
                                <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td height="25" style="vertical-align:top;height:25px;"><![endif]-->
                                <div style="height:25px;">&nbsp;
                                </div>
                                <!--[if mso | IE]></td></tr></table><![endif]-->
                              </td>
                            </tr>
                          </table>
                        </div>
                        <!--[if mso | IE]></td></tr></table><![endif]-->
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!--[if mso | IE]></td></tr></table><![endif]-->
            </td>
          </tr>
        </tbody>
      </table>
      <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
      <div style="background:#1f2e78;background-color:#1f2e78;Margin:0px auto;max-width:600px;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:<?php echo $colors['accent']; ?>;background-color:<?php echo $colors['accent']; ?>;width:100%;">
          <tbody>
            <tr>
              <td style="direction:ltr;font-size:0px;padding-bottom:5px;text-align:center;vertical-align:top;">
                <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;"><![endif]-->
                <div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                  <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                    <tr>
                      <td align="center" style="font-size:0px;padding:0;word-break:break-word;">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
                          <tbody>
                            <tr>
                              <td style="width:600px;">
                                <img alt="" height="auto" src="<?php echo OUTOFTHEBOX_ROOTPATH; ?>/templates/notifications/images/event_summary_header.png" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;" width="600">
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </table>
                </div>
                <!--[if mso | IE]></td></tr></table><![endif]-->
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="body-section-outlook" style="width:600px;" width="600"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
      <div class="body-section" style="-webkit-box-shadow: 1px 4px 11px 0px rgba(0, 0, 0, 0.15); -moz-box-shadow: 1px 4px 11px 0px rgba(0, 0, 0, 0.15); box-shadow: 1px 4px 11px 0px rgba(0, 0, 0, 0.15); background: #ffffff; background-color: #ffffff; Margin: 0px auto; max-width: 600px;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;">
          <tbody>
            <tr>
              <td style="direction:ltr;font-size:0px;padding:20px 0;padding-bottom:0;padding-top:0;text-align:center;vertical-align:top;">
                <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" width="600px"><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                <div style="Margin:0px auto;max-width:600px;">
                  <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                    <tbody>
                      <tr>
                        <td style="direction:ltr;font-size:0px;padding:20px 0;padding-left:15px;padding-right:15px;text-align:center;vertical-align:top;">
                          <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:570px;"><![endif]-->
                          <div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                            <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                              <tr>
                                <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                  <div style="font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:24px;font-weight:400;line-height:24px;text-align:left;color:#000000;">
                                    <?php esc_html_e('Hi there!','wpcloudplugins'); ?>
                                  </div>
                                </td>
                              </tr>
                              <tr>
                                <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                  <div style="font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:16px;font-weight:400;line-height:24px;text-align:left;color:#000000;">
                                    <?php
                                        echo sprintf(esc_html__('This is the latest summary of all the activites that are logged since %s', 'wpcloudplugins'), '<em>'.date_i18n('l j F '.get_option('time_format'), date_format($since, 'U')).'</em>');
                                    ?>.
                                  </div>
                                </td>
                              </tr>
                              <tr>
                                <td style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                  <p style="border-top:dashed 1px lightgrey;font-size:1;margin:0px auto;width:100%;">
                                  </p>
                                  <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" style="border-top:dashed 1px lightgrey;font-size:1;margin:0px auto;width:520px;" role="presentation" width="520px" ><tr><td style="height:0;line-height:0;">&nbsp;</td></tr></table><![endif]-->
                                </td>
                              </tr>
                              <tr>
                                <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                  <div style="font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:24px;font-weight:400;line-height:24px;text-align:left;color:#000000;">Top 
                                    <?php echo $max_top_downloads; ?> downloads
                                  </div>
                                </td>
                              </tr>
                              <tr>
                                <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                  <table cellpadding="0" cellspacing="0" width="100%" border="0" style="cellspacing:0;color:#000000;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:14px;line-height:22px;table-layout:auto;width:100%;">
                                    <?php
                                        if (0 === $topdownloads['recordsFiltered']) {
                                            ?>
                                    <tr>
                                      <td style="width:20px;padding-right:10px;padding-top: 5px;padding-left: 5px;">
                                        <em>- 
                                          <?php esc_html_e('No downloads found in this period','wpcloudplugins'); ?> -
                                        </em>
                                      </td>
                                    </tr>
                                    <?php
                                        }

                                        $current = 1;

                                        foreach ($topdownloads['data'] as $entry) {
                                            if ($current > $max_top_downloads) {
                                                break;
                                            } ?>
                                    <tr style="<?php echo ($current % 2) ? 'background: #f4f4f4;' : ''; ?> height: 35px;">
                                      <td style="width:20px;padding-right:10px;padding-top: 5px;padding-left: 5px;">
                                        <img alt="" height="16" src="<?php echo $entry['icon']; ?>" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;" width="16">
                                      </td>
                                      <td style="line-height:25px;">
                                        <a href="https://www.dropbox.com/home/<?php echo $entry['entry_path']; ?>" target="_blank">
                                          <?php echo $entry['entry_name']; ?>
                                        </a>
                                        <br/>
                                        <div style="font-size:12px;line-height:18px;color:#a6a6a6;outline:none;text-decoration:none;">
                                          <?php echo $entry['parent_path']; ?>
                                        </div>
                                      </td>
                                      <td style="font-weight: bold;">
                                        <?php echo $entry['total']; ?>
                                      </td>
                                    </tr>
                                    <?php
                                        ++$current;
                                        }
                                    ?>
                                  </table>
                                </td>
                              </tr>
                              <tr>
                                <td style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                  <p style="border-top:dashed 1px lightgrey;font-size:1;margin:0px auto;width:100%;">
                                  </p>
                                  <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" style="border-top:dashed 1px lightgrey;font-size:1;margin:0px auto;width:520px;" role="presentation" width="520px"><tr><td style="height:0;line-height:0;">&nbsp;</td></tr></table><![endif]-->
                                </td>
                              </tr>
                              <tr>
                                <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                  <div style="font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:24px;font-weight:400;line-height:24px;text-align:left;color:#000000;">Latest activities
                                  </div>
                                </td>
                              </tr>
                              <tr>
                                <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                  <table cellpadding="0" cellspacing="0" width="100%" border="0" style="cellspacing:0;color:#000000;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:14px;line-height:22px;table-layout:auto;width:100%;">
                                    <?php
                                        if (0 === $all_events['recordsFiltered']) {
                                            ?>
                                    <tr>
                                      <td style="width:20px;padding-right:10px;padding-top: 5px;padding-left: 5px;">
                                        <em>- 
                                          <?php esc_html_e('No activities found in this period','wpcloudplugins'); ?> -
                                        </em>
                                      </td>
                                    </tr>
                                    <?php
                                        }

                                        $current = 1;
                                        $current_day = null;

                                        foreach ($all_events['data'] as $entry) {
                                            if ($current > $max_events) {
                                                break;
                                            }

                                            $unixtime = strtotime($entry['timestamp']);
                                            $event_day = date_i18n('l j F', $unixtime);
                                            $event_time = date_i18n(get_option('time_format'), $unixtime);

                                            if ($current_day !== $event_day) {
                                                ?>
                                    <tr>
                                      <td style="font-size:15px;font-weight: bold;padding-top:20px;padding-bottom:10px;" colspan="2">
                                        <?php echo $event_day; ?>
                                      </td>
                                    </tr>
                                    <?php
                                            }
                                            $current_day = $event_day; ?>
                                    <tr style="<?php echo ($current % 2) ? 'background: #f4f4f4;' : ''; ?> height: 50px;">
                                      <td style="line-height:25px;padding-left:5px;">
                                        <img src="<?php echo $entry['user_icon']; ?>" alt="" height="16" width="16" style="border:0;display:inline-block;outline:none;text-decoration:none;height:auto;width:16px;">
                                        <div style="font-weight:bold;display:inline-block;outline:none;text-decoration:none;">
                                          <?php echo $entry['user']; ?>
                                        </div>
                                        <?php echo strtolower($entry['type']); ?>
                                        <a href="https://www.dropbox.com/home/<?php echo $entry['entry_path']; ?>" target="_blank">
                                          <?php echo $entry['entry_name']; ?>
                                        </a>
                                        <br/>
                                        <div style="font-size:12px;line-height:18px;color:#a6a6a6;outline:none;text-decoration:none;">
                                          <?php echo $entry['parent_path']; ?>
                                        </div>
                                      </td>
                                      <td style="width:50px;padding-right:5px;padding-left:5px;">
                                        <?php echo $event_time; ?>
                                      </td>
                                    </tr>
                                    <?php
                                        ++$current;
                                        }
                                    ?>
                                  </table>
                                </td>
                              </tr>
                              <tr>
                                <td align="center" vertical-align="middle" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                  <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:separate;width:300px;line-height:100%;">
                                    <tr>
                                      <td align="center" bgcolor="#5e6ebf" role="presentation" style="border:none;border-radius:3px;cursor:auto;padding:10px 25px;background:<?php echo $colors['accent']; ?>;" valign="middle">
                                        <a href="<?php echo get_admin_url(null, 'admin.php?page=OutoftheBox_settings_dashboard'); ?>" style="background:<?php echo $colors['accent']; ?>;color:#ffffff;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:17px;font-weight:bold;line-height:120%;Margin:0;text-decoration:none;text-transform:none;" target="_blank">
                                          <?php
                                            $total = $all_events['recordsFiltered'];
                                            if ($total - $max_events > 0) {
                                                echo sprintf(esc_html__('View more activites (%s left)', 'wpcloudplugins'), ($total - $max_events));
                                            } else {
                                                esc_html_e('View all activites','wpcloudplugins');
                                            }
                                          ?>
                                        </a>
                                      </td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                              <tr>
                                <td style="font-size:0px;word-break:break-word;">
                                  <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td height="25" style="vertical-align:top;height:25px;"><![endif]-->
                                  <div style="height:25px;">&nbsp;
                                  </div>
                                  <!--[if mso | IE]></td></tr></table><![endif]-->
                                </td>
                              </tr>
                            </table>
                          </div>
                          <!--[if mso | IE]></td></tr></table><![endif]-->
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!--[if mso | IE]></td></tr></table></td></tr></table><![endif]-->
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!--[if mso | IE]></td></tr></table><![endif]-->
      <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
          <tr>
            <td>
              <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
              <div style="Margin:0px auto;max-width:600px;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                  <tbody>
                    <tr>
                      <td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;">
                        <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" width="600px"><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                        <div style="Margin:0px auto;max-width:600px;">
                          <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                            <tbody>
                              <tr>
                                <td style="direction:ltr;font-size:0px;padding:20px 0;padding-top:0;text-align:center;vertical-align:top;">
                                  <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;"><![endif]-->
                                  <div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
                                      <tbody>
                                        <tr>
                                          <td style="vertical-align:top;padding:0;">
                                            <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%">
                                              <tr>
                                                <td align="center" style="font-size:0px;padding:10px 25px;padding-top:20px;word-break:break-word;">
                                                  <div style="font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:11px;font-weight:400;line-height:16px;text-align:center;color:#000000;">You can change the frequency of this email or turn it on and off by visiting your 
                                                    <a href="<?php echo get_admin_url(null, 'admin.php?page=OutoftheBox_settings#settings_stats'); ?>">Out-of-the-Box options
                                                    </a> page.
                                                  </div>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                                  <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
                                                    <tbody>
                                                      <tr>
                                                        <td style="width:250px;">
                                                          <a href="https://www.wpcloudplugins.com/">
                                                            <img alt="" height="auto" src="<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/wpcloudplugins-logo-dark.png" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;" width="250">
                                                          </a>
                                                        </td>
                                                      </tr>
                                                    </tbody>
                                                  </table>
                                                </td>
                                              </tr>
                                            </table>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </div>
                                  <!--[if mso | IE]></td></tr></table><![endif]-->
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <!--[if mso | IE]></td></tr></table></td></tr></table><![endif]-->
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!--[if mso | IE]></td></tr></table><![endif]-->
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </body>
</html>
