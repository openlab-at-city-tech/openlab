<?php
if ( !defined('P3_PATH') )
	die( 'Forbidden ');
?>
<form id="scans-filter" method="post">
	<input type="hidden" name="page" value="<?php echo sanitize_text_field( $_REQUEST ['page'] ); ?>" />
	<?php echo wp_nonce_field( 'delete_scans', 'p3_nonce' ); ?>
	<?php self::$scan_table->display(); ?>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$("input:submit", "#scans-filter").click(function(evt) {
			if (0 == $("input:checked", $("#scans-filter")).length) {
				evt.stopPropagation();
				evt.preventDefault();					
			} else if (!confirm( '<?php _e( 'Are you sure you want to delete these scans?', 'p3-profiler' ); ?>' )) {
				evt.stopPropagation();
				evt.preventDefault();
			} else {
				return true;
			}
		});
		$("a.delete-scan").click(function(evt) {
			if (confirm( '<?php _e( 'Are you sure you want to delete this scan?', 'p3-profiler' ); ?>' )) {

				// De-select the checkboxes
				$("#scans-filter input:checked").prop("checked", false);

				// Find the parent TR
				$tr = $(this).parents("tr");

				// Check the checkbox
				$("input:checkbox", $tr).prop("checked", true);

				// Select the delete action
				$("select:first", "#scans-filter").val("delete");

				// Submit the form
				$("#scans-filter").submit();
			}
		});
	});
</script>