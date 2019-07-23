<?php

defined('ABSPATH') or die('Nope, not accessing this');

class WCP_Forms {
    public function __construct() {
        parent::__construct();
    }

    public static function get_form_html($option_data = "") {
        ob_start();
        ?>
        <div class="wcp-custom-form">
            <div class="form-title">
                <?php echo __("Folders", WCP_FOLDER) ?>
                <a href="javascript:;" class="add-new-folder" id="add-new-folder"><span class="folder-icon-create_new_folder"></span> <span><?php echo __("New Folder", WCP_FOLDER) ?></span></a>
                <div class="clear"></div>
            </div>
            <div class="form-options">
                <ul>
                    <li>
                        <a href="javascript:;" id="inline-update"><span class="icon folder-icon-border_color"><span class="path1"></span><span class="path2"></span></span> <span class="text"><?php echo __("Rename", WCP_FOLDER) ?></span> </a>
                    </li>
                    <li>
                        <a href="javascript:;" id="inline-remove"><span class="icon folder-icon-delete"></span> <span class="text"><?php echo __("Delete", WCP_FOLDER) ?></span> </a>
                    </li>
                    <li class="last">
                        <a href="javascript:;" id="expand-collapse-list"><span class="icon folder-icon-expand_more"></span></span> </a>
                    </li>
                </ul>
                <div class="upgrade-message">
                    <?php
                    $total_folders = get_option("folder_old_plugin_folder_status");
                    if($total_folders == false || $total_folders < 10) {
                        $total_folders = 10;
                    }
                    $total = WCP_Folders::get_total_term_folders();
                    if($total > $total_folders) {
                        $total_folders = $total;
                    }
                    ?>
                    <span class="upgrade-message">You have used <?php echo "<span class='pink' id='current-folder'>".$total."</span>/<span id='total-folder'>".$total_folders."</span>" ?> Folders. <a class="pink" href="<?php echo admin_url("admin.php?page=wcp_folders_upgrade") ?>"><?php echo __("Upgrade", WCP_FOLDER) ?></a></span>
                    <script>
                        folderLimitation = <?php echo $total_folders; ?>;
                    </script>
                </div>
            </div>
            <div class="form-loader">
                <div class="form-loader-count"></div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}