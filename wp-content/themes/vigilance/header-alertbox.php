<?php global $vigilance; ?>
<?php if ($vigilance->alertboxState() == 'on' ) { ?>
	<div class="alert-box entry">
		<h2><?php echo $vigilance->alertboxTitle(); ?></h2>
		<?php echo $vigilance->alertboxContent(); ?>
	</div><!--end alert-box-->
<?php } ?>