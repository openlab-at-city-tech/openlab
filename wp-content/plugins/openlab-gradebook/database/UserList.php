<?php

class OPLB_USER_LIST {

    public function __construct() {
        add_action('wp_ajax_oplb_user_list', array($this, 'oplb_user_list'));
    }

    /**
     * @todo: work out error messages a little better
     * @global type $wpdb
     * @global type $members_template
     */
    public function oplb_user_list($method = false) {
        global $wpdb, $oplb_gradebook_api;
        $wpdb->show_errors();
        $params = array();

        //if a method is passed, this means we are accessing the function directly
        //and don't need to perform ajax checks
        if ($method) {
            $params['method'] = $method;
        } else {
            $params = $oplb_gradebook_api->oplb_gradebook_get_params();

            //user check - only instructors allowed in
            if ($oplb_gradebook_api->oplb_gradebook_get_user_role() !== 'instructor') {
                echo json_encode(array("status" => "Not Allowed."));
                die();
            }

            //nonce check
            if (!wp_verify_nonce($params['nonce'], 'oplb_gradebook')) {
                echo json_encode(array("status" => "Authentication error."));
                die();
            }
        }

        switch ($params['method']) {
            case 'DELETE' :
                echo json_encode(array("delete" => "deleting"));
                die();
            case 'PUT' :
                echo json_encode(array("put" => "putting"));
                die();
            case 'UPDATE' :
                echo json_encode(array("update" => "updating"));
                break;
            case 'PATCH' :
                echo json_encode(array("patch" => "patching"));
                break;
            case 'GET' :
            case 'retrieve' :

                $students_out = array("error" => "no_students");

                //if budypress is not avaiable, return nothing
                if (!OPLB_BP_AVAILABLE) {
                    echo json_encode(array("error" => "no_bp"));
                    die();
                }

                //first we need to find the associated group for this site
                $blog_id = get_current_blog_id();

                $query = $wpdb->prepare("SELECT group_id FROM {$wpdb->groupmeta} WHERE meta_key = %s AND meta_value = %d", 'wds_bp_group_site_id', $blog_id);
                $results = $wpdb->get_results($query);

                if (!$results || empty($results)) {
                    echo json_encode(array("error" => "no_site"));
                    die();
                }

                $group_id = intval($results[0]->group_id);

                $member_arg = array(
                    'group_id' => $group_id,
                    'exclude_admins_mods' => true,
                );

                if (bp_group_has_members($member_arg)) :

                    //reset outgoing array
                    $students_out = array();

                    while (bp_group_members()) : bp_group_the_member();

                        global $members_template;
                        $member = $members_template->member;

                        //if buddypress is available, add xprofile fields
                        if (OPLB_BP_AVAILABLE) {

                            $member->xprofile_first_name = xprofile_get_field_data('First Name', $member->ID);
                            $member->xprofile_last_name = xprofile_get_field_data('Last Name', $member->ID);
                        }

                        array_push($students_out, $member);

                    endwhile;

                endif;

                if ($method === 'retrieve') {
                    return $students_out;
                }

                echo json_encode($students_out);
                die();
            case 'POST' :
                echo json_encode(array("post" => "posting"));
                die();
        }
        die();
    }

}

?>