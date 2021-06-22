<?php

$tab = isset($_GET['tab']) ? $_GET['tab'] : '1';
$tab_url = "?page=".$this->plugin_name."-results&tab=";

$popular_quizzes = $this->results_obj->get_quizzes_for_chart();
$most_popular = stripslashes($popular_quizzes['most_popular']['title']);
$least_popular = stripslashes($popular_quizzes['least_popular']['title']);


$user_id = get_current_user_id();
// $author_id = intval( $quiz['author_id'] );
// $owner = false;
// if( $user_id == $author_id ){
//     $owner = true;
// }

// if( current_user_can( 'manage_options' ) ){
//     $owner = true;
// }

// if( !$owner ){
//     $url = esc_url_raw( remove_query_arg( array( 'page', 'quiz' ) ) ) . "?page=quiz-maker-results";
//     wp_redirect( $url );
// }

?>
<div class="wrap ays_results_table">
    <h1 class="wp-heading-inline">
        <?php
        echo __(esc_html(get_admin_page_title()),$this->plugin_name);
        ?>
    </h1>
    <div class="question-action-butons">
        <a href="javascript:void(0)" class="ays-export-filters ays_export_results page-title-action" style="float: right;"><?php echo __('Export', $this->plugin_name); ?></a>
    </div>
    <div class="nav-tab-wrapper">
        <a href="#tab1" class="nav-tab <?php echo ($tab == '1') ? 'nav-tab-active' : ''; ?>"><?php echo __('Quizzes',$this->plugin_name)?></a>
        <a href="#tab2" class="nav-tab <?php echo ($tab == '2') ? 'nav-tab-active' : ''; ?>"><?php echo __('Global Statistics',$this->plugin_name)?></a>
        <a href="#tab3" class="nav-tab <?php echo ($tab == '3') ? 'nav-tab-active' : ''; ?>"><?php echo __('Global Leaderboard',$this->plugin_name)?></a>
        <a href="<?php echo "?page=".$this->plugin_name."-all-results"; ?>" class="no-js nav-tab <?php echo ($tab == '4') ? 'nav-tab-active' : ''; ?>"><?php echo __('All Results',$this->plugin_name)?></a>
    </div>
    <style>
        .column-quiz_rate,
        .column-score,
        .column-unreads,
        .column-id,
        .column-google_sheet,
        .column-res_count,
        .column-user_count {
            text-align: center !important;
        }
        .column-id a {
            display: inline-block !important;
            padding: 5px 70px;
        }
    </style>
    <div id="tab1" class="ays-quiz-tab-content <?php echo ($tab == '1') ? 'ays-quiz-tab-content-active' : ''; ?>">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <form method="post">
                            <?php
                                $this->results_obj->prepare_items();
                                $this->results_obj->display();
                            ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="tab2" class="ays-quiz-tab-content <?php echo ($tab == '2') ? 'ays-quiz-tab-content-active' : ''; ?>">
        <div class="ays-field ays_quiz_stat_select_div" style="width:25%; margin-top: 20px;">
            <?php
                $quizzes = $this->results_obj->get_reports_titles();
                $data = Quiz_Maker_Admin::get_quiz_statistic_by_id( 0 );
                $dates_values = json_encode($data['values']);
                $dates = json_encode($data['dates']);
            ?>
            <select name="quiz_stat" id="quiz_stat_select">
                <option value="0"><?php echo __( "All Quizzes", $this->plugin_name ); ?></option>
                <?php foreach ($quizzes as $quiz) {
                    echo "<option value = '" . $quiz['id'] . "' >" . stripslashes($quiz['title']) . "</option >";
                } ?>
            </select>
            <img class="loader display_none" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/loaders/gear.svg" style="width:30px;">
        </div>
        <hr/>
        <div class="charts" style="display:flex;">
            <div class="ays-field-dashboard" style="width:65%; margin-right:10px;">
                <h1><?php echo __('Quiz Statistics',$this->plugin_name)?></h1>
                <hr/>
                <div>
                    <p style="margin:0;margin-bottom: -15px;text-align:center;">
                        <span><?php echo __( "Number of Completes", $this->plugin_name ); ?></span>
                    </p>
                    <div id="chart_quizzes_div" class="chart_div"></div>
                </div>
                <script>
                    var chartQuizzesData = <?php echo json_encode($data); ?>;
                </script>
            </div>
            <div class="ays-field-dashboard" style="width:35%;">
                <h1><?php echo __('Statistics Signboard',$this->plugin_name)?></h1>
                <hr/>
                <ul class="ays-collection">
                    <li class="ays-collection-item">
                        <div class="stat-left-div">
                            <p class="stat-active"><?php echo $most_popular; ?></p>
                            <span class="stat-description"><?php echo __('Most popular quiz',$this->plugin_name)?></span>
                        </div>
                        <div class="stat-left-div" >
                            <p class="stat-active"><?php echo $least_popular; ?></p>
                            <span class="stat-description"><?php echo __('Least popular quiz',$this->plugin_name)?></span>
                        </div>
                    </li>
                    <?php
                    $statistics_items = array(1,7,25,30,120);
                    foreach ($statistics_items as $statistics_item){
                        $img = '';
                        $element = $this->results_obj->get_quizzes_count_by_days($statistics_item);
                        $diff = $element['difference'];
                        if($diff < 0){
                            $img = '<img src="' . AYS_QUIZ_ADMIN_URL . '/images/down_red_arrow.png" alt="Down">';
                        }elseif ($diff > 0){
                            $img = '<img src="' . AYS_QUIZ_ADMIN_URL . '/images/up_green_arrow.png" alt="Up">';
                        }else{
                            $img = '<img src="' . AYS_QUIZ_ADMIN_URL . '/images/equal.png" alt="Equal">';
                        }
                        echo "<li class=\"ays-collection-item\">
                            <div class=\"stat-left-div\">
                                <p class=\"stat-count\"> ".$element['quizzes_count']."</p>
                                <span class=\"stat-description\">".__('quizzes taken last',$this->plugin_name). " $statistics_item " .__('day',$this->plugin_name)."</span>
                            </div>
                            <div class=\"stat-right-div\">
                                <p class=\"stat-diff-count\">".$element['difference']."%</p>
                                ".$img."
                            </div>
                        </li>";
                    }
                    ?>

                </ul>
            </div>
        </div>
    </div>

    <div id="tab3" class="ays-quiz-tab-content <?php echo ($tab == '3') ? 'ays-quiz-tab-content-active' : ''; ?>">
        <p class="ays-subtitle"><?php echo __('Leaderboard',$this->plugin_name)?></p>
        <hr>
        <?php
            global $wpdb;
            if( ! $this->current_user_can_edit ){
                $author_id_str = ' AND q.author_id = ' . $user_id;
            } else {
                $author_id_str = '';
            }

            $sql = "SELECT r.quiz_id, r.user_id, AVG(CAST(r.duration AS DECIMAL(10))) AS dur_avg, AVG(CAST(r.`score` AS DECIMAL(10))) AS avg_score
                    FROM {$wpdb->prefix}aysquiz_reports r
                    LEFT JOIN {$wpdb->prefix}aysquiz_quizes q
                        ON r.quiz_id = q.id 
                    WHERE r.user_id != 0 {$author_id_str}
                    GROUP BY r.user_id
                    ORDER BY avg_score DESC, dur_avg
                    LIMIT 10";
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

    <div class="ays-modal" id="export-filters">
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
                <form method="post" id="ays_export_filter">
                    <div class="filter-col">
                        <label for="user_id-filter"><?=__("Users", $this->plugin_name)?></label>
                        <button type="button" class="ays_userid_clear button button-small wp-picker-default"><?=__("Clear", $this->plugin_name)?></button>
                        <select name="user_id-select[]" id="user_id-filter" multiple="multiple"></select>
                    </div>
                    <hr>
                    <div class="filter-col">
                        <label for="quiz_id-filter"><?=__("Quizzes", $this->plugin_name)?></label>
                        <button type="button" class="ays_quizid_clear button button-small wp-picker-default"><?=__("Clear", $this->plugin_name)?></button>
                        <select name="quiz_id-select[]" id="quiz_id-filter" multiple="multiple"></select>
                    </div>
                    <div class="filter-block">
                        <div class="filter-block filter-col">
                            <label for="start-date-filter"><?=__("Start Date from", $this->plugin_name)?></label>
                            <input type="date" name="start-date-filter" id="start-date-filter">
                        </div>
                        <div class="filter-block filter-col">
                            <label for="end-date-filter"><?=__("Start Date to", $this->plugin_name)?></label>
                            <input type="date" name="end-date-filter" id="end-date-filter">
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
                <button type="button" class="button button-primary export-action" data-type="csv"><?=__('CSV', $this->plugin_name)?></button>
                <button type="button" class="button button-primary export-action" data-type="xlsx"><?=__('XLSX', $this->plugin_name)?></button>
                <button type="button" class="button button-primary export-action" data-type="json"><?=__('JSON', $this->plugin_name)?></button>
                <a download="" id="downloadFile" hidden href=""></a>
            </div>

        </div>
    </div>
</div>

