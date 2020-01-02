<?php

/**
* Displays the report main metabox.
*
* @since 2.0
*/

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

?>

<div id="bptk-report-settings" class="bptk-box">
  <h3 class="bptk-box-header"><?php 
_e( 'Report Settings', 'bp-toolkit' );
?></h3>
  <div id="" class="panel bptk-metabox bptk-no-tabs">
    <div id="" class="panel bptk-metabox bptk-no-tabs">
      <div class="bptk-metabox-header">
        <?php 
?>
          <p><?php 
_e( 'The Report service allows your members to report others. They can find a \'Report\' button on every members profile page. Use the \'All Reports\' menu item to see your member\'s reports. In the pro edition, your members can report a much wider range of content types, including private messages, activity updates and comments, and groups. They can even report content from other 3rd-party plugins such as rtMedia uploads and bbPress forum topics and replies. The Pro Edition also allows you to specify your own report types (offensive, abusive, etc), or edit the default ones. Finally, Pro Edition users can create their own reports, which comes in handy when they receive a report of bad behaviour via email or message, rather than by a member hitting the \'Report\' button.', 'bp-toolkit' );
?></p>
        <?php 
?>
        <p><?php 
printf( wp_kses( 'This version also begins to integrate Block, Suspend, Report with other 3rd-party plugins. If you have any such plugins active, you may see further settings below. Please head to the <a href="%s" target="_blank">plugin homepage</a> to see our current integrations.', 'bp-toolkit' ), BP_TOOLKIT_HOMEPAGE );
?></p>
      </div>
      <form method="post" action="options.php">
        <?php 
do_settings_sections( 'report_section' );
settings_fields( 'report_section' );
submit_button();
?>
      </form>
      <p class="bptk-docs-link"><a href="<?php 
echo  BP_TOOLKIT_SUPPORT . 'bsr/report-settings' ;
?>" target="_blank"><?php 
_e( 'Need Help? See docs on "Report Settings"', 'bp-toolkit' );
?><span class="dashicons dashicons-editor-help"></span></a></p>
    </div>
  </div>
