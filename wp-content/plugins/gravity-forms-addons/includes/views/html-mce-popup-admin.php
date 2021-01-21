<?php
/**
 * The template that adds MCE popup.
 *
 * @link       https://gravityview.co
 * @since      4.2
 *
 * @package    gravity-forms-addons
 * @subpackage gravity-forms-addons/includes/views
 */
?>

<script>
	function addslashes( str ) {
		// Escapes single quote, double quotes and backslash characters in a string with backslashes
		// discuss at: http://phpjs.org/functions/addslashes
		return (str + '').replace( /[\\"']/g, '\\$&' ).replace( /\u0000/g, '\\0' );
	}

	jQuery( 'document' ).ready( function ( $ ) {
		$( '#select_gf_directory_form .datepicker' ).each( function () {
			if ( $.fn.datepicker ) {
				var element = jQuery( this );
				var format = "yy-mm-dd";

				var image = "";
				var showOn = "focus";
				if ( element.hasClass( "datepicker_with_icon" ) ) {
					showOn = "both";
					image = jQuery( '#gforms_calendar_icon_' + this.id ).val();
				}

				element.datepicker( {
					yearRange: '-100:+10',
					showOn: showOn,
					buttonImage: image,
					buttonImageOnly: true,
					dateFormat: format
				} );
			}
		} );


		$( '#select_gf_directory_form' ).bind( 'submit', function ( e ) {
			e.preventDefault();
			var shortcode = InsertGFDirectory();
			//send_to_editor(shortcode);
			return false;
		} );


		$( document ).on( 'click', '#insert_gf_directory', function ( e ) {
			e.preventDefault();
			$( '#select_gf_directory_form' ).trigger( 'submit' );
			return;
		} );

		$( 'a.select_gf_directory' ).click( function ( e ) {
			// This auto-sizes the box
			if ( typeof tb_position == 'function' ) {
				tb_position();
			}
			return;
		} );

		// Toggle advanced settings
		$( 'a.kws_gf_advanced_settings' ).click( function ( e ) {
			e.preventDefault();
			$( '#kws_gf_advanced_settings' ).toggle();
			return false;
		} );

		function InsertGFDirectory() {
			var directory_id = jQuery( "#add_directory_id" ).val();
			if ( directory_id == "" ) {
				alert( "<?php echo esc_js( __( 'Please select a form', 'gravity-forms-addons' ) ); ?>" );
				jQuery( '#add_directory_id' ).focus();
				return false;
			}

			<?php
			$js = self::make_popup_options( true );

			$ids = $idOutputList = $setvalues = $vars = '';

			foreach ( $js as $j ) {
				$vars .= $j['js'] . '
				';
				$ids .= $j['idcode'] . ' ';
				$setvalues .= $j['setvalue'] . '
				';
				$idOutputList .= $j['id'] . 'Output' . ' + ';
			}
			echo $vars;
			echo $setvalues;
			?>

			//var win = window.dialogArguments || opener || parent || top;
			var shortcode = "[directory form=\"" + directory_id + "\"" + <?php echo addslashes( $idOutputList ); ?>"]";
			window.send_to_editor( shortcode );
			return false;
		}
	} );
</script>

<div id="select_gf_directory" style="overflow-x:hidden; overflow-y:auto;display:none;">
	<form action="#" method="get" id="select_gf_directory_form">
		<div class="wrap">
			<div>
				<div style="padding:15px 15px 0 15px;">
					<h2><?php esc_html_e( 'Insert A Directory', 'gravity-forms-addons' ); ?></h2>
				<span>
					<?php esc_html_e( 'Select a form below to add it to your post or page.', 'gravity-forms-addons' ); ?>
				</span>
				</div>
				<div style="padding:15px 15px 0 15px;">
					<select id="add_directory_id">
						<option
							value="">  <?php esc_html_e( 'Select a Form', 'gravity-forms-addons' ); ?>  </option>
						<?php
						$forms = RGFormsModel::get_forms( 1, 'title' );
						foreach ( $forms as $form ) {
							?>
							<option
								value="<?php echo absint( $form->id ); ?>"><?php echo esc_html( $form->title ); ?></option>
							<?php
						}
						?>
					</select> <br/>
					<div
						style="padding:8px 0 0 0; font-size:11px; font-style:italic; color:#5A5A5A"><?php esc_html_e( 'This form will be the basis of your directory.', 'gravity-forms-addons' ); ?></div>
				</div>
				<?php

				self::make_popup_options();

				?>
				<div class="submit">
					<input type="submit" class="button-primary" style="margin-right:15px;"
						   value="Insert Directory" id="insert_gf_directory"/>
					<a class="button button-secondary" style="color:#bbb;" href="#"
					   onclick="tb_remove(); return false;"><?php esc_html_e( 'Cancel', 'gravity-forms-addons' ); ?></a>
				</div>
			</div>
		</div>
	</form>
</div>
