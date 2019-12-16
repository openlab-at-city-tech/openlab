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
      <p><?php 
_e( 'Actually, there are no settings for the Block element. Hoorah! This is because all our block functionality runs behind the scenes. Hows does it work? When a user blocks another, they will no longer be able to receive private messages, view their activity, view their comments or see their profile. And of course, vice versa. This brings the same kind of blocking system seen on other social media sites such as Facebook or Twitter, to BuddyPress.', 'bp-toolkit' );
?></p>
      <p><?php 
printf( wp_kses( 'This version also begins to integrate Block, Suspend, Report with other 3rd-party plugins. If you have any such plugins active, you may see further settings below. Please head to the <a href="%s" target="_blank">plugin homepage</a> to see our current integrations.', 'bp-toolkit' ), BP_TOOLKIT_HOMEPAGE );
?></p>
    </div>
    <?php 
?>

    <p class="bptk-docs-link"><a href="<?php 
echo  BP_TOOLKIT_SUPPORT . 'bsr/block-settings' ;
?>" target="_blank"><?php 
_e( 'Need Help? See docs on "Block Settings"', 'bp-toolkit' );
?><span class="dashicons dashicons-editor-help"></span></a></p>
  </div>
</div>
