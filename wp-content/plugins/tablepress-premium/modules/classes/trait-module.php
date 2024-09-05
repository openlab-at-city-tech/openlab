<?php
/**
 * TablePress Module Trait with members and methods for all TablePress Premium Modules.
 *
 * @package TablePress
 * @subpackage Modules
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * TablePress Modules trait.
 *
 * @package TablePress
 * @subpackage Modules
 * @author Tobias Bäthge
 * @since 2.0.0
 */
trait TablePress_Module {

	/**
	 * Properties for the module.
	 *
	 * @since 2.0.0
	 * @var array{slug: string, name: string, description: string, category: string, class: string, incompatible_classes: string[], minimum_plan: string, default_active: bool}
	 */
	public static $module = array(
		'slug'                 => '',
		'name'                 => '',
		'description'          => '',
		'category'             => '',
		'class'                => '',
		'incompatible_classes' => array(),
		'minimum_plan'         => '',
		'default_active'       => false,
	);

	/**
	 * Prints the HTML code for a "Help" button and the corresponding "Help box" container.
	 *
	 * @param string $help_box_content HTML content of the Help Box that is specific to the Help item.
	 * @param string $height           Height of the Help Box in pixels.
	 * @param string $width            Width  of the Help Box in pixels.
	 */
	public static function print_help_box_markup( string $help_box_content = '', string $height = '', string $width = '' ): void {
		// Define the default values for the help box size here, so that it's possible to override the second parameter (width), without having to repeat the first parameter (height).
		if ( '' === $height ) {
			$height = '400';
		}
		if ( '' === $width ) {
			$width = '400';
		}
		?>
		<input type="button" class="button button-small button-show-help-box button-module-help" value="<?php esc_attr_e( 'Help', 'tablepress' ); ?>" title="<?php echo esc_attr( sprintf( __( 'Help on the “%s” module', 'tablepress' ), self::$module['name'] ) ); ?>" data-help-box="#help-box-<?php echo esc_attr( self::$module['slug'] ); ?>">
		<div id="help-box-<?php echo esc_attr( self::$module['slug'] ); ?>" class="help-box hidden-container" title="<?php echo esc_attr( self::$module['name'] ); ?>" data-height="<?php echo esc_attr( $height ); ?>" data-width="<?php echo esc_attr( $width ); ?>">
			<h4><?php echo esc_html( self::$module['description'] ); ?></h4>
			<?php echo $help_box_content; ?>
			<p><a href="<?php echo esc_url( 'https://tablepress.org/modules/' . self::$module['slug'] . '/?utm_source=plugin&utm_medium=textlink&utm_content=edit-screen-help-box' ); ?>" class="module-link" target="_blank" rel="noopener"><?php _e( 'More Details, Examples, and Documentation', 'tablepress' ); ?></a></p>
		</div>
		<?php
	}

} // trait TablePress_Module
