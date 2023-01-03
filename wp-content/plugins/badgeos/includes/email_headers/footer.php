<?php
/**
 * Email Headers - BadgeOS Email Footer.
 *
 * @package BadgeOS
 */

$badgeos_admin_tools                   = ! empty( badgeos_utilities::get_option( 'badgeos_admin_tools' ) ) ? badgeos_utilities::get_option( 'badgeos_admin_tools' ) : array();
$email_general_footer_background_color = ! empty( $badgeos_admin_tools['email_general_footer_background_color'] ) ? $badgeos_admin_tools['email_general_footer_background_color'] : '#ffffff';
$email_general_footer_text_color       = ! empty( $badgeos_admin_tools['email_general_footer_text_color'] ) ? $badgeos_admin_tools['email_general_footer_text_color'] : '#000000';
$allow_unsubscribe_email               = ! empty( $badgeos_admin_tools['allow_unsubscribe_email'] ) ? $badgeos_admin_tools['allow_unsubscribe_email'] : 'No';
$unsubscribe_email_page                = ! empty( $badgeos_admin_tools['unsubscribe_email_page'] ) ? get_permalink( $badgeos_admin_tools['unsubscribe_email_page'] ) : site_url();

$unsubcribe_url      = add_query_arg(
	array(
		'action'  => 'badgeos_unsubscribe_email',
		'user_id' => absint( $user_id ),
	),
	$unsubscribe_email_page
);
$badgeos_admin_tools = ! empty( badgeos_utilities::get_option( 'badgeos_admin_tools' ) ) ? badgeos_utilities::get_option( 'badgeos_admin_tools' ) : array();
?>
<br>
	</td>
		</tr>
		<!-- END MAIN CONTENT AREA -->
		</table>
			<!-- START FOOTER -->
			<div class="footer" style="background-color:<?php echo esc_attr( $email_general_footer_background_color ); ?>;clear: both; text-align: center; width: 100%;color: <?php echo esc_attr( $email_general_footer_text_color ); ?>;"> 
				<div style="padding:10px"><?php echo esc_attr( $badgeos_admin_tools['email_general_footer_text'] ); ?></div>
				<?php if ( 'Yes' === trim( $allow_unsubscribe_email ) ) { ?>
				<div style="padding:10px;color:#aaa;font-size:12px;"><?php esc_attr_e( 'If you wish to unsubscribe, please click', 'badgeos' ); ?> <a style="color:#aaa" href="<?php echo esc_url( $unsubcribe_url ); ?>"><?php esc_attr_e( 'here', 'badgeos' ); ?></a></div>
				<?php } ?>
			</div>
			<!-- END FOOTER -->

		<!-- END CENTERED WHITE CONTAINER -->
		</div> 
		</td>
		<td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
	</tr>
	</table>
</body>
</html>
