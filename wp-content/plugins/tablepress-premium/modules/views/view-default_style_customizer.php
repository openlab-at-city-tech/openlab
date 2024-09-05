<?php
/**
 * Default Style Customizer View.
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 2.2.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Default Style Customizer View class.
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 2.2.0
 */
class TablePress_Default_Style_Customizer_View extends TablePress_Options_View {

	/**
	 * Sets up the view with data and do things that are specific for this view.
	 *
	 * @since 2.2.0
	 *
	 * @param string               $action Action for this view.
	 * @param array<string, mixed> $data   Data for this view.
	 */
	#[\Override]
	public function setup( /* string */ $action, array $data ) /* : void */ {
		// Don't use type hints in the method declaration to prevent PHP errors, as the method is inherited.

		parent::setup( $action, $data );

		if ( current_user_can( 'tablepress_edit_options' ) ) {
			// Determine Default CSS URL.
			$rtl = ( is_rtl() ) ? '-rtl' : '';
			$unfiltered_default_css_url = plugins_url( "css/build/default{$rtl}.css", TABLEPRESS__FILE__ );
			/**
			 * Filters the URL from which the TablePress Default CSS file is loaded.
			 *
			 * @since 1.0.0
			 *
			 * @param string $unfiltered_default_css_url URL of the TablePress Default CSS file.
			 */
			$default_css_url = apply_filters( 'tablepress_default_css_url', $unfiltered_default_css_url );
			$default_css_url .= '?ver=' . TablePress::version; // Add version number as cache buster.

			TablePress_Modules_Helper::enqueue_style( 'default-style-customizer', array( 'wp-components' ) );
			TablePress_Modules_Helper::enqueue_script(
				'default-style-customizer',
				array(),
				array(
					'default_style_customizer_settings' => array(
						'defaultCssUrl' => $default_css_url,
					),
				)
			);
		}
	}

	/**
	 * Prints the content of the "Default Style Customizer Screen" post meta box.
	 *
	 * @since 2.2.0
	 *
	 * @param array<string, mixed> $data Data for this screen.
	 * @param array<string, mixed> $box  Information about the meta box.
	 */
	public function postbox_default_style_customizer_screen( array $data, array $box ): void {
		echo '<div id="tablepress-default-style-customizer-screen">';
		$this->textbox_no_javascript( $data, $box );
		echo '</div>';
	}

	/**
	 * Prints "Save Changes" button, with different CSS classes.
	 *
	 * This is a copy of the method from the parent class, with the CSS classes changed,
	 * so that the button styling matches the "Export to “Custom CSS”" button,
	 * which uses `wp-components` styling.
	 *
	 * @since 2.2.0
	 *
	 * @param array<string, mixed> $data Data for this screen.
	 * @param array<string, mixed> $box  Information about the text box.
	 */
	#[\Override]
	public function textbox_submit_button( array $data, array $box ): void {
		?>
			<p class="submit">
				<input type="submit" id="tablepress-options-save-changes" class="components-button is-primary button-save-changes" value="<?php esc_attr_e( 'Save Changes', 'tablepress' ); ?>" data-shortcut="<?php echo esc_attr( _x( '%1$sS', 'keyboard shortcut for Save Changes', 'tablepress' ) ); ?>">
			</p>
		<?php
	}

} // class TablePress_Default_Style_Customizer_View
