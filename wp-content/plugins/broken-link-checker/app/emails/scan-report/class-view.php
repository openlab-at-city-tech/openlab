<?php
/**
 * View for scan reports emails. Holds specific parts for email template to use.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Emails\Scan_Report
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Emails\Scan_Report;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\App\Emails\Scan_Report\Model;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;

//use WPMUDEV_BLC\Core\Utils\Utilities;

class View extends Base {
	/**
	 * Provides the header full title including the image.
	 *
	 * @return string
	 */
	public function get_full_header_title() {
		ob_start();
		?>
        <img height="28" width="28" src="{{HEADER_LOGO_SOURCE}}"
             style="border:0;outline:none;text-decoration:none;height:28px;width:28px;font-size:13px;vertical-align:middle"
        />
        {{TITLE}}
		<?php

		return ob_get_clean();
	}

	/**
	 * Gives a table with a single row and 2 columns even in small screens. Widths are specific.
	 *
	 * @param string $title
	 * @param string $value
	 *
	 * @return string
	 */
	public function get_summary_row( string $title = '', string $value = '' ) {
		return "
        <table  align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" 
        style=\"width:100%;\">
            <tbody>
            <tr>
                <td align=\"left\" style=\"width: 70% !important;max-width: 70%;text-align:left;\">
                    <div style=\"font-family:Roboto;font-size:15px;font-weight:500;line-height:22px;text-align:left;color:#000000;\">
                        {$title}
                    </div>
                </td>
                <td align=\"right\" style=\"width: 30% !important;max-width: 30%;text-align: right;\">
                    <div>
                        {$value}
                    </div>
                    
                </td>
            </tr>
            </tbody>
        </table>
        ";
	}

	/**
	 * Gives the markup of broken links email part.
	 *
	 * @return string
	 */
	public function broken_links_list_markup() {
		$broken_links_list = Model::get_scan_results( 'broken_links_list' );
		$markup            = '';

		if ( empty( $broken_links_list ) ) {
			return $markup;
		}

		foreach ( $broken_links_list as $broken_link ) {

			if ( is_object( $broken_link ) ) {
				$broken_link = (array) $broken_link;
			}

			if ( ! empty( $broken_link['is_ignored'] ) ) {
				continue;
			}

			$origin_source    = $broken_link['origins'][0] ?? false;
			$origin_source_id = null;

			if ( $origin_source ) {
				$origin_source_id = intval( url_to_postid( $origin_source ) );
			}

			$origin_post_title    = $origin_source_id ? get_the_title( $origin_source_id ) : __( 'Unknown', 'broken-link-checker' );
			$origin_post_edit_url = get_edit_post_link( $origin_source_id );

			// If get_edit_post_link keeps returning empty.
			if ( empty( $origin_post_edit_url ) ) {
				$post = get_post( $origin_source_id );

				if ( $post instanceof \WP_Post ) {
					$post_type_object = get_post_type_object( $post->post_type );

					if ( ! $post_type_object ) {
						continue;
					}

					$origin_post_edit_url = admin_url( sprintf( $post_type_object->_edit_link . "&action=edit", $post->ID ) );
				} else {
					continue;
				}
			}

			ob_start();
			?>
            <tr style="border: 1px solid #F2F2F2;">
                <td style="font-family: Roboto, sans-serif;font-weight: 500;padding: 20px; font-size: 12px;
                line-height: 14px;color: #286EFA; vertical-align:top; text-align: left;">
                    <a href="<?php echo $broken_link['url']; ?>" style="color: #286EFA;">
						<?php echo $broken_link['url']; ?>
                    </a>
                </td>
                <td style="font-family: Roboto, sans-serif;font-weight: 500;padding: 20px; font-size: 12px;
                line-height: 14px;color: #1A1A1A; width:33%;  vertical-align:top; text-align: left;">

                    <table>
                        <tr>
                            <td style="min-width: 50px;">
                                <span style="padding: 4px 8px;width: 36px;height: 22px;background: #FF6D6D;border-radius: 12px;font-weight: 500;font-size: 12px;line-height: 14px;color: #FFFFFF; margin:0 4px 0 0;">
                                    <?php echo $broken_link['status_code']; ?>
                                </span>
                            </td>
                            <td style="min-width: 50px;">
								<?php echo $broken_link['status_ref']; ?>
                            </td>
                        </tr>
                    </table>

                </td>
                <td style="font-family: Roboto, sans-serif;font-weight: 500;padding: 20px; font-size: 12px;line-height: 14px;color: #286EFA; width:33%; vertical-align:top; text-align: left;">
                    <a href="<?php echo $origin_post_edit_url; ?>" target="_blank" style="color: #286EFA;">
						<?php echo $origin_post_title; ?>
                    </a>
                </td>
            </tr>
			<?php
			$markup .= ob_get_clean();
		}

		return "<div style=\"overflow-x: auto;\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\" style=\"color:#000000;
        #font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:22px;table-layout:auto;
        #width:100%;border:none;min-width:400px;\">
        <tr style=\"align-items: flex-start;padding: 0px;width: 550px;height: 32px;\">
            <th style=\"box-shadow: -1px 0px 0px 0px #f2f2f2; -webkit-box-shadow: -1px 0px 0px 0px #f2f2f2; -moz-box-shadow: -1px 0px 0px 0px #f2f2f2; background: #F2F2F2;font-family: Roboto, sans-serif;font-weight: 600;padding: 10px 20px;font-size: 12px;line-height: 14px;color: #1A1A1A; text-align:left;border-radius: 4px 0px 0px 0px;min-width: 80px;\">
                " . esc_html__( 'Broken Links', 'broken-link-checker' ) . "
            </th>
            <th style=\"background: #F2F2F2;font-family: Roboto, sans-serif;font-weight: 600;padding: 10px 20px;font-size: 12px;line-height: 14px;color: #1A1A1A;text-align:left;\">
                " . esc_html__( 'Status', 'broken-link-checker' ) . "
            </th>
            <th style=\"background: #F2F2F2;font-family: Roboto, sans-serif;font-weight: 600;padding: 10px 20px;font-size: 12px;line-height: 14px;color: #1A1A1A;text-align:left;border-top-right-radius:4px;min-width: 80px;\">
                " . esc_html__( 'Source URL', 'broken-link-checker' ) . "
            </th>
          </tr>
        {$markup}
        </table></div>";
	}

	/**
	 * Return the footer content.
	 *
	 * @return string
	 */
	public function get_footer_content() {
		$footer_slogan_img_url = WPMUDEV_BLC_ASSETS_URL . 'images/footer-slogan.png';

		ob_start();
		?>
        <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:center;
        color:#000000; width: 100%;">
            <table style="width:100%;">
                <tbody>
                <tr>
                    <td align="center">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                               style="border-collapse:collapse;border-spacing:0px">
                            <tbody>
                            <tr>
                                <td style="width:168px">
                                    <a href="https://wpmudev.com" title="{{LINK_TO_WPMUDEV_HOME}}" target="_blank"
                                       data-saferedirecturl="https://wpmudev.com">
                                        <img height="auto" src="<?php echo $footer_slogan_img_url; ?>" style="border:0;
                            display:block;outline:none; text-decoration:none;height:auto;width:100%;font-size:13px"
                                             width="168">
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>


        </div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Returns the social links for the email footer.
	 */
	public function get_social_links() {
		$social_data = Model::social_links();
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
