<div class='mapp-iw'>
	<div class='mapp-title'>
		<?php echo $poi->get_title_link(); ?>
	</div>
	<div class='mapp-body'>
		<?php echo $poi->get_thumbnail(array('class' => 'mapp-thumb')); ?>
		<?php echo $poi->get_body(); ?>
	</div>
	<div class='mapp-links'>
		<?php echo $poi->get_links(); ?>
	</div>
</div>