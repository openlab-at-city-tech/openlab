<?php
/**
 * Soliloquy Settings
 *
 * @since 2.7.4
 *
 * @package Soliloquy Lite
 */

/**
 * Soliloquy Settings Class
 */
class Soliloquy_Settings {

	/**
	 * Helper Method for Class Hooks
	 *
	 * @since 2.7.4
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 11 );
	}

	/**
	 * Helper Method for Settings Admin Menu
	 *
	 * @since 2.7.4
	 *
	 * @return void
	 */
	public function admin_menu() {

		global $submenu;

		// Register the submenus.
		add_submenu_page(
			'edit.php?post_type=soliloquy',
			esc_html__( 'Settings', 'soliloquy' ),
			esc_html__( 'Settings', 'soliloquy' ),
			apply_filters( 'soliloquy_gallery_menu_cap', 'manage_options' ),
			'soliloquy-settings',
			[ $this, 'page' ]
		);
	}

	/**
	 * Output tab navigation
	 *
	 * @since 2.2.0
	 *
	 * @param string $tab Tab to highlight as active.
	 */
	public static function tab_navigation( $tab = 'soliloquy-settings' ) {
		?>

		<ul class="soliloquy-nav-tab-wrapper">
			<li>
			<a class="soliloquy-nav-tab
			<?php
			if ( isset( $_GET['page'] ) && 'soliloquy-settings' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				?>
				soliloquy-nav-tab-active<?php endif; ?>" href="
				<?php
				echo esc_url(
					admin_url(
						add_query_arg(
							[
								'post_type' => 'soliloquy',
								'page'      => 'soliloquy-settings',
							],
							'edit.php'
						)
					)
				);
				?>
														">
				<?php esc_html_e( 'Settings', 'soliloquy' ); ?>
			</a>
			</li>

		</ul>

		<?php
	}

	/**
	 * Generate Settings Content
	 *
	 * @since 2.7.4
	 *
	 * @return void
	 */
	public function page() {

		self::tab_navigation( __METHOD__ );
		?>
		<div class="soliloquy-settings-tab">
			<table class="form-table">
				<tbody>
					<tr id="soliloquy-image-gallery-settings-title">
						<th scope="row" colspan="2">
							<h3><?php esc_html_e( 'License', 'soliloquy' ); ?></h3>
							<p><?php esc_html_e( 'Your license key provides access to updates and add-ons.', 'soliloquy' ); ?></p>
						</th>
					</tr>
					<tr id="soliloquy-settings-key-box" class="title">
						<th scope="row">
							<label for="soliloquy-settings-key"><?php esc_html_e( ' License Key', 'soliloquy' ); ?></label>
						</th>
						<td>
							<p><?php esc_html_e( "You're using Soliloquy Lite - no license needed. Enjoy! 🙂", 'soliloquy' ); ?></p>

							<p>
							<?php
							printf(
							// Translators: %1$s - Opening anchor tag, do not translate. %2$s - Closing anchor tag, do not translate.
								esc_html__( 'To unlock more features consider %1$supgrading to PRO%2$s.', 'soliloquy' ),
								'<strong><a href="' . esc_url( Soliloquy_Common_Admin_Lite::get_instance()->get_upgrade_link( 'https://soliloquywp.com/lite', 'settingspage', 'upgradingtopro' ) ) . '" target="_blank" rel="noopener noreferrer">',
								'</a></strong>'
							);
							?>
							</p>
							<p>
							<?php
								printf(
									// Translators: %1$s - Opening span tag, do not translate. %2$s - Closing span tag, do not translate.
									esc_html__( 'As a valued Soliloquy Lite user you receive %1$s 50%% off%2$s, automatically applied at checkout', 'soliloquy' ),
									'<span class="green"><strong>',
									'</strong></span>'
								);
							?>
							</p>
							<hr />
							<form id="soliloquy-settings-verify-key" method="post">
								<p class="description"><?php esc_html_e( 'Already purchased? Simply enter your license key below to enable Soliloquy PRO!', 'soliloquy-gallery' ); ?></p>
								<input placeholder="<?php esc_attr_e( 'Paste license key here', 'soliloquy' ); ?>" type="password" name="soliloquy-license-key" id="soliloquy-settings-key" value="" />
								<button type="button " class="button soliloquy-primary-button soliloquy-verify-submit primary" id="soliloquy-settings-connect-btn">
					<?php esc_html_e( 'Verify Key', 'soliloquy' ); ?>
				</button>


							</form>
						</td>
					</tr>

				</tbody>
			</table>

			<!-- <hr /> -->
		</div>
		<?php
	}
}
