<?php

/**
* The file that renders the content of the Report Custom Post Type metabox.
*
* @link       https://www.therealbenroberts.com
* @since      2.0
*/

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/**
* Are we on the edit screen? If yes, return disabled
*
* @since 2.0
*/
function bptk_is_edit_screen()
{
    global  $pagenow ;
    global  $post_type ;
    if ( 'post.php' === $pagenow ) {
        return 'disabled';
    }
}

/**
* Are we on the add new post screen?
*
* @since 2.0
*/
function bptk_is_add_screen()
{
    global  $pagenow ;
    global  $post_type ;
    if ( 'post-new.php' === $pagenow ) {
        return true;
    }
}

/**
* Render a list of all users.
*
* @since 2.0
*/
function get_bptk_users( $arg )
{
    $dropdown_html_users = '<option value="">Select a user</option>' . "\n";
    $users = get_users();
    $post = get_post( get_the_ID() );
    
    if ( $arg == 'reported' ) {
        $stored_user = $post->_bptk_member_reported;
    } elseif ( $arg == 'reporting' ) {
        $stored_user = $post->_bptk_reported_by;
    }
    
    foreach ( $users as $user ) {
        $dropdown_html_users .= '<option value="' . esc_html( $user->ID ) . '"' . selected( $stored_user, $user->ID, false ) . '>' . esc_html( $user->display_name ) . '</option>' . "\n";
    }
    return $dropdown_html_users;
}

?>

<div class="bptk-metabox-panel-wrap">

  <div id="" class="panel bptk-metabox bptk-no-tabs">
    <fieldset class="bptk-field-wrap _bptk_member_reported_field">
      <span class="bptk-field-label"><?php 
_e( 'Member Reported', 'bp-toolkit' );
?></span>
      <legend class="screen-reader-text"><?php 
_e( 'Member Reported', 'bp-toolkit' );
?></legend>
      <select class="bptk-userlist" style="width: 50%;" id="_bptk_member_reported" name="_bptk_member_reported" <?php 
echo  bptk_is_edit_screen() ;
?>><?php 
echo  get_bptk_users( 'reported' ) ;
?></select>
      <?php 
echo  '<span class="bptk-field-description">' . __( 'The member who has been reported.', 'bp-toolkit' ) . '</span>' ;
?>
      </fieldset>
      <fieldset class="bptk-field-wrap _bptk_reported_by_field">
        <span class="bptk-field-label"><?php 
_e( 'Reported By', 'bp-toolkit' );
?></span>
        <legend class="screen-reader-text"><?php 
_e( 'Reported By', 'bp-toolkit' );
?></legend>
        <select class="bptk-userlist" style="width: 50%;" id="_bptk_reported_by" name="_bptk_reported_by" <?php 
echo  bptk_is_edit_screen() ;
?>><?php 
echo  get_bptk_users( 'reporting' ) ;
?></select>
        <?php 
echo  '<span class="bptk-field-description">' . __( 'The member who made this report.', 'bp-toolkit' ) . '</span>' ;
?>
        </fieldset>
        <?php 

if ( !bptk_is_add_screen() ) {
    ?>
          <?php 
    
    if ( $post->_bptk_link ) {
        ?>
            <fieldset class="bptk-field-wrap _bptk_link_field">
              <span class="bptk-field-label"><?php 
        _e( 'Link to Reported Content', 'bp-toolkit' );
        ?></span>
              <legend class="screen-reader-text"><?php 
        _e( 'Link to Reported Content', 'bp-toolkit' );
        ?></legend>
              <a href="<?php 
        echo  ( $post->_bptk_link ? sanitize_text_field( $post->_bptk_link ) : '' ) ;
        ?>" target="_blank"><?php 
        echo  ( $post->_bptk_link ? sanitize_text_field( $post->_bptk_link ) : '' ) ;
        ?></a>
              <span class="bptk-field-description"><?php 
        _e( 'Click the link to be taken to the content that has been reported.', 'bp-toolkit' );
        ?></span>
            </fieldset>
          <?php 
    }
    
    ?>
          <?php 
    
    if ( $post->_bptk_meta ) {
        ?>
            <fieldset class="bptk-field-wrap _bptk_meta_field">
              <span class="bptk-field-label"><?php 
        _e( 'Reported Content', 'bp-toolkit' );
        ?></span>
              <legend class="screen-reader-text"><?php 
        _e( 'Reported Content', 'bp-toolkit' );
        ?></legend>
              <?php 
        
        if ( $post->_bptk_activity_type == 'rtmedia' ) {
            echo  '<img src="' . $post->_bptk_meta . '">' ;
        } else {
            echo  ( '<span>' . $post->_bptk_meta ? sanitize_text_field( $post->_bptk_meta ) : '' . '</span>' ) ;
        }
        
        ?>
              <span class="bptk-field-description"><?php 
        _e( 'The content that this report relates to.', 'bp-toolkit' );
        ?></span>
            </fieldset>
          <?php 
    }
    
    ?>

        <?php 
}

?>

        <fieldset class="bptk-field-wrap _bptk_activity_type_field">
          <span class="bptk-field-label"><?php 
_e( 'Content Type', 'bp-toolkit' );
?></span>
          <legend class="screen-reader-text"><?php 
_e( 'Content Type', 'bp-toolkit' );
?></legend>
          <?php 
?>
            <ul class="bptk-radios">
              <li><label disabled><input name="" value="" type="radio" style="" class="" disabled CHECKED><?php 
_e( ' Member', 'bp-toolkit' );
?></label></li>
            </ul>
          <?php 
?>
          <?php 
echo  '<span class="bptk-field-description">' . sprintf( __( 'In the free edition, only members themselves can be reported. Please <a href="%s">upgrade now</a> to the Pro Edition to report specific activities, including comments, private messages, activity updates and groups.', 'bp-toolkit' ), esc_url( bptk_fs()->get_upgrade_url() ) ) . '</span>' ;
?>
          </fieldset>
          <fieldset class="bptk-field-wrap _bptk_report_comments_field">
            <span class="bptk-field-label"><?php 
_e( 'Details of Report', 'bp-toolkit' );
?></span>
            <legend class="screen-reader-text"><?php 
_e( 'Details of Report', 'bp-toolkit' );
?></legend>
            <textarea class="bptk-textarea" id="post_content" rows="10" value="" name="post_content"><?php 
echo  ( $post->post_content ? esc_textarea( $post->post_content ) : '' ) ;
?></textarea>
            <span class="bptk-field-description"><?php 
_e( 'Members can explain why they made the report. In some cases, this is not necessary, such as when reporting an image.', 'bp-toolkit' );
?></span>
          </fieldset>
          <p class="bptk-docs-link"><a href="<?php 
echo  BP_TOOLKIT_SUPPORT . 'bsr/report-screen' ;
?>" target="_blank"><?php 
_e( 'Need Help? See docs on "The Reports Screen"', 'bp-toolkit' );
?><span class="dashicons dashicons-editor-help"></span></a></p>
        </div>
      </div>
