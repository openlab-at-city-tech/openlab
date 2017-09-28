<?php

class OPLB_USER {

    public function __construct() {
        add_action('wp_ajax_oplb_user', array($this, 'oplb_user'));
    }

    public function oplb_user() {
        global $wpdb;
        $wpdb->show_errors();
        $method = (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'DELETE' :
                parse_str($_SERVER['QUERY_STRING'], $params);
                $x = $params['id'];
                $y = $params['gbid'];

                $results1 = $wpdb->delete("{$wpdb->prefix}oplb_gradebook_users", array('uid' => $x, 'gbid' => $y));
                $results2 = $wpdb->delete("{$wpdb->prefix}oplb_gradebook_cells", array('uid' => $x, 'gbid' => $y));

                break;
            case 'PUT' :
                global $oplb_gradebook_api;
                $params = json_decode(file_get_contents('php://input'), true);
                $ID = $params['id'];
                $first_name = $params['first_name'];
                $last_name = $params['last_name'];
                $results = $oplb_gradebook_api->oplb_gradebook_update_user($ID, $first_name, $last_name);
                echo json_encode($results);
                die();
                break;
            case 'UPDATE' :
                echo json_encode(array("update" => "updating"));
                break;
            case 'PATCH' :
                echo json_encode(array("patch" => "patching"));
                break;
            case 'GET' :
                global $oplb_gradebook_api;
                //This is not called anywhere... do we need it?
                $id = wp_get_current_user()->ID;
                $gbid = $_GET['gbid'];
                $results = $oplb_gradebook_api->oplb_gradebook_get_current_user($id, $gbid);
                echo json_encode($results);
                break;
            case 'POST' :
                $params = json_decode(file_get_contents('php://input'), true);
                $gbid = $params['gbid'];

                if ($params['student_range_option'] === 'studentAll') {

                    global $oplb_gradebook_api;
                    $results = $oplb_gradebook_api->oplb_gradebook_add_all_students($gbid);
                    wp_send_json($results);
                } else {

                    $first_name = $params['first_name'];
                    $last_name = $params['last_name'];
                    $id = null;
                    $user_login = $params['id-exists'];

                    //@todo: create client-side response for error messages
                    if (intval($user_login === 0)) {
                        echo 'No user submitted';
                        die();
                    }

                    global $oplb_gradebook_api;
                    $results = $oplb_gradebook_api->oplb_gradebook_create_user($id, $gbid, $first_name, $last_name, $user_login);
                    wp_send_json($results);
                }
        }
        die();
    }

}

?>