<?php
global $wpdb;
if(isset($_GET['ays_result_tab'])){
    $tab = $_GET['ays_result_tab'];
}else{
    $tab = 'poststuff';
}

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
    $url = esc_url_raw( remove_query_arg( array( 'page', 'quiz' ) ) ) . "?page=quiz-maker-results";
    wp_redirect( $url );
}


$questions_ids = array();
$questions_counts = array();
$questions_list = array();
if($results != ''){
    $sql = "SELECT id, question FROM {$quizes_questions_table} WHERE id IN ({$results});";
    $test = $wpdb->get_results($sql, "ARRAY_A");
    foreach($test as $k => $v){
        $questions_list[$v['id']] = $v['question'];
        $questions_ids[$v['id']] = 0;
        $questions_counts[$v['id']] = 0;
    }

    $results = explode("," , $results);
    $questions_ids = Quiz_Maker_Data::sort_array_keys_by_array_for_id_keys( $questions_ids, $results );
}

$quizes_reports_table = $wpdb->prefix . 'aysquiz_reports';
$sql = "SELECT options FROM {$quizes_reports_table} WHERE quiz_id ={$quiz_id} AND `status` = 'finished';";
$report = $wpdb->get_results( $sql, ARRAY_A );
if(! empty($report)){
    foreach ($report as $key){
        $report = json_decode($key["options"]);
        $questions = $report->correctness;
        foreach ($questions as $i => $v){
            $q = (int) substr($i,12);
            if(isset($questions_ids[$q])) {
                if ($v) {
                    $questions_ids[$q]++;
                }

                $questions_counts[$q]++;
            }
        }
    }
}

$not_finished_res_url = menu_page_url("quiz-maker", false) . '-not-finished-results';
$not_finished_res_url = add_query_arg( array(
    'quiz' => $quiz_id
), $not_finished_res_url );
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
                <a href="#poststuff" class="nav-tab <?php echo ($tab == 'poststuff') ? 'nav-tab-active' : ''; ?>" ><?php echo __("Results", $this->plugin_name); ?></a>
                <a href="<?php echo $not_finished_res_url; ?>" class="no-js nav-tab" ><?php echo __("Not Finished", $this->plugin_name); ?></a>
                <a href="#statistics" class="nav-tab <?php echo ($tab == 'statistics') ? 'nav-tab-active' : ''; ?>"><?php echo __("Statistics", $this->plugin_name); ?></a>
                <a href="#questions" class="nav-tab <?php echo ($tab == 'questions') ? 'nav-tab-active' : ''; ?>"><?php echo __("Questions", $this->plugin_name); ?></a>
                <a href="#quest_cat_stat" class="nav-tab <?php echo ($tab == 'quest_cat_stat') ? 'nav-tab-active' : ''; ?>"><?php echo __("Question category statistics", $this->plugin_name); ?></a>
                <a href="#leaderboard" class="nav-tab <?php echo ($tab == 'leaderboard') ? 'nav-tab-active' : ''; ?>"><?php echo __("Leaderboard", $this->plugin_name); ?></a>
                <a href="<?php echo "?page=".$this->plugin_name."-all-reviews&quiz=$quiz_id"; ?>" class="no-js nav-tab <?php echo ($tab == '6') ? 'nav-tab-active' : ''; ?>"><?php echo __('Reviews',$this->plugin_name)?></a>
            </div>
        </div>
        <div class="ays_menu_right" data-scroll="-1"><i class="ays_fa ays_fa_angle_right"></i></div>
    </div>
    <div id="poststuff" class="ays-quiz-tab-content <?php echo ($tab == 'poststuff') ? 'ays-quiz-tab-content-active' : ''; ?>">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <?php
                        $this->each_result_obj->views();
                    ?>
                    <form method="post">
                      <?php
                        $this->each_result_obj->prepare_items();
                        $this->each_result_obj->search_box('Search', $this->plugin_name);
                        $this->each_result_obj->display();
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
    <div id="statistics" class="ays-quiz-tab-content <?php echo ($tab == 'statistics') ? 'ays-quiz-tab-content-active' : ''; ?>">
        <div class="wrap">
           <h1 style="text-align:center;"><?php echo __("Reports count per day", $this->plugin_name); ?></h1>
           <div id="chart1_div" class="chart_div"></div>
        </div>
        <div class="row wrap">
            <div class="col-md-6">
                <h1 style="text-align:center;"><?php echo __("Correct answers", $this->plugin_name); ?></h1>
                <div id="chart2_div" class="chart_div"></div>
            </div>
            <div class="col-md-6 ays_divider_left">
                <h1 style="text-align:center;"><?php echo __("Quiz passed users", $this->plugin_name); ?></h1>
                <div id="chart_quiz_div" class="chart_div"></div>
            </div>
        </div>
        <hr>
        <div class="wrap">
           <h1 style="text-align:center;"><?php echo __("Users count by score", $this->plugin_name); ?></h1>
           <div id="chart3_div" class="chart_div"></div>
        </div>
        <hr>
        <div class="wrap">
           <h1 style="text-align:center;"><?php echo __("Users count by interval", $this->plugin_name); ?></h1>
           <div id="chart4_div" class="chart_div"></div>
        </div>

    </div>
    <div id="questions" class="ays-quiz-tab-content <?php echo ($tab == 'questions') ? 'ays-quiz-tab-content-active' : ''; ?>">
        <div class="row">
            <div class="question-action-butons" style="padding: 10px; width: 100%;">
                <button type="button" class="button button-primary ays-export-questions-statistics" data-type="xlsx" quiz-id="<?php echo $quiz_id ?>"><?php echo __('Export', $this->plugin_name); ?></button>
            </div>
            <div class="col-sm-12" style="padding: 15px;">
                <table class="table table-hover table-striped ays_each_results_question_table" border="1">
                    <thead>
                    <tr>
                        <th style="width: 35%;"><span><?php echo __("Question", $this->plugin_name); ?></span></th>
                        <th style="width: 35%;"><span><?php echo __("Correctness", $this->plugin_name); ?></span></th>
                        <th style="width: 20%;"><span><?php echo __("Amount of correct", $this->plugin_name); ?></span></th>
                        <th style="width: 10%;"><span><?php echo __("ID", $this->plugin_name); ?></span></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($questions_ids as $n => $a){
                            if ($a != 0 ||  $questions_counts[$n] != 0){
                                $score = round($a/$questions_counts[$n]*100, 1);
                            }else {
                                $score = 0;
                            }

                            echo "<tr>
                                    <td><span>".stripslashes($questions_list[$n])."</span></td>
                                    <td>
                                        <div class=\"progress\">
                                          <div class=\"progress-bar progress-bar-striped progress-bar-animated bg-success\" role=\"progressbar\" style=\"width: ".$score."%\" aria-valuenow=\"25\" aria-valuemin=\"0\" aria-valuemax=\"100\">".$score."%</div>
                                        </div>
                                    </td>
                                    <td><span>".$a."/".$questions_counts[$n]."</span></td>
                                    <td><span>".$n."</span></td>
                                </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="quest_cat_stat" class="ays-quiz-tab-content <?php echo ($tab == 'quest_cat_stat') ? 'ays-quiz-tab-content-active' : ''; ?>">
        <div class="wrap">
           <h1 style="text-align:center;"><?php echo __("Category right answers by percent", $this->plugin_name); ?></h1>
           <div id="chart5_div" class="chart_div"></div>
        </div>
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

<?php
    $data = array();
    $d = $this->each_result_obj->quiz_count_by_days($quiz_id);
    if( !empty( $d ) ){
        if( isset( $d[0] ) && !empty( $d[0] ) ){
            $old = strtotime( $d[0]['date'] );
            foreach($d as $date){
                $curDate = strtotime($date['date']);
                if ($curDate < $old) {
                    $old = $curDate;
                }
            }
            $now = date("Y-m-d", current_time('timestamp'));
            $diff = $old - current_time('timestamp');
            $days = abs(round($diff / 86400));
            for( $i = $days; $i >= 0; $i-- ){
                $day = date('Y-m-d', strtotime( "today - $i days", current_time('timestamp') ) );
                $val = 0;
                foreach($d as $value){
                    if($day == $value['date']){
                        $val = $value['value'];
                        break;
                    }
                }
                array_push($data,array('date'=>$day,'value'=>$val));
            }
        }
    }
?>
<script>
    var chart1_data = <?php echo json_encode($data); ?>;
    var chart2_data = <?php echo json_encode($this->each_result_obj->quiz_each_question_correct_answers($quiz_id)); ?>;
    var chart3_data = <?php echo json_encode(Quiz_Each_Results_List_Table::users_count()); ?>;
    var chart4_data = <?php echo json_encode(Quiz_Each_Results_List_Table::users_count_by_score()); ?>;
    var chart5_data = <?php echo json_encode(Quiz_Each_Results_List_Table::question_category_statistics()); ?>;
</script>
