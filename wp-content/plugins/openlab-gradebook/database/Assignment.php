<?php

class gradebook_assignment_API {

    public function __construct() {
        add_action('wp_ajax_assignment', array($this, 'assignment'));
    }

    public function assignment() {
        global $wpdb, $oplb_gradebook_api;
        $wpdb->show_errors();
        $method = (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'DELETE' :
                parse_str($_SERVER['QUERY_STRING'], $params);
                $id = $params['id'];
                
                $gbid = $wpdb->get_var("SELECT gbid FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE id = $id");
                if ($oplb_gradebook_api->oplb_gradebook_get_user_role($gbid) != 'instructor') {
                    echo json_encode(array("status" => "Not Allowed."));
                    die();
                }
                $wpdb->delete("{$wpdb->prefix}oplb_gradebook_cells", array('amid' => $id));
                $wpdb->delete("{$wpdb->prefix}oplb_gradebook_assignments", array('id' => $id));

                $return_data = array('id' => $id);

                $student_data = $oplb_gradebook_api->oplb_gradebook_update_all_student_current_grade_averages($params['gbid']);

                if (!empty($student_data)) {
                    $return_data['student_grade_update'] = $student_data;
                }

                echo json_encode($return_data);
                break;
            case 'PUT' :
                $params = json_decode(file_get_contents('php://input'), true);
                $gbid = $params['gbid'];
                if ($oplb_gradebook_api->oplb_gradebook_get_user_role($gbid) != 'instructor') {
                    echo json_encode(array("status" => "Not Allowed."));
                    die();
                }

                $query = $wpdb->prepare("SELECT assign_weight FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE id = %d", $params['id']);
                $current_weight = $wpdb->get_var($query);
                $incoming_weight = $params['assign_weight'];

                $wpdb->update("{$wpdb->prefix}oplb_gradebook_assignments", array(
                    'assign_name' => $params['assign_name'],
                    'assign_date' => $params['assign_date'],
                    'assign_due' => $params['assign_due'],
                    'assign_order' => $params['assign_order'],
                    'assign_category' => $params['assign_category'],
                    'assign_visibility' => $params['assign_visibility'],
                    'assign_grade_type' => $params['assign_grade_type'],
                    'assign_weight' => $params['assign_weight'],
                        ), array(
                    'id' => $params['id']
                        ), array(
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%f',
                        ), array(
                    '%d',
                        )
                );
                $wpdb->update("{$wpdb->prefix}oplb_gradebook_cells", array(
                    'assign_order' => $params['assign_order']
                        ), array(
                    'amid' => $params['id']
                        ), array(
                    '%d',
                        ), array(
                    '%d',
                        )
                );
                $assignment = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE id = {$params['id']}", ARRAY_A);

                //get the total weight
                $weight_return = $oplb_gradebook_api->oplb_gradebook_get_total_weight();


                $assignment['id'] = intval($assignment['id']);
                $assignment['gbid'] = intval($assignment['gbid']);
                $assignment['assign_order'] = intval($assignment['assign_order']);
                $assignment['total_weight'] = $weight_return['total_weight'];

                //if weight changed, update students
                if ($current_weight !== $incoming_weight) {

                    $student_data = $oplb_gradebook_api->oplb_gradebook_update_all_student_current_grade_averages($assignment['gbid']);

                    if (!empty($student_data)) {
                        $assignment['student_grade_update'] = $student_data;
                    }
                }

                echo json_encode($assignment);
                break;
            case 'UPDATE' :
                echo json_encode(array("update" => "updating"));
                break;
            case 'PATCH' :
                echo json_encode(array("patch" => "patching"));
                break;
            case 'GET' :
                echo json_encode(array("get" => "getting"));
                break;
            case 'POST' :
                $params = json_decode(file_get_contents('php://input'), true);
                $gbid = $params['gbid'];
                if ($oplb_gradebook_api->oplb_gradebook_get_user_role($gbid) != 'instructor') {
                    echo json_encode(array("status" => "Not Allowed."));
                    die();
                }
                $assignOrders = $wpdb->get_col("SELECT assign_order FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE gbid = {$params['gbid']}");
                if (!$assignOrders) {
                    $assignOrders = array(0);
                }
                $assignOrder = max($assignOrders) + 1;
                $wpdb->insert("{$wpdb->prefix}oplb_gradebook_assignments", array(
                    'assign_name' => $params['assign_name'],
                    'assign_date' => $params['assign_date'],
                    'assign_due' => $params['assign_due'],
                    'assign_category' => $params['assign_category'],
                    'assign_visibility' => $params['assign_visibility'],
                    'assign_grade_type' => $params['assign_grade_type'],
                    'assign_weight' => $params['assign_weight'],
                    'gbid' => $params['gbid'],
                    'assign_order' => $assignOrder
                        ), array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d')
                );
                $assignID = $wpdb->insert_id;
                $studentIDs = $wpdb->get_results("SELECT uid FROM {$wpdb->prefix}oplb_gradebook_users WHERE gbid = {$params['gbid']} AND role = 'student'", ARRAY_N);
                foreach ($studentIDs as $value) {
                    $wpdb->insert("{$wpdb->prefix}oplb_gradebook_cells", array(
                        'amid' => $assignID,
                        'uid' => $value[0],
                        'gbid' => $params['gbid'],
                        'assign_order' => $assignOrder,
                        'assign_points_earned' => 0
                            ), array('%d', '%d', '%d', '%d', '%f')
                    );
                }
                $assignment = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE id = $assignID", ARRAY_A);
                $assignment['assign_order'] = intval($assignment['assign_order']);
                $assignment['gbid'] = intval($assignment['gbid']);
                $assignment['id'] = intval($assignment['id']);

                //get the total weight
                $weight_return = $oplb_gradebook_api->oplb_gradebook_get_total_weight();
                $assignment['total_weight'] = $weight_return['total_weight'];

                $student_data = $oplb_gradebook_api->oplb_gradebook_update_all_student_current_grade_averages($assignment['gbid']);
                
                if (!empty($student_data)) {
                    $assignment['student_grade_update'] = $student_data;
                }

                $cells = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_cells WHERE amid = $assignID", ARRAY_A);
                foreach ($cells as &$cell) {
                    $cell['amid'] = intval($cell['amid']);
                    $cell['uid'] = intval($cell['uid']);
                    $cell['assign_order'] = intval($cell['assign_order']);
                    $cell['assign_points_earned'] = floatval($cell['assign_points_earned']);
                    $cell['gbid'] = intval($cell['gbid']);
                    $cell['id'] = intval($cell['id']);
                }
                $data = array('assignment' => $assignment, 'cells' => $cells);
                echo json_encode($data);
                break;
        }
        die();
    }

}

?>