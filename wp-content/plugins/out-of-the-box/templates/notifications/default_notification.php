<?php
/** Don't edit this file directly as it will automatically be overwritten
 * when updating the plugin. Instead, use the 'outofthebox_notification_set_template' filter
 * to modify the location of the template that needs to be used.
 *
 * E.g.
 *
 * add_filter('outofthebox_notification_set_template','change_notification_email', 10, 2);
 *
 * function change_notification_email($template_location, $notification){
 *   return WP_CONTENT_DIR .'/custom_notifications/default_notification.php';
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
                              <td style="font-size:0px;word-break:break-word;">
                                <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td height="25" style="vertical-align:top;height:25px;"><![endif]-->
                                <div style="height:10px;">&nbsp;
                                </div>
                                <!--[if mso | IE]></td></tr></table><![endif]-->
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
                                  <div style="font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:16px;font-weight:400;line-height:24px;text-align:left;color:#000000;">
                                    <?php echo $message; ?>
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
                                                        <td>
                                                          <div style="font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:10px;font-weight:400;line-height:24px;text-align:left;color:#000000;">
                                                            <a href="<?php echo get_site_url(); ?>">
                                                              <?php echo get_bloginfo(); ?>
                                                            </a>
                                                          </div>
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
