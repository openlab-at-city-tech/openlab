<?php
/**
 * The Right Alternative sidebar
 *
 * @since Twenty Ten weaver 1.5
 */
  // An alternative sidebar for the right sidebar template
    if (!weaver_replace_alternative('altright') && is_active_sidebar( 'alternative-widget-area' ) ) {
    printf('<div id="altright" class="widget-area" role="complementary">
    <ul class="xoxo">'."\n");
	dynamic_sidebar( 'alternative-widget-area' );
    printf("</ul>
</div><!-- #altright .widget-area -->\n");
    }
?>
