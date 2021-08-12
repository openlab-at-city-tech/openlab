<?php
global $wpdb;

$action = ( isset($_GET['action']) ) ? $_GET['action'] : '';
$id     = ( isset($_GET['question']) ) ? $_GET['question'] : null;

if($action == 'duplicate'){
    $this->questions_obj->duplicate_question($id);
}

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

?>
<div class="wrap ays_questions_list_table" style="position: relative;">
    <h1 class="wp-heading-inline">
        <?php
            echo esc_html(get_admin_page_title());
            echo sprintf( '<a href="?page=%s&action=%s" class="page-title-action">' . __('Add New', $this->plugin_name) . '</a>', esc_attr( $_REQUEST['page'] ), 'add');
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
    <div class="export-download-progress-bar display_none">
        <div class="ays-progress fourth display_block">
            <span class="ays-progress-value fourth">0%</span>
            <div class="ays-progress-bg fourth">
                <div class="ays-progress-bar fourth" style="width:0%"></div>
            </div>
        </div>
    </div>
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
        <a href="javascript:void(0);" id="import_toggle_button"
           class="button upload-view-toggle" aria-expanded="false"><?= __('Import', $this->plugin_name) ?></a>
    </div>
<!--
    <form method="post" enctype="multipart/form-data" class="ays-dn" style="display:none;">
        <input type="file" name="quiz_import_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, .json" onchange="this.form.submit()" id="import_file"/>
    </form>
-->
    <div class="upload-import-file-wrap">
        <div class="upload-import-file">
            <p class="install-help"><?php echo __( "If you have questions in a .csv, .xlsx or .json format, you may add it by uploading it here.", $this->plugin_name ); ?></p>
            <form method="post" enctype="multipart/form-data" class="ays-dn">
                <label for="simple_import_check" class="install-help">
                    <b><?php echo __( "Simple", $this->plugin_name ); ?></b> .xlsx
                    <input type="checkbox" name="import_simple_xlsx" value="on" id="simple_import_check">
                    <a class="ays_help mr-2" style="font-size:15px;" data-toggle="tooltip"
                       title="<?php echo __("For importing Simple XLSX, enable this option before uploading", $this->plugin_name) ?>">
                        <i class="ays_fa ays_fa_info_circle"></i>
                    </a>
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
</div>

