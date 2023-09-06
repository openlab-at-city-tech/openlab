<?php
global $wpdb;

$action = ( isset($_GET['action']) ) ? $_GET['action'] : '';
$id     = ( isset($_GET['question']) ) ? $_GET['question'] : null;

if($action == 'duplicate'){
    $this->questions_obj->duplicate_question($id);
}

$tab_url = "?page=".$this->plugin_name."-question-reports";

$actual_reports_count = Quiz_Maker_Data::get_actual_reports_count();

if( isset( $_FILES['quiz_import_file'] ) ){
    if (isset($_POST["import_simple_xlsx"]) && $_POST["import_simple_xlsx"] == "on") {
        $stats = $this->questions_obj->ays_xlsx_questions_simple_import($_FILES['quiz_import_file']);
    }else{
        $stats = $this->questions_obj->questions_import($_FILES['quiz_import_file']);
    }

    $url = esc_url_raw( remove_query_arg( false ) ) . '&status=imported&stats=' . $stats;
    wp_redirect( $url );
}
$example_export_path = AYS_QUIZ_ADMIN_URL . '/partials/questions/export_file/';

$add_url = remove_query_arg( array('status') );
$url_args = array(
    "page"    => esc_attr( $_REQUEST['page'] ),
    "action"    => 'add',
);

if( isset( $_GET['paged'] ) && sanitize_text_field( $_GET['paged'] ) != '' ){
    $url_args['paged'] = sanitize_text_field( $_GET['paged'] );
}

$add_url = add_query_arg( $url_args, $add_url );

?>
<div class="wrap ays-quiz-list-table ays_questions_list_table" style="position: relative;">
    <div class="ays-quiz-heading-box">
        <div class="ays-quiz-wordpress-user-manual-box">
            <a href="https://ays-pro.com/wordpress-quiz-maker-user-manual" target="_blank"><?php echo __("View Documentation", $this->plugin_name); ?></a>
        </div>
    </div>
    <h1 class="wp-heading-inline">
        <?php
            echo esc_html(get_admin_page_title());
            echo sprintf( '<a href="%s" class="page-title-action button-primary ays-quiz-add-new-button">' . __('Add New', $this->plugin_name) . '</a>', $add_url);
        ?>
    </h1>
    <style>
        
        .spinner.ays-wp-loading {
            background-image: url(images/loading.gif);
            float: left;
            margin-left: 0;
            visibility: visible;
        }

    </style>
<!--
    <div class="export-download-progress-bar display_none">
        <div class="ays-progress fourth display_block">
            <span class="ays-progress-value fourth">0%</span>
            <div class="ays-progress-bg fourth">
                <div class="ays-progress-bar fourth" style="width:0%"></div>
            </div>
        </div>
    </div>
-->
    <div class="question-action-butons">
        <a class="ays_help mr-2" style="font-size:20px;" data-toggle="tooltip"
           title="<?php echo __("For import XLSX file your version of PHP must be over than 5.6.", $this->plugin_name) ?>">
            <i class="ays_fa ays_fa_info_circle"></i>
        </a>
        <div class="dropdown ays-export-dropdown" style="">
            <a href="javascript:void(0);" data-toggle="dropdown" class="button mr-2 dropdown-toggle">
                <span class="ays-wp-loading d-none"></span>
                <?= __('Example', $this->plugin_name) ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right ays-dropdown-menu">
                <a href="<?php echo $example_export_path; ?>example_questions_export.csv"                    
                   download="example_questions_export.csv" class="dropdown-item">
                    CSV
                </a>
                <a href="<?php echo $example_export_path; ?>example_questions_export.xlsx"
                   download="example_questions_export.xlsx" class="dropdown-item">
                    XLSX
                </a>
                <a href="<?php echo $example_export_path; ?>example_questions_export.json"
                   download="example_questions_export.json" class="dropdown-item">
                    JSON
                </a>
                <a href="<?php echo $example_export_path; ?>example_questions_export_simple.xlsx"
                   download="example_questions_export_simple.xlsx" class="dropdown-item">
                    Simple XLSX
                </a>
            </div>
        </div>
<!--
        <div class="dropdown ays-export-dropdown" style="">
            <a href="javascript:void(0);" data-toggle="dropdown" class="button mr-2 dropdown-toggle">
                <span class="spinner ays-wp-loading d-none"></span>
                <?= __('Export to', $this->plugin_name) ?>
            </a>
            <div class="dropdown-menu  dropdown-menu-right ays-dropdown-menu">
                <a class="dropdown-item ays-questions-export" data-type="csv" href="javascript:void(0);">
                    CSV
                </a>
                <a class="dropdown-item ays-questions-export" data-type="xlsx" href="javascript:void(0);">
                    XLSX
                </a>
                <a class="dropdown-item ays-questions-export" data-type="json" href="javascript:void(0);">
                    JSON
                </a>
                <a class="dropdown-item ays-questions-export" data-type="simple_xlsx" href="javascript:void(0);">
                    Simple XLSX
                </a>
                <a download="" id="downloadFile" hidden href=""></a>
            </div>
        </div>
-->
        <a href="javascript:void(0);"  class="button ays-export-questions-filters">
            <span class="spinner ays-wp-loading d-none"></span>
            <?= __('Export', $this->plugin_name) ?>
        </a>
        <a href="javascript:void(0);" id="import_toggle_button"
           class="button upload-view-toggle" aria-expanded="false"><?= __('Import', $this->plugin_name) ?></a>
    </div>
    <div class="ays-quiz-heading-box ays-quiz-unset-float">
        <div class="ays-quiz-wordpress-user-manual-box ays-quiz-wordpress-text-align">
            <a href="https://www.youtube.com/watch?v=RldosodJItI" target="_blank">
                <?php echo __("How to export/import questions - video", $this->plugin_name); ?>
            </a>
        </div>
    </div>
    <div class="nav-tab-wrapper">
        <a href="#poststuff" class="nav-tab nav-tab-active">
            <?php echo __("Questions", $this->plugin_name);?>
        </a>
        <a href="<?php echo $tab_url; ?>" class="no-js nav-tab">
            <?php echo __("Reports", $this->plugin_name);
            if ($actual_reports_count > 0) {
                echo '<span class="ays_menu_badge ays_results_bage">' . $actual_reports_count . '</span>';
            }
            ?>
        </a>
    </div>
<!--
    <form method="post" enctype="multipart/form-data" class="ays-dn" style="display:none;">
        <input type="file" name="quiz_import_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, .json" onchange="this.form.submit()" id="import_file"/>
    </form>
-->

   <div class="ays-modal" id="questions-export-filters">
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
                <form method="post" id="ays_questions_export_filter">
                    <div class="filter-col">
                        <label for="author_id-filter"><?=__("Authors", $this->plugin_name)?></label>
                        <button type="button" class="ays_authorid_clear button button-small wp-picker-default"><?=__("Clear", $this->plugin_name)?></button>
                        <select name="author_id-select[]" id="author_id-filter" multiple="multiple"></select>
                    </div>
                    <hr>
                    <div class="filter-col">
                        <label for="category_id-filter"><?=__("Categories", $this->plugin_name)?></label>
                        <button type="button" class="ays_catid_clear button button-small wp-picker-default"><?=__("Clear", $this->plugin_name)?></button>
                        <select name="category_id-select[]" id="category_id-filter" multiple="multiple"></select>
                    </div>
                    <hr>
                    <div class="filter-col">
                        <label for="tag_id-filter"><?php echo __("Tags", $this->plugin_name); ?></label>
                        <button type="button" class="ays_tagid_clear button button-small wp-picker-default"><?php echo __("Clear", $this->plugin_name);?></button>
                        <select name="tag_id-select[]" id="tag_id-filter" multiple="multiple"></select>
                    </div>
                    <div class="filter-block">
                        <div class="filter-block filter-col">
                            <label for="question-start-date-filter"><?=__("Start Date from", $this->plugin_name)?></label>
                            <input type="date" name="question-start-date-filter" id="question-start-date-filter">
                        </div>
                        <div class="filter-block filter-col">
                            <label for="question-end-date-filter"><?=__("Start Date to", $this->plugin_name)?></label>
                            <input type="date" name="question-end-date-filter" id="question-end-date-filter">
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
                <button type="button" class="button button-primary ays-questions-export" data-type="csv"><?=__('CSV', $this->plugin_name)?></button>
                <button type="button" class="button button-primary ays-questions-export" data-type="xlsx"><?=__('XLSX', $this->plugin_name)?></button>
                <button type="button" class="button button-primary ays-questions-export" data-type="json"><?=__('JSON', $this->plugin_name)?></button>
                <button type="button" class="button button-primary ays-questions-export" data-type="simple_xlsx"><?=__('Simple XLSX', $this->plugin_name)?></button>
                <a download="" id="downloadFile" hidden href=""></a>
            </div>
        </div>
    </div>

    <div class="upload-import-file-wrap">
        <div class="upload-import-file">
            <p class="install-help"><?php echo __( "If you have questions in a .csv, .xlsx or .json format, you may add it by uploading it here.", $this->plugin_name ); ?> 
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Make sure the categories of the questions start with letters, instead of numbers, while importing. Please note, that the categories must start with letters so that the functionality can work correctly for you.',$this->plugin_name); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </p>
            <form method="post" enctype="multipart/form-data" class="ays-dn">
                <label for="simple_import_check" class="install-help">
                    <?php
                        echo ( sprintf(
                            __("%sTick this checkbox if you're importing a %sSimple XLSX%s file.%s",$this->plugin_name),
                            '<span>',
                            '<strong>',
                            '</strong>',
                            '</span>'
                        ) );
                     ?>
                    <input type="checkbox" name="import_simple_xlsx" value="on" id="simple_import_check" style="margin-left: 5px;">
                </label>
                <label for="ays_quiz_update_existing_questions" class="install-help">
                    <?php
                        echo __("Update existing questions",$this->plugin_name);
                     ?>
                    <input type="checkbox" name="ays_quiz_update_existing_questions" value="on" id="ays_quiz_update_existing_questions" style="margin-left: 5px;">
                </label>
                <input type="hidden" name="import_file_type" id="import_file_hidden" value="custom">
                <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, .json" name="quiz_import_file" id="import_file"/>
                <label class="screen-reader-text" for="import_file"><?php echo __( "Import file", $this->plugin_name ); ?></label>
                <input type="submit" name="import-file-submit" class="button" value="<?php echo __( "Import now", $this->plugin_name ); ?>" disabled="">
            </form>
        </div>
    </div>

    <div class="clear"></div>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">                    
                    <?php
                        $this->questions_obj->views();
                    ?>
                    <form method="post">
                        <?php
                        $this->questions_obj->prepare_items();
                        $search = __( "Search", $this->plugin_name );
                        $this->questions_obj->search_box($search, $this->plugin_name);
                        $this->questions_obj->display();
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>

    <h1 class="wp-heading-inline">
        <?php
            echo esc_html(get_admin_page_title());
            echo sprintf( '<a href="%s" class="page-title-action button-primary ays-quiz-add-new-button">' . __('Add New', $this->plugin_name) . '</a>', $add_url);
        ?>
    </h1>
</div>

