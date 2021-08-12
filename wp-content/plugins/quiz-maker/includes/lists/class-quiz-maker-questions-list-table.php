<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

class Questions_List_Table extends WP_List_Table{
    private $plugin_name;
    private $title_length;
    protected $used_questions;
    protected $current_user_can_edit;
    /** Class constructor */
    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        parent::__construct( array(
            'singular' => __( 'Question', $this->plugin_name ), //singular name of the listed records
            'plural'   => __( 'Questions', $this->plugin_name ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ) );
        $this->used_questions = Quiz_Maker_Admin::get_published_questions_used();
        $this->title_length = Quiz_Maker_Data::get_listtables_title_length('questions');
        $this->current_user_can_edit = Quiz_Maker_Data::quiz_maker_capabilities_for_editing();

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
            <select name="filterby-<?php echo $which; ?>" id="bulk-action-category-selector-<?php echo $which; ?>">
                <option value=""><?php echo __('Select Category',$this->plugin_name)?></option>
                <?php
                    foreach($categories_select as $key => $cat_title){
                        echo "<option ".$cat_title['selected']." value='".$cat_title['id']."'>".$cat_title['title']."</option>";
                    }
                ?>
            </select>
            <input type="button" id="doaction-<?php echo $which; ?>" class="cat-filter-apply-<?php echo $which; ?> button" value="Filter">
        </div>
        <a style="margin: 0px 8px 0 0;display:inline-block;" href="?page=<?php echo $_REQUEST['page'] ?>" class="button"><?php echo __( "Clear filters", $this->plugin_name ); ?></a>
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
            "all" => "<a ".$selected_all." href='?".esc_attr( $query_str )."'>". __( 'All', $this->plugin_name )." (".$all_count.")</a>",
            "published" => "<a ".$selected_1." href='?".esc_attr( $query_str )."&fstatus=1'>". __( 'Published', $this->plugin_name )." (".$published_count.")</a>",
            "unpublished"   => "<a ".$selected_0." href='?".esc_attr( $query_str )."&fstatus=0'>". __( 'Unpublished', $this->plugin_name )." (".$unpublished_count.")</a>"
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
            $where[] = $search;
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

    public static function ays_quiz_published_unpublished_questions( $id, $status = 'published' ) {
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";

        switch ( $status ) {
            case 'published':
                $published = 1;
                break;
            case 'unpublished':
                $published = 0;
                break;
            default:
                $published = 1;
                break;
        }

        $question_result = $wpdb->update(
            $questions_table,
            array(
                'published' => $published,

            ),
            array( 'id' => $id ),
            array(
                '%d'
            ),
            array( '%d' )
        );
    }

    public function get_question_categories() {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_categories ORDER BY title ASC";

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

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_answers WHERE question_id=" . absint( intval( $question_id ) ) . " ORDER BY ordering";

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    public function add_edit_questions($data){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $ays_change_type = (isset($data['ays_change_type']) && $data['ays_change_type'] != '') ? $data['ays_change_type'] : '';
        if( isset($data["question_action"]) && wp_verify_nonce( $data["question_action"],'question_action' ) ){

            // Question ID
            $id = ( isset($data['id']) && ! is_null($data['id']) ) ? absint( intval( $data['id'] ) ) : null;

            // Question title
            $question           = (isset($data['ays_question']) && $data['ays_question'] != '') ? $data['ays_question'] : '';

            // Question title ( Banner )
            $question_title     = (isset($data['ays_question_title']) && $data['ays_question_title'] != '') ? $data['ays_question_title'] : '';

            // Question hint
            $question_hint      = (isset($data['ays_question_hint']) && $data['ays_question_hint'] != '') ? wpautop($data['ays_question_hint']) : '';

            // Question Image
            $question_image     = (isset($data['ays_question_image']) && $data['ays_question_image'] != '') ? $data['ays_question_image'] : NULL;

            // Question category
            $category_id        = (isset($data['ays_question_category']) && $data['ays_question_category'] != '') ? absint( intval( $data['ays_question_category'] ) ) : 1;

            // Question status
            $published          = (isset($data['ays_publish']) && $data['ays_publish'] != '') ? absint( intval( $data['ays_publish'] ) ) : 0;

            // User answer explanation
            $user_explanation   = (isset($data['ays_user_explanation']) && $data['ays_user_explanation'] != '') ? sanitize_text_field( $data['ays_user_explanation'] ) : 'off';

            // Question type
            $type               = (isset($data['ays_question_type']) && $data['ays_question_type'] != '') ? sanitize_text_field( $data['ays_question_type'] ) : 'radio';

            // Correct answers Array
            $correct_answers    = (isset($data['ays-correct-answer']) ) && ! empty($data['ays-correct-answer']) ? $data['ays-correct-answer'] : array();

            // Answers weight Array
            $answers_weight     = (isset($data['ays-answer-weight']) ) && ! empty($data['ays-answer-weight']) ? $data['ays-answer-weight'] : array();

            // Answers Array
            $answer_values      = (isset($data['ays-correct-answer-value']) && ! empty($data['ays-correct-answer-value'])) ? $data['ays-correct-answer-value'] : array();

            // Answers placeholders Array
            $answer_placeholders = (isset($data['ays-answer-placeholder']) && ! empty($data['ays-answer-placeholder']) ) ? $data['ays-answer-placeholder'] : array();

            // Answers image Array
            $answer_image       = (isset($data['ays_answer_image']) && ! empty($data['ays_answer_image']) ) ? $data['ays_answer_image'] : array();

            // Text In case of wrong answer
            $wrong_answer_text  = (isset($data['wrong_answer_text']) && $data['wrong_answer_text'] != '') ? $data['wrong_answer_text'] : '';

            // Text In case of right answer
            $right_answer_text  = (isset($data['right_answer_text']) && $data['right_answer_text'] != '') ? $data['right_answer_text'] : '';

            // Question explanation
            $explanation        = (isset($data['explanation']) && $data['explanation'] != '') ? $data['explanation'] : '';

            // Not influence to score
            $not_influence_to_score = (isset($data['ays_not_influence_to_score']) && $data['ays_not_influence_to_score'] == 'on') ? 'on' : 'off';

             // Question weight
            $question_weight = (isset($data['ays_question_weight']) && $data['ays_question_weight'] != '') ? floatval($data['ays_question_weight']) : floatval(1);

            // Create date
            $quest_create_date  = !isset($data['ays_question_ctrate_date']) ? '0000-00-00 00:00:00' : $data['ays_question_ctrate_date'];

            // Author
            $author_id = isset($data['ays_question_author']) ? intval( $data['ays_question_author'] ) : 0;

            // Question background image
            $bg_image = (isset($data['ays_question_bg_image']) && $data['ays_question_bg_image'] != '') ? $data['ays_question_bg_image'] : '';
                
            // Use HTML for answers
            $use_html = (isset($data['ays-use-html']) && $data['ays-use-html'] == 'on') ? 'on' : 'off';

            // Answer Keywords Array
            $answer_keywords = (isset($data['ays_quiz_keywords']) && !empty($data['ays_quiz_keywords'])) ? $data['ays_quiz_keywords'] : array();

            // Maximum length of a text field
            $enable_question_text_max_length = (isset($data['ays_enable_question_text_max_length']) && sanitize_text_field( $data['ays_enable_question_text_max_length'] ) == 'on') ? 'on' : 'off';

            // Length
            $question_text_max_length = ( isset($data['ays_question_text_max_length']) && sanitize_text_field( $data['ays_question_text_max_length'] ) != '' ) ? absint( intval( $data['ays_question_text_max_length'] ) ) : '';

            // Limit by
            $question_limit_text_type = ( isset($data['ays_question_limit_text_type']) && $data['ays_question_limit_text_type'] != '' ) ? sanitize_text_field( $data['ays_question_limit_text_type'] ) : 'characters';

            // Show the counter-message
            $question_enable_text_message = ( isset($_POST['ays_question_enable_text_message']) && sanitize_text_field( $_POST['ays_question_enable_text_message'] ) == 'on' ) ? 'on' : 'off';

            // Maximum length of a text field
            $enable_question_number_max_length = (isset($_POST['ays_enable_question_number_max_length']) && sanitize_text_field( $_POST['ays_enable_question_number_max_length'] ) == 'on') ? 'on' : 'off';

            // Length
            $question_number_max_length = ( isset($_POST['ays_question_number_max_length']) && sanitize_text_field( $_POST['ays_question_number_max_length'] ) != '' ) ? intval( sanitize_text_field ( $_POST['ays_question_number_max_length'] ) ) : '';

            // Hide question text on the front-end
            $quiz_hide_question_text = ( isset($_POST['ays_quiz_hide_question_text']) && sanitize_text_field( $_POST['ays_quiz_hide_question_text'] ) == 'on' ) ? 'on' : 'off';

            $options = array(
				'bg_image' => $bg_image,
                'use_html' => $use_html,
                'enable_question_text_max_length' => $enable_question_text_max_length,
                'question_text_max_length' => $question_text_max_length,
                'question_limit_text_type' => $question_limit_text_type,
                'question_enable_text_message' => $question_enable_text_message,
                'enable_question_number_max_length' => $enable_question_number_max_length,
                'question_number_max_length' => $question_number_max_length,
                'quiz_hide_question_text' => $quiz_hide_question_text,
            );
            
            if($id == 0) {
                $question_result = $wpdb->insert(
                    $questions_table,
                    array(
                        'category_id'       => $category_id,
                        'author_id'         => $author_id,
                        'question'          => $question,
                        'question_title'    => $question_title,
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
                        '%d', // author_id
                        '%s', // question
                        '%s', // question_title
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

                    $keyword = 'A';
                    if(isset($answer_keywords[$index])){
                        $keyword = $answer_keywords[$index];
                    }

                    $placeholder = '';
                    if(isset($answer_placeholders[$index])){
                        $placeholder = $answer_placeholders[$index];
                    }

                    $answers_results[] = $wpdb->insert(
                        $answers_table,
                        array(
                            'question_id'   => $question_id,
                            'answer'        => ($answer_value),
                            'image'         => isset( $answer_image[$index] ) ? $answer_image[$index] : '',
                            'correct'       => $correct,
                            'ordering'      => ($index + 1),
                            'weight'        => $weight,
                            'keyword'       => $keyword,
                            'placeholder'   => $placeholder
                        ),
                        array(
                            '%d', // question_id
                            '%s', // answer
                            '%s', // image
                            '%d', // correct
                            '%d', // ordering
                            '%f', // weight
                            '%s', // keyword
                            '%s', // placeholder
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
                        'author_id'         => $author_id,
                        'question'          => $question,
                        'question_title'    => $question_title,
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
                        '%d', // author_id
                        '%s', // question
                        '%s', // question_title
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

                        $keyword = 'A';
                        if(isset($answer_keywords[$index])){
                            $keyword = $answer_keywords[$index];
                        }

                        $placeholder = '';
                        if(isset($answer_placeholders[$index])){
                            $placeholder = $answer_placeholders[$index];
                        }

                        $answers_results[] = $wpdb->update(
                            $answers_table,
                            array(
                                'question_id'   => $id,
                                'answer'        => ($answer_value),
                                'image'         => isset( $answer_image[$index] ) ? $answer_image[$index] : '',
                                'correct'       => $correct,
                                'ordering'      => ($index + 1),
                                'weight'        => $weight,
                                'keyword'       => $keyword,
                                'placeholder'   => $placeholder
                            ),
                            array('id' => $old_answers[$index]["id"]),
                            array(
                                '%d', // question_id
                                '%s', // answer
                                '%s', // image
                                '%d', // correct
                                '%d', // ordering
                                '%f', // weight
                                '%s', // keyword
                                '%s', // placeholder
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

                        $keyword = 'A';
                        if(isset($answer_keywords[$index])){
                            $keyword = $answer_keywords[$index];
                        }

                        $placeholder = '';
                        if(isset($answer_placeholders[$index])){
                            $placeholder = $answer_placeholders[$index];
                        }

                        if( $old_answers_count < ( $index + 1) ){
                            $answers_results[] = $wpdb->insert(
                                $answers_table,
                                array(
                                    'question_id'   => $id,
                                    'answer'        => ($answer_value),
                                    'image'         => isset( $answer_image[$index] ) ? $answer_image[$index] : '',
                                    'correct'       => $correct,
                                    'ordering'      => ($index + 1),
                                    'weight'        => $weight,
                                    'keyword'       => $keyword,
                                    'placeholder'   => $placeholder
                                ),
                                array(
                                    '%d', // question_id
                                    '%s', // answer
                                    '%s', // image
                                    '%d', // correct
                                    '%d', // ordering
                                    '%f', // weight
                                    '%s', // keyword
                                    '%s', // placeholder
                                )
                            );
                        }else{
                            $weight = $answers_weight[$index];
                            $answers_results[] = $wpdb->update(
                                $answers_table,
                                array(
                                    'question_id'   => $id,
                                    'answer'        => ($answer_value),
                                    'image'         => isset( $answer_image[$index] ) ? $answer_image[$index] : '',
                                    'correct'       => $correct,
                                    'ordering'      => ($index + 1),
                                    'weight'        => $weight,
                                    'keyword'       => $keyword,
                                    'placeholder'   => $placeholder
                                ),
                                array('id' => $old_answers[$index]["id"]),
                                array(
                                    '%d', // question_id
                                    '%s', // answer
                                    '%s', // image
                                    '%d', // correct
                                    '%d', // ordering
                                    '%f', // weight
                                    '%s', // keyword
                                    '%s', // placeholder
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

                        $keyword = 'A';
                        if(isset($answer_keywords[$index])){
                            $keyword = $answer_keywords[$index];
                        }

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
                                'keyword'       => $keyword,
                                'placeholder'   => $placeholder
                            ),
                            array('id' => $old_answers[$index]["id"]),
                            array(
                                '%d', // question_id
                                '%s', // answer
                                '%d', // correct
                                '%d', // ordering
                                '%f', // weight
                                '%s', // keyword
                                '%s', // placeholder
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

        $question_category_table = $wpdb->prefix . "aysquiz_categories";
        $categories_r = $wpdb->get_results("SELECT id, title FROM ".$question_category_table, 'ARRAY_A');
        $for_import = array();
        $categories = array();
        foreach($categories_r as $cat){
            $categories[$cat['id']] = strtolower($cat['title']);
        }

		switch ( $type ) {
			case 'xlsx':
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
                            $answers = "[" . trim($row['answers'], '[]') . "]";
                            $answers = json_decode( $answers, true );
				            $row['answers'] = $answers;
                        }else{
                            $row['answers'] = array();
                        }

                        if(isset($row['options']) && $row['options'] != ''){
                            $row_options = json_decode( $row['options'], true );
                            $row['options'] = $row_options;
                        }else{
                            $row['options'] = array();
                        }
					}
					$questions = array_values($sheet_data);
					foreach ( $questions as $key => &$question ) {

                        $question_category = 1;
                        $question_category_file = 'Uncategorized';
                        if(isset($question['category'])){
                            $question_category_file = strval($question['category']);
                        }

                        if(Quiz_Maker_Admin::string_starts_with_number($question_category_file)){
                            $question_category = 1;
                        }elseif(in_array(strtolower($question_category_file), $categories)){
                            $category_id = array_search(strtolower($question_category_file), $categories);
                            if($category_id !== false){
                                $question_category = intval($category_id);
                            }else{
                                $question_category = 1;
                            }
                        }else{
                            $wpdb->insert(
                                $question_category_table,
                                array(
                                    'title'  =>  $question_category_file,
                                    'published'  =>  1
                                ),
                                array( '%s', '%d' )
                            );
                            $question_category = $wpdb->insert_id;
                            $categories[$question_category] = strtolower($question_category_file);
                        }

                        $category_id = $question_category;
                        $question_content = htmlspecialchars_decode(isset($question['question']) && $question['question'] != '' ? $question['question'] : '', ENT_HTML5);
                        $question_title = htmlspecialchars_decode(isset($question['question_title']) && $question['question_title'] != '' ? $question['question_title'] : '', ENT_HTML5);
                        $question_image = (isset($question['question_image']) && $question['question_image'] != '') ? $question['question_image'] : '';
                        $question_hint = htmlspecialchars_decode(isset($question['question_hint']) && $question['question_hint'] != '' ? $question['question_hint'] : '', ENT_HTML5);
                        $type = (isset($question['type']) && $question['type'] != '') ? $question['type'] : 'radio';
                        $published = (isset($question['published']) && absint(intval($question['published'])) == 0) ? intval($question['published']) : 1;
                        $wrong_answer_text = htmlspecialchars_decode(isset($question['wrong_answer_text']) && $question['wrong_answer_text'] != '' ? $question['wrong_answer_text'] : '', ENT_HTML5);
                        $right_answer_text = htmlspecialchars_decode(isset($question['right_answer_text']) && $question['right_answer_text'] != '' ? $question['right_answer_text'] : '', ENT_HTML5);
                        $explanation = htmlspecialchars_decode(isset($question['explanation']) && $question['explanation'] != '' ? $question['explanation'] : '', ENT_HTML5);
                        $user_explanation = (isset($question['user_explanation']) && $question['user_explanation'] != '') ? $question['user_explanation'] : 'off';
                        $not_influence_to_score = (isset($question['not_influence_to_score']) && $question['not_influence_to_score'] != '') ? $question['not_influence_to_score'] : 'off';
                        $question_weight = (isset($question['weight']) && $question['weight'] != '') ? floatval($question['weight']) : 1;
                        $create_date = current_time( 'mysql' );
                        $answers_get = $question['answers'];

                        $bg_image = (isset($question['options']['bg_image']) && $question['options']['bg_image'] != '') ? $question['options']['bg_image'] : '';
                        $use_html = (isset($question['options']['use_html']) && $question['options']['use_html'] != '') ? $question['options']['use_html'] : 'off';
                        $enable_question_text_max_length = (isset($question['options']['enable_question_text_max_length']) && $question['options']['enable_question_text_max_length'] != '') ? $question['options']['enable_question_text_max_length'] : 'off';
                        $question_text_max_length = (isset($question['options']['question_text_max_length']) && $question['options']['question_text_max_length'] != '') ? $question['options']['question_text_max_length'] : '';
                        $question_limit_text_type = (isset($question['options']['question_limit_text_type']) && $question['options']['question_limit_text_type'] != '') ? $question['options']['question_limit_text_type'] : '';
                        $question_enable_text_message = (isset($question['options']['question_enable_text_message']) && $question['options']['question_enable_text_message'] != '') ? $question['options']['question_enable_text_message'] : 'off';

                        $options = array(
                            'author' => $author,
                            'bg_image' => $bg_image,
                            'use_html' => $use_html,
                            'enable_question_text_max_length' => $enable_question_text_max_length,
                            'question_text_max_length' => $question_text_max_length,
                            'question_limit_text_type' => $question_limit_text_type,
                            'question_enable_text_message' => $question_enable_text_message,
                        );

                        $attach_url = $question_image;

                        // Add image
                        if($question_image != ""){
                            $attach_url = Quiz_Maker_Data::ays_get_images_from_url( $question_image );
                        }

                        $answers = array();
                        foreach($answers_get as $key => $answer){

                            $answer_content = (isset($answer['answer']) && $answer['answer'] != '') ? htmlspecialchars_decode($answer['answer'], ENT_HTML5) : '';
                            $image = (isset($answer['image']) && $answer['image'] != '') ? $answer['image'] : '';
                            $correct = (isset($answer['correct']) && $answer['correct'] != '') ? intval($answer['correct']) : 0;
                            $ordering = $key + 1;
                            $weight = (isset($answer['weight']) && $answer['weight'] != '') ? floatval($answer['weight']) : 0;
                            $placeholder = (isset($answer['placeholder']) && $answer['placeholder'] != '') ? htmlspecialchars_decode($answer['placeholder'], ENT_HTML5) : '';

                            $answers[] = array(
                                'answer'        => $answer_content,
                                'image'         => $image,
                                'correct'       => $correct,
                                'ordering'      => $ordering,
                                'weight'        => $weight,
                                'placeholder'   => $placeholder,
                            );
                        }
                        
                        $for_import[] = array(
                            'category_id'               => $category_id,
                            'question'                  => $question_content,
                            'question_title'            => $question_title,
                            'question_image'            => $attach_url,
                            'question_hint'             => $question_hint,
                            'type'                      => $type,
                            'published'                 => $published,
                            'wrong_answer_text'         => $wrong_answer_text,
                            'right_answer_text'         => $right_answer_text,
                            'explanation'               => $explanation,
                            'user_explanation'          => $user_explanation,
                            'not_influence_to_score'    => $not_influence_to_score,
                            'weight'                    => $question_weight,
                            'create_date'               => $create_date,
                            'options'                   => $options,
                            'answers'                   => $answers,
                        );
					}
				}
				break;
			case 'csv':
				$row = 1;
				while ( ($data = fgetcsv($questions_lines, 10000, ',')) !== false ) {
					if ($row == 1) {
						$row++;
						continue;
					}

//                    $data = array_map("utf8_encode", $data);

                    $question_category = 1;
                    $question_category_file = 'Uncategorized';
                    if(isset($data[0])){
                        $question_category_file = strval($data[0]);
                    }

                    if(Quiz_Maker_Admin::string_starts_with_number($question_category_file)){
                        $question_category = 1;
                    }elseif(in_array(strtolower($question_category_file), $categories)){
                        $category_id = array_search(strtolower($question_category_file), $categories);
                        if($category_id !== false){
                            $question_category = intval($category_id);
                        }else{
                            $question_category = 1;
                        }
                    }else{
                        $wpdb->insert(
                            $question_category_table,
                            array(
                                'title' => $question_category_file,
                                'published' => 1
                            ),
                            array( '%s', '%d' )
                        );
                        $question_category = $wpdb->insert_id;
                        $categories[$question_category] = strtolower($question_category_file);
                    }

                    $category_id = $question_category;
                    $question = htmlspecialchars_decode($data[1], ENT_HTML5);
                    $question_title = htmlspecialchars_decode((isset($data[14]) && $data[14] != '') ? $data[14] : '', ENT_HTML5);
                    $question_image = (isset($data[2]) && $data[2] != '') ? $data[2] : '';
                    $question_hint = htmlspecialchars_decode($data[3], ENT_HTML5);
                    $type = (isset($data[4]) && $data[4] != '') ? $data[4] : 'radio';
                    $published = (isset($data[5]) && $data[5] != '') ? intval($data[5]) : 1;
                    $wrong_answer_text = htmlspecialchars_decode($data[6], ENT_HTML5);
                    $right_answer_text = htmlspecialchars_decode($data[7], ENT_HTML5);
                    $explanation = htmlspecialchars_decode($data[8], ENT_HTML5);
                    $user_explanation = (isset($data[9]) && $data[9] != '') ? $data[9] : 'off';

                    $not_influence_to_score = 'off';
                    $question_weight = 1;
                    $options = array(
                        'author' => $author,
                    );
                    if(isset($data[12])){
                        $not_influence_to_score = (isset($data[10]) && $data[10] != '') ? $data[10] : 'off';
                        $question_weight = (isset($data[11]) && $data[11] != '') ? floatval($data[11]) : 1;
                        $question_options = (isset($data[13]) && $data[13] != '') ? $data[13] : '';
                        $question_options = preg_split("/::/", $question_options);
                        foreach($question_options as $opt){
                            $option = explode( '=', $opt );
                            if(! empty($option)){
                                $options[$option[0]] = $option[1];
                            }
                        }
                    }

                    $attach_url = $question_image;

                    // Add image
                    if($question_image != ""){
                        $attach_url = Quiz_Maker_Data::ays_get_images_from_url( $question_image );
                    }

                    $create_date = current_time( 'mysql' );
                    $answer_csv = (isset($data[12]) && $data[12] != '') ? $data[12] : ((isset($data[10]) && $data[10] !='') ? $data[10] : '');

					$answers_get = preg_split("/;;/", $answer_csv);
					array_pop($answers_get);
                    $answers = array();

					foreach ( $answers_get as $key => $answer ) {
						$ans = preg_split("/::/", $answer);

                        $answer_content = (isset($ans[0]) && $ans[0] != '') ? htmlspecialchars_decode($ans[0], ENT_HTML5) : '';
                        $answer_content = htmlspecialchars_decode($answer_content, ENT_QUOTES);
                        $image = (isset($ans[3]) && $ans[3] != '') ? $ans[3] : '';
                        $correct = (isset($ans[1]) && $ans[1] != '') ? intval($ans[1]) : 0;
                        $ordering = $key + 1;
                        $weight = (isset($ans[2]) && $ans[2] != '') ? floatval($ans[2]) : 0;
                        $placeholder = (isset($ans[4]) && $ans[4] != '') ? htmlspecialchars_decode($ans[4], ENT_HTML5) : '';
                        $placeholder = htmlspecialchars_decode($placeholder, ENT_QUOTES);

                        $answers[] = array(
                            'answer'        => $answer_content,
                            'image'         => $image,
                            'correct'       => $correct,
                            'ordering'      => $ordering,
                            'weight'        => $weight,
                            'placeholder'   => $placeholder,
                        );
					}

                    $for_import[] = array(
                        'category_id'               => $category_id,
                        'question'                  => $question,
                        'question_title'            => $question_title,
                        'question_image'            => $attach_url,
                        'question_hint'             => $question_hint,
                        'type'                      => $type,
                        'published'                 => $published,
                        'wrong_answer_text'         => $wrong_answer_text,
                        'right_answer_text'         => $right_answer_text,
                        'explanation'               => $explanation,
                        'user_explanation'          => $user_explanation,
                        'not_influence_to_score'    => $not_influence_to_score,
                        'weight'                    => $question_weight,
                        'create_date'               => $create_date,
                        'options'                   => $options,
                        'answers'                   => $answers,
                    );
				}
				break;
			case 'json':
				$json      = file_get_contents($import_file['tmp_name']);
				$questions = json_decode($json, true);
				foreach ( $questions as &$question ) {
                    $question_options = array();
                    $options = array(
                        'author' => $author,
                    );
                    if(isset($question['options']) && $question['options'] != ''){
                        $question_options = json_decode($question['options'], true);
                    }
                    if(isset($question_options['bg_image']) && $question_options['bg_image'] != ''){
                        $options['bg_image'] = $question_options['bg_image'];
                    }
                    if(isset($question_options['use_html']) && $question_options['use_html'] != ''){
                        $options['use_html'] = $question_options['use_html'];
                    }

                    $enable_question_text_max_length = (isset($question_options['enable_question_text_max_length']) && $question_options['enable_question_text_max_length'] != '') ? $question_options['enable_question_text_max_length'] : 'off';
                    $question_text_max_length = (isset($question_options['question_text_max_length']) && $question_options['question_text_max_length'] != '') ? $question_options['question_text_max_length'] : '';
                    $question_limit_text_type = (isset($question_options['question_limit_text_type']) && $question_options['question_limit_text_type'] != '') ? $question_options['question_limit_text_type'] : '';
                    $question_enable_text_message = (isset($question_options['question_enable_text_message']) && $question_options['question_enable_text_message'] != '') ? $question_options['question_enable_text_message'] : 'off';

                    $options['enable_question_text_max_length'] = $enable_question_text_max_length;
                    $options['question_text_max_length'] = $question_text_max_length;
                    $options['question_limit_text_type'] = $question_limit_text_type;
                    $options['question_enable_text_message'] = $question_enable_text_message;

                    $question_category = 1;
                    $question_category_file = 'Uncategorized';
                    if(isset($question['category'])){
                        $question_category_file = strval($question['category']);
                    }

                    if(Quiz_Maker_Admin::string_starts_with_number($question_category_file)){
                        $question_category = 1;
                    }elseif(in_array(strtolower($question_category_file), $categories)){
                        $category_id = array_search(strtolower($question_category_file), $categories);
                        if($category_id !== false){
                            $question_category = intval($category_id);
                        }else{
                            $question_category = 1;
                        }
                    }else{
                        $wpdb->insert(
                            $question_category_table,
                            array(
                                'title'  =>  $question_category_file,
                                'published'  =>  1
                            ),
                            array( '%s', '%d' )
                        );
                        $question_category = $wpdb->insert_id;
                        $categories[$question_category] = strtolower($question_category_file);
                    }

                    $category_id = $question_category;
                    $question_content = htmlspecialchars_decode($question['question'], ENT_HTML5);
                    $question_title = htmlspecialchars_decode(isset($question['question_title']) && $question['question_title'] != '' ? $question['question_title'] : '', ENT_HTML5);
                    $question_image = (isset($question['question_image']) && $question['question_image'] != '') ? $question['question_image'] : '';
                    $question_hint = htmlspecialchars_decode($question['question_hint'], ENT_HTML5);
                    $type = (isset($question['type']) && $question['type'] != '') ? $question['type'] : 'radio';
                    $published = (isset($question['published']) && $question['published'] != '') ? intval($question['published']) : 1;
                    $wrong_answer_text = htmlspecialchars_decode($question['wrong_answer_text'], ENT_HTML5);
                    $right_answer_text = htmlspecialchars_decode($question['right_answer_text'], ENT_HTML5);
                    $explanation = htmlspecialchars_decode($question['explanation'], ENT_HTML5);
                    $user_explanation = (isset($question['user_explanation']) && $question['user_explanation'] != '') ? $question['user_explanation'] : 'off';
                    $not_influence_to_score = (isset($question['not_influence_to_score']) && $question['not_influence_to_score'] != '') ? $question['not_influence_to_score'] : 'off';
                    $question_weight = (isset($question['weight']) && $question['weight'] != '') ? floatval($question['weight']) : 1;
                    $create_date = current_time( 'mysql' );

                    $answers_get = $question['answers'];

                    if(isset($question['answers'])){
                        $answers_get = $question['answers'];
                    }else{
                        $answers_get = array();
                    }

                    $attach_url = $question_image;

                    // Add image
                    if($question_image != ""){
                        $attach_url = Quiz_Maker_Data::ays_get_images_from_url( $question_image );
                    }

                    $answers = array();
                    foreach($answers_get as $key => $answer){

                        $answer_content = (isset($answer['answer']) && $answer['answer'] != '') ? htmlspecialchars_decode($answer['answer'], ENT_HTML5) : '';
                        $image = (isset($answer['image']) && $answer['image'] != '') ? $answer['image'] : '';
                        $correct = (isset($answer['correct']) && $answer['correct'] != '') ? intval($answer['correct']) : 0;
                        $ordering = $key + 1;
                        $weight = (isset($answer['weight']) && $answer['weight'] != '') ? floatval($answer['weight']) : 0;
                        $placeholder = (isset($answer['placeholder']) && $answer['placeholder'] != '') ? htmlspecialchars_decode($answer['placeholder'], ENT_HTML5) : '';

                        $answers[] = array(
                            'answer'        => $answer_content,
                            'image'         => $image,
                            'correct'       => $correct,
                            'ordering'      => $ordering,
                            'weight'        => $weight,
                            'placeholder'   => $placeholder,
                        );
                    }

                    $for_import[] = array(
                        'category_id'               => $category_id,
                        'question'                  => $question_content,
                        'question_title'            => $question_title,
                        'question_image'            => $attach_url,
                        'question_hint'             => $question_hint,
                        'type'                      => $type,
                        'published'                 => $published,
                        'wrong_answer_text'         => $wrong_answer_text,
                        'right_answer_text'         => $right_answer_text,
                        'explanation'               => $explanation,
                        'user_explanation'          => $user_explanation,
                        'not_influence_to_score'    => $not_influence_to_score,
                        'weight'                    => $question_weight,
                        'create_date'               => $create_date,
                        'options'                   => $options,
                        'answers'                   => $answers,
                    );
				}
				break;
			default:
				return false;
				break;
		}
        
        $imported = 0;
        $failed = 0;

        foreach($for_import as $key => $question){
            $quest_res = $wpdb->insert(
                $questions_table,						
                array(
                    'category_id'               => $question['category_id'],
                    'question'                  => $question['question'],
                    'question_title'            => $question['question_title'],
                    'question_image'            => $question['question_image'],
                    'question_hint'             => $question['question_hint'],
                    'type'                      => $question['type'],
                    'published'                 => $question['published'],
                    'wrong_answer_text'         => $question['wrong_answer_text'],
                    'right_answer_text'         => $question['right_answer_text'],
                    'explanation'               => $question['explanation'],
                    'user_explanation'          => $question['user_explanation'],
                    'not_influence_to_score'    => $question['not_influence_to_score'],
                    'weight'                    => $question['weight'],
                    'create_date'               => $question['create_date'],
                    'author_id'                 => $author['id'],
                    'options'                   => json_encode($question['options']),
                ),
                array(
                    '%d', //category_id
                    '%s', //question
                    '%s', //question_title
                    '%s', //question_image
                    '%s', //hint
                    '%s', //type
                    '%d', //published
                    '%s', //wrong answer text
                    '%s', //right answer text
                    '%s', //explanation
                    '%s', //user_explanation
                    '%s', //not_influence_to_score
                    '%f', //weight
                    '%s', //create_date
                    '%d', //author
                    '%s', //options
                )
            );
            $question_id = $wpdb->insert_id;
            $ordering = 1;
            $answer_res_success = 0;
            $answer_res_fail = 0;
            foreach ( $question['answers'] as &$answer ) {
                $result = $wpdb->insert(
                    $answers_table,
                    array(
                        'question_id'   => $question_id,
                        'answer'        => $answer['answer'],
                        'image'         => $answer['image'],
                        'correct'       => $answer['correct'],
                        'ordering'      => $answer['ordering'],
                        'weight'        => $answer['weight'],
                        'placeholder'   => $answer['placeholder'],
                    ),
                    array(
                        '%d', // question_id
                        '%s', // answer
                        '%s', // image
                        '%d', // correct
                        '%d', // ordering
                        '%f', // weight
                        '%s', // placeholder
                    )
                );
                if($result === false){
                    $answer_res_fail++;
                }
                if($result >= 0){
                    $answer_res_success++;
                }
            }
            if($quest_res === false){
                $failed++;
            }
            if($quest_res >= 0 && $answer_res_success > 0){
                $imported++;
            }else{
                $failed++;
            }
        }
        $stats = $imported."-".$failed;
        return $stats;
	}
    
    public function ays_xlsx_questions_simple_import( $import_file_simple ){
        global $wpdb;
        $answers_table   = $wpdb->prefix . "aysquiz_answers";
        $quizes_table = $wpdb->prefix . "aysquiz_quizes";
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $answers_table   = $wpdb->prefix . "aysquiz_answers";
        $quiz_category_table   = $wpdb->prefix . "aysquiz_quizcategories";
        $question_category_table   = $wpdb->prefix . "aysquiz_categories";
        $answers = array();
        $category = null;
        $aaa = fopen($import_file_simple['tmp_name'], 'r');
        $import_file = $import_file_simple;

        $user_id = get_current_user_id();
        $user    = get_userdata($user_id);
        $author  = array(
            'id'   => $user->ID,
            'name' => $user->data->display_name
        );

        $options = array(
            'author' => $author,
        );
        $categories_r = $wpdb->get_results("SELECT id, title FROM ".$question_category_table, 'ARRAY_A');
        $categories = array();
        foreach($categories_r as $cat){
            $categories[$cat['id']] = strtolower($cat['title']);
        }
        $ver = (float) phpversion();
        $imported = 0;
        $failed = 0;
        if ($ver >= 5.6) {
            require_once(AYS_QUIZ_DIR . 'includes/PHPExcel/vendor/autoload.php');
            $spreadsheet = IOFactory::load($import_file['tmp_name']);
            $sheet_data = $spreadsheet->getActiveSheet()->toArray("", true, true, true);
            $headers = $sheet_data[1];
            unset($sheet_data[1]);
            foreach ($sheet_data as $sheet_value) {
                $answers = array();
                $break = false;
                foreach ($sheet_value as $sh_key => $sh_val) {
                    switch ($sh_key) {
                        case 'A':
                            if (empty($sh_val)) {
                                $break = true;
                                break;
                            }else {
                                $q = html_entity_decode(preg_replace('/_x([0-9a-fA-F]{4})_/', '&#x$1;', $sh_val));
                                $question = htmlspecialchars_decode($q, ENT_HTML5);
                            }
                            break;
                        case 'B':
                            $category = $sh_val;
                            break;
                        case 'C':
                            $correct_answer = $sh_val;
                            break;
                        default:
                            if(!empty($sh_val)){
                                $answers[] = htmlspecialchars_decode( preg_replace('/_x([0-9a-fA-F]{4})_/', '&#x$1;', $sh_val), ENT_HTML5);
                            }
                            break;
                    }
                }

                if ($break) {
                    continue;
                }

                // Insert Category
                $question_category = 1;
                $question_category_file = 'Uncategorized';
                if(isset($category)){
                    $question_category_file = strval($category);
                }
                if(Quiz_Maker_Admin::string_starts_with_number($question_category_file)){
                    $question_category = 1;
                }elseif(in_array(strtolower($question_category_file), $categories)){
                    $category_id = array_search(strtolower($question_category_file), $categories);
                    if($category_id !== false){
                        $question_category = intval($category_id);
                    }else{
                        $question_category = 1;
                    }
                }else{
                    $wpdb->insert(
                        $question_category_table,
                        array(
                            'title'  =>  $question_category_file,
                            'published'  =>  1
                        ),
                        array( '%s', '%d' )
                    );
                    $question_category = $wpdb->insert_id;
                    $categories[$question_category] = strtolower($question_category_file);
                }

                // Insert Question
                $quest_res = $wpdb->insert(
                    $questions_table,
                    array(
                        'category_id'      => $question_category,
                        'question'         => $question,
                        'published'        => 1,
                        'type'             => 'radio',
                        'question_image'   => '',
                        'weight'           => 1,
                        'create_date'      => current_time( 'mysql' ),
                        'author_id'        => $author['id'],
                        'options'          => json_encode($options),
                    ),
                    array(
                        '%d', // category_id
                        '%s', // questiion
                        '%d', // published
                        '%s', // type
                        '%s', // question_image
                        '%f', // weight
                        '%s', // create_date
                        '%d', // author_id
                        '%s', // options
                    )
                );
                $question_id = $wpdb->insert_id;

                // Insert Answers
                $ordering = 1;
                $correct_answer--;
                $answer_res_success = 0;
                $answer_res_fail = 0;
                for ($j=0; $j < count($answers); $j++) {
                    $question_correct_answer = 0;

                    if ($correct_answer == $j) {
                        $question_correct_answer = 1;
                    }
                    $answ_res = $wpdb->insert(
                        $answers_table,
                        array(
                            'question_id'  => $question_id,
                            'answer'       => $answers[$j],
                            'correct'      => $question_correct_answer,
                            'ordering'     => $ordering,
                            'weight'       => 0
                        ),
                        array(
                            '%d', // question_id
                            '%s', // answer
                            '%d', // correct
                            '%d', // ordering
                            '%f'  // weight
                        )
                    );
                    if($answ_res === false){
                        $answer_res_fail++;
                    }
                    if($answ_res >= 0){
                        $answer_res_success++;
                    }
                }
                if($quest_res === false){
                    $failed++;
                }
                if($quest_res >= 0 && $answer_res_success > 0){
                    $imported++;
                }else{
                    $failed++;
                }
            }
        }else{
            $failed++;
        }
        $stats = $imported."-".$failed;
        return $stats;
    }

    public function duplicate_question($id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $answers_table = $wpdb->prefix . "aysquiz_answers";

        $questionDup = $this->get_question($id);
        $asnwers = $this->get_question_answers($id);

        $question_options = (isset($questionDup['options']) && $questionDup['options'] != '') ? json_decode($questionDup['options'] ,true) : array();

        // Use HTML
        $question_options['use_html'] = isset($question_options['use_html']) ? sanitize_text_field($question_options['use_html']) : 'off';
        $use_html = (isset($question_options['use_html']) && sanitize_text_field( $question_options['use_html'] ) == 'on') ? 'on' : 'off';

        // Maximum length of a text field
        $question_options['enable_question_text_max_length'] = isset($question_options['enable_question_text_max_length']) ? sanitize_text_field($question_options['enable_question_text_max_length']) : 'off';
        $enable_question_text_max_length = (isset($question_options['enable_question_text_max_length']) && sanitize_text_field( $question_options['enable_question_text_max_length'] ) == 'on') ? 'on' : 'off';

        // Length
        $question_text_max_length = ( isset($question_options['question_text_max_length']) && sanitize_text_field( $question_options['question_text_max_length'] ) != '' ) ? absint( intval( sanitize_text_field( $question_options['question_text_max_length'] ) ) ) : '';

        // Limit by
        $question_limit_text_type = ( isset($question_options['question_limit_text_type']) && sanitize_text_field( $question_options['question_limit_text_type'] ) != '' ) ? sanitize_text_field( $question_options['question_limit_text_type'] ) : 'characters';

        // Show the counter-message
        $question_options['question_enable_text_message'] = isset($question_options['question_enable_text_message']) ? sanitize_text_field( $question_options['question_enable_text_message'] ) : 'off';
        $question_enable_text_message = (isset($question_options['question_enable_text_message']) && $question_options['question_enable_text_message'] == 'on') ? 'on' : 'off';

        // Maximum length of a number field
        $question_options['enable_question_number_max_length'] = isset($question_options['enable_question_number_max_length']) ? sanitize_text_field( $question_options['enable_question_number_max_length'] ) : 'off';
        $enable_question_number_max_length = (isset($question_options['enable_question_number_max_length']) && sanitize_text_field( $question_options['enable_question_number_max_length'] ) == 'on') ? 'on' : 'off';

        // Length
        $question_number_max_length = ( isset($question_options['question_number_max_length']) && sanitize_text_field( $question_options['question_number_max_length'] ) != '' ) ? intval( sanitize_text_field( $question_options['question_number_max_length'] ) ) : '';

        // Hide question text on the front-end
        $question_options['quiz_hide_question_text'] = isset($question_options['quiz_hide_question_text']) ? sanitize_text_field( $question_options['quiz_hide_question_text'] ) : 'off';
        $quiz_hide_question_text = (isset($question_options['quiz_hide_question_text']) && $question_options['quiz_hide_question_text'] == 'on') ? 'on' : 'off';

        $is_custom_type = false;
        $custom_types = array( "video", "custom" );
        if(in_array($questionDup["type"], $custom_types)){
            $is_custom_type = true;
        }

        if($is_custom_type){
            $question_content = $questionDup['question'];
        }else{
            $question_content = "Copy - " . $questionDup['question'];
        }

        if($is_custom_type){
            $question_title = "Copy - " . $questionDup['question_title'];
        }else{
            $question_title = $questionDup['question_title'];
        }

        $options = isset($questionDup['options']) ? json_decode($questionDup['options'], true) : array(
            'use_html' => $use_html,
            'enable_question_text_max_length' => $enable_question_text_max_length,
            'question_text_max_length' => $question_text_max_length,
            'question_limit_text_type' => $question_limit_text_type,
            'question_enable_text_message' => $question_enable_text_message,
            'enable_question_number_max_length' => $enable_question_number_max_length,
            'question_number_max_length' => $question_number_max_length,
            'quiz_hide_question_text' => $quiz_hide_question_text,
        );

        $question_result = $wpdb->insert(
            $questions_table,
            array(
                'category_id' => $questionDup['category_id'],
                'author_id' => get_current_user_id(),
                'question' => $question_content,
                'question_title' => $question_title,
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
                '%d', // author_id
                '%s', // question
                '%s', // question_title
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
                    'ordering'      => ($key + 1),
                    'weight'        => floatval( $answer['weight'] ),
                    'keyword'       => $answer['keyword'],
                    'placeholder'   => $answer['placeholder']
                ),
                array(
                    '%d', // question_id
                    '%s', // answer
                    '%s', // image
                    '%d', // correct
                    '%d', // ordering
                    '%f', // weight
                    '%s', // keyword
                    '%s', // placeholder
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

            $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
            if( $search ){
                $filter[] = sprintf(" question LIKE '%%%s%%' ", $search );
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
    
    public static function record_count_for_dashboard() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_questions WHERE 1=1";
        
        $current_user = get_current_user_id();
        if( ! Quiz_Maker_Data::quiz_maker_capabilities_for_editing() ){
            $sql .= " AND author_id = ".$current_user." ";
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
            case 'author_id':
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
        $current_page = $this->get_pagenum();
        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-question' );
        $current_user = get_current_user_id();
        $author_id = intval( $item['author_id'] );
        $owner = false;
        if( $current_user == $author_id ){
            $owner = true;
        }

        if( $this->current_user_can_edit ){
            $owner = true;
        }

        $question_title_length = intval( $this->title_length );

        $question_title = '';
        if($item['type'] == 'custom'){
            if(isset($item['question_title']) && $item['question_title'] != ''){
                $question_title = htmlspecialchars_decode($item['question_title'], ENT_COMPAT);
                $question_title = stripslashes($question_title);
            }else{
                $question_title = __( 'Custom question', $this->plugin_name ) . ' #'.$item['id'];
            }
            $q = esc_attr($question_title);
        }else{
            if(isset($item['question_title']) && $item['question_title'] != ''){
                $question_title = esc_attr( $item['question_title'] );
            }elseif( isset($item['question']) && strlen($item['question']) != 0){
                $question_title = strip_tags(stripslashes($item['question']));
                if ($question_title == '') {
                    $question_title = __( 'Question ID', $this->plugin_name ) .' '. $item['id'];
                }
            }elseif(isset($item['question_image']) && $item['question_image'] !=''){
                $question_title = __( 'Image question', $this->plugin_name );
            }
            $q = esc_attr($question_title);
        }
        $question_title = Quiz_Maker_Admin::ays_restriction_string("word",$question_title, $question_title_length);
        
        $title = sprintf( '<a href="?page=%s&paged=%d&action=%s&question=%d" title="%s">%s</a>', esc_attr( $_REQUEST['page'] ), $current_page, 'edit', absint( $item['id'] ), $q, $question_title );

        $actions = array();

        if( $owner ){
            $actions['edit'] = sprintf( '<a href="?page=%s&paged=%d&action=%s&question=%d">'. __('Edit', $this->plugin_name) .'</a>', esc_attr( $_REQUEST['page'] ), $current_page, 'edit', absint( $item['id'] ) );
        }else{
            $actions['edit'] = sprintf( '<a href="?page=%s&paged=%d&action=%s&question=%d">'. __('View', $this->plugin_name) .'</a>', esc_attr( $_REQUEST['page'] ), $current_page, 'edit', absint( $item['id'] ) );
        }

        $actions['duplicate'] = sprintf( '<a href="?page=%s&action=%s&question=%d">'. __('Duplicate', $this->plugin_name) .'</a>', esc_attr( $_REQUEST['page'] ), 'duplicate', absint( $item['id'] ) );
        
        if( $owner ){
            $actions['delete'] = sprintf( '<a class="ays_confirm_del" data-message="%s" href="?page=%s&action=%s&question=%s&_wpnonce=%s">'. __('Delete', $this->plugin_name) .'</a>', $question_title, esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce );
        }

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
        $date = isset($item['create_date']) && $item['create_date'] != '' ? $item['create_date'] : "0000-00-00 00:00:00";
        $date_format = get_option( 'date_format' );
        $time_format = get_option( 'time_format' );
        $format = $date_format . " " . $time_format;
        $text = "";
        if(Quiz_Maker_Admin::validateDate($date)){
            $text .= date_i18n( $format, strtotime( $date ) );
        }

        return $text;
    }

    function column_author_id( $item ) {
        $author_id = isset($item['author_id']) && intval( $item['author_id'] ) != 0 ? intval( $item['author_id'] ) : 0;
        $author = null;
        if( $author_id != 0){
            $author = get_userdata( $author_id );
        }
        
        $text = "";
        if( $author !== null ){
            $text .= $author->data->display_name;
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
        );
        
        if( $this->current_user_can_edit ){
            $columns['author_id'] = __( 'Author', $this->plugin_name );
        }

        $columns['used'] = __( 'Used', $this->plugin_name );
        $columns['id'] = __( 'ID', $this->plugin_name );
        
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
            'create_date'   => array( 'create_date', true ),
            'author_id'     => array( 'author_id', true ),
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
            'bulk-published' => __('Publish', $this->plugin_name),
            'bulk-unpublished' => __('Unpublish', $this->plugin_name),
            'bulk-delete' => __('Delete', $this->plugin_name),
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

        $do_search = ( $search ) ? sprintf(" ( question LIKE '%%%s%%' OR question_title LIKE '%%%s%%' ) ", $search, $search ) : '';

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
        || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' ) ) {

            $delete_ids = esc_sql( $_POST['bulk-delete'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::delete_questions( $id );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $url = esc_url_raw( remove_query_arg( array('action', 'question', '_wpnonce') ) ) . '&status=aredeleted';
            wp_redirect( $url );
        } elseif ( (isset($_POST['action']) && $_POST['action'] == 'bulk-published')
                || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-published') ) {

            $published_ids = esc_sql($_POST['bulk-delete']);

            // loop over the array of record IDs and mark as read them

            foreach ( $published_ids as $id ) {
                self::ays_quiz_published_unpublished_questions( $id , 'published' );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $url = esc_url_raw( remove_query_arg(array('action', 'question', '_wpnonce')  ) ) . '&status=published';
            wp_redirect( $url );
        } elseif ( (isset($_POST['action']) && $_POST['action'] == 'bulk-unpublished')
                || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-unpublished') ) {

            $unpublished_ids = esc_sql($_POST['bulk-delete']);

            // loop over the array of record IDs and mark as read them

            foreach ( $unpublished_ids as $id ) {
                self::ays_quiz_published_unpublished_questions( $id , 'unpublished' );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $url = esc_url_raw( remove_query_arg(array('action', 'question', '_wpnonce')  ) ) . '&status=unpublished';
            wp_redirect( $url );
        }
    }

    public function question_notices(){
        $status = (isset($_REQUEST['status'])) ? sanitize_text_field( $_REQUEST['status'] ) : '';

        if ( empty( $status ) )
            return;

        $status_color = ' notice-success ';
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
        elseif ( 'published' == $status )
            $updated_message = esc_html( __( 'Question(s) published.', $this->plugin_name ) );
        elseif ( 'unpublished' == $status )
            $updated_message = esc_html( __( 'Question(s) unpublished.', $this->plugin_name ) );
        elseif ( 'imported' == $status ){
            $counts = (isset($_REQUEST['stats'])) ? sanitize_text_field( $_REQUEST['stats'] ) : '';
            $impoted = 0;
            $error = 0;
            if($counts != ''){
                $stats = explode('-', $counts);
                $impoted = intval($stats[0]);
                $error = intval($stats[1]);
            }
            $updated_message = '';
            if($impoted == 0){
                $updated_message .= esc_html( __( 'Questions import failed.', $this->plugin_name ) );
                $status_color = ' notice-error ';
            }else{
                if($impoted == 1){
                    $updated_message .= $impoted . ' ' . esc_html( __( 'question is imported successfully.', $this->plugin_name ) );
                }else{
                    $updated_message .= $impoted . ' ' . esc_html( __( 'questions are imported successfully.', $this->plugin_name ) );
                }
                $updated_message .= '<br>';
                if($error == 0){
                    $updated_message .= esc_html( __( 'No failures found.', $this->plugin_name ) );
                }else{
                    if($error == 1){
                        $updated_message .= $error . ' ' . esc_html( __( 'question is failed to import.', $this->plugin_name ) );
                    }else{
                        $updated_message .= $error . ' ' . esc_html( __( 'questions are failed to import.', $this->plugin_name ) );
                    }
                }
            }
        }

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice <?php echo $status_color; ?> is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }
}
