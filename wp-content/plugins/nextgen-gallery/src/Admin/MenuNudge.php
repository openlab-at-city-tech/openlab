<?php
/**
 * Menu Nudge class.
 *
 * @since 1.8.1
 *
 * @package Envira_Gallery
 * @author  Envira Gallery Team
 */

namespace Imagely\NGG\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Menu Nudge class.
 *
 * @since 3.59.4
 */
class MenuNudge {
	/**
	 * Holds base singleton.
	 *
	 * @since 3.5.0
	 *
	 * @var object
	 */
	public $base = null;

	/**
	 * Class Hooks
	 *
	 * @since 3.5.0
	 *
	 * @return void
	 */
	public function hooks() {
		// Admin menu ToolTip.
		add_action( 'adminmenu', [ $this, 'get_admin_menu_tooltip' ] );

		// Ajax call for hide ToolTip.
		add_action( 'wp_ajax_ngg_hide_admin_menu_tooltip', [ $this, 'mark_admin_menu_tooltip_hidden' ] );

		// Add styles and scripts for menu ToolTip.
		add_action( 'admin_head', [ $this, 'custom_style_scripts_menu_tooltip' ] );
	}

	/**
	 * Admin menu tooltip.
	 */
	public function get_admin_menu_tooltip() {
		$display_tooltip = $this->ngg_condition_check_to_display_tooltip();
		if ( ! $display_tooltip ) {
			return;
		}

		$url = admin_url( 'admin.php?page=ngg_addgallery' );
		?>
		<div id="nggallery-admin-menu-tooltip" class="nggallery-admin-menu-tooltip-hide">
			<div class="nggallery-admin-menu-tooltip-header">
				<span class="nggallery-admin-menu-tooltip-icon"><span class="dashicons dashicons-megaphone"></span></span>
				<?php esc_html_e( 'No Galleries Are Live!', 'nggallery' ); ?>
				<span class="nggallery-admin-menu-tooltip-close"><span class="dashicons dashicons-dismiss"></span></span>
			</div>
			<div class="nggallery-admin-menu-tooltip-content">
				<?php esc_html_e( "👋 You're not showcasing any images on this website. Why not create a stunning gallery with NextGEN?", 'nggallery' ); ?>
				<p>
					<button id="nggallery-admin-menu-launch-survery-tooltip-button" data-url="<?php echo esc_url( $url ); ?>" class="button button-primary"><?php esc_html_e( 'Build a Gallery', 'nggallery' ); ?></button>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Custom styles and scripts for menu Tooltip.
	 *
	 * @return void
	 */
	public function custom_style_scripts_menu_tooltip() {
		$display_tooltip = $this->ngg_condition_check_to_display_tooltip();
		if ( ! $display_tooltip ) {
			return;
		}
		?>
		<style>
			#toplevel_page_nextgen-gallery {
				position: relative;
			}

			#toplevel_page_nextgen-gallery.wp-not-current-submenu .wp-submenu-wrap {
				display: none;
			}

			#nggallery-admin-menu-tooltip {
				position: absolute;
				left: 100%;
				top: 0;
				background: #fff;
				margin-top: 6px;
				margin-left: 16px;
				width: 350px;
				box-shadow: 0px 4px 7px 0px #ccc;
			}

			#nggallery-admin-menu-tooltip:before {
				content: '';
				width: 0;
				height: 0;
				border-style: solid;
				border-width: 12px 12px 12px 0;
				border-color: transparent #fff transparent transparent;
				position: absolute;
				right: 100%;
				top: 75px;
				z-index: 10;
			}

			#nggallery-admin-menu-tooltip:after {
				content: '';
				width: 0;
				height: 0;
				border-style: solid;
				border-width: 13px 13px 13px 0;
				border-color: transparent #ccc transparent transparent;
				position: absolute;
				right: 100%;
				margin-left: -1px;
				top: 74px;
				z-index: 5;
			}

			.nggallery-admin-menu-tooltip-header {
				background: #37993B;
				padding: 5px 12px;
				font-size: 14px;
				font-weight: 700;
				font-family: Arial, Helvetica, "Trebuchet MS", sans-serif;
				color: #fff;
				line-height: 1.6;
			}

			.nggallery-admin-menu-tooltip-icon {
				background: #fff;
				border-radius: 50%;
				width: 28px;
				height: 25px;
				display: inline-block;
				color: #37993B;
				text-align: center;
				padding: 3px 0 0;
				margin-right: 6px;
			}

			.nggallery-admin-menu-tooltip-hide {
				display: none;
			}

			.nggallery-admin-menu-tooltip-content {
				color: #3c434a;
				padding: 15px 15px 7px;
			}

			.nggallery-admin-menu-tooltip-content:hover {
				color: #3c434a;
			}

			.nggallery-admin-menu-tooltip-close {
				color: #fff;
				text-decoration: none;
				position: absolute;
				right: 10px;
				top: 12px;
				display: block;
				cursor: pointer;
			}

			.nggallery-admin-menu-tooltip-close:hover {
				color: #fff;
				text-decoration: none;
			}

			.nggallery-admin-menu-tooltip-close:hover span::before {
				border-left: none !important;
			}

			.nggallery-admin-menu-tooltip-close .dashicons {
				font-size: 14px;
			}

			#nggallery-admin-menu-launch-survery-tooltip-button {
				background: #37993B;
				border-color: #37993B;
			}

			@media (max-width: 782px) {
				#nggallery-admin-menu-tooltip {
					display: none;
				}
			}
		</style>
		<script type="text/javascript">
			jQuery(function($) {
				const $tooltip = $(document.getElementById('nggallery-admin-menu-tooltip'));
				let $menuitem  = $(document.getElementById('toplevel_page_nextgen-gallery'));
				if (0 === $menuitem.length) {
					$menuitem = $(document.getElementById('toplevel_page_nextgen-gallery'));
				}
				if (0 === $menuitem.length) {
					$menuitem = $(document.getElementById('toplevel_page_nextgen-gallery'));
				}
				if (0 === $menuitem.length) {
					return;
				}

				if ($menuitem.length) {
					$menuitem.append($tooltip);
					$tooltip.removeClass('nggallery-admin-menu-tooltip-hide');
				}
				$tooltip.css({
					top: -1 * $tooltip.innerHeight() / 2 + 'px'
				});

				function hideTooltip() {
					$tooltip.addClass('nggallery-admin-menu-tooltip-hide');
					$.post(ajaxurl, {
						action: 'ngg_hide_admin_menu_tooltip',
						nonce: '<?php echo esc_js( wp_create_nonce( 'ngg-tooltip-admin-nonce' ) ); ?>',
					});
				}

				$('#nggallery-admin-menu-launch-survery-tooltip-button').click(function() {
					window.location = $(this).data('url');
					return false;
				});

				$('.nggallery-admin-menu-tooltip-close').on('click', function(e) {
					e.preventDefault();
					hideTooltip();
				});
			});
		</script>
		<?php
	}

	/**
	 * Condition check to display menu tooltip.
	 *
	 * @return bool
	 */
	public function ngg_condition_check_to_display_tooltip() {
		$gallery_count = \Imagely\NGG\DataMappers\Gallery::get_instance()->count();
		// Check if there are any galleries.
		if ( $gallery_count > 0 ) {
			return false;
		}

		// Bail if user is on manage galleries screen.
		$screen = get_current_screen();
		if ( false !== strpos( $screen->id, 'ngg_addgallery' ) || false !== strpos( $screen->id, 'nggallery-manage-gallery' ) ) {
			return false;
		}

		// Bail if the user is not allowed to save settings.
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		// Bail if the user has dismissed the tooltip within 7 days.
		$show_tooltip = get_option( 'ngg_admin_menu_tooltip', 0 );
		if ( $show_tooltip && ( $show_tooltip + 7 * DAY_IN_SECONDS > time() ) ) {
			// Dismissed less than 7 days ago.
			return false;
		}

		return true;
	}

	/**
	 * Store the time when the float bar was hidden so it won't show again for 14 days.
	 */
	public function mark_admin_menu_tooltip_hidden() {
		check_ajax_referer( 'ngg-tooltip-admin-nonce', 'nonce' );
		update_option( 'ngg_admin_menu_tooltip', time() );
		wp_send_json_success();
	}
}
