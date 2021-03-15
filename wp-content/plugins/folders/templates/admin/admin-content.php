<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<style>
    <?php
    $string = "";
    global $typenow;
    $width = get_option("wcp_dynamic_width_for_" . $typenow);
    if($width == null || empty($width)) {
        $width = 280;
    }
    $width = $width - 40;
    $customize_folders = get_option('customize_folders');
    ?>
</style>
<style>
<?php
if(!isset($customize_folders['new_folder_color']) || empty($customize_folders['new_folder_color'])) {
    $customize_folders['new_folder_color'] = "#f51366";
}
?>
.add-new-folder { background-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?>; border-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?> }
.wcp-hide-show-buttons .toggle-buttons { background-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?>; }
.folders-toggle-button span { background-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?>; }
.ui-resizable-handle.ui-resizable-e:before, .ui-resizable-handle.ui-resizable-w:before {border-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?>;}
<?php if(!isset($customize_folders['bulk_organize_button_color']) || empty($customize_folders['bulk_organize_button_color'])) {
    $customize_folders['bulk_organize_button_color'] = "#f51366";
} ?>
button.button.organize-button { background-color: <?php echo esc_attr($customize_folders['bulk_organize_button_color']) ?>; border-color: <?php echo esc_attr($customize_folders['bulk_organize_button_color']) ?>; }
button.button.organize-button:hover { background-color: <?php echo esc_attr($customize_folders['bulk_organize_button_color']) ?>; border-color: <?php echo esc_attr($customize_folders['bulk_organize_button_color']) ?>; }
<?php if(!isset($customize_folders['folder_bg_color']) || empty($customize_folders['folder_bg_color'])) {
    $customize_folders['folder_bg_color'] = "#f51366";
} $rgbColor = self::hexToRgb($customize_folders['folder_bg_color']); ?>
    body:not(.no-hover-css) #custom-scroll-menu .jstree-hovered:not(.jstree-clicked), body:not(.no-hover-css) #custom-scroll-menu .jstree-hovered:not(.jstree-clicked):hover { background: rgba(<?php echo esc_attr($rgbColor['r'].",".$rgbColor['g'].",".$rgbColor['b'].", 0.08") ?>) !important; color: #333333;}
    body:not(.no-hover-css) #custom-scroll-menu .jstree-clicked, body:not(.no-hover-css) #custom-scroll-menu .jstree-clicked:not(.jstree-clicked):focus, #custom-scroll-menu .jstree-clicked, #custom-scroll-menu .jstree-clicked:hover { background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; color: #ffffff !important; }
    #custom-scroll-menu .jstree-hovered.wcp-drop-hover, #custom-scroll-menu .jstree-hovered.wcp-drop-hover:hover, #custom-scroll-menu .jstree-clicked.wcp-drop-hover, #custom-scroll-menu .jstree-clicked.wcp-drop-hover:hover, body #custom-scroll-menu  *.drag-in > , body #custom-scroll-menu  *.drag-in > a:hover { background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; color: #ffffff !important; }
    .drag-bot > a {
        border-bottom: solid 2px <?php echo esc_attr($customize_folders['folder_bg_color']) ?>;
    }
    .drag-up > a {
        border-top: solid 2px <?php echo esc_attr($customize_folders['folder_bg_color']) ?>;
    }
    body:not(.no-hover-css) #custom-scroll-menu *.drag-in > a.jstree-hovered, body:not(.no-hover-css) #custom-scroll-menu *.drag-in > a.jstree-hovered:hover {
        background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important;
        color: #fff !important;
    }
    .orange-bg > span ,.wcp-container .route.active-item > h3.title, .header-posts a.active-item, .un-categorised-items.active-item, .sticky-folders ul li a.active-item { background-color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; color: #ffffff; }
    body:not(.no-hover-css) .wcp-container .route .title:hover, body:not(.no-hover-css) .header-posts a:hover, body:not(.no-hover-css) .un-categorised-items:hover, body:not(.no-hover-css) .sticky-folders ul li a:hover {background: rgba(<?php echo esc_attr($rgbColor['r'].",".$rgbColor['g'].",".$rgbColor['b'].", 0.08") ?>);}
    .wcp-drop-hover {
        background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important;
    }
    #custom-menu .route .nav-icon .wcp-icon {color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important;}
    .mCS-3d.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar { background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; }
    .ui-state-highlight { border-color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; background: rgba(<?php echo esc_attr($rgbColor['r'].",".$rgbColor['g'].",".$rgbColor['b'].", 0.08") ?> !important;}
    .jstree-node.drag-in > a.jstree-anchor.jstree-hovered { background-color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; color: #ffffff; }
    <?php
$font_family = "";
if(isset($customize_folders['folder_font']) && !empty($customize_folders['folder_font'])) {
    $font_family = $customize_folders['folder_font'];
    $folder_fonts = self::get_font_list();
    if(isset($folder_fonts[$font_family])) {
    ?>
    .wcp-container, .folder-popup-form { font-family: "<?php echo esc_attr($font_family) ?>"; }
    <?php
    }
    if($folder_fonts[$font_family] == "Default") {
        $font_family = "";
    }
}
if(isset($customize_folders['folder_size']) && !empty($customize_folders['folder_size'])) {
    ?>
    .wcp-container .route span.title-text, .header-posts a, .un-categorised-items a, .sticky-title, .sticky-folders > ul > li > a { font-size: <?php echo esc_attr($customize_folders['folder_size']) ?>px; }
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
                    <div class="top-settings">
                        <div class="folder-search-form">
                            <div class="form-search-input">
                                <input type="text" value="" id="folder-search" autocomplete="off" />
                                <span><i class="pfolder-search"></i></span>
                            </div>
                        </div>
                        <div class="folder-separator"></div>
                        <div class="header-posts">
                            <a href="javascript:;" class="all-posts <?php echo esc_attr($active_all_class) ?>"><?php esc_attr_e("All ".$title, WCP_FOLDER ) ?> <span class="total-count"><?php echo $ttpsts ?></span></a>
                        </div>
                        <div class="un-categorised-items <?php echo esc_attr($active) ?>">
                            <a href="javascript:;" class="un-categorized-posts"><?php esc_attr_e("Unassigned ".$title, WCP_FOLDER) ?> <span class="total-count total-empty"><?php echo $ttemp ?></span> </a>
                        </div>
                        <div class="folder-separator-2"></div>
                    </div>
                </div>
                <div id="custom-scroll-menu">
                    <div class="ajax-preloader">
                        <div class="cssload-container">
                            <div class="cssload-tube-tunnel"></div>
                        </div>
                    </div>
                    <div class="js-tree-data">
                        <div id="js-tree-menu" class="<?php echo ($status==1)?"active":"" ?>">
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
</div>
<div id="folder-add-update-content">
    <div class="folder-popup-form" id="add-update-folder">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <form action="" method="post" id="save-folder-form">
                    <div id="add-update-folder-title" class="add-update-folder-title">
                        Add a new folder
                    </div>
                    <div class="add-folder-note">
                        Enter your folder's name (or create more than one folder by separating the name with a comma)
                    </div>
                    <div class="folder-form-input">
                        <div class="folder-group">
                            <input id="add-update-folder-name" autocomplete="off" required="required">
                            <span class="highlight"></span><span class="folder-bar"></span>
                            <label for="add-update-folder-name">Folder name</label>
                        </div>
                    </div>
                    <div class="folder-form-errors">
                        <span class="dashicons dashicons-info"></span> Please enter folder name
                    </div>
                    <div class="folder-form-buttons hide-it pro-message" id="pro-notice">
                        <span class="pro-tip">
                            Pro tip
                        </span>
                        <div class="pro-notice">
                            <a class="inline-button" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>">Upgrade to Pro</a> to create subfolders (with 20+ amaizing features) & premium support 🎉
                        </div>
                    </div>
                    <div class="folder-form-buttons">
                        <a href="javascript:;" class="form-cancel-btn">Cancel</a>
                        <button type="submit" class="form-submit-btn" id="save-folder-data" style="width: 160px">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="update-folder-item">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <form action="" method="post" id="update-folder-form">
                    <div id="update-folder-title" class="add-update-folder-title">
                        Rename folder
                    </div>
                    <div class="folder-form-input">
                        <div class="folder-group">
                            <input id="update-folder-item-name" autocomplete="off" required="required">
                            <span class="highlight"></span><span class="folder-bar"></span>
                            <label for="update-folder-item-name">Folder name</label>
                        </div>
                    </div>
                    <div class="folder-form-errors">
                        <span class="dashicons dashicons-info"></span> Please enter folder name
                    </div>
                    <div class="folder-form-buttons">
                        <a href="javascript:;" class="form-cancel-btn">Cancel</a>
                        <button type="submit" class="form-submit-btn" id="update-folder-data" style="width: 160px">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="confirm-remove-folder">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
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
    </div>

    <div class="folder-popup-form" id="no-more-folder-credit">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="add-update-folder-title" id="folder-limitation-message">

                </div>
                <div class="folder-form-message">
                    Unlock unlimited amount of folders by upgrading to one of our pro plans.
                </div>
                <div class="folder-form-buttons">
                    <a href="javascript:;" class="form-cancel-btn">Cancel</a>
                    <a href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" target="_blank" class="form-submit-btn">See Pro Plans</a>
                </div>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="error-folder-popup">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="add-update-folder-title" id="error-folder-popup-message">

                </div>
                <div class="folder-form-buttons">
                    <a href="javascript:;" class="form-cancel-btn">Close</a>
                </div>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="sub-folder-popup">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="add-update-folder-title">
                    Sub-folders is a pro feature
                </div>
                <div class="folder-form-message" style="padding: 25px 10px;" >
                    Hey, it looks like you want to create sub-folders on Folders. Sub-folders is a premium feature. Upgrade now to create, access and organize your files with sub-folders.
                </div>
                <div class="folder-form-buttons">
                    <a href="javascript:;" class="form-cancel-btn">Cancel</a>
                    <a href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" target="_blank" class="form-submit-btn">Upgrade Now</a>
                </div>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="add-sub-folder-popup">

        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="add-update-folder-title">
                    Add a new folder
                </div>
                <div class="folder-form-input">
                    <div class="folder-group">
                        <input id="update-folder-item-name" autocomplete="off" required="required" readonly>
                        <span class="highlight"></span><span class="folder-bar"></span>
                        <label for="update-folder-item-name">Folder name</label>
                    </div>
                </div>
                <div class="folder-form-buttons">
                    <span class="pro-tip">
                        Pro tip
                    </span>
                    <div class="pro-notice">
                        <a class="inline-button" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>">Upgrade to Pro</a> to create subfolders (with 20+ amaizing features) & premium support 🎉
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="bulk-move-folder">
    <form action="" method="post" id="bulk-folder-form">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="popup-folder-title">
                    Select Folder
                </div>
                <div class="select-box">
                    <select id="bulk-select">
                        <option value="">Select Folder</option>
                    </select>
                </div>
                <div class="folder-form-buttons">
                    <a href="javascript:;" class="form-cancel-btn">Cancel</a>
                    <button type="submit" class="form-submit-btn" id="move-to-folder" style="width: 200px">Move to Folder</button>
                </div>
            </div>
        </div>
    </form>
</div>
</div>
