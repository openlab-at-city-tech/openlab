<?php
/**
 * BLC Cloud_Submenu admin page
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Pages\Cloud_Submenu
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Pages\Cloud_Submenu;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Controllers\Admin_Page;
use WPMUDEV_BLC\Core\Traits\Escape;
use WPMUDEV_BLC\Core\Utils\Utilities;
use WPMUDEV_BLC\Core\Traits\Dashboard_API;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Admin_Pages\Cloud_Submenu
 */
class Controller extends Admin_Page {
	/**
	 * Use the Escape and Dashboard_API Traits.
	 *
	 * @since 2.0.0
	 */
	use Escape, Dashboard_API;

	/**
	 * The Admin Page's Menu Type.
	 *
	 * @since 2.0.0
	 * @var bool $is_submenu Set to true if page uses submenu.
	 *
	 */
	protected $is_submenu = true;

	/**
	 * The Admin SubPage's Parent Slug.
	 *
	 * @since 2.0.0
	 * @var string $parent_slug The slug of the parent admin menu.
	 *
	 */
	protected $parent_slug = 'blc_dash';

	/**
	 * Prepares the properties of the Admin Page.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function prepare_props() {
		add_action( 'admin_enqueue_scripts', array( $this, 'menu_tag_style' ) );
		add_action( 'admin_menu', array( $this, 'admin_submenu_actions' ) );

		$this->is_submenu = true;
		$this->unique_id  = Utilities::get_unique_id();
		$this->page_title = __( 'Broken Link Checker', 'broken-link-checker' );
		$this->menu_title = __( 'Cloud [new]', 'broken-link-checker' );
		/*
		$this->menu_title = sprintf(
			__( 'Cloud [new] %sBeta%s', 'broken-link-checker' ),
			'<span class="awaiting-mod blc-beta-tag">',
			'</span>'
		);
		*/

		$this->capability = 'manage_options';
		$this->menu_slug  = 'blc_dash';
		$this->position   = 0;
	}

	public function admin_submenu_actions() {
		global $submenu;

		if ( isset( $submenu[ $this->parent_slug ] ) ) {
			remove_submenu_page( $this->parent_slug, $this->parent_slug );
		}
	}

	/**
	 * Admin Menu Callback.
	 *
	 * @since 1.0.0
	 * @return void The callback function of the Admin Menu Page.
	 */
	public function output() {
		// Output handled by Dashboard page since pages share common slug.
	}

	public function menu_tag_style() {
		$style_handler = 'blc_main_menu_tag';

		// Not using `@import url(https://fonts.bunny.net/css?family=roboto:100,500);` since 2.0.1 so that it will use default font family.
		$style_data    = "
		#adminmenu .awaiting-mod.blc-new-tag, 
		#adminmenu .menu-counter.blc-new-tag, 
		#adminmenu .update-plugins.blc-new-tag,
		#adminmenu li a.wp-has-current-submenu .update-plugins.blc-new-tag, 
		#adminmenu li.current a .awaiting-mod.blc-new-tag,
		#adminmenu .awaiting-mod.blc-beta-tag, 
		#adminmenu .menu-counter.blc-beta-tag, 
		#adminmenu .update-plugins.blc-beta-tag,
		#adminmenu li a.wp-has-current-submenu .update-plugins.blc-beta-tag, 
		#adminmenu li.current a .awaiting-mod.blc-beta-tag{
			/*padding: 1.8px 8px;*/
			padding-top: 1.2px;
			position: relative;
			background: #18BB4B;
			border-radius: 12px;
			font-style: normal;
			font-weight: 400;
			font-size: 9px;
			line-height: 12px;
			text-align: center;
			letter-spacing: -0.1px;
			color: #FFFFFF;
			flex: none;
			text-transform: uppercase;
			font-family: 'Roboto', sans-serif;
			display: inline-block;
			height: 16px;
			width: 38px;
			text-align: center;
		}
		
		#adminmenu .awaiting-mod.blc-beta-tag, 
		#adminmenu .menu-counter.blc-beta-tag, 
		#adminmenu .update-plugins.blc-beta-tag,
		#adminmenu li a.wp-has-current-submenu .update-plugins.blc-beta-tag, 
		#adminmenu li.current a .awaiting-mod.blc-beta-tag{
			background: none;
			border: 1px solid #FFFFFF;
		}
		";

		$style_data    = str_replace( "\n", "", $style_data );
		$style_data    = str_replace( "\t", "", $style_data );

		wp_register_style( $style_handler, false );
		wp_enqueue_style( $style_handler );
		wp_add_inline_style(
			$style_handler,
			$style_data
		);
	}

}
