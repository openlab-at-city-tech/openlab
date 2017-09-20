<?php

class oplb_gradebook_api {

    public function build_sorter($key) {
        return function ($a, $b) use ($key) {
            return strnatcmp($a[$key], $b[$key]);
        };
    }

    public function oplb_gradebook_update_user($id, $first_name, $last_name) {
        $user_id = wp_update_user(array(
            'ID' => $id,
            'first_name' => $first_name,
            'last_name' => $last_name));
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
        ///$cells = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_cells WHERE uid = $uid AND gbid = $gbid", ARRAY_A);	
        $class_cells = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_cells WHERE gbid = $gbid", ARRAY_A);
        $cells = array_map(function($class_cell) use ($uid) {
            if ($class_cell['uid'] == $uid) {
                return $class_cell;
            }
        }, $class_cells);
        $cells = array_filter($cells);
        $assignments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE gbid = $gbid", ARRAY_A);

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
        $pie_chart_data = $wpdb->get_col("SELECT assign_points_earned FROM {$wpdb->prefix}oplb_gradebook_cells WHERE amid = $amid");

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

    public function oplb_get_gradebook($gbid, $role, $uid) {
        global $current_user, $wpdb;
        if (!$uid) {
            $uid = $current_user->ID;
        }
        if (!$role) {
            $role = $wpdb->get_var("SELECT role FROM {$wpdb->prefix}oplb_gradebook_users WHERE gbid = $gbid AND uid = $uid");
        }

        switch ($role) {
            case 'instructor' :
                $assignments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE gbid = $gbid", ARRAY_A);

                foreach ($assignments as &$assignment) {
                    $assignment['id'] = intval($assignment['id']);
                    $assignment['gbid'] = intval($assignment['gbid']);
                    $assignment['assign_order'] = intval($assignment['assign_order']);
                }
                $cells = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_cells WHERE gbid = $gbid", ARRAY_A);
                foreach ($assignments as &$assignment) {
                    $assignment['gbid'] = intval($assignment['gbid']);
                }
                $students = $wpdb->get_results("SELECT uid FROM {$wpdb->prefix}oplb_gradebook_users WHERE gbid = $gbid AND role = 'student'", ARRAY_N);
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



                return array("assignments" => $assignments,
                    "cells" => $cells,
                    "students" => $students,
                    "role" => "instructor"
                );
            case 'student' :
                $assignments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE assign_visibility = 'Students' AND gbid = $gbid", ARRAY_A);
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
                $cells = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_cells WHERE amid IN ( $assignmentIDsformated ) AND uid = {$current_user->ID}", ARRAY_A);
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

    public function oplb_gradebook_get_user_role($gbid) {
        global $wpdb, $current_user;
        $uid = $current_user->ID;
        $role = $wpdb->get_var("SELECT role FROM {$wpdb->prefix}oplb_gradebook_users WHERE gbid = $gbid AND uid = $uid");
        return $role;
    }

    public function oplb_is_gb_administrator() {
        global $current_user;
        $x = $current_user->roles;
        $y = array_keys(get_option('oplb_gradebook_settings'), true);
        $z = array_intersect($x, $y);
        if (count($z)) {
            return true;
        } else {
            return false;
        }
    }

    public function oplb_gradebook_get_current_grade_average($uid, $gbid) {
        global $wpdb;
        $average_out = 0.00;

        $current_grade_average_query = $wpdb->prepare("SELECT current_grade_average FROM {$wpdb->prefix}oplb_gradebook_users WHERE uid = %d AND gbid = %d", $uid, $gbid);
        $current_grade_average = $wpdb->get_results($current_grade_average_query);

        if (!$current_grade_average || empty($current_grade_average)) {
            $average_out = 0.00;
        }

        $average_out = $current_grade_average[0]->current_grade_average;
        $calc_grade_average = $this->oplb_calculate_current_grade_average($uid, $gbid);

        if ($calc_grade_average !== $average_out) {
            $average_out = $calc_grade_average;
            $this->oplb_gradebook_update_current_grade_average($average_out, $uid, $gbid);
        }

        return number_format((float) $average_out, 2, '.', '');
    }

    public function oplb_gradebook_get_total_weight() {
        global $wpdb;
        $weights_by_assignment = array();

        $weights = $wpdb->get_results("SELECT id, assign_weight FROM {$wpdb->prefix}oplb_gradebook_assignments", ARRAY_A);

        $total_weight = 0;

        foreach ($weights as $weight) {
            $total_weight = $total_weight + $weight['assign_weight'];
            $weights_by_assignment[$weight['id']] = number_format((float) $weight['assign_weight'], 2, '.', '');
        }

        return array(
            'weights_by_assignment' => $weights_by_assignment,
            'total_weight' => $total_weight,
        );
    }

    public function oplb_calculate_current_grade_average($uid, $gbid) {
        global $wpdb;

        $average_out = 0.00;

        //first get total weight
        $weights_return = $this->oplb_gradebook_get_total_weight();
        $weights_by_assignment = $weights_return['weights_by_assignment'];

        //calibrate weight to 100
        $normalization_pct = 100 / $weights_return['total_weight'];

        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_cells WHERE uid = %d AND gbid = %d", $uid, $gbid);
        $assignments = $wpdb->get_results($query);

        if (!$assignments || empty($assignments)) {
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

    public function oplb_gradebook_update_all_student_current_grade_averages($gbid) {
        global $wpdb;

        $student_data = array();

        $query = $wpdb->prepare("SELECT uid FROM {$wpdb->prefix}oplb_gradebook_users WHERE gbid = %d AND role = %s", $gbid, 'student');
        $students = $wpdb->get_results($query);

        if ($students && !empty($students)) {

            $student_data = array();

            foreach ($students as $key => $student) {

                $calc_grade_average = $this->oplb_calculate_current_grade_average($student->uid, $gbid);
                $this->oplb_gradebook_update_current_grade_average($calc_grade_average, $student->id, $gbid);

                $student_data[$key] = array(
                    'uid' => $student->uid,
                    'current_grade_average' => $calc_grade_average,
                );
            }
        }

        return $student_data;
    }

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

    public function oplb_gradebook_get_user($id, $gbid, $bool = false) {
        global $wpdb;
        $user = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}oplb_gradebook_users WHERE uid = $id AND gbid = $gbid", ARRAY_A);

        //added boolean check in case of legacy usage of this function
        if ($bool && !$user || empty($user)) {
            return false;
        }

        $current_grade_average = $this->oplb_gradebook_get_current_grade_average($id, $gbid);

        $user_data = get_user_by('id', $id);
        $user_data->ID;
        $user['id'] = intval($user['id']);
        $user['gbid'] = intval($user['gbid']);
        $user['uid'] = intval($user['uid']);
        $user['first_name'] = $user_data->first_name;
        $user['last_name'] = $user_data->last_name;
        $user['user_login'] = $user_data->user_login;
        $user['current_grade_average'] = $current_grade_average;
        return $user;
    }

    public function oplb_gradebook_create_user($id, $gbid, $first_name, $last_name, $user_login) {
        global $wpdb;
        //$gbid is being passed as string, should be int.
        if (!$user_login) {
            $counter = intval($wpdb->get_var("SELECT MAX(id) FROM {$wpdb->users}")) + 1;
            $result = wp_insert_user(array(
                'user_login' => strtolower($first_name[0] . $last_name . $counter),
                'first_name' => $first_name,
                'last_name' => $last_name,
                'user_pass' => 'password'
            ));
            if (is_wp_error($result)) {
                echo $result->get_error_message();
                die();
            }
            $user_id = $result;
            $wpdb->update($wpdb->users, array('user_login' => strtolower($first_name[0] . $last_name) . $user_id), array('ID' => $user_id)
            );
            $assignments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE gbid = $gbid", ARRAY_A);
            foreach ($assignments as $assignment) {
                $wpdb->insert("{$wpdb->prefix}oplb_gradebook_cells", array(
                    'gbid' => $gbid, 'amid' => $assignment['id'],
                    'uid' => $result, 'assign_order' => $assignment['assign_order']
                ));
            };
            $student = get_user_by('id', $user_id);
            $wpdb->insert("{$wpdb->prefix}oplb_gradebook_users", array('uid' => $student->ID, 'gbid' => $gbid, 'role' => 'student'));
            $cells = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_cells WHERE uid = $result", ARRAY_A);
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
                'student' => array(
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'user_login' => $student->user_login,
                    'current_grade_average' => 0.00,
                    'gbid' => intval($gbid),
                    'id' => intval($result)
                ),
                'cells' => $cells
            );
        } else {
            $user = get_user_by('login', $user_login);

            if ($user) {

                //if user already exists, we're done
                $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_users WHERE uid = %d AND gbid = %d", $user->ID, $gbid);
                $check_for_existing_user = $wpdb->get_results($query);

                if ($check_for_existing_user && !empty($check_for_existing_user)) {
                    echo 'User already exists';
                    die();
                }

                $result = $wpdb->insert("{$wpdb->prefix}oplb_gradebook_users", array(
                    'uid' => $user->ID,
                    'gbid' => $gbid,
                    'role' => 'student',
                    'current_grade_average',
                        ), array(
                    '%d',
                    '%d',
                    '%s',
                    '%f',
                        )
                );
                $assignments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE gbid = $gbid", ARRAY_A);
                foreach ($assignments as $assignment) {
                    $wpdb->insert("{$wpdb->prefix}oplb_gradebook_cells", array(
                        'gbid' => $gbid,
                        'amid' => $assignment['id'],
                        'uid' => $user->ID,
                        'assign_order' => $assignment['assign_order'],
                            )
                    );
                };
                $role = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_users WHERE uid = {$user->ID} AND gbid = $gbid", ARRAY_A);

                $cells = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_cells WHERE uid = {$user->ID} AND gbid = $gbid", ARRAY_A);
                usort($cells, build_sorter('assign_order'));
                foreach ($cells as &$cell) {
                    $cell['amid'] = intval($cell['amid']);
                    $cell['uid'] = intval($cell['uid']);
                    $cell['assign_order'] = intval($cell['assign_order']);
                    $cell['assign_points_earned'] = floatval($cell['assign_points_earned']);
                    $cell['gbid'] = intval($cell['gbid']);
                    $cell['id'] = intval($cell['id']);
                }

                $user_first_name = $user->first_name;
                $user_last_name = $user->last_name;

                $first_name_retrieve = get_user_meta($user->ID, 'first_name', true);
                $last_name_retrieve = get_user_meta($user->ID, 'last_name', true);

                //if BP is available, retreive xprofile data
                if (OPLB_BP_AVAILABLE) {
                    $bp_first_name = xprofile_get_field_data('First Name', $user->ID);
                    $bp_last_name = xprofile_get_field_data('Last Name', $user->ID);
                }

                $user_first_name = ($bp_first_name && !empty($bp_first_name)) ? $bp_first_name : $first_name_retrieve;
                $user_last_name = ($bp_last_name && !empty($bp_last_name)) ? $bp_last_name : $last_name_retrieve;

                echo json_encode(array('student' => array(
                        'first_name' => $user_first_name,
                        'last_name' => $user_last_name,
                        'user_login' => $user->user_login,
                        'current_grade_average' => 0.00,
                        'gbid' => intval($gbid),
                        'id' => $user->ID,
                        'role' => $role[0]['role']),
                    'cells' => $cells
                ));
                die();
            }
        }
    }

}

?>