<?php

ob_start();
// ToDo - temporary fix for headers sent issue when deleting from wp_list_table implementation
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
/**
 * The class that looks after the Toolkit's Moderate function
 */
class BPTK_Moderate
{
    /**
     * Initialize the class and set its properties.
     */
    public function __construct()
    {
        if ( class_exists( 'BuddyPress' ) ) {
            add_filter( 'bp_get_activity_css_class', array( $this, 'add_css' ) );
        }
    }
    
    /**
     * Add a CSS class to any activity that is moderated.
     *
     * @param $class
     *
     * @return mixed|string
     */
    public function add_css( $class )
    {
        global  $activities_template ;
        $moderated_activity_updates = bptk_get_moderated_list( 'activity' );
        $moderated_group_updates = bptk_get_moderated_list( 'groups' );
        $moderated_items = array_merge( $moderated_activity_updates, $moderated_group_updates );
        if ( in_array( $activities_template->activity->id, (array) $moderated_items ) ) {
            $class .= ' moderated';
        }
        return $class;
    }
    
    /**
     * Ajax function to handle an approval button press.
     *
     * @return void
     */
    public function unmoderate_activity()
    {
        
        if ( !wp_verify_nonce( $_POST['nonce'], 'unmoderate_activity' ) ) {
            $error = new WP_Error( '100', __( 'Something went wrong.', 'bp-toolkit' ) );
            wp_send_json_error( $error );
        }
        
        $activity_id = intval( $_POST['activity_id'] );
        bptk_unmoderate_activity( $activity_id, 'activity' );
        $return = array(
            'message' => __( 'Activity approved', 'bp-toolkit' ),
            'id'      => $activity_id,
        );
        wp_send_json_success( $return );
    }

}