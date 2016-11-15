<table style='width:100%'>
	<tbody>
	<?php foreach($map->pois as $poi) : ?>
		<?php if ($poi->type == 'kml') continue; ?>
		<tr>
			<td>
				<?php echo $poi->get_icon(); ?>
			</td>
			<td style='width:100%'>
				<div class='mapp-title'>
					<?php echo $poi->get_open_link(); ?>
				</div>
				<div>
					<?php echo $poi->get_links('poi_list'); ?>
				</div>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
