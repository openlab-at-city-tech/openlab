<?php
/**
 * Plugin activation modal view.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Modals\Plugin_Activation
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Modals\Plugin_Activation;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;


/**
 * Class View
 *
 * @package WPMUDEV_BLC\App\Admin_Modals\Plugin_Activation
 */
class View extends Base {
	/**
	 * The unique id that can be used by react. Sent over from Controller.
	 *
	 * @var int $unique_id
	 *
	 * @since 2.0.0
	 */
	public static $unique_id = null;

	/**
	 * Render the output.
	 *
	 * @since 2.0.0
	 *
	 * @return void Render the output.
	 */
	public function render( $params = array() ) {
		self::$unique_id = isset( $params['unique_id'] ) ? $params['unique_id'] : null;

		?>
		<div class="sui-wrap wrap-blc wrap-blc-activation-modal <?php echo 'wrap-' . esc_attr( self::$unique_id ); ?>">
			<?php
			$this->render_body();
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
