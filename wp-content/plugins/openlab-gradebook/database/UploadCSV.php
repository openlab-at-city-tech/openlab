<?php

class gradebook_upload_csv_API {

    public function __construct() {
        ini_set('auto_detect_line_endings', true);
        //add_action('wp_ajax_oplb_gradebook_upload_csv', array($this, 'upload_csv'));
    }

    /**
     * Handle uploading of the CSV
     */
    public function upload_csv($files, $name) {

        $gbid = $files['gbid'];

        $allowed_types = array('text/csv');
        $message = array(
            'response' => 'oplb-gradebook-success',
            'content' => 'CSV successfully uploaded to OpenLab Gradebook.',
        );

        $fields = array('file', 'type', 'url');
        foreach ($fields as $field) {
            $k[$field] = $files[$field];
        }

        $k['name'] = sanitize_file_name($name);

        if ($k['file'] == "") {
            $message = array(
                'response' => 'oplb-gradebook-error',
                'content' => 'CSV not successfully uploaded.',
            );
            return $message;
        }

        $typecheck = wp_check_filetype_and_ext($k['file'], $k['name'], false);

        // Add an error message if MIME-type is not allowed
        if (!in_array($typecheck['type'], $allowed_types)) {
            $message = array(
                'response' => 'oplb-gradebook-error',
                'content' => 'This file does not appear to be a CSV.',
            );
            return $message;
        }

        // Now let's try to catch eval( base64() ) et al
        if (0 !== $this->_invoke_paranoia_on_file_contents(file_get_contents($k['file']))) {
            $message = array(
                'response' => 'oplb-gradebook-error',
                'content' => 'Suspicious file error.',
            );
            return $message;
        }

        $parse_result = $this->parseCSV($k['file']);

        $process_result = $this->processData($parse_result, $gbid);

        return $message;
    }

    /**
     * Parse the incoming CSV file
     * @param type $file
     * @return type
     */
    public function parseCSV($file) {

        // Create an array to hold the data
        $arrData = array();

        // Create a variable to hold the header information
        $header = NULL;

        // If the file can be opened as readable, bind a named resource
        if (($handle = fopen($file, 'r')) !== FALSE) {
            // Loop through each row
            while (($row = fgetcsv($handle)) !== FALSE) {

                $row = array_map("utf8_encode", $row);

                // If the header has been stored
                if ($header) {
                    // Create an associative array with the data
                    $arrData['data'][] = array_combine($header, $row);
                }
                // Else the header has not been stored
                else {

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
                }
            }

            // Close the file pointer
            fclose($handle);
        }

        return $arrData;
    }

    /**
     * 
     * @global type $wpdb
     * @param type $process_result
     * @param type $gbid
     * @return string
     */
    private function processData($process_result, $gbid) {
        global $wpdb;
        $assignments_stored = array();

        //if the headers are not present in this CSV, we are abadoning ship
        if (!isset($process_result['headers'])) {
            $message = array(
                'response' => 'oplb-gradebook-error',
                'content' => 'This CSV file does not have the necessary headers.',
            );
            return $message;
        }

        $headers = $process_result['headers'];
        $check_array = array(
            0 => 'firstname',
            1 => 'lastname',
            2 => 'user_login',
        );

        $formatted_correctly = true;
        $assignments = array();
        $assignmentdex = 0;

        foreach ($headers as $index => $header) {

            //start setting up assignments, so we have them if this CSV is formatted correctly
            if ($index > 2) {
                $assignments[$assignmentdex] = $header;
                $assignmentdex++;
                continue;
            }

            if ($check_array[$index] !== strtolower(trim($header))) {
                $formatted_correctly = false;
            }
        }

        if (!$formatted_correctly) {

            $message = array(
                'response' => 'oplb-gradebook-error',
                'content' => 'This CSV file is not formatted correctly.',
            );
            return $message;
        }

        //we'll first setup the assignments, in case this is a blank document
        if (empty($assignments)) {
            $message = array(
                'response' => 'oplb-gradebook-error',
                'content' => 'This CSV file does not have any assignments listed.',
            );
            return $message;
        }

        $weights = array();
        $weight_normalize = 1;

        //only add weights if they are properly formatted
        if ($process_result['weights'][2] === 'weight') {

            $weights = array_slice($process_result['weights'], 3);
        }

        foreach ($assignments as $thisdex => $assignment) {

            //if there is student data, we'll try and determine the assignment's type
            //if not, we'll default to numeric (the standard default)
            $type = 'numeric';
            $default_type = false;
            if (!isset($process_result['data']) || empty($process_result['data'])) {
                $default_type = true;
            } else {
                //use the first row of student data to determine the type
                $type = $this->checkAssignmentType($assignment, $process_result['data']);
            }

            //check for existing assignments first
            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE assign_name LIKE '%s'", $assignment);
            $existing_assignment = $wpdb->get_results($query);

            if ($existing_assignment && !empty($existing_assignment)) {
                $to_update = array();
                $to_update_type = array();

                //update info that needs to be updated store assignment info for later use
                $assignments_stored[$thisdex]['name'] = $assignment;
                $assignments_stored[$thisdex]['amid'] = $existing_assignment[0]->id;
                $assignments_stored[$thisdex]['assign_order'] = $existing_assignment[0]->assign_order;


                $assignments_stored[$thisdex]['assign_weight'] = $existing_assignment[0]->assign_weight;
                //for weights, we will update the weight as present on the spreadsheet
                if ($this->isStringNotEmpty(trim($weights[$thisdex]))) {
                    $to_update['assign_weight'] = floatval($weights[$thisdex]);
                    array_push($to_update_type, '%f');
                    $assignments_stored[$thisdex]['assign_weight'] = floatval($weights[$thisdex]);
                }

                $assignments_stored[$thisdex]['assign_grade_type'] = $existing_assignment[0]->assign_grade_type;
                //for grade type - if the type is already set in the database, we won't do anyting
                //if it's empty, we'll use the type determined from the data in the CSV
                if (!$this->isStringNotEmpty($existing_assignment[0]->assign_grade_type)) {
                    $to_update['assign_grade_type'] = $type;
                    array_push($to_update_type, '%s');
                    $assignments_stored[$thisdex]['assign_grade_type'] = $type;
                }

                //if the visiblity is empty, default to 'Student'
                if (!$this->isStringNotEmpty($existing_assignment[0]->assign_visibility)) {
                    $to_update['assign_visibility'] = 'Student';
                    array_push($to_update_type, '%s');
                }

                if (!empty($to_update)) {

                    $wpdb->update("{$wpdb->prefix}oplb_gradebook_assignments", $to_update, array(
                        'id' => $existing_assignment[0]->id,
                            ), $to_update_type, array(
                        '%d',
                            )
                    );
                }

                continue;
            }

            //if the assignment doesn't exist, insert it
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
                'assign_name' => sanitize_text_field($assignment),
                'assign_date' => date('Y-m-d'),
                'assign_due' => date('Y-m-d', strtotime("+1 week")),
                'assign_category' => '',
                'assign_visibility' => 'Student',
                'assign_grade_type' => 'numeric',
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

            //store assignment info for later use
            $assignments_stored[$thisdex]['name'] = $assignment;
            $assignments_stored[$thisdex]['amid'] = $assignID;
            $assignments_stored[$thisdex]['assign_order'] = $assignOrder;
            $assignments_stored[$thisdex]['assign_weight'] = $assign_weight;

            $query = $wpdb->prepare("SELECT uid FROM {$wpdb->prefix}oplb_gradebook_users WHERE gbid = %d AND role = '%s'", $gbid, 'student');
            $studentIDs = $wpdb->get_results($query, ARRAY_N);
            
            foreach ($studentIDs as $value) {
                $wpdb->insert("{$wpdb->prefix}oplb_gradebook_cells", array(
                    'amid' => $assignID,
                    'uid' => $value[0],
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

        return 'success';
    }

    /**
     * Process student data
     * @global type $wpdb
     * @return string
     * @todo: handle individual student errors, preferably with user feedback
     */
    private function processStudent($student, $gbid, $assignments) {
        global $wpdb, $oplb_gradebook_api;
        $result = 'success';
        $student_id = 0;
        $grades = array_slice($student, 3);

        //first look up user by either first/last name or user login
        //@todo: add empty space detection to user login
        if (isset($student['user_login']) && !empty($student['user_login'])) {

            $student_lookup = get_user_by('login', sanitize_text_field($student['user_login']));

            if ($student_lookup && isset($student_lookup->ID)) {
                $student_id = $student_lookup->ID;
            }
        }

        //if the user login is not available, try for the first name and last name
        //both must be available for this to work
        //@todo: add empty space detection
        if ($student_id === 0
                && isset($student['firstname'])
                && !empty($student['firstname'])
                && isset($student['lastname'])
                && !empty($student['lastname'])) {

            $search_name = "{$student['firstname']} {$student['lastname']}";

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
            return 'student not found';
        }

        //check to see if student is already included in the course
        //if they are, we'll update their assignment cells accordingly
        $student_exists = $oplb_gradebook_api->oplb_gradebook_get_user($student_id, $gbid, true);

        if ($student_exists && !empty($student_exists)) {

            foreach ($assignments as $assigndex => $assignment) {

                $this_grade = $this->processGrade($grades[$assignment['name']]);

                $wpdb->update("{$wpdb->prefix}oplb_gradebook_cells", array(
                    'assign_points_earned' => $this_grade
                        ), array(
                    'amid' => $assignment['amid'],
                    'uid' => $student_id,
                        ), array(
                    '%f',
                        ), array(
                    '%d',
                    '%d',
                        )
                );
            }
        } else {

            //add student to course
            $wpdb->insert("{$wpdb->prefix}oplb_gradebook_users", array(
                'uid' => $student_id,
                'gbid' => $gbid,
                'role' => 'student'), array(
                '%d',
                '%d',
                '%s'
                    )
            );
            //we need to get a list of all assignments, in case there are legacy assignments not on this spreadsheet
            $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}oplb_gradebook_assignments");
            $current_assignments = $wpdb->get_results($query, ARRAY_A);

            foreach ($assignments as $assigndex => $assignment) {

                $this_grade = $this->processGrade($grades[$assignment['name']]);

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
                    'assign_points_earned' => $this_grade
                        ), array(
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%f'
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
                        'assign_points_earned' => 0
                            ), array(
                        '%d',
                        '%d',
                        '%d',
                        '%d',
                        '%f'
                            )
                    );
                }
            }
        }

        return $result;
    }

    public function processGrade($grade) {

        $possible_letter_grades = $this->getPossibleLetterGrades();

        if (in_array(strtolower(trim($grade)), $possible_letter_grades)) {
            $grade = $this->changeLetterGradeToNumeric($grade);
        } else if (strtolower(trim($grade)) === 'x') {
            $grade = 100;
        } else if (!$this->isStringNotEmpty(trim($grade))) {
            $grade = 0;
        }

        return $grade;
    }

    private function checkAssignmentType($index, $data) {
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

    public function changeLetterGradeToNumeric($grade) {
        $grade_out = 0;
        $letter_grades = $this->getLetterGrades();

        foreach ($letter_grades as $letter_grade) {

            if (strtoupper(trim($grade)) === $letter_grade['label']) {
                $grade_out = $letter_grade['value'];
                return $grade_out;
            }
        }

        return $grade_out;
    }

    /**
     * Return count of regex matches for common type of upload attack eval(base64($malicious_payload))
     * @param  string $str [description]
     * @return int count of matches
     * Also borrowed from WordPress Frontend Uploader
     */
    private function _invoke_paranoia_on_file_contents($str = '') {
        // Not a string, bail
        if (!is_string($str))
            return 0;

        return preg_match_all('/<\?php|eval\s*\(|base64_decode|gzinflate|gzuncompress/imsU', $str, $matches);
    }

    private function isStringNotEmpty($string, $return_string = false) {

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
                $return_value = NULL;
            }
        }

        return $return_value;
    }

    private function getPossibleLetterGrades() {

        $possible_letter_grades = array('a+', 'a', 'a-', 'b+', 'b', 'b-', 'c+', 'c', 'c-', 'd+', 'd', 'd-', 'f', 'inc');

        return $possible_letter_grades;
    }

    public function getLetterGrades() {

        $letter_grades = array(
            array(
                label => 'A+',
                value => 100,
                range_low => 100,
                range_high => 101
            ),
            array(
                label => 'A',
                value => 96,
                range_low => 93,
                range_high => 100
            ),
            array(
                label => 'A-',
                value => 91.5,
                range_low => 90,
                range_high => 93
            ),
            array(
                label => 'B+',
                value => 88.5,
                range_low => 87,
                range_high => 90
            ),
            array(
                label => 'B',
                value => 85,
                range_low => 83,
                range_high => 87
            ),
            array(
                label => 'B-',
                value => 81.5,
                range_low => 80,
                range_high => 83
            ),
            array(
                label => 'C+',
                value => 78.5,
                range_low => 77,
                range_high => 80
            ),
            array(
                label => 'C',
                value => 75,
                range_low => 73,
                range_high => 77
            ),
            array(
                label => 'C-',
                value => 71.5,
                range_low => 70,
                range_high => 73
            ),
            array(
                label => 'D+',
                value => 68.5,
                range_low => 67,
                range_high => 70
            ),
            array(
                label => 'D',
                value => 65,
                range_low => 63,
                range_high => 67
            ),
            array(
                label => 'D-',
                value => 61.5,
                range_low => 60,
                range_high => 63
            ),
            array(
                label => 'F',
                value => 50,
                range_low => 1,
                range_high => 60
            )
        );

        return $letter_grades;
    }

    public function numeric_to_letter_grade_conversion($number) {

        $letter_grades = $this->getLetterGrades();

        foreach ($letter_grades as $letter_grade) {

            if ($number < $letter_grade['range_high'] && $number >= $letter_grade['range_low']) {

                $letter = $letter_grade['label'];
                break;
            }
        }

        return $letter;
    }

}
