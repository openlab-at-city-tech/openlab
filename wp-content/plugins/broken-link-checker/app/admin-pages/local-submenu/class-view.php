<?php
/**
 * Local BLC admin page view.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Pages\Local_Submenu
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Pages\Local_Submenu;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Views\Admin_Page;
use WPMUDEV_BLC\App\Admin_Modals\Local\View as Local_Modal;


/**
 * Class View
 *
 * @package WPMUDEV_BLC\App\Admin_Pages\Local_Submenu
 */
class View extends Admin_Page {

    protected $is_settings_tab = null;

	/**
	 * Render the output.
	 *
	 * @since 2.1.0
	 *
	 * @return void Render the output.
	 */
	public function render( $params = array() ) {
		//$hook_suffix = ! empty( $params['hook_suffix'] ) ? $params['hook_suffix'] : null;
		$local_blc = ! empty( $params['local_blc'] ) ? $params['local_blc'] : null;
		$this->is_settings_tab = ! empty( $params['is_settings_tab'] ) ? $params['is_settings_tab'] : null;

		if ( $this->is_settings_tab ) {
			// When links tab is checked:
			$local_blc->options_page();
		} else {
			// When settings/options tab is checked:
			$local_blc->links_page();
		}
	}

    public function local_nav() {
        ?>
        <div id="wpmudev-blc-local-nav-wrap" class="_notice">
            <nav class="wpmudev-blc-local-nav">
                <a href="<?php echo admin_url( 'admin.php?page=blc_local' );?>" class="wpmudev-blc-local-nav-item blc-local-links <?php if ( ! $this->is_settings_tab ) echo 'active'; ?>" aria-current="true">Broken Links</a>
                <a href="<?php echo admin_url( 'admin.php?page=blc_local&local-settings=true' ); ?>" class="wpmudev-blc-local-nav-item blc-local-settings <?php if ( $this->is_settings_tab ) echo 'active'; ?>" aria-current="true">Settings</a>
            </nav>
        </div>
        <?php
    }

    public function local_header() {
        // Following h2 tag is added on purpose so that WP admin notices (that are automatically pushed right after first h tag) won't break ui.
        ?>
        <h2></h2>
        <div class="sui-wrap wrap-blc wrap-blc-local-page wrap-blc_local">
            <div class="sui-box local-blc-header-wrap">
                <div class="sui-box-header">
                    <h2 class="local-blc-heading"><?php echo esc_html__( 'Local Broken Link Checker', 'broken-link-checker' ); ?></h2>
                    <div class="sui-actions-right">
                        <?php Local_Modal::instance()->render( array( 'unique_id' => Local_Modal::instance()::$unique_id ) ); ?>
                           <!--
                            <span data-tooltip="">
                                <button id="blc-new-scan-button" aria-label="Run New Scan" class="sui-button  sui-button-blue blc-blue">
                                    <?php echo esc_html__( 'Activate Cloud-based Link Checker', 'broken-link-checker' ); ?>
                                    <span class="sui-screen-reader-text"><?php echo esc_html__( 'Activate Cloud-based Link Checker', 'broken-link-checker' ); ?></span>
                                </button>
                            </span>
                        -->
                    </div>
                </div>
            </div>
        </div>

	    <?php
        // Following Admin Notice is going to be removed.
	    //\WPMUDEV_BLC\App\Admin_Notices\Local\View::instance()->render();
    }
}
