<?php
/**
 * The Left Alternative sidebar
 */
  // An alternative sidebar for the right sidebar template
    if (!weaver_replace_alternative('altleft') && is_active_sidebar( 'alternative-widget-area' ) ) {
    printf('<div id="altleft" class="widget-area" role="complementary">
    <ul class="xoxo">'."\n");
	dynamic_sidebar( 'alternative-widget-area' );
    printf("</ul>
</div><!-- #altleft .widget-area -->\n");
    }
?>
