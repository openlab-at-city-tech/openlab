<?php
/**
 * Class Admin
 *
 * @package TEC\Common\TrustedLogin\Client
 *
 * @copyright 2021 Katz Web Services, Inc.
 */

namespace TEC\Common\TrustedLogin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WP_User;
use WP_Admin_Bar;

/**
 * Handle all the functionality that is related to the WordPress Dashboard.
 */
final class Admin {

	/**
	 * URL pointing to the "About TrustedLogin" page, shown below the Grant Access dialog
	 */
	const ABOUT_TL_URL = 'https://www.trustedlogin.com/about/easy-and-safe/';

	const ABOUT_LIVE_ACCESS_URL = 'https://www.trustedlogin.com/about/live-access/';

	/**
	 * Config object.
	 *
	 * @var Config
	 */
	private $config;

	/**
	 * SupportUser object.
	 *
	 * @var SupportUser $support_user
	 */
	private $support_user;

	/**
	 * Form object.
	 *
	 * @var Form $form
	 */
	private $form;

	/**
	 * Admin constructor.
	 *
	 * @param Config      $config Config object.
	 * @param Form        $form Form object.
	 * @param SupportUser $support_user SupportUser object.
	 */
	public function __construct( Config $config, Form $form, SupportUser $support_user ) {
		$this->config       = $config;
		$this->form         = $form;
		$this->support_user = $support_user;
	}

	/**
	 * Sets up all the admin and login hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {

		if ( is_admin() ) {
			$this->admin_init();
		}

		// Always load login hooks; it's faster than calling is_login()!
		$this->login_init();
	}

	/**
	 * Sets up all the admin hooks.
	 *
	 * @since 1.8.0
	 *
	 * @return void
	 */
	public function admin_init() {

		// @phpstan-ignore-next-line
		add_action(
			'trustedlogin/' . $this->config->ns() . '/button',
			array(
				$this->form,
				'generate_button',
			),
			10,
			2
		);

		// @phpstan-ignore-next-line
		add_action(
			'trustedlogin/' . $this->config->ns() . '/users_table',
			array(
				$this->form,
				'output_support_users',
			),
			20
		);

		add_action(
			'trustedlogin/' . $this->config->ns() . '/auth_screen',
			array(
				$this->form,
				'print_auth_screen',
			),
			20
		);

		add_filter(
			'user_row_actions',
			array(
				$this,
				'user_row_action_revoke',
			),
			10,
			2
		);
		add_action(
			'admin_bar_menu',
			array(
				$this,
				'admin_bar_add_toolbar_items',
			),
			100
		);

		if ( $this->config->get_setting( 'menu' ) ) {
			$menu_priority = $this->config->get_setting( 'menu/priority', 100 );
			add_action(
				'admin_menu',
				array(
					$this,
					'admin_menu_auth_link_page',
				),
				(int) $menu_priority
			);
		}

		if ( $this->config->get_setting( 'register_assets', true ) ) {
			add_action(
				'admin_enqueue_scripts',
				array(
					$this->form,
					'register_assets',
				)
			);
		}

		add_action(
			'trustedlogin/' . $this->config->ns() . '/admin/access_revoked',
			array(
				$this,
				'admin_notices',
			)
		);
	}

	/**
	 * Sets up all the admin hooks.
	 *
	 * @since 1.8.0
	 *
	 * @return void
	 */
	private function login_init() {

		add_action(
			'login_form_trustedlogin',
			array(
				$this->form,
				'maybe_print_request_screen',
			),
			20
		);

		if ( $this->config->get_setting( 'register_assets', true ) ) {
			add_action(
				'login_enqueue_scripts',
				array(
					$this->form,
					'register_assets',
				)
			);
		}
	}

	/**
	 * Filter: Update the actions on the users.php list for our support users.
	 *
	 * @since 1.0.0
	 *
	 * @param array   $actions     The user row actions links.
	 * @param WP_User $user_object The user object.
	 *
	 * @return array
	 */
	public function user_row_action_revoke( $actions, $user_object ) {

		if ( ! current_user_can( $this->support_user->role->get_name() ) && ! current_user_can( 'delete_users' ) ) {
			return $actions;
		}

		$revoke_url = $this->support_user->get_revoke_url( $user_object );

		if ( ! $revoke_url ) {
			return $actions;
		}

		return array(
			'revoke' => "<a class='trustedlogin tl-revoke submitdelete' href='" . esc_url( $revoke_url ) . "'>" . esc_html__( 'Revoke Access', 'trustedlogin' ) . '</a>',
		);
	}


	/**
	 * Adds a "Revoke TrustedLogin" menu item to the admin toolbar
	 *
	 * @param WP_Admin_Bar $admin_bar The admin bar object.
	 *
	 * @return void
	 */
	public function admin_bar_add_toolbar_items( $admin_bar ) {

		if ( ! current_user_can( $this->support_user->role->get_name() ) ) {
			return;
		}

		if ( ! $admin_bar instanceof WP_Admin_Bar ) {
			return;
		}

		$is_user_active = $this->support_user->is_active();

		if ( ! $is_user_active ) {
			return;
		}

		$icon = '<span style="
			height: 32px;
			width: 23px;
			margin: 0 1px;
			display: inline-block;
			vertical-align: top;
			background: url(\'data:image/svg+xml;base64,PHN2ZyBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCAyNTAgMjUwIiB2aWV3Qm94PSIwIDAgMjUwIDI1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJtLTQ0NC42IDE0LjdjLTI2LjUgMC00OC4xIDIxLjYtNDguMSA0OC4xdjM5LjhoMjAuNnYtMzkuOGMwLTE1LjIgMTIuMy0yNy41IDI3LjUtMjcuNSAxNS4xIDAgMjcuNSAxMi4zIDI3LjUgMjcuNXYzOS44aDIwLjZ2LTM5LjhjMC0yNi42LTIxLjYtNDguMS00OC4xLTQ4LjF6IiBmaWxsPSIjMTA5OWQ2Ii8+PHBhdGggZD0ibS00NDQuNiA5MGMtMzguNSAwLTY5LjcgNC44LTY5LjcgMTAuOHY3OS44YzAgMzguNSA0Ny41IDU0LjggNjkuNyA1NC44czY5LjctMTYuMyA2OS43LTU0Ljh2LTc5LjhjLS4xLTYtMzEuMy0xMC44LTY5LjctMTAuOHoiIGZpbGw9IiMxYjJiNTkiLz48cGF0aCBkPSJtLTQ0NC42IDExMC4yYy0yMyAwLTQyLjUgMTUuMy00OC45IDM2LjJoMTQuOGM1LjgtMTMuMSAxOC45LTIyLjMgMzQuMS0yMi4zIDIwLjUgMCAzNy4yIDE2LjcgMzcuMiAzNy4ycy0xNi43IDM3LjItMzcuMiAzNy4yYy0xNS4yIDAtMjguMy05LjItMzQuMS0yMi4zaC0xNC44YzYuNCAyMC45IDI1LjkgMzYuMiA0OC45IDM2LjIgMjguMiAwIDUxLjEtMjIuOSA1MS4xLTUxLjEtLjEtMjguMi0yMy01MS4xLTUxLjEtNTEuMXoiIGZpbGw9IiNmZmYiLz48cGF0aCBkPSJtLTQyNSAxNTktMjguMy0xNi40Yy0yLjItMS4zLTQtLjItNCAyLjN2OS44aC01Ni45djEzaDU2Ljl2OS44YzAgMi41IDEuOCAzLjYgNCAyLjNsMjguMy0xNi40YzIuMi0xLjEgMi4yLTMuMSAwLTQuNHoiIGZpbGw9IiNmZmYiLz48cGF0aCBkPSJtMTI1IDIuMWMtMjkuNSAwLTUzLjYgMjQtNTMuNiA1My42djQ0LjRoMjN2LTQ0LjRjMC0xNi45IDEzLjctMzAuNiAzMC42LTMwLjZzMzAuNiAxMy43IDMwLjYgMzAuNnY0NC40aDIzdi00NC40YzAtMjkuNS0yNC4xLTUzLjYtNTMuNi01My42eiIgZmlsbD0iIzEwOTlkNiIvPjxwYXRoIGQ9Im0xMjUgODZjLTQyLjggMC03Ny42IDUuNC03Ny42IDEydjg4LjhjMCA0Mi44IDUyLjkgNjEgNzcuNiA2MXM3Ny42LTE4LjIgNzcuNi02MXYtODguOGMwLTYuNi0zNC44LTEyLTc3LjYtMTJ6IiBmaWxsPSIjMWIyYjU5Ii8+PHBhdGggZD0ibTEyNSAxMDguNWMtMjUuNiAwLTQ3LjMgMTctNTQuNCA0MC4zaDE2LjRjNi40LTE0LjYgMjEtMjQuOSAzOC0yNC45IDIyLjggMCA0MS40IDE4LjYgNDEuNCA0MS40cy0xOC42IDQxLjQtNDEuNCA0MS40Yy0xNyAwLTMxLjYtMTAuMi0zOC0yNC45aC0xNi40YzcuMSAyMy4zIDI4LjggNDAuMyA1NC40IDQwLjMgMzEuNCAwIDU2LjktMjUuNSA1Ni45LTU2LjkgMC0zMS4xLTI1LjUtNTYuNy01Ni45LTU2Ljd6IiBmaWxsPSIjZmZmIi8+PHBhdGggZD0ibTE0Ni44IDE2Mi45LTMxLjYtMTguMmMtMi40LTEuNC00LjQtLjMtNC40IDIuNnYxMWgtNjMuNHYxNC41aDYzLjR2MTFjMCAyLjggMiA0IDQuNCAyLjZsMzEuNi0xOC4yYzIuNS0xLjYgMi41LTMuOSAwLTUuM3oiIGZpbGw9IiNmZmYiLz48dGV4dCB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtNjUxLjEwMjkgMzA5Ljk2MDMpIj48dHNwYW4gZmlsbD0iIzFiMmI1OSIgZm9udC1mYW1pbHk9Ik11c2VvU2Fucy05MDAiIGZvbnQtc2l6ZT0iNTIuODQ0NyIgeD0iMCIgeT0iMCI+VHJ1c3RlPC90c3Bhbj48dHNwYW4gZmlsbD0iIzFiMmI1OSIgZm9udC1mYW1pbHk9Ik11c2VvU2Fucy05MDAiIGZvbnQtc2l6ZT0iNTIuODQ0NyIgbGV0dGVyLXNwYWNpbmc9IjMiIHg9IjIwNS43IiB5PSIwIj5kPC90c3Bhbj48dHNwYW4gZmlsbD0iIzEwOTlkNiIgZm9udC1mYW1pbHk9Ik11c2VvU2Fucy01MDAiIGZvbnQtc2l6ZT0iNTIuODQ0NyIgbGV0dGVyLXNwYWNpbmc9Ii0zIiB4PSIyNDguOCIgeT0iMCI+TDwvdHNwYW4+PHRzcGFuIGZpbGw9IiMxMDk5ZDYiIGZvbnQtZmFtaWx5PSJNdXNlb1NhbnMtNTAwIiBmb250LXNpemU9IjUyLjg0NDciIHg9IjI3My40IiB5PSIwIj5vPC90c3Bhbj48dHNwYW4gZmlsbD0iIzEwOTlkNiIgZm9udC1mYW1pbHk9Ik11c2VvU2Fucy01MDAiIGZvbnQtc2l6ZT0iNTIuODQ0NyIgeD0iMzE2LjMiIHk9IjAiPmdpbjwvdHNwYW4+PC90ZXh0PjxwYXRoIGQ9Im0tNTQwLjcgNDcyLjZjLTI2LjUgMC00OC4xIDIxLjYtNDguMSA0OC4xdjM5LjhoMjAuNnYtMzkuOGMwLTE1LjIgMTIuMy0yNy41IDI3LjUtMjcuNSAxNS4xIDAgMjcuNSAxMi4zIDI3LjUgMjcuNXYzOS44aDIwLjZ2LTM5LjhjMC0yNi41LTIxLjYtNDguMS00OC4xLTQ4LjF6IiBmaWxsPSIjMTA5OWQ2Ii8+PHBhdGggZD0ibS01NDAuNyA1NDcuOWMtMzguNSAwLTY5LjcgNC44LTY5LjcgMTAuOHY3OS44YzAgMzguNSA0Ny41IDU0LjggNjkuNyA1NC44czY5LjctMTYuMyA2OS43LTU0Ljh2LTc5LjhjLS4xLTYtMzEuMy0xMC44LTY5LjctMTAuOHoiIGZpbGw9IiMxYjJiNTkiLz48cGF0aCBkPSJtLTU0MC43IDU2OC4xYy0yMyAwLTQyLjUgMTUuMy00OC45IDM2LjJoMTQuOGM1LjgtMTMuMSAxOC45LTIyLjMgMzQuMS0yMi4zIDIwLjUgMCAzNy4yIDE2LjcgMzcuMiAzNy4ycy0xNi43IDM3LjItMzcuMiAzNy4yYy0xNS4yIDAtMjguMy05LjItMzQuMS0yMi4zaC0xNC44YzYuNCAyMC45IDI1LjkgMzYuMiA0OC45IDM2LjIgMjguMiAwIDUxLjEtMjIuOSA1MS4xLTUxLjEtLjEtMjguMi0yMy01MS4xLTUxLjEtNTEuMXoiIGZpbGw9IiNmZmYiLz48cGF0aCBkPSJtLTUyMS4xIDYxNi45LTI4LjMtMTYuNGMtMi4yLTEuMy00LS4yLTQgMi4zdjkuOGgtNTYuOXYxM2g1Ni45djkuOGMwIDIuNSAxLjggMy42IDQgMi4zbDI4LjMtMTYuNGMyLjItMS4xIDIuMi0zLjEgMC00LjR6IiBmaWxsPSIjZmZmIi8+PHRleHQgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTQyNi41OTQ1IDY0OC43NTIxKSI+PHRzcGFuIGZpbGw9IiMxYjJiNTkiIGZvbnQtZmFtaWx5PSJNdXNlb1NhbnMtOTAwIiBmb250LXNpemU9IjEyMS4xNzA5IiBsZXR0ZXItc3BhY2luZz0iMSIgeD0iMCIgeT0iMCI+VFJVU1RFPC90c3Bhbj48dHNwYW4gZmlsbD0iIzFiMmI1OSIgZm9udC1mYW1pbHk9Ik11c2VvU2Fucy05MDAiIGZvbnQtc2l6ZT0iMTIxLjE3MDkiIGxldHRlci1zcGFjaW5nPSI2IiB4PSI0NzEuNyIgeT0iMCI+RDwvdHNwYW4+PHRzcGFuIGZpbGw9IiMxMDk5ZDYiIGZvbnQtZmFtaWx5PSJNdXNlb1NhbnMtNTAwIiBmb250LXNpemU9IjEyMS4xNzA5IiBsZXR0ZXItc3BhY2luZz0iLTIiIHg9IjU2OC4yIiB5PSIwIj5MPC90c3Bhbj48dHNwYW4gZmlsbD0iIzEwOTlkNiIgZm9udC1mYW1pbHk9Ik11c2VvU2Fucy01MDAiIGZvbnQtc2l6ZT0iMTIxLjE3MDkiIGxldHRlci1zcGFjaW5nPSIxIiB4PSI2MjkuNiIgeT0iMCI+T0dJTjwvdHNwYW4+PC90ZXh0Pjwvc3ZnPg==\') left center no-repeat;
			background-size: 22px 23px;
		"></span>';

		$admin_bar->add_menu(
			array(
				'id'     => 'tl-' . $this->config->ns() . '-revoke',
				'title'  => $icon . esc_html__( 'Revoke Access', 'trustedlogin' ),
				'href'   => $this->support_user->get_revoke_url( 'all' ),
				'parent' => 'top-secondary',
				'meta'   => array(
					'class' => 'tl-destroy-session',
					'title' => esc_html__( 'You are logged in as a support user. Click to permanently revoke access.', 'trustedlogin' ),
				),
			)
		);
	}

	/**
	 * Generates the auth link page
	 *
	 * This simulates the addition of an admin submenu item with null as the menu location
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function admin_menu_auth_link_page() {

		/**
		 * Get the menu slug parent from the configuration.
		 *
		 * @var string|false $parent_slug
		 */
		$parent_slug = $this->config->get_setting( 'menu/slug', null );

		// When false, there will be no menus added.
		if ( false === $parent_slug ) {
			return;
		}

		$ns = $this->config->ns();

		$menu_slug = apply_filters( 'trustedlogin/' . $this->config->ns() . '/admin/menu/menu_slug', 'grant-' . $ns . '-access' );

		$menu_title = $this->config->get_setting( 'menu/title', esc_html__( 'Grant Support Access', 'trustedlogin' ) );

		$menu_position = $this->config->get_setting( 'menu/position', null );
		$menu_position = is_null( $menu_position ) ? null : (float) $menu_position;

		// If empty (null or empty string), add top-level menu.
		if ( empty( $parent_slug ) ) {
			add_menu_page(
				$menu_title,
				$menu_title,
				'create_users',
				$menu_slug,
				array( $this->form, 'print_auth_screen' ),
				$this->config->get_setting( 'menu/icon_url', '' ),
				$menu_position
			);

			return;
		}

		add_submenu_page(
			$parent_slug,
			$menu_title,
			$menu_title,
			'create_users',
			$menu_slug,
			array( $this->form, 'print_auth_screen' ),
			$menu_position
		);
	}

	/**
	 * Add admin_notices hooks
	 *
	 * @return void
	 */
	public function admin_notices() {
		add_action( 'admin_notices', array( $this->form, 'admin_notice_revoked' ) );
	}
}
