<style type="text/css">
    .subsubsub li+li:before {content:'| ';}
</style>
<ul class="subsubsub">
    <?php foreach($submenus as $menu): ?>
        <li><a href="<?php echo $menu['link']; ?>" target="<?php echo $menu['target']; ?>" <?php echo ($menu['current']) ? 'class="current"' : ''; ?>><?php echo $menu['title']; ?></a></li>
    <?php endforeach; ?>
</ul>
<div style="clear:both"></div>