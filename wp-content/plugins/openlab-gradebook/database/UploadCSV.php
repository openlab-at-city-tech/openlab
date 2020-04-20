<?php

class gradebook_upload_csv_API
{

    public function __construct()
    {
        ini_set('auto_detect_line_endings', true);
        add_action('wp_ajax_oplb_gradebook_upload_csv', array($this, 'upload_csv'));
        add_action('admin_init', array($this, 'download_csv'));
    }

    /**
     * Handle uploading of the CSV
     */
    public function upload_csv()
    {
        global $oplb_gradebook_api;
        $nonce = filter_var($_REQUEST['nonce']);

        if (!wp_verify_nonce($nonce, 'oplb_gradebook')) {
            echo json_encode(array("status" => "Authentication error."));
            die();
        }

        $upload = $_FILES['upload-csv'];
        $gbid = filter_var($_REQUEST['gbid'], FILTER_SANITIZE_NUMBER_INT);

        $file = array(
            'name' => filter_var($upload['name']),
            'file' => filter_var($upload['tmp_name']),
        );

        $allowed_types = array('text/csv');

        $message = array(
            'response' => 'oplb-gradebook-success',
            'content' => '',
        );

        if (empty($file['name'])) {
            $message = array(
                'response' => 'oplb-gradebook-error',
                'error' => 'CSV not successfully uploaded.',
            );
            return $message;
        }

        $typecheck = wp_check_filetype_and_ext($file['file'], $file['name'], false);

        // Add an error message if MIME-type is not allowed
        if (!in_array($typecheck['type'], $allowed_types)) {
            $message = array(
                'response' => 'oplb-gradebook-error',
                'error' => 'This file does not appear to be a CSV.',
            );
            return $message;
        }

        // Now let's try to catch eval( base64() ) et al
        if (0 !== $this->_invoke_paranoia_on_file_contents(file_get_contents($file['file']))) {
            $message = array(
                'response' => 'oplb-gradebook-error',
                'error' => 'Suspicious file error.',
            );
            return $message;
        }

        $parse_result = $this->parseCSV($file['file']);

        $process_result = $this->checkData($parse_result, $gbid);

        if ($process_result['errors'] === 'global') {

            array_push($process_result['headers'], "**Error:{$process_result['message']['error']}**");
            $process_result['file'] = $file;

            $this->handleErrors($process_result, $gbid);

        } else if ($process_result['errors'] > 0) {

            $process_result['file'] = $file;
            $this->handleErrors($process_result, $gbid);

        }

        $process_result = $this->processData($process_result, $gbid);

        $message = array(
            'content' => $oplb_gradebook_api->oplb_get_gradebook($gbid, null, null),
            'message' => __('<p></p><div id="upload-csv-success-message" class="bs-callout bs-callout-success text-left"><p class="bold">Your CSV file has been uploaded to Gradebook!</p><p>Close this window and confirm that the values are correct.</p><span id="modal-download-csv-success"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-check fa-stack-1x"></i></span>Success!</span></div>', 'openlab-gradebook'),
        );

        wp_send_json($message);
    }

    /**
     * Parse the incoming CSV file
     * @param type $file
     * @return type
     */
    public function parseCSV($file)
    {
        global $oplb_gradebook_api;
        // Create an array to hold the data
        $arrData = array();

        // Create a variable to hold the header information
        $header = array();

        //Create a variable to hold the type information
        $type = array();

        $header_rows_filled = false;

        // If the file can be opened as readable, bind a named resource
        if (($handle = fopen($file, 'r')) !== false) {
            // Loop through each row
            while (($row = fgetcsv($handle)) !== false) {

                //also add a trim to prevent accidental spacing errors
                $row = array_map("trim", $row);
                $row = $oplb_gradebook_api->special_char_handling_incoming($row);
                $row = array_map("sanitize_text_field", $row);

                // If the header has been stored
                if ($header_rows_filled) {
                    // Create an associative array with the data
                    $arrData['data'][] = array_map(function ($key, $val) {return array('key' => $key, 'val' => $val);}, $header, $row);
                }
                // Else the header has not been stored
                else {

                    if (empty($header)) {

                        $has_weights = array();
                        if (!empty($row)) {
                            foreach ($row as $row_item) {
                                if (strtolower(trim($row_item)) === 'weight') {
                                    $has_weights = true;
                                }
                            }
                        }

                        if ($has_weights) {
                            $arrData['weights'] = $row;
                        } else {
                            // Store the current row as the header
                            $header = $row;
                            $arrData['headers'] = $header;
                        }

                    } else {

                        $possibly_types = $row;

                        if (strpos($possibly_types[0], 'Assignment Types') !== false) {
                            $arrData['types'] = $row;
                        } else {
                            $arrData['data'][] = array_map(function ($key, $val) {return array('key' => $key, 'val' => $val);}, $header, $row);
                        }

                        $header_rows_filled = true;
                    }
                }
            }

            // Close the file pointer
            fclose($handle);
        }

        //this is here to handle duplicate keys
        //duplicate keys will be handled later on (and throw an error), but we need to preserve the key
        //somehow in order to send the column of grades back with the error CSV
        if (!empty($arrData['data'])) {

            $final_data = array();
            foreach ($arrData['data'] as $key => $data) {

                foreach ($data as $item) {

                    if (isset($final_data[$key][$item['key']])) {
                        $final_data[$key][$item['key'] . rand(1000, 9999)] = $item['val'];
                    } else {
                        $final_data[$key][$item['key']] = $item['val'];
                    }

                }

            }

            $arrData['data'] = $final_data;

        }

        return $arrData;
    }

    private function checkData($process_result, $gbid)
    {
        $errors = 0;

        //if the headers are not present in this CSV, we are abadoning ship
        if (empty($process_result['headers'])) {
            $process_result['message'] = array(
                'response' => 'oplb-gradebook-error',
                'error' => 'This CSV file does not have the necessary headers.',
            );

            $process_result['errors'] = 'global';

            return $process_result;
        }

        //if data is not present in this CSV, we are abadoning ship
        if (empty($process_result['data'])) {
            $process_result['message'] = array(
                'response' => 'oplb-gradebook-error',
                'error' => 'This CSV file does not contain any data.',
            );
            $process_result['errors'] = 'global';

            return $process_result;
        }

        foreach ($process_result['data'] as &$student) {

            $is_student = $this->checkStudent($student, $gbid);

            if (!$is_student) {
                $student['username'] = "**This student is not added to the Gradebook** {$student['username']}";
                $errors++;
            } else {
                $student['student_id'] = $is_student;
            }

        }

        $process_result['errors'] = $errors;

        //if errors, immediately report back
        if ($process_result['errors'] > 0) {
            return $process_result;
        }

        $process_result = $this->checkHeaderFormatting($process_result, $gbid);

        //if errors, immediately report back
        if ($process_result['errors'] > 0) {
            return $process_result;
        }

        $process_result = $this->checkGradeFormatting($process_result);

        return $process_result;
    }

    public function checkHeaderFormatting($process_result, $gbid)
    {
        global $wpdb;
        $errors = 0;

        $headers = &$process_result['headers'];
        $check_array = array(
            0 => 'firstname',
            1 => 'lastname',
            2 => 'username',
            3 => 'mid_semester_grade',
            4 => 'final_grade',
        );

        $formatted_correctly = true;
        $assignments = array();
        $assignmentdex = 0;

        foreach ($headers as $index => &$header) {

            //start setting up assignments, so we have them if this CSV is formatted correctly
            if ($index > $this->getAssignmentIndexStart()) {

                if (in_array($header, $assignments)) {
                    $header = "**This assignment already exists in the CSV file**$header";
                    $assignments[$assignmentdex] = $header;
                    $errors++;
                } else {
                    $assignments[$assignmentdex] = $header;
                }

                $assignmentdex++;
                continue;
            }

            if ($check_array[$index] !== strtolower(trim($header))) {
                $formatted_correctly = false;
            }
        }

        if (!$formatted_correctly) {

            $process_result['message'] = array(
                'response' => 'oplb-gradebook-error',
                'error' => 'This CSV file is not formatted correctly.',
            );
            $process_result['errors'] = 'global';

            return $process_result;
        }

        if (!empty($process_result['types'])) {

            $typedex = $this->getAssignmentIndexStart() + 1;
            $valid_types = array('numeric', 'letter', 'checkmark');

            foreach ($process_result['types'] as $index => &$type) {

                if ($index < $typedex) {
                    continue;
                }

                if (empty(trim($type))) {
                    $type = "numeric";
                } else if (!in_array(strtolower(trim($type)), $valid_types)) {
                    $type = "**This is not a valid assignment type**" . $type;
                    $errors++;
                }

            }

        }

        $process_result['assignments'] = $assignments;

        foreach ($assignments as $thisdex => $assignment) {

            $assignment = trim($assignment);

            $assigndex = $this->getAssignmentIndexStart() + 1;
            $stored_type = '';

            if (!empty($process_result['types']) && !empty($process_result['types'][$assigndex + $thisdex])) {
                $stored_type = $process_result['types'][$assigndex + $thisdex];
            }

            //check for existing assignments first
            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE TRIM(assign_name) LIKE '%s' AND gbid = %d", $assignment, $gbid);
            $existing_assignment = $wpdb->get_results($query);

            if (!empty($existing_assignment)) {

                $this_type = !empty($stored_type) ? $stored_type : $existing_assignment[0]->assign_grade_type;

                $process_result['assignments'][$thisdex] = array(
                    'status' => 'existing',
                    'name' => trim($assignment),
                    'type' => $this_type,
                    'id' => $existing_assignment[0]->id,
                    'assign_order' => $existing_assignment[0]->assign_order,
                    'assign_weight' => $existing_assignment[0]->id,
                    'assign_visibility' => $existing_assignment[0]->assign_visibility,
                );
            } else {

                $this_type = !empty($stored_type) ? $stored_type : $this->checkAssignmentType($assignment, $process_result['data']);

                $process_result['assignments'][$thisdex] = array(
                    'status' => 'new',
                    'name' => trim($assignment),
                    'type' => $this_type,
                );

            }
        }

        $process_result['errors'] = $errors;

        return $process_result;
    }

    private function checkGradeFormatting($process_result)
    {
        global $oplb_gradebook_api;
        $errors = $process_result['errors'];

        //check midsemester and final grades first
        foreach ($process_result['data'] as &$student) {

            $midsemester = $this->checkMidSemesterGrades($student['mid_semester_grade']);

            if ($midsemester['result'] !== 'okay') {
                $student['mid_semester_grade'] = $midsemester['result'];
                $errors++;
            } else {
                $student['mid_semester_grade_value'] = $midsemester['value'];
            }

            $final = $this->checkFinalGrades($student['final_grade']);

            if ($final['result'] !== 'okay') {
                $student['final_grade'] = $final['result'];
                $errors++;
            } else {
                $student['final_grade_value'] = $final['value'];
            }

            //check the student grades
            $assignments = $process_result['assignments'];

            foreach ($assignments as $thisdex => &$assignment) {

                $this_grade = $this->processGrade($student[$assignment['name']], $assignment['type'], true);

                if (is_array($this_grade) && !empty($this_grade['type']) && $this_grade['type'] === 'error') {
                    $errors++;
                }

                $student[$assignment['name']] = $this_grade;
            }

        }

        $process_result['errors'] = $errors;

        return $process_result;
    }

    private function checkMidSemesterGrades($grade)
    {
        global $oplb_gradebook_api;
        $outbound = array(
            'result' => 'okay',
            'value' => $grade,
        );

        if (empty($grade) || $grade === '--') {
            return $outbound;
        }

        if (is_numeric($grade)) {
            $result = $grade . "**numeric values not accepted for mid-semester grades**";

            $outbound = array(
                'result' => $result,
                'value' => $grade,
            );

            return $outbound;
        }

        $mid_grades = $oplb_gradebook_api->getMidSemesterGrades();

        foreach ($mid_grades as $mid_grade) {

            if (trim(strtolower($grade)) === strtolower($mid_grade['label'])) {
                $outbound = array(
                    'result' => 'okay',
                    'value' => $mid_grade['value'],
                );

                return $outbound;
            }

        }

        $result = $grade . "**this value is not valid for mid-semester grades**";

        $outbound = array(
            'result' => $result,
            'value' => $grade,
        );

        return $outbound;
    }

    private function checkFinalGrades($grade)
    {
        global $oplb_gradebook_api;
        $outbound = array(
            'result' => 'okay',
            'value' => $grade,
        );

        if (empty($grade) || $grade === '--') {
            return $outbound;
        }

        if (is_numeric($grade)) {

            $final_grades = $oplb_gradebook_api->getFinalNumericGrades();

            foreach ($final_grades as $final_grade) {

                if (intval($grade) < $final_grade['range_high'] && intval($grade) >= $final_grade['range_low']) {
                    $outbound = array(
                        'result' => 'okay',
                        'value' => $final_grade['value'],
                    );

                    return $outbound;
                }

            }

        } else {

            $final_numeric_grades = $oplb_gradebook_api->getFinalNumericGrades();

            foreach ($final_numeric_grades as $final_grade) {

                if (trim(strtolower($grade)) === strtolower($final_grade['label'])) {

                    $outbound = array(
                        'result' => 'okay',
                        'value' => $final_grade['value'],
                    );

                    return $outbound;
                }

            }

            $final_letter_grades = $oplb_gradebook_api->getFinalLetterGrades();

            foreach ($final_letter_grades as $final_grade) {

                if (trim(strtolower($grade)) === strtolower($final_grade['label'])) {

                    $outbound = array(
                        'result' => 'okay',
                        'value' => $final_grade['value'],
                    );

                    return $outbound;
                }

            }

        }

        $result = $grade . "**this value is not valid for final grades**";
        $outbound = array(
            'result' => $result,
            'value' => $grade,
        );

        return $outbound;
    }

    /**
     *
     * @global type $wpdb
     * @param type $process_result
     * @param type $gbid
     * @return string
     */
    private function processData($process_result, $gbid)
    {
        global $wpdb;
        $assignments_stored = array();

        $assignments = $process_result['assignments'];

        $weights = array();
        $weight_normalize = 1;

        //only add weights if they are properly formatted
        if ($process_result['weights'][2] === 'weight') {

            $weights = array_slice($process_result['weights'], 3);
        }

        foreach ($assignments as $thisdex => $assignment) {

            $type = $assignment['type'];
            $status = $assignment['status'];

            if ($status !== 'new') {

                $to_update = array();
                $to_update_type = array();

                //update info that needs to be updated store assignment info for later use
                $assignments_stored[$thisdex]['name'] = $assignment['name'];
                $assignments_stored[$thisdex]['amid'] = $assignment['id'];
                $assignments_stored[$thisdex]['assign_order'] = $assignment['assign_order'];

                $assignments_stored[$thisdex]['assign_weight'] = $assignment['assign_weight'];
                //for weights, we will update the weight as present on the spreadsheet
                if ($this->isStringNotEmpty(trim($weights[$thisdex]))) {
                    $to_update['assign_weight'] = floatval($weights[$thisdex]);
                    array_push($to_update_type, '%f');
                    $assignments_stored[$thisdex]['assign_weight'] = floatval($weights[$thisdex]);
                }

                $assignments_stored[$thisdex]['assign_grade_type'] = $assignment['assign_grade_type'];
                //for grade type - if the type is already set in the database, we won't do anyting
                //if it's empty, we'll use the type determined from the data in the CSV
                if (!$this->isStringNotEmpty($assignment['assign_grade_type'])) {
                    $to_update['assign_grade_type'] = $type;
                    array_push($to_update_type, '%s');
                    $assignments_stored[$thisdex]['assign_grade_type'] = $type;
                }

                //if the visiblity is empty, default to 'Student'
                if (!$this->isStringNotEmpty($assignment['assign_visibility'])) {
                    $to_update['assign_visibility'] = 'Student';
                    array_push($to_update_type, '%s');
                }

                if (!empty($to_update)) {

                    $wpdb->update("{$wpdb->prefix}oplb_gradebook_assignments", $to_update, array(
                        'id' => $assignment['id'],
                    ), $to_update_type, array(
                        '%d',
                    )
                    );
                }

                continue;
            } else {

                //if the assignment doesn't exist, insert it
                $this->insertAssignment($weights, $thisdex, $assignment, $gbid, $process_result['data']);

            }

        }

        //finally, let's check for individual users on the CSV
        //the user must exist on OpenLab, or they will by bypassed
        //if the user is not added to the current course they will be
        //@todo: limit users to only users that have joined this course
        //@todo: put in message for situation where data was successfully added, but without students
        if (!isset($process_result['data']) || empty($process_result['data'])) {
            return 'success - no students';
        }

        foreach ($process_result['data'] as $student) {

            $this->processStudent($student, $gbid, $assignments_stored);
        }

        return $process_result;
    }

    private function insertAssignment($weights, $thisdex, $assignment, $gbid, $students)
    {
        global $wpdb;

        $query = $wpdb->prepare("SELECT assign_order FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE gbid = %d", $gbid);
        $assignOrders = $wpdb->get_col($query);

        if (!$assignOrders) {
            $assignOrders = array(0);
        }
        $assignOrder = max($assignOrders) + 1;

        $assign_weight = 0;

        if (isset($weights[$thisdex])) {

            $assign_weight = floatval($weights[$thisdex]);
        }

        $result = $wpdb->insert("{$wpdb->prefix}oplb_gradebook_assignments", array(
            'assign_name' => sanitize_text_field($assignment['name']),
            'assign_date' => date('Y-m-d'),
            'assign_due' => date('Y-m-d', strtotime("+1 week")),
            'assign_category' => '',
            'assign_visibility' => 'Student',
            'assign_grade_type' => $assignment['type'],
            'gbid' => $gbid,
            'assign_order' => $assignOrder,
            'assign_weight' => $assign_weight,
        ), array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
            '%f',
        )
        );

        //also need to insert rows for any existing students - we'll address students off the CSV in a minute (existing or otherwise)
        $assignID = $wpdb->insert_id;

        $query = $wpdb->prepare("SELECT uid FROM {$wpdb->prefix}oplb_gradebook_users WHERE gbid = %d AND role = '%s'", $gbid, 'student');
        $studentIDs = $wpdb->get_results($query, ARRAY_N);
        $remaining_students = $studentIDs;

        foreach ($studentIDs as $key => $value) {

            foreach ($students as $student) {

                if (intval($student['student_id']) === intval($value[0])) {

                    unset($remaining_students[$key]);

                    $this_grade = $this->processGrade($student[$assignment['name']]['value'], $assignment['type']);

                    $is_null = 0;

                    if ($this_grade['value'] === '--') {
                        $is_null = 1;
                    }

                    $wpdb->insert("{$wpdb->prefix}oplb_gradebook_cells", array(
                        'amid' => $assignID,
                        'uid' => $value[0],
                        'gbid' => $gbid,
                        'assign_order' => $assignOrder,
                        'assign_points_earned' => $this_grade['value'],
                        'is_null' => $is_null,
                    ), array(
                        '%d',
                        '%d',
                        '%d',
                        '%d',
                        '%f',
                        '%d',
                    )
                    );
                }

            }
        }

        //handle studentst that are in Gradebook but not in the CSV
        foreach ($remaining_students as $remaining) {
            $wpdb->insert("{$wpdb->prefix}oplb_gradebook_cells", array(
                'amid' => $assignID,
                'uid' => $remaining[0],
                'gbid' => $gbid,
                'assign_order' => $assignOrder,
                'assign_points_earned' => 0,
            ), array(
                '%d',
                '%d',
                '%d',
                '%d',
                '%f',
            )
            );
        }
    }

    private function checkStudent($student, $gbid)
    {
        global $oplb_gradebook_api, $wpdb;
        $student_id = $student['student_id'];

        //first look up user by either first/last name or user login
        //@todo: add empty space detection to user login
        if (isset($student['username']) && !empty($student['username'])) {

            $student_lookup = get_user_by('login', sanitize_text_field($student['username']));

            if ($student_lookup && isset($student_lookup->ID)) {
                $student_id = $student_lookup->ID;
            }

        }

        //if the user login is not available, try for the first name and last name
        //both must be available for this to work
        //@todo: add empty space detection
        if (empty($student_id)
            && isset($student['firstname'])
            && !empty($student['firstname'])
            && isset($student['lastname'])
            && !empty($student['lastname'])) {

            $query = $wpdb->prepare("SELECT DISTINCT $wpdb->users.* FROM $wpdb->users INNER JOIN $wpdb->usermeta um1 ON um1.user_id = $wpdb->users.ID JOIN $wpdb->usermeta um2 ON um2.user_id = $wpdb->users.ID WHERE um1.meta_key = '%s' AND um1.meta_value LIKE '%s' AND um2.meta_key = '%s' AND um2.meta_value LIKE '%s';", 'first_name', $student['firstname'], 'last_name', $student['lastname']);
            $this_user = $wpdb->get_results($query);

            if ($this_user
                && !empty($this_user)
                && isset($this_user[0])
                && isset($this_user[0]->ID)) {
                $student_id = $this_user[0]->ID;
            }
        }

        if ($student_id === 0) {
            return false;
        }

        //check to see if student is already included in the course
        //if they are, we'll update their assignment cells accordingly
        $student_exists = $oplb_gradebook_api->oplb_gradebook_get_user($student_id, $gbid, true);

        if (!empty($student_exists)) {
            return $student_id;
        }

        return false;
    }

    /**
     * Process student data
     * @global type $wpdb
     * @return string
     * @todo: handle individual student errors, preferably with user feedback
     */
    private function processStudent($student, $gbid, $assignments)
    {
        global $wpdb, $oplb_gradebook_api;
        $result = 'success';
        $student_id = $student['student_id'];

        //check to see if student is already included in the course
        //if they are, we'll update their assignment cells accordingly
        $student_exists = $oplb_gradebook_api->oplb_gradebook_get_user($student_id, $gbid, true);

        if ($student_exists && !empty($student_exists)) {

            //update mid_semester grades
            $midsemester = $student['mid_semester_grade_value'];

            if (!empty($midsemester)) {
                $wpdb->update("{$wpdb->prefix}oplb_gradebook_users", array(
                    'mid_semester_grade' => $midsemester,
                ), array(
                    'gbid' => $gbid,
                    'uid' => $student_id,
                ), array(
                    '%s',
                ), array(
                    '%d',
                    '%d',
                )
                );
            }

            $final = $student['final_grade_value'];

            if (!empty($final)) {
                $wpdb->update("{$wpdb->prefix}oplb_gradebook_users", array(
                    'final_grade' => $final,
                ), array(
                    'gbid' => $gbid,
                    'uid' => $student_id,
                ), array(
                    '%s',
                ), array(
                    '%d',
                    '%d',
                )
                );
            }

            foreach ($assignments as $assignment) {

                $this_grade = $this->processGrade($student[$assignment['name']], $assignment['type']);
                $is_null = ($this_grade === '--' || empty($this_grade)) ? 1 : 0;

                $wpdb->update("{$wpdb->prefix}oplb_gradebook_cells", array(
                    'assign_points_earned' => $this_grade['value'],
                    'is_null' => $is_null,
                ), array(
                    'amid' => $assignment['amid'],
                    'uid' => $student_id,
                ), array(
                    '%f',
                    '%d',
                ), array(
                    '%d',
                    '%d',
                )
                );

            }
        } else {

            //currently not adding students
            //$this->addStudentToCourse($student_id, $gbid, $assignments, $grades);

        }

        return $result;
    }

    public function processGrade($grade, $type, $verify = false)
    {
        global $oplb_gradebook_api;
        $is_verified = false;

        //handle incoming array values
        if (is_array($grade)) {
            $grade = $grade['value'];
        }

        //cleanup
        $grade = preg_replace('/[[:cntrl:]]/', '', $grade);
        $type = trim($type);

        //handle values marked for null
        if (trim($grade === '--')) {
            $grade = trim($grade);
            $is_verified = true;
        } else if ($type === 'letter') {

            $possible_letter_grades = $this->getPossibleLetterGrades();

            if (empty($grade)) {

                //empty letter grades filp to null
                $grade = '--';
                $is_verified = true;

            } else if (in_array(strtolower(trim($grade)), $possible_letter_grades)) {
                $grade = $this->changeLetterGradeToNumeric($grade);
                $is_verified = true;
            } else if (is_numeric($grade)) {

                $letter_grades = $oplb_gradebook_api->getLetterGrades();

                foreach ($letter_grades as $letter_grade) {

                    if (intval($grade) < $letter_grade['range_high'] && intval($grade) >= $letter_grade['range_low']) {
                        $grade = $letter_grade['value'];
                        $is_verified = true;
                    }
                }

            }

        } else if ($type === 'numeric') {

            if (empty($grade)) {

                //empty numeric grades filp to null
                $grade = '--';
                $is_verified = true;

            } else if (is_numeric($grade)) {
                $is_verified = true;
            }

        } else if ($type === 'checkmark') {

            if (strtolower(trim($grade)) === 'x') {
                $grade = 100;
                $is_verified = true;
            } else if (empty(trim($grade))) {

                //empty checkmark grades flip to no checkmark
                $grade = 0;
                $is_verified = true;
            } else if (is_numeric(trim($grade))) {

                $is_verified = true;

            }

        }

        if ($verify && !$is_verified) {

            $grade = array(
                'type' => 'error',
                'value' => $grade . "**this value is not valid for {$type}-type grades**",
            );

        } else {
            $grade = array(
                'type' => $type,
                'value' => $grade,
            );
        }

        return $grade;
    }

    private function checkAssignmentType($index, $data)
    {
        $type = 'numeric';

        foreach ($data as $student) {

            if (!isset($student[$index]) || !$this->isStringNotEmpty($student[$index])) {
                continue;
            }

            $possible_letter_grades = $this->getPossibleLetterGrades();

            $to_test = $student[$index];

            if (in_array(strtolower(trim($to_test)), $possible_letter_grades)) {
                $type = 'letter';
                break;
            } else if (strtolower(trim($to_test)) === 'x') {
                $type = 'checkmark';
                break;
            }
        }

        return $type;
    }

    public function changeLetterGradeToNumeric($grade)
    {
        global $oplb_gradebook_api;
        $grade_out = 0;
        $letter_grades = $oplb_gradebook_api->getLetterGrades();

        foreach ($letter_grades as $letter_grade) {

            if (strtoupper(trim($grade)) === $letter_grade['label']) {
                $grade_out = $letter_grade['value'];
                return $grade_out;
            }
        }

        return $grade_out;
    }

    private function addStudentToCourse($student_id, $gbid, $assignments, $grades)
    {
        global $wpdb;

        //add student to course
        $wpdb->insert("{$wpdb->prefix}oplb_gradebook_users", array(
            'uid' => $student_id,
            'gbid' => $gbid,
            'role' => 'student'), array(
            '%d',
            '%d',
            '%s',
        )
        );
        //we need to get a list of all assignments, in case there are legacy assignments not on this spreadsheet
        $query = "SELECT id FROM {$wpdb->prefix}oplb_gradebook_assignments";
        $current_assignments = $wpdb->get_results($query, ARRAY_A);

        foreach ($assignments as $assigndex => $assignment) {

            $this_grade = $this->processGrade($grades[$assignment['name']], $assignment['type']);

            foreach ($current_assignments as $currentdex => $current_assignment) {

                if ($current_assignment['id'] === $assignment['amid']) {
                    unset($current_assignments[$currentdex]);
                }
            }

            $wpdb->insert("{$wpdb->prefix}oplb_gradebook_cells", array(
                'amid' => $assignment['amid'],
                'uid' => $student_id,
                'gbid' => $gbid,
                'assign_order' => $assignment['assign_order'],
                'assign_points_earned' => $this_grade['value'],
            ), array(
                '%d',
                '%d',
                '%d',
                '%d',
                '%f',
            )
            );
        }

        if (!empty($current_assignments)) {

            foreach ($current_assignments as $remaining_assignment) {

                $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE id = %d", $remaining_assignment['id']);
                $this_assignment_get = $wpdb->get_results($query, ARRAY_A);

                if (!$this_assignment_get || empty($this_assignment_get)) {
                    continue;
                }

                $this_assignment = $this_assignment_get[0];

                $wpdb->insert("{$wpdb->prefix}oplb_gradebook_cells", array(
                    'amid' => $this_assignment['id'],
                    'uid' => $student_id,
                    'gbid' => $gbid,
                    'assign_order' => $this_assignment['assign_order'],
                    'assign_points_earned' => 0,
                ), array(
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%f',
                )
                );
            }
        }

    }

    /**
     * Return count of regex matches for common type of upload attack eval(base64($malicious_payload))
     * @param  string $str [description]
     * @return int count of matches
     * Also borrowed from WordPress Frontend Uploader
     */
    private function _invoke_paranoia_on_file_contents($str = '')
    {
        // Not a string, bail
        if (!is_string($str)) {
            return 0;
        }

        return preg_match_all('/<\?php|eval\s*\(|base64_decode|gzinflate|gzuncompress/imsU', $str, $matches);
    }

    private function isStringNotEmpty($string, $return_string = false)
    {

        if ($string && !ctype_space($string) && $string !== '') {
            if ($return_string) {
                $return_value = $string;
            } else {
                $return_value = true;
            }
        } else {
            if ($return_string) {
                $return_value = '';
            } else {
                $return_value = null;
            }
        }

        return $return_value;
    }

    private function getPossibleLetterGrades($type = "")
    {

        $possible_letter_grades = array('a+', 'a', 'a-', 'b+', 'b', 'b-', 'c+', 'c', 'c-', 'd+', 'd', 'd-', 'f');

        if ($type === 'mid_semester') {
            $possible_letter_grades = array('bl', 'u', 'sa', 'p');
        } else if ($type === 'final') {
            $possible_letter_grades = array('a', 'a-', 'b+', 'b', 'b-', 'c+', 'c', 'c-', 'd', 'f', 'wf', 'wn', '*wn', 'wu');
        }

        return $possible_letter_grades;
    }

    private function handleErrors($process_result, $gbid)
    {
        $error_log = get_option('openlab_gradebook_csv_error_log', array());

        $error_log[$gbid] = $process_result;

        update_option('openlab_gradebook_csv_error_log', $error_log);

        $csv_link = admin_url("admin.php?page=oplb_gradebook&gradebook_download_csv={$gbid}#gradebook/{$gbid}");

        ob_start();
        include plugin_dir_path(__DIR__) . 'components/parts/partials/upload-csv-error.php';
        $outbound = ob_get_clean();

        $message = array(
            'response' => 'oplb-gradebook-error',
            'error' => $outbound,
        );
        wp_send_json($message);
    }

    private function buildCSV($data_out)
    {

        if (empty($data_out)) {
            return null;
        }

        $df = fopen("php://output", 'w');

        //for special char encoding
        fputs($df, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        if (empty($data_out['data']) && !empty($data_out['headers'])) {

            fputcsv($df, $data_out['headers']);
            fclose($df);

        } else {

            $header_row = array();
            foreach ($data_out['headers'] as $header) {
                array_push($header_row, $header);
            }

            fputcsv($df, $header_row);

            if (!empty($data_out['types'])) {
                $types_row = array();

                foreach ($data_out['types'] as $type) {
                    array_push($types_row, $type);
                }

                fputcsv($df, $types_row);

            }

            foreach ($data_out['data'] as $row) {
                fputcsv($df, $row);
            }
            fclose($df);
        }

    }

    private function outputCSV($data_out, $filename)
    {
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: UTF-8");

        $this->buildCSV($data_out);
        die();
    }

    public function download_csv()
    {
        global $oplb_gradebook_api;

        if (empty($_REQUEST['gradebook_download_csv'])) {
            return true;
        }

        $download_gbid = filter_var($_REQUEST['gradebook_download_csv'], FILTER_SANITIZE_NUMBER_INT);

        if (empty($download_gbid)) {
            return true;
        }

        $error_log = get_option('openlab_gradebook_csv_error_log');

        $this_data = $error_log[$download_gbid];

        //a little cleanup
        foreach ($this_data['data'] as &$student) {

            if (!empty($student['mid_semester_grade_value'])) {
                unset($student['mid_semester_grade_value']);
            }

            if (!empty($student['final_grade_value'])) {
                unset($student['final_grade_value']);
            }

            if (!empty($student['student_id'])) {
                unset($student['student_id']);
            }

            $assigndex = $this->getAssignmentIndexStart();
            $studentdex = 0;

            foreach ($student as &$item) {

                if ($studentdex <= $assigndex) {
                    $studentdex++;
                    continue;
                }

                if (is_array($item)) {
                    //only convert values that are not errors
                    if ($item['type'] !== 'error') {
                        $item = $this->convertCSVValues($item['value'], $item['type']);
                    } else {
                        $item = $item['value'];
                    }
                }

                $studentdex++;
            }

        }

        $this_data['data'] = $oplb_gradebook_api->sort_array_by($this_data['data'], 'lastname');

        $filename = str_replace(".csv", "", $this_data['file']['name']) . "_errors.csv";

        $this_data['headers'] = $oplb_gradebook_api->special_char_handling($this_data['headers'], ENT_QUOTES);
        $this_data['types'] = $oplb_gradebook_api->special_char_handling($this_data['types'], ENT_QUOTES);

        $this->outputCSV($this_data, $filename);

    }

    private function convertCSVValues($value, $type)
    {
        global $oplb_gradebook_api;

        switch ($type) {
            case 'letter':

                if (is_numeric($value)) {
                    $value = $oplb_gradebook_api->numeric_to_letter_grade_conversion($value);
                }

                break;
            case 'checkmark':

                if (is_numeric($value)) {
                    if (floatval($value) >= 60) {
                        $value = 'x';
                    } else {
                        $value = '';
                    }
                }

                break;
        }

        return $value;
    }

    private function getAssignmentIndexStart()
    {
        return 4;
    }

}
