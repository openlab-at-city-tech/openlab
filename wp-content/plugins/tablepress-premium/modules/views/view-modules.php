<?php
/**
 * Modules View.
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Modules View class.
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Modules_View extends TablePress_View {

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

		TablePress_Modules_Helper::enqueue_style( 'modules' );
		TablePress_Modules_Helper::enqueue_script( 'modules' );

		$this->process_action_messages( array(
			'success_save' => __( 'The active modules were saved successfully.', 'tablepress' ),
			'error_save'   => __( 'Error: The active modules could not be saved.', 'tablepress' ),
		) );

		$this->add_text_box( 'head', array( $this, 'textbox_head' ), 'normal' );
		$this->add_text_box( 'modules', array( $this, 'textbox_modules' ), 'normal' );

		if ( tb_tp_fs()->is_plan_or_trial( $data['minimum_modules_plan'] ) ) {
			$this->add_text_box( 'submit', array( $this, 'textbox_submit_button' ), 'submit' );
		}
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
		<p>
			<?php printf( __( 'TablePress offers an additional range of useful features, available as <a href="%s">Modules</a> in its Premium versions.', 'tablepress' ), 'https://tablepress.org/modules/' ); ?>
		</p>
		<p>
			<?php _e( 'Depending on your site’s license plan, different Premium Modules are part of your subscription.', 'tablepress' ); ?>
			<?php _e( 'Below, you can activate desired Modules and their functionality.', 'tablepress' ); ?>
			<?php _e( 'Further configuration or customization options will then be available when managing your tables.', 'tablepress' ); ?>
			<?php printf( __( 'For more details, visit the module’s web page on the <a href="%s">TablePress website</a>.', 'tablepress' ), 'https://tablepress.org/modules/' ); ?>
		</p>
		<?php
	}

	/**
	 * Prints the content of the "Modules" text box.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data Data for this screen.
	 * @param array<string, mixed> $box  Information about the meta box.
	 */
	public function textbox_modules( array $data, array $box ): void {
		foreach ( $data['categories'] as $category => $category_name ) {
			echo "<h2 class=\"category-name\">{$category_name}</h2>";
			echo '<div class="module-box-wrapper">';
			foreach ( $data['available_modules'][ $category ] as $slug => $module ) {
				$active_module = in_array( $slug, $data['active_modules'], true );
				$checked = $active_module ? ' checked' : '';
				$disabled = tb_tp_fs()->is_plan_or_trial( $module['minimum_plan'] ) ? '' : ' disabled';
				switch ( $module['minimum_plan'] ) {
					case 'max':
						$plan = 'Max';
						break;
					case 'pro':
						$plan = 'Pro';
						break;
					default:
						$plan = 'Pro';
						break;
				}
				$title = ( '' === $disabled ) ? '' : ' title="' . sprintf( __( 'The “%1$s” Premium Module requires a license subscription to the “%2$s” plan.', 'tablepress' ), $module['name'], $plan ) . '"';
				echo "<div class=\"module-box\"{$title}>";
				echo "<input type=\"checkbox\" id=\"module-{$slug}\" name=\"modules[]\" value=\"{$slug}\"{$checked}{$disabled}>";
				echo "<label for=\"module-{$slug}\">";
				echo '<div class="module-box-top">';
				echo "<h3>{$module['name']}</h3>";
				echo "<p class=\"description\">{$module['description']}</p>";
				echo '</div>';
				echo '<div class="module-box-bottom">';
				echo '<a href="' . esc_url( "https://tablepress.org/modules/{$slug}/?utm_source=plugin&utm_medium=textlink&utm_content=module-box" ) . '" class="module-link">' . __( 'More Details', 'tablepress' ) . '</a>';
				echo '<span class="module-state"><input type="checkbox" class="module-inactive" tabindex="-1"><input type="checkbox" class="module-active" tabindex="-1" checked>' . __( 'Module active', 'tablepress' ) . '</span>';
				echo '</div>';
				echo '</label>';
				if ( '' !== $disabled ) {
					echo '<div class="ribbon"><span>' . sprintf( '<a href="%1$s">%2$s</a>', 'https://tablepress.org/premium/?utm_source=plugin&utm_medium=textlink&utm_content=module-box', sprintf( __( '%s plan', 'tablepress' ), $plan ) ) . '</span></div>';
				}
				echo '</div>';
			}
			echo '</div>';
		}

		// Add a hidden field to make sure that the `$_POST['modules']` variable is always populated as an array, even when no modules are activated.
		echo '<input type="hidden" name="modules[]" value="_http-test">';
	}

	/**
	 * Prints "Save Changes" button.
	 *
	 * @since 2.1.2
	 *
	 * @param array<string, mixed> $data Data for this screen.
	 * @param array<string, mixed> $box  Information about the text box.
	 */
	#[\Override]
	public function textbox_submit_button( array $data, array $box ): void {
		?>
			<p class="submit">
				<input type="submit" id="tablepress-modules-save-changes" class="button button-primary button-large button-save-changes" value="<?php esc_attr_e( 'Save Changes', 'tablepress' ); ?>" data-shortcut="<?php echo esc_attr( _x( '%1$sS', 'keyboard shortcut for Save Changes', 'tablepress' ) ); ?>">
			</p>
		<?php
	}

} // class TablePress_Modules_View
