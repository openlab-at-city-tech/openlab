<?php

class oplb_gradebook_api {

    public function build_sorter($key) {
        return function ($a, $b) use ($key) {
            return strnatcmp($a[$key], $b[$key]);
        };
    }

    /**
     * used to be able to update user meta here, but this has been deprecated
     * may come back in the future as a Gradebook-only feature
     * @param type $id
     * @param type $first_name
     * @param type $last_name
     * @return type
     */
    public function oplb_gradebook_update_user($id, $first_name, $last_name) {

        $user = get_user_by('id', $user_id);
        return array(
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'id' => $user_id
        );
    }

    public function get_line_chart($uid, $gbid) {
        global $wpdb;
        //need to check that user has access to this gradebook.		
        if (!is_user_logged_in()) {
            echo json_encode(array("status" => "Not Allowed."));
            die();
        }

        //@todo: find out what this does
        //$query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_cells WHERE uid = %d AND gbid = %d", $uid, $gbid);
        //$cells = $wpdb->get_results($query, ARRAY_A);	

        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_cells WHERE gbid = %d", $gbid);
        $class_cells = $wpdb->get_results($query, ARRAY_A);
        $cells = array_map(function($class_cell) use ($uid) {
            if ($class_cell['uid'] == $uid) {
                return $class_cell;
            }
        }, $class_cells);
        $cells = array_filter($cells);

        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE gbid = %d", $gbid);
        $assignments = $wpdb->get_results($query, ARRAY_A);

        $cells_points = array();
        $assignments_names = array();
        $assignment_averages = array();

        usort($cells, $this->build_sorter('assign_order'));
        usort($assignments, $this->build_sorter('assign_order'));

        foreach ($assignments as $assignment) {
            $assignment_cells_points = array_map(function($class_cell) use ($assignment) {
                if ($class_cell['amid'] == $assignment['id']) {
                    return floatval($class_cell['assign_points_earned']);
                }
            }, $class_cells);
            $assignment_cells_points = array_filter($assignment_cells_points);
            $total_points = array_sum($assignment_cells_points);
            array_push($assignment_averages, number_format($total_points / count($assignment_cells_points), 2));
        }

        $cells_points = array_map(function($cell) {
            return floatval($cell['assign_points_earned']);
        }, $cells);

        $assignments_names = array_map(function($assignment) {
            return $assignment['assign_name'];
        }, $assignments);

        return array(
            'datasets' => array(
                array(
                    'label' => "Student Grades",
                    'backgroundColor' => "rgba(220,220,220,0.2)",
                    'borderColor' => "rgba(220,220,220,1)",
                    'pointColor' => "rgba(220,220,220,1)",
                    'pointBackgroundColor' => "rgba(220,220,220,1)",
                    'data' => $cells_points
                ),
                array(
                    'label' => "Class Average",
                    'backgroundColor' => "rgba(151,187,205,0.2)",
                    'borderColor' => "rgba(151,187,205,1)",
                    'pointColor' => "rgba(151,187,205,1)",
                    'pointBackgroundColor' => "rgba(151,187,205,1)",
                    'data' => $assignment_averages
                )
            ),
            'labels' => $assignments_names
        );
    }

    public function get_pie_chart($amid) {
        global $wpdb;
        //need to check that user has access to this assignment.
        if (!is_user_logged_in()) {
            echo json_encode(array("status" => "Not Allowed."));
            die();
        }
        $query = $wpdb->prepare("SELECT assign_points_earned FROM {$wpdb->prefix}oplb_gradebook_cells WHERE amid = %d", $amid);
        $pie_chart_data = $wpdb->get_col($query);

        function isA($n) {
            return ($n >= 90 ? true : false);
        }

        function isB($n) {
            return ($n >= 80 && $n < 90 ? true : false);
        }

        function isC($n) {
            return ($n >= 70 && $n < 80 ? true : false);
        }

        function isD($n) {
            return ($n >= 60 && $n < 70 ? true : false);
        }

        function isF($n) {
            return ($n < 60 ? true : false);
        }

        $is_A = count(array_filter($pie_chart_data, 'isA'));
        $is_B = count(array_filter($pie_chart_data, 'isB'));
        $is_C = count(array_filter($pie_chart_data, 'isC'));
        $is_D = count(array_filter($pie_chart_data, 'isD'));
        $is_F = count(array_filter($pie_chart_data, 'isF'));

        $pie_chart_data = array(
            'labels' => array('A', 'B', 'C', 'D', 'F'),
            'datasets' => array(
                array(
                    'label' => 'Grade Breakdown',
                    'data' => array($is_A, $is_B, $is_C, $is_D, $is_F),
                    'backgroundColor' => array('#F7464A', '#46BFBD', '#FDB45C', '#949FB1', '#4D5360'),
                    'hoverBackgroundColor' => array('#FF5A5E', '#5AD3D1', '#FFC870', '#A8B3C5', '#616774'),
                )
            )
        );

        return $pie_chart_data;
    }

    /**
     * Retreive data to establish gradebook view on client-side
     * @global type $current_user
     * @global type $wpdb
     * @param type $gbid
     * @param type $role
     * @param type $uid
     * @return type
     */
    public function oplb_get_gradebook($gbid, $role, $uid) {
        global $current_user, $wpdb;
        if (!$uid) {
            $uid = $current_user->ID;
        }
        if (!$role) {
            $query = $wpdb->prepare("SELECT role FROM {$wpdb->prefix}oplb_gradebook_users WHERE gbid = %d AND uid = %d", $gbid, $uid);
            $role = $wpdb->get_var($query);
        }

        if ($role === 'instructor') {

            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE gbid = %d", $gbid);
            $assignments = $wpdb->get_results($query, ARRAY_A);

            foreach ($assignments as &$assignment) {
                $assignment['id'] = intval($assignment['id']);
                $assignment['gbid'] = intval($assignment['gbid']);
                $assignment['assign_order'] = intval($assignment['assign_order']);
            }

            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_cells WHERE gbid = %d", $gbid);
            $cells = $wpdb->get_results($query, ARRAY_A);

            foreach ($assignments as &$assignment) {
                $assignment['gbid'] = intval($assignment['gbid']);
            }

            $query = $wpdb->prepare("SELECT uid FROM {$wpdb->prefix}oplb_gradebook_users WHERE gbid = %d AND role = '%s'", $gbid, 'student');
            $students = $wpdb->get_results($query, ARRAY_N);

            foreach ($students as &$student_id) {
                $student = get_userdata($student_id[0]);
                $current_grade_average = $this->oplb_gradebook_get_current_grade_average($student_id[0], $gbid);
                $student_id = array(
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'user_login' => $student->user_login,
                    'current_grade_average' => $current_grade_average,
                    'id' => intval($student->ID),
                    'gbid' => intval($gbid)
                );
            }
            usort($cells, build_sorter('assign_order'));
            foreach ($cells as &$cell) {
                $cell['amid'] = intval($cell['amid']);
                $cell['uid'] = intval($cell['uid']);
                $cell['assign_order'] = intval($cell['assign_order']);
                $cell['assign_points_earned'] = floatval($cell['assign_points_earned']);
                $cell['gbid'] = intval($cell['gbid']);
                $cell['id'] = intval($cell['id']);
            }

            //get weight info
            $get_weight_info = $this->oplb_gradebook_get_total_weight($gbid);

            return array("assignments" => $assignments,
                "cells" => $cells,
                "students" => $students,
                "role" => "instructor",
                "distributed_weight" => $get_weight_info['distributed_weight'],
            );
        } else if ($role === 'student') {

            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE assign_visibility = '%s' AND gbid = %d", 'Students', $gbid);
            $assignments = $wpdb->get_results($query, ARRAY_A);

            $assignments2 = $assignments;
            foreach ($assignments as &$assignment) {
                $assignment['id'] = intval($assignment['id']);
                $assignment['gbid'] = intval($assignment['gbid']);
                $assignment['assign_order'] = intval($assignment['assign_order']);
            }
            $assignmentIDsformated = '';
            foreach ($assignments as &$assignment) {
                $assignmentIDsformated = $assignmentIDsformated . $assignment['id'] . ',';
            }
            $assignmentIDsformated = substr($assignmentIDsformated, 0, -1);
            $assignments_to_process = explode(',', $assignmentIDsformated);

            $assignment_count = count($assignments_to_process);
            $placeholders = array_fill(0, $assignment_count, '%d');
            $format = implode(', ', $placeholders);

            array_push($assignments_to_process, $current_user->ID);

            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_cells WHERE amid IN ($format) AND uid = %d", $assignments_to_process);

            $cells = $wpdb->get_results($query, ARRAY_A);

            foreach ($cells as &$cell) {
                $cell['gbid'] = intval($cell['gbid']);
            }
            $student = get_userdata($current_user->ID);
            $current_grade_average = $this->oplb_gradebook_get_current_grade_average($current_user->ID, $gbid);

            $student = array(
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'user_login' => $student->user_login,
                'current_grade_average' => $current_grade_average,
                'id' => intval($student->ID),
                'gbid' => intval($gbid)
            );
            usort($cells, build_sorter('assign_order'));
            foreach ($cells as &$cell) {
                $cell['amid'] = intval($cell['amid']);
                $cell['uid'] = intval($cell['uid']);
                $cell['assign_order'] = intval($cell['assign_order']);
                $cell['assign_points_earned'] = floatval($cell['assign_points_earned']);
                $cell['gbid'] = intval($cell['gbid']);
                $cell['id'] = intval($cell['id']);
            }
            return array(
                "assignments" => $assignments,
                "cells" => $cells,
                "students" => array($student),
                "role" => "student",
                "test" => $assignments2
            );
        }
    }

    public function oplb_gradebook_get_params() {
        global $wpdb;

        $args = array(
            'id' => FILTER_SANITIZE_NUMBER_INT,
            'gbid' => FILTER_SANITIZE_NUMBER_INT,
            'nonce' => FILTER_SANITIZE_STRING,
            'name' => FILTER_SANITIZE_STRING,
            'school' => FILTER_SANITIZE_STRING,
            'semester' => FILTER_SANITIZE_STRING,
            'year' => FILTER_SANITIZE_NUMBER_INT,
            'assign_category' => FILTER_SANITIZE_STRING,
            'assign_date' => FILTER_SANITIZE_STRING,
            'assign_due' => FILTER_SANITIZE_STRING,
            'assign_grade_type' => FILTER_SANITIZE_STRING,
            'assign_name' => FILTER_SANITIZE_STRING,
            'assign_visibility' => FILTER_SANITIZE_STRING,
            'assign_weight' => FILTER_SANITIZE_STRING,
            'gbid' => FILTER_SANITIZE_NUMBER_INT,
            'publish' => FILTER_VALIDATE_BOOLEAN,
            'selected' => FILTER_VALIDATE_BOOLEAN,
            'sorted' => FILTER_SANITIZE_STRING,
            'visibility' => FILTER_VALIDATE_BOOLEAN,
            'amid' => FILTER_SANITIZE_NUMBER_INT,
            'assign_order' => FILTER_SANITIZE_NUMBER_INT,
            'assign_points_earned' => FILTER_SANITIZE_STRING,
            'current_grade_average' => FILTER_SANITIZE_STRING,
            'display' => FILTER_VALIDATE_BOOLEAN,
            'hover' => FILTER_VALIDATE_BOOLEAN,
            'uid' => FILTER_SANITIZE_NUMBER_INT,
            'chart_type' => FILTER_SANITIZE_STRING,
            'amid' => FILTER_SANITIZE_NUMBER_INT,
            'student_range_option' => FILTER_SANITIZE_STRING,
            'first_name' => FILTER_SANITIZE_STRING,
            'last_name' => FILTER_SANITIZE_STRING,
            'id-exists' => FILTER_SANITIZE_STRING,
        );

        $method = (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : $_SERVER['REQUEST_METHOD'];

        $params = filter_input_array(INPUT_GET, $args);

        if ($method === 'POST' || $method === 'PUT') {
            //test for payload
            $incoming = json_decode(file_get_contents('php://input'), true);

            if (!empty($incoming)) {
                $incoming_params = filter_var_array($incoming, $args);
                $params = $this->oplb_gradebook_merge_arrays_on_null($params, $incoming_params);
            }
        }

        $params['method'] = $method;

        return $params;
    }

    private function oplb_gradebook_merge_arrays_on_null($a, $b) {

        $c = array();
        foreach ($a as $key => $val) {

            if ($key == NULL && $b[$key] == NULL) {
                $c[$key] = $val;
            } else if ($key != NULL && $b[$key] == NULL) {
                $c[$key] = $val;
            } else if ($key != NULL && $b[$key] != NULL) {
                $c[$key] = $b[$key];
            } else {
                $c[$key] = $b[$key];
            }
        }

        return $c;
    }

    /**
     * Easily retrieve current user role
     * @global type $wpdb
     * @global type $current_user
     * @return type
     */
    public function oplb_gradebook_get_user_role() {
        global $wpdb, $current_user;
        $uid = $current_user->ID;
        $query = $wpdb->prepare("SELECT role FROM {$wpdb->prefix}oplb_gradebook_users WHERE uid = %d", $uid);
        $role = $wpdb->get_var($query);
        return $role;
    }

    /**
     * Easily retrieve current user role
     * @global type $wpdb
     * @global type $current_user
     * @param type $gbid
     * @return type
     */
    public function oplb_gradebook_get_user_role_by_gbid($gbid) {
        global $wpdb, $current_user;
        $uid = $current_user->ID;
        $query = $wpdb->prepare("SELECT role FROM {$wpdb->prefix}oplb_gradebook_users WHERE gbid = %d AND uid = %d", $gbid, $uid);
        $role = $wpdb->get_var($query);
        return $role;
    }

    /**
     * Easily establish if current user is an OpenLab Gradebook administrator
     * @global type $current_user
     * @return boolean
     */
    public function oplb_is_gb_administrator() {
        global $current_user;
        $x = $current_user->roles;
        $y = array_keys(get_option('oplb_gradebook_settings'), true);

        $z = array_uintersect($x, $y, array($this, "oplb_array_uintersect_strict_comparison"));

        if (count($z)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Array intersect with a strict comparison
     * @param type $a
     * @param type $b
     * @return int
     */
    private function oplb_array_uintersect_strict_comparison($a, $b) {
        if ($a === $b) {
            return 0;
        }
        return ($a > $b) ? 1 : -1;
    }

    /**
     * Retrieve the current grade average for a particular student
     * Compares calculated grade average to stored average, and updates stored average
     * if there is a difference between the two
     * @global type $wpdb
     * @param type $uid
     * @param type $gbid
     * @return type
     */
    public function oplb_gradebook_get_current_grade_average($uid, $gbid) {
        global $wpdb;
        $average_out = 0.00;

        $current_grade_average_query = $wpdb->prepare("SELECT current_grade_average FROM {$wpdb->prefix}oplb_gradebook_users WHERE uid = %d AND gbid = %d", $uid, $gbid);
        $current_grade_average = $wpdb->get_results($current_grade_average_query);

        if (empty($current_grade_average)) {
            $average_out = 0.00;
        }

        $average_out = $current_grade_average[0]->current_grade_average;
        $calc_grade_average = $this->oplb_calculate_current_grade_average($uid, $gbid);

        if ($calc_grade_average !== $average_out) {
            $average_out = $calc_grade_average;
            $this->oplb_gradebook_update_current_grade_average($average_out, $gbid, $uid);
        }

        return number_format((float) $average_out, 2, '.', '');
    }

    /**
     * Calculates the total weight, i.e. the sum of all weights applied to assignments
     * @global type $wpdb
     * @return type
     */
    public function oplb_gradebook_get_total_weight($gbid) {
        global $wpdb;
        $weights_by_assignment = array();
        $distributed_weight = 0;

        $query = $wpdb->prepare("SELECT id, assign_weight FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE gbid = %d", $gbid);
        $weights = $wpdb->get_results($query, ARRAY_A);

        $total_weight = 0;
        $total_assignments = 0;
        $assignments_with_no_weight = 0;
        foreach ($weights as $weight) {
            $total_weight = $total_weight + $weight['assign_weight'];
            $weights_by_assignment[$weight['id']] = number_format((float) $weight['assign_weight'], 2, '.', '');

            //let's work out any assignments that don't have a weight
            if (floatval($weights_by_assignment[$weight['id']]) === 0.00) {
                $assignments_with_no_weight++;
            }

            $total_assignments++;
        }

        //if no weights are assigned (i.e. all weights are set to 0), distribute weights equally
        if (intval($total_weight) === 0) {
            $total_weight = 100;
            
            //avoid division by zero
            if($total_assignments === 0){
                $total_assignments = 1;
            }
            
            $distributed_weight = $total_weight / $total_assignments;
            foreach ($weights_by_assignment as &$assign_weight) {
                $assign_weight = $distributed_weight;
            }
        }

        //if only some assignments have a weight, and the total weight is less than 100, calculate a distributed weight for the other assignments
        if ($assignments_with_no_weight > 0 && $total_weight < 100) {

            $available_weight = 100 - $total_weight;
            $total_weight = 100;
            $distributed_weight = $available_weight / $assignments_with_no_weight;

            foreach ($weights_by_assignment as &$assign_weight) {

                if (floatval($assign_weight) === 0.00) {
                    $assign_weight = $distributed_weight;
                }
            }
        }


        return array(
            'weights_by_assignment' => $weights_by_assignment,
            'distributed_weight' => $distributed_weight,
            'total_weight' => $total_weight,
        );
    }

    /**
     * Calculates the current grade average for a particular student
     * Normalizes weights to 100%, then uses those weights in the grade calculation
     * @global type $wpdb
     * @param type $uid
     * @param type $gbid
     * @return type
     */
    public function oplb_calculate_current_grade_average($uid, $gbid) {
        global $wpdb;

        $average_out = 0.00;

        //first get total weight
        $weights_return = $this->oplb_gradebook_get_total_weight($gbid);
        $weights_by_assignment = $weights_return['weights_by_assignment'];

        //calibrate weight to 100
        $normalization_pct = 100 / $weights_return['total_weight'];

        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_cells WHERE uid = %d AND gbid = %d", $uid, $gbid);
        $assignments = $wpdb->get_results($query);

        if (empty($assignments)) {
            return number_format((float) $average_out, 2, '.', '');
        }

        foreach ($assignments as $assignment) {

            //first get assignment weight
            $amid = $assignment->amid;
            $this_grade = $assignment->assign_points_earned;

            $this_weight = 1.00;

            if (isset($weights_by_assignment[$amid])) {
                $this_weight = $weights_by_assignment[$amid];
            }

            //normalize weight
            $weight_adj = ($this_weight * $normalization_pct) / 100;

            //grade adjusted
            $grade_adj = $this_grade * $weight_adj;
            $average_out = $average_out + $grade_adj;
        }

        return number_format((float) $average_out, 2, '.', '');
    }

    /**
     * Utility for updating all student grade averages at once
     * @global type $wpdb
     * @param type $gbid
     * @return type
     */
    public function oplb_gradebook_update_all_student_current_grade_averages($gbid) {
        global $wpdb;

        $student_data = array();

        $query = $wpdb->prepare("SELECT uid FROM {$wpdb->prefix}oplb_gradebook_users WHERE gbid = %d AND role = %s", $gbid, 'student');
        $students = $wpdb->get_results($query);

        if (!empty($students)) {

            $student_data = array();

            foreach ($students as $key => $student) {

                $calc_grade_average = $this->oplb_calculate_current_grade_average($student->uid, $gbid);
                $this->oplb_gradebook_update_current_grade_average($calc_grade_average, $gbid, $student->id);

                $student_data[$key] = array(
                    'uid' => intval($student->uid),
                    'current_grade_average' => number_format((float) $calc_grade_average, 2, '.', ''),
                );
            }
        }

        return $student_data;
    }

    /**
     * Updates current grade average stored in the DB for a given student
     * @global type $wpdb
     * @param type $calc_grade_average
     * @param type $gbid
     * @param type $uid
     */
    public function oplb_gradebook_update_current_grade_average($calc_grade_average, $gbid, $uid) {
        global $wpdb;

        $wpdb->update("{$wpdb->prefix}oplb_gradebook_users", array(
            'current_grade_average' => $calc_grade_average
                ), array(
            'gbid' => $gbid,
            'uid' => $uid,
                ), array(
            '%f',
                ), array(
            '%d',
            '%d',
                )
        );
    }

    /**
     * Retrieve user data for a given user
     * Combines data stored in local OpenLab Gradebook tables, plus global user tables
     * @global type $wpdb
     * @param type $id
     * @param type $gbid
     * @param type $bool
     * @return boolean
     */
    public function oplb_gradebook_get_user($id, $gbid, $bool = false) {
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_users WHERE uid = %d AND gbid = %d", $id, $gbid);
        $user = $wpdb->get_row($query, ARRAY_A);

        //added boolean check in case of legacy usage of this function
        if ($bool && empty($user)) {
            return false;
        }

        $current_grade_average = $this->oplb_gradebook_get_current_grade_average($id, $gbid);

        $user_data = get_user_by('id', $id);
        $user['id'] = intval($user['id']);
        $user['gbid'] = intval($user['gbid']);
        $user['uid'] = intval($user['uid']);
        $user['first_name'] = $user_data->first_name;
        $user['last_name'] = $user_data->last_name;
        $user['user_login'] = $user_data->user_login;
        $user['current_grade_average'] = $current_grade_average;
        return $user;
    }

    /**
     * Add a new user to OpenLab Gradebook
     * Contains legacy functionality that adds a global user
     * @todo: deprecate this legacy functionality
     * @global type $wpdb
     * @param type $id
     * @param type $gbid
     * @param type $first_name
     * @param type $last_name
     * @param type $user_login
     * @return type
     */
    public function oplb_gradebook_create_user($id, $gbid, $first_name, $last_name, $user_login, $return = false) {
        global $wpdb;

        if (!$user_login) {
            if ($return) {
                return false;
            }

            echo 'User does not exist';
            die();
        }

        $user = get_user_by('login', $user_login);

        if ($user) {

            //if user already exists, we're done
            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_users WHERE uid = %d AND gbid = %d", $user->ID, $gbid);
            $check_for_existing_user = $wpdb->get_results($query);

            if (!empty($check_for_existing_user)) {

                if ($return) {
                    return false;
                }

                echo 'User already exists';
                die();
            }

            $result = $wpdb->insert("{$wpdb->prefix}oplb_gradebook_users", array(
                'uid' => $user->ID,
                'gbid' => $gbid,
                'role' => 'student',
                'current_grade_average' => 0.00,
                    ), array(
                '%d',
                '%d',
                '%s',
                '%f',
                    )
            );

            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE gbid = %d", $gbid);
            $assignments = $wpdb->get_results($query, ARRAY_A);

            foreach ($assignments as $assignment) {
                $wpdb->insert("{$wpdb->prefix}oplb_gradebook_cells", array(
                    'gbid' => $gbid,
                    'amid' => $assignment['id'],
                    'uid' => $user->ID,
                    'assign_order' => $assignment['assign_order'],
                        )
                );
            };

            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_users WHERE uid = %d AND gbid = %d", $user->ID, $gbid);
            $role = $wpdb->get_results($query, ARRAY_A);

            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_cells WHERE uid = %d AND gbid = %d", $user->ID, $gbid);
            $cells = $wpdb->get_results($query, ARRAY_A);

            usort($cells, build_sorter('assign_order'));
            foreach ($cells as &$cell) {
                $cell['amid'] = intval($cell['amid']);
                $cell['uid'] = intval($cell['uid']);
                $cell['assign_order'] = intval($cell['assign_order']);
                $cell['assign_points_earned'] = floatval($cell['assign_points_earned']);
                $cell['gbid'] = intval($cell['gbid']);
                $cell['id'] = intval($cell['id']);
            }

            $user_meta = $this->oplb_gradebook_get_user_meta($user);

            $student_out = array(
                'type' => 'single',
                'student' => array(
                    'first_name' => $user_meta['first_name'],
                    'last_name' => $user_meta['last_name'],
                    'user_login' => $user->user_login,
                    'current_grade_average' => number_format((float) 0.00, 2, '.', ''),
                    'gbid' => intval($gbid),
                    'id' => $user->ID,
                    'role' => $role[0]['role']
                ),
                'cells' => $cells
            );

            if ($return) {
                return $student_out;
            }

            wp_send_json($student_out);
        }
    }

    public function oplb_gradebook_add_all_students($gbid) {
        global $oplb_user_list;
        $data_out = array(
            'type' => 'all',
            'students' => array(),
            'cells' => array(),
        );

        $group_users = $oplb_user_list->oplb_user_list('retrieve');

        if (empty($group_users)) {
            echo json_encode(array("error" => "no_students"));
            die();
        }

        foreach ($group_users as $user) {

            $student_return = $this->oplb_gradebook_create_user($user->ID, $gbid, $user->first_name, $user->last_name, $user->user_login, true);

            if (!$student_return) {
                continue;
            }

            array_push($data_out['students'], $student_return['student']);

            foreach ($student_return['cells'] as $cell) {
                array_push($data_out['cells'], $cell);
            }
        }

        return $data_out;
    }

    /**
     * Retrieves user meta for First Name and Last Name
     * If those fields are not available, attempts to parse name from "Nickname" meta
     * @param type $user
     * @return type
     */
    public function oplb_gradebook_get_user_meta($user) {
        
        $first_name_retrieve = get_user_meta($user->ID, 'first_name', true);
        $last_name_retrieve = get_user_meta($user->ID, 'last_name', true);
        $nickname = get_user_meta($user->ID, 'nickname', true);
        
        //this won't always work, but it's worth a shot
        if (!empty($nickname)) {

            $nickname_raw = explode(' ', $nickname);
            $count = 0;
            $new_first_name = array();
            $new_last_name = array();

            //we're think of every word except the last word as the "first name"
            foreach ($nickname_raw as $key => $name_part) {

                if ($key + 1 === count($nickname_raw)) {
                    $new_last_name[] = $name_part;
                    continue;
                }

                $new_first_name[] = $name_part;
            }

            if (empty($first_name_retrieve)) {
                $first_name_retrieve = implode(' ', $new_first_name);
            }

            if (empty($last_name_retrieve)) {

                $last_name_retrieve = implode(' ', $new_last_name);
            }
        }

        //default in case nothing comes back
        if (empty($first_name_retrieve)) {
            $first_name_retrieve = $user->user_login;
        }

        if (empty($last_name_retrieve)) {

            $last_name_retrieve = '';
        }

        $meta_out = array(
            'first_name' => $first_name_retrieve,
            'last_name' => $last_name_retrieve,
        );

        return apply_filters('oplb_gradebook_user_meta', $meta_out, $user);
    }

}

?>