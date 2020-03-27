<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/admin/partials
 */


?>
<div class="wrap">
    <div class="ays-quiz-maker-wrapper" style="position:relative;">
        <h1><?php echo __(esc_html(get_admin_page_title()),$this->plugin_name); ?> <i class="ays_fa ays_fa_heart_o animated"></i></h1>
        <button class="ays-pulse-button" id="ays_quick_start" title="Quick quiz" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="<?php echo __('Build your quiz in a few minutes',$this->plugin_name)?>"></button>
        <fieldset style="border:1px solid #ccc; padding:10px;width:fit-content; margin:0 auto;">
            <legend style="padding:0 10px;width:auto;text-align:center;font-size: 24px;margin:0;">
                <?php echo __( "Simple step by step", $this->plugin_name ); ?>
            </legend>
            <ol>
                <li>
                    <?php echo __("Create questions", $this->plugin_name ) . " <a target='_blank' href='admin.php?page=quiz-maker-questions&action=add'>" . __("here", $this->plugin_name )."</a>"; ?>.
                </li>
                <li>
                    <?php echo __("Create a quiz", $this->plugin_name ) . " <a target='_blank' href='admin.php?page=quiz-maker&action=add'>" . __("here", $this->plugin_name ) ."</a> " . __("and add questions to it.", $this->plugin_name ); ?>
                </li>
                <li>
                    <?php echo __("Copy and paste quiz shortcode into your post/page.", $this->plugin_name ); ?>
                </li>
            </ol>
        </fieldset>
        <hr/>
        <div id="ays-quick-modal" tabindex="-1" class="ays-modal">
            <!-- Modal content -->
            <div class="ays-modal-content fadeInDown" id="ays-quick-modal-content">
                <div class="ays-quiz-preloader">
                    <img src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/loaders/tail-spin.svg">
                </div>
                <div class="ays-modal-header">
                    <span class="ays-close">&times;</span>
                    <h4><?php echo __('Build your quiz in few seconds', $this->plugin_name); ?></h4>
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
                        <div class="ays-quick-questions-container">
                            <div class="ays-modal-flexbox">
                                <p class="ays_questions_title"><?php echo __('Questions',$this->plugin_name)?></p>
                                <a href="javascript:void(0)" class="ays_add_question">
                                    <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                                </a>
                            </div>
                            <hr/>
                            <div tabindex="0" class="ays_modal_element ays_modal_question">
                                <div class="ays_question_overlay"></div>
                                <p class="ays_question"><?php echo __('Question Default Title',$this->plugin_name)?></p>
                                <div class="ays-modal-flexbox flex-end">
                                    <table class="ays_answers_table">
                                        <tr>
                                            <td>
                                                <input type="radio" name="ays_answer_radio[1]" checked>
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
                                                <input type="radio" name="ays_answer_radio[1]">
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
                                                <input type="radio" name="ays_answer_radio[1]">
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
                                    <a href="javascript:void(0)" class="ays_trash_icon">
                                        <i class="ays_fa ays_fa_trash_o" aria-hidden="true"></i>
                                    </a>
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
                    </form>
                </div>
            </div>
        </div>
        <div class="ays-quiz-section">
            <div class="container ays-quiz-container">
                <div class="row">
                    <div class="col-lg-4 col-sm-6 ays-quiz-card animated">
                        <a href="javascript:document.getElementById('ays_quick_start').click()" >
                            <span><?php echo __('Quick Quiz', $this->plugin_name); ?></span>
                            <i class="ays_fa ays_fa_fighter_jet" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6 ays-quiz-card animated">
                        <a href="<?php echo admin_url('admin.php') . '?page=' . $this->plugin_name.'-questions'; ?>">
                            <span><?php echo __('Questions', $this->plugin_name); ?></span>
                            <i class="ays_fa ays_fa_question" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6 ays-quiz-card animated">
                        <a href="<?php echo admin_url('admin.php') . '?page=' . $this->plugin_name.'-question-categories'; ?>">
                            <span><?php echo __('Question Categories', $this->plugin_name); ?></span>
                            <i class="ays_fa ays_fa_list_ul" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6 ays-quiz-card animated">
                        <a href="<?php echo admin_url('admin.php') . '?page=' . $this->plugin_name; ?>">
                            <span><?php echo __('Quizzes', $this->plugin_name); ?></span>
                            <i class="ays_fa ays_fa_check_square" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6 ays-quiz-card animated">
                        <a href="<?php echo admin_url('admin.php') . '?page=' . $this->plugin_name.'-quiz-categories'; ?>">
                            <span><?php echo __('Quiz Categories', $this->plugin_name); ?></span>
                            <i class="ays_fa ays_fa_th_list" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6 ays-quiz-card animated">
                        <a href="<?php echo admin_url('admin.php') . '?page=' . $this->plugin_name.'-results'; ?>">
                            <span><?php echo __('Results', $this->plugin_name); ?></span>
                            <i class="ays_fa ays_fa_bar_chart" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6 ays-quiz-card animated">
                        <a href="<?php echo admin_url('admin.php') . '?page=' . $this->plugin_name.'-quiz-attributes'; ?>">
                            <span><?php echo __('Attributes', $this->plugin_name); ?></span>
                            <i class="ays_fa ays_fa_puzzle_piece" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6 ays-quiz-card animated">
                        <a href="<?php echo admin_url('admin.php') . '?page=' . $this->plugin_name.'-quiz-orders'; ?>">
                            <span><?php echo __('Orders', $this->plugin_name); ?></span>
                            <i class="ays_fa ays_fa_money" aria-hidden="true"></i>
                        </a>
                    </div>
                    <?php
                        if(is_user_logged_in()){
                            $current_user = wp_get_current_user();
                            $current_user_roles = $current_user->roles;
                            if(in_array('administrator', $current_user_roles)){
                    ?>
                    <div class="col-lg-4 col-sm-6 ays-quiz-card animated">
                        <a href="<?php echo admin_url('admin.php') . '?page=' . $this->plugin_name.'-settings'; ?>">
                            <span><?php echo __('General Settings', $this->plugin_name); ?></span>
                            <i class="ays_fa ays_fa_cogs" aria-hidden="true"></i>
                        </a>
                    </div>
                    <?php                                
                            }
                        }
                    ?>
                    <div class="col-lg-4 col-sm-6 ays-quiz-card animated">
                        <a href="<?php echo admin_url('admin.php') . '?page=' . $this->plugin_name.'-featured-plugins'; ?>">
                            <span><?php echo __('Featured Plugins', $this->plugin_name); ?></span>
                            <i class="ays_fa ays_fa_diamond" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
