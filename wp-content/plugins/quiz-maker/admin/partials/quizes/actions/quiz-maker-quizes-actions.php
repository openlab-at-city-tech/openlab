<?php
    require_once(AYS_QUIZ_ADMIN_PATH . "/partials/quizes/actions/quiz-maker-quizes-actions-options.php");
?>

<style id="ays_live_custom_css"></style>
<div class="wrap">
    <div class="container-fluid">
        <form class="ays-quiz-category-form ays-quiz-form" id="ays-quiz-category-form" method="post">
            <input type="hidden" name="ays_quiz_tab" value="<?php echo esc_attr($ays_quiz_tab); ?>">
            <input type="hidden" name="ays_quiz_ctrate_date" value="<?php echo $quiz_create_date; ?>">
            <input type="hidden" name="ays_quiz_author" value="<?php echo $author_id; ?>">
            <input type="hidden" class="quiz_wp_editor_height" value="<?php echo $quiz_wp_editor_height; ?>">
            <div class="ays-quiz-heading-box">
                <div class="ays-quiz-wordpress-user-manual-box">
                    <a href="https://ays-pro.com/wordpress-quiz-maker-user-manual" target="_blank"><?php echo __("View Documentation", $this->plugin_name); ?></a>
                </div>
            </div>
            <h1 class="wp-heading-inline">
                <?php
                if( $owner ){
                    echo $heading;
                    $other_attributes = array();
                    $other_attributes_only_save = array(
                        'title' => __('Ctrl + s', $this->plugin_name),
                        'data-toggle' => 'tooltip',
                        'data-delay'=> '{"show":"1000"}'
                    );

                    if( $action == 'edit' ){
                        $other_attributes['disabled'] = 'disabled';
                        $other_attributes_only_save['disabled'] = 'disabled';
                    }

                    submit_button(__('Save and close', $this->plugin_name), 'primary ays-quiz-loader-banner', 'ays_submit_top', false, $other_attributes);
                    submit_button(__('Save', $this->plugin_name), 'ays-quiz-loader-banner', 'ays_apply_top', false, $other_attributes_only_save);
                    echo $loader_iamge;
                }
                ?>
            </h1>
            <div>
                <div class="ays-quiz-subtitle-main-box">
                    <p class="ays-subtitle">
                        <?php if(isset($id) && count($get_all_quizzes) > 1):?>
                        <i class="ays_fa ays_fa_arrow_down ays-quiz-open-quizzes-list" style="font-size: 15px;"></i>   
                        <?php endif; ?>
                        <span style="visibility:hidden;display: inline-block;width: 0;letter-spacing: -100000px;">Quiz</span>
                        <strong class="ays_quiz_title_in_top"><?php echo $quiz_title; ?></strong>
                        <?php 

                            $embed_button_html = "";

                            if ( $quiz_iframe_html == "" ) {
                                
                                $embed_button_html = '<button type="button" class="button button-primary" style="float: right;" title="' . __('Save quiz',$this->plugin_name) .'" disabled>'. __("Embed code", $this->plugin_name) .'</button>';
                            } else {
                                $embed_button_html = '<button type="button" class="button button-primary ays-quiz-copy-embed-code" style="float: right;" data-toggle="tooltip" title="' . __('Click for copy',$this->plugin_name) .'">'. __("Embed code", $this->plugin_name) .'</button>';
                            }

                            echo $embed_button_html;

                        ?>
                    </p>
                    <?php if(isset($id) && count($get_all_quizzes) > 1):?>
                    <div class="ays-quiz-quizzes-data">
                        <?php $var_counter = 0; foreach($get_all_quizzes as $var => $var_name): if( intval($var_name['id']) == $id ){continue;} $var_counter++; ?>
                            <?php ?>
                            <label class="ays-quiz-message-vars-each-data-label">
                                <input type="radio" class="ays-quiz-quizzes-each-data-checker" hidden id="ays_quiz_message_var_count_<?php echo $var_counter?>" name="ays_quiz_message_var_count">
                                <div class="ays-quiz-quizzes-each-data">
                                    <input type="hidden" class="ays-quiz-quizzes-each-var" value="<?php echo $var; ?>">
                                    <a href="?page=quiz-maker&action=edit&quiz=<?php echo $var_name['id']?>" target="_blank" class="ays-quiz-go-to-quizzes"><span><?php echo stripslashes(esc_attr($var_name['title'])); ?></span></a>
                                </div>
                            </label>              
                        <?php endforeach ?>
                    </div>                        
                <?php endif; ?>
                </div>
                <?php if($id !== null): ?>
                <div class="row">
                    <div class="col-sm-3">
                        <label> <?php echo __( "Shortcode text for editor", $this->plugin_name ); ?> </label>
                    </div>
                    <div class="col-sm-9">
                        <p style="font-size:14px; font-style:italic;">
                            <?php echo __("To insert the Quiz into a page, post or text widget, copy shortcode", $this->plugin_name); ?>
                            <strong class="ays-quiz-shortcode-box" onClick="selectElementContents(this)" class="ays_help" data-toggle="tooltip" title="<?php echo __('Click for copy',$this->plugin_name);?>" style="font-size:16px; font-style:normal;"><?php echo "[ays_quiz id='".$id."']"; ?></strong>
                            <?php echo " " . __( "and paste it at the desired place in the editor.", $this->plugin_name); ?>
                        </p>
                    </div>
                </div>
                <?php endif;?>
            </div>
            <hr/>

            <div class="ays-top-menu-wrapper">
                <div class="ays_menu_left" data-scroll="0"><i class="ays_fa ays_fa_angle_left"></i></div>
                <div class="ays-top-menu">
                    <div class="nav-tab-wrapper ays-top-tab-wrapper">
                        <a href="#tab1" data-tab="tab1" class="nav-tab <?php echo ($ays_quiz_tab == 'tab1') ? 'nav-tab-active' : ''; ?>">
                            <?php echo __("General", $this->plugin_name);?>
                        </a>
                        <a href="#tab2" data-tab="tab2" class="nav-tab <?php echo ($ays_quiz_tab == 'tab2') ? 'nav-tab-active' : ''; ?>">
                            <?php echo __("Styles", $this->plugin_name);?>
                        </a>
                        <a href="#tab3" data-tab="tab3" class="nav-tab <?php echo ($ays_quiz_tab == 'tab3') ? 'nav-tab-active' : ''; ?>">
                            <?php echo __("Settings", $this->plugin_name);?>
                        </a>
                        <a href="#tab4" data-tab="tab4" class="nav-tab <?php echo ($ays_quiz_tab == 'tab4') ? 'nav-tab-active' : ''; ?>">
                            <?php echo __("Results Settings", $this->plugin_name);?>
                        </a>
                        <a href="#tab5" data-tab="tab5" class="nav-tab <?php echo ($ays_quiz_tab == 'tab5') ? 'nav-tab-active' : ''; ?>">
                            <?php echo __("Limitation Users", $this->plugin_name);?>
                        </a>
                        <a href="#tab6" data-tab="tab6" class="nav-tab <?php echo ($ays_quiz_tab == 'tab6') ? 'nav-tab-active' : ''; ?>">
                            <?php echo __("User Data", $this->plugin_name);?>
                        </a>
                        <a href="#tab7" data-tab="tab7" class="nav-tab <?php echo ($ays_quiz_tab == 'tab7') ? 'nav-tab-active' : ''; ?>">
                            <?php echo __("E-Mail, Certificate", $this->plugin_name);?>
                        </a>
                        <a href="#tab8" data-tab="tab8" class="nav-tab <?php echo ($ays_quiz_tab == 'tab8') ? 'nav-tab-active' : ''; ?>">
                            <?php echo __("Integrations", $this->plugin_name);?>
                        </a>
                    </div>  
                </div>
                <div class="ays_menu_right" data-scroll="-1"><i class="ays_fa ays_fa_angle_right"></i></div>
            </div>

            <?php
                for($tab_ind = 1; $tab_ind <= 8; $tab_ind++){
                    require_once( AYS_QUIZ_ADMIN_PATH . "/partials/quizes/actions/partials/quiz-maker-quizes-actions-tab".$tab_ind.".php" );
                }
            ?>

            <hr/>
            <?php
                echo $quiz_iframe_html;
                if( $owner ){
                    wp_nonce_field('quiz_action', 'quiz_action');
                    $other_attributes = array();
                    $other_attributes_only_save = array(
                        'title' => __('Ctrl + s', $this->plugin_name),
                        'data-toggle' => 'tooltip',
                        'data-delay'=> '{"show":"1000"}'
                    );

                    if( $action == 'edit' ){
                        $other_attributes['disabled'] = 'disabled';
                        $other_attributes_only_save['disabled'] = 'disabled';
                    }

                    $buttons_html = '';
                    $buttons_html .= '<div class="ays_save_buttons_content">';
                        $buttons_html .= '<div class="ays_save_buttons_box">';
                        echo $buttons_html;
                            submit_button(__('Save and close', $this->plugin_name), 'primary ays-quiz-loader-banner', 'ays_submit', true, $other_attributes);
                            submit_button(__('Save', $this->plugin_name), 'ays-quiz-loader-banner', 'ays_apply', true, $other_attributes_only_save);
                            echo $loader_iamge;
                        $buttons_html = '</div>';
                        $buttons_html .= '<div class="ays_save_default_button_box">';
                        echo $buttons_html;

                            if ( $prev_quiz_id != "" && !is_null( $prev_quiz_id ) ) {

                                $other_attributes = array(
                                    'id'            => 'ays-quiz-prev-button',
                                    'data-message'  => __( 'Are you sure you want to go to the previous quiz page?', $this->plugin_name),
                                    'href'          => sprintf( '?page=%s&action=%s&quiz=%d', esc_attr( $_REQUEST['page'] ), 'edit', absint( $prev_quiz_id ) )
                                );
                                submit_button(__('Prev Quiz', $this->plugin_name), 'primary ays-quiz-next-button-class', 'ays_quiz_prev_button', true, $other_attributes);
                            }

                            if ( $next_quiz_id != "" && !is_null( $next_quiz_id ) ) {

                                $other_attributes = array(
                                    'id'            => 'ays-quiz-next-button',
                                    'data-message'  => __( 'Are you sure you want to go to the next quiz page?', $this->plugin_name),
                                    'href'          => sprintf( '?page=%s&action=%s&quiz=%d', esc_attr( $_REQUEST['page'] ), 'edit', absint( $next_quiz_id ) )
                                );
                                submit_button(__('Next Quiz', $this->plugin_name), 'primary ays-quiz-next-button-class', 'ays_quiz_next_button', true, $other_attributes);
                            }
                            
                            $buttons_html = '<a class="ays_help" data-toggle="tooltip" title="'. __( 'Saves the assigned settings of the current quiz as default. After clicking on this button, each time creating a new quiz, the system will take the settings and styles of the current quiz. If you want to change and renew it, please click on this button on another quiz.', $this->plugin_name ) .'">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>';
                            echo $buttons_html;
                            $other_attributes = array( 'data-message' => __( 'Are you sure that you want to save these parameters as default?', $this->plugin_name ) . "\n" . __( "Note: All the default values will be replaced with the current quiz settings and will be applied to the newly created quizzes.", $this->plugin_name ) );
                            submit_button(__('Save as default', $this->plugin_name), 'primary ays_default_btn', 'ays_default', true, $other_attributes);
                        $buttons_html = '</div>';
                    $buttons_html .= "</div>";
                    echo $buttons_html;
                }
            ?>
        </form>
    </div>
</div>

<div id="ays-questions-modal" class="ays-modal">
    <!-- Modal content -->
    <div class="ays-modal-content">
        <form method="post" id="ays_add_question_rows">
            <div class="ays-quiz-preloader">
                <img src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/loaders/cogs.svg">
            </div>
            <div class="ays-modal-header">
                <span class="ays-close">&times;</span>
                <h2><?php echo __('Select questions', $this->plugin_name); ?></h2>
            </div>
            <div class="ays-modal-body">
                <?php
                // wp_nonce_field('add_question_rows_top', 'add_question_rows_top_second');
                $other_attributes = array();
                submit_button(__('Select questions', $this->plugin_name), 'primary', 'add_question_rows_top', true, $other_attributes);
                ?>
                <span style="font-size: 13px; font-style: italic;">
                    <?php echo __('For select questions click on question row and then click "Select questions" button', $this->plugin_name); ?>
                </span>
                <p style="font-size: 16px; padding-right:20px; margin:0; text-align:right;">
                    <a class="" href="admin.php?page=<?php echo $this->plugin_name; ?>-questions&action=add" target="_blank"><?php echo __('Create question', $this->plugin_name); ?></a>
                </p>
                <div class="row" style="margin:0;">
                    <div class="col-sm-12" id="quest_cat_container">
                        <label style="width:100%;" for="add_quest_category_filter">
                            <p style="font-size: 13px; margin:0; font-style: italic;">
                                <?php echo __( "Filter by category", $this->plugin_name); ?>
                                <button type="button" class="ays_filter_cat_clear button button-small wp-picker-default"><?php echo __( "Clear", $this->plugin_name ); ?></button>
                            </p>
                        </label>
                        <select id="add_quest_category_filter" multiple="multiple" class='cat_filter custom-select custom-select-sm form-control form-control-sm'>
                            <?php
                                $quiz_cats = $this->get_questions_categories();
                                foreach($quiz_cats as $cat){
                                    echo "<option value='". esc_attr( $cat['id'] ) ."'>". esc_attr( $cat['title'] ) ."</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <hr>
                <!-- Add Question Tag Filter Start -->
                <div class="row" style="margin:0;">
                    <div class="col-sm-12" id="quest_tag_container">
                        <label style="width:100%;" for="add_quest_tag_filter">
                            <p style="font-size: 13px; margin:0; font-style: italic;">
                                <?php echo __( "Filter by tag", $this->plugin_name); ?>
                                <button type="button" class="ays_filter_tag_clear button button-small wp-picker-default"><?php echo __( "Clear", $this->plugin_name ); ?></button>
                            </p>
                        </label>
                        <select id="add_quest_tag_filter" multiple="multiple" class='tag_filter custom-select custom-select-sm form-control form-control-sm'>
                            <?php
                                $quiz_tags = $this->get_questions_tags();
                                foreach($quiz_tags as $tag){
                                    echo "<option value='". esc_attr( $tag['id'] ) ."'>". esc_attr( $tag['title'] ) ."</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <!-- Add Question Tag Filter End  -->
                <hr>
                <div style="overflow-x:auto;">
                    <table class="ays-add-questions-table hover order-column" id="ays-question-table-add" data-page-length='5'>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo __('Question', $this->plugin_name); ?></th>
                            <th><?php echo __('Type', $this->plugin_name); ?></th>
                            <th style="width:250px;"><?php echo __('Created', $this->plugin_name); ?></th>
                            <th><?php echo __('Category', $this->plugin_name); ?></th>
                            <th><?php echo __('Tags', $this->plugin_name); ?></th>
                            <th><?php echo __('Used', $this->plugin_name); ?></th>
                            <th style="width:50px;">ID</th>
                        </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="ays-modal-footer" style="justify-content:flex-start;">
                <?php
                // wp_nonce_field('add_question_rows', 'add_question_rows');
                $other_attributes = array('id' => 'ays-button');
                submit_button(__('Select questions', $this->plugin_name), 'primary', 'add_question_rows', true, $other_attributes);
                ?>
            </div>
        </form>
    </div>
</div>
