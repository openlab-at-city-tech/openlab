<?php

/**
* Displays the suspend main metabox.
*
* @since 2.0
*/

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

?>

<div id="bptk-suspend-settings" class="bptk-box">
  <h3 class="bptk-box-header"><?php 
_e( 'Suspend Settings', 'bp-toolkit' );
?></h3>
  <div id="" class="panel bptk-metabox bptk-no-tabs">
    <div class="bptk-metabox-header">
      <?php 
?>
        <p><?php 
_e( 'With the suspend service, you can use the suspend button on your member\'s profile pages to prevent them from logging in. If they are currently using your site, the plugin will detect that and end their session. Use the setting below to create the error message they receive when they try to log in. Pro Edition users can choose to send an email to the suspended user, to advise them of the suspension, and instructions for rectifying the situation.', 'bp-toolkit' );
?></p>
      <?php 
?>
      <p><?php 
printf( wp_kses( 'This version also begins to integrate Block, Suspend, Report with other 3rd-party plugins. If you have any such plugins active, you may see further settings below. Please head to the <a href="%s" target="_blank">plugin homepage</a> to see our current integrations.', 'bp-toolkit' ), BP_TOOLKIT_HOMEPAGE );
?></p>
    </div>
    <form method="post" action="options.php">
      <?php 
do_settings_sections( 'suspend_section' );
settings_fields( 'suspend_section' );
submit_button();
?>
    </form>
    <p class="bptk-docs-link"><a href="<?php 
echo  BP_TOOLKIT_SUPPORT . 'bsr/suspend-settings' ;
?>" target="_blank"><?php 
_e( 'Need Help? See docs on "Suspend Settings"', 'bp-toolkit' );
?><span class="dashicons dashicons-editor-help"></span></a></p>
  </div>
</div>
