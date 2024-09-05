<?php
/**
 * Advanced Access Rights View.
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Advanced Access Rights View class.
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Advanced_Access_Rights_View extends TablePress_View {

	/**
	 * Sets up the view with data and do things that are specific for this view.
	 *
	 * @since 2.0.0
	 *
	 * @param string               $action Action for this view.
	 * @param array<string, mixed> $data   Data for this view.
	 */
	#[\Override]
	public function setup( /* string */ $action, array $data ) /* : void */ {
		// Don't use type hints in the method declaration to prevent PHP errors, as the method is inherited.

		parent::setup( $action, $data );

		$this->add_text_box( 'no-javascript', array( $this, 'textbox_no_javascript' ), 'header' );

		TablePress_Modules_Helper::enqueue_style( 'advanced-access-rights' );
		TablePress_Modules_Helper::enqueue_script( 'advanced-access-rights' );

		$this->add_text_box( 'head', array( $this, 'textbox_head' ), 'normal' );
		$this->add_text_box( 'access_rights_map', array( $this, 'textbox_access_rights' ), 'normal' );
	}

	/**
	 * Prints the screen head text.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data Data for this screen.
	 * @param array<string, mixed> $box  Information about the text box.
	 */
	public function textbox_head( array $data, array $box ): void {
		?>
		<div class="hide-if-no-js">
			<p>
				<?php _e( 'To restrict editing access to certain tables for certain users, use the table below.', 'tablepress' ); ?>
				<?php _e( 'You can also set the default access rights for newly added users and newly added tables.', 'tablepress' ); ?>
			</p>
			<p>
				<?php _e( 'If a user, including yourself, does not have access to a table (i.e., the user’s checkbox for that table is unchecked), he will not see that table on the TablePress admin screens.', 'tablepress' ); ?>
			</p>
			<p>
				<?php _e( 'Note that the user’s user role will also need access for a user to be granted access.', 'tablepress' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Prints the content of the "Advanced Access Rights" text box.
	 *
	 * @since 2.2.0
	 *
	 * @param array<string, mixed> $data Data for this screen.
	 * @param array<string, mixed> $box  Information about the text box.
	 */
	public function textbox_access_rights( array $data, array $box ): void {
		$script_data = array(
			'map'    => $this->admin_page->convert_to_json_parse_output( $data['access_rights_map'] ),
			'tables' => $this->admin_page->convert_to_json_parse_output( $data['tables'] ),
			'users'  => $this->admin_page->convert_to_json_parse_output( $data['users'] ),
		);

		echo "<script>\n";
		echo "window.tp = window.tp || {};\n";
		echo "tp.advanced_access_rights = {};\n";
		foreach ( $script_data as $variable => $value ) {
			echo "tp.advanced_access_rights.{$variable} = {$value};\n";
		}
		echo "</script>\n";

		echo '<div id="tablepress-advanced-access-rights-screen"></div>';
	}

} // class TablePress_Advanced_Access_Rights_View
