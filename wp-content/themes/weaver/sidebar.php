<?php
/**
 * The Sidebar containing the primary and secondary widget areas.
 */
?>
<?php

    weaver_put_area('presidebar');
    if (!weaver_is_checked_page_opt('hide-primary-widget-area') && !weaver_replace_primary()) {
    ?>
	<div id="primary" class="widget-area" role="complementary">
	    <ul class="xoxo">
<?php

	/* When we call the dynamic_sidebar() function, it'll spit out
	 * the widgets for that widget area. If it instead returns false,
	 * then the sidebar simply doesn't exist, so we'll hard-code in
	 * some default sidebar stuff just in case.
	 */
	if ( ! dynamic_sidebar( 'primary-widget-area' ) ) { ?>
	    <li id="meta" class="widget-container">
		<h3 class="widget-title"><?php _e( 'Primary Widget Area', WEAVER_TRANSADMIN ); ?></h3>
		<ul>
<?php _e("This theme has been designed to be used with sidebars. This message will no
longer be displayed after you add at least one widget to the Primary Widget Area
using the Appearance->Widgets control panel.",WEAVER_TRANSADMIN); ?>
		    <li><?php wp_loginout(); ?></li>
		</ul>
	    </li>

	<?php } // end primary widget area ?>
	</ul>
	</div><!-- #primary .widget-area -->
<?php
    }

    /* now the secondary area */
    if (!weaver_is_checked_page_opt('hide-secondary-widget-area')&& !weaver_replace_secondary()) {
	    // The Secondary Widget Area
	if ( is_active_sidebar( 'secondary-widget-area' ) ) { ?>
	    <div id="secondary" class="widget-area" role="complementary">
	    <ul class="xoxo">
		<?php dynamic_sidebar( 'secondary-widget-area' ); ?>
	    </ul>
	    </div><!-- #secondary .widget-area -->
<?php
	}
    }
?>
