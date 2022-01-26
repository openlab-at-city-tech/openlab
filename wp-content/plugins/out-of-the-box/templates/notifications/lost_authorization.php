<?php
/** Don't edit this file directly as it will automatically be overwritten
 * when updating the plugin. Instead, use the 'outofthebox_set_lost_authorization_template' filter
 * to modify the location of the template that needs to be used.
 *
 * E.g.
 *
 * add_filter('outofthebox_set_lost_authorization_template','change_lost_authorization_template', 10, 1);
 *
 * public function change_lost_authorization_template($template_location, $events){
 *   return WP_CONTENT_DIR .'/custom_notifications/lost_authorization.php'
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
                                <img alt="" height="auto" src="<?php echo OUTOFTHEBOX_ROOTPATH; ?>/templates/notifications/images/support_header.png" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;" width="600">
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
                                    <p>The 
                                      <strong>Out-of-the-Box
                                      </strong> plugin on 
                                      <a href="<?php echo get_site_url(); ?>">
                                        <?php echo get_site_url(); ?>
                                      </a> is not able to refresh access to the Dropbox <strong><?php echo !empty($account) ? '('.$account->get_email().'- ID: '.$account->get_id().')' : ''; ?></strong>. Please relink the plugin to your account by visiting the Admin Dashboard.
                                    </p>
                                    </p>
                                    <p>In case you are not able to reauthorize the plugin, please check the status of the Dropbox API service via 
                                      <a href="https://status.dropbox.com/">https://status.dropbox.com/</a>.
                                    </p>
                                    <br/>
                                    <p>Kind regards,
                                      <br/>
                                      <br/>
                                      WP Cloud Plugins
                                      <br/>
                                      <a href="https://www.wpcloudplugins.com">www.wpcloudplugins.com</a>
                                    </p>
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
                                <td align="center" vertical-align="middle" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                  <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:separate;width:300px;line-height:100%;">
                                    <tr>
                                      <td align="center" bgcolor="#5e6ebf" role="presentation" style="border:none;border-radius:3px;cursor:auto;padding:10px 25px;background:<?php echo $colors['accent']; ?>;" valign="middle">
                                        <a href="<?php echo get_admin_url(null, 'admin.php?page=OutoftheBox_settings_dashboard'); ?>" style="background:<?php echo $colors['accent']; ?>;color:#ffffff;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:17px;font-weight:bold;line-height:120%;Margin:0;text-decoration:none;text-transform:none;" target="_blank">
                                          ❱❱❱
                                          <?php esc_html_e('Authorize the plugin!','wpcloudplugins'); ?>
                                        </a>
                                      </td>
                                    </tr>
                                  </table>
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
                                  <div style="font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:12px;font-weight:400;line-height:24px;text-align:left;color:#000000;">
                                    <p>If you are receiving those emails often, enable your 
                                      <code>WP_DEBUG
                                      </code> log to allow the plugin to log the error information it receives from the API and contact the WP Cloud Plugins support team.
                                    </p>
                                  </div>
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
