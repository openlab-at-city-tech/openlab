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
use WPMUDEV_BLC\Core\Utils\Utilities;
use WP_Post;
use WP_User;

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

		if ( empty( $broken_links_list ) || ! is_array( $broken_links_list ) ) {
			return $markup;
		}

		foreach ( $broken_links_list as $broken_link ) {

			if ( is_object( $broken_link ) ) {
				$broken_link = (array) $broken_link;
			}

			if ( ! is_array( $broken_link ) || empty( $broken_link['url'] ) ) {
				Utilities::log( 'BLC_SCAN_REPORT_EMAIL - Skipped a broken link entry because it holds no url.' );

				continue;
			}

			if ( ! empty( $broken_link['is_ignored'] ) ) {
				continue;
			}

			$status_code = isset( $broken_link['status_code'] ) ? trim( (string) $broken_link['status_code'] ) : '';
			$status_ref  = isset( $broken_link['status_ref'] ) ? trim( (string) $broken_link['status_ref'] ) : '';
			$origin_cell = $this->origin_markup( $broken_link );

			ob_start();
			?>
            <tr style="border: 1px solid #F2F2F2;">
                <td style="font-family: Roboto, sans-serif;font-weight: 500;padding: 20px; font-size: 12px;
                line-height: 14px;color: #286EFA; vertical-align:top; text-align: left;">
                    <a href="<?php echo esc_url( $broken_link['url'] ); ?>" style="color: #286EFA;">
						<?php echo esc_html( $broken_link['url'] ); ?>
                    </a>
                </td>
                <td style="font-family: roboto, sans-serif;font-weight: 500;padding: 20px; font-size: 12px;
                line-height: 14px;color: #1A1A1A; width:33%;  vertical-align:top; text-align: left;">

                    <table>
                        <tr>
							<?php if ( '' !== $status_code ) : ?>
                                <td style="min-width: 50px;">
                                <span style="padding: 4px 8px;width: 36px;height: 22px;background: #FF6D6D;border-radius: 12px;font-weight: 500;font-size: 12px;line-height: 14px;color: #FFFFFF; margin:0 4px 0 0;">
                                    <?php echo esc_html( $status_code ); ?>
                                </span>
                                </td>
							<?php endif; ?>
                            <td style="min-width: 50px;">
								<?php echo esc_html( $status_ref ); ?>
                            </td>
                        </tr>
                    </table>

                </td>
                <td style="font-family: roboto, sans-serif;font-weight: 500;padding: 20px; font-size: 12px;line-height: 14px;color: #286EFA; width:33%; vertical-align:top; text-align: left;">
					<?php echo $origin_cell; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in origin_markup(). ?>
                </td>
            </tr>
			<?php
			$markup .= ob_get_clean();
		}

		// The heading row below is unconditional, so returning it without any
		// data row would render a table holding nothing but column labels.
		if ( empty( $markup ) ) {
			return '';
		}

		return "<div style=\"overflow-x: auto;\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\" style=\"color:#000000;
        #font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:22px;table-layout:auto;
        #width:100%;border:none;min-width:400px;\">
        <tr style=\"align-items: flex-start;padding: 0px;width: 550px;height: 32px;\">
            <th style=\"box-shadow: -1px 0px 0px 0px #f2f2f2; -webkit-box-shadow: -1px 0px 0px 0px #f2f2f2; -moz-box-shadow: -1px 0px 0px 0px #f2f2f2; background: #F2F2F2;font-family: roboto, sans-serif;font-weight: 600;padding: 10px 20px;font-size: 12px;line-height: 14px;color: #1A1A1A; text-align:left;border-radius: 4px 0px 0px 0px;min-width: 80px;\">
                " . esc_html__( 'Broken Links', 'broken-link-checker' ) . "
            </th>
            <th style=\"background: #F2F2F2;font-family: roboto, sans-serif;font-weight: 600;padding: 10px 20px;font-size: 12px;line-height: 14px;color: #1A1A1A;text-align:left;\">
                " . esc_html__( 'Status', 'broken-link-checker' ) . "
            </th>
            <th style=\"background: #F2F2F2;font-family: roboto, sans-serif;font-weight: 600;padding: 10px 20px;font-size: 12px;line-height: 14px;color: #1A1A1A;text-align:left;border-top-right-radius:4px;min-width: 80px;\">
                " . esc_html__( 'Source URL', 'broken-link-checker' ) . "
            </th>
          </tr>
        {$markup}
        </table></div>";
	}

	/**
	 * Gives the markup of the Source URL cell of a broken link row.
	 *
	 * A broken link is never dropped from the report because we could not tie its
	 * origin back to a local post. We link to the best target we can resolve and
	 * fall back to naming the origin as plain text.
	 *
	 * @param array $broken_link A single broken link record of the scan results.
	 *
	 * @return string
	 */
	protected function origin_markup( array $broken_link = array() ) {
		$origin_source = $this->first_origin( $broken_link );

		if ( empty( $origin_source ) ) {
			Utilities::log(
				sprintf(
					'BLC_SCAN_REPORT_EMAIL - Broken link %s holds no origin. Reporting it without a source.',
					$broken_link['url'] ?? ''
				)
			);

			return $this->origin_label_markup( __( 'Unknown', 'broken-link-checker' ) );
		}

		$origin_source_id = intval( url_to_postid( $origin_source ) );

		if ( ! empty( $origin_source_id ) ) {
			$edit_url = $this->post_edit_url( $origin_source_id );

			if ( ! empty( $edit_url ) ) {
				$origin_post_title = get_the_title( $origin_source_id );

				if ( '' === trim( (string) $origin_post_title ) ) {
					$origin_post_title = $origin_source;
				}

				return $this->origin_link_markup( $edit_url, $origin_post_title );
			}
		}

		// Author archives never resolve through url_to_postid().
		$author = Utilities::is_author_url( $origin_source );

		if ( $author instanceof WP_User ) {
			return $this->origin_link_markup( $origin_source, $author->display_name );
		}

		/*
		 * Home page, term or date archives, and permalinks we cannot map to a post.
		 * The origin is still worth reporting, so we point at the page itself.
		 */
		Utilities::log(
			sprintf(
				'BLC_SCAN_REPORT_EMAIL - Origin %s did not resolve to a post. Reporting it as a plain url.',
				$origin_source
			)
		);

		return $this->origin_link_markup( $origin_source, $origin_source );
	}

	/**
	 * Returns the first usable origin url of a broken link.
	 *
	 * Origins reach us from the Hub API, so we accept a list of urls, a list of
	 * objects or arrays holding a url, and a bare url string.
	 *
	 * @param array $broken_link A single broken link record of the scan results.
	 *
	 * @return string
	 */
	protected function first_origin( array $broken_link = array() ) {
		$origins = $broken_link['origins'] ?? array();

		if ( is_string( $origins ) ) {
			$origins = array( $origins );
		}

		if ( is_object( $origins ) ) {
			$origins = (array) $origins;
		}

		if ( ! is_array( $origins ) ) {
			return '';
		}

		foreach ( $origins as $origin ) {
			if ( is_object( $origin ) ) {
				$origin = (array) $origin;
			}

			if ( is_array( $origin ) ) {
				$origin = $origin['url'] ?? '';
			}

			if ( is_string( $origin ) && '' !== trim( $origin ) ) {
				return trim( $origin );
			}
		}

		return '';
	}

	/**
	 * Returns the edit link of a post.
	 *
	 * The get_edit_post_link() function returns null whenever the current user
	 * cannot edit the post, and a scheduled report is always sent without a user
	 * in context. So we build the link ourselves in that case.
	 *
	 * @param int $post_id The post to build the edit link of.
	 *
	 * @return string
	 */
	protected function post_edit_url( int $post_id = 0 ) {
		if ( empty( $post_id ) ) {
			return '';
		}

		$edit_url = get_edit_post_link( $post_id );

		if ( ! empty( $edit_url ) ) {
			return $edit_url;
		}

		$post = get_post( $post_id );

		if ( ! $post instanceof WP_Post ) {
			return '';
		}

		$post_type_object = get_post_type_object( $post->post_type );

		if ( ! $post_type_object || empty( $post_type_object->_edit_link ) ) {
			return '';
		}

		return admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', $post->ID ) );
	}

	/**
	 * Wraps an origin in a link.
	 *
	 * @param string $url   The link target.
	 * @param string $label The text to show.
	 *
	 * @return string
	 */
	protected function origin_link_markup( string $url = '', string $label = '' ) {
		return sprintf(
			'<a href="%1$s" target="_blank" style="color: #286EFA;">%2$s</a>',
			esc_url( $url ),
			esc_html( $label )
		);
	}

	/**
	 * Wraps an origin in plain text, for when there is nothing to link to.
	 *
	 * @param string $label The text to show.
	 *
	 * @return string
	 */
	protected function origin_label_markup( string $label = '' ) {
		return sprintf(
			'<span style="color: #1A1A1A;">%s</span>',
			esc_html( $label )
		);
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




	public function review_box_markup( string $review_content = null ) {
		return '<!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:550px;border-radius: 8px" width="550" bgcolor="#FFFFFF" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
    <div style="background:#FFFFFF;background-color:#FFFFFF;margin:0px auto;max-width:600px;">
      <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#FFFFFF;background-color:#FFFFFF;width:100%;">
        <tbody>
          <tr>
            <td style="border:none;direction:ltr;font-size:0px;padding:20px;text-align:center;">
                <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="email-review-prompt-outlook" width="550px" ><table align="center" border="0" cellpadding="0" cellspacing="0" class="email-review-prompt-outlook" role="presentation" style="width:550px;" width="550" bgcolor="#DAF4EF" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                <div class="email-review-prompt" style="background:#DAF4EF; background-color:#DAF4EF; margin:0px auto; border-radius: 8px; max-width:550px; padding:20px 25px 20px 25px; vertical-align: top;">
                    <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                        <tr>
                            <td>
                                   <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
                                        <tr>
                                            <td style="border:none;direction:ltr;font-size:0px;padding:0px 0px 0px 0px;text-align:left;width:50px; vertical-align: top;">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td align="left" style="padding:0px; padding-top:0px; word-break:break-word;vertical-align: top;">
                                                                <div style="vertical-align: top;">{{REVIEW_ICON}}</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                            <td style="border:none;direction:ltr;font-size:0px;padding:0px 0px 0px 0px;text-align:left;vertical-align: top;">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%">
                                                        <tbody>
                                                          <tr>
                                                            <td align="left" style="font-size:0px;padding:0px 0px 0px 20px;padding-top:0px;word-break:break-word;vertical-align: top;">
                                                              <div style="font-family:Roboto;font-size:14px;font-weight:400;line-height:1.7;text-align:left;color:#000000;vertical-align: top;">{{REVIEW_CONTENT}}</div>
                                                            </td>
                                                          </tr>
                                                        </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                   </table>
                            </td>
                        </tr>
                    </table>        
                </div>
                <!--[if mso | IE]></td></tr></table></td></tr></table><![endif]-->

            </td>
          </tr>
        </tbody>
      </table>
    </div>';
	}

    public function review_box_icon() {
	    $icon_src = WPMUDEV_BLC_ASSETS_URL . 'images/email-review-prompt-icon.png';

        return '<img height="auto" src="' . $icon_src . '" style="border:0;text-decoration:none;height:auto;width:50px;font-size:13px" width="168">';
    }
}
