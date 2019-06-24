<?php

class OPLB_USER
{

    public function __construct()
    {
        add_action('wp_ajax_oplb_user', array($this, 'oplb_user'));
        add_action('wp_ajax_oplb_student_grades', array($this, 'oplb_student_grades'));
    }

    public function oplb_user()
    {
        global $wpdb, $oplb_gradebook_api;
        $wpdb->show_errors();

        $params = $oplb_gradebook_api->oplb_gradebook_get_params();
        $gbid = $params['gbid'];

        //user check - only instructors allowed in
        if ($oplb_gradebook_api->oplb_gradebook_get_user_role_by_gbid($gbid) !== 'instructor') {
            echo json_encode(array("status" => "Not Allowed."));
            die();
        }

        //nonce check
        if (!wp_verify_nonce($params['nonce'], 'oplb_gradebook')) {
            echo json_encode(array("status" => "Authentication error."));
            die();
        }

        switch ($params['method']) {
            case 'DELETE':
                parse_str($_SERVER['QUERY_STRING'], $params);
                $x = $params['id'];
                $y = $params['gbid'];

                $results1 = $wpdb->delete("{$wpdb->prefix}oplb_gradebook_users", array('uid' => $x, 'gbid' => $y));
                $results2 = $wpdb->delete("{$wpdb->prefix}oplb_gradebook_cells", array('uid' => $x, 'gbid' => $y));

                break;
            case 'PUT':
                echo json_encode(array("put" => "putting"));
                die();
                break;
            case 'UPDATE':
                echo json_encode(array("update" => "updating"));
                break;
            case 'PATCH':
                echo json_encode(array("patch" => "patching"));
                break;
            case 'GET':
                echo json_encode(array("get" => "getting"));
                break;
            case 'POST':
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

    public function oplb_student_grades()
    {
        global $wpdb, $oplb_gradebook_api;
        $wpdb->show_errors();

        $params = $oplb_gradebook_api->oplb_gradebook_get_params();

        $gbid = $params['gbid'];
        $uid = $params['uid'];

        //user check - only instructors allowed in
        if ($oplb_gradebook_api->oplb_gradebook_get_user_role_by_gbid($gbid) !== 'instructor') {
            echo json_encode(array("status" => "Not Allowed."));
            die();
        }

        //nonce check
        if (!wp_verify_nonce($params['nonce'], 'oplb_gradebook')) {
            echo json_encode(array("status" => "Authentication error."));
            die();
        }

        $target_column = 'mid_semester_grade';
        $target_comments = 'mid_semester_comments';
        if ($params['type'] === 'final') {
            $target_column = 'final_grade';
            $target_comments = 'final_comments';
        }

        $values = array(
            $target_column => $params['grade'],
        );

        $formats = array(
            '%s',
        );

        if (!empty($params['comment_edit'])) {
            if (!empty($params['comments'])) {
                $values[$target_comments] = $params['comments'];
                array_push($formats, '%s');
            } else {
                $values[$target_comments] = null;
                array_push($formats, '%s');
            }
        }

        $wpdb->update(
            "{$wpdb->prefix}oplb_gradebook_users",
            $values,
            array(
                "uid" => $uid,
                "gbid" => $gbid,
            ),
            $formats,
            array(
                '%d',
                '%d',
            )
        );

        wp_send_json('success!');

        wp_die();
    }

}
