<div <?php echo $map->part('layout-atts');?>>
	<?php echo $map->part('header'); ?>
	<div class='mapp-wrapper' style='<?php echo $map->part('wrapper-style');?>' >
		<div class='mapp-main'>
			<?php echo $map->part('filters'); ?>
			<?php echo $map->part('list-left'); ?>
			<div class='mapp-canvas-panel'>
				<?php echo $map->part('canvas'); ?>
				<?php echo $map->part('controls'); ?>
				<?php echo $map->part('iw'); ?>
				<div class='mapp-dialog'></div>
			</div>
		</div>
	</div>
	<?php echo $map->part('directions'); ?>
	<?php echo $map->part('list-inline'); ?>
</div>