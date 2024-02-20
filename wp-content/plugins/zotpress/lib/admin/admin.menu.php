<?php

if ( ( isset($_GET['accounts']) && $_GET['accounts'] == "true" )
		|| ( isset($_GET['selective']) && $_GET['selective'] == "true" )
		|| ( isset($_GET['import']) && $_GET['import'] == "true" ) )
{
    $tagpage = "accounts";
}
elseif ( isset($_GET['options']) && $_GET['options'] == "true" )
{
    $tagpage = "options";
}
elseif ( isset($_GET['help']) && $_GET['help'] == "true" )
{
    $tagpage = "help";
}
else
{
    $tagpage = "default";
}

?>

<div id="zp-Zotpress-Navigation">

    <div id="zp-Icon">
        <img src="<?php echo ZOTPRESS_PLUGIN_URL; ?>/images/icon-64x64.png" title="Zotero + WordPress = Zotpress">
    </div>

    <div class="nav">
        <a class="nav-item <?php if ($tagpage == "default") echo "nav-tab-active"; ?>" href="admin.php?page=Zotpress"><?php _e('Browse','zotpress'); ?></a>
        <?php if ( current_user_can('edit_others_posts') ) { ?><a class="nav-item <?php if ($tagpage == "accounts") echo "nav-tab-active"; ?>" href="admin.php?page=Zotpress&amp;accounts=true"><?php _e('Accounts', 'zotpress'); ?></a><?php } ?>
        <?php if ( current_user_can('edit_others_posts') ) { ?><a class="nav-item <?php if ($tagpage == "options") echo "nav-tab-active"; ?>" href="admin.php?page=Zotpress&amp;options=true"><?php _e('Options', 'zotpress'); ?></a><?php } ?>
        <a class="nav-item <?php if ($tagpage == "help") echo "nav-tab-active"; ?>" href="admin.php?page=Zotpress&amp;help=true"><?php _e('Help', 'zotpress'); ?></a>
    </div>

</div><!-- #zp-Zotpress-Navigation -->
