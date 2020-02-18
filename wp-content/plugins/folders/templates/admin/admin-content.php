<?php
defined('ABSPATH') or wp_die('Nope, not accessing this');
?>
<style>
    .ui-state-highlight {
        background: transparent;
        border: dashed 1px #0073AA;
        width:150px;
        height: 25px !important;
    }
    <?php
    $string = "";
    global $typenow;
    $width = get_option("wcp_dynamic_width_for_" . $typenow);
    if($width == null || empty($width)) {
        $width = 292;
    }
    $width = $width - 40;
    $customize_folders = get_option('customize_folders');
    ?>
</style>
<div id="wcp-custom-style">
    <style>
        <?php
            $string = "";
            for($i=0;$i<=15;$i++) {
            $string .= " .space > .route >";
            $new_width = $width - (13+(20*$i));
                echo "#custom-menu > {$string} .title { width: {$new_width}px !important; } ";
            }
        ?>
    </style>
</div>
<div id="style-css">

</div>
<style>
    <?php
    if(isset($customize_folders['new_folder_color']) && !empty($customize_folders['new_folder_color'])) {
        ?>
    .add-new-folder { background-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?>; border-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?> }
    .wcp-hide-show-buttons .toggle-buttons { background-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?>; }
    .folders-toggle-button span { background-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?>; }
    <?php
}
if(isset($customize_folders['dropdown_color']) && !empty($customize_folders['dropdown_color'])) {
    ?>
    #media-attachment-taxonomy-filter { border-color: <?php echo esc_attr($customize_folders['dropdown_color']) ?>; color: <?php echo esc_attr($customize_folders['dropdown_color']) ?> }
    <?php
}
if(isset($customize_folders['folder_bg_color']) && !empty($customize_folders['folder_bg_color'])) {
    ?>
    .wcp-container .route.active-item > h3.title, .header-posts a.active-item, .un-categorised-items.active-item { background-color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; color: #ffffff; }
    <?php
}
if(isset($customize_folders['bulk_organize_button_color']) && !empty($customize_folders['bulk_organize_button_color'])) {
    ?>
    button.button.organize-button { background-color: <?php echo esc_attr($customize_folders['bulk_organize_button_color']) ?>; border-color: <?php echo esc_attr($customize_folders['bulk_organize_button_color']) ?>; }
    button.button.organize-button:hover { background-color: <?php echo esc_attr($customize_folders['bulk_organize_button_color']) ?>; border-color: <?php echo esc_attr($customize_folders['bulk_organize_button_color']) ?>; }
    <?php
}
$font_family = "";
if(isset($customize_folders['folder_font']) && !empty($customize_folders['folder_font'])) {
    $font_family = $customize_folders['folder_font'];
    ?>
    .wcp-container, .folder-popup-form { font-family: "<?php echo esc_attr($font_family) ?>"; }
    <?php
}
if(isset($customize_folders['folder_size']) && !empty($customize_folders['folder_size'])) {
    ?>
    .wcp-container .route span.title-text, .header-posts a, .un-categorised-items a { font-size: <?php echo esc_attr($customize_folders['folder_size']) ?>px; }
    <?php
}
?>
</style>
<?php if(!empty($font_family)) {
    wp_enqueue_style( 'custom-google-fonts', 'https://fonts.googleapis.com/css?family='.urlencode($font_family), false );
} ?>
<div id="media-css">

</div>
<?php
$optionName = $typenow."_parent_status";
$status = get_option($optionName);
global $typenow;
$title = ucfirst($typenow);
if($typenow == "page") {
    $title = "Pages";
} else if($typenow == "post") {
    $title = "Posts";
} else if($typenow == "attachment") {
    $title = "Files";
}
$display_status = "wcp_dynamic_display_status_" . $typenow;
$display_status = get_option($display_status);
$class_name = isset($display_status) && $display_status == "hide"?"hide-folders-area":"";
$active_class = (isset($display_status) && $display_status == "hide")?"":"active";
$active_class_2 = (isset($display_status) && $display_status == "hide")?"active":"";

/* Do not change here, Free/Pro Class name */
$post_type = WCP_Folders::get_custom_post_type($typenow);
$active = "";
$active_all_class = "";
if(!empty($post_type)) {
    if(isset($_REQUEST[$post_type]) && $_REQUEST[$post_type] == -1) {
        $active = "active-item";
    }

    if(!isset($_REQUEST[$post_type]) || $_REQUEST[$post_type] == "") {
        $active_all_class = "active-item";
    }
}
?>
<div id="wcp-content" class="<?php echo esc_attr(isset($display_status) && $display_status == "hide"?"hide-folders-area":"")  ?>" >
    <div id="wcp-content-resize">
        <div class="wcp-content">
            <div class="wcp-hide-show-buttons">
                <div class="toggle-buttons hide-folders <?php echo esc_attr($active_class)  ?>"><span class="dashicons dashicons-arrow-left"></span></div>
                <div class="toggle-buttons show-folders <?php echo esc_attr($active_class_2) ?>"><span class="dashicons dashicons-arrow-right"></span></div>
            </div>
            <div class='wcp-container'>
                <div class="sticky-wcp-custom-form">
                    <?php echo $form_html ?>
                    <div class="header-posts">
                        <a href="javascript:;" class="all-posts <?php echo esc_attr($active_all_class) ?>"><span class="wcp-icon folder-icon-insert_drive_file"></span> <?php esc_attr_e("All ".$title, WCP_FOLDER ) ?> <span class="total-count"><?php echo $total_posts ?></span></a>
                    </div>
                    <div class="un-categorised-items <?php echo esc_attr($active) ?>">
                        <a href="javascript:;" class="un-categorized-posts"><?php esc_attr_e("Unassigned ".$title, WCP_FOLDER) ?> <span class="total-count total-empty"><?php echo $total_empty ?></span> </a>
                    </div>
                </div>
                <div id="custom-scroll-menu">
                    <div id="custom-menu" class="wcp-custom-menu <?php echo ($status==1)?"active":"" ?>">
                        <!--<div class="wcp-parent" id="title0"><i class="fa fa-folder-o"></i> All Folders</div>-->
                        <ul class='space first-space' id='space_0'>
                            <?php echo $terms_data; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="folder-popup-form" id="add-update-folder">
    <div class="popup-form-content">
        <form action="" method="post" id="save-folder-form">
            <div id="add-update-folder-title" class="add-update-folder-title">
                Add Folder
            </div>
            <div class="folder-form-input">
                <input id="add-update-folder-name" autocomplete="off" placeholder="Folder name">
            </div>
            <div class="folder-form-errors">
                <span class="dashicons dashicons-info"></span> Please enter folder name
            </div>
            <div class="folder-form-buttons">
                <button type="submit" class="form-submit-btn" id="save-folder-data" style="width: 106px">Submit</button>
                <a href="javascript:;" class="form-cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</div>

<div class="folder-popup-form" id="update-folder-item">
    <div class="popup-form-content">
        <form action="" method="post" id="update-folder-form">
            <div id="update-folder-title" class="add-update-folder-title">
                Rename Folder
            </div>
            <div class="folder-form-input">
                <input id="update-folder-item-name" autocomplete="off" placeholder="Folder name">
            </div>
            <div class="folder-form-errors">
                <span class="dashicons dashicons-info"></span> Please enter folder name
            </div>
            <div class="folder-form-buttons">
                <button type="submit" class="form-submit-btn" id="update-folder-data" style="width: 106px">Submit</button>
                <a href="javascript:;" class="form-cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</div>

<div class="folder-popup-form" id="confirm-remove-folder">
    <div class="popup-form-content">
        <div class="add-update-folder-title" id="remove-folder-message">
            Are you sure you want to delete the selected folder?
        </div>
        <div class="folder-form-message" id="remove-folder-notice">
            Items in the folder will not be deleted.
        </div>
        <div class="folder-form-buttons">
            <a href="javascript:;" class="form-cancel-btn">No, Keep it</a>
            <a href="javascript:;" class="form-submit-btn" id="remove-folder-item">Yes, Delete it!</a>
        </div>
    </div>
</div>

<div class="folder-popup-form" id="no-more-folder-credit">
    <div class="popup-form-content">
        <div class="add-update-folder-title" id="folder-limitation-message">

        </div>
        <div class="folder-form-message">
            Unlock unlimited amount of folders by upgrading to one of our pro plans.
        </div>
        <div class="folder-form-buttons">
            <a href="javascript:;" class="form-cancel-btn">Cancel</a>
            <a href="<?php echo esc_url(admin_url("admin.php?page=wcp_folders_upgrade")) ?>" target="_blank" class="form-submit-btn">See Pro Plans</a>
        </div>
    </div>
</div>

<div class="folder-popup-form" id="error-folder-popup">
    <div class="popup-form-content">
        <div class="add-update-folder-title" id="error-folder-popup-message">

        </div>
        <div class="folder-form-buttons">
            <a href="javascript:;" class="form-cancel-btn">Close</a>
        </div>
    </div>
</div>