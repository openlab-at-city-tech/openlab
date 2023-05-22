<?php
/**
 * BLC Cloud_Page admin page view.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Pages\Cloud_Page
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Pages\Cloud_Page;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Views\Admin_Page;


/**
 * Class View
 *
 * @package WPMUDEV_BLC\App\Admin_Pages\Cloud_Page
 */
class View extends Admin_Page {

	/**
	 * Render the output.
	 *
	 * @since 2.0.0
	 *
	 * @return void Render the output.
	 */
	public function render( $params = array() ) {
		self::$unique_id = isset( $params['unique_id'] ) ? $params['unique_id'] : null;
		self::$slug      = isset( $params['slug'] ) ? $params['slug'] : null;
		$site_connected  = isset( $params['site_connected'] ) ? boolval( $params['site_connected'] ) : false;
		?>
		<div class="sui-wrap wrap-blc wrap-blc-dashboard-page <?php echo 'wrap-' . esc_attr( self::$slug ); ?>">
            <?php
            $this->render_body();

			if ( $site_connected ){
				$this->render_footer();
			}
            ?>
		</div>
		<?php
	}

    public function render_body() {
	    ?>
        <div id="<?php esc_attr_e( self::$unique_id ); ?>"></div>
	    <?php
    }

}
