<?php
/**
 * The template that contains 'Directory Columns' tab setting.
 *
 * @link       https://gravityview.co
 * @since      4.2
 *
 * @package    gravity-forms-addons
 * @subpackage gravity-forms-addons/includes/views
 */
?>

<style>
	.lead_approved .toggleApproved {
		background: url(<?php echo GF_DIRECTORY_URL . 'images/tick.png'; ?>) left top no-repeat;
	}
	.toggleApproved {
		background: url(<?php echo GF_DIRECTORY_URL . 'images/cross.png'; ?>) left top no-repeat;
		width: 16px;
		height: 16px;
		display: block;
		text-indent: -9999px;
		overflow: hidden;
	}
</style>
<script>
	<?php

	if ( empty( $formID ) ) {
		$forms = RGFormsModel::get_forms( null, 'title' );
		$formID = $forms[0]->id;
	}

	$approvedcolumn = GFDirectory::globals_get_approved_column( $formID );

		echo 'formID = ' . $formID . ';';
	?>

	function UpdateApproved(lead_id, approved) {
		var mysack = new sack("<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>" );
		mysack.execute = 1;
		mysack.method = 'POST';
		mysack.setVar( "action", "rg_update_approved" );
		mysack.setVar( "rg_update_approved", "<?php echo wp_create_nonce( 'rg_update_approved' ); ?>" );
		mysack.setVar( "lead_id", lead_id);
		mysack.setVar( "form_id", formID);
		mysack.setVar( "approved", approved);
		mysack.encVar( "cookie", document.cookie, false );
		mysack.onError = function() {
			console.log('<?php echo esc_js( __( 'Ajax error while setting lead approval', 'gravity-forms-addons' ) ); ?>' );
		};
		mysack.runAJAX();

		return true;
	}

 <?php

	if ( ! function_exists( 'gform_get_meta' ) ) {
		?>

	function displayMessage(message, messageClass, container){

		hideMessage(container, true);

		var messageBox = jQuery('<div class="message ' + messageClass + '" style="display:none;"><p>' + message + '</p></div>');
		jQuery(messageBox).prependTo(container).slideDown();

		if(messageClass == 'updated')
			{messageTimeout = setTimeout(function(){ hideMessage(container, false); }, 10000);
}

	}

	function hideMessage(container, messageQueued){

		var messageBox = jQuery(container).find('.message');

		if(messageQueued)
			{jQuery(messageBox).remove();
}else
			{jQuery(messageBox).slideUp(function(){ jQuery(this).remove(); });
}

	}

	<?php } // end meta check for 1.6 ?>

	jQuery(document).ready(function($) {

		<?php if ( ! empty( $process_bulk_update_message ) ) { ?>
			displayMessage('<?php echo esc_js( $process_bulk_update_message ); ?>', 'updated', '.gf_entries');
		<?php } ?>

		var approveTitle = '<?php echo esc_js( __( 'Entry not approved for directory viewing. Click to approve this entry.', 'gravity-forms-addons' ) ); ?>';
		var unapproveTitle = '<?php echo esc_js( __( 'Entry approved for directory viewing. Click to disapprove this entry.', 'gravity-forms-addons' ) ); ?>';

		$(document).on('click load', '.toggleApproved', function(e) {
			e.preventDefault();

			var $tr = $(this).parents('tr');
			var is_approved = $tr.is(".lead_approved");

			if(e.type === 'click') {
				$tr.toggleClass("lead_approved");
			}

			// Update the title and screen-reader text
			if(!is_approved) {
				$(this).text('X').prop('title', unapproveTitle);
			} else {
				$(this).text('O').prop('title', approveTitle);
			}

			if(e.type == 'click') {
				UpdateApproved($('th input[type="checkbox"]', $tr).val(), is_approved ? 0 : 'Approved');
			}

			UpdateApprovedColumns($(this).parents('table.gf_entries'), false);

			return false;
		});

		// We want to make sure that the checkboxes go away even if the Approved column is showing.
		// They will be in sync when loaded, so only upon click will we process.
		function UpdateApprovedColumns($table, onLoad) {

			<?php

			if ( ! empty( $approvedcolumn ) ) {
				/** @see https://stackoverflow.com/a/350300/480856 */
				$approved_column_jquery = str_replace( '.', '\\\.', $approvedcolumn );
				$approved_column_jquery = 'field_id-' . esc_html( $approved_column_jquery );

				?>

			$('tr', $table).each(function() {

				// No GF approval; don't modify things
				if( 0 === $( '.toggleApproved', $( this ) ).length ) {
					return;
				}

				if( $(this).is('.lead_approved') || (onLoad && $("input.lead_approved", $(this)).length > 0)) {

					if(onLoad && $(this).not('.lead_approved')) {
						$(this).addClass('lead_approved');
					}

					$('td.column-<?php echo $approved_column_jquery; ?>:visible', $(this)).html('<i class="fa fa-check gf_valid"></i>');

				} else {

					if(onLoad && $(this).is('.lead_approved')) {
						$(this).removeClass('lead_approved');
					}

					$('td.column-<?php echo $approved_column_jquery; ?>:visible', $(this)).html('');
				}
			});
				<?php
			}
			?>
		}

		// Add the header column
		$('thead .column-is_starred, tfoot .column-is_starred').after('<th class="manage-column column-is_starred sortable"><a href="<?php echo esc_url( add_query_arg( array( 'sort' => $approvedcolumn ) ) ); ?>"><img src="<?php echo GF_DIRECTORY_URL . 'images/form-button-1.png'; ?>" title="<?php echo esc_js( __( 'Show entry in directory view?', 'gravity-forms-addons' ) ); ?>" /></span></a></th>');

		// Add to each row
		$('tbody th:has(img[src*="star"])').after('<td><a href="#" class="toggleApproved" title="'+approveTitle+'">X</a></td>');

		$('tr:has(input.lead_approved)').addClass('lead_approved').find('a.toggleApproved').prop('title', unapproveTitle).text('O');

		UpdateApprovedColumns($('table.gf_entries'), true);

	});
</script>
