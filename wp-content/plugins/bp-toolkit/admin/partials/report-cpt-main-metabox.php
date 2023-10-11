<?php

/**
 * The file that renders the content of the Report Custom Post Type metabox.
 *
 * @link       https://www.bouncingsprout.com
 * @since      2.0
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/**
 * Are we on the add new post screen?
 *
 * @since 2.0
 */
function bptk_is_add_screen()
{
    global  $pagenow ;
    if ( 'post-new.php' === $pagenow ) {
        return true;
    }
}

function get_username( $id )
{
    $user_info = get_userdata( $id );
    $user_name = $user_info->display_name;
    return $user_name;
}

update_post_meta( get_the_id(), 'is_read', 1 );
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

			<?php 

if ( bptk_is_add_screen() || empty($post->_bptk_member_reported) ) {
    echo  '<span>Enter ID of user to report: </span>' ;
    echo  '<input style="width: 75px;" id="_bptk_member_reported" name="_bptk_member_reported" type="text" value="">' ;
} else {
    echo  '<span class="bptk-userid-input-label">' ;
    echo  bp_core_fetch_avatar( array(
        'item_id' => $post->_bptk_member_reported,
    ) ) ;
    echo  '<span class="bptk-userid-input-label-username">' ;
    echo  get_username( $post->_bptk_member_reported ) ;
    echo  '</span></span>' ;
    echo  '<span class="bptk-field-description">' . sprintf( __( 'The member who has been reported. Go to this member\'s <a href="%s">profile page</a>.', 'bp-toolkit' ), esc_url( get_edit_user_link( $post->_bptk_member_reported ) ) ) . '</span>' ;
}

?>

        </fieldset>

        <fieldset class="bptk-field-wrap _bptk_reported_by_field">
            <span class="bptk-field-label"><?php 
_e( 'Reported By', 'bp-toolkit' );
?></span>
            <legend class="screen-reader-text"><?php 
_e( 'Reported By', 'bp-toolkit' );
?></legend>

			<?php 

if ( bptk_is_add_screen() || empty($post->_bptk_reported_by) ) {
    $user = wp_get_current_user();
    echo  '<span class="bptk-userid-input-label">' ;
    echo  bp_core_fetch_avatar( array(
        'item_id' => $user->ID,
    ) ) ;
    echo  '<span class="bptk-userid-input-label-username">' ;
    echo  $user->display_name ;
    echo  '</span></span>' ;
    echo  '<input name="_bptk_reported_by" type="hidden" value="' . get_current_user_id() . '">' ;
} else {
    echo  '<span class="bptk-userid-input-label">' ;
    echo  bp_core_fetch_avatar( array(
        'item_id' => $post->_bptk_reported_by,
    ) ) ;
    echo  '<span class="bptk-userid-input-label-username">' ;
    echo  get_username( $post->_bptk_reported_by ) ;
    echo  '</span></span>' ;
    if ( get_current_user_id() != $post->_bptk_reported_by ) {
        echo  '<span class="bptk-field-description">' . sprintf( __( 'The member who made the report. Go to this member\'s <a href="%s">profile page</a>.', 'bp-toolkit' ), esc_url( get_edit_user_link( $post->_bptk_reported_by ) ) ) . '</span>' ;
    }
}

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
        echo  ( $post->_bptk_link ? esc_url( $post->_bptk_link ) : '' ) ;
        ?>"
                       target="_blank"><?php 
        echo  ( $post->_bptk_link ? urldecode( $post->_bptk_link ) : '' ) ;
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
            echo  '<img src="' . esc_url( $post->_bptk_meta ) . '">' ;
        } else {
            echo  ( '<span>' . $post->_bptk_meta ? esc_html( $post->_bptk_meta ) : '' . '</span>' ) ;
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

if ( bptk_is_add_screen() ) {
    echo  '<span class="bptk-userid-input-label"><span class="bptk-userid-input-label-username">Admin Created Report</span></span>' ;
    echo  '<input name="_bptk_activity_type" type="hidden" value="member">' ;
    echo  '<input name="_bptk_admin_created" type="hidden" value="1">' ;
} elseif ( $post->_bptk_admin_created == 1 ) {
    // This is an admin created report, so we just need to show that instead
    echo  '<span class="bptk-userid-input-label"><span class="bptk-userid-input-label-username">Admin Created Report</span></span>' ;
    echo  '<span class="bptk-field-description">' . __( 'This report was created by an administrator, without a specific item.', 'bp-toolkit' ) . '</span>' ;
} else {
    switch ( $post->_bptk_activity_type ) {
        case 'member':
            $type = "Member";
            break;
        case 'comment':
            $type = "Comment";
            break;
        case 'activity':
            $type = "Activity";
            break;
        case 'activity-comment':
            $type = "Activity Comment";
            break;
        case 'group':
            $type = "Group";
            break;
        case 'message':
            $type = "Private Message";
            break;
        case 'forum-topic':
            $type = "Forum Topic";
            break;
        case 'forum-reply':
            $type = "Forum Reply";
            break;
        case 'rtmedia':
            $type = "Media Upload";
            break;
        default:
            $type = "Member";
            break;
    }
    echo  '<span class="bptk-userid-input-label"><span class="bptk-userid-input-label-username">' . $type . '</span></span>' ;
    echo  '<span class="bptk-field-description">' . sprintf( __( 'In the free edition, only members themselves can be reported. Please <a href="%s">upgrade now</a> to the Pro Edition to report specific activities, including comments, private messages, activity updates and groups.', 'bp-toolkit' ), esc_url( bptk_fs()->get_upgrade_url() ) ) . '</span>' ;
}

?>

        </fieldset>
        <fieldset class="bptk-field-wrap _bptk_report_comments_field">
            <span class="bptk-field-label"><?php 
_e( 'Details of Report', 'bp-toolkit' );
?></span>
            <legend class="screen-reader-text"><?php 
_e( 'Details of Report', 'bp-toolkit' );
?></legend>
            <textarea class="bptk-textarea" id="post_content" rows="10" value=""
                      name="post_content"><?php 
echo  ( $post->post_content ? esc_textarea( $post->post_content ) : '' ) ;
?></textarea>

			<?php 
if ( !isset( $post->_bptk_admin_created ) ) {
    echo  '<span class="bptk-field-description">' . __( 'Members can explain why they made the report. In some cases, this is not necessary, such as when reporting an image.', 'bp-toolkit' ) . '</span>' ;
}
?>

        </fieldset>
        <p class="bptk-docs-link"><a href="<?php 
echo  BP_TOOLKIT_SUPPORT ;
?>"
                                     target="_blank"><?php 
_e( 'Need Help? See docs on "The Reports Screen"', 'bp-toolkit' );
?><span class="dashicons dashicons-editor-help"></span></a></p>
    </div>
</div>
