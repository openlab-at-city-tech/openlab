<?php

class OPLB_COURSE_LIST {

    public function __construct() {
        add_action('wp_ajax_course_list', array($this, 'course_list'));
    }

    public function course_list() {
        global $wpdb, $oplb_gradebook_api;

        $params = $oplb_gradebook_api->oplb_gradebook_get_params();
        $id = $gbid = $params['gbid'];

        //because the GET request selects all courses, we go with the generic user check
        if ($oplb_gradebook_api->oplb_gradebook_get_user_role() !== 'instructor'
                && $oplb_gradebook_api->oplb_gradebook_get_user_role() !== 'student') {
            echo json_encode(array("status" => "Not Allowed."));
            die();
        }

        //nonce check
        if (!wp_verify_nonce($params['nonce'], 'oplb_gradebook')) {
            echo json_encode(array("status" => "Authentication error."));
            die();
        }

        $wpdb->show_errors();
        switch ($params['method']) {
            case 'DELETE' :
                echo json_encode(array("delete" => "deleting"));
                break;
            case 'PUT' :
                echo json_encode(array("put" => "putting"));
                break;
            case 'UPDATE' :
                echo json_encode(array("update" => "updating"));
                break;
            case 'PATCH' :
                echo json_encode(array("patch" => "patching"));
                break;
            case 'GET' :
                $user_id = wp_get_current_user()->ID;
                $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_courses WHERE id IN ( SELECT gbid FROM {$wpdb->prefix}oplb_gradebook_users WHERE uid = %d )", $user_id);
                $courses = $wpdb->get_results($query, ARRAY_A);
                foreach ($courses as &$course) {
                    $course['id'] = intval($course['id']);
                    $course['year'] = intval($course['year']);
                }
                echo json_encode(array('course_list' => $courses));
                break;
            case 'POST' :
                echo json_encode(array("post" => "posting"));
                break;
                break;
        }
        die();
    }

}

?>