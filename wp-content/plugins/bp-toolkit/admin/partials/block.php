<?php

/**
 * Displays the block main metabox.
 *
 * @since 2.0
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

?>

<div id="bptk-block-settings" class="bptk-box">
    <h3 class="bptk-box-header"><?php 
_e( 'Block Settings', 'bp-toolkit' );
?></h3>
    <div id="" class="panel bptk-metabox bptk-no-tabs">
        <div class="bptk-metabox-header">
            <p>Pro users are able to choose where to place block buttons, for example, on member profile pages, or the
                member directory.</p>
        </div>
		<?php 
?>

        <p class="bptk-docs-link"><a href="<?php 
echo  BP_TOOLKIT_SUPPORT ;
?>"
                                     target="_blank"><?php 
_e( 'Need Help? See docs on "Block Settings"', 'bp-toolkit' );
?><span class="dashicons dashicons-editor-help"></span></a></p>
    </div>
</div>
