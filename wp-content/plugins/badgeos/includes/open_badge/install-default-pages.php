<?php
/**
 * Install Default Pages.
 *
 * @package BadgeOS
 * @subpackage Open Badge
 * @author Learning Times
 * @license http://www.gnu.org/licenses/agpl.txt GNU AGPL v3.0
 */

/**
 *  Shows a notice to install/create the required open badge pages.
 */
function badgeos_ob_install_pages_if_not_installed() {

	$assertion_page = badgeos_utilities::get_option( 'badgeos_assertion_url', false );
	$json_page      = badgeos_utilities::get_option( 'badgeos_json_url', false );
	$issuer_page    = badgeos_utilities::get_option( 'badgeos_issuer_url', false );
	$evidence_page  = badgeos_utilities::get_option( 'badgeos_evidence_url', false );

	if ( current_user_can( 'manage_options' ) && ( ! badgeos_ob_is_page_exists( $assertion_page ) || ! badgeos_ob_is_page_exists( $json_page ) || ! badgeos_ob_is_page_exists( $issuer_page ) || ! badgeos_ob_is_page_exists( $evidence_page ) ) ) {
		$class       = 'notice is-dismissible error';
		$config_link = 'admin-post.php?action=badgeos_config_pages';
		printf( '<div id="message" class="%s"> <p>Please, click <a href="%s">here</a> to configure/create new open badge pages.</p></div>', esc_attr( $class ), esc_url( $config_link ) );
	}

}

add_action( 'admin_notices', 'badgeos_ob_install_pages_if_not_installed' );

/**
 * Check and create if any of the required open badge page is not found.
 */
function badgeos_ob_config_pages_func() {

	$page_created = false;

	$assertion_page = badgeos_utilities::get_option( 'badgeos_assertion_url', false );

	if ( ! badgeos_ob_is_page_exists( $assertion_page ) ) {

		$assertion_page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_name'    => 'Assertion Page',
				'post_title'   => 'Assertion Page',
				'post_content' => 'This page will display assertion json only.',
				'post_status'  => 'publish',
			)
		);

		badgeos_utilities::update_option( 'badgeos_assertion_url', $assertion_page_id );

		$page_created = true;
	}

	$json_page = badgeos_utilities::get_option( 'badgeos_json_url', false );

	if ( ! badgeos_ob_is_page_exists( $json_page ) ) {

		$json_page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_name'    => 'Badge Page',
				'post_title'   => 'Badge Page',
				'post_content' => 'This page will display badge json only.',
				'post_status'  => 'publish',
			)
		);

		badgeos_utilities::update_option( 'badgeos_json_url', $json_page_id );

		$page_created = true;
	}

	$issuer_page = badgeos_utilities::get_option( 'badgeos_issuer_url', false );

	if ( ! badgeos_ob_is_page_exists( $issuer_page ) ) {

		$issuer_page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_name'    => 'Issuer Page',
				'post_title'   => 'Issuer Page',
				'post_content' => 'This page will display issuer json only.',
				'post_status'  => 'publish',
			)
		);

		badgeos_utilities::update_option( 'badgeos_issuer_url', $issuer_page_id );

		$page_created = true;
	}

	$evidence_page = badgeos_utilities::get_option( 'badgeos_evidence_url', false );

	if ( ! badgeos_ob_is_page_exists( $evidence_page ) ) {

		$evidence_page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_name'    => 'Evidence Page',
				'post_title'   => 'Evidence Page',
				'post_content' => '[badgeos_evidence]',
				'post_status'  => 'publish',
			)
		);

		badgeos_utilities::update_option( 'badgeos_evidence_url', $evidence_page_id );

		$page_created = true;
	}

	if ( $page_created ) {
		flush_rewrite_rules();
	}

	wp_safe_redirect( 'admin.php?page=badgeos-ob-integration' );
}

add_action( 'admin_post_badgeos_config_pages', 'badgeos_ob_config_pages_func' );

/**
 * BadgeOS open badge page exists.
 *
 * @param int $page_id page id.
 * @return bool return true|false.
 */
function badgeos_ob_is_page_exists( $page_id ) {
	if ( ! empty( $page_id ) ) {
		if ( get_post_status( $page_id ) === 'publish' ) {
			return true;
		}
	}

	return false;
}
