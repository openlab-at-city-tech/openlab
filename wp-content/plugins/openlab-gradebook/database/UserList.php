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
    public function oplb_user_list() {
        global $wpdb;
        $wpdb->show_errors();
        $method = (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : $_SERVER['REQUEST_METHOD'];
        switch ($method) {
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