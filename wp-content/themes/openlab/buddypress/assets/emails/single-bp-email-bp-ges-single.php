<?php
/**
 * bp-ges-single email template
 *
 * Magic numbers:
 *  1.618 = golden mean.
 *  1.35  = default body_text_size multipler. Gives default heading of 20px.
 *
 * @since 2.5.0
 *
 * @package BuddyPress
 * @subpackage Core
 */

/*
Based on the Cerberus "Fluid" template by Ted Goas (http://tedgoas.github.io/Cerberus/).
License for the original template:


The MIT License (MIT)

Copyright (c) 2013 Ted Goas

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$settings = bp_email_get_appearance_settings();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">
	<meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->

	<!-- CSS Reset -->
	<style type="text/css">
		/* What it does: Remove spaces around the email design added by some email clients. */
		/* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
		html,
		body {
			Margin: 0 !important;
			padding: 0 !important;
			height: 100% !important;
			width: 100% !important;
		}

		/* What it does: Stops email clients resizing small text. */
		* {
			-ms-text-size-adjust: 100%;
			-webkit-text-size-adjust: 100%;
		}

		/* What it does: Forces Outlook.com to display emails full width. */
		.ExternalClass {
			width: 100%;
		}

		/* What is does: Centers email on Android 4.4 */
		div[style*="margin: 16px 0"] {
			margin: 0 !important;
		}

		/* What it does: Stops Outlook from adding extra spacing to tables. */
		table,
		td {
			mso-table-lspace: 0pt !important;
			mso-table-rspace: 0pt !important;
		}

		/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */
		table {
			border-spacing: 0 !important;
			border-collapse: collapse !important;
			table-layout: fixed !important;
			Margin: 0 auto !important;
		}
		table table table {
			table-layout: auto;
		}

		body.email_bg a {
			color: <?php echo esc_html( $settings['body_text_color'] ); ?>;
		}

		/* What it does: Uses a better rendering method when resizing images in IE. */
		/* & manages img max widths to ensure content body images don't exceed template width. */
		img {
			-ms-interpolation-mode:bicubic;
			height: auto;
			max-width: 100%;
		}

		/* What it does: Overrides styles added when Yahoo's auto-senses a link. */
		.yshortcuts a {
			border-bottom: none !important;
		}

		/* What it does: A work-around for iOS meddling in triggered links. */
		a[x-apple-data-detectors] {
			color: inherit !important;
			text-decoration: underline !important;
		}
	</style>

</head>
<body class="email_bg" width="100%" height="100%" bgcolor="<?php echo esc_attr( $settings['email_bg'] ); ?>" style="Margin: 0;">
<table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" bgcolor="<?php echo esc_attr( $settings['email_bg'] ); ?>" style="border-collapse:collapse;" class="email_bg"><tr><td valign="top">
	<center style="width: 100%;">

		<!-- Visually Hidden Preheader Text : BEGIN -->
		<div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
			{{email.preheader}}
		</div>
		<!-- Visually Hidden Preheader Text : END -->

		<div style="max-width: 600px;">
			<!--[if (gte mso 9)|(IE)]>
			<table cellspacing="0" cellpadding="0" border="0" width="600" align="center">
			<tr>
			<td>
			<![endif]-->

			<!-- Email Header : BEGIN -->
			<table cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 600px; border-top: 7px solid <?php echo esc_attr( $settings['highlight_color'] ); ?>" bgcolor="<?php echo esc_attr( $settings['header_bg'] ); ?>" class="header_bg">
				<tr>
					<td style="text-align: center; padding: 15px 0; font-family: sans-serif; mso-height-rule: exactly; font-weight: bold; color: <?php echo esc_attr( $settings['header_text_color'] ); ?>; font-size: <?php echo esc_attr( $settings['header_text_size'] . 'px' ); ?>" class="header_text_color header_text_size">
						<?php
						/**
						 * Fires before the display of the email template header.
						 *
						 * @since 2.5.0
						 */
						do_action( 'bp_before_email_header' );
						?>
						<img src="https://openlab.citytech.cuny.edu/wp-content/themes/openlab/images/openlab-logo.jpg" alt="<?php echo bp_get_option( 'blogname' ); ?>" />

						<?php
						/**
						 * Fires after the display of the email template header.
						 *
						 * @since 2.5.0
						 */
						do_action( 'bp_after_email_header' );
						?>
					</td>
				</tr>
			</table>
			<!-- Email Header : END -->

			<!-- Email Body : BEGIN -->
			<table cellspacing="0" cellpadding="0" border="0" align="center" bgcolor="<?php echo esc_attr( $settings['body_bg'] ); ?>" width="100%" style="max-width: 600px; border-radius: 5px;" class="body_bg">

				<!-- 1 Column Text : BEGIN -->
				<tr>
					<td>
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
						  <tr>
								<td style="padding: 20px; font-family: sans-serif; mso-height-rule: exactly; line-height: <?php echo esc_attr( floor( $settings['body_text_size'] * 1.618 ) . 'px' ) ?>; color: #222; font-size: <?php echo esc_attr( $settings['body_text_size'] . 'px' ); ?>" class="body_text_size">
									<span style="font-weight: bold; font-size: <?php echo esc_attr( floor( $settings['body_text_size'] * 1.35 ) . 'px' ); ?>" class="welcome"><?php bp_email_the_salutation( $settings ); ?></span>

									<table cellspacing="0" cellpadding="0" border="0" width="100%">
										<tr>
											<td style="border-top: 10px solid transparent;">&nbsp;</td>
										</tr>

										<tr>
											<td style="border: 1px solid #ddd; padding: 20px; font-family: sans-serif; mso-height-rule: exactly; line-height: <?php echo esc_attr( floor( $settings['body_text_size'] * 1.618 ) . 'px' ) ?>; color: #222; font-size: <?php echo esc_attr( $settings['body_text_size'] . 'px' ); ?>" class="body_text_size">
												<p>{{{ges.action}}}</p>
												<p>{{{usermessage}}}</p>

												<p><a style="display: inline-block; background-color: #2b2b2b; color: #fff; padding: 6px 12px; border-radius: 3px; text-decoration: none;" href="{{{thread.url}}}">{{{ges.view-text}}}</a></p>
											</td>
										</tr>
									</table>
								</td>
						  </tr>
						</table>
					</td>
				</tr>
				<!-- 1 Column Text : BEGIN -->

			</table>
			<!-- Email Body : END -->

			<!-- Email Footer : BEGIN -->
			<br>

			<hr style="height: 5px; background-color: #f05b5b; border: none; margin: 0;" />
			<hr style="height: 1px; background-color: #fff; border: none; margin: 0;" />
			<hr style="height: 5px; background-color: #fbef60; border: none; margin: 0;" />

			<table cellspacing="0" cellpadding="10" border="0" align="left" width="100%" style="max-width: 600px;" bgcolor="#fafafa" class="footer_bg">
				<tr>
					<td style="color: #222; padding: 20px 10px; width: 100%; font-size: 12px; font-family: sans-serif; mso-height-rule: exactly; line-height: 19px; text-align: left;" class="footer_text_size">

						<p><span class="footer_text">{{{ges.email-setting-description}}}</span><br>
						Go to <a href="{{{group.url}}}notifications/">Membership > Your Email Options</a> to change your email settings for this {{{ges.group-type}}}.</p>

						<p><strong>Please note</strong>: You cannot reply by email to this notification. <a href="{{{thread.url}}}">Go to the post</a> to read or reply.</p>
					</td>
				</tr>
			</table>

			<table cellspacing="0" cellpadding="10" border="0" align="left" width="100%" style="max-width: 600px; color: #222;" bgcolor="#f0f0f0" class="footer_bg">
				<tr>
					<td style="padding: 10px; width: 100%%; font-size: <?php echo esc_attr( $settings['footer_text_size'] . 'px' ); ?>; font-family: sans-serif; mso-height-rule: exactly; line-height: <?php echo esc_attr( floor( $settings['footer_text_size'] * 1.618 ) . 'px' ) ?>; text-align: left; color: <?php echo esc_attr( $settings['footer_text_color'] ); ?>;" class="footer_text_color footer_text_size">
						<p style="font-size: 14px;"><strong>The OpenLab at City Tech: A place to learn, work, and share</strong></p>

						<table cellspacing="0" cellpadding="0" border="0" align="left" width="100%">
							<tr>
								<td width="70">
									<a href="http://www.citytech.cuny.edu/"><img style="max-width: 100%;" src="https://openlab.citytech.cuny.edu/wp-content/mu-plugins/css/images/ctnyc_seal.png" alt="New York City College of Technology" border="0"></a>
								</td>

								<td width="80">
									<a href="http://www.cuny.edu/"><img style="max-width: 100%;" src="https://openlab.citytech.cuny.edu/wp-content/mu-plugins/css/images/cuny_logo.png" alt="City University of New York" border="0"></a>
								</td>

								<td valign="top" style="padding-left: 10px; font-size: 10px; font-weight: 600;">The OpenLab is an open-source, digital platform designed to support teaching and learning at City Tech (New York City College of Technology), and to promote student and faculty engagement in the intellectual and social life of the college community.</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<td style="padding: 10px; width: 100%%; font-size: <?php echo esc_attr( $settings['footer_text_size'] . 'px' ); ?>; font-family: sans-serif; mso-height-rule: exactly; line-height: <?php echo esc_attr( floor( $settings['footer_text_size'] * 1.618 ) . 'px' ) ?>; text-align: left; color: <?php echo esc_attr( $settings['footer_text_color'] ); ?>;" class="footer_text_color footer_text_size">
						<a href="http://www.citytech.cuny.edu/">New York City College of Technology</a> | <a href="https://cuny.edu">City University of New York</a>
					</td>
				</tr>
			</table>
			<!-- Email Footer : END -->

			<!--[if (gte mso 9)|(IE)]>
			</td>
			</tr>
			</table>
			<![endif]-->
		</div>
	</center>
</td></tr></table>
<?php if ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) wp_footer(); ?>
</body>
</html>
