<?php
/**
 * Email temaplte for scan results.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package broken-link-checker
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

 // phpcs:ignore
/*
 * Variables:
 * {{HEADER_LOGO_SOURCE}}
 * {{TITLE}}
 * {{SCANDATE}}
 * {{USERNAME}}
 * {{SITEURL}}
 * {{BROKEN_LINKS_COUNT}}
 * {{SUCCESSFUL_URLS_COUNT}}
 * {{UNIQUE_URLS_COUNT}}
 * {{FULL_REPORT_TITLE}}
 * {{FULL_REPORT_URL}}
 * {{COMPANY_TITLE}} // WPMU DEV
 * {{BROKEN_LINKS_LIST}}
 * {{FOOTER_TITLE}}
 * {{FOOTER_COMPANY}}
 * {{FOOTER_LOGO_SRC}}
 * {{FOOTER_SLOGAN}}
 * {{SOCIAL_LINKS}}
 * {{COMPANY_ADDRESS}}
 * {{UNSUBSCRIBE}}
 * -
 * For Broken links list
 * {{BROKEN_LINK_TITLE}}
 * {{BROKEN_LINK_STATUS}}
 * {{BROKEN_LINK_STATUS_TITLE}}
 * {{BROKEN_LINK_URL}}
*/

require_once 'email-body-markup.php';
