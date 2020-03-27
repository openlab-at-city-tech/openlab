<?php
global $wpdb;

$action = ( isset($_GET['action']) ) ? $_GET['action'] : '';
$id     = ( isset($_GET['question']) ) ? $_GET['question'] : null;

if($action == 'duplicate'){
    $this->questions_obj->duplicate_question($id);
}

if( isset( $_FILES['quiz_import_file'] ) ){
    $size =  $this->questions_obj->questions_import($_FILES['quiz_import_file']);
}
$example_export_path = AYS_QUIZ_ADMIN_URL . '/partials/questions/export_file/';

?>
<div class="wrap ays_questions_list_table">
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
    <div class="question-action-butons">
        <a class="ays_help mr-2" style="font-size:20px;" data-toggle="tooltip"
           title="<?php echo __("For import XLS file your version of PHP must be over than 5.5.", $this->plugin_name) ?>">
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
                <a download="" id="downloadFile" hidden href=""></a>
            </div>
        </div>
        <a href="javascript:void(0);" id="import_button"
           class="button upload-view-toggle" aria-expanded="false"><?= __('Import', $this->plugin_name) ?></a>
    </div>
    <form method="post" enctype="multipart/form-data" class="ays-dn" style="display:none;">
        <input type="file" name="quiz_import_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, .json" onchange="this.form.submit()" id="import_file"/>
    </form>
    <div class="upload-import-file-wrap">
        <div class="upload-import-file">
            <p class="install-help"><?php echo __( "If you have questions in a .csv, .xlsx or .json format, you may add it by uploading it here.", $this->plugin_name ); ?></p>
            <form method="post" enctype="multipart/form-data" class="ays-dn">
                <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, .json" name="quiz_import_file" id="import_file"/>
                <label class="screen-reader-text" for="import_file"><?php echo __( "Import file", $this->plugin_name ); ?></label>
                <input type="submit" name="import-file-submit" class="button" value="<?php echo __( "Upload now", $this->plugin_name ); ?>" disabled="">
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
                        $this->questions_obj->search_box('Search', $this->plugin_name);
                        $this->questions_obj->display();
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>

