<?php
/**
 * The Emails model for Recipient activation.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Emails\Recipient_Activation;
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Emails\Recipient_Activation;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Utilities;

/**
 * Class Settings
 *
 * @package WPMUDEV_BLC\App\Emails\Recipient_Activation
 */
class Model {
	/**
	 * Returns the header logo of the email.
	 *
	 * @return string
	 */
	public static function header_logo() {
		return apply_filters(
			'wpmudev_blc_scan_report_email_header_logo',
			esc_url( WPMUDEV_BLC_ASSETS_URL . 'images/blc-logo-white-28x28.png' )
		);
	}

	/**
	 * Returns the BLC Title to be used in the email header.
	 *
	 * @return string
	 */
	public static function header_title() {
		return apply_filters(
			'wpmudev_blc_scan_report_email_header_title',
			__( 'Broken Link Notification', 'brocken-link-checker' )
		);
	}

	/**
	 * Returns home url.
	 *
	 * @retun string
	 */
	public static function get_hub_home_url() {
		return Utilities::hub_home_url();
	}

	/**
	 * Returns the header logo of the email.
	 *
	 * @return string
	 */
	public static function footer_logo() {
		return apply_filters(
			'wpmudev_blc_scan_report_email_header_logo',
			esc_url( WPMUDEV_BLC_ASSETS_URL . 'images/footer-slogan.png' )
		);
	}

	/**
	 * Returns social links info.
	 *
	 * @return array
	 */
	public static function social_links() {
		return apply_filters(
			'wpmudev_blc_scan_report_email_social_data',
			array(
				'facebook'  => array(
					'icon' => WPMUDEV_BLC_ASSETS_URL . 'images/social/facebook-dark-7x14.png',
					'url'  => 'https://www.facebook.com/wpmudev',
				),
				'instagram' => array(
					'icon' => WPMUDEV_BLC_ASSETS_URL . 'images/social/instagram-dark-14x14.png',
					'url'  => 'https://www.instagram.com/wpmu_dev/',
				),
				'twitter'   => array(
					'icon' => WPMUDEV_BLC_ASSETS_URL . 'images/social/twitter-dark-13x11.png',
					'url'  => 'https://twitter.com/wpmudev/',
				),
			)
		);
	}

	/**
	 * Returns the social links for the email footer.
	 */
	public static function get_social_links() {
		$social_data = self::social_links();
		$output      = '';

		if ( ! empty( $social_data ) ) {
			$output .= '<tr>';
			$output .= '<td><span style="font-weight: 700;font-size: 13px;">' . esc_html__( 'Follow us', 'broken-link-checker' ) . '</span></td>';

			foreach ( $social_data as $key => $data ) {
				$url    = $data['url'];
				$icon   = $data['icon'];
				$output .= "<td>
                    <a href=\"{$url}\" target=\"_blank\">
                        <img height=\"13\" src=\"{$icon}\" style=\"border-radius:3px;display:block;max-height:13px;margin-left: 10px;\" />
                    </a>
				</td>
			";
			}

			$output .= '<tr>';
		}

		return "<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"float:none;display:inline-table;\">{$output}</table>";
	}

}
