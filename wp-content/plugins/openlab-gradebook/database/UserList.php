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

                //pass blog id to filter
                $blog_id = get_current_blog_id();

                //for now we're going to target 'author' users
                $args = array(
                    'role__in' => array('author'),
                );

                $users = get_users($args);

                if(!empty($users)){

                    //reset outgoing array
                    $students_out = array();

                    foreach ($users as $user){

                        $this_user = new stdClass;
                        
                        $user_meta = $oplb_gradebook_api->oplb_gradebook_get_user_meta($user);
                        $this_user->first_name = $user_meta['first_name'];
                        $this_user->last_name = $user_meta['last_name'];
                        $this_user->user_login = $user->user_login;

                        array_push($students_out, $this_user);

                    }
                }

                $students_out = apply_filters('oplb_gradebook_students_list', $students_out, $blog_id);
                
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