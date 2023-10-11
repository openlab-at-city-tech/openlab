<?php
global $wpdb;
// if(isset($_GET['ays_result_tab'])){
//     $tab = $_GET['ays_result_tab'];
// }else{
// }
$tab = 'leaderboard';

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

$quiz_trash_status = (isset( $quiz['published'] ) && absint( $quiz['published'] ) == 2 ) ? true : false;

$user_id = get_current_user_id();
$author_id = intval( $quiz['author_id'] );
$owner = false;
if( $user_id == $author_id ){
    $owner = true;
}

if( $this->current_user_can_edit ){
    $owner = true;
}

if( !$owner ){
    $url = esc_url_raw( remove_query_arg( array( 'page', 'quiz', 'ays_result_tab' ) ) ) . "?page=quiz-maker-results";
    wp_redirect( $url );
}

if( $quiz_trash_status ){
    $url = esc_url_raw( remove_query_arg( array( 'page', 'quiz', 'ays_result_tab' ) ) ) . "?page=quiz-maker-results";
    wp_redirect( $url );
}

$quiz_each_results_tab                = "?page=".$this->plugin_name."-each-result&quiz=$quiz_id";
$quiz_each_not_finished_tab           = "?page=".$this->plugin_name."-not-finished-results&quiz=$quiz_id";
$quiz_each_results_statistics_tab     = "?page=".$this->plugin_name."-each-result-statistics&quiz=$quiz_id";
$quiz_each_results_questions_tab      = "?page=".$this->plugin_name."-each-result-questions&quiz=$quiz_id";
$quiz_each_results_quest_cat_stat_tab = "?page=".$this->plugin_name."-each-result-question-category-statistics&quiz=$quiz_id";
$quiz_each_results_leaderboard_tab    = "?page=".$this->plugin_name."-each-result-leaderboard&quiz=$quiz_id";
$quiz_each_results_reviews_tab        = "?page=".$this->plugin_name."-all-reviews&quiz=$quiz_id";

?>

<div class="wrap ays-quiz-list-table ays_each_results_table">
    <div class="ays-quiz-heading-box">
        <div class="ays-quiz-wordpress-user-manual-box">
            <a href="https://ays-pro.com/wordpress-quiz-maker-user-manual" target="_blank"><?php echo __("View Documentation", $this->plugin_name); ?></a>
        </div>
    </div>
    <h1 class="wp-heading-inline" style="padding-left:15px;">
        <?php
        echo sprintf( '<a href="?page=%s" class="go_back"><span><i class="fa fa-long-arrow-left" aria-hidden="true"></i> %s</span></a>', $this->plugin_name."-results", __("Back to results", $this->plugin_name) );
        ?>
    </h1>
    <div style="display: flex; justify-content: space-between;">
        <h1 class="wp-heading-inline" style="padding-left:15px;">
            <?php
            echo __("Results for", $this->plugin_name) . " \"" . __(esc_html( stripslashes( $quiz['title'] ) ), $this->plugin_name) . "\"";
            ?>
        </h1>
        <div class="question-action-butons" style="padding: 10px; display: inline-block;">
            <button type="button" class="button ays-export-answers-filters" data-type="xlsx" quiz-id="<?php echo $quiz_id ?>"><?php echo __('Export results', $this->plugin_name); ?></button>
        </div>
    </div>

    <div class="ays-top-menu-wrapper">
        <div class="ays_menu_left" data-scroll="0"><i class="ays_fa ays_fa_angle_left"></i></div>
        <div class="ays-top-menu">
            <div class="nav-tab-wrapper ays-top-tab-wrapper">
                <a href="<?php echo $quiz_each_results_leaderboard_tab; ?>" class="no-js nav-tab <?php echo ($tab == 'poststuff') ? 'nav-tab-active' : ''; ?>" ><?php echo __("Results", $this->plugin_name); ?></a>
                <a href="<?php echo $quiz_each_not_finished_tab; ?>" class="no-js nav-tab" ><?php echo __("Not Finished", $this->plugin_name); ?></a>
                <a href="<?php echo $quiz_each_results_statistics_tab; ?>" class="no-js nav-tab <?php echo ($tab == 'statistics') ? 'nav-tab-active' : ''; ?>"><?php echo __("Statistics", $this->plugin_name); ?></a>
                <a href="<?php echo $quiz_each_results_questions_tab; ?>" class="no-js nav-tab <?php echo ($tab == 'questions') ? 'nav-tab-active' : ''; ?>"><?php echo __("Questions", $this->plugin_name); ?></a>
                <a href="<?php echo $quiz_each_results_quest_cat_stat_tab; ?>" class="no-js nav-tab <?php echo ($tab == 'quest_cat_stat') ? 'nav-tab-active' : ''; ?>"><?php echo __("Question category statistics", $this->plugin_name); ?></a>
                <a href="#leaderboard" class="no-js nav-tab <?php echo ($tab == 'leaderboard') ? 'nav-tab-active' : ''; ?>"><?php echo __("Leaderboard", $this->plugin_name); ?></a>
                <a href="<?php echo $quiz_each_results_reviews_tab; ?>" class="no-js nav-tab <?php echo ($tab == '6') ? 'nav-tab-active' : ''; ?>"><?php echo __('Reviews',$this->plugin_name)?></a>
            </div>
        </div>
        <div class="ays_menu_right" data-scroll="-1"><i class="ays_fa ays_fa_angle_right"></i></div>
    </div>
    <div id="leaderboard" class="ays-quiz-tab-content <?php echo ($tab == 'leaderboard') ? 'ays-quiz-tab-content-active' : ''; ?>">
        <p class="ays-subtitle"><?php echo __('Leaderboard',$this->plugin_name)?></p>
        <hr>
        <?php
            global $wpdb;
            $sql = "SELECT quiz_id, user_id, user_email, AVG(CAST(duration AS DECIMAL(10))) AS dur_avg, AVG(CAST(score AS DECIMAL(10))) AS avg_score
                    FROM {$wpdb->prefix}aysquiz_reports
                    WHERE quiz_id = {$quiz_id} AND user_id != 0
                    GROUP BY user_id
                    ORDER BY avg_score DESC, dur_avg
                    LIMIT  10";
            $result = $wpdb->get_results($sql, 'ARRAY_A');

            $c = 1;
            $content = "<div class='ays_lb_container'>
            <ul class='ays_lb_ul' style='width: 100%;'>
                <li class='ays_lb_li'>
                    <div class='ays_lb_pos'>Pos.</div>
                    <div class='ays_lb_user'>".__("Name", $this->plugin_name)."</div>
                    <div class='ays_lb_score'>".__("Score", $this->plugin_name)."</div>
                    <div class='ays_lb_duration'>".__("Duration", $this->plugin_name)."</div>
                </li>";

            foreach ($result as $val) {
                $score = round($val['avg_score'], 2);
                $user = get_user_by('id', $val['user_id']);
                if ($user !== false) {
                    $user_name = $user->data->display_name ? $user->data->display_name : $user->user_login;

                    $duration = (isset($val['dur_avg']) && $val['dur_avg'] != '') ? round(floatval($val['dur_avg']), 2) : '0';

                    $content .= "<li class='ays_lb_li'>
                                    <div class='ays_lb_pos'>".$c.".</div>
                                    <div class='ays_lb_user'>".$user_name."</div>
                                    <div class='ays_lb_score'>".$score." %</div>
                                    <div class='ays_lb_duration'>".$duration."s</div>
                                </li>";
                    $c++;
                }
            }
            $content .= "</ul>
            </div>";
            echo $content;
        ?>
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
                   <div class="filter-row">
                        <div class="filter-row-overlay display_none"></div>
                        <div class="filter-col">
                            <label for="user_id-answers-filter"><?=__("Users", $this->plugin_name)?></label>
                            <button type="button" class="ays_userid_clear button button-small wp-picker-default"><?=__("Clear", $this->plugin_name)?></button>
                            <select name="user_id-select[]" id="user_id-answers-filter" multiple="multiple"></select>
                            <input type="hidden" name="quiz_id-answers-filter" id="quiz_id-answers-filter" value="<?php echo $quiz_id; ?>">
                        </div>
                        <div class="filter-col">
                           <div style="padding: 10px;line-height:1;">
                                <input type="checkbox" name="export_answers_guests" id="export_answers_guests" value="on">
                                <label for="export_answers_guests">
                                    <span><?php echo __( "Include guests who do not have any personal data", $this->plugin_name ); ?></span>
                                </label>
                                <br>
                                <span style="font-style: italic; font-size:14px;"><?php echo __( "Include those not logged in users who have not inserted any personal data such as name and email.", $this->plugin_name ); ?></span>
                           </div>
                        </div>
                    </div>
                    <hr>
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
                    <hr>
                    <div class="filter-col">
                       <div style="padding: 10px;line-height:1;">
                            <input type="checkbox" name="export_answers_only_guests" id="export_answers_only_guests" value="on">
                            <label for="export_answers_only_guests">
                                <span><?php echo __( "Export only guests results", $this->plugin_name ); ?></span>
                            </label>
                            <br>
                            <span style="font-style: italic; font-size:14px;"><?php echo __( "Please note that if this checkbox is ticked, the above filters (except dates) will not be applied.", $this->plugin_name ); ?></span>
                       </div>
                    </div>
                </form>
            </div>

          <!-- Modal footer -->
            <div class="ays-modal-footer">
                <div class="export_results_count">
                    <p><?php echo __( "Matched", $this->plugin_name ); ?> <span></span> <?php echo __( "results", $this->plugin_name ); ?></p>
                </div>
                <span><?php echo __('Export to', $this->plugin_name); ?></span>
                <button type="button" class="button button-primary export-anwers-action" data-type="xlsx" quiz-id="<?php echo $quiz_id; ?>"><?=__('XLSX', $this->plugin_name)?></button>
                <a download="" id="downloadFile" hidden href=""></a>
            </div>

        </div>
    </div>

</div>