<style type="text/css">
    .subsubsub li+li:before {content:'| ';}
</style>
<ul class="subsubsub">
    <?php foreach ( $submenus as $menu ): ?>
        <?php if ( empty( $_GET[ 'page' ] ) || $_GET[ 'page' ] != 'cmtt_pro' ) { ?>
            <li><a href="<?php echo $menu[ 'link' ]; ?>" target="<?php echo $menu[ 'target' ]; ?>" <?php echo ($menu[ 'current' ]) ? 'class="current"' : ''; ?>><?php echo $menu[ 'title' ]; ?></a></li>
        <?php } ?>

    <?php endforeach; ?>
</ul>