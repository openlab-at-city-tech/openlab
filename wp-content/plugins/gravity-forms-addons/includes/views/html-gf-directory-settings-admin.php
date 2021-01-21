<?php
/**
 * The template that contains admin settings.
 *
 * @link       https://gravityview.co
 * @since      4.2
 *
 * @package    gravity-forms-addons
 * @subpackage gravity-forms-addons/includes/views
 */
?>

<style>
	.ul-square li {
		list-style: square !important;
	}

	.ol-decimal li {
		list-style: decimal !important;
	}

	.form-table label {
		font-size: 1em !important;
		margin: .4em 0;
		display: block;
	}

	li.setting-container {
		border: none !important;
	}
</style>

<script>
	jQuery( 'document' ).ready( function ( $ ) {
		$( '#kws_gf_advanced_settings' ).show();
		$( 'a:contains(Directory)', $( 'ul.subsubsub' ) ).css( 'font-weight', 'bold' );
		$( '.wp-submenu li.current, .wp-submenu li.current a' ).removeClass( 'current' );
		$( 'a:contains(Directory)', $( '.wp-submenu' ) ).addClass( 'current' ).parent( 'li' ).addClass( 'current' );

		$( 'a.kws_gf_advanced_settings' ).hide(); //click(function(e) {  e.preventDefault(); jQuery('#kws_gf_advanced_settings').slideToggle(); return false; });

		$( '#kws_gf_advanced_settings' ).change( function () {
			if ( $( "#gf_settings_thead:checked" ).length || $( "#gf_settings_tfoot:checked" ).length ) {
				$( '#gf_settings_jssearch' ).parents( 'li' ).show();
			} else {
				$( '#gf_settings_jssearch' ).parents( 'li' ).hide();
			}
		} ).trigger( 'change' );

		$( document ).on( 'load click', 'label[for=gf_addons_directory]', function () {
			if ( $( '#gf_addons_directory' ).is( ":checked" ) ) {
				$( "tr#directory_settings_row" ).show();
			} else {
				$( "tr#directory_settings_row" ).hide();
			}
		} );

		$( '#kws_gf_instructions_button' ).click( function ( e ) {
			e.preventDefault();

			$( '#kws_gf_instructions' ).slideToggle( function () {
				var $that = $( '#kws_gf_instructions_button' );
				$that.text( function () {
					if ( $( '#kws_gf_instructions' ).is( ":visible" ) ) {
						return '<?php echo esc_js( __( 'Hide Directory Instructions', 'gravity-forms-addons' ) ); ?>';
					} else {
						return '<?php echo esc_js( __( 'View Directory Instructions', 'gravity-forms-addons' ) ); ?>';
					}
				} );
			} );

			return false;
		} );
		$( '#message.fade' ).delay( 1000 ).fadeOut( 'slow' );
	} );
</script>

<div class="wrap">
	<?php
	if ( 'gf_settings' !== $plugin_page ) {

		echo '<h2>' . esc_html__( 'Gravity Forms Directory Add-on', 'gravity-forms-addons' ) . '</h2>';
	}
	if ( $message ) {
		echo "<div class='fade below-h2 updated' id='message'>" . wpautop( $message ) . '</div>';
	}

	// if you must, you can filter this out...
	if ( apply_filters( 'kws_gf_show_donate_box', true ) ) {
		include_once( GF_DIRECTORY_PATH . '/includes/views/html-gravityview-info-admin.php' );
	} // End donate box

	?>

	<p class="submit"><span style="padding-right:.5em;" class="description"><?php esc_html_e( 'Need help getting started?', 'gravity-forms-addons' ); ?></span>
		<a href="#" class="button button-secondary" id="kws_gf_instructions_button">
		<?php
		if ( ! empty( $settings['saved'] ) && ! isset( $_REQUEST['viewinstructions'] ) ) {
			esc_html_e( 'View Directory Instructions', 'gravity-forms-addons' );
		} else {
			esc_html_e( 'Hide Directory Instructions', 'gravity-forms-addons' );
		}
		?>
			</a>
		</p>

	<div id="kws_gf_instructions"
	<?php
	if ( ! empty( $settings['saved'] ) && ! isset( $_REQUEST['viewinstructions'] ) ) {
		?>
		  class="hide-if-js clear" <?php } ?>>
		<div class="delete-alert alert_gray">
			<div class="alignright" style="margin:1em 1.2em;">
				<iframe width="400" height="255"
						src="http<?php echo is_ssl() ? 's' : ''; ?>://www.youtube.com/embed/PMI7Jb-RP2I?hd=1"
						frameborder="0" allowfullscreen></iframe>
			</div>
			<h3 style="padding-top:1em; line-height: 1"><?php esc_html_e( 'To integrate a form with Directory:', 'gravity-forms-addons' ); ?></h3>
			<ol class="ol-decimal">
				<li><?php esc_html_e( 'Go to the post or page where you would like to add the directory.', 'gravity-forms-addons' ); ?></li>
				<li><?php esc_html_e( 'Click the "Add Directory" button above the content area.', 'gravity-forms-addons' ); ?></li>
				<li><?php esc_html_e( 'Choose a form from the drop-down menu and configure settings as you would like them.', 'gravity-forms-addons' ); ?></li>
				<li><?php printf( esc_html__( 'Click "Insert Directory". A "shortcode" should appear in the content editor that looks similar to %1$s[directory form="#"]%2$s', 'gravity-forms-addons' ), '<code style="font-size:1em;">', '</code>' ); ?></li>
				<li><?php esc_html_e( 'Save the post or page.', 'gravity-forms-addons' ); ?></li>
			</ol>

			<h4><?php esc_html_e( 'Configuring Fields & Columns', 'gravity-forms-addons' ); ?></h4>

			<?php echo wpautop( esc_html__( 'When editing a form, click on a field to expand the field. Next, click the "Directory" tab. There, you will find options to:', 'gravity-forms-addons' ) ); ?>

			<ul class="ul-square">
				<li><?php esc_html_e( 'Choose whether you would like the field to be a link to the Single Entry View', 'gravity-forms-addons' ); ?></li>
				<li><?php esc_html_e( 'Hide the field in Directory View', 'gravity-forms-addons' ); ?></li>
				<li><?php esc_html_e( 'Hide the field in Single Entry View', 'gravity-forms-addons' ); ?></li>
			</ul>

			<h4><?php esc_html_e( 'Configuring Column Visibility & Order', 'gravity-forms-addons' ); ?></h4>

			<ol class="ol-decimal">
				<li><?php esc_html_e( 'When editing a form in Gravity Forms, click the link near the top-center of the page named "Directory Columns".', 'gravity-forms-addons' ); ?></li>
				<li><?php esc_html_e( 'Drag and drop columns from the right ("Hidden Columns") side to the left ("Visible Columns") side.', 'gravity-forms-addons' ); ?></li>
				<li><?php esc_html_e( 'Click the "Save" button.', 'gravity-forms-addons' ); ?></li>
			</ol>

		</div>

		<div class="hr-divider"></div>
	</div>
	<form method="post" action="" class="clear">
		<?php wp_nonce_field( 'update', 'gf_directory_update' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row"><label
						for="gf_addons_directory"><?php esc_html_e( 'Gravity Forms Directory', 'gravity-forms-addons' ); ?></label>
				</th>
				<td>
					<label for="gf_addons_directory" class="howto"><input type="checkbox" id="gf_addons_directory" name="gf_addons_directory" <?php checked( $settings['directory'] ); ?> /> <?php esc_html_e( 'Enable Gravity Forms Directory capabilities', 'gravity-forms-addons' ); ?>
					</label>
				</td>
			</tr>
			<tr id="directory_settings_row">
				<th scope="row"></th>
				<td>
					<h2 style="margin-bottom:0; padding-bottom:0;"><?php esc_html_e( 'Directory Default Settings', 'gravity-forms-addons' ); ?></h2>
					<h3><?php esc_html_e( 'These defaults can be over-written when inserting a directory.', 'gravity-forms-addons' ); ?></h3>

					<?php
					self::make_popup_options( false );
					?>
					<div class="hr-divider"></div>
				</td>
			</tr>
			<tr>
				<th scope="row"><label
						for="gf_addons_referrer"><?php esc_html_e( 'Add Referrer Data to Emails', 'gravity-forms-addons' ); ?></label>
				</th>
				<td>
					<label for="gf_addons_referrer">
						<input type="checkbox" id="gf_addons_referrer" name="gf_addons_referrer" <?php checked( $settings['referrer'] ); ?> /> <?php esc_html_e( 'Adds referrer data to entries, including the path the user took to get to the form before submitting.', 'gravity-forms-addons' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><label
						for="gf_addons_modify_admin"><?php esc_html_e( 'Modify Gravity Forms Admin', 'gravity-forms-addons' ); ?></label>
				</th>
				<td>
					<ul>
						<li>
							<label for="gf_addons_modify_admin_expand">
								<input type="checkbox" id="gf_addons_modify_admin_expand" name="gf_addons_modify_admin[expand]" <?php checked( isset( $settings['modify_admin']['expand'] ) ); ?> /> <?php esc_html_e( 'Show option to expand Form Editor Field boxes', 'gravity-forms-addons' ); ?>
							</label>
						</li>

						<li>
							<label for="gf_addons_modify_admin_toggle">
								<input type="checkbox" id="gf_addons_modify_admin_toggle" name="gf_addons_modify_admin[toggle]" <?php checked( isset( $settings['modify_admin']['toggle'] ) ); ?> /> <?php esc_html_e( 'When clicking Form Editor Field boxes, toggle open and closed instead of "accordion mode" (closing all except the clicked box).', 'gravity-forms-addons' ); ?>
							</label>
						</li>

						<li>
							<label for="gf_addons_modify_admin_edit">
								<input type="checkbox" id="gf_addons_modify_admin_edit" name="gf_addons_modify_admin[edit]" <?php checked( isset( $settings['modify_admin']['edit'] ) ); ?> /> <?php printf( esc_html__( 'Makes possible direct editing of entries from %1$sEntries list view%2$s', 'gravity-forms-addons' ), '<a href="' . esc_url( admin_url( 'admin.php?page=gf_entries' ) ) . '">', '</a>' ); ?>
							</label>
						</li>

						<li>
							<label for="gf_addons_modify_admin_ids">
								<input type="checkbox" id="gf_addons_modify_admin_ids" name="gf_addons_modify_admin[ids]" <?php checked( isset( $settings['modify_admin']['ids'] ) ); ?> /> <?php printf( esc_html__( 'Adds a link in the Forms list view to view form IDs', 'gravity-forms-addons' ), '<a href="' . esc_url( admin_url( 'admin.php?page=gf_edit_forms' ) ) . '">', '</a>' ); ?>
							</label>
						</li>
					</ul>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="gf_addons_submit" class="button-primary button-large button-mega" value="<?php esc_attr_e( 'Save Settings', 'gravity-forms-addons' ); ?>"/>
				</td>
			</tr>
		</table>
	</form>
</div>
