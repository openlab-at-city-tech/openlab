<?php

/**
 * Assignment API
 */
class gradebook_assignment_API
{

    public function __construct()
    {
        add_action('wp_ajax_assignment', array($this, 'assignment'));
    }

    public function assignment()
    {
        global $wpdb, $oplb_gradebook_api;

        $params = $oplb_gradebook_api->oplb_gradebook_get_params();
        $gbid = $params['gbid'];

        //user check - only instructors allowed in
        if ($oplb_gradebook_api->oplb_gradebook_get_user_role_by_gbid($gbid) != 'instructor') {
            echo json_encode(array("status" => "Not Allowed."));
            die();
        }

        //nonce check
        if (!wp_verify_nonce($params['nonce'], 'oplb_gradebook')) {
            echo json_encode(array("status" => "Authentication error."));
            die();
        }

        $wpdb->show_errors();
        
        //trim assignment_category to prevent downstream spacing issues
        if (!empty($params['assign_category'])) {
            $params['assign_category'] = trim($params['assign_category']);
        }

        switch ($params['method']) {
            case 'DELETE':

                $id = $params['id'];

                $wpdb->delete(
                    "{$wpdb->prefix}oplb_gradebook_cells",
                    array(
                        'amid' => $id,
                        'gbid' => $gbid
                    )
                );
                $wpdb->delete(
                    "{$wpdb->prefix}oplb_gradebook_assignments",
                    array(
                        'id' => $id,
                        'gbid' => $gbid
                    )
                );

                $return_data = array('id' => $id);

                $student_data = $oplb_gradebook_api->oplb_gradebook_update_all_student_current_grade_averages($params['gbid']);

                if (!empty($student_data)) {
                    $return_data['student_grade_update'] = $student_data;
                }
                
                //get the total weight
                $weight_return = $oplb_gradebook_api->oplb_gradebook_get_total_weight($gbid, array());
                $return_data['distributed_weight'] = $weight_return['distributed_weight'];

                echo json_encode($return_data);
                break;
            case 'PUT':

                $query = $wpdb->prepare("SELECT assign_weight FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE id = %d AND gbid = %d", $params['id'], $gbid);
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
                    'id' => $params['id'],
                    'gbid' => $gbid,
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
                    '%d',
                ));

                $is_null = 1;

                if ($params['assign_grade_type'] === 'checkmark') {
                    $is_null = 0;
                }

                $oplb_gradebook_api->oplb_gradebook_update_cells_by_assignment($params['id'], $gbid, $params['assign_order']);

                $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE id = %d AND gbid = %d", $params['id'], $gbid);
                $assignment = $wpdb->get_row($query, ARRAY_A);

                //get the total weight
                $weight_return = $oplb_gradebook_api->oplb_gradebook_get_total_weight($gbid, array());

                $assignment['id'] = intval($assignment['id']);
                $assignment['gbid'] = intval($assignment['gbid']);
                $assignment['assign_order'] = intval($assignment['assign_order']);
                $assignment['total_weight'] = $weight_return['total_weight'];
                $assignment['distributed_weight'] = $weight_return['distributed_weight'];

                //if weight changed, update students
                if ($current_weight !== $incoming_weight) {

                    $student_data = $oplb_gradebook_api->oplb_gradebook_update_all_student_current_grade_averages($assignment['gbid']);

                    if (!empty($student_data)) {
                        $assignment['student_grade_update'] = $student_data;
                    }
                }

                echo json_encode($assignment);
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
                $query = $wpdb->prepare("SELECT assign_order FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE gbid = %d", $params['gbid']);
                $assignOrders = $wpdb->get_col($query);
                if (!$assignOrders) {
                    $assignOrders = array(0);
                }
                $assignOrder = max($assignOrders) + 1;
                
                //handle values that cannot be NULL
                if (!$params['assign_weight']) {
                    $params['assign_weight'] = 0;
                }

                if (!$params['assign_date']) {
                    $params['assign_date'] = date('Y-m-d');
                }

                if (!$params['assign_due']) {
                    $params['assign_due'] = date('0000-00-00');
                }

                if (!$params['assign_category']) {
                    $params['assign_category'] = 'uncategorized';
                }

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
                ), array('%s', '%s', '%s', '%s', '%s', '%s', '%f', '%d', '%d'));

                $assignID = $wpdb->insert_id;

                $query = $wpdb->prepare("SELECT uid FROM {$wpdb->prefix}oplb_gradebook_users WHERE gbid = %d AND role = %s", $params['gbid'], 'student');
                $studentIDs = $wpdb->get_results($query, ARRAY_N);

                $is_null = 1;

                if ($params['assign_grade_type'] === 'checkmark') {
                    $is_null = 0;
                }

                foreach ($studentIDs as $value) {
                    $wpdb->insert("{$wpdb->prefix}oplb_gradebook_cells", array(
                        'amid' => $assignID,
                        'uid' => $value[0],
                        'gbid' => $params['gbid'],
                        'assign_order' => $assignOrder,
                        'assign_points_earned' => 0,
                        'is_null' => $is_null,
                    ), array('%d', '%d', '%d', '%d', '%f', '%d'));
                }
                $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE id = %d AND gbid = %d", $assignID, $gbid);
                $assignment = $wpdb->get_row($query, ARRAY_A);
                $assignment['assign_order'] = intval($assignment['assign_order']);
                $assignment['gbid'] = intval($assignment['gbid']);
                $assignment['id'] = intval($assignment['id']);

                //get the total weight
                $weight_return = $oplb_gradebook_api->oplb_gradebook_get_total_weight($assignment['gbid'], array());
                $assignment['total_weight'] = $weight_return['total_weight'];
                $assignment['distributed_weight'] = $weight_return['distributed_weight'];

                $student_data = $oplb_gradebook_api->oplb_gradebook_update_all_student_current_grade_averages($assignment['gbid']);

                if (!empty($student_data)) {
                    $assignment['student_grade_update'] = $student_data;
                }

                $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_cells WHERE amid = %d AND gbid = %d", $assignID, $gbid);
                $cells = $wpdb->get_results($query, ARRAY_A);
                foreach ($cells as &$cell) {
                    $cell['amid'] = intval($cell['amid']);
                    $cell['uid'] = intval($cell['uid']);
                    $cell['assign_order'] = intval($cell['assign_order']);
                    $cell['assign_points_earned'] = floatval($cell['assign_points_earned']);
                    $cell['gbid'] = intval($cell['gbid']);
                    $cell['id'] = intval($cell['id']);
                    $cell['is_null'] = boolval(intval($cell['is_null']));
                    $cell['comments'] = !empty($cell['comments']) ? sanitize_text_field($cell['comments']) : false;
                }
                $data = array('assignment' => $assignment, 'cells' => $cells);
                echo json_encode($data);
                break;
        }
        die();
    }

}

?>