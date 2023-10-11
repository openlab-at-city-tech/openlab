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

        $filter_author_html = "";
        $users = array();

        $user_id = null;
        if( isset( $_GET['filterbyuser'] )){
            $user_id = intval( sanitize_text_field( $_GET['filterbyuser'] ) );
        }
        if( $this->current_user_can_edit ){

            $author_sql = "SELECT DISTINCT `author_id` FROM `{$wpdb->prefix}aysquiz_questions`";
            $author_ids_arr = $wpdb->get_col($author_sql);

            if( isset( $author_ids_arr ) && !empty( $author_ids_arr ) ){

                $users_table = esc_sql( $wpdb->prefix . 'users' );

                $quiz_user_ids = implode( ",", $author_ids_arr );

                $sql_users = "SELECT ID,display_name FROM {$users_table} WHERE ID IN (". $quiz_user_ids .")";

                $users = $wpdb->get_results($sql_users, "ARRAY_A");
                if ( !is_null( $users ) && !empty( $users ) ) {
                    sort($users);
                }
            }

            $filter_author_html = array();

            $filter_author_html[] = '<select name="filterbyuser-'. esc_attr( $which ) .'" id="bulk-action-filterbyuser-selector-'. esc_attr( $which ) .'">';
                $filter_author_html[] = '<option value="">'. __('Select author',$this->plugin_name) .'</option>';
                foreach($users as $key => $value){
                    $selected2 = "";
                    if($user_id === intval($value['ID'])){
                        $selected2 = "selected";
                    }
                    $filter_author_html[] = "<option ".$selected2." value='".$value['ID']."'>".$value['display_name']."</option>";
                }
            $filter_author_html[] = '</select>';

            $filter_author_html = implode( '', $filter_author_html);
        }

        $tag_titles = "SELECT `title`, `id` FROM {$wpdb->prefix}aysquiz_question_tags";
        $tag_results = $wpdb->get_results($tag_titles);
        $tag_ids = null;
        if( isset( $_GET['filterbytags'] )){
            $tag_ids = explode(',', $_GET['filterbytags']);
        }

        $tab_select = array();
        foreach($tag_results as $key => $tag_title){
            $selected = "";
            if($tag_ids != null){

                if(in_array($tag_title->id, $tag_ids)){
                    $selected = "selected";
                }
            }
            $tab_select[$tag_title->id]['title'] = $tag_title->title;
            $tab_select[$tag_title->id]['selected'] = $selected;
            $tab_select[$tag_title->id]['id'] = $tag_title->id;
        }
        sort($tab_select);

        $question_types = array(
            "radio"             => __("Radio", $this->plugin_name),
            "checkbox"          => __("Checkbox( Multiple )", $this->plugin_name),
            "select"            => __("Dropdown", $this->plugin_name),
            "text"              => __("Text", $this->plugin_name),
            "short_text"        => __("Short Text", $this->plugin_name),
            "number"            => __("Number", $this->plugin_name),
            "date"              => __("Date", $this->plugin_name),
            "true_or_false"     => __("True/False", $this->plugin_name),
            "custom"            => __("Custom (Banner)", $this->plugin_name),
            "fill_in_blank"     => __("Fill in the blanks", $this->plugin_name),
        );

        $selected_question_type = (isset( $_GET['type'] ) && sanitize_text_field( $_GET['type'] ) != "") ? sanitize_text_field( $_GET['type'] ) : "";

        ?>
        <div id="category-filter-div" class="alignleft actions bulkactions">
            <?php echo $filter_author_html; ?>

            <select name="filterby-<?php echo esc_attr( $which ); ?>" id="bulk-action-category-selector-<?php echo esc_attr( $which ); ?>">
                <option value=""><?php echo __('Select Category',$this->plugin_name)?></option>
                <?php
                    foreach($categories_select as $key => $cat_title){
                        echo "<option ".$cat_title['selected']." value='".$cat_title['id']."'>".$cat_title['title']."</option>";
                    }
                ?>
            </select>

            <select name="type-<?php echo esc_attr( $which ); ?>" id="bulk-action-question-type-selector-<?php echo esc_attr( $which ); ?>">
                <option value=""><?php echo __('Select question type',$this->plugin_name)?></option>
                <?php
                    $question_type_html = array();
                    foreach($question_types as $option_value => $question_type){
                        $selected_type = ($selected_question_type == $option_value ) ? "selected" : "";

                        $question_type_html[] = "<option value='".$option_value."' ". $selected_type .">".$question_type."</option>";
                    }
                    $question_type_html = implode( '' , $question_type_html);
                    echo $question_type_html;
                ?>
            </select>

            <select name="filterbytags-<?php echo esc_attr( $which ); ?>" id="bulk-action-question-tag-selector-<?php echo esc_attr( $which ); ?>" class="ays-quiz-question-tab-filter" multiple>
                <option value=""><?php echo __('Select Tags',$this->plugin_name)?></option>
                <?php
                    foreach($tab_select as $key => $tab_title){
                        echo "<option ".$tab_title['selected']." value='".$tab_title['id']."'>".$tab_title['title']."</option>";
                    }
                ?>
            </select>
            <input type="button" id="doaction-<?php echo esc_attr( $which ); ?>" class="ays-quiz-question-tab-all-filter-button-<?php echo esc_attr( $which ); ?> button" value="<?php echo __( "Filter", $this->plugin_name ); ?>">
            <a style="margin: 0px 8px 0 0;display:inline-block;" href="?page=<?php echo sanitize_text_field( $_REQUEST['page'] ); ?>" class="button"><?php echo __( "Clear filters", $this->plugin_name ); ?></a>
        </div>
        <?php
    }
    
    protected function get_views() {
        $published_count = $this->published_questions_count();
        $unpublished_count = $this->unpublished_questions_count();
        $trash_count = $this->trash_questions_count();
        $all_count = $this->all_record_count();
        $selected_all = "";
        $selected_0 = "";
        $selected_1 = "";
        $selected_2 = "";
        if(isset($_GET['fstatus'])){
            switch($_GET['fstatus']){
                case "0":
                    $selected_0 = " style='font-weight:bold;' ";
                    break;
                case "1":
                    $selected_1 = " style='font-weight:bold;' ";
                    break;
                case "2":
                    $selected_2 = " style='font-weight:bold;' ";
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
            "all" => "<a ".$selected_all." href='?page=".esc_attr( $_REQUEST['page'] )."'>" . __( "All", $this->plugin_name ) . " (".$all_count.")</a>",
        );

        if( intval( $published_count ) > 0 ){
            $status_links["published"] = "<a ".$selected_1." href='?".esc_attr( $query_str )."&fstatus=1'>". __( 'Published', $this->plugin_name )." (".$published_count.")</a>";
        }
        if( intval( $unpublished_count ) > 0 ){
            $status_links["draft"] = "<a ".$selected_0." href='?".esc_attr( $query_str )."&fstatus=0'>". __( 'Unpublished', $this->plugin_name )." (".$unpublished_count.")</a>";
        }
        if( intval( $trash_count ) > 0 ){
            $status_links["trashed"] = "<a ".$selected_2." href='?".esc_attr( $query_str )."&fstatus=2'>". __( 'Trash', $this->plugin_name )." (".$trash_count.")</a>";
        }

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

        if ( Quiz_Maker_Data::quiz_maker_capabilities_for_editing() ) {
            if( isset( $_REQUEST['filterbyuser'] ) && ! empty( $_REQUEST['filterbyuser'] ) && $_REQUEST['filterbyuser'] > 0){
                $author_id = intval( sanitize_text_field( $_REQUEST['filterbyuser'] ) );
                $where[] = ' author_id = '.$author_id.'';
            }
        }

        if( isset( $_REQUEST['filterbytags'] ) && ! empty( $_REQUEST['filterbytags'] )){

            $tag_ids = explode(',', sanitize_text_field( $_REQUEST['filterbytags'] ));
            $tags_sql = array();
            if(!empty($tag_ids) && $tag_ids[0] != ''){

                $numItems = count($tag_ids);
                $flag_i = 0;
                foreach($tag_ids as $key => $tag_id){
                    $tag_id = absint( sanitize_text_field( $tag_id ) );
                    $start_sql = "";
                    $close_sql = "";
                    if ( $tag_id > 0 ) {
                        if ( $numItems > 1 ) {
                            if ( $flag_i == 0 ) {
                                $start_sql = " ( ";
                            }

                            if(++$flag_i === $numItems) {
                                $close_sql = " ) ";
                            }
                        }
                        $tags_sql[] = $start_sql . ' FIND_IN_SET('.$tag_id.', tag_id ) ' . $close_sql;
                    }
                }
            }
            if( !empty( $tags_sql ) ){
                $where[] = implode( ' OR ', $tags_sql );
            }
        }

        if( isset( $_REQUEST['type'] ) ){
            $where[] = ' type = "'. sanitize_text_field( $_REQUEST['type'] ) .'" ';
        }
        if( isset( $_REQUEST['fstatus'] ) ){
            $fstatus  = absint( esc_sql( $_REQUEST['fstatus'] ) );
            if($fstatus !== null){
                $where[] = " published = ".$fstatus." ";
            }
        }else{
            $where[] = " published != 2 ";
        }

        if( ! empty($where) ){
            $sql .= " WHERE " . implode( " AND ", $where );
        }

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $order_by  = ( isset( $_REQUEST['orderby'] ) && sanitize_text_field( $_REQUEST['orderby'] ) != '' ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'id';
            $order_by .= ( ! empty( $_REQUEST['order'] ) && strtolower( $_REQUEST['order'] ) == 'asc' ) ? ' ASC' : ' DESC';

            $sql_orderby = sanitize_sql_orderby($order_by);

            if ( $sql_orderby ) {
                $sql .= ' ORDER BY ' . $sql_orderby;
            } else {
                $sql .= ' ORDER BY id DESC';
            }
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

    /**
     * Move to trash a customer record.
     *
     * @param int $id customer ID
     */
    public static function trash_items( $id ) {
        global $wpdb;

        $wpdb->update(
            "{$wpdb->prefix}aysquiz_questions",
            array( 'published' => 2 ),
            array( 'id' => absint( $id ) ),
            array( '%d' ),
            array( '%d' )
        );
    }

    /**
     * Restore a customer record.
     *
     * @param int $id customer ID
     */
    public static function restore_items( $id ) {
        global $wpdb;

        $wpdb->update(
            "{$wpdb->prefix}aysquiz_questions",
            array( 'published' => 1 ),
            array( 'id' => absint( $id ) ),
            array( '%d' ),
            array( '%d' )
        );
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

    public function get_published_question_categories() {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_categories WHERE `published`= 1 ORDER BY title ASC";

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
            $question = '';
            if ( isset( $_POST['ays_question'] ) ) {
                $question = wp_kses_post( $_POST['ays_question'] );
            }


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

            // Answers Matches Array
            $matches_values      = (isset($data['ays-correct-answer-value-match']) && ! empty($data['ays-correct-answer-value-match'])) ? $data['ays-correct-answer-value-match'] : array();

            // Answers placeholders Array
            $answer_placeholders = (isset($data['ays-answer-placeholder']) && ! empty($data['ays-answer-placeholder']) ) ? $data['ays-answer-placeholder'] : array();

            // Answers slugs Array
            $answer_slags = (isset($data['ays_answer_slug']) && ! empty($data['ays_answer_slug']) ) ? array_map("sanitize_text_field", $data['ays_answer_slug']) : array();

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

            // Answer global weight
            $answers_weight_for_all = (isset($data['ays_answers_weight']) && $data['ays_answers_weight'] != '') ? floatval($data['ays_answers_weight']) : floatval(0);

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

            // Enable maximum selection number
            $enable_max_selection_number = (isset($_POST['ays_enable_max_selection_number']) && sanitize_text_field( $_POST['ays_enable_max_selection_number'] ) == 'on') ? 'on' : 'off';

            // Max value
            $max_selection_number = ( isset($_POST['ays_max_selection_number']) && $_POST['ays_max_selection_number'] != '' ) ? absint( sanitize_text_field ( $_POST['ays_max_selection_number'] ) ) : '';

            //Question Tag ID
            $question_tag_id = ( isset($_POST['ays_quiz_question_tags']) && !empty($_POST['ays_quiz_question_tags'])) ? implode(',', $_POST['ays_quiz_question_tags'] ) : '';

            // Note text
            $quiz_question_note_message = ( isset($_POST['ays_quiz_question_note_message']) && $_POST['ays_quiz_question_note_message'] != '' ) ? wp_kses_post( $_POST['ays_quiz_question_note_message'] ) : '';

            // Enable case sensitive text
            $enable_case_sensitive_text = (isset($_POST['ays_enable_case_sensitive_text']) && sanitize_text_field( $_POST['ays_enable_case_sensitive_text'] ) == 'on') ? 'on' : 'off';

            // Enable minimum selection number
            $enable_min_selection_number = (isset($_POST['ays_enable_min_selection_number']) && sanitize_text_field( $_POST['ays_enable_min_selection_number'] ) == 'on') ? 'on' : 'off';

            // Min value
            $min_selection_number = ( isset($_POST['ays_min_selection_number']) && $_POST['ays_min_selection_number'] != '' ) ? absint( sanitize_text_field ( $_POST['ays_min_selection_number'] ) ) : '';

            // Minimum length of a text field
            $enable_question_number_min_length = (isset($_POST['ays_enable_question_number_min_length']) && sanitize_text_field( $_POST['ays_enable_question_number_min_length'] ) == 'on') ? 'on' : 'off';

            // Length
            $question_number_min_length = ( isset($_POST['ays_question_number_min_length']) && sanitize_text_field( $_POST['ays_question_number_min_length'] ) != '' ) ? intval( sanitize_text_field ( $_POST['ays_question_number_min_length'] ) ) : '';

            // Show error message
            $enable_question_number_error_message = (isset($_POST['ays_enable_question_number_error_message']) && sanitize_text_field( $_POST['ays_enable_question_number_error_message'] ) == 'on') ? 'on' : 'off';

            // Message
            $question_number_error_message = ( isset($_POST['ays_question_number_error_message']) && sanitize_text_field( $_POST['ays_question_number_error_message'] ) != '' ) ? stripslashes( sanitize_text_field ( $_POST['ays_question_number_error_message'] ) ) : '';

            // Enable strip slashes for questions
            $quiz_enable_question_stripslashes = (isset($_POST['ays_quiz_enable_question_stripslashes']) && sanitize_text_field( $_POST['ays_quiz_enable_question_stripslashes'] ) == 'on') ? 'on' : 'off';

            // Disable strip slashes for answers
            $quiz_disable_answer_stripslashes = (isset($_POST['ays_quiz_disable_answer_stripslashes']) && sanitize_text_field( $_POST['ays_quiz_disable_answer_stripslashes'] ) == 'on') ? 'on' : 'off';

            // Answer slug max ID
            $answer_slug_max_id = ( isset($_POST['ays_answer_slug_max_id']) && sanitize_text_field( $_POST['ays_answer_slug_max_id'] ) != '' ) ? absint( sanitize_text_field ( $_POST['ays_answer_slug_max_id'] ) ) : 1;

            // Matching question type incorrect answers/matches
            $answer_incorrect_matches_arr = (isset($data['ays-answer-incorrect-matches']) && !empty($data['ays-answer-incorrect-matches'])) ? $data['ays-answer-incorrect-matches'] : array();
            $answer_incorrect_matches = array();
            foreach ( $answer_incorrect_matches_arr as $vkey => $value ) {
                $answer_incorrect_matches[ -($vkey +1) ] = $value;
            }

            $options = array(
				'bg_image'                              => $bg_image,
                'use_html'                              => $use_html,
                'enable_question_text_max_length'       => $enable_question_text_max_length,
                'question_text_max_length'              => $question_text_max_length,
                'question_limit_text_type'              => $question_limit_text_type,
                'question_enable_text_message'          => $question_enable_text_message,
                'enable_question_number_max_length'     => $enable_question_number_max_length,
                'question_number_max_length'            => $question_number_max_length,
                'quiz_hide_question_text'               => $quiz_hide_question_text,
                'enable_max_selection_number'           => $enable_max_selection_number,
                'max_selection_number'                  => $max_selection_number,
                'quiz_question_note_message'            => $quiz_question_note_message,
                'enable_case_sensitive_text'            => $enable_case_sensitive_text,
                'enable_min_selection_number'           => $enable_min_selection_number,
                'min_selection_number'                  => $min_selection_number,
                'enable_question_number_min_length'     => $enable_question_number_min_length,
                'question_number_min_length'            => $question_number_min_length,
                'enable_question_number_error_message'  => $enable_question_number_error_message,
                'question_number_error_message'         => $question_number_error_message,
                'quiz_enable_question_stripslashes'     => $quiz_enable_question_stripslashes,
                'quiz_disable_answer_stripslashes'      => $quiz_disable_answer_stripslashes,
                'answer_slug_max_id'                    => $answer_slug_max_id,
                'answer_incorrect_matches'              => $answer_incorrect_matches,
            );
            
            $text_types = array('text', 'short_text', 'number', 'fill_in_blank');
            if($id == 0) {
                $question_result = $wpdb->insert(
                    $questions_table,
                    array(
                        'category_id'       => $category_id,
                        'tag_id'            => $question_tag_id,
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
                        'answers_weight'    => $answers_weight_for_all,
                        'options'           => json_encode($options),
                    ),
                    array(
                        '%d', // category_id
                        '%s', // tag_id
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
                        '%f', // answers_weight
                        '%s', // options
                    )
                );
                $question_id = $wpdb->insert_id;
                $answers_results = array();
                $flag = true;
                foreach ($answer_values as $index => $answer_value) {
                    if ( $quiz_disable_answer_stripslashes == 'off' ) {
                        $answer_value = stripslashes($answer_value);
                    }
                    if(in_array( $type, $text_types )){
                        $correct = 1;
                        $answer_value = htmlspecialchars_decode($answer_value, ENT_QUOTES );
                    }else{
                        $correct = (in_array(($index + 1), $correct_answers)) ? 1 : 0;
                    }
                    $weight = $answers_weight[$index];

                    $keyword = 'A';
                    if(isset($answer_keywords[$index])){
                        $keyword = $answer_keywords[$index];
                    }

                    if (!in_array( $type, $text_types ) && trim($answer_value) == '') {
                        continue;
                    }

                    $placeholder = '';
                    if(isset($answer_placeholders[$index])){
                        $placeholder = $answer_placeholders[$index];
                    }

                    $answer_slag = '';
                    if(isset($answer_slags[$index])){
                        $answer_slag = $answer_slags[$index];
                    }

                    $answer_options = array(
                        
                    );

                    if(isset($matches_values[$index])){
                        $match = $matches_values[$index];
                        if ( $quiz_disable_answer_stripslashes == 'off' ) {
                            $match = stripslashes($match);
                        }
                        $answer_options['correct_match'] = $match;
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
                            'placeholder'   => $placeholder,
                            'slug'          => $answer_slag,
                            'options'       => json_encode($answer_options),
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
                            '%s', // slug
                            '%s', // options
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
                        'tag_id'            => $question_tag_id,
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
                        'answers_weight'    => $answers_weight_for_all,
                        'options'           => json_encode($options),
                    ),
                    array( 'id' => $id ),
                    array(
                        '%d', // category_id
                        '%s', // tag_id
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
                        '%f', // answers_weight
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
                        if ( $quiz_disable_answer_stripslashes == 'off' ) {
                            $answer_value = stripslashes($answer_value);
                        }
                        if(in_array( $type, $text_types )){
                            $correct = 1;
                            $answer_value = htmlspecialchars_decode($answer_value, ENT_QUOTES );
                        }else{
                            $correct = (in_array(($index + 1), $correct_answers)) ? 1 : 0;
                        }
                        $weight = $answers_weight[$index];

                        $keyword = 'A';
                        if(isset($answer_keywords[$index])){
                            $keyword = $answer_keywords[$index];
                        }

                        if (!in_array( $type, $text_types ) && trim($answer_value) == '') {
                            continue;
                        }

                        $placeholder = '';
                        if(isset($answer_placeholders[$index])){
                            $placeholder = $answer_placeholders[$index];
                        }

                        $answer_slag = '';
                        if(isset($answer_slags[$index])){
                            $answer_slag = $answer_slags[$index];
                        }

                        $answer_options = array(
                            
                        );

                        if(isset($matches_values[$index])){
                            $match = $matches_values[$index];
                            if ( $quiz_disable_answer_stripslashes == 'off' ) {
                                $match = stripslashes($match);
                            }
                            $answer_options['correct_match'] = $match;
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
                                'placeholder'   => $placeholder,
                                'slug'          => $answer_slag,
                                'options'       => json_encode($answer_options),
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
                                '%s', // slug
                                '%s', // options
                            ),
                            array('%d')
                        );
                    }
                }

                if($old_answers_count < count($answer_values)){
                    foreach ($answer_values as $index => $answer_value) {
                        if ( $quiz_disable_answer_stripslashes == 'off' ) {
                            $answer_value = stripslashes($answer_value);
                        }
                        if(in_array( $type, $text_types )){
                            $correct = 1;
                            $answer_value = htmlspecialchars_decode($answer_value, ENT_QUOTES );
                        }else{
                            $correct = (in_array(($index + 1), $correct_answers)) ? 1 : 0;
                        }
                        $weight = $answers_weight[$index];

                        $keyword = 'A';
                        if(isset($answer_keywords[$index])){
                            $keyword = $answer_keywords[$index];
                        }

                        if (!in_array( $type, $text_types ) && trim($answer_value) == '') {
                            continue;
                        }

                        $placeholder = '';
                        if(isset($answer_placeholders[$index])){
                            $placeholder = $answer_placeholders[$index];
                        }

                        $answer_slag = '';
                        if(isset($answer_slags[$index])){
                            $answer_slag = $answer_slags[$index];
                        }

                        $answer_options = array(
                            
                        );

                        if(isset($matches_values[$index])){
                            $match = $matches_values[$index];
                            if ( $quiz_disable_answer_stripslashes == 'off' ) {
                                $match = stripslashes($match);
                            }
                            $answer_options['correct_match'] = $match;
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
                                    'placeholder'   => $placeholder,
                                    'slug'          => $answer_slag,
                                    'options'       => json_encode($answer_options),
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
                                    '%s', // slug
                                    '%s', // options
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
                                    'placeholder'   => $placeholder,
                                    'slug'          => $answer_slag,
                                    'options'       => json_encode($answer_options),
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
                                    '%s', // slug
                                    '%s', // options
                                ),
                                array('%d')
                            );
                        }
                    }
                }

                if($old_answers_count > count($answer_values)){
                    if ( $quiz_disable_answer_stripslashes == 'off' ) {
                        $answer_value = stripslashes($answer_value);
                    }
                    $diff = $old_answers_count - count($answer_values);

                    $removeable_answers = array_slice( $old_answers, -$diff, $diff );

                    foreach ( $removeable_answers as $removeable_answer ){
                        $delete_result = $wpdb->delete( $answers_table, array('id' => intval( $removeable_answer["id"] )) );
                    }

                    foreach ($answer_values as $index => $answer_value) {
                        if(in_array( $type, $text_types )){
                            $correct = 1;
                            $answer_value = htmlspecialchars_decode($answer_value, ENT_QUOTES );
                        }else{
                            $correct = (in_array(($index + 1), $correct_answers)) ? 1 : 0;
                        }
                        $weight = $answers_weight[$index];

                        $keyword = 'A';
                        if(isset($answer_keywords[$index])){
                            $keyword = $answer_keywords[$index];
                        }

                        if (!in_array( $type, $text_types ) && trim($answer_value) == '') {
                            continue;
                        }

                        $placeholder = '';
                        if(isset($answer_placeholders[$index])){
                            $placeholder = $answer_placeholders[$index];
                        }

                        $answer_slag = '';
                        if(isset($answer_slags[$index])){
                            $answer_slag = $answer_slags[$index];
                        }

                        $answer_options = array(
                            
                        );

                        if(isset($matches_values[$index])){
                            $match = $matches_values[$index];
                            if ( $quiz_disable_answer_stripslashes == 'off' ) {
                                $match = stripslashes($match);
                            }
                            $answer_options['correct_match'] = $match;
                        }

                        $answers_results[] = $wpdb->update(
                            $answers_table,
                            array(
                                'question_id'   => $id,
                                'answer'        => $answer_value,
                                'image'         => isset( $answer_image[$index] ) ? $answer_image[$index] : '',
                                'correct'       => $correct,
                                'ordering'      => ($index + 1),
                                'weight'        => $weight,
                                'keyword'       => $keyword,
                                'placeholder'   => $placeholder,
                                'slug'          => $answer_slag,
                                'options'       => json_encode($answer_options),
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
                                '%s', // slug
                                '%s', // options
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

        // Update existing questions
        $_POST['ays_quiz_update_existing_questions'] = isset($_POST['ays_quiz_update_existing_questions']) ? $_POST['ays_quiz_update_existing_questions'] : 'off';
        $quiz_update_existing_questions = (isset($_POST['ays_quiz_update_existing_questions']) && $_POST['ays_quiz_update_existing_questions'] == 'on') ? true : false;


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

        $question_tags_table = $wpdb->prefix . "aysquiz_question_tags";
        $tags_r = $wpdb->get_results("SELECT id, title FROM ". $question_tags_table ." WHERE status='published'", 'ARRAY_A');
        $tags = array();
        foreach($tags_r as $tag){
            $tags[$tag['id']] = strtolower($tag['title']);
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

                        $question_tags = array();
                        $question_tags_file = array();
                        if(isset($question['tags'])){
                            $question_tags_file = explode( ',', $question['tags'] );
                        }

                        foreach( $question_tags_file as $tk => $tag_name ){
                            if( $tag_name == null || $tag_name == '' ){
                                continue;
                            }

                            if(in_array(strtolower($tag_name), $tags)){
                                $tag_id = array_search(strtolower($tag_name), $tags);
                                if($tag_id !== false){
                                    $question_tags[] = intval($tag_id);
                                }
                            }else{
                                $wpdb->insert(
                                    $question_tags_table,
                                    array(
                                        'title' => $tag_name,
                                        'status' => 'published'
                                    ),
                                    array( '%s', '%s' )
                                );
                                $question_tags[] = $wpdb->insert_id;
                                $tags[$wpdb->insert_id] = strtolower($tag_name);
                            }
                        }

                        $category_id = $question_category;
                        $tag_ids = implode( ',', $question_tags );

                        $db_question_id = (isset($question['id']) && absint(sanitize_text_field($question['id'])) != 0) ? absint(sanitize_text_field($question['id'])) : "";

                        $question_content = htmlspecialchars_decode(isset($question['question']) && $question['question'] != '' ? $question['question'] : '', ENT_HTML5);
                        $question_content = Quiz_Maker_Data::convertFromCP1252( $question_content );
                        $question_content = preg_replace('/_x([0-9a-fA-F]{4})_/', '&#x$1;', $question_content);

                        $question_title = htmlspecialchars_decode(isset($question['question_title']) && $question['question_title'] != '' ? $question['question_title'] : '', ENT_HTML5);
                        $question_title = Quiz_Maker_Data::convertFromCP1252( $question_title );

                        $question_image = (isset($question['question_image']) && $question['question_image'] != '') ? $question['question_image'] : '';
                        $question_hint = htmlspecialchars_decode(isset($question['question_hint']) && $question['question_hint'] != '' ? $question['question_hint'] : '');
                        $question_hint = Quiz_Maker_Data::convertFromCP1252( $question_hint );

                        $type = (isset($question['type']) && $question['type'] != '') ? strtolower($question['type']) : 'radio';
                        $published = (isset($question['published']) && absint(intval($question['published'])) == 0) ? intval($question['published']) : 1;

                        $wrong_answer_text = htmlspecialchars_decode(isset($question['wrong_answer_text']) && $question['wrong_answer_text'] != '' ? $question['wrong_answer_text'] : '');
                        $wrong_answer_text = Quiz_Maker_Data::convertFromCP1252( $wrong_answer_text );

                        $right_answer_text = htmlspecialchars_decode(isset($question['right_answer_text']) && $question['right_answer_text'] != '' ? $question['right_answer_text'] : '');
                        $right_answer_text = Quiz_Maker_Data::convertFromCP1252( $right_answer_text );

                        $explanation = htmlspecialchars_decode(isset($question['explanation']) && $question['explanation'] != '' ? $question['explanation'] : '');
                        $explanation = Quiz_Maker_Data::convertFromCP1252( $explanation );

                        $user_explanation = (isset($question['user_explanation']) && $question['user_explanation'] != '') ? $question['user_explanation'] : 'off';
                        $not_influence_to_score = (isset($question['not_influence_to_score']) && $question['not_influence_to_score'] != '') ? $question['not_influence_to_score'] : 'off';
                        $question_weight = (isset($question['weight']) && $question['weight'] != '') ? floatval($question['weight']) : 1;
                        $create_date = current_time( 'mysql' );
                        $answers_get = $question['answers'];
                        $questions_options = $question['options'];

                        $bg_image = (isset($questions_options['bg_image']) && $questions_options['bg_image'] != '') ? $questions_options['bg_image'] : '';
                        $use_html = (isset($questions_options['use_html']) && $questions_options['use_html'] != '') ? $questions_options['use_html'] : 'off';
                        $enable_question_text_max_length = (isset($questions_options['enable_question_text_max_length']) && $questions_options['enable_question_text_max_length'] != '') ? $questions_options['enable_question_text_max_length'] : 'off';
                        $question_text_max_length = (isset($questions_options['question_text_max_length']) && $questions_options['question_text_max_length'] != '') ? $questions_options['question_text_max_length'] : '';
                        $question_limit_text_type = (isset($questions_options['question_limit_text_type']) && $questions_options['question_limit_text_type'] != '') ? $questions_options['question_limit_text_type'] : '';
                        $question_enable_text_message = (isset($questions_options['question_enable_text_message']) && $questions_options['question_enable_text_message'] != '') ? $questions_options['question_enable_text_message'] : 'off';

                        // Maximum length of a number field
                        $questions_options['enable_question_number_max_length'] = isset($questions_options['enable_question_number_max_length']) ? sanitize_text_field( $questions_options['enable_question_number_max_length'] ) : 'off';
                        $enable_question_number_max_length = (isset($questions_options['enable_question_number_max_length']) && sanitize_text_field( $questions_options['enable_question_number_max_length'] ) == 'on') ? 'on' : 'off';

                        // Length
                        $question_number_max_length = ( isset($questions_options['question_number_max_length']) && sanitize_text_field( $questions_options['question_number_max_length'] ) != '' ) ? intval( sanitize_text_field( $questions_options['question_number_max_length'] ) ) : '';

                        // Hide question text on the front-end
                        $questions_options['quiz_hide_question_text'] = isset($questions_options['quiz_hide_question_text']) ? sanitize_text_field( $questions_options['quiz_hide_question_text'] ) : 'off';
                        $quiz_hide_question_text = (isset($questions_options['quiz_hide_question_text']) && $questions_options['quiz_hide_question_text'] == 'on') ? 'on' : 'off';


                        // Enable maximum selection number
                        $questions_options['enable_max_selection_number'] = isset($questions_options['enable_max_selection_number']) ? sanitize_text_field( $questions_options['enable_max_selection_number'] ) : 'off';
                        $enable_max_selection_number = (isset($questions_options['enable_max_selection_number']) && sanitize_text_field( $questions_options['enable_max_selection_number'] ) == 'on') ? 'on' : 'off';

                        // Max value
                        $max_selection_number = ( isset($questions_options['max_selection_number']) && $questions_options['max_selection_number'] != '' ) ? intval( sanitize_text_field ( $questions_options['max_selection_number'] ) ) : '';

                        // Note text
                        $quiz_question_note_message = ( isset($questions_options['quiz_question_note_message']) && $questions_options['quiz_question_note_message'] != '' ) ? wp_kses_post( $questions_options['quiz_question_note_message'] ) : '';
                        if ( $quiz_question_note_message != "" ) {
                            $quiz_question_note_message = htmlspecialchars_decode( stripslashes( str_replace( "\n", "", $quiz_question_note_message ) ) );
                        }

                        // Enable case sensitive text
                        $enable_case_sensitive_text = (isset($questions_options['enable_case_sensitive_text']) && sanitize_text_field( $questions_options['enable_case_sensitive_text'] ) == 'on') ? 'on' : 'off';

                        // Enable minimum selection number
                        $questions_options['enable_min_selection_number'] = isset($questions_options['enable_min_selection_number']) ? sanitize_text_field( $questions_options['enable_min_selection_number'] ) : 'off';
                        $enable_min_selection_number = (isset($questions_options['enable_min_selection_number']) && sanitize_text_field( $questions_options['enable_min_selection_number'] ) == 'on') ? 'on' : 'off';

                        // Min value
                        $min_selection_number = ( isset($questions_options['min_selection_number']) && $questions_options['min_selection_number'] != '' ) ? intval( sanitize_text_field ( $questions_options['min_selection_number'] ) ) : '';

                        // Minimum length of a number field
                        $question_options['enable_question_number_min_length'] = isset($question_options['enable_question_number_min_length']) ? sanitize_text_field( $question_options['enable_question_number_min_length'] ) : 'off';
                        $enable_question_number_min_length = (isset($question_options['enable_question_number_min_length']) && sanitize_text_field( $question_options['enable_question_number_min_length'] ) == 'on') ? 'on' : 'off';

                        // Length
                        $question_number_min_length = ( isset($question_options['question_number_min_length']) && sanitize_text_field( $question_options['question_number_min_length'] ) != '' ) ? intval( sanitize_text_field( $question_options['question_number_min_length'] ) ) : '';

                        // Show error message
                        $question_options['enable_question_number_error_message'] = isset($question_options['enable_question_number_error_message']) ? sanitize_text_field( $question_options['enable_question_number_error_message'] ) : 'off';
                        $enable_question_number_error_message = (isset($question_options['enable_question_number_error_message']) && sanitize_text_field( $question_options['enable_question_number_error_message'] ) == 'on') ? 'on' : 'off';

                        // Message
                        $question_number_error_message = ( isset($question_options['question_number_error_message']) && sanitize_text_field( $question_options['question_number_error_message'] ) != '' ) ? stripslashes( sanitize_text_field( $question_options['question_number_error_message'] ) ) : '';

                        // Enable strip slashes for questions
                        $questions_options['quiz_enable_question_stripslashes'] = isset($questions_options['quiz_enable_question_stripslashes']) ? sanitize_text_field( $questions_options['quiz_enable_question_stripslashes'] ) : 'off';
                        $quiz_enable_question_stripslashes = (isset($questions_options['quiz_enable_question_stripslashes']) && sanitize_text_field( $questions_options['quiz_enable_question_stripslashes'] ) == 'on') ? 'on' : 'off';

                        // Disable strip slashes for answers
                        $questions_options['quiz_disable_answer_stripslashes'] = isset($questions_options['quiz_disable_answer_stripslashes']) ? sanitize_text_field( $questions_options['quiz_disable_answer_stripslashes'] ) : 'off';
                        $quiz_disable_answer_stripslashes = (isset($questions_options['quiz_disable_answer_stripslashes']) && sanitize_text_field( $questions_options['quiz_disable_answer_stripslashes'] ) == 'on') ? 'on' : 'off';

                        // Disable strip slashes for answers
                        $questions_options['quiz_disable_answer_stripslashes'] = isset($questions_options['quiz_disable_answer_stripslashes']) ? sanitize_text_field( $questions_options['quiz_disable_answer_stripslashes'] ) : 'off';
                        $quiz_disable_answer_stripslashes = (isset($questions_options['quiz_disable_answer_stripslashes']) && sanitize_text_field( $questions_options['quiz_disable_answer_stripslashes'] ) == 'on') ? 'on' : 'off';

                        // Answer slug max ID
                        $answer_slug_max_id = ( isset($questions_options['answer_slug_max_id']) && sanitize_text_field( $questions_options['answer_slug_max_id'] ) != '' ) ? absint( sanitize_text_field ( $questions_options['answer_slug_max_id'] ) ) : 1;

                        // Matching question type incorrect answers/matches
                        $answer_incorrect_matches = (isset($questions_options['answer_incorrect_matches']) && !empty($questions_options['answer_incorrect_matches'])) ? $questions_options['answer_incorrect_matches'] : array();

                        $options = array(
                            'author'                                => $author,
                            'bg_image'                              => $bg_image,
                            'use_html'                              => $use_html,
                            'enable_question_text_max_length'       => $enable_question_text_max_length,
                            'question_text_max_length'              => $question_text_max_length,
                            'question_limit_text_type'              => $question_limit_text_type,
                            'question_enable_text_message'          => $question_enable_text_message,
                            'enable_question_number_max_length'     => $enable_question_number_max_length,
                            'question_number_max_length'            => $question_number_max_length,
                            'quiz_hide_question_text'               => $quiz_hide_question_text,
                            'enable_max_selection_number'           => $enable_max_selection_number,
                            'max_selection_number'                  => $max_selection_number,
                            'quiz_question_note_message'            => $quiz_question_note_message,
                            'enable_case_sensitive_text'            => $enable_case_sensitive_text,
                            'enable_min_selection_number'           => $enable_min_selection_number,
                            'min_selection_number'                  => $min_selection_number,
                            'enable_question_number_min_length'     => $enable_question_number_min_length,
                            'question_number_min_length'            => $question_number_min_length,
                            'enable_question_number_error_message'  => $enable_question_number_error_message,
                            'question_number_error_message'         => $question_number_error_message,
                            'quiz_enable_question_stripslashes'     => $quiz_enable_question_stripslashes,
                            'quiz_disable_answer_stripslashes'      => $quiz_disable_answer_stripslashes,
                            'answer_slug_max_id'                    => $answer_slug_max_id,
                            'answer_incorrect_matches'              => $answer_incorrect_matches,
                        );

                        $attach_url = $question_image;

                        // Add image
                        if($question_image != ""){
                            $attach_url = Quiz_Maker_Data::ays_get_images_from_url( $question_image );
                            if ( !$attach_url ) {
                                $attach_url = $question_image;
                            }
                        }

                        $answers = array();
                        foreach($answers_get as $key => $answer){

                            $answer_id = (isset($answer['id']) && absint(sanitize_text_field($answer['id'])) != 0) ? absint(sanitize_text_field($answer['id'])) : "";

                            $answer_content = (isset($answer['answer']) && $answer['answer'] != '') ? htmlspecialchars_decode($answer['answer'], ENT_HTML5) : '';
                            $question_content = Quiz_Maker_Data::convertFromCP1252( $question_content );

                            $image = (isset($answer['image']) && $answer['image'] != '') ? $answer['image'] : '';
                            $correct = (isset($answer['correct']) && $answer['correct'] != '') ? intval($answer['correct']) : 0;
                            $ordering = $key + 1;
                            $weight = (isset($answer['weight']) && $answer['weight'] != '') ? floatval($answer['weight']) : 0;

                            $placeholder = (isset($answer['placeholder']) && $answer['placeholder'] != '') ? htmlspecialchars_decode($answer['placeholder'], ENT_HTML5) : '';
                            $question_content = Quiz_Maker_Data::convertFromCP1252( $question_content );

                            $keyword = (isset($answer['keyword']) && $answer['keyword'] != '') ? sanitize_text_field($answer['keyword']) : "A";
                            $slug = (isset($answer['slug']) && $answer['slug'] != '') ? sanitize_text_field($answer['slug']) : "";

                            $answer_options = (isset($answer['options']) && $answer['options'] != '') ? sanitize_text_field($answer['options']) : "";

                            $answers[] = array(
                                'id'            => $answer_id,
                                'answer'        => $answer_content,
                                'image'         => $image,
                                'correct'       => $correct,
                                'ordering'      => $ordering,
                                'weight'        => $weight,
                                'placeholder'   => $placeholder,
                                'keyword'       => $keyword,
                                'slug'          => $slug,
                                'question_id'   => $db_question_id,
                                'options'       => $answer_options,
                            );
                        }
                        
                        $for_import[] = array(
                            'id'                        => $db_question_id,
                            'category_id'               => $category_id,
                            'tag_id'                    => $tag_ids,
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

                    // $data = array_map("utf8_encode", $data);

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

                    $question_tags = array();
                    $question_tags_file = array();
                    if(isset($data[15])){
                        $question_tags_file = explode( ',', $data[15] );
                    }

                    foreach( $question_tags_file as $tk => $tag_name ){
                        if( $tag_name == null || $tag_name == '' ){
                            continue;
                        }

                        if(in_array(strtolower($tag_name), $tags)){
                            $tag_id = array_search(strtolower($tag_name), $tags);
                            if($tag_id !== false){
                                $question_tags[] = intval($tag_id);
                            }
                        }else{
                            $wpdb->insert(
                                $question_tags_table,
                                array(
                                    'title' => $tag_name,
                                    'status' => 'published'
                                ),
                                array( '%s', '%s' )
                            );
                            $question_tags[] = $wpdb->insert_id;
                            $tags[$wpdb->insert_id] = strtolower($tag_name);
                        }
                    }

                    $category_id = $question_category;
                    $tag_ids = implode( ',', $question_tags );

                    $db_question_id = (isset($data[16]) && absint(sanitize_text_field($data[16])) > 0) ? absint(sanitize_text_field($data[16])) : "";

                    $question = htmlspecialchars_decode($data[1]);
                    $question = Quiz_Maker_Data::convertFromCP1252( $question );

                    $question_title = htmlspecialchars_decode((isset($data[14]) && $data[14] != '') ? $data[14] : '');
                    $question_title = Quiz_Maker_Data::convertFromCP1252( $question_title );

                    $question_image = (isset($data[2]) && $data[2] != '') ? $data[2] : '';

                    $question_hint = htmlspecialchars_decode($data[3]);
                    $question_hint = Quiz_Maker_Data::convertFromCP1252( $question_hint );

                    $type = (isset($data[4]) && $data[4] != '') ? strtolower($data[4]) : 'radio';
                    $published = (isset($data[5]) && $data[5] != '') ? intval($data[5]) : 1;

                    $wrong_answer_text = htmlspecialchars_decode($data[6]);
                    $wrong_answer_text = Quiz_Maker_Data::convertFromCP1252( $wrong_answer_text );

                    $right_answer_text = htmlspecialchars_decode($data[7]);
                    $right_answer_text = Quiz_Maker_Data::convertFromCP1252( $right_answer_text );

                    $explanation = htmlspecialchars_decode($data[8]);
                    $explanation = Quiz_Maker_Data::convertFromCP1252( $explanation );

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
                            if(! empty($option) && $option[0] != ''){
                                if ( $option[0] == "quiz_question_note_message" ) {
                                    $quiz_question_note_message = htmlspecialchars_decode(isset( $opt ) && $opt != '' ? $opt : '');
                                    $quiz_question_note_message = Quiz_Maker_Data::convertFromCP1252( $quiz_question_note_message );
                                    $options[$option[0]] = str_replace("quiz_question_note_message=","",$quiz_question_note_message);
                                } elseif ( $option[0] == "answer_incorrect_matches" ) {
                                    $quiz_question_answer_incorrect_matches = base64_decode(isset( $option[1] ) && $option[1] != '' ? $option[1] : '');
                                    $quiz_question_answer_incorrect_matches_arr = array();
                                    if( !empty( $quiz_question_answer_incorrect_matches ) ){
                                        $quiz_question_answer_incorrect_matches_arr = json_decode( $quiz_question_answer_incorrect_matches, true );
                                    }
                                    $options[$option[0]] = $quiz_question_answer_incorrect_matches_arr;
                                } else {
                                    $options[$option[0]] = isset( $option[1] ) && $option[1] != '' ? $option[1] : '';
                                }
                            }
                        }
                    }

                    $attach_url = $question_image;

                    // Add image
                    if($question_image != ""){
                        $attach_url = Quiz_Maker_Data::ays_get_images_from_url( $question_image );
                        if ( !$attach_url ) {
                            $attach_url = $question_image;
                        }
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
                        $answer_content = Quiz_Maker_Data::convertFromCP1252( $answer_content );

                        // if ( function_exists("mb_strlen") && version_compare(PHP_VERSION, '8.0.0', '<') ) {
                        //     if (mb_strlen($answer_content, 'UTF-8') != strlen($answer_content)) {
                        //         // For Japan language (multibyte system)
                        //         $allowed_utf_answers = "Shift-JIS,EUC-JP,JIS,SJIS,JIS-ms,eucJP-win,SJIS-win,ISO-2022-JP,
                        //                         ISO-2022-JP-MS,SJIS-mac,SJIS-Mobile#DOCOMO,SJIS-Mobile#KDDI,
                        //                         SJIS-Mobile#SOFTBANK,UTF-8-Mobile#DOCOMO,UTF-8-Mobile#KDDI-A,
                        //                         UTF-8-Mobile#KDDI-B,UTF-8-Mobile#SOFTBANK,ISO-2022-JP-MOBILE#KDDI";
                        //         // $answer_content = mb_convert_encoding($answer_content, "UTF-8", $allowed_utf_answers);
                        //     }
                        // }

                        $image = (isset($ans[3]) && $ans[3] != '') ? $ans[3] : '';
                        $correct = (isset($ans[1]) && $ans[1] != '') ? intval($ans[1]) : 0;
                        $ordering = $key + 1;
                        $weight = (isset($ans[2]) && $ans[2] != '') ? floatval($ans[2]) : 0;

                        $placeholder = (isset($ans[4]) && $ans[4] != '') ? htmlspecialchars_decode($ans[4], ENT_HTML5) : '';
                        $placeholder = htmlspecialchars_decode($placeholder, ENT_QUOTES);
                        $placeholder = Quiz_Maker_Data::convertFromCP1252( $placeholder );

                        $keyword = (isset($ans[5]) && $ans[5] != '') ? htmlspecialchars_decode($ans[5], ENT_HTML5) : 'A';

                        $answer_id = (isset($ans[6]) && $ans[6] != '' && absint($ans[6]) > 0) ? absint(sanitize_text_field($ans[6])) : "";
                        $slug = (isset($ans[7]) && $ans[7] != '') ? sanitize_text_field($ans[7]) : "";
                        $answer_options = (isset($ans[8]) && $ans[8] != '') ? htmlspecialchars_decode($ans[8]) : "";

                        $answers[] = array(
                            'answer'        => $answer_content,
                            'image'         => $image,
                            'correct'       => $correct,
                            'ordering'      => $ordering,
                            'weight'        => $weight,
                            'placeholder'   => $placeholder,
                            'keyword'       => $keyword,
                            'id'            => $answer_id,
                            'slug'          => $slug,
                            'options'       => $answer_options,
                        );
					}

                    $allowed_utf = "Shift-JIS,EUC-JP,JIS,SJIS,JIS-ms,eucJP-win,SJIS-win,ISO-2022-JP,
                                    ISO-2022-JP-MS,SJIS-mac,SJIS-Mobile#DOCOMO,SJIS-Mobile#KDDI,
                                    SJIS-Mobile#SOFTBANK,UTF-8-Mobile#DOCOMO,UTF-8-Mobile#KDDI-A,
                                    UTF-8-Mobile#KDDI-B,UTF-8-Mobile#SOFTBANK,ISO-2022-JP-MOBILE#KDDI";

                    $question_text_array = array(
                        "question"          => $question,
                        "question_title"    => $question_title,
                        "question_hint"     => $question_hint,
                        "wrong_answer_text" => $wrong_answer_text,
                        "right_answer_text" => $right_answer_text,
                        "explanation"       => $explanation,
                    );

                    if ( function_exists("mb_strlen") ) {
                        foreach($question_text_array as $q_key => $q_value){
                            $new_value = $q_value;
                            // if (version_compare(PHP_VERSION, '8.0.0', '<')) {
                            //     if (mb_strlen($q_value, 'UTF-8') != strlen($q_value)) {
                            //         // $new_value = mb_convert_encoding($q_value , "UTF-8", $allowed_utf);
                            //     }
                            // }
                            $$q_key = $new_value;
                        }
                    }

                    $for_import[] = array(
                        'id'                        => $db_question_id,
                        'category_id'               => $category_id,
                        'tag_id'                    => $tag_ids,
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
                    $questions_options = array();

                    if(isset($question['options']) && $question['options'] != ''){
                        $questions_options = json_decode($question['options'], true);
                    }

                    $bg_image = (isset($questions_options['bg_image']) && $questions_options['bg_image'] != '') ? $questions_options['bg_image'] : '';
                    $use_html = (isset($questions_options['use_html']) && $questions_options['use_html'] != '') ? $questions_options['use_html'] : 'off';
                    $enable_question_text_max_length = (isset($questions_options['enable_question_text_max_length']) && $questions_options['enable_question_text_max_length'] != '') ? $questions_options['enable_question_text_max_length'] : 'off';
                    $question_text_max_length = (isset($questions_options['question_text_max_length']) && $questions_options['question_text_max_length'] != '') ? $questions_options['question_text_max_length'] : '';
                    $question_limit_text_type = (isset($questions_options['question_limit_text_type']) && $questions_options['question_limit_text_type'] != '') ? $questions_options['question_limit_text_type'] : '';
                    $question_enable_text_message = (isset($questions_options['question_enable_text_message']) && $questions_options['question_enable_text_message'] != '') ? $questions_options['question_enable_text_message'] : 'off';

                    // Maximum length of a number field
                    $questions_options['enable_question_number_max_length'] = isset($questions_options['enable_question_number_max_length']) ? sanitize_text_field( $questions_options['enable_question_number_max_length'] ) : 'off';
                    $enable_question_number_max_length = (isset($questions_options['enable_question_number_max_length']) && sanitize_text_field( $questions_options['enable_question_number_max_length'] ) == 'on') ? 'on' : 'off';

                    // Length
                    $question_number_max_length = ( isset($questions_options['question_number_max_length']) && sanitize_text_field( $questions_options['question_number_max_length'] ) != '' ) ? intval( sanitize_text_field( $questions_options['question_number_max_length'] ) ) : '';

                    // Hide question text on the front-end
                    $questions_options['quiz_hide_question_text'] = isset($questions_options['quiz_hide_question_text']) ? sanitize_text_field( $questions_options['quiz_hide_question_text'] ) : 'off';
                    $quiz_hide_question_text = (isset($questions_options['quiz_hide_question_text']) && $questions_options['quiz_hide_question_text'] == 'on') ? 'on' : 'off';


                    // Enable maximum selection number
                    $questions_options['enable_max_selection_number'] = isset($questions_options['enable_max_selection_number']) ? sanitize_text_field( $questions_options['enable_max_selection_number'] ) : 'off';
                    $enable_max_selection_number = (isset($questions_options['enable_max_selection_number']) && sanitize_text_field( $questions_options['enable_max_selection_number'] ) == 'on') ? 'on' : 'off';

                    // Max value
                    $max_selection_number = ( isset($questions_options['max_selection_number']) && $questions_options['max_selection_number'] != '' ) ? intval( sanitize_text_field ( $questions_options['max_selection_number'] ) ) : '';

                    // Note text
                    $quiz_question_note_message = ( isset($questions_options['quiz_question_note_message']) && $questions_options['quiz_question_note_message'] != '' ) ? wp_kses_post( wp_unslash($questions_options['quiz_question_note_message']) ) : '';
                    if ( $quiz_question_note_message != "" ) {
                        $quiz_question_note_message = htmlspecialchars_decode( stripslashes( str_replace( "\n", "", $quiz_question_note_message ) ) );
                    }

                    // Enable case sensitive text
                    $enable_case_sensitive_text = (isset($questions_options['enable_case_sensitive_text']) && sanitize_text_field( $questions_options['enable_case_sensitive_text'] ) == 'on') ? 'on' : 'off';

                    // Enable minimum selection number
                    $questions_options['enable_min_selection_number'] = isset($questions_options['enable_min_selection_number']) ? sanitize_text_field( $questions_options['enable_min_selection_number'] ) : 'off';
                    $enable_min_selection_number = (isset($questions_options['enable_min_selection_number']) && sanitize_text_field( $questions_options['enable_min_selection_number'] ) == 'on') ? 'on' : 'off';

                    // Min value
                    $min_selection_number = ( isset($questions_options['min_selection_number']) && $questions_options['min_selection_number'] != '' ) ? intval( sanitize_text_field ( $questions_options['min_selection_number'] ) ) : '';

                    // Minimum length of a number field
                    $questions_options['enable_question_number_min_length'] = isset($questions_options['enable_question_number_min_length']) ? sanitize_text_field( $questions_options['enable_question_number_min_length'] ) : 'off';
                    $enable_question_number_min_length = (isset($questions_options['enable_question_number_min_length']) && sanitize_text_field( $questions_options['enable_question_number_min_length'] ) == 'on') ? 'on' : 'off';

                    // Length
                    $question_number_min_length = ( isset($questions_options['question_number_min_length']) && sanitize_text_field( $questions_options['question_number_min_length'] ) != '' ) ? intval( sanitize_text_field( $questions_options['question_number_min_length'] ) ) : '';

                    // Show error message
                    $questions_options['enable_question_number_error_message'] = isset($questions_options['enable_question_number_error_message']) ? sanitize_text_field( $questions_options['enable_question_number_error_message'] ) : 'off';
                    $enable_question_number_error_message = (isset($questions_options['enable_question_number_error_message']) && sanitize_text_field( $questions_options['enable_question_number_error_message'] ) == 'on') ? 'on' : 'off';

                    // Message
                    $question_number_error_message = ( isset($questions_options['question_number_error_message']) && sanitize_text_field( $questions_options['question_number_error_message'] ) != '' ) ? stripslashes( sanitize_text_field( $questions_options['question_number_error_message'] ) ) : '';

                    // Enable strip slashes for questions
                    $questions_options['quiz_enable_question_stripslashes'] = isset($questions_options['quiz_enable_question_stripslashes']) ? sanitize_text_field( $questions_options['quiz_enable_question_stripslashes'] ) : 'off';
                    $quiz_enable_question_stripslashes = (isset($questions_options['quiz_enable_question_stripslashes']) && sanitize_text_field( $questions_options['quiz_enable_question_stripslashes'] ) == 'on') ? 'on' : 'off';

                    // Disable strip slashes for answers
                    $questions_options['quiz_disable_answer_stripslashes'] = isset($questions_options['quiz_disable_answer_stripslashes']) ? sanitize_text_field( $questions_options['quiz_disable_answer_stripslashes'] ) : 'off';
                    $quiz_disable_answer_stripslashes = (isset($questions_options['quiz_disable_answer_stripslashes']) && sanitize_text_field( $questions_options['quiz_disable_answer_stripslashes'] ) == 'on') ? 'on' : 'off';

                    // Answer slug max ID
                    $answer_slug_max_id = ( isset($questions_options['answer_slug_max_id']) && sanitize_text_field( $questions_options['answer_slug_max_id'] ) != '' ) ? absint( sanitize_text_field ( $questions_options['answer_slug_max_id'] ) ) : 1;

                    // Matching question type incorrect answers/matches
                    $answer_incorrect_matches = (isset($questions_options['answer_incorrect_matches']) && !empty($questions_options['answer_incorrect_matches'])) ? $questions_options['answer_incorrect_matches'] : array();

                    $options = array(
                        'author'                                => $author,
                        'bg_image'                              => $bg_image,
                        'use_html'                              => $use_html,
                        'enable_question_text_max_length'       => $enable_question_text_max_length,
                        'question_text_max_length'              => $question_text_max_length,
                        'question_limit_text_type'              => $question_limit_text_type,
                        'question_enable_text_message'          => $question_enable_text_message,
                        'enable_question_number_max_length'     => $enable_question_number_max_length,
                        'question_number_max_length'            => $question_number_max_length,
                        'quiz_hide_question_text'               => $quiz_hide_question_text,
                        'enable_max_selection_number'           => $enable_max_selection_number,
                        'max_selection_number'                  => $max_selection_number,
                        'quiz_question_note_message'            => $quiz_question_note_message,
                        'enable_case_sensitive_text'            => $enable_case_sensitive_text,
                        'enable_min_selection_number'           => $enable_min_selection_number,
                        'min_selection_number'                  => $min_selection_number,
                        'enable_question_number_min_length'     => $enable_question_number_min_length,
                        'question_number_min_length'            => $question_number_min_length,
                        'enable_question_number_error_message'  => $enable_question_number_error_message,
                        'question_number_error_message'         => $question_number_error_message,
                        'quiz_enable_question_stripslashes'     => $quiz_enable_question_stripslashes,
                        'quiz_disable_answer_stripslashes'      => $quiz_disable_answer_stripslashes,
                        'answer_slug_max_id'                    => $answer_slug_max_id,
                        'answer_incorrect_matches'              => $answer_incorrect_matches,
                    );

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

                    $question_tags = array();
                    $question_tags_file = array();
                    if(isset($question['tag_title'])){
                        $question_tags_file = explode( ',', $question['tag_title'] );
                    }

                    foreach( $question_tags_file as $tk => $tag_name ){
                        if( $tag_name == null || $tag_name == '' ){
                            continue;
                        }

                        if(in_array(strtolower($tag_name), $tags)){
                            $tag_id = array_search(strtolower($tag_name), $tags);
                            if($tag_id !== false){
                                $question_tags[] = intval($tag_id);
                            }
                        }else{
                            $wpdb->insert(
                                $question_tags_table,
                                array(
                                    'title' => $tag_name,
                                    'status' => 'published'
                                ),
                                array( '%s', '%s' )
                            );
                            $question_tags[] = $wpdb->insert_id;
                            $tags[$wpdb->insert_id] = strtolower($tag_name);
                        }
                    }

                    $category_id = $question_category;
                    $tag_ids = implode( ',', $question_tags );

                    $db_question_id = (isset($question['id']) && $question['id'] != '' && absint($question['id']) > 0) ? absint($question['id']) : "";

                    $question_content = htmlspecialchars_decode($question['question'], ENT_HTML5);
                    $question_content = Quiz_Maker_Data::convertFromCP1252( $question_content );

                    $question_title = htmlspecialchars_decode(isset($question['question_title']) && $question['question_title'] != '' ? $question['question_title'] : '', ENT_HTML5);
                    $question_title = Quiz_Maker_Data::convertFromCP1252( $question_title );

                    $question_image = (isset($question['question_image']) && $question['question_image'] != '') ? $question['question_image'] : '';

                    $question_hint = htmlspecialchars_decode($question['question_hint'], ENT_HTML5);
                    $question_hint = Quiz_Maker_Data::convertFromCP1252( $question_hint );

                    $type = (isset($question['type']) && $question['type'] != '') ? strtolower($question['type']) : 'radio';
                    $published = (isset($question['published']) && $question['published'] != '') ? intval($question['published']) : 1;

                    $wrong_answer_text = htmlspecialchars_decode($question['wrong_answer_text'], ENT_HTML5);
                    $wrong_answer_text = Quiz_Maker_Data::convertFromCP1252( $wrong_answer_text );

                    $right_answer_text = htmlspecialchars_decode($question['right_answer_text'], ENT_HTML5);
                    $right_answer_text = Quiz_Maker_Data::convertFromCP1252( $right_answer_text );

                    $explanation = htmlspecialchars_decode($question['explanation'], ENT_HTML5);
                    $explanation = Quiz_Maker_Data::convertFromCP1252( $explanation );

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
                        if ( !$attach_url ) {
                            $attach_url = $question_image;
                        }
                    }

                    $answers = array();
                    foreach($answers_get as $key => $answer){

                        $answer_id = (isset($answer['id']) && absint($answer['id']) > 0) ? absint(sanitize_text_field($answer['id'])) : "";

                        $answer_content = (isset($answer['answer']) && $answer['answer'] != '') ? htmlspecialchars_decode($answer['answer'], ENT_HTML5) : '';
                        $answer_content = Quiz_Maker_Data::convertFromCP1252( $answer_content );

                        $image = (isset($answer['image']) && $answer['image'] != '') ? $answer['image'] : '';
                        $correct = (isset($answer['correct']) && $answer['correct'] != '') ? intval($answer['correct']) : 0;
                        $ordering = $key + 1;
                        $weight = (isset($answer['weight']) && $answer['weight'] != '') ? floatval($answer['weight']) : 0;

                        $placeholder = (isset($answer['placeholder']) && $answer['placeholder'] != '') ? htmlspecialchars_decode($answer['placeholder'], ENT_HTML5) : '';
                        $placeholder = Quiz_Maker_Data::convertFromCP1252( $placeholder );

                        $keyword = (isset($answer['keyword']) && $answer['keyword'] != '') ? sanitize_text_field($answer['keyword']) : "A";
                        $slug = (isset($answer['slug']) && $answer['slug'] != '') ? sanitize_text_field($answer['slug']) : "";
                        $answer_options = (isset($answer['options']) && $answer['options'] != '') ? sanitize_text_field($answer['options']) : "";

                        $answers[] = array(
                            'id'            => $answer_id,
                            'answer'        => $answer_content,
                            'image'         => $image,
                            'correct'       => $correct,
                            'ordering'      => $ordering,
                            'weight'        => $weight,
                            'placeholder'   => $placeholder,
                            'keyword'       => $keyword,
                            'slug'          => $slug,
                            'options'       => $answer_options,
                        );
                    }

                    $for_import[] = array(
                        'id'                        => $db_question_id,
                        'category_id'               => $category_id,
                        'tag_id'                    => $tag_ids,
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

            $db_question_fields = array(
                'category_id'               => $question['category_id'],
                'tag_id'                    => $question['tag_id'],
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
            );

            $db_question_fields_types = array(
                '%d', //category_id
                '%s', //tag_id
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
            );

            $db_question_id = (isset($question['id']) && $question['id'] != "" && $question['id'] > 0) ? absint($question['id']) : "";

            if ( !$quiz_update_existing_questions ) {
                $db_question_id = "";
            }

            if(is_null($db_question_id) || $db_question_id == ""){
                $quest_res = $wpdb->insert(
                    $questions_table,
                    $db_question_fields,
                    $db_question_fields_types
                );
                $question_id = $wpdb->insert_id;
            }else{
                $quest_res = $wpdb->update(
                    $questions_table,
                    $db_question_fields,
                    array( 'id' => absint(sanitize_text_field($db_question_id)) ),
                    $db_question_fields_types,
                    array( '%d' )
                );
                $question_id = $db_question_id;

                if ($quest_res == 0 && $wpdb->last_error != "") {
                    $quest_res = $wpdb->insert(
                        $questions_table,
                        $db_question_fields,
                        $db_question_fields_types
                    );
                    $question_id = $wpdb->insert_id;
                }

            }

            $ordering = 1;
            $answer_res_success = 0;
            $answer_res_fail = 0;
            foreach ( $question['answers'] as &$answer ) {

                $db_answer_fields = array(
                    'question_id'   => $question_id,
                    'answer'        => $answer['answer'],
                    'image'         => $answer['image'],
                    'correct'       => $answer['correct'],
                    'ordering'      => $answer['ordering'],
                    'weight'        => $answer['weight'],
                    'placeholder'   => $answer['placeholder'],
                    'keyword'       => $answer['keyword'],
                    'slug'          => $answer['slug'],
                    'options'       => $answer['options'],
                );

                $db_answer_fields_types = array(
                    '%d', // question_id
                    '%s', // answer
                    '%s', // image
                    '%d', // correct
                    '%d', // ordering
                    '%f', // weight
                    '%s', // placeholder
                    '%s', // keyword
                    '%s', // slug
                    '%s', // options
                );

                $db_answer_id = (isset($answer['id']) && $answer['id'] != "" && $answer['id'] > 0) ? absint($answer['id']) : "";

                if ( !$quiz_update_existing_questions ) {
                    $db_answer_id = "";
                }

                if(is_null($db_answer_id) || $db_answer_id == ""){
                    $result = $wpdb->insert(
                        $answers_table,
                        $db_answer_fields,
                        $db_answer_fields_types
                    );
                }else{
                    $result = $wpdb->update(
                        $answers_table,
                        $db_answer_fields,
                        array( 'id' => absint(sanitize_text_field($db_answer_id)) ),
                        $db_answer_fields_types,
                        array( '%d' )
                    );

                    if ( $result == 0 && $wpdb->last_error != "" ) {
                        $result = $wpdb->insert(
                            $answers_table,
                            $db_answer_fields,
                            $db_answer_fields_types
                        );
                    }
                }

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
        $question_explanation = '';
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
                            $q_e = htmlspecialchars_decode( preg_replace('/_x([0-9a-fA-F]{4})_/', '&#x$1;', $sh_val), ENT_HTML5);
                            $question_explanation = htmlspecialchars_decode($q_e, ENT_HTML5);
                            break;
                        case 'D':
                            $correct_answer = $sh_val;
                            break;
                        default:
                            if ( is_bool( $sh_val ) ) {
                                switch ( $sh_val ) {
                                    case 1:
                                        $answers[] = __("True", $this->plugin_name);
                                        break;
                                    case 0:
                                    default:
                                        $answers[] = __("False", $this->plugin_name);
                                        break;
                                }
                            }elseif ( is_float($sh_val) && $sh_val == 0 ) {
                                $answers[] = $sh_val."";
                            } elseif(!empty($sh_val)){
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
                        'explanation'      => $question_explanation,
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
                        '%s', // explanation
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

        // Enable maximum selection number
        $question_options['enable_max_selection_number'] = isset($question_options['enable_max_selection_number']) ? sanitize_text_field( $question_options['enable_max_selection_number'] ) : 'off';
        $enable_max_selection_number = (isset($question_options['enable_max_selection_number']) && sanitize_text_field( $question_options['enable_max_selection_number'] ) == 'on') ? 'on' : 'off';

        // Max value
        $max_selection_number = ( isset($question_options['max_selection_number']) && $question_options['max_selection_number'] != '' ) ? intval( sanitize_text_field ( $question_options['max_selection_number'] ) ) : '';

        // Note text
        $quiz_question_note_message = ( isset($question_options['quiz_question_note_message']) && $question_options['quiz_question_note_message'] != '' ) ? wp_kses_post( $question_options['quiz_question_note_message'] ) : '';

        // Enable case sensitive text
        $enable_case_sensitive_text = (isset($question_options['enable_case_sensitive_text']) && sanitize_text_field( $question_options['enable_case_sensitive_text'] ) == 'on') ? 'on' : 'off';

        // Enable minimum selection number
        $question_options['enable_min_selection_number'] = isset($question_options['enable_min_selection_number']) ? sanitize_text_field( $question_options['enable_min_selection_number'] ) : 'off';
        $enable_min_selection_number = (isset($question_options['enable_min_selection_number']) && sanitize_text_field( $question_options['enable_min_selection_number'] ) == 'on') ? 'on' : 'off';

        // Min value
        $min_selection_number = ( isset($question_options['min_selection_number']) && $question_options['min_selection_number'] != '' ) ? intval( sanitize_text_field ( $question_options['min_selection_number'] ) ) : '';

        // Minimum length of a number field
        $question_options['enable_question_number_min_length'] = isset($question_options['enable_question_number_min_length']) ? sanitize_text_field( $question_options['enable_question_number_min_length'] ) : 'off';
        $enable_question_number_min_length = (isset($question_options['enable_question_number_min_length']) && sanitize_text_field( $question_options['enable_question_number_min_length'] ) == 'on') ? 'on' : 'off';

        // Length
        $question_number_min_length = ( isset($question_options['question_number_min_length']) && sanitize_text_field( $question_options['question_number_min_length'] ) != '' ) ? intval( sanitize_text_field( $question_options['question_number_min_length'] ) ) : '';

        // Show error message
        $question_options['enable_question_number_error_message'] = isset($question_options['enable_question_number_error_message']) ? sanitize_text_field( $question_options['enable_question_number_error_message'] ) : 'off';
        $enable_question_number_error_message = (isset($question_options['enable_question_number_error_message']) && sanitize_text_field( $question_options['enable_question_number_error_message'] ) == 'on') ? 'on' : 'off';

        // Message
        $question_number_error_message = ( isset($question_options['question_number_error_message']) && sanitize_text_field( $question_options['question_number_error_message'] ) != '' ) ? stripslashes( sanitize_text_field( $question_options['question_number_error_message'] ) ) : '';

        // Enable strip slashes for questions
        $question_options['quiz_enable_question_stripslashes'] = isset($question_options['quiz_enable_question_stripslashes']) ? sanitize_text_field( $question_options['quiz_enable_question_stripslashes'] ) : 'off';
        $quiz_enable_question_stripslashes = (isset($question_options['quiz_enable_question_stripslashes']) && sanitize_text_field( $question_options['quiz_enable_question_stripslashes'] ) == 'on') ? 'on' : 'off';

        // Disable strip slashes for answers
        $question_options['quiz_disable_answer_stripslashes'] = isset($question_options['quiz_disable_answer_stripslashes']) ? sanitize_text_field( $question_options['quiz_disable_answer_stripslashes'] ) : 'off';
        $quiz_disable_answer_stripslashes = (isset($question_options['quiz_disable_answer_stripslashes']) && sanitize_text_field( $question_options['quiz_disable_answer_stripslashes'] ) == 'on') ? 'on' : 'off';

        $options = isset($questionDup['options']) ? json_decode($questionDup['options'], true) : array(
            'use_html' => $use_html,
            'enable_question_text_max_length'       => $enable_question_text_max_length,
            'question_text_max_length'              => $question_text_max_length,
            'question_limit_text_type'              => $question_limit_text_type,
            'question_enable_text_message'          => $question_enable_text_message,
            'enable_question_number_max_length'     => $enable_question_number_max_length,
            'question_number_max_length'            => $question_number_max_length,
            'quiz_hide_question_text'               => $quiz_hide_question_text,
            'enable_max_selection_number'           => $enable_max_selection_number,
            'max_selection_number'                  => $max_selection_number,
            'quiz_question_note_message'            => $quiz_question_note_message,
            'enable_case_sensitive_text'            => $enable_case_sensitive_text,
            'enable_min_selection_number'           => $enable_min_selection_number,
            'min_selection_number'                  => $min_selection_number,
            'enable_question_number_min_length'     => $enable_question_number_min_length,
            'question_number_min_length'            => $question_number_min_length,
            'enable_question_number_error_message'  => $enable_question_number_error_message,
            'question_number_error_message'         => $question_number_error_message,
            'quiz_enable_question_stripslashes'     => $quiz_enable_question_stripslashes,
            'quiz_disable_answer_stripslashes'      => $quiz_disable_answer_stripslashes,
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

            $answer_slag = isset( $answer['slug'] ) && $answer['slug'] != "" ? $answer['slug'] : "";
            $answer_options = isset( $answer['options'] ) && $answer['options'] != "" ? $answer['options'] : "";

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
                    'placeholder'   => $answer['placeholder'],
                    'slug'          => $answer_slag,
                    'options'       => $answer_options,
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
                    '%s', // slug
                    '%s', // options
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
        if ( Quiz_Maker_Data::quiz_maker_capabilities_for_editing() ) {
            if(! empty( $_GET['filterbyuser'] ) && $_GET['filterbyuser'] > 0){
                $author_id = intval( sanitize_text_field( $_GET['filterbyuser'] ) );
                $filter[] = ' author_id = '.$author_id.'';
            }
        }

        if( isset( $_GET['filterbytags'] ) && ! empty( $_GET['filterbytags'] )){

            $tag_ids = explode(',', sanitize_text_field( $_GET['filterbytags'] ));
            $tags_sql = array();
            if(!empty($tag_ids) && $tag_ids[0] != ''){
                $numItems = count($tag_ids);
                $flag_i = 0;
                foreach($tag_ids as $key => $tag_id){
                    $tag_id = absint( sanitize_text_field( $tag_id ) );
                    $start_sql = "";
                    $close_sql = "";
                    if ( $tag_id > 0 ) {
                        if ( $numItems > 1 ) {
                            if ( $flag_i == 0 ) {
                                $start_sql = " ( ";
                            }

                            if(++$flag_i === $numItems) {
                                $close_sql = " ) ";
                            }
                        }
                        $tags_sql[] = $start_sql . ' FIND_IN_SET('.$tag_id.', tag_id ) ' . $close_sql;
                    }
                }
            }
            if( !empty( $tags_sql ) ){
                $filter[] = implode( ' OR ', $tags_sql );
            }
        }

        if( isset( $_REQUEST['fstatus'] ) ){
            $fstatus = $_REQUEST['fstatus'];
            if($fstatus !== null){
                $filter[] = " published = ".$fstatus." ";
            }
        } else{
            $filter[] = " published != 2 ";
        }
   

        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
        if( $search ){
            $filter[] = sprintf(" ( question LIKE '%%%s%%' OR question_title LIKE '%%%s%%' ) ", esc_sql( $wpdb->esc_like( $search ) ) , esc_sql( $wpdb->esc_like( $search ) )  );
        }

        if( isset($_REQUEST['type']) ){
            $filter[] = " type ='". sanitize_text_field( $_REQUEST['type'] ) ."' ";
        }

        if(count($filter) !== 0){
            $sql .= " WHERE ".implode(" AND ", $filter);
        }

        return $wpdb->get_var( $sql );
    }
    
    public static function all_record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_questions WHERE 1=1 AND published != 2";

        if( isset( $_GET['filterby'] ) && intval($_GET['filterby']) > 0){
            $cat_id = intval($_GET['filterby']);
            $sql .= ' AND category_id = '.$cat_id.' ';
        }

        if ( Quiz_Maker_Data::quiz_maker_capabilities_for_editing() ) {
            if(! empty( $_GET['filterbyuser'] ) && $_GET['filterbyuser'] > 0){
                $author_id = intval( sanitize_text_field( $_GET['filterbyuser'] ) );
                $sql .= ' AND author_id = '.$author_id.'';
            }
        }

        if( isset( $_GET['filterbytags'] ) && ! empty( $_GET['filterbytags'] ) ){

            $tag_ids = explode(',', sanitize_text_field( $_REQUEST['filterbytags'] ));
            $tags_sql = array();
            if(!empty($tag_ids) && $tag_ids[0] != ''){
                $numItems = count($tag_ids);
                $flag_i = 0;
                foreach($tag_ids as $key => $tag_id){
                    $tag_id = absint( sanitize_text_field( $tag_id ) );
                    $start_sql = "";
                    $close_sql = "";
                    if ( $tag_id > 0 ) {
                        if ( $numItems > 1 ) {
                            if ( $flag_i == 0 ) {
                                $start_sql = " ( ";
                            }

                            if(++$flag_i === $numItems) {
                                $close_sql = " ) ";
                            }
                        }
                        $tags_sql[] = $start_sql . ' FIND_IN_SET('.$tag_id.', tag_id ) ' . $close_sql;
                    }
                }
            }
            if( !empty( $tags_sql ) ){
                $sql .= " AND " . implode( ' OR ', $tags_sql );
            }
        }

        if( isset($_REQUEST['type']) ){
            $sql .= " AND type ='". sanitize_text_field( $_REQUEST['type'] ) ."' ";
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

        if ( Quiz_Maker_Data::quiz_maker_capabilities_for_editing() ) {
            if(! empty( $_GET['filterbyuser'] ) && $_GET['filterbyuser'] > 0){
                $author_id = intval( sanitize_text_field( $_GET['filterbyuser'] ) );
                $sql .= ' AND author_id = '.$author_id.'';
            }
        }

        if( isset( $_GET['filterbytags'] ) && ! empty( $_GET['filterbytags'] ) ){

            $tag_ids = explode(',', sanitize_text_field( $_REQUEST['filterbytags'] ));
            $tags_sql = array();
            if(!empty($tag_ids) && $tag_ids[0] != ''){
                $numItems = count($tag_ids);
                $flag_i = 0;
                foreach($tag_ids as $key => $tag_id){
                    $tag_id = absint( sanitize_text_field( $tag_id ) );
                    $start_sql = "";
                    $close_sql = "";
                    if ( $tag_id > 0 ) {
                        if ( $numItems > 1 ) {
                            if ( $flag_i == 0 ) {
                                $start_sql = " ( ";
                            }

                            if(++$flag_i === $numItems) {
                                $close_sql = " ) ";
                            }
                        }
                        $tags_sql[] = $start_sql . ' FIND_IN_SET('.$tag_id.', tag_id ) ' . $close_sql;
                    }
                }
            }
            if( !empty( $tags_sql ) ){
                $sql .= " AND " . implode( ' OR ', $tags_sql );
            }
        }

        if( isset($_REQUEST['type']) ){
            $sql .= " AND type ='". sanitize_text_field( $_REQUEST['type'] ) ."' ";
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

        if ( Quiz_Maker_Data::quiz_maker_capabilities_for_editing() ) {
            if(! empty( $_GET['filterbyuser'] ) && $_GET['filterbyuser'] > 0){
                $author_id = intval( sanitize_text_field( $_GET['filterbyuser'] ) );
                $sql .= ' AND author_id = '.$author_id.'';
            }
        }

        if( isset( $_GET['filterbytags'] ) && ! empty( $_GET['filterbytags'] ) ){

            $tag_ids = explode(',', sanitize_text_field( $_REQUEST['filterbytags'] ));
            $tags_sql = array();
            if(!empty($tag_ids) && $tag_ids[0] != ''){
                $numItems = count($tag_ids);
                $flag_i = 0;
                foreach($tag_ids as $key => $tag_id){
                    $tag_id = absint( sanitize_text_field( $tag_id ) );
                    $start_sql = "";
                    $close_sql = "";
                    if ( $tag_id > 0 ) {
                        if ( $numItems > 1 ) {
                            if ( $flag_i == 0 ) {
                                $start_sql = " ( ";
                            }

                            if(++$flag_i === $numItems) {
                                $close_sql = " ) ";
                            }
                        }
                        $tags_sql[] = $start_sql . ' FIND_IN_SET('.$tag_id.', tag_id ) ' . $close_sql;
                    }
                }
            }
            if( !empty( $tags_sql ) ){
                $sql .= " AND " . implode( ' OR ', $tags_sql );
            }
        }

        if( isset($_REQUEST['type']) ){
            $sql .= " AND type ='". sanitize_text_field( $_REQUEST['type'] ) ."' ";
        }
        
        return $wpdb->get_var( $sql );
    }

    public static function trash_questions_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_questions WHERE published=2";

        if( isset( $_GET['filterby'] ) && intval($_GET['filterby']) > 0){
            $cat_id = intval($_GET['filterby']);
            $sql .= ' AND category_id = '.$cat_id.' ';
        }

        if ( Quiz_Maker_Data::quiz_maker_capabilities_for_editing() ) {
            if(! empty( $_GET['filterbyuser'] ) && $_GET['filterbyuser'] > 0){
                $author_id = intval( sanitize_text_field( $_GET['filterbyuser'] ) );
                $sql .= ' AND author_id = '.$author_id.'';
            }
        }

        if( isset( $_GET['filterbytags'] ) && ! empty( $_GET['filterbytags'] ) ){

            $tag_ids = explode(',', sanitize_text_field( $_REQUEST['filterbytags'] ));
            $tags_sql = array();
            if(!empty($tag_ids) && $tag_ids[0] != ''){
                $numItems = count($tag_ids);
                $flag_i = 0;
                foreach($tag_ids as $key => $tag_id){
                    $tag_id = absint( sanitize_text_field( $tag_id ) );
                    $start_sql = "";
                    $close_sql = "";
                    if ( $tag_id > 0 ) {
                        if ( $numItems > 1 ) {
                            if ( $flag_i == 0 ) {
                                $start_sql = " ( ";
                            }

                            if(++$flag_i === $numItems) {
                                $close_sql = " ) ";
                            }
                        }
                        $tags_sql[] = $start_sql . ' FIND_IN_SET('.$tag_id.', tag_id ) ' . $close_sql;
                    }
                }
            }
            if( !empty( $tags_sql ) ){
                $sql .= " AND " . implode( ' OR ', $tags_sql );
            }
        }

        if( isset($_REQUEST['type']) ){
            $sql .= " AND type ='". sanitize_text_field( $_REQUEST['type'] ) ."' ";
        }

        return $wpdb->get_var( $sql );
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
        if( isset( $_GET['status'] ) && ($_GET['status'] == 'deleted' || $_GET['status'] == 'restored')){
            $url = remove_query_arg( array('fstatus', 'status', '_wpnonce') );
            $url = esc_url_raw( $url );
            wp_redirect( $url );
        }
        else{
            echo __( 'There are no questions yet.', $this->plugin_name );
        }
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
            case 'tag_id':
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
        $current_user = get_current_user_id();
        $author_id = intval( $item['author_id'] );

        if( $current_user == $author_id ){
            return sprintf(
                '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
            );
        }

        if( ! $this->current_user_can_edit ){
            return '';
        }

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

        $delete_nonce  = wp_create_nonce( $this->plugin_name . '-delete-question' );
        $trash_nonce   = wp_create_nonce( $this->plugin_name . '-trash-question' );
        $restore_nonce = wp_create_nonce( $this->plugin_name . '-restore-question' );

        $fstatus = '';
        if( isset( $_GET['fstatus'] ) && $_GET['fstatus'] != '' ){
            $fstatus = '&fstatus=' . sanitize_text_field( $_GET['fstatus'] );
        }

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
                $question_title = stripslashes( $item['question_title'] );
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

        $question_title = esc_attr( $question_title );

        $question_title = Quiz_Maker_Admin::ays_restriction_string("word",$question_title, $question_title_length);
        
        $url = remove_query_arg( array('status') );
        $url_args = array(
            "page"    => esc_attr( $_REQUEST['page'] ),
            "question"    => absint( $item['id'] ),
        );
        $url_args['action'] = "edit";

        if( isset( $_GET['paged'] ) && sanitize_text_field( $_GET['paged'] ) != '' ){
            $url_args['paged'] = $current_page;
        }

        $url = add_query_arg( $url_args, $url );

        $title = sprintf( '<a href="%s" title="%s">%s</a>', $url, $q, $question_title );

        $actions = array();

        if( $item['published'] == 2 ) {
            $title              = sprintf( '<strong><a title="%s">%s</a></strong>', $q, $question_title );

            $actions['restore'] = sprintf( '<a href="?page=%s&action=%s&question=%d&_wpnonce=%s' . $fstatus . '">' . __( 'Restore', $this->plugin_name ) . '</a>', esc_attr( $_REQUEST['page'] ), 'restore', absint( $item['id'] ), $restore_nonce );
            $actions['delete']  = sprintf( '<a class="ays_confirm_del" data-message="%s" href="?page=%s&action=%s&question=%s&_wpnonce=%s' . $fstatus . '">' . __( 'Delete Permanently', $this->plugin_name ) . '</a>', $question_title, esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce );
        } else {
            if( $owner ){
                $actions['edit'] = sprintf( '<a href="%s">'. __('Edit', $this->plugin_name) .'</a>', $url );
            }else{
                $actions['edit'] = sprintf( '<a href="%s">'. __('View', $this->plugin_name) .'</a>', $url );
            }

            $url_args['action']   = "duplicate";
            $url                  = add_query_arg( $url_args, $url );
            $actions['duplicate'] = sprintf( '<a href="%s">' . __( 'Duplicate', $this->plugin_name ) . '</a>', $url );

            if ( $owner ) {
                $url_args['action']   = "trash";
                $url_args['_wpnonce'] = $trash_nonce;
                $url                  = add_query_arg( $url_args, $url );
                $actions['trash']     = sprintf( '<a href="%s">' . __( 'Move to trash', $this->plugin_name ) . '</a>', $url );
            }
        }

        return $title . $this->row_actions( $actions );
    }

    function column_category_id( $item ) {
        global $wpdb;

        $question_categories_table = esc_sql( $wpdb->prefix . "aysquiz_categories" );

        $category_id = ( isset( $item['category_id'] ) && $item['category_id'] != "" ) ? absint( sanitize_text_field( $item['category_id'] ) ) : 0;

        $sql = "SELECT * FROM {$question_categories_table} WHERE id=" . $category_id;

        $result = $wpdb->get_row($sql, 'ARRAY_A');

        $results = "";
        if($result !== null){

            $category_title = ( isset( $result['title'] ) && $result['title'] != "" ) ? sanitize_text_field( $result['title'] ) : "";

            if ( $category_title != "" ) {
                $results = sprintf( '<a href="?page=%s&action=edit&question_category=%d" target="_blank">%s</a>', 'quiz-maker-question-categories', $category_id, $category_title);
            }
        }else{
            $results = "";
        }

        return $results;
    }

    function column_tag_id( $item ) {
        global $wpdb;

        $question_tagss_table = esc_sql( $wpdb->prefix . "aysquiz_question_tags" );
        
        $tag_ids = ( isset( $item['tag_id'] ) && $item['tag_id'] != "" ) ? sanitize_text_field( $item['tag_id'] ) : 0;
        $sql = "SELECT title FROM {$question_tagss_table} WHERE id IN ({$tag_ids})";
        
        $results = $wpdb->get_results($sql, 'ARRAY_A');
        
        $tag_results = array();
        
        foreach( $results as $key => $result ){
            $tag_results[] = ( isset( $result['title'] ) && $result['title'] != '' ) ? stripslashes( sanitize_text_field( $result['title']  ) ) : '';
        }

        $tags = implode(',', $tag_results);

        return $tags;
    }

    function column_published( $item ) {
        $status = (isset( $item['published'] ) && $item['published'] != '') ? absint( sanitize_text_field( $item['published'] ) ) : '';

        $status_html = '';

        switch( $status ) {
            case 1:
                $status_html = '<span class="ays-publish-status"><i class="ays_fa ays_fa_check_square_o" aria-hidden="true"></i>'. __('Published',$this->plugin_name) . '</span>';
                break;
            case 0:
                $status_html = '<span class="ays-publish-status"><i class="ays_fa ays_fa_square_o" aria-hidden="true"></i>'. __('Unpublished',$this->plugin_name) . '</span>';
                break;
             default:
                $status_html = '<span class="ays-publish-status"><i class="ays_fa ays_fa_square_o" aria-hidden="true"></i>'. __('Unpublished',$this->plugin_name) . '</span>';
                break;
        }

        return $status_html;
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
        if( $author && $author !== null && !empty( $author ) ){
            $text .= $author->data->display_name;
        }
        return $text;
    }

    function column_type( $item ) {        
        $query_str = Quiz_Maker_Admin::ays_query_string(array("status", "type"));

        $question_type = $item['type'];
        switch ( $item['type'] ) {
            case 'short_text':
                $question_type = 'short text';
                break;
            case 'true_or_false':
                $question_type = 'true/false';
                break;
            case 'fill_in_blank':
                $question_type = 'Fill in blank';
                break;
            default:
                $question_type = $item['type'];
                break;
        }

        $type = "<a href='?".$query_str."&type=".$item['type']."' >".ucfirst( $question_type )."</a>";
        return $type;
    }
    
    function column_used( $item ) {
        $used = __( "False", $this->plugin_name );
        if( in_array($item["id"], $this->used_questions) ){
            $used = __( "True", $this->plugin_name );
        }
        return $used;
    }

    function column_items_count( $item ) {
        global $wpdb;
        $result = '';
        if ( isset( $item['id'] ) && absint( $item['id'] ) > 0 && ! is_null( sanitize_text_field( $item['id'] ) ) ) {
            $id = absint( esc_sql( $item['id'] ) );

            $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_answers WHERE question_id = " . $id;

            $result = $wpdb->get_var($sql);
        }

        return "<p style='text-align:center;font-size:14px;'>" . $result . "</p>";
    }

    function column_question_image( $item ) {
        global $wpdb;
        $current_page = $this->get_pagenum();

        $question_image = (isset( $item['question_image'] ) && $item['question_image'] != '') ? esc_url( $item['question_image'] ) : '';

        $image_html     = array();
        $edit_page_url  = '';

        if($question_image != ''){

            if ( isset( $item['id'] ) && absint( $item['id'] ) > 0 ) {
                $edit_page_url = sprintf( 'href="?page=%s&paged=%d&action=%s&question=%d"', esc_attr( $_REQUEST['page'] ), $current_page, 'edit', absint( $item['id'] ) );
            }

            $question_image_url = $question_image;
            $this_site_path = trim( get_site_url(), "https:" );
            if( strpos( trim( $question_image_url, "https:" ), $this_site_path ) !== false ){
                $query = "SELECT * FROM `" . $wpdb->prefix . "posts` WHERE `post_type` = 'attachment' AND `guid` = '" . $question_image_url . "'";
                $result_img =  $wpdb->get_results( $query, "ARRAY_A" );
                if( ! empty( $result_img ) ){
                    $url_img = wp_get_attachment_image_src( $result_img[0]['ID'], 'thumbnail' );
                    if( $url_img !== false ){
                        $question_image_url = $url_img[0];
                    }
                }
            }

            $image_html[] = '<div class="ays-question-image-list-table-column">';
                $image_html[] = '<a '. $edit_page_url .' class="ays-question-image-list-table-link-column">';
                    $image_html[] = '<img src="'. $question_image_url .'" class="ays-question-image-list-table-img-column">';
                $image_html[] = '</a>';
            $image_html[] = '</div>';
        }

        $image_html = implode('', $image_html);

        return $image_html;
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'       => '<input type="checkbox" />',
            'question' => __( 'Question', $this->plugin_name ),
        );

        $fstatus = isset( $_REQUEST['fstatus'] ) ? absint( sanitize_text_field( $_REQUEST['fstatus'] ) ) : null;

        $columns['question_image']  = __( 'Image', $this->plugin_name );
        $columns['category_id']     = __( 'Category', $this->plugin_name );
        $columns['tag_id']          = __( 'Tags', $this->plugin_name );
        $columns['type']            = __( 'Type', $this->plugin_name );
        $columns['items_count']     = __( 'Answers count', $this->plugin_name );
        $columns['create_date']     = __( 'Created', $this->plugin_name );

        if( $fstatus !== 2 ) {
            $columns['published'] = __( 'Status', $this->plugin_name );
        }

        if( $this->current_user_can_edit ){
            $columns['author_id'] = __( 'Author', $this->plugin_name );
        }

        if( $fstatus !== 2 ) {
            $columns['used'] = __( 'Used', $this->plugin_name );
        }

        $columns['id'] = __( 'ID', $this->plugin_name );
        
        if( isset( $_GET['action'] ) && ( $_GET['action'] == 'add' || $_GET['action'] == 'edit' ) ){
            return array();
        }

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
            'published'     => array( 'published', true ),
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
        $actions = array();

        $fstatus = isset( $_REQUEST['fstatus'] ) ? absint( sanitize_text_field( $_REQUEST['fstatus'] ) ) : null;
        if( $fstatus === 2 ){
            $actions['bulk-restore'] = __('Restore', $this->plugin_name);
            $actions['bulk-delete']  = __('Delete Permanently', $this->plugin_name);
        }else{
            $actions['bulk-published']   = __('Publish', $this->plugin_name);
            $actions['bulk-unpublished'] = __('Unpublish', $this->plugin_name);
            $actions['bulk-trash']       = __('Move to trash', $this->plugin_name);
        }

        $if_user_created_question = Quiz_Maker_Data::ays_quiz_if_current_user_created("aysquiz_questions");

        if ( ! is_null( $if_user_created_question ) && ! empty( $if_user_created_question ) && $if_user_created_question > 0 ) {

        } else if( ! $this->current_user_can_edit ){
            $actions = array();
        }

        return $actions;
    }
    
    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {
        global $wpdb;

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

        $search = ( isset( $_REQUEST['s'] ) ) ? sanitize_text_field( $_REQUEST['s'] ) : false;

        $do_search = ( $search ) ? sprintf(" ( question LIKE '%%%s%%' OR question_title LIKE '%%%s%%' ) ", esc_sql( $wpdb->esc_like( $search ) ) , esc_sql( $wpdb->esc_like( $search ) )  ) : '';

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

        //Detect when a bulk action is being triggered...
        if ( 'trash' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, $this->plugin_name . '-trash-question' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::trash_items( absint( $_GET['question'] ) );

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $add_query_args = array(
                    "status" => 'trashed'
                );
                if( isset( $_GET['fstatus'] ) && $_GET['fstatus'] != '' ){
                    $add_query_args['fstatus'] = sanitize_text_field( $_GET['fstatus'] );
                }
                $url = remove_query_arg( array('action', 'question', '_wpnonce') );
                $url = esc_url_raw( add_query_arg( $add_query_args, $url ) );
                wp_redirect( $url );
            }
        }

        //Detect when a bulk action is being triggered...
        if ( 'restore' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, $this->plugin_name . '-restore-question' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::restore_items( absint( $_GET['question'] ) );

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $add_query_args = array(
                    "status" => 'restored'
                );
                if( isset( $_GET['fstatus'] ) && $_GET['fstatus'] != '' ){
                    $add_query_args['fstatus'] = sanitize_text_field( $_GET['fstatus'] );
                }
                $url = remove_query_arg( array('action', 'question', '_wpnonce') );
                $url = esc_url_raw( add_query_arg( $add_query_args, $url ) );
                wp_redirect( $url );
            }

        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
        || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' ) ) {

            $delete_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

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

            $published_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

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

            $unpublished_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

            // loop over the array of record IDs and mark as read them

            foreach ( $unpublished_ids as $id ) {
                self::ays_quiz_published_unpublished_questions( $id , 'unpublished' );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $url = esc_url_raw( remove_query_arg(array('action', 'question', '_wpnonce')  ) ) . '&status=unpublished';
            wp_redirect( $url );
        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-trash' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-trash' ) ) {

            $trash_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

            // loop over the array of record IDs and delete them
            foreach ( $trash_ids as $id ) {
                self::trash_items( $id );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $add_query_args = array(
                "status" => 'all-trashed'
            );
            if( isset( $_GET['fstatus'] ) && $_GET['fstatus'] != '' ){
                $add_query_args['fstatus'] = sanitize_text_field( $_GET['fstatus'] );
            }
            $url = remove_query_arg( array('action', 'question', '_wpnonce') );
            $url = esc_url_raw( add_query_arg( $add_query_args, $url ) );
            wp_redirect( $url );
        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-restore' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-restore' ) ) {

            $restore_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

            // loop over the array of record IDs and delete them
            foreach ( $restore_ids as $id ) {
                self::restore_items( $id );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $add_query_args = array(
                "status" => 'all-restored'
            );
            if( isset( $_GET['fstatus'] ) && $_GET['fstatus'] != '' ){
                $add_query_args['fstatus'] = sanitize_text_field( $_GET['fstatus'] );
            }
            $url = remove_query_arg( array('action', 'question', '_wpnonce') );
            $url = esc_url_raw( add_query_arg( $add_query_args, $url ) );
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
        <div class="notice <?php echo esc_attr( $status_color ); ?> is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }

    public function ays_quiz_if_user_created_question() {
        global $wpdb;

        $current_user = get_current_user_id();
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions WHERE `author_id` = ".$current_user." ";

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }
}
