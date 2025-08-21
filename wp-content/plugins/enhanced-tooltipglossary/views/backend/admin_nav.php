<style type="text/css">
    .subsubsub li+li:before {content:'| ';}
</style>
<ul class="subsubsub">
    <?php
	foreach($submenus as $menu) {
		if($menu['title'] != '<span class="cmseparator"></span>') {
			?>
			<li><a href="<?php echo $menu['link']; ?>" target="<?php echo $menu['target']; ?>" <?php echo ($menu['current']) ? 'class="current"' : ''; ?>><?php echo $menu['title']; ?></a></li>
			<?php
		}
	}
	?>
</ul>
<div style="clear:both"></div>