<?php

/**
 * Cell API
 */
class gradebook_cell_API
{
    public function __construct()
    {
        add_action('wp_ajax_cell', array($this, 'cell'));
    }

    public function cell()
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

        switch ($params['method']) {
            case 'DELETE':
                echo json_encode(array('delete' => 'deleting'));
                break;
            case 'PUT':
			case 'POST':
                $is_null = 1;

                if (is_numeric($params['assign_points_earned'])) {
                    $is_null = 0;
                }

                $values = array(
                    'assign_order' => $params['assign_order'],
                    'assign_points_earned' => $params['assign_points_earned'],
                    'is_null' => $is_null,
                );
                $formats = array(
                    '%d',
                    '%f',
                    '%d',
                );

                if (!empty($params['comments'])) {
                    $values['comments'] = $params['comments'];
                    array_push($formats, '%s');
                }

                $wpdb->update(
                    "{$wpdb->prefix}oplb_gradebook_cells",
                    $values,
                    array(
                        'uid' => $params['uid'],
                        'amid' => $params['amid'],
                        'gbid' => $gbid,
                    ),
                    $formats,
                    array(
                        '%d',
                        '%d',
                        '%d',
                    )
                );

                $query = $wpdb->prepare("SELECT assign_points_earned FROM {$wpdb->prefix}oplb_gradebook_cells WHERE uid = %d AND amid = %d AND gbid = %d", $params['uid'], $params['amid'], $gbid);
                $assign_points_earned = $wpdb->get_row($query, ARRAY_A);

                $current_grade_average = $oplb_gradebook_api->oplb_gradebook_get_current_grade_average($params['uid'], $gbid);

                $data_back = array(
                    'current_grade_average' => $current_grade_average,
                    'assign_points_earned' => floatval($assign_points_earned['assign_points_earned']),
                    'uid' => intval($params['uid']),
                    'is_null' => $is_null,
                );

                echo json_encode($data_back);
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
        }
        die();
    }
}
