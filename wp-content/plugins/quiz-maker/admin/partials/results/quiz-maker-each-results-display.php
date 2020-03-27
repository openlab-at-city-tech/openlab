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
$questions_ids = array();
$questions_counts = array();
$questions_list = array();
if($results != ''){
    $results = explode("," , $results);
    foreach ($results as $key){
        $questions_ids[$key] = 0;
        $questions_counts[$key] = 0;
        $sql = "SELECT question FROM {$quizes_questions_table} WHERE id = {$key} ; ";
        $questions_list[$key] = $wpdb->get_var( $sql);
    }
}

$quizes_reports_table = $wpdb->prefix . 'aysquiz_reports';
$sql = "SELECT options FROM {$quizes_reports_table} WHERE quiz_id ={$quiz_id};";
$report = $wpdb->get_results( $sql, ARRAY_A );
if(! empty($report)){
    foreach ($report as $key){
        $report = json_decode($key["options"]);
        $questions = $report->correctness;
        foreach ($questions as $i => $v){
            $q = (int) substr($i ,12);
            if(isset($questions_ids[$q])) {
                if ($v) {
                    $questions_ids[$q]++;
                }

                $questions_counts[$q]++;
            }
        }
    }
}

Quiz_Each_Results_List_Table::users_count_by_score();

$sql = "SELECT * FROM {$wpdb->prefix}aysquiz_quizes WHERE id =". $quiz_id;
$quiz_name = $wpdb->get_row( $sql, 'ARRAY_A' );

?>

<div class="wrap ays_each_results_table">
    <h1 class="wp-heading-inline" style="padding-left:15px;">
        <?php
        echo sprintf( '<a href="?page=%s" class="go_back"><span><i class="fa fa-long-arrow-left" aria-hidden="true"></i> %s</span></a>', $this->plugin_name."-results", __("Back to results", $this->plugin_name) );
        ?>
    </h1>
    <h1 class="wp-heading" style="padding-left:15px;">
        <?php
        echo __("Results for", $this->plugin_name) . " \"" . __(esc_html($quiz_name['title']), $this->plugin_name) . "\"";
        ?>
    </h1>
    <div class="nav-tab-wrapper">
        <a href="#poststuff" class="nav-tab nav-tab-active" ><?php echo __("Results", $this->plugin_name); ?></a>
        <a href="#statistics" class="nav-tab "><?php echo __("Statistics", $this->plugin_name); ?></a>
        <a href="#questions" class="nav-tab "><?php echo __("Questions", $this->plugin_name); ?></a>
        <a href="#leaderboard" class="nav-tab "><?php echo __("Leaderboard", $this->plugin_name); ?></a>
    </div>
    <div id="poststuff" class="ays-quiz-tab-content ays-quiz-tab-content-active">
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
    <div id="statistics" class="ays-quiz-tab-content">
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
    <div id="questions" class="ays-quiz-tab-content">
        <div class="row">
            <div class="col-sm-12" style="padding: 15px;">
                <table class="table table-hover table-striped" border="1">
                    <thead>
                    <tr>
                        <th style="width: 40%;"><span><?php echo __("Question", $this->plugin_name); ?></span></th>
                        <th style="width: 40%;"><span><?php echo __("Correctness", $this->plugin_name); ?></span></th>
                        <th  style="width: 20%;"><span><?php echo __("Amount of correct", $this->plugin_name); ?></span></th>
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
                                </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="leaderboard" class="ays-quiz-tab-content">
        <p class="ays-subtitle"><?php echo __('Leaderboard',$this->plugin_name)?></p>
        <hr>
        <?php
            global $wpdb;
            $sql = "SELECT quiz_id, user_id, AVG(score) AS avg_score
                    FROM {$wpdb->prefix}aysquiz_reports
                    WHERE user_id != 0 AND quiz_id = {$quiz_id}
                    GROUP BY user_id
                    ORDER BY avg_score DESC LIMIT 10";
            $result = $wpdb->get_results($sql, 'ARRAY_A');

            $c = 1;
            $content = "<div class='ays_lb_container'>
            <ul class='ays_lb_ul' style='width: 100%;'>
                <li class='ays_lb_li'>
                    <div class='ays_lb_pos'>Pos.</div>
                    <div class='ays_lb_user'>Name</div>
                    <div class='ays_lb_score'>Score</div>
                </li>";

            foreach ($result as $val) {
                $score = round($val['avg_score'], 2);
                $user = get_user_by('id', $val['user_id']);
                $user_name = $user->data->display_name ? $user->data->display_name : $user->user_login;

                $content .= "<li class='ays_lb_li'>
                                <div class='ays_lb_pos'>".$c.".</div>
                                <div class='ays_lb_user'>".$user_name."</div>
                                <div class='ays_lb_score'>".$score." %</div>
                            </li>";
                $c++;
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
</div>

<?php
    $data = array();
    $d =  $this->each_result_obj->quiz_count_by_days($quiz_id);
    $old= $d[0]['date'];
    foreach($d as $date){
        $curDate = strtotime($date['date']);
        if ($curDate < $old) {
            $old = $curDate;
        }
    }
    $now = date("Y-m-d", current_time('timestamp'));
    $diff = strtotime($old) - strtotime($now);
    $days = abs(round($diff / 86400));
    for($i = $days;$i>=0;$i-- ){
        $day =  date('Y-m-d', strtotime( "today - $i days", current_time('timestamp') ) );
        $val = 0;
        foreach($d as $value){
            if($day == $value['date']){
                $val = $value['value'];
                break;
            }
        }
        array_push($data,array('date'=>$day,'value'=>$val));
    }
?>
<?php ($this->each_result_obj->quiz_each_question_correct_answers($quiz_id)); ?>
<script>
    var chart1_data = <?php echo json_encode($data); ?>;
    var chart2_data = <?php echo json_encode($this->each_result_obj->quiz_each_question_correct_answers($quiz_id)); ?>;
    var chart3_data = <?php echo json_encode(Quiz_Each_Results_List_Table::users_count()); ?>;
    var chart4_data = <?php echo json_encode(Quiz_Each_Results_List_Table::users_count_by_score()); ?>;
</script>
