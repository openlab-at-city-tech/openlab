<?php
	$width = $map->width();
	$height = $map->height();

	$id = $map->name . '_layout';
	$style = "width: $width; " . $map->get_layout_style();
	$class = $map->get_layout_class();
?>

<?php echo $map->get_show_link(); ?>

<div id="<?php echo $id; ?>" class="<?php echo $class; ?>" style="<?php echo $style; ?>">
	<div id="<?php echo $map->name . '_links';?>" class="mapp-map-links"><?php echo $map->get_links(); ?></div>
	<div id="<?php echo $map->name . '_dialog';?>" class="mapp-dialog"></div>
	<div id="<?php echo $map->name;?>" class="mapp-canvas" style="<?php echo "width: 100%; height: $height; "; ?>"></div>
	<div id="<?php echo $map->name . '_directions';?>" class="mapp-directions" style="width:100%"></div>
	<div id="<?php echo $map->name . '_poi_list';?>" class="mapp-poi-list" style="width:100%"></div>
</div>