<?php
global $wpdb;
$quiz_id = isset($_GET['quiz']) ? intval($_GET['quiz']) : null;
if($quiz_id === null){
    wp_redirect( admin_url('admin.php') . '?page=' . $this->plugin_name . '-results' );
}
$quizes_table = $wpdb->prefix . 'aysquiz_quizes';
$quizes_questions_table = $wpdb->prefix . 'aysquiz_questions';
$sql = "SELECT question_ids FROM {$quizes_table} WHERE id = {$quiz_id};";
$results = $wpdb->get_var( $sql);

$sql = "SELECT * FROM {$quizes_table} WHERE id =". $quiz_id;
$quiz = $wpdb->get_row( $sql, 'ARRAY_A' );

$tab_url = "?page=".$this->plugin_name."-each-result&quiz=$quiz_id&ays_result_tab=";
$tab_url_2 = "?page=".$this->plugin_name."-not-finished-results&quiz=$quiz_id&ays_result_tab=";
$tab_url_3 = "?page=".$this->plugin_name."-all-reviews&quiz=$quiz_id";

?>

<div class="wrap ays_each_results_table">
    <h1 class="wp-heading-inline" style="padding-left:15px;">
        <?php
        echo sprintf( '<a href="?page=%s" class="go_back"><span><i class="fa fa-long-arrow-left" aria-hidden="true"></i> %s</span></a>', $this->plugin_name."-results", __("Back to results", $this->plugin_name) );
        ?>
    </h1>
    <div style="display: flex; justify-content: space-between;">
        <h1 class="wp-heading-inline" style="padding-left:15px;">
            <?php
            echo __("Results for", $this->plugin_name) . " \"" . __(esc_html($quiz['title']), $this->plugin_name) . "\"";
            ?>
        </h1>
        <div class="question-action-butons" style="padding: 10px; display: inline-block;">
            <button type="button" class="button ays-export-answers-filters" data-type="xlsx" quiz-id="<?php echo $quiz_id ?>"><?php echo __('Export answers', $this->plugin_name); ?></button>
        </div>
    </div>

    <div class="ays-top-menu-wrapper">
        <div class="ays_menu_left" data-scroll="0"><i class="ays_fa ays_fa_angle_left"></i></div>
        <div class="ays-top-menu">
            <div class="nav-tab-wrapper ays-top-tab-wrapper">
                <a href="<?php echo $tab_url . "poststuff"; ?>" class="no-js nav-tab" ><?php echo __("Results", $this->plugin_name); ?></a>
                <a href="<?php echo $tab_url_2 . "not_finished_result"; ?>" class="no-js nav-tab nav-tab-active" ><?php echo __("Not Finished", $this->plugin_name); ?></a>
                <a href="<?php echo $tab_url . "statistics"; ?>" class="no-js nav-tab"><?php echo __("Statistics", $this->plugin_name); ?></a>
                <a href="<?php echo $tab_url . "questions"; ?>" class="no-js nav-tab"><?php echo __("Questions", $this->plugin_name); ?></a>
                <a href="<?php echo $tab_url . "quest_cat_stat"; ?>" class="no-js nav-tab"><?php echo __("Question category statistics", $this->plugin_name); ?></a>
                <a href="<?php echo $tab_url . "leaderboard"; ?>" class="no-js nav-tab"><?php echo __("Leaderboard", $this->plugin_name); ?></a>
                <a href="<?php echo $tab_url_3; ?>" class="no-js nav-tab"><?php echo __('Reviews',$this->plugin_name)?></a>

            </div>
        </div>
        <div class="ays_menu_right" data-scroll="-1"><i class="ays_fa ays_fa_angle_right"></i></div>
    </div>
    <div id="not_finished_result" class="ays-quiz-tab-content ays-quiz-tab-content-active">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <form method="post">
                      <?php
                        $this->not_finished_result_obj->prepare_items();
                        $this->not_finished_result_obj->display();
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
    <div id="ays-results-modal" class="ays-modal">
        <div class="ays-modal-content">
            <div class="ays-quiz-preloader">
                <img class="loader" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/loaders/3-1.svg">
            </div>
            <div class="ays-modal-header">
                <span class="ays-close" id="ays-close-results">&times;</span>
                <h2><?php echo __("Detailed report", $this->plugin_name); ?></h2>
            </div>
            <div class="ays-modal-body" id="ays-results-body">
            </div>
        </div>
    </div>
    <div class="ays-modal" id="export-answers-filters">
        <div class="ays-modal-content">
            <div class="ays-quiz-preloader">
                <img class="loader" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/loaders/3-1.svg">
            </div>
          <!-- Modal Header -->
            <div class="ays-modal-header">
                <span class="ays-close">&times;</span>
                <h2><?=__('Export Filter', $this->plugin_name)?></h2>
            </div>

          <!-- Modal body -->
            <div class="ays-modal-body">
                <form method="post" id="ays_export_answers_filter">
                    <div class="filter-col">
                        <label for="user_id-answers-filter"><?=__("Users", $this->plugin_name)?></label>
                        <button type="button" class="ays_userid_clear button button-small wp-picker-default"><?=__("Clear", $this->plugin_name)?></button>
                        <select name="user_id-select[]" id="user_id-answers-filter" multiple="multiple"></select>
                        <input type="hidden" name="quiz_id-answers-filter" id="quiz_id-answers-filter" value="<?php echo $quiz_id; ?>">
                    </div>
                    <div class="filter-block">
                        <div class="filter-block filter-col">
                            <label for="start-date-answers-filter"><?=__("Start Date from", $this->plugin_name)?></label>
                            <input type="date" name="start-date-filter" id="start-date-answers-filter">
                        </div>
                        <div class="filter-block filter-col">
                            <label for="end-date-answers-filter"><?=__("Start Date to", $this->plugin_name)?></label>
                            <input type="date" name="end-date-filter" id="end-date-answers-filter">
                        </div>
                    </div>
                </form>
            </div>

          <!-- Modal footer -->
            <div class="ays-modal-footer">
                <div class="export_results_count">
                    <p>Matched <span></span> results</p>
                </div>
                <span><?php echo __('Export to', $this->plugin_name); ?></span>
                <button type="button" class="button button-primary export-anwers-action" data-type="xlsx" quiz-id="<?php echo $quiz_id; ?>"><?=__('XLSX', $this->plugin_name)?></button>
                <a download="" id="downloadFile" hidden href=""></a>
            </div>

        </div>
    </div>
</div>
