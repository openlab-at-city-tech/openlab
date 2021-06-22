<?php
    $action = ( isset($_GET['action']) ) ? $_GET['action'] : '';
    $id     = ( isset($_GET['quiz']) ) ? $_GET['quiz'] : null;

    if($action == 'duplicate'){
        $this->quizes_obj->duplicate_quizzes($id);
    }
    $max_id = $this->get_max_id('questions');
    $user_id = get_current_user_id();

    $gen_options = ($this->settings_obj->ays_get_setting('options') === false) ? array() : json_decode( stripcslashes( $this->settings_obj->ays_get_setting('options') ), true);

    $question_default_type = isset($gen_options['question_default_type']) && $gen_options['question_default_type'] != '' ? $gen_options['question_default_type'] : null;

    $options = array(
        'bg_image' => "",
        'use_html' => 'off',
    );
    $question = array(
        'category_id' => '1',
        'author_id' => $user_id,
        'question' => '',
        'question_image' => '',
        'type' => $question_default_type,
        'published' => '',
        'user_explanation' => 'off',
        'wrong_answer_text' => '',
        'right_answer_text' => '',
        'explanation' => '',
        'create_date' => current_time( 'mysql' ),
        'not_influence_to_score' => 'off',
        'weight' => floatval(1),
        'options' => json_encode($options),
    );

    $question_categories = $this->quizes_obj->get_question_categories();
    $quiz_categories = $this->quizes_obj->get_quiz_categories();

?>

<div class="wrap ays_quizzes_list_table">
    <button style="width:50px;height:50px;" class="ays-pulse-button ays-quizzes-table-quick-start" id="ays_quick_start" title="Quick quiz" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="left" data-content="<?php echo __('Build your quiz in a few minutes',$this->plugin_name)?>"></button>
    <h1 class="wp-heading-inline">
        <?php
            echo __(esc_html(get_admin_page_title()),$this->plugin_name);
            echo sprintf( '<a href="?page=%s&action=%s" class="page-title-action">' . __('Add New', $this->plugin_name) . '</a>', esc_attr( $_REQUEST['page'] ), 'add');
        ?>
    </h1>
    <?php if($max_id <= 6): ?>
    <div class="notice notice-success is-dismissible">
        <p style="font-size:14px;">
            <strong>
                <?php echo __( "If you haven't created questions yet, you need to do it first.", $this->plugin_name ); ?>
            </strong>
            <br>
            <strong>
                <em>
                    <?php echo __( "For creating a question go", $this->plugin_name ); ?> 
                    <a href="<?php echo admin_url('admin.php') . "?page=".$this->plugin_name . "-questions"; ?>" target="_blank">
                        <?php echo __( "here.", $this->plugin_name ); ?>.
                    </a>
                </em>
            </strong>
        </p>
    </div>
    <?php endif; ?>
    <div id="poststuff" style="margin-top:20px;">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <?php
                        $this->quizes_obj->views();
                    ?>
                    <form method="post">
                        <?php
                            $this->quizes_obj->prepare_items();
                            $search = __( "Search", $this->plugin_name );
                            $this->quizes_obj->search_box($search, $this->plugin_name);
                            $this->quizes_obj->display();
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
    <div id="ays-quick-modal" tabindex="-1" class="ays-modal">
        <!-- Modal content -->
        <div class="ays-modal-content fadeInDown" id="ays-quick-modal-content">
            <div class="ays-quiz-preloader">
                <img src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/loaders/tail-spin.svg">
            </div>
            <div class="ays-modal-header">
                <span class="ays-close">&times;</span>
                <h4><?php echo __('Build your quiz in a few minutes', $this->plugin_name); ?></h4>
            </div>
            <div class="ays-modal-body">
                <form method="post" id="ays_quick_popup">
                    <div class="ays_modal_element">
                        <div class="form-group row">
                            <div class="col-sm-2">
                                <label class='ays-label ays_quiz_title' for='ays-quiz-title'><?php echo __('Quiz Title', $this->plugin_name); ?></label>
                            </div>
                            <div class="col-sm-10">
                                <input type="text" class="ays-text-input" id='ays-quiz-title' name='ays_quiz_title' value=""/>
                            </div>
                        </div>
                    </div>
                    <div class="ays_modal_element">
                        <div class="form-group row">
                            <div class="col-sm-2">
                                <label class='ays-label ays_quiz_title' for='ays-quiz-category'><?php echo __('Quiz Category', $this->plugin_name); ?></label>
                            </div>
                            <div class="col-sm-10">
                                <select id="ays-quiz-category" class="ays-text-input ays-text-input-short" name="ays_quiz_category">
                                    <?php
                                        foreach ($quiz_categories as $key => $quiz_category) {
                                            $selected = '';
                                            if( intval( $quiz_category['id'] ) == 1 ){
                                                $selected = ' selected ';
                                            }
                                            echo "<option value='" . $quiz_category['id'] . "' " . $selected . ">" . esc_attr( $quiz_category['title'] ) . "</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="ays-quick-questions-container">
                        <div class="ays-modal-flexbox">
                            <p class="ays_questions_title"><?php echo __('Questions',$this->plugin_name)?></p>
                            <a href="javascript:void(0)" class="ays_add_question">
                                <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                            </a>
                        </div>
                        <hr/>
                        <div tabindex="0" class="ays_modal_element ays_modal_question active_question_border" id="ays_question_id_1">
                            <div class="form-group row">
                                <div class="col-sm-8">
                                    <input type="text" value="<?php echo __( 'Question Default Title' , $this->plugin_name); ?>" class="ays_question_input">
                                </div>
                                <div class="col-sm-4" style="text-align: right;">
                                    <select class="ays_quick_question_type" name="ays_quick_question_type[]" style="width: 200px;">
                                        <option value="radio"><?php echo __("Radio", $this->plugin_name); ?></option>
                                        <option value="checkbox"><?php echo __("Checkbox", $this->plugin_name); ?></option>
                                        <option value="select"><?php echo __("Dropdawn", $this->plugin_name); ?></option>
                                        <option value="text"><?php echo __("Text", $this->plugin_name); ?></option>
                                    </select>
                                </div>
                            </div>

<!--                            <div class="ays_question_overlay"></div>-->
                            <div class="form-group row">
                                <div class="col-sm-8"></div>
                                <div class="col-sm-4" style="text-align: right;">
                                    <select class="ays_quick_question_cat" name="ays_quick_question_cat[]" style="width: 200px;">
                                        <?php
                                            $cat = 0;
                                            foreach ($question_categories as $question_category) {
                                                $checked = (intval($question_category['id']) == intval($question['category_id'])) ? "selected" : "";
                                                if ($cat == 0 && intval($question['category_id']) == 0) {
                                                    $checked = 'selected';
                                                }
                                                echo "<option value='" . $question_category['id'] . "' " . $checked . ">" . stripslashes($question_category['title']) . "</option>";
                                                $cat++;
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="ays-modal-flexbox flex-end">
                                <table class="ays_answers_table">
                                    <tr>
                                        <td>
                                            <input class="ays_answer_unique_id" type="radio" name="ays_answer_radio[1]" checked>
                                        </td>
                                        <td class="ays_answer_td">
                                            <p class="ays_answer"><?php echo __('Answer',$this->plugin_name)?></p>
                                        </td>
                                        <td class="show_remove_answer">
                                            <i class="ays_fa ays_fa_times" aria-hidden="true"></i>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input class="ays_answer_unique_id" type="radio" name="ays_answer_radio[1]">
                                        </td>
                                        <td class="ays_answer_td">
                                            <p class="ays_answer"><?php echo __('Answer',$this->plugin_name)?></p>
                                        </td>
                                        <td class="show_remove_answer">
                                            <i class="ays_fa ays_fa_times" aria-hidden="true"></i>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input class="ays_answer_unique_id" type="radio" name="ays_answer_radio[1]">
                                        </td>
                                        <td class="ays_answer_td">
                                            <p class="ays_answer"><?php echo __('Answer',$this->plugin_name)?></p>
                                        </td>
                                        <td class="show_remove_answer">
                                            <i class="ays_fa ays_fa_times" aria-hidden="true"></i>
                                        </td>
                                    </tr>
                                    <tr class="show_add_answer">
                                        <td colspan="3">
                                            <a href="javascript:void(0)" class="ays_add_answer">
                                                <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                                <table class="ays_quick_quiz_text_type_table display_none" id="ays_quick_quiz_text_type_table">
                                    <tr>
                                        <td>
                                            <input style="display:none;" class="ays-correct-answer" type="checkbox" name="ays-correct-answer[]" value="1" checked/>
                                            <textarea type="text" name="ays-correct-answer-value[]" class="ays-correct-answer-value" placeholder="<?php echo __( 'Answer text', $this->plugin_name ); ?>"></textarea>
                                        </td>
                                    </tr>
                                </table>
                                <div>
                                    <a href="javascript:void(0)" class="ays_question_clone_icon">
                                        <i class="ays_fa ays_fa_clone" aria-hidden="true"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="ays_trash_icon">
                                        <i class="ays_fa ays_fa_trash_o" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="ays-modal-flexbox" style="justify-content: flex-end;">
                        <a href="javascript:void(0)" class="ays_add_question">
                            <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                        </a>
                    </div>
                    <input type="button" class="btn btn-primary ays_submit_button" id="ays_quick_submit_button" value="<?php echo __('Submit',$this->plugin_name)?>"/>
                    <input type="hidden" id="ays_quick_question_max_id" value="1"/>
                </form>
            </div>
        </div>
    </div>
</div>
