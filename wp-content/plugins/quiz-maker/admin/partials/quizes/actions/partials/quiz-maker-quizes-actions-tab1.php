<div id="tab1" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab1') ? 'ays-quiz-tab-content-active' : ''; ?>">
    <p class="ays-subtitle"><?php echo __('General Settings',$this->plugin_name)?></p>
    <hr/>
    <div class="form-group row">
        <div class="col-sm-2">
            <label for='ays-quiz-title'>
                <?php echo __('Title', $this->plugin_name); ?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Title of the quiz',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-10">
            <input type="text" class="ays-text-input" id='ays-quiz-title' name='ays_quiz_title'
                   value="<?php echo $quiz_title; ?>"/>
        </div>
    </div> <!-- Quiz Title -->
    <hr/>
    <div class='ays-field-dashboard'>
        <label>
            <?php echo __('Quiz image', $this->plugin_name); ?>
            <a href="javascript:void(0)" class="add-quiz-image"><?php echo $image_text; ?></a>
            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Add image to the starting page of the quiz',$this->plugin_name)?>">
                <i class="ays_fa ays_fa_info_circle"></i>
            </a>
        </label>
        <div class="ays-quiz-image-container" style="<?php echo $style; ?>">
            <span class="ays-remove-quiz-img"></span>
            <img src="<?php echo $quiz_image; ?>" id="ays-quiz-img"/>
        </div>
    </div> <!-- Quiz Image -->
    <hr/>
    <input type="hidden" name="ays_quiz_image" id="ays-quiz-image" value="<?php echo $quiz_image; ?>"/>
    <div class='ays-field-dashboard ays-quiz-result-message-vars-parent'>
        <label for='ays-quiz-description'>
            <?php echo __('Description', $this->plugin_name); ?>
            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Provide more information about the quiz. You can choose whether to show it or not in the front end in the “Settings” tab.',$this->plugin_name)?>">
                <i class="ays_fa ays_fa_info_circle"></i>
            </a>
        </label>
        <?php
            echo $quiz_message_vars_description_html;
            $content = $quiz_description;
            $editor_id = 'ays-quiz-description';
            $settings = array(
                'editor_height' => $quiz_wp_editor_height,
                'textarea_name' => 'ays_quiz_description',
                'editor_class' => 'ays-textarea',
                'media_buttons' => true
            );
            wp_editor($content, $editor_id, $settings);
        ?>
    </div> <!-- Quiz Deacription -->
    <hr/>
    <div class="form-group row">
        <div class="col-sm-2">
            <label for="ays-category">
                <?php echo __('Category', $this->plugin_name); ?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Category of the quiz. For making a category please visit Quiz Categories page from left navbar',$this->plugin_name); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-10">
            <select id="ays-category" name="ays_quiz_category">
                <option></option>
                <?php
                $cat = 0;
                foreach ($quiz_categories as $key => $quiz_category) {

                    $q_category_id = (isset( $quiz_category['id'] ) && $quiz_category['id'] != "") ? $quiz_category['id'] : 1;
                    $quiz_category_title = (isset( $quiz_category['title'] ) && $quiz_category['title'] != "") ? esc_attr( stripslashes($quiz_category['title']) ) : "";

                    $selected = ($q_category_id == $quiz_category_id) ? "selected" : "";
                    if ($cat == 0 && $quiz_category_id == 0) {
                        $selected = 'selected';
                    }
                    echo '<option value="' . $quiz_category["id"] . '" ' . $selected . '>' . $quiz_category_title . '</option>';
                    $cat++;
                }
                ?>
            </select>
        </div>
    </div> <!-- Quiz Category -->
    <hr/>
    <div class="form-group row">
        <div class="col-sm-2">
            <label>
                <?php echo __('Status', $this->plugin_name); ?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose whether the quiz is active or not.If you choose Unpublished option, the quiz won’t be shown anywhere in your website (You don’t need to remove shortcodes).',$this->plugin_name); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-10">
            <div class="form-check form-check-inline">
                <input type="radio" id="ays-publish" name="ays_publish"
                       value="1" <?php echo ($quiz_published == '') ? "checked" : ""; ?>  <?php echo ($quiz_published == '1') ? 'checked' : ''; ?>/>
                <label class="form-check-label"
                       for="ays-publish"> <?php echo __('Published', $this->plugin_name); ?> </label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" id="ays-unpublish" name="ays_publish"
                       value="0" <?php echo ($quiz_published == '0') ? 'checked' : ''; ?>/>
                <label class="form-check-label"
                       for="ays-unpublish"> <?php echo __('Unpublished', $this->plugin_name); ?> </label>
            </div>
        </div>
    </div> <!-- Quiz Status -->
    <hr/>
    <?php if($post_id === null): ?>
    <div class="form-group row ays_toggle_parent">
        <div class="col-sm-2">
            <label for="ays_add_post_for_quiz">
                <?php echo __('Create post for quiz',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('A new WordPress post will be created automatically and will include the shortcode of this quiz. This function will be executed only once. You can find this post on Posts page, which will have the same title as the quiz. The image of the quiz will be the featured image of the post.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-1">
            <input type="checkbox" id="ays_add_post_for_quiz" name="ays_add_post_for_quiz" value="on" class="ays_toggle_checkbox"/>
        </div>
        <div class="col-sm-9 ays_toggle_target ays_divider_left display_none">
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_add_postcat_for_quiz">
                        <?php echo __('Choose post categories',$this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can choose one or several categories. These categories are WordPress default post categories. There is no connection with quiz categories.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <div class="input-group">
                        <select name="ays_add_postcat_for_quiz[]"
                                id="ays_add_postcat_for_quiz"
                                class="ays_postcat_for_quiz"
                                multiple>
                            <?php
                                foreach ($cat_list as $cat) {
                                    echo "<option value='" . $cat->cat_ID . "' >" . esc_attr($cat->name) . "</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Create WP Post -->
    <hr/>
    <?php else: ?>
    <div class="form-group row">
        <div class="col-sm-2">
            <label for="ays_add_post_for_quiz">
                <?php echo __('WP post', $this->plugin_name); ?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Via these two links you can see the connected post in front end and make changes in the dashboard.',$this->plugin_name); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-10">
            <div class="row">
                <div style="margin-right: 10px;">
                    <a class="button" href="<?php echo $ays_quiz_view_post_url; ?>" target="_blank"><?php echo __( "View Post", $this->plugin_name ); ?> <i class="ays_fa ays_fa_external_link"></i></a>
                </div>
                <div>
                    <a class="button" href="<?php echo $ays_quiz_edit_post_url; ?>" target="_blank"><?php echo __( "Edit Post", $this->plugin_name ); ?> <i class="ays_fa ays_fa_external_link"></i></a>
                </div>
            </div>
        </div>
        <input type="hidden" name="ays_post_id_for_quiz" value="<?php echo $post_id; ?>">
    </div> <!-- WP Post -->
    <hr>
    <?php endif; ?>
    <input type="radio" class="ays-enable-timer1" name="ays_quiz_condition_calculation_type" value="default" <?php echo ($quiz_condition_calculation_type == 'default') ? 'checked' : '' ?> style="display: none;"/>
    <input type="radio" class="ays-enable-timer1" name="ays_quiz_condition_calculation_type" value="by_keyword" <?php echo ($quiz_condition_calculation_type == 'by_keyword') ? 'checked' : ''; ?> style="display: none;"/>
    <input type="checkbox" class="ays-enable-timer1" id="ays_quiz_condition_show_all_results" name="ays_quiz_condition_show_all_results" value="on" <?php echo $quiz_condition_show_all_results ? 'checked' : '' ?> style="display: none;" />
    <div class='form-group row ays_items_count_div'>
        <div class="col-sm-3" style="display: flex; align-items: center;">
            <div style='display: flex;align-items: center;margin-right: 15px;'>
                <a href="javascript:void(0)" class="ays-add-question">
                    <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                    <?php echo __('Add questions', $this->plugin_name); ?>
                </a>
                <a class="ays_help" style="font-size:15px;" data-placement="bottom" data-toggle="tooltip" data-html="true" title="<?php echo "<p style='margin:0;text-indent:7px;'>".esc_attr(__('For adding questions to the quiz you need to make questions first from the “Questions page” in the left navbar. After popup’s opening, you can filter and select your prepared questions for this quiz.', $this->plugin_name))."</p><p style='margin:0;text-indent:7px;'>".esc_attr(__('The ordering of the questions will be the same as you chose. Also, you can reorder them after selection. There are no limitations for questions quantity.', $this->plugin_name))."</p>"; ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </div>
        </div>
        <div class="col-sm-9">
            <div class="form-group row" style="margin-bottom: 0;">
                <div class="col-sm-9">
                    <p class="ays_questions_action">
                        <span class="ays_questions_count">
                            <?php
                            echo '<span class="questions_count_number">' . $question_id_array_count . '</span> '. __('items',$this->plugin_name);
                            ?>
                        </span>
                    </p>
                </div>
                <div class="col-sm-3" style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="ays-question-ordering" tabindex="0" data-ordered="false">
                        <i class="ays_fa fas ays_fa_exchange"></i>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Reverse the ordering of the questions in the list.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </div>
                    <div style="display: flex;">
                        <button class="ays_bulk_del_questions button" type="button" style="margin: 0 10px;" disabled>
                            <?php echo __( 'Delete', $this->plugin_name); ?>
                        </button>
                        <button class="ays_select_all button ays-quiz-select-all-button" type="button">
                            <?php echo __( 'Select All', $this->plugin_name); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

<!--
    <div class='ays-field-dashboard ays_items_count_div'>
        <div style='display: flex;align-items: center;margin-right: 15px;'>
            <a href="javascript:void(0)" class="ays-add-question">
                <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                <?php echo __('Add questions', $this->plugin_name); ?>
            </a>
            <a class="ays_help" style="font-size:15px;" data-placement="bottom" data-html="true" data-toggle="tooltip" title="<?php echo "<p style='margin:0;text-indent:7px;'>".esc_attr(__('For adding questions to the quiz you need to make questions first from the “Questions page” in the left navbar. After popup’s opening, you can filter and select your prepared questions for this quiz.', $this->plugin_name))."</p><p style='margin:0;text-indent:7px;'>".esc_attr(__('The ordering of the questions will be the same as you chose. Also, you can reorder them after selection. There are no limitations for questions quantity.', $this->plugin_name))."</p>"; ?>">
                <i class="ays_fa ays_fa_info_circle"></i>
            </a>
        </div>
        <p class="ays_questions_action">
            <span class="ays_questions_count">
                <?php
                echo '<span class="questions_count_number">' . count($question_id_array) . '</span> '. __('items',$this->plugin_name);
                ?>
            </span>
            <button class="ays_bulk_del_questions button" type="button" disabled>
                <?php echo __( 'Delete', $this->plugin_name); ?>
            </button>
            <button class="ays_select_all button" type="button">
                <?php echo __( 'Select All', $this->plugin_name); ?>
            </button>
        </p>
-->
    </div>
    <div class="ays-field-dashboard ays-table-wrap" style="padding-top: 15px;">
        <table class="ays-questions-table" id="ays-questions-table">
            <thead>
            <tr class="ui-state-default">
                <th class="ays-quiz-question-ordering-row th-150"><?php echo __('Ordering', $this->plugin_name); ?></th>
                <th class="ays-quiz-question-question-row th-650"><?php echo __('Question', $this->plugin_name); ?></th>
                <th class="ays-quiz-question-type-row th-150"><?php echo __('Type', $this->plugin_name); ?></th>
                <th class="ays-quiz-question-category-row th-150"><?php echo __('Category', $this->plugin_name); ?></th>
                <th class="ays-quiz-question-tag-row th-150"><?php echo __('Tags', $this->plugin_name); ?></th>
                <th class="ays-quiz-question-id-row th-150"><?php echo __('ID', $this->plugin_name); ?></th>
                <th class="ays-quiz-question-action-row th-150" style="min-width:120px;"><?php echo __('Actions', $this->plugin_name); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(!(count($question_id_array) === 1 && $question_id_array[0] == '')  && 1==0) {
                foreach ($question_id_array as $key => $question_id) {
                    $data = $this->quizes_obj->get_published_questions_by('id', absint(intval($question_id)));

                    $if_question_trash_status = (isset( $data["published"] ) && absint( $data["published"] ) == 2) ? true : false;

                    if ( $if_question_trash_status ) {
                        continue;
                    }

                    $className = "";
                    if (($key + 1) % 2 == 0) {
                        $className = "even";
                    }
                    $edit_question_url = "?page=".$this->plugin_name."-questions&action=edit&question=".$data['id'];
                    // $table_question = (strip_tags(stripslashes($data['question'])));
                    // $table_question = $this->ays_restriction_string("word",$table_question, 10);
                    $data_tag_id = (isset( $data['tag_id'] ) && $data['tag_id'] != "") ? $data['tag_id'] : "";

                    $tag_ids = array();
                    if ( $data_tag_id != "" ) {
                        $tag_ids = explode(',',$data['tag_id']);
                    }
                    $question_tags_title = '';
                    foreach ($tag_ids as $tag_id) {
                        if( isset( $question_tags_array[$tag_id] ) ){
                            $question_tags_title .= $question_tags_array[$tag_id].",";
                        }
                    }

                    if($data['type'] == 'custom'){
                        if(isset($data['question_title']) && $data['question_title'] != ''){
                            $table_question = htmlspecialchars_decode( $data['question_title'], ENT_COMPAT);
                            $table_question = stripslashes( $table_question );
                            if($table_question == ''){
                                $table_question = __( 'Question ID', $this->plugin_name ) .' '. $data['id'];
                            }
                        }else{
                            $table_question = __( 'Custom question', $this->plugin_name ) . ' #' . $data['id'];
                        }
                    }else{
                        if(isset($data['question_title']) && $data['question_title'] != ''){
                            $table_question = esc_attr( $data['question_title'] );
                        }elseif(isset($data['question']) && strlen($data['question']) != 0){
                            $table_question = strip_tags(stripslashes($data['question']));
                        }elseif ((isset($data['question_image']) && $data['question_image'] !='')){
                            $table_question = __( 'Image question', $this->plugin_name );
                        }
                        $table_question = $this->ays_restriction_string("word", $table_question, 10);
                    }

                    switch ( $data['type'] ) {
                        case 'short_text':
                            $ays_question_type = 'short text';
                            break;
                        case 'true_or_false':
                            $ays_question_type = 'true/false';
                            break;
                        case 'fill_in_blank':
                            $ays_question_type = 'Fill in blank';
                            break;
                        default:
                            $ays_question_type = $data['type'];
                            break;
                    }

                    ?>
                    <tr class="ays-question-row ui-state-default ays-question-selected <?php echo $className; ?>"
                        data-id="<?php echo $data['id']; ?>">
                        <td class="ays-sort ays-quiz-question-ordering-row"><i class="ays_fa ays_fa_arrows" aria-hidden="true"></i></td>
                        <td class="ays-quiz-question-question-row">
                            <a href="<?php echo $edit_question_url; ?>" target="_blank" class="ays-edit-question" title="<?php echo __('Edit question', $this->plugin_name); ?>">
                                <?php echo $table_question ?>
                            </a>
                        </td>
                        <td class="ays-quiz-question-type-row">
                            <?php echo $ays_question_type; ?>
                            <input type="hidden" name="ays_question_type[<?php echo $data['type']; ?>][]" value="<?php echo $data['id']; ?>">
                        </td>
                        <td class="ays-quiz-question-category-row"><?php echo $question_categories_array[$data['category_id']]; ?></td>
                        <td class="ays-quiz-question-tag-row"><?php echo rtrim($question_tags_title,','); ?></td>
                        <td class="ays-quiz-question-id-row"><?php echo $data['id']; ?></td>
                        <td class="ays-quiz-question-action-row">
                            <div class="ays-question-row-actions">
                                <input type="checkbox" class="ays_del_tr">
                                <a href="<?php echo $edit_question_url; ?>" target="_blank" class="ays-edit-question" title="<?php echo __('Edit question', $this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_pencil_square" aria-hidden="true"></i>
                                </a>
                                <a href="javascript:void(0)" class="ays-delete-question" title="<?php echo __('Delete', $this->plugin_name); ?>"
                                   data-id="<?php echo $data['id']; ?>">
                                    <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            }
            if(empty($question_id_array)){
                ?>
                <tr class="ays-question-row ui-state-default">
                    <td colspan="7" class="empty_quiz_td">
                        <div>
                            <i class="ays_fa ays_fa_info" aria-hidden="true" style="margin-right:10px"></i>
                            <span style="font-size: 13px; font-style: italic;">
                            <?php
                                echo __( 'There are no questions yet.', $this->plugin_name );
                            ?>
                            </span>
                            <a class="create_question_link" href="admin.php?page=<?php echo $this->plugin_name; ?>-questions&action=add" target="_blank"><?php echo __('Create question', $this->plugin_name); ?></a>
                        </div>
                        <div class='ays_add_question_from_table'>
                            <a href="javascript:void(0)" class="ays-add-question">
                                <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                                <?php echo __('Add questions', $this->plugin_name); ?>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <p class="ays_questions_action" style="width:100%;">
            <span class="ays_questions_count">
                <?php
                echo '<span class="questions_count_number">' . $question_id_array_count . '</span> '. __('items',$this->plugin_name);
                ?>
            </span>
            <button class="ays_bulk_del_questions button" type="button" disabled>
                <?php echo __( 'Delete', $this->plugin_name); ?>
            </button>
            <button class="ays_select_all button ays-quiz-select-all-button" type="button">
                <?php echo __( 'Select All', $this->plugin_name); ?>
            </button>
        </p>
    </div> <!-- Questions table -->
    <input type="hidden" id="ays_already_added_questions" name="ays_added_questions" value="<?php echo $question_ids; ?>"/>
    <input type="hidden" id="ays_already_added_questions_count" value="<?php echo count($question_id_array); ?>"/>
</div>
