<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

ob_start();

class Questions_List_Table extends WP_List_Table{
    private $plugin_name;
    protected $used_questions;
    /** Class constructor */
    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        parent::__construct( array(
            'singular' => __( 'Question', $this->plugin_name ), //singular name of the listed records
            'plural'   => __( 'Questions', $this->plugin_name ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ) );
        $this->used_questions = Quiz_Maker_Admin::get_published_questions_used();
        add_action( 'admin_notices', array( $this, 'question_notices' ) );
    }

    
    
    /**
     * Override of table nav to avoid breaking with bulk actions & according nonce field
     */
    public function display_tablenav( $which ) {
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">
            
            <div class="alignleft actions">
                <?php $this->bulk_actions( $which ); ?>
            </div>
             
            <?php
            $this->extra_tablenav( $which );
            $this->pagination( $which );
            ?>
            <br class="clear" />
        </div>
        <?php
    }
    
    public function extra_tablenav( $which ){
        global $wpdb;
        $titles_sql = "SELECT {$wpdb->prefix}aysquiz_categories.title,{$wpdb->prefix}aysquiz_categories.id FROM {$wpdb->prefix}aysquiz_categories";
        $cat_titles = $wpdb->get_results($titles_sql);
        $cat_id = null;
        if( isset( $_GET['filterby'] )){
            $cat_id = intval($_GET['filterby']);
        }
        $categories_select = array();
        foreach($cat_titles as $key => $cat_title){
            $selected = "";
            if($cat_id === intval($cat_title->id)){
                $selected = "selected";
            }
            $categories_select[$cat_title->id]['title'] = $cat_title->title;
            $categories_select[$cat_title->id]['selected'] = $selected;
            $categories_select[$cat_title->id]['id'] = $cat_title->id;
        }
        sort($categories_select);
        ?>
        <div id="category-filter-div" class="alignleft actions bulkactions">
            <select name="filterby" id="bulk-action-selector-top">
                <option value=""><?php echo __('Select Category',$this->plugin_name)?></option>
                <?php
                    foreach($categories_select as $key => $cat_title){
                        echo "<option ".$cat_title['selected']." value='".$cat_title['id']."'>".$cat_title['title']."</option>";
                    }
                ?>
            </select>
            <input type="button" id="doaction" class="cat-filter-apply button" value="Filter">
        </div>
        <a style="margin: 3px 8px 0 0;display:inline-block;" href="?page=<?php echo $_REQUEST['page'] ?>" class="button"><?php echo __( "Clear filters", $this->plugin_name ); ?></a>
        <?php
    }

    
    protected function get_views() {
        $published_count = $this->published_questions_count();
        $unpublished_count = $this->unpublished_questions_count();
        $all_count = $this->all_record_count();
        $selected_all = "";
        $selected_0 = "";
        $selected_1 = "";
        if(isset($_GET['fstatus'])){
            switch($_GET['fstatus']){
                case "0":
                    $selected_0 = " style='font-weight:bold;' ";
                    break;
                case "1":
                    $selected_1 = " style='font-weight:bold;' ";
                    break;
                default:
                    $selected_all = " style='font-weight:bold;' ";
                    break;
            }
        }else{
            $selected_all = " style='font-weight:bold;' ";
        }
        $query_str = Quiz_Maker_Admin::ays_query_string(array("status", "fstatus"));
        $status_links = array(
            "all" => "<a ".$selected_all." href='?".esc_attr( $query_str )."'>All (".$all_count.")</a>",
            "published" => "<a ".$selected_1." href='?".esc_attr( $query_str )."&fstatus=1'>Published (".$published_count.")</a>",
            "unpublished"   => "<a ".$selected_0." href='?".esc_attr( $query_str )."&fstatus=0'>Unpublished (".$unpublished_count.")</a>"
        );
        return $status_links;
    }
    
    
    /**
     * Retrieve customers data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_questions( $per_page = 20, $page_number = 1, $search = '' ) {

        global $wpdb;
        
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions";
        
        $where = array();
        
        if( $search != '' ){
            $sql .= $search;
        }
        
        if(! empty( $_REQUEST['filterby'] ) && $_REQUEST['filterby'] > 0){
            $cat_id = intval($_REQUEST['filterby']);
            $where[] = ' category_id = '.$cat_id.'';
        }
        if( isset( $_REQUEST['type'] ) ){
            $where[] = ' type = "'.$_REQUEST['type'].'" ';
        }
        if( isset( $_REQUEST['fstatus'] ) ){
            $fstatus = $_REQUEST['fstatus'];
            if($fstatus !== null){
                $where[] = " published = ".$fstatus." ";
            }
        }
        if( ! empty($where) ){
            $sql .= " WHERE " . implode( " AND ", $where );
        }
        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ESC';
        }else{
            $sql .= ' ORDER BY id DESC';
        }
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }


    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_questions( $id ) {
        global $wpdb;
        $wpdb->delete(
            "{$wpdb->prefix}aysquiz_questions",
            array( 'id' => $id ),
            array( '%d' )
        );

        $wpdb->delete(
            "{$wpdb->prefix}aysquiz_answers",
            array('question_id' => $id),
            array('%d')
        );
        $sql = "SELECT `question_ids` ,`id` FROM {$wpdb->prefix}aysquiz_quizes";
        $quizzes = $wpdb->get_results($sql);
        if(!empty($quizzes)) {
            foreach ($quizzes as $quiz) {
                $quiz_questions = explode(',', $quiz->question_ids);
                if (($key = array_search($id, $quiz_questions)) !== false) {
                    unset($quiz_questions[$key]);
                }
                $quiz_questions_implode = implode(',', $quiz_questions);
                $update_sql = "UPDATE {$wpdb->prefix}aysquiz_quizes SET question_ids='{$quiz_questions_implode}' WHERE id={$quiz->id}";
                $wpdb->get_var($update_sql);
            }
        }

    }

    public function get_question_categories() {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_categories";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public function get_question( $id ) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions WHERE id=" . absint( intval( $id ) );

        $result = $wpdb->get_row($sql, 'ARRAY_A');

        return $result;
    }

    public function get_question_answers( $question_id ) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_answers WHERE question_id=" . absint( intval( $question_id ) );

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    public function add_edit_questions($data){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $ays_change_type = (isset($data['ays_change_type']))?$data['ays_change_type']:'';
        if( isset($data["question_action"]) && wp_verify_nonce( $data["question_action"],'question_action' ) ){

            $id = absint( intval( $data['id'] ) );
            $question           = wpautop($data['ays_question']);
            $question_hint      = wpautop($data['ays_question_hint']);
            $question_image     = ($data['ays_question_image'] != '') ? $data['ays_question_image'] : NULL;
            $category_id        = absint( intval( $data['ays_question_category'] ) );
            $published          = absint( intval( $data['ays_publish'] ) );
            $user_explanation   = sanitize_text_field( $data['ays_user_explanation'] );
            $type               = sanitize_text_field( $data['ays_question_type'] );
            $correct_answers    = $data['ays-correct-answer'];
            $answers_weight     = $data['ays-answer-weight'];
            $answer_values      = $data['ays-correct-answer-value'];
            $answer_placeholders = isset($data['ays-answer-placeholder']) ? $data['ays-answer-placeholder'] : array();
            $answer_image       = $data['ays_answer_image'];
            $wrong_answer_text  = $data['wrong_answer_text'];
            $right_answer_text  = $data['right_answer_text'];
            $explanation        = $data['explanation'];
            $not_influence_to_score = (isset($data['ays_not_influence_to_score']) && $data['ays_not_influence_to_score'] == 'on') ? 'on' : 'off';
            $question_weight = (isset($data['ays_question_weight']) && $data['ays_question_weight'] != '') ? floatval($data['ays_question_weight']) : floatval(1);


            $quest_create_date  = !isset($data['ays_question_ctrate_date']) ? '0000-00-00 00:00:00' : $data['ays_question_ctrate_date'];
            $author = isset($data['ays_question_author'])?stripslashes($data['ays_question_author']):'';
            $author = json_decode($author, true);
            $bg_image = isset($data['ays_question_bg_image']) && $data['ays_question_bg_image'] != "" ? $data['ays_question_bg_image'] : "";
                
            // Use HTML for answers
            $use_html = (isset($data['ays-use-html']) && $data['ays-use-html'] == 'on') ? 'on' : 'off';

            $options = array(
                'author' => $author,
				'bg_image' => $bg_image,
                'use_html' => $use_html,
            );
            
            if($id == 0) {
                $question_result = $wpdb->insert(
                    $questions_table,
                    array(
                        'category_id'       => $category_id,
                        'question'          => $question,
                        'question_image'    => $question_image,
                        'type'              => $type,
                        'published'         => $published,
                        'wrong_answer_text' => $wrong_answer_text,
                        'right_answer_text' => $right_answer_text,
                        'question_hint'     => $question_hint,
                        'explanation'       => $explanation,
                        'user_explanation'  => $user_explanation,
                        'create_date'       => $quest_create_date,
                        'not_influence_to_score' => $not_influence_to_score,
                        'weight'            => $question_weight,
                        'options'           => json_encode($options),
                    ),
                    array(
                        '%d', // category_id
                        '%s', // question
                        '%s', // question_image
                        '%s', // type
                        '%d', // published
                        '%s', // wrong_answer_text
                        '%s', // right_answer_text
                        '%s', // question_hint
                        '%s', // explanation
                        '%s', // user_explanation
                        '%s', // create_date
                        '%s', // not_influence_to_score
                        '%f', // weight
                        '%s', // options
                    )
                );
                $question_id = $wpdb->insert_id;
                $answers_results = array();
                $flag = true;
                foreach ($answer_values as $index => $answer_value) {
                    if($type == "text"){
                        $correct = 1;
                    }else{
                        $correct = (in_array(($index + 1), $correct_answers)) ? 1 : 0;
                    }
                    $weight = $answers_weight[$index];

                    $placeholder = '';
                    if(isset($answer_placeholders[$index])){
                        $placeholder = $answer_placeholders[$index];
                    }

                    $answers_results[] = $wpdb->insert(
                        $answers_table,
                        array(
                            'question_id'   => $question_id,
                            'answer'        => stripslashes($answer_value),
                            'image'         => $answer_image[$index],
                            'correct'       => $correct,
                            'ordering'      => ($index + 1),
                            'weight'        => $weight,
                            'placeholder'   => $placeholder
                        ),
                        array(
                            '%d',
                            '%s',
                            '%s',
                            '%d',
                            '%d',
                            '%f',
                            '%s'
                        )
                    );
                }

                foreach ($answers_results as $answers_result) {
                    if ($answers_result >= 0) {
                        $flag = true;
                    } else {
                        $flag = false;
                        break;
                    }
                }
                $message = 'created';
            }else{
                $question_result = $wpdb->update(
                    $questions_table,
                    array(
                        'category_id'       => $category_id,
                        'question'          => $question,
                        'question_image'    => $question_image,
                        'type'              => $type,
                        'published'         => $published,
                        'wrong_answer_text' => $wrong_answer_text,
                        'right_answer_text' => $right_answer_text,
                        'question_hint'     => $question_hint,
                        'explanation'       => $explanation,
                        'user_explanation'  => $user_explanation,
                        'create_date'       => $quest_create_date,
                        'not_influence_to_score' => $not_influence_to_score,
                        'weight'            => $question_weight,
                        'options'           => json_encode($options),
                    ),
                    array( 'id' => $id ),
                    array(
                        '%d', // category_id
                        '%s', // question
                        '%s', // question_image
                        '%s', // type
                        '%d', // published
                        '%s', // wrong_answer_text
                        '%s', // right_answer_text
                        '%s', // question_hint
                        '%s', // explanation
                        '%s', // user_explanation
                        '%s', // create_date
                        '%s', // not_influence_to_score
                        '%f', // weight
                        '%s', // options
                    ),
                    array( '%d' )
                );

                $answers_results = array();
                $flag = true;
                $old_answers = $this->get_question_answers( $id );
                $old_answers_count = count( $old_answers );

                if($old_answers_count == count($answer_values)){
                    foreach ($answer_values as $index => $answer_value) {
                        if($type == "text"){
                            $correct = 1;
                        }else{
                            $correct = (in_array(($index + 1), $correct_answers)) ? 1 : 0;
                        }
                        $weight = $answers_weight[$index];

                        $placeholder = '';
                        if(isset($answer_placeholders[$index])){
                            $placeholder = $answer_placeholders[$index];
                        }

                        $answers_results[] = $wpdb->update(
                            $answers_table,
                            array(
                                'question_id'   => $id,
                                'answer'        => stripslashes($answer_value),
                                'image'         => $answer_image[$index],
                                'correct'       => $correct,
                                'ordering'      => ($index + 1),
                                'weight'        => $weight,
                                'placeholder'   => $placeholder
                            ),
                            array('id' => $old_answers[$index]["id"]),
                            array(
                                '%d',
                                '%s',
                                '%s',
                                '%d',
                                '%d',
                                '%f',
                                '%s'
                            ),
                            array('%d')
                        );
                    }
                }

                if($old_answers_count < count($answer_values)){
                    foreach ($answer_values as $index => $answer_value) {
                        if($type == "text"){
                            $correct = 1;
                        }else{
                            $correct = (in_array(($index + 1), $correct_answers)) ? 1 : 0;
                        }
                        $weight = $answers_weight[$index];

                        $placeholder = '';
                        if(isset($answer_placeholders[$index])){
                            $placeholder = $answer_placeholders[$index];
                        }

                        if( $old_answers_count < ( $index + 1) ){
                            $answers_results[] = $wpdb->insert(
                                $answers_table,
                                array(
                                    'question_id'   => $id,
                                    'answer'        => stripslashes($answer_value),
                                    'image'         => $answer_image[$index],
                                    'correct'       => $correct,
                                    'ordering'      => ($index + 1),
                                    'weight'        => $weight,
                                    'placeholder'   => $placeholder
                                ),
                                array(
                                    '%d',
                                    '%s',
                                    '%s',
                                    '%d',
                                    '%d',
                                    '%f',
                                    '%s'
                                )
                            );
                        }else{
                            $weight = $answers_weight[$index];
                            $answers_results[] = $wpdb->update(
                                $answers_table,
                                array(
                                    'question_id'   => $id,
                                    'answer'        => stripslashes($answer_value),
                                    'image'         => $answer_image[$index],
                                    'correct'       => $correct,
                                    'ordering'      => ($index + 1),
                                    'weight'        => $weight,
                                    'placeholder'   => $placeholder
                                ),
                                array('id' => $old_answers[$index]["id"]),
                                array(
                                    '%d',
                                    '%s',
                                    '%s',
                                    '%d',
                                    '%d',
                                    '%f',
                                    '%s'
                                ),
                                array('%d')
                            );
                        }
                    }
                }

                if($old_answers_count > count($answer_values)){
                    $diff = $old_answers_count - count($answer_values);

                    $removeable_answers = array_slice( $old_answers, -$diff, $diff );

                    foreach ( $removeable_answers as $removeable_answer ){
                        $delete_result = $wpdb->delete( $answers_table, array('id' => intval( $removeable_answer["id"] )) );
                    }

                    foreach ($answer_values as $index => $answer_value) {
                        if($type == "text"){
                            $correct = 1;
                        }else{
                            $correct = (in_array(($index + 1), $correct_answers)) ? 1 : 0;
                        }
                        $weight = $answers_weight[$index];
                        $placeholder = '';
                        if(isset($answer_placeholders[$index])){
                            $placeholder = $answer_placeholders[$index];
                        }
                        $answers_results[] = $wpdb->update(
                            $answers_table,
                            array(
                                'question_id'   => $id,
                                'answer'        => $answer_value,
                                'correct'       => $correct,
                                'ordering'      => ($index + 1),
                                'weight'        => $weight,
                                'placeholder'   => $placeholder
                            ),
                            array('id' => $old_answers[$index]["id"]),
                            array(
                                '%d',
                                '%s',
                                '%d',
                                '%d',
                                '%f',
                                '%s'
                            ),
                            array('%d')
                        );
                    }
                }
                foreach ($answers_results as $answers_result) {
                    if ($answers_result >= 0) {
                        $flag = true;
                    } else {
                        $flag = false;
                        break;
                    }
                }
                $message = "updated";
            }

            if( $question_result >= 0 && $flag == true ) {
                if($ays_change_type == 'apply'){
                    if($id == null){
                        $url = esc_url_raw( add_query_arg( array(
                            "action"    => "edit",
                            "question"  => $question_id,
                            "status"    => $message
                        ) ) );
                    }else{
                        $url = esc_url_raw( remove_query_arg(false) ) . '&status=' . $message;
                    }
                    wp_redirect( $url );
                }elseif($ays_change_type == 'save_new'){
                    $url = remove_query_arg( array('question') );
                    $url = add_query_arg( array(
                        "action"    => "add",
                        "status"    => $message
                    ), $url );
                    wp_redirect( $url );
                }else{
                    $url = esc_url_raw( remove_query_arg( array('action', 'question') ) ) . '&status=' . $message;
                    wp_redirect( $url );
                }
            }
        }
    }

	public function questions_import( $import_file ) {
		global $wpdb;
		$name_arr = explode('.', $import_file['name']);
		$type     = end($name_arr);

		$questions_table = $wpdb->prefix . "aysquiz_questions";
		$answers_table   = $wpdb->prefix . "aysquiz_answers";
		$questions_lines = fopen($import_file['tmp_name'], 'r');

		$user_id = get_current_user_id();
		$user    = get_userdata($user_id);
		$author  = array(
			'id'   => $user->ID,
			'name' => $user->data->display_name
		);
        
		$options = array(
			'author' => $author,
		);
        
        $categories_r = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}aysquiz_categories", 'ARRAY_A');
        $for_import = array();
        $categories = array();
        foreach($categories_r as $cat){
            $categories[] = $cat['id'];
        }
		switch ( $type ) {
			case 'xlsx':
//			case 'xls':
				$ver = (float) phpversion();
				if ($ver >= 5.6) {
					require_once(AYS_QUIZ_DIR . 'includes/PHPExcel/vendor/autoload.php');
					$spreadsheet = IOFactory::load($import_file['tmp_name']);
					$sheet_data  = $spreadsheet->getActiveSheet()->toArray("", true, true, true);
                    
					$headers     = $sheet_data[1];
					unset($sheet_data[1]);
					//walk and array_combine with array_values
					foreach ( $sheet_data as &$row ) {
						$values = array_values($row);
						$row = array_combine($headers, $values);
                        if(isset($row['answers'])){
				            $row['answers'] = json_decode($row['answers'], true);
                        }else{
                            $row['answers'] = array();
                        }
                        if(isset($row['options']['bg_image']) && $row['options']['bg_image'] != ''){
                            $options['bg_image'] = $row['options']['bg_image'];
                        }
                        $row['options'] = $options;
					}
					$questions = array_values($sheet_data);
                    
					foreach ( $questions as &$question ) {
                        
                        $for_import[] = array(
                            'category_id'       => in_array(strval($question['category_id']), $categories) ? intval($question['category_id']) : 1,
                            'question'          => htmlspecialchars_decode($question['question'], ENT_HTML5),
                            'question_image'    => $question['question_image'],
                            'question_hint'     => htmlspecialchars_decode($question['question_hint'], ENT_HTML5),
                            'type'              => ($question['type'] == '' || $question['type'] == null) ? 'radio' : $question['type'],
                            'published'         => ($question['published'] == '' || $question['published'] == null) ? 1 : intval($question['published']),
                            'wrong_answer_text' => htmlspecialchars_decode($question['wrong_answer_text'], ENT_HTML5),
                            'right_answer_text' => htmlspecialchars_decode($question['right_answer_text'], ENT_HTML5),
                            'explanation'       => htmlspecialchars_decode($question['explanation'], ENT_HTML5),
                            'user_explanation'  => ($question['user_explanation'] == '' || $question['user_explanation'] == null) ? 'off' : $question['user_explanation'],
                            'create_date'       => current_time( 'mysql' ),
                            'options'           => $options,
                            'answers'           => $question['answers'],
                        );
					}
				}
				break;
			case 'csv':
				$row = 1;
				while ( ($data = fgetcsv($questions_lines, 5000, ',')) !== false ) {
					if ($row == 1) {
						$row++;
						continue;
					}
					$answers = preg_split("/;;/", $data[10]);
					array_pop($answers);
					foreach ( $answers as $key => $answer ) {
						$ans = preg_split("/::/", $answer);
                        $answer = array();
                        $answer['answer'] = $ans[0];
                        $answer['correct'] = $ans[1];
						$answers[$key] = $answer;
					}
                    $for_import[] = array(
                        'category_id'       => in_array(strval($data[0]), $categories) ? $data[0] : 1,
                        'question'          => htmlspecialchars_decode($data[1], ENT_HTML5),
                        'question_image'    => $data[2],
                        'question_hint'     => htmlspecialchars_decode($data[3], ENT_HTML5),
                        'type'              => ($data[4] == '' || $data[4] == null) ? 'radio' : $data[4],
                        'published'         => ($data[5] == '' || $data[5] == null) ? 1 : intval($data[5]),
                        'wrong_answer_text' => htmlspecialchars_decode($data[6], ENT_HTML5),
                        'right_answer_text' => htmlspecialchars_decode($data[7], ENT_HTML5),
                        'explanation'       => htmlspecialchars_decode($data[8], ENT_HTML5),
                        'user_explanation'  => ($data[9] == '' || $data[9] == null) ? 'off' : $data[9],
                        'create_date'       => current_time( 'mysql' ),
                        'options'           => $options,
                        'answers'           => $answers,
                    );
				}
				break;
			case 'json':
				$json      = file_get_contents($import_file['tmp_name']);
				$questions = json_decode($json, true);
				foreach ( $questions as &$question ) {
                    if(isset($question['options']['bg_image']) && $question['options']['bg_image'] != ''){
                        $options['bg_image'] = $question['options']['bg_image'];
                    }
                    if(isset($question['answers'])){
                        $question['answers'] = $question['answers'];
                    }else{
                        $question['answers'] = array();
                    }
                    $for_import[] = array(
                        'category_id'       => in_array($question['category_id'], $categories) ? intval($question['category_id']) : 1,
                        'question'          => htmlspecialchars_decode($question['question'], ENT_HTML5),
                        'question_image'    => $question['question_image'],
                        'question_hint'     => htmlspecialchars_decode($question['question_hint'], ENT_HTML5),
                        'type'              => ($question['type'] == '' || $question['type'] == null) ? 'radio' : $question['type'],
                        'published'         => ($question['published'] == '' || $question['published'] == null) ? 1 : intval($question['published']),
                        'wrong_answer_text' => htmlspecialchars_decode($question['wrong_answer_text'], ENT_HTML5),
                        'right_answer_text' => htmlspecialchars_decode($question['right_answer_text'], ENT_HTML5),
                        'explanation'       => htmlspecialchars_decode($question['explanation'], ENT_HTML5),
                        'user_explanation'  => ($question['user_explanation'] == '' || $question['user_explanation'] == null) ? 'off' : $question['user_explanation'],
                        'create_date'       => current_time( 'mysql' ),
                        'options'           => $options,
                        'answers'           => $question['answers'],
                    );
				}
				break;
			default:
				return false;
				break;
		}
        
        foreach($for_import as $key => $question){
            $wpdb->insert(
                $questions_table,						
                array(
                    'category_id'       => $question['category_id'],
                    'question'          => $question['question'],
                    'question_image'    => $question['question_image'],
                    'question_hint'     => $question['question_hint'],
                    'type'              => $question['type'],
                    'published'         => $question['published'],
                    'wrong_answer_text' => $question['wrong_answer_text'],
                    'right_answer_text' => $question['right_answer_text'],
                    'explanation'       => $question['explanation'],
                    'user_explanation'  => $question['user_explanation'],
                    'create_date'       => current_time( 'mysql' ),
                    'options'           => json_encode($options),
                ),
                array(
                    '%d', //category_id
                    '%s', //question
                    '%s', //question_image
                    '%s', //hint
                    '%s', //type
                    '%d', //published
                    '%s', //wrong answer text
                    '%s', //right answer text
                    '%s', //explanation
                    '%s', //user_explanation
                    '%s', //create_date
                    '%s', //options
                )
            );
            $question_id = $wpdb->insert_id;            
            $ordering = 1;
            foreach ( $question['answers'] as &$answer ) {
                
                $wpdb->insert(
                    $answers_table,
                    array(
                        'question_id'   => $question_id,
                        'answer'        => htmlspecialchars_decode($answer['answer'], ENT_HTML5),
                        'image'         => (isset($answer['image']) && $answer['image'] != '') ? $answer['image'] : '',
                        'correct'       => intval($answer['correct']),
                        'ordering'      => $ordering,
                    ),
                    array(
                        '%d', // question_id
                        '%s', // answer
                        '%s', // image
                        '%d', // correct
                        '%d'  // ordering
                    )
                );
            }
        }
	}
    
    public function duplicate_question($id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $answers_table = $wpdb->prefix . "aysquiz_answers";

        $questionDup = $this->get_question($id);
        $asnwers = $this->get_question_answers($id);

        $options = isset($questionDup['options']) ? json_decode($questionDup['options'], true) : array();
        $question_result = $wpdb->insert(
            $questions_table,
            array(
                'category_id' => $questionDup['category_id'],
                'question' => "Copy - ".$questionDup['question'],
                'question_image' => $questionDup['question_image'],
                'type' => $questionDup['type'],
                'published' => $questionDup['published'],
                'explanation'  => $questionDup['explanation'],
                'wrong_answer_text'=>$questionDup['wrong_answer_text'],
                'right_answer_text'=>$questionDup['right_answer_text'],
                'question_hint' => $questionDup['question_hint'],
                'not_influence_to_score' => $questionDup['not_influence_to_score'],
                'user_explanation' => $questionDup['user_explanation'],
                'weight' => floatval($questionDup['weight']),
                'create_date' => current_time( 'mysql' ),
                'options' => json_encode($options),
            ),
            array(
                '%d', // category_id
                '%s', // question
                '%s', // question_image
                '%s', // type
                '%d', // published
                '%s', // explanation
                '%s', // wrong_answer_text
                '%s', // right_answer_text
                '%s', // question_hint
                '%s', // not_influence_to_score
                '%s', // user_explanation
                '%f', // weight
                '%s', // create_date
                '%s', // options
            )
        );
        $question_id = $wpdb->insert_id;
        
        $answers_results = array();
        $flag = true;
        foreach ($asnwers as $key => $answer){
            $answers_results[] = $wpdb->insert(
                $answers_table,
                array(
                    'question_id'   => $question_id,
                    'answer'        => $answer['answer'],
                    'image'         => $answer['image'],
                    'correct'       => intval( $answer['correct'] ),
                    'weight'        => floatval( $answer['weight'] ),
                    'ordering'      => ($key + 1),
                    'placeholder'   => $answer['placeholder']
                ),
                array(
                    '%d',
                    '%s',
                    '%s',
                    '%d',
                    '%f',
                    '%d',
                    '%s'
                )
            );
        }

        foreach ($answers_results as $answers_result) {
            if ($answers_result >= 0) {
                $flag = true;
            } else {
                $flag = false;
                break;
            }
        }
        $message = 'duplicated';
        if( $question_result >= 0 && $flag == true ) {
            $url = esc_url_raw( remove_query_arg( array('action', 'question') ) ) . '&status=' . $message;
            wp_redirect( $url );
        }
    }
    
    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
            global $wpdb;
            $filter = array();
            $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_questions";
            if( isset( $_GET['filterby'] ) && intval($_GET['filterby']) > 0){
                $cat_id = intval($_GET['filterby']);
                $filter[] = ' category_id = '.$cat_id.' ';
            }
            if( isset( $_REQUEST['fstatus'] ) ){
                $fstatus = $_REQUEST['fstatus'];
                if($fstatus !== null){
                    $filter[] = " published = ".$fstatus." ";
                }
            }        

            if( isset($_REQUEST['type']) ){
                $filter[] = " type ='".$_REQUEST['type']."' ";
            }

            if(count($filter) !== 0){
                $sql .= " WHERE ".implode(" AND ", $filter);
            }

        return $wpdb->get_var( $sql );
    }
    
    public static function all_record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_questions WHERE 1=1";

        if( isset( $_GET['filterby'] ) && intval($_GET['filterby']) > 0){
            $cat_id = intval($_GET['filterby']);
            $sql .= ' AND category_id = '.$cat_id.' ';
        }
        if( isset($_REQUEST['type']) ){
            $sql .= " AND type ='".$_REQUEST['type']."' ";
        }
        
        return $wpdb->get_var( $sql );
    }

    public static function published_questions_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_questions WHERE published=1";

        if( isset( $_GET['filterby'] ) && intval($_GET['filterby']) > 0){
            $cat_id = intval($_GET['filterby']);
            $sql .= ' AND category_id = '.$cat_id.' ';
        }
        if( isset($_REQUEST['type']) ){
            $sql .= " AND type ='".$_REQUEST['type']."' ";
        }
        
        return $wpdb->get_var( $sql );
    }
    
    public static function unpublished_questions_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_questions WHERE published=0";

        if( isset( $_GET['filterby'] ) && intval($_GET['filterby']) > 0){
            $cat_id = intval($_GET['filterby']);
            $sql .= ' AND category_id = '.$cat_id.' ';
        }
        if( isset($_REQUEST['type']) ){
            $sql .= " AND type ='".$_REQUEST['type']."' ";
        }
        
        return $wpdb->get_var( $sql );
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
        echo __( 'There are no questions yet.', $this->plugin_name );
    }

    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'question':
            case 'category_id':
            case 'type':
            case 'items_count':
            case 'create_date':
            case 'id':
            case 'used':
                return $item[ $column_name ];
                break;
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }


    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_question( $item ) {
        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-question' );

        $question_title = '';
        if(isset($item['question']) && strlen($item['question']) != 0){
            $question_title = strip_tags(stripslashes($item['question']));
        }elseif ((isset($item['question_image']) && $item['question_image'] !='')){
            $question_title = 'Image question';
        }
        $question_title = Quiz_Maker_Admin::ays_restriction_string("word",$question_title, 5);
        
        $title = sprintf( '<a href="?page=%s&action=%s&question=%d">%s</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ), $question_title );

        $actions = array(
            'edit' => sprintf( '<a href="?page=%s&action=%s&question=%d">'. __('Edit', $this->plugin_name) .'</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ) ),
            'duplicate' => sprintf( '<a href="?page=%s&action=%s&question=%d">'. __('Duplicate', $this->plugin_name) .'</a>', esc_attr( $_REQUEST['page'] ), 'duplicate', absint( $item['id'] ) ),
            'delete' => sprintf( '<a class="ays_confirm_del" data-message="%s" href="?page=%s&action=%s&question=%s&_wpnonce=%s">'. __('Delete', $this->plugin_name) .'</a>', $question_title, esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
        );

        return $title . $this->row_actions( $actions );
    }

    function column_category_id( $item ) {
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_categories WHERE id=" . absint( intval( $item["category_id"] ) );

        $category = $wpdb->get_row( $sql );
        
        if($category === null){
            return "";
        }
        
        return $category->title;
    }

    function column_published( $item ) {
        switch( $item['published'] ) {
            case "1":
                return '<span class="ays-publish-status"><i class="ays_fa ays_fa_check_square_o" aria-hidden="true"></i>'. __('Published',$this->plugin_name) . '</span>';
                break;
            case "0":
                return '<span class="ays-publish-status"><i class="ays_fa ays_fa_square_o" aria-hidden="true"></i>'. __('Unublished',$this->plugin_name) . '</span>';
                break;
        }
    }

    function column_create_date( $item ) {
        $options = isset($item['options']) && $item['options'] != ''?json_decode($item['options'], true):array();
        $date = isset($item['create_date']) && $item['create_date'] != '' ? $item['create_date'] : "0000-00-00 00:00:00";
        if(isset($options['author'])){
            if(is_array($options['author'])){
                $author = $options['author'];
            }else{
                $author = json_decode($options['author'], true);
            }
        }else{
            $author = array("name"=>"Unknown");
        }
        $text = "";
        if(Quiz_Maker_Admin::validateDate($date)){
            $text .= "<p><b>Date:</b> ".$date."</p>";
        }
        if($author['name'] !== "Unknown"){
            $text .= "<p><b>Author:</b> ".$author['name']."</p>";
        }
        return $text;
    }

    function column_type( $item ) {        
        $query_str = Quiz_Maker_Admin::ays_query_string(array("status", "type"));
        $type = "<a href='?".$query_str."&type=".$item['type']."' >".ucfirst( $item['type'] )."</a>";
        return $type;
    }
    
    function column_used( $item ) {
        $used = "False";
        if( in_array($item["id"], $this->used_questions) ){
            $used = "True";
        }
        return $used;
    }

    function column_items_count( $item ) {
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_answers WHERE question_id = " . $item['id'];
        $result = $wpdb->get_var($sql);
        return "<p style='text-align:center;font-size:14px;'>" . $result . "</p>";
    }



    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'                => '<input type="checkbox" />',
            'question'          => __( 'Question', $this->plugin_name ),
            'category_id'       => __( 'Category', $this->plugin_name ),
            'type'              => __( 'Type', $this->plugin_name ),
            'items_count'       => __( 'Answers count', $this->plugin_name ),
            'create_date'       => __( 'Created', $this->plugin_name ),
            'used'              => __( 'Used', $this->plugin_name ), //aray
            'id'                => __( 'ID', $this->plugin_name ),
        );

        return $columns;
    }


    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'question'      => array( 'question', true ),
            'category_id'   => array( 'category_id', true ),
            'type'          => array( 'type', true ),
            'id'            => array( 'id', true ),
        );

        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = array(
            'bulk-delete' => __('Delete', $this->plugin_name)
        );

        return $actions;
    }
    
    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'questions_per_page', 20 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );

        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;

        $do_search = ( $search ) ? sprintf(" WHERE question LIKE '%%%s%%' ", $search ) : '';

        $this->items = self::get_questions( $per_page, $current_page, $do_search );
    }

    public function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, $this->plugin_name . '-delete-question' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::delete_questions( absint( $_GET['question'] ) );

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $url = esc_url_raw( remove_query_arg( array('action', 'question', '_wpnonce') ) ) . '&status=deleted';
                wp_redirect( $url );
            }

        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = esc_sql( $_POST['bulk-delete'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::delete_questions( $id );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $url = esc_url_raw( remove_query_arg( array('action', 'question', '_wpnonce') ) ) . '&status=aredeleted';
            wp_redirect( $url );
        }
    }

    public function question_notices(){
        $status = (isset($_REQUEST['status'])) ? sanitize_text_field( $_REQUEST['status'] ) : '';

        if ( empty( $status ) )
            return;

        if ( 'created' == $status )
            $updated_message = esc_html( __( 'Question created.', $this->plugin_name ) );
        elseif ( 'updated' == $status )
            $updated_message = esc_html( __( 'Question saved.', $this->plugin_name ) );
        elseif ( 'duplicated' == $status )
            $updated_message = esc_html( __( 'Question duplicated.', $this->plugin_name ) );
        elseif ( 'deleted' == $status )
            $updated_message = esc_html( __( 'Question deleted.', $this->plugin_name ) );
        elseif ( 'aredeleted' == $status )
            $updated_message = esc_html( __( 'Questions are deleted successfully.', $this->plugin_name ) );

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }
}
