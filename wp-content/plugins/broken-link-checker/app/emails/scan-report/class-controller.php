<?php
/**
 * Controller for scan reports emails.
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

use WPMUDEV_BLC\Core\Controllers\Mailer;
use WPMUDEV_BLC\App\Emails\Scan_Report\Model;
use WPMUDEV_BLC\Core\Utils\Utilities;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Emails\Scan_Report
 */
class Controller extends Mailer {
	/**
	 * Module name. It might be useful in hooks.
	 *
	 * @var string
	 */
	public $email_module_name = 'scan_report';

	/**
	 * WP Cron interval.
	 *
	 * @var boolean
	 */
	protected $cron_generate_interval = false;

	/**
	 * Define if scheduled events ( wp pseudo cron ) can be used for emails.
	 *
	 * @var bool
	 */
	protected $use_cron = false;

	/**
	 * Sends the BLC scan report to recipients.
	 * Called by WPMUDEV_BLC\App\Hub_Endpoints\Set_Data\Controller
	 * @return void
	 */
	public function send_email() {
		$site_name             = get_bloginfo( 'name' );
		$recipients            = Model::get_recipients();
		$broken_links_count    = Model::get_scan_results( 'broken_links' );
		$recipients_collection = array();

		$this->body_variables =
			apply_filters(
				'wpmudev_blc_scan_report_email_vars',
				array(
					'{{HEADER_FULL_TITLE}}'        => View::instance()->get_full_header_title(),
					'{{HEADER_LOGO_SOURCE}}'       => esc_html( Model::header_logo() ),
					'{{TITLE}}'                    => esc_html( Model::header_title() ),
					'{{SITENAME}}'                 => $site_name,
					'{{HEADING_START}}'            => esc_html__( 'Broken Link Report for', 'broken-link-checker' ),
					'{{GREETING}}'                 => esc_html__( 'Hi {{USERNAME}}', 'broken-link-checker' ),
					'{{USERNAME}}'                 => '',
					'{{CONTENT_MENTION_SUMMARY}}'  => esc_html__( 'Here\'s your latest broken link summary generated on {{SCANDATE}}.', 'broken-link-checker' ),
					'{{SITEURL}}'                  => site_url(),
					'{{SCANDATE}}'                 => esc_html( Model::scan_date() ),
					// SUMMARY PART
					'{{SUMMARY_TITLE}}'            => esc_html__( 'Summary', 'broken-link-checker' ),
					'{{SUMMARY_INTRO}}'            => esc_html__( 'Here are your latest broken link test results.', 'broken-link-checker' ),
					'{{SUMMARY_ROW_ONE}}'          => View::instance()->get_summary_row( '{{SUMMARY_BROKEN_LINKS_LBL}}',
						$this->get_broken_links_count_markup() ),
					'{{SUMMARY_BROKEN_LINKS_LBL}}' => esc_html__( 'Broken Links', 'broken-link-checker' ),
					'{{BROKEN_LINKS_COUNT}}'       => $broken_links_count,
					'{{BROKEN_LINKS_COUNT_COLOR}}' => $broken_links_count > 0 ? '#FF6D6D' : '#1ABC9C',
					'{{SUMMARY_ROW_TWO}}'          => View::instance()->get_summary_row( '{{SUMMARY_TOTAL_URLS_LBL}}', '{{TOTAL_URLS_COUNT}}' ),
					//'{{SUMMARY_SUCCESSFUL_URLS_LBL}}' => esc_html__( 'Successful URLs', 'broken-link-checker' ),
					//'{{SUCCESSFUL_URLS_COUNT}}'       => Model::get_scan_results( 'succeeded_urls' ),
					'{{SUMMARY_TOTAL_URLS_LBL}}'   => esc_html__( 'Total Links', 'broken-link-checker' ),
					'{{TOTAL_URLS_COUNT}}'         => Model::get_scan_results( 'total_urls' ),
					'{{SUMMARY_ROW_THREE}}'        => View::instance()->get_summary_row( '{{SUMMARY_UNIQUE_URLS_LBL}}', '{{UNIQUE_URLS_COUNT}}' ),
					'{{SUMMARY_UNIQUE_URLS_LBL}}'  => esc_html__( 'Unique URLs', 'broken-link-checker' ),
					'{{UNIQUE_URLS_COUNT}}'        => Model::get_scan_results( 'unique_urls' ),
					// REPORT PART
					'{{REPORT_PADDING}}'           => $broken_links_count > 0 ? '25px' : '0',
					'{{REPORT_LIST_PADDING}}'      => $broken_links_count > 0 ? '8px' : '0',
					'{{REPORT_BTN_PADDING}}'       => $broken_links_count > 0 ? '20px' : '0',
					'{{REPORT_TITLE}}'             => $broken_links_count > 0 ? esc_html__( 'Broken link report', 'broken-link-checker' ) : '',
					'{{REPORT_DESCRIPTION}}'       => $broken_links_count > 0 ? esc_html__( 'The list below shows a maximum of 20 broken links. Click the View Full Report button to see the full list.', 'broken-link-checker' ) : '',
					'{{REPORT_BTN_URL}}'           => esc_url( Model::get_hub_home_url() ),
					'{{REPORT_BTN_TITLE}}'         => $broken_links_count > 0 ? esc_html__( 'View Full Report', 'broken-link-checker' ) : esc_html__( 'View Report', 'broken-link-checker' ),
					'{{BROKEN_LINKS_LIST}}'        => $broken_links_count > 0 ? View::instance()->broken_links_list_markup() : '',
					// FOOTER PART
					'{{FOOTER_TITLE}}'             => esc_html__( 'Broken Link Checker', 'broken-link-checker' ),
					'{{FOOTER_COMPANY}}'           => 'WPMU DEV',
					//$site_name,
					'{{FOOTER_CONTENT}}'           => View::instance()->get_footer_content(),
					'{{FOOTER_LOGO_SOURCE}}'       => Model::footer_logo(),
					'{{LINK_TO_WPMUDEV_HOME}}'     => esc_html__( 'Link to WPMU DEV Home page', 'broken-link-checker' ),
					'{{FOOTER_SLOGAN}}'            => esc_html__( 'Build A Better WordPress Business', 'broken-link-checker' ),
					'{{COMPANY_ADDRESS}}'          => esc_html__( 'INCSUB PO BOX 163, ALBERT PARK, VICTORIA.3206 AUSTRALIA', 'broken-link-checker' ),
					'{{COMPANY_TITLE}}'            => $site_name,
					'{{UNSUBSCRIBE}}'              => esc_html__( 'Unsubscribe', 'broken-link-checker' ),
					'{{UNSUBSCRIBE_LINK}}'         => '',
					'{{SOCIAL_LINKS}}'             => View::instance()->get_social_links(),
					'{{REVIEW_BOX}}'               => '',
				),
				$this
			);

		if ( ! empty( $recipients ) && apply_filters( 'wpmudev_blc_can_send_scan_report', true, $recipients, $broken_links_count, Model::instance() ) ) {
			foreach ( $recipients as $recipient ) {
				$this->body_variables['{{USERNAME}}']         = $recipient['name'] ?? '';
				$this->body_variables['{{UNSUBSCRIBE_LINK}}'] = $recipient['unsubscribe_link'] ?? '';
				$this->body_variables['{{REVIEW_BOX}}']       = '';
				$this->body_variables['{{REVIEW_CONTENT}}']  = '';
				$this->body_variables['{{REVIEW_ICON}}']  = '';
				$recipient_collection                         = array(
					'name'  => $recipient['name'] ?? '',
					'email' => $recipient['email'] ?? '',
				);

				if ( ! empty( $recipient[ 'review_link' ] ) ) {
					$review_content = sprintf(
					/* translators: 1: opening <a> tag 2: closing </a> tag */
						esc_html__( 'Are you enjoying the new Broken Link Checker? Share your valuable feedback on wordpress.org to help us further enhance and support our plugin. Give us feedback %shere%s.', 'broken-link-checker' ),
						'<a href="' . $recipient[ 'review_link' ] .'">',
						'</a>'
					);
					$this->body_variables['{{REVIEW_BOX}}'] = View::instance()->review_box_markup( $review_content );
					$this->body_variables['{{REVIEW_CONTENT}}'] = $review_content;
					$this->body_variables['{{REVIEW_ICON}}'] = View::instance()->review_box_icon();
					//email-review-prompt
				}

				$recipient_collection    = array_merge( $recipient_collection, $this->body_variables );
				$recipients_collection[] = $recipient_collection;
			}

			$this->send_multiple(
				$recipients_collection,
				true
			);
		}

	}

	/**
	 * Returns the markup for the Broken Links count value.
	 *
	 * @return string
	 */
	protected function get_broken_links_count_markup() {
		return "
        <div style=\"font-family:Roboto;font-size:22px;font-weight:700;line-height:22px;text-align:left;color:{{BROKEN_LINKS_COUNT_COLOR}};\">
            <div style=\"text-align: right;\">{{BROKEN_LINKS_COUNT}}</div>
        </div>
        ";
	}

	/**
	 * Sets required vars. In parent class it is an abstract method.
	 *
	 * @return void
	 */
	protected function prepare_vars() {
		$site_name           = get_bloginfo( 'name' );
		$site_email          = get_bloginfo( 'admin_email' );
		$this->email_headers = array(
			'Content-Type: text/html; charset=UTF-8',
			"From: {$site_name} <{$site_email}> \r\n",
		);
		$this->email_subject = esc_html__( 'Broken links reports', 'broken-link-checker' );
	}

}
