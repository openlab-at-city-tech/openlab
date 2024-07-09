<?php
/**
 * Admin form folder settings
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (! defined('ABSPATH')) {
    exit;
}

?>
<style>
    <?php
    $string = "";
    global $typenow;
    $width = get_option("wcp_dynamic_width_for_".$typenow);
    $width = intval($width);
    if ($width == null || empty($width) || $width > 1200) {
        $width = 280;
    }

    $width = ($width - 40);
    $customize_folders = get_option('customize_folders');
    $customize_folders = (empty($customize_folders)||!is_array($customize_folders))?[]:$customize_folders;
    ?>
</style>
<style>
<?php
$font_family = "";
if (isset($customize_folders['folder_font']) && !empty($customize_folders['folder_font'])) {
    $font_family  = $customize_folders['folder_font'];
    $folder_fonts = self::get_font_list();
    if (isset($folder_fonts[$font_family])) {
        if ($font_family == "System Stack") {
            $font_family = "-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell,Helvetica Neue,sans-serif";
        }
        ?>
.wcp-container, .folder-popup-form, .dynamic-menu { font-family: <?php echo esc_attr($font_family) ?>; }
        <?php
    }

    if ($font_family == "-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell,Helvetica Neue,sans-serif") {
        $font_family = "System Stack";
    }

    if ($folder_fonts[$font_family] == "Default") {
        $font_family = "";
    }
}

if (!isset($customize_folders['new_folder_color']) || empty($customize_folders['new_folder_color'])) {
    $customize_folders['new_folder_color'] = "#f51366";
}
if (!isset($customize_folders['default_icon_color']) || empty($customize_folders['default_icon_color'])) {
    $customize_folders['default_icon_color'] = "#334155";
}
?>
.add-new-folder { background-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?>; border-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?> }
.wcp-hide-show-buttons .toggle-buttons { background-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?>; }
.folders-toggle-button span { background-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?>; }
.ui-resizable-handle.ui-resizable-e:before, .ui-resizable-handle.ui-resizable-w:before {border-color: <?php echo esc_attr($customize_folders['new_folder_color']) ?>;}
<?php if (!isset($customize_folders['bulk_organize_button_color']) || empty($customize_folders['bulk_organize_button_color'])) {
    $customize_folders['bulk_organize_button_color'] = "#f51366";
} ?>
button.button.organize-button { background-color: <?php echo esc_attr($customize_folders['bulk_organize_button_color']) ?>; border-color: <?php echo esc_attr($customize_folders['bulk_organize_button_color']) ?>; }
button.button.organize-button:hover { background-color: <?php echo esc_attr($customize_folders['bulk_organize_button_color']) ?>; border-color: <?php echo esc_attr($customize_folders['bulk_organize_button_color']) ?>; }
<?php if (!isset($customize_folders['folder_bg_color']) || empty($customize_folders['folder_bg_color'])) {
    $customize_folders['folder_bg_color'] = "#f51366";
}

$rgbColor = self::hexToRgb($customize_folders['folder_bg_color']); ?>
body:not(.no-hover-css) #custom-scroll-menu .jstree-hovered:not(.jstree-clicked), body:not(.no-hover-css) #custom-scroll-menu .jstree-hovered:not(.jstree-clicked):hover, .dynamic-menu a.active, .dynamic-menu a:hover, .folder-setting-menu li a:hover { background: rgba(<?php echo esc_attr($rgbColor['r'].",".$rgbColor['g'].",".$rgbColor['b'].", 0.08") ?>) !important; color: #333333;}
.dynamic-menu li.color-folder:hover { background: rgba(<?php echo esc_attr($rgbColor['r'].",".$rgbColor['g'].",".$rgbColor['b'].", 0.08") ?>) !important; }
body:not(.no-hover-css) .dynamic-menu li.color-folder a:hover { background: transparent !important; }

body:not(.no-hover-css) #custom-scroll-menu .jstree-clicked, body:not(.no-hover-css) #custom-scroll-menu .jstree-clicked:not(.jstree-clicked):focus, #custom-scroll-menu .jstree-clicked, #custom-scroll-menu .jstree-clicked:hover { background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; color: #ffffff !important; }
body:not(.no-hover-css) #custom-scroll-menu .jstree-clicked .folder-actions { background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; color: #ffffff !important; }
#custom-scroll-menu .jstree-hovered.wcp-drop-hover, #custom-scroll-menu .jstree-hovered.wcp-drop-hover:hover, #custom-scroll-menu .jstree-clicked.wcp-drop-hover, #custom-scroll-menu .jstree-clicked.wcp-drop-hover:hover, body #custom-scroll-menu  *.drag-in > a:hover { background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; color: #ffffff !important; }
.pfolder-folder-close {color: <?php echo esc_attr($customize_folders['default_icon_color']) ?>}
.drag-bot > a {
    border-bottom: solid 2px <?php echo esc_attr($customize_folders['folder_bg_color']) ?>;
}
.drag-up > a {
    border-top: solid 2px <?php echo esc_attr($customize_folders['folder_bg_color']) ?>;
}
#custom-scroll-menu .jstree-hovered:not(.jstree-clicked) .pfolder-folder-close {
    color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?>;
}
.folders-action-menu > ul > li > a:not(.disabled):hover, .folders-action-menu > ul > li > label:not(.disabled):hover {
    color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?>;
}
.dynamic-menu a.active span i, .dynamic-menu a:hover span i, .dynamic-menu a.active span.dashicons, .dynamic-menu a:hover span.dashicons { color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> }
body:not(.no-hover-css) #custom-scroll-menu *.drag-in > a.jstree-hovered, body:not(.no-hover-css) #custom-scroll-menu *.drag-in > a.jstree-hovered:hover {
    background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important;
    color: #fff !important;
}
.custom-scroll-menu.hor-scroll .horizontal-scroll-menu .jstree-clicked .folder-actions { background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; color: #ffffff !important; }
#custom-scroll-menu .jstree-hovered.wcp-drop-hover, #custom-scroll-menu .jstree-hovered.wcp-drop-hover:hover, #custom-scroll-menu .jstree-clicked.wcp-drop-hover, #custom-scroll-menu .jstree-clicked.wcp-drop-hover:hover, body #custom-scroll-menu  *.drag-in > a:hover { background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; color: #ffffff !important; }
body:not(.no-hover-css) #custom-scroll-menu *.drag-in > a.jstree-hovered .pfolder-folder-close, body:not(.no-hover-css) #custom-scroll-menu *.drag-in > a.jstree-hovered:hover .pfolder-folder-close {
    color: #fff !important;
}
.orange-bg > span ,.wcp-container .route.active-item > h3.title, .header-posts a.active-item, .un-categorised-items.active-item, .sticky-folders ul li a.active-item { background-color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; color: #ffffff; }
body:not(.no-hover-css) .wcp-container .route .title:hover, body:not(.no-hover-css) .header-posts a:hover, body:not(.no-hover-css) .un-categorised-items:hover, body:not(.no-hover-css) .sticky-folders ul li a:hover {background: rgba(<?php echo esc_attr($rgbColor['r'].",".$rgbColor['g'].",".$rgbColor['b'].", 0.08") ?>);}
.wcp-drop-hover {
    background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important;
}
#custom-menu .route .nav-icon .wcp-icon {color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important;}
.mCS-3d.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar { background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; }
.os-theme-dark>.os-scrollbar>.os-scrollbar-track>.os-scrollbar-handle { background: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; }
.jstree-node.drag-in > a.jstree-anchor.jstree-hovered { background-color: <?php echo esc_attr($customize_folders['folder_bg_color']) ?> !important; color: #ffffff; }
<?php

if (isset($customize_folders['folder_size']) && !empty($customize_folders['folder_size'])) {
    if ($customize_folders['folder_size'] == "custom") {
        $customize_folders['folder_size'] = ! isset($customize_folders['folder_custom_font_size']) || empty($customize_folders['folder_custom_font_size']) ? "16" : $customize_folders['folder_custom_font_size'];
    }
    ?>
    .wcp-container .route span.title-text, .header-posts a, .un-categorised-items a, .sticky-title, .sticky-folders > ul > li > a, .jstree-default .jstree-anchor { font-size: <?php echo esc_attr($customize_folders['folder_size']) ?>px; }
    <?php
}
?>
</style>
<?php if (!empty($font_family)) {
    wp_enqueue_style('custom-google-fonts', 'https://fonts.googleapis.com/css?family='.urlencode($font_family), false, WCP_FOLDER_VERSION);
} ?>
<div id="media-css">

</div>
<?php
$optionName = $typenow."_parent_status";
$status     = get_option($optionName);
global $typenow;
$title = ucfirst($typenow);
if ($typenow == "page") {
    $title = "Pages";
} else if ($typenow == "post") {
    $title = "Posts";
} else if ($typenow == "attachment") {
    $title = "Files";
} else {
    $post_type  = $typenow;
    $post_types = get_post_types([ "name" => $post_type], 'objects');
    if (!empty($post_types) && is_array($post_types) && isset($post_types[$post_type]) && isset($post_types[$post_type]->label)) {
        $title = $post_types[$post_type]->label;
    }
}

$display_status = "wcp_dynamic_display_status_".$typenow;
$display_status = get_option($display_status);
$class_name     = isset($display_status) && $display_status == "hide" ? "hide-folders-area" : "";
$active_class   = (isset($display_status) && $display_status == "hide") ? "" : "active";
$active_class_2 = (isset($display_status) && $display_status == "hide") ? "active" : "";

// Do not change here, Free/Pro Class name
$post_type        = WCP_Folders::get_custom_post_type($typenow);
$active           = "";
$active_all_class = "";
if (!empty($post_type)) {
    $activeItem = filter_input(INPUT_POST, $post_type);
    if (empty($activeItem)) {
        $activeItem = filter_input(INPUT_GET, $post_type);
    }

    if ($activeItem == -1) {
        $active = "active-item";
    }

    if (empty($activeItem) || $activeItem == "") {
        $active_all_class = "active-item";
    }
}
$horClass = (!isset($customize_folders['enable_horizontal_scroll']) || $customize_folders['enable_horizontal_scroll'] == "on") ? "hor-scroll" : "";
?>
<div id="wcp-content" class="<?php echo esc_attr(isset($display_status) && $display_status == "hide" ? "hide-folders-area" : "")  ?>" >
    <div id="wcp-content-resize">
        <div class="wcp-content">
            <div class="wcp-hide-show-buttons">
                <div class="toggle-buttons hide-folders <?php echo esc_attr($active_class)  ?>"><span class="dashicons dashicons-arrow-left"></span></div>
                <div class="toggle-buttons show-folders <?php echo esc_attr($active_class_2) ?>"><span class="dashicons dashicons-arrow-right"></span></div>
            </div>
            <div class='wcp-container'>
                <div class="sticky-wcp-custom-form">
                    <?php echo ($form_html) ?>
                    <div class="top-settings">
                        <div class="folder-search-form">
                            <div class="form-search-input">
                                <input type="text" value="" id="folder-search" autocomplete="off" />
                                <span><i class="pfolder-search"></i></span>
                            </div>
                        </div>
                        <div class="folder-separator"></div>
                        <div class="header-posts">
                            <a href="javascript:;" class="all-posts <?php echo esc_attr($active_all_class) ?>"><?php echo esc_attr("All ".$title); ?> <span class="total-count"><?php echo esc_attr($ttpsts) ?></span></a>
                        </div>
                        <div class="un-categorised-items <?php echo esc_attr($active) ?>">
                            <a href="javascript:;" class="un-categorized-posts"><?php echo esc_attr("Unassigned ".$title); ?> <span class="total-count total-empty"><?php echo esc_attr($ttemp) ?></span> </a>
                        </div>
                        <div class="folder-separator-2"></div>
                        <div class="folders-action-menu">
                            <ul>
                                <li style="flex: 0 0 22px;"><a href="javascript:;" class="no-bg"><input type="checkbox" id="menu-checkbox" ></a></li>
                                <li class="folder-inline-tooltip">
                                    <a class="full-width upload-media-action disabled" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>">
                                        <span class="inline-tooltip"><?php esc_html_e("Uploading folder is pro feature", "folders"); ?> <span><?php esc_html_e("Upgrade Now 🎉", "folders") ?></span></span>
                                        <span class="dashicons dashicons-cloud-upload"></span>
                                    </a>
                                </li>
                                <li class="folder-inline-tooltip cut-folder-action">
                                    <a class="full cut-folder-action disabled" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                        <span class="inline-tooltip"><?php esc_html_e("Cut is pro feature", "folders"); ?> <span><?php esc_html_e("Upgrade Now 🎉", "folders") ?></span></span>
                                        <span class="pfolder-cut"></span>
                                    </a>
                                </li>
                                <li class="folder-inline-tooltip cut-folder-action">
                                    <a class="full copy-folder-action disabled" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                        <span class="inline-tooltip"><?php esc_html_e("Copy is pro feature", "folders"); ?> <span><?php esc_html_e("Upgrade Now 🎉", "folders") ?></span></span>
                                        <span class="pfolder-copy"></span>
                                    </a>
                                </li>
                                <li class="folder-inline-tooltip cut-folder-action">
                                    <a class="paste-folder-action disabled" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                        <span class="inline-tooltip"><?php esc_html_e("Paste is pro feature", "folders"); ?> <span><?php esc_html_e("Upgrade Now 🎉", "folders") ?></span></span>
                                        <span class="pfolder-paste"></span>
                                    </a>
                                </li>
                                <li class="folder-inline-tooltip">
                                    <a class="lock-unlock-all-folders open-folders disabled" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                        <span class="inline-tooltip"><?php esc_html_e("Lock/Unlock is pro feature", "folders"); ?> <span><?php esc_html_e("Upgrade Now 🎉", "folders") ?></span></span>
                                        <span class="dashicons dashicons-lock"></span>
                                    </a>
                                </li>
                                <!--<li><a class="folder-tooltip undo-folder-action disabled" href="javascript:;" data-folder-tooltip="<?php /*esc_html_e("Undo Changes", "folders"); */?>"><span class="pfolder-undo"></span></a></li>-->
                                <li><a class="folder-tooltip delete-folder-action disabled" href="javascript:;" data-folder-tooltip="<?php esc_html_e("Delete", "folders"); ?>"><span class="pfolder-remove"></span></a></li>
                            </ul>
                        </div>
                        <div class="folder-separator-2"></div>
                    </div>
                </div>
                <div id="custom-scroll-menu" class="custom-scroll-menu <?php echo esc_attr($horClass) ?>">
                    <div class="horizontal-scroll-menu">
                        <div class="ajax-preloader">
                            <div class="cssload-container">
                                <div class="cssload-tube-tunnel"></div>
                            </div>
                        </div>
                        <div class="js-tree-data">
                        <div id="js-tree-menu" class="<?php echo ($status == 1) ? "active" : "" ?>">
                            <ul class='space first-space' id='space_0'>
                                <?php echo ($terms_data); ?>
                            </ul>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include_once "modals.php";
$hide_folder_color_pop_up = get_option("hide_folder_color_pop_up");
if($hide_folder_color_pop_up != "yes" && WCP_FOLDER_VERSION == "3.0") {
    $customize_folders = get_option("customize_folders");
    if (isset($customize_folders['show_folder_in_settings']) && $customize_folders['show_folder_in_settings'] == "yes") {
        $setting_url = admin_url("options-general.php?page=wcp_folders_settings&setting_page=customize-folders&focus=icon-color");
    } else {
        $setting_url = admin_url("admin.php?page=wcp_folders_settings&setting_page=customize-folders&focus=icon-color");
    }
    ?>
    <div class="folder-popup-form color-popup-options always-show" id="color-pop-up-options" style="display: block">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="is-modal" href="javascript:;"><span></span></a>
                </div>
                <div class="folder-popup-top">
                    <img src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/color-popup.png") ?>" />
                </div>
                <div class="folder-popup-bottom">
                    <div class="folder-color-title">
                        <?php esc_html_e("🎨 Set custom colors to folders icon", "folders") ?>
                    </div>
                    <div class="folder-color-desc">
                        <?php esc_html_e("You can now change the icon color for each folder from the menu and change the default icon color from the Folders settings.", "folders") ?>
                    </div>
                </div>
                <div class="folder-form-buttons">
                    <a href="javascript:;" class="form-cancel-btn avoid-cancel"><?php esc_html_e("Cancel", "folders") ?></a>
                    <a href="<?php echo esc_url($setting_url) ?>" class="form-submit-btn customize-folder-color"><?php esc_html_e("Customise", "folders") ?></a>
                </div>
            </div>
        </div>
        <script>
            jQuery(document).ready(function(){
                jQuery(document).on("click", ".color-popup-options .form-cancel-btn, .color-popup-options .close-popup-button, #color-pop-up-options", function(e){
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    jQuery("#color-pop-up-options").hide();
                    jQuery.ajax({
                        url: "<?php echo esc_url(admin_url("admin-ajax.php")) ?>",
                        data: {
                            action: 'hide_folder_color_pop_up',
                            nonce: '<?php echo esc_attr(wp_create_nonce('hide_folder_color_pop_up')) ?>'
                        },
                        type: 'post',
                        success: function(){

                        }
                    });
                });
            });
        </script>
    </div>
<?php } ?>
