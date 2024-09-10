<?php
/**
 * Admin folders settings
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (! defined('ABSPATH')) {
    exit;
}
?>
<!-- do not change here, Free/Pro URL Change -->
<style>
    <?php if (function_exists('is_rtl') && is_rtl()) { ?>
    #setting-form {
        float: right;
    }
    <?php } ?>
</style>
<script>
    (function (factory) {
        "use strict";
        if (typeof define === 'function' && define.amd) {
            define(['jquery'], factory);
        }
        else if(typeof module !== 'undefined' && module.exports) {
            module.exports = factory(require('jquery'));
        }
        else {
            factory(jQuery);
        }
    }(function ($, undefined) {
        var selectedItem;
        var importTitle = "<?php esc_html_e("Import folders from %plugin%", "folders"); ?>";
        var importDesc = "<?php esc_html_e("Are you sure you'd like to import %1\$d folders from %plugin%?", "folders"); ?>";
        var removeTitle = "<?php esc_html_e("Are you sure?", "folders"); ?>";
        var removeDesc = "<?php esc_html_e("You're about to delete %plugin%'s folders. Are you sure you'd like to proceed?", "folders"); ?>";
        $(document).ready(function(){
            <?php if ($setting_page == "folder-settings") { ?>
                $(".select2-box").select2();
            <?php } ?>
            <?php
            $focus = filter_input(INPUT_GET, 'focus');
            if($focus == "icon-color") {
                $hide_folder_color_pop_up = get_option("hide_folder_color_pop_up");
                if(!($hide_folder_color_pop_up)) {
                    add_option("hide_folder_color_pop_up", "yes");
                } else {
                    update_option("hide_folder_color_pop_up", "yes");
                }
                ?>
                $(".default-icon-color").addClass("add-focus-animate");
            <?php } ?>
            $(document).on("click",".form-cancel-btn, .close-popup-button, .folder-popup-form",function(){
                if($(this).hasClass("cancel-folders") || $(this).hasClass("remove-folders-box") || $(this).hasClass("close-remove-folders")) {
                    $("#remove_folders_when_removed").prop("checked", false);
                    setFoldersRemoveStatus("off");
                }
                if($(this).hasClass("delete-button")) {
                    setFoldersRemoveStatus("on");
                }
                $(".folder-popup-form").hide();
                if($(this).closest(".folder-popup-form").attr("id") == "import-third-party-plugin-data") {
                    if($("#wordpress-popup").length) {
                        $("#wordpress-popup").show();
                    }
                }
            });
            $(document).on("click",".import-folders-button", function(e){
                $("#import-folders-popup").show();
            });
            $(document).on("click",".popup-form-content", function(e){
                e.stopPropagation();
                e.stopImmediatePropagation();
            });
            $(document).on("click",".folder-select",function(){
                if($(this).is(":checked")) {
                    $(this).closest("tr").find(".hide-show-option").removeClass("hide-option");
                } else {
                    $(this).closest("tr").find(".hide-show-option").addClass("hide-option");
                }
            });
            $(document).on("click", ".accordion-header", function(){
                if($(this).hasClass("active")) {
                    $(this).closest(".accordion").find(".accordion-content").slideUp();
                    $(this).removeClass("active");
                } else {
                    $(this).closest(".accordion").find(".accordion-content").slideDown();
                    $(this).addClass("active");
                }
            });
            $(document).on("change", ".hide-show-option", function(){
                if($(this).val() == "folders-pro") {
                    $(this).find("option").prop("selected", false);
                    $(this).find("option:first").prop("selected", true);
                    window.open("<?php echo esc_url($this->getFoldersUpgradeURL()) ?>", "_blank");
                }
            });
            $(document).on("change", "#folder_font", function(){
                if($(this).val() == "folders-pro") {
                    $(this).val("").trigger("change");
                    window.open("<?php echo esc_url($this->getFoldersUpgradeURL()) ?>", "_blank");
                }
            });
            $(document).on("click",".view-shortcodes", function(e){
                e.preventDefault();
                $("#keyboard-shortcut").show();
            });
            $(document).on("change", "#folder_size", function(){
                if($(this).val() == "folders-pro" || $(this).val() == "folders-pro-item" || $(this).val() == "folders-item-pro") {
                    $(this).val("16").trigger("change");
                    window.open("<?php echo esc_url($this->getFoldersUpgradeURL()) ?>", "_blank");
                }
            });
            $(".accordion-header:first").trigger("click");
            $("#folder_font, #folder_size").change(function(){
                setCSSProperties();
            });
            $(document).on("click", "input[name='customize_folders[show_media_details]']", function(){
                if($("#show_media_details").is(":checked")) {
                    $(".media-setting-box").addClass("active");
                } else {
                    $(".media-setting-box").removeClass("active");
                }
            });
            $(document).on("change", "input[name='customize_folders[default_icon_color]']:checked", function(){
                setCSSProperties();
            });
            setCSSProperties();
            $('.color-field').spectrum({
                chooseText: "Submit",
                preferredFormat: "hex",
                showInput: true,
                cancelText: "Cancel",
                move: function (color) {
                    $(this).val(color.toHexString());
                    setCSSProperties();
                },
                change: function (color) {
                    $(this).val(color.toHexString());
                    setCSSProperties();
                }
            });
            $(document).on("click", "input[name='customize_folders[remove_folders_when_removed]']", function(e){
                if($(this).is(":checked")) {
                    $("#remove-folders-data-box").show();
                    //setFoldersRemoveStatus("on");
                } else {
                    setFoldersRemoveStatus("off");
                }
            })
            $(document).on("click", ".import-folder-data", function(e){
                selectedItem = $(this).closest("tr").data("plugin");
                if(!$(this).hasClass("in-popup")) {
                    var pluginName = $(this).closest("tr").find(".plugin-name").html();
                    var pluginFolders = parseInt($(this).closest("tr").data("folders"));
                    var popupTitle = importTitle.replace("%plugin%", pluginName);
                    $(".import-folder-title").html(popupTitle);
                    var popupDesc = importDesc.replace("%plugin%", "<b>" + pluginName + "</b>");
                    popupDesc = popupDesc.replace("%d", "<b>" + pluginFolders + "</b>");
                    $(".import-folder-note").html(popupDesc);
                    $("#import-plugin-data").show();
                } else {
                    importPluginData();
                }
            });
            $(document).on("click", ".remove-folders-data", function(e){
                e.preventDefault();
                $("#remove-confirmation-box").show();
                $("#delete-input").focus();
            });
            $(document).on("keyup", "#delete-input", function(){
                if($.trim($(this).val()) != "") {
                    var inputVal = $.trim($(this).val()).toLowerCase();
                    if (inputVal == "delete") {
                        $("#remove-folders-data-button").prop("disabled", false);
                        $(".delete-confirmation-message").html("<?php esc_html_e('This will delete all existing folders & settings', 'folders'); ?>");
                    } else {
                        $("#remove-folders-data-button").prop("disabled", true);
                        if (inputVal != "") {
                            var textLen = inputVal.length;
                            var curStr = ("delete").substring(0, textLen);
                            if (curStr != inputVal) {
                                $(".delete-confirmation-message").html("<?php esc_html_e('Please type DELETE and click on the "Delete" button to confirm', 'folders'); ?>");
                            } else {
                                $(".delete-confirmation-message").html("");
                            }
                        } else {
                            $(".delete-confirmation-message").html("");
                        }
                    }
                } else {
                    $(".delete-confirmation-message").html("");
                }
            });
            $(document).on("submit", "#remove_folders_data", function(e){
                e.preventDefault();
                if($.trim($("#delete-input").val()).toLowerCase() == "delete") {
                    $.ajax({
                        url: "<?php echo esc_url(admin_url("admin-ajax.php")) ?>",
                        data: {
                            action: 'wcp_remove_all_folders_data',
                            nonce: $("#remove-folder-nonce").val()
                        },
                        type: 'post',
                        success: function(res) {
                            <?php
                            $redirectURL = $this->getFolderSettingsURL();
                            if (!empty($redirectURL)) {
                                $page        = filter_input(INPUT_POST, 'tab_page');
                                $type        = filter_input(INPUT_GET, 'setting_page');
                                $type        = empty($type) ? "" : "&setting_page=".esc_attr($type);
                                $redirectURL = $redirectURL.$type;
                                if (!empty($page)) {
                                    $redirectURL .= "&setting_page=".esc_attr($page);
                                }
                            }

                            $redirectURL = $redirectURL."&note=2";
                            ?>
                            window.location = "<?php echo esc_url($redirectURL) ?>";
                        }
                    });
                }
                return false;
            });
            $(document).on("change", "#delete-input", function(){
                if($.trim($(this).val()).toLowerCase() == "delete") {
                    $("#remove-folders-data-button").prop("disabled", false);
                } else {
                    $("#remove-folders-data-button").prop("disabled", true);
                }
            });
            $(document).on("click", "#import-folder-button", function(e){
                importPluginData();
            });
            $(document).on("click", "#folders_by_user_roles", function(e){
                if($(this).is(":checked")) {
                    $(".folder-user-settings").addClass("active");
                } else {
                    $(".folder-user-settings").removeClass("active");
                }
            });
            $(document).on("click", ".remove-folder-data", function(e){
                selectedItem = $(this).closest("tr").data("plugin");
                var pluginName = $(this).closest("tr").find(".plugin-name").html();
                var pluginFolders = parseInt($(this).closest("tr").data("folders"));
                var popupTitle = removeTitle.replace("%plugin%", pluginName);
                $(".remove-folder-title").html(popupTitle);
                var popupDesc = removeDesc.replace("%plugin%", "<b>" + pluginName + "</b>");
                popupDesc = popupDesc.replace("%d", "<b>" + pluginFolders + "</b>");
                $(".remove-folder-note").html(popupDesc);
                $("#remove-plugin-data").show();
            });
            $(document).on("click", "#remove-folder-button", function(){
                removePluginData();
            });
            setTooltipPosition();

            $(document).on("click", ".checkbox-color", function(){
                setCSSProperties();
            });

            $(document).on("change", ".checkbox-color", function(){
                setCSSProperties();
            });
            $(document).on("click", "#use_folder_undo", function(){
                if($(this).is(":checked")) {
                    $(".timeout-settings").addClass("active");
                } else {
                    $(".timeout-settings").removeClass("active");
                }
            });
            $('.enable-replace-media').hover(
                function(){
                    //$(this).addClass('hover')
                },
                function(){
                    $(this).removeClass('show')
                }
            )
            $('.enable-replace-media .html-tooltip').hover(
                function(){
                    $(this).closest(".enable-replace-media").addClass('show')
                },
                function(){
                    //$(this).removeClass('show')
                }
            )
        });

        function setFoldersRemoveStatus(status) {
            $.ajax({
                url: "<?php echo esc_url(admin_url("admin-ajax.php")) ?>",
                data: {
                    'action': 'wcp_update_folders_uninstall_status',
                    'status': status,
                    'nonce': "<?php echo esc_attr(wp_create_nonce("wcp_folders_uninstall_status")) ?>"
                },
                type: 'post',
                success: function (res) {

                }
            });
        }

        var totalAttachments = 0;

        function importPluginData() {
            $("#import-folder-button").addClass("button");
            $("#import-folder-button").prop("disabled", true);
            $(".import-folder-data").prop("disabled", true);
            $(".other-plugins-"+selectedItem+" .import-folder-data .spinner").addClass("active");
            totalAttachments = 0;
            importPluginDataByPage(1);
        }

        function importPluginDataByPage(pageNo) {
            $.ajax({
                url: "<?php echo esc_url(admin_url("admin-ajax.php")) ?>",
                data: {
                    'plugin': $(".other-plugins-"+selectedItem).data("plugin"),
                    'nonce': $(".other-plugins-"+selectedItem).data("nonce"),
                    'action': 'wcp_import_plugin_folders_data',
                    'paged' : pageNo,
                    'attached': totalAttachments
                },
                type: 'post',
                success: function(res){
                    var response = $.parseJSON(res);
                    if(response.status == -1) {
                        totalAttachments = 0;
                        $(".import-folder-data").prop("disabled", false);
                        $(".other-plugins-"+selectedItem+" .import-folder-data .spinner").removeClass("active");
                        $("#import-third-party-plugin-data").hide();
                        $("#no-more-folder-credit").show();
                        $("#import-folder-button").removeClass("button");
                        $("#import-folder-button").prop("disabled", false);
                    } else if(response.status) {
                        $(".other-plugins-"+response.data.plugin+" .import-message").html(response.message).addClass("success-import");

                        if(parseInt(response.data.pages) > parseInt(response.data.current)) {
                            totalAttachments = response.data.attachments;
                            importPluginDataByPage(parseInt(response.data.current)+1);
                        } else {
                            totalAttachments = 0;
                            $(".other-plugins-"+response.data.plugin+" .import-folder-data").remove();
                            $(".import-folder-data").prop("disabled", false);
                        }
                    } else {
                        $(".other-plugins-"+response.data.plugin+" .import-message").html(response.message).addClass("error-import");
                        $(".other-plugins-"+response.data.plugin+" .import-folder-data").remove();
                        $(".import-folder-data").prop("disabled", false);
                        totalAttachments = 0;
                    }
                    $("#import-folder-button").prop("disabled", false);
                    $("#import-plugin-data").hide();
                }
            });
        }

        function removePluginData() {
            $(".other-plugins-"+selectedItem+" .remove-folder-data .spinner").addClass("active");
            $.ajax({
                url: "<?php echo esc_url(admin_url("admin-ajax.php")) ?>",
                data: {
                    'plugin': $(".other-plugins-"+selectedItem).data("plugin"),
                    'nonce': $(".other-plugins-"+selectedItem).data("nonce"),
                    'action': 'wcp_remove_plugin_folders_data'
                },
                type: 'post',
                success: function(res){
                    var response = $.parseJSON(res);
                    $("#remove-plugin-data").hide();
                    if(response.status) {
                        $(".other-plugins-"+response.data.plugin).remove();
                    } else {
                        $(".other-plugins-"+response.data.plugin+" .import-message").html(response.message).addClass("error-import");
                        $(".other-plugins-"+response.data.plugin+" .remove-folder-data .spinner").removeClass("active");
                    }

                    if($("#import-folders-popup .plugin-import-table tr").length == 0) {
                        $("#import-folders-popup").hide();
                        $(".has-other-plugins").remove();
                        $(".no-more-plugins").addClass("active");
                    }
                }
            });
        }

        function setCSSProperties() {
            if(jQuery("#new_folder_color").length && $("#new_folder_color").val() != "") {
                $("#add-new-folder").css("border-color", $("#new_folder_color").val());
                $("#add-new-folder").css("background-color", $("#new_folder_color").val());
            } else  if ($("input[name='customize_folders[new_folder_color]']:checked").length) {
                $("#add-new-folder").css("border-color", $("input[name='customize_folders[new_folder_color]']:checked").val());
                $("#add-new-folder").css("background-color", $("input[name='customize_folders[new_folder_color]']:checked").val());
            }

            if($("#bulk_organize_button_color").length && $("#bulk_organize_button_color").val() != "") {
                $(".organize-button").css("border-color", $("#bulk_organize_button_color").val());
                $(".organize-button").css("background-color", $("#bulk_organize_button_color").val());
                $(".organize-button").css("color", "#ffffff");
            } else if ($("input[name='customize_folders[bulk_organize_button_color]']:checked").length) {
                $(".organize-button").css("border-color", $("input[name='customize_folders[bulk_organize_button_color]']:checked").val());
                $(".organize-button").css("background-color", $("input[name='customize_folders[bulk_organize_button_color]']:checked").val());
                $(".organize-button").css("color", "#ffffff");
            }

            if($("#dropdown_color").length && $("#dropdown_color").val() != "") {
                $(".media-select").css("border-color", $("#dropdown_color").val());
                $(".media-select").css("color", $("#dropdown_color").val());
            } else if ($("input[name='customize_folders[dropdown_color]']:checked").length) {
                $(".media-select").css("border-color", $("input[name='customize_folders[dropdown_color]']:checked").val());
                $(".media-select").css("color", $("input[name='customize_folders[dropdown_color]']:checked").val());
            }

            if($("#folder_bg_color").length && $("#folder_bg_color").val() != "") {
                $(".all-posts.active-item-link").css("border-color", $("#folder_bg_color").val());
                $(".all-posts.active-item-link").css("background-color", $("#folder_bg_color").val());
                $(".all-posts.active-item-link").css("color", "#ffffff");
            } else if ($("input[name='customize_folders[folder_bg_color]']:checked").length) {
                $(".all-posts.active-item-link").css("border-color", $("input[name='customize_folders[folder_bg_color]']:checked").val());
                $(".all-posts.active-item-link").css("background-color", $("input[name='customize_folders[folder_bg_color]']:checked").val());
                $(".all-posts.active-item-link").css("color", "#ffffff");
            }

            $("#custom-css").html("");
            if($("#folder_font").val() != "") {
                font_val = $("#folder_font").val();
                if(font_val == "System Stack") {
                    font_val = "-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell,Helvetica Neue,sans-serif";
                } else {
                    $('head').append('<link href="https://fonts.googleapis.com/css?family=' + font_val + ':400,600,700" rel="stylesheet" type="text/css" class="chaty-google-font">');
                }
                $('.preview-box').css('font-family', font_val);
            } else {
                $('.preview-box').css('style', "");
            }
            if($("#folder_size").val() != "") {
                $(".folder-list li a span, .header-posts a, .un-categorised-items a").css("font-size", $("#folder_size").val()+"px");
            } else {
                $(".folder-list li a span, .header-posts a, .un-categorised-items a").css("font-size", "14px");
            }

            var folderColor = "#334155";
            if($("input[name='customize_folders[default_icon_color]']:checked").length) {
                folderColor = $("input[name='customize_folders[default_icon_color]']:checked").val();
            }
            $(".folder-list i").css("color", folderColor);
        }

        $(window).on("scroll", function(){
            setTooltipPosition();
        }).on("resize", function(){
            setTooltipPosition();
        });

        function setTooltipPosition() {
            if($(".html-tooltip:not(.no-position)").length) {
                $(".html-tooltip:not(.no-position)").each(function(){
                    if($(this).offset().top - $(window).scrollTop() > 540) {
                        $(this).addClass("top").removeClass("side").removeClass("bottom");
                        $(this).find(".tooltip-text").attr("style","");
                        $(this).find(".tooltip-text").removeClass("hide-arrow");
                    } else if($(window).height() - ($(this).offset().top - $(window).scrollTop()) > 460) {
                        $(this).addClass("bottom").removeClass("top").removeClass("side");
                        $(this).find(".tooltip-text").attr("style","");
                        $(this).find(".tooltip-text").removeClass("hide-arrow");
                    } else {
                        $(this).addClass("side").removeClass("top").removeClass("bottom");
                        if($(this).find(".tooltip-text").length) {
                            $(this).find(".tooltip-text").attr("style","");
                            $(this).find(".tooltip-text").removeClass("hide-arrow");

                            if($(this).find(".tooltip-text").offset().top - $(window).scrollTop() - 50 < 0) {
                                $(this).find(".tooltip-text").css("margin-top", Math.abs($(this).find(".tooltip-text").offset().top - $(window).scrollTop() - 50)+"px");
                                $(this).find(".tooltip-text").addClass("hide-arrow");
                            } else {
                                $(this).find(".tooltip-text").attr("style","");
                                if(($(this).find(".tooltip-text").offset().top + parseInt($(this).find(".tooltip-text").outerHeight()) - $(window).scrollTop() - $(window).height()) > 0) {
                                    $(this).find(".tooltip-text").css("margin-top", ((-1)*Math.abs($(this).find(".tooltip-text").offset().top + parseInt($(this).find(".tooltip-text").outerHeight()) - $(window).scrollTop() - $(window).height()) - 10)+"px");
                                    $(this).find(".tooltip-text").addClass("hide-arrow");
                                }
                            }
                        }
                    }
                });
            }
        }
    }));
</script>
<div id="custom-css">

</div>
<?php
// Check website is hosted on wp.org or not
$wp_status = get_option("is_web_hosted_on_wp");
if ($wp_status === false) {
    $site_url = site_url("/");
    $domain   = wp_parse_url($site_url);

    $options = [
        'http' => ['ignore_errors' => true],
    ];

    $webLink = $domain['host'];

    $context  = stream_context_create($options);
    $response = wp_remote_get('https://public-api.wordpress.com/rest/v1/sites/'.$webLink);
    if (!is_wp_error($response)) {
        $data = wp_remote_retrieve_body($response);
        $response = json_decode($data, true);

        if (!empty($response) && is_array($response)) {
            if (isset($response['ID']) && !empty($response['ID'])) {
                add_option("is_web_hosted_on_wp", "yes");
                $wp_status = "yes";
            }
        }
    }
}//end if

if ($wp_status === false) {
    if (!function_exists("get_current_user_id")) {
        add_option("is_web_hosted_on_wp", "yes");
        $wp_status = "yes";
    }
}

if ($wp_status === false) {
    add_option("is_web_hosted_on_wp", "no");
}

$show_media_popup = false;
if ($wp_status == "yes") {
    $popup_shown = get_option("is_wp_media_popup_shown");
    if ($popup_shown === false) {
        $show_media_popup = true;
        add_option("is_wp_media_popup_shown", 1);
    } else {
        if (!is_numeric($popup_shown)) {
            $popup_shown = 1;
        }

        $popup_shown++;
        if ($popup_shown < 4) {
            $show_media_popup = true;
        }

        update_option("is_wp_media_popup_shown", $popup_shown);
    }
}

?>
<div class="wrap">
    <h1><?php esc_html_e('Folders Settings', 'folders'); ?></h1>
    <?php
    settings_fields('folders_settings');
    settings_fields('default_folders');
    settings_fields('customize_folders');
    $options           = get_option('folders_settings');
    $default_folders   = get_option('default_folders');
    $customize_folders = get_option('customize_folders');
    $default_folders   = (empty($default_folders) || !is_array($default_folders)) ? [] : $default_folders;
    do_settings_sections(__FILE__);
    delete_transient("premio_folders_without_trash");
    $note = filter_input(INPUT_GET, "note");
    ?>
    <?php if ($note == 1) { ?>
        <div class="folder-notification notice notice-success is-dismissible">
            <div class="folder-notification-title"><?php esc_html_e("Changes Saved", "folders") ?></div>
            <div class="folder-notification-note"><?php esc_html_e("Your changes have been saved.", "folders") ?></div>
        </div>
    <?php } else if ($note == 2) {?>
        <div class="folder-notification notice notice-error is-dismissible">
            <div class="folder-notification-title"><?php esc_html_e("Folders Deleted", "folders") ?></div>
            <div class="folder-notification-note"><?php esc_html_e("All folders has been successfully deleted.", "folders") ?></div>
        </div>
    <?php } ?>
    <?php if ($setting_page != "license-key") { ?>
        <form action="options.php" method="post" id="setting-form">
            <input type="hidden" name="tab_page" value="<?php echo esc_attr($setting_page) ?>">
    <?php } ?>
        <div class="folders-tabs">
            <div class="folder-tab-menu">
                <ul>
                    <li><a class="<?php echo esc_attr(($setting_page == "folder-settings") ? "active" : "") ?>" href="<?php echo esc_url($settingURL."&setting_page=folder-settings") ?>"><?php esc_html_e('Folders Settings', 'folders'); ?></a></li>
                    <li><a class="<?php echo esc_attr(($setting_page == "folders-by-user") ? "active" : "") ?>" href="<?php echo esc_url($settingURL."&setting_page=folders-by-user") ?>"><?php esc_html_e('User Restrictions', 'folders'); ?></a></li>
                    <li><a class="<?php echo esc_attr(($setting_page == "customize-folders") ? "active" : "") ?>" href="<?php echo esc_url($settingURL."&setting_page=customize-folders") ?>"><?php esc_html_e('Customize Folders', 'folders'); ?></a></li>
                    <li><a class="<?php echo esc_attr(($setting_page == "notification-settings") ? "active" : "") ?>" href="<?php echo esc_url($settingURL."&setting_page=notification-settings") ?>"><?php esc_html_e('Notifications', 'folders'); ?></a></li>
                    <li><a class="<?php echo esc_attr(($setting_page == "folders-import") ? "active" : "") ?>" href="<?php echo esc_url($settingURL."&setting_page=folders-import") ?>"><?php esc_html_e('Tools', 'folders'); ?></a></li>
                    <?php if ($isInSettings) { ?>
                        <li><a class="<?php echo esc_attr(($setting_page == "upgrade-to-pro") ? "active" : "") ?>" href="<?php echo esc_url($settingURL."&setting_page=upgrade-to-pro") ?>"><?php esc_html_e('Upgrade to Pro', 'folders'); ?></a></li>
                    <?php } ?>
                </ul>
            </div>
            <div class="folder-tab-content">

                <?php include_once dirname(dirname(__FILE__))."/admin/settings-tabs/folder-settings.php"; ?>

                <?php include_once dirname(dirname(__FILE__))."/admin/settings-tabs/customize-folders.php"; ?>

                <?php include_once dirname(dirname(__FILE__))."/admin/settings-tabs/folders-import.php"; ?>

                <?php include_once dirname(dirname(__FILE__))."/admin/settings-tabs/notification-settings.php"; ?>

                <div class="tab-content <?php echo esc_attr(($setting_page == "upgrade-to-pro") ? "active" : "") ?>">
                    <?php if ($setting_page == "upgrade-to-pro") { ?>
                        <?php include_once "upgrade-table.php"; ?>
                    <?php } ?>
                </div>

                <?php include_once dirname(dirname(__FILE__))."/admin/settings-tabs/folders-by-user.php"; ?>


            </div>
        </div>
        <?php
        ?>
        <input type="hidden" name="folder_nonce" value="<?php echo esc_attr(wp_create_nonce("folder_settings")) ?>">
        <input type="hidden" name="folder_page" value="<?php echo filter_input(INPUT_SERVER, "REQUEST_URI") ?>">
        <?php if ($setting_page != "upgrade-to-pro") { ?>
    </form>
        <?php } ?>
</div>

<div class="folder-popup-form" id="import-plugin-data">
    <div class="popup-form-content">
        <div class="popup-content">
            <div class="close-popup-button">
                <a class="" href="javascript:;"><span></span></a>
            </div>
            <div class="import-folder-title"></div>
            <div class="import-folder-note">Are you sure you'd like to import $x folders from $plugin?</div>
            <div class="folder-form-buttons">
                <button type="submit" class="form-submit-btn" id="import-folder-button"><?php esc_html_e("Import", 'folders'); ?></button>
                <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", 'folders'); ?></a>
            </div>
        </div>
    </div>
</div>

<?php
// if($plugin['is_exists']) { ?>
<div class="folder-popup-form" id="import-folders-popup">
    <div class="popup-form-content">
        <div class="popup-content">
            <div class="close-popup-button">
                <a class="" href="javascript:;"><span></span></a>
            </div>
            <div class="import-plugin-title"><?php esc_html_e("Import data", 'folders'); ?></div>
            <div class="plugin-import-table">
                <div class="import-folder-table">
                    <table>
                        <tbody>
                        <?php foreach ($plugin_info as $slug => $plugin) { ?>
                            <?php if ($plugin['is_exists']) { ?>
                                <tr class="other-plugins-<?php echo esc_attr($slug) ?>" data-plugin="<?php echo esc_attr($slug) ?>" data-nonce="<?php echo esc_attr(wp_create_nonce("import_data_from_".$slug)) ?>" data-folders="<?php echo esc_attr($plugin['total_folders']) ?>" data-attachments="<?php echo esc_attr($plugin['total_attachments']) ?>">
                                    <th class="plugin-name"><?php echo esc_attr($plugin['name']) ?></th>
                                    <td>
                                        <span class="import-message"><?php printf(esc_html__("%1\$s folder%2\$s and %3\$s attachment%4\$s", "folders"), "<b>".esc_attr($plugin['total_folders'])."</b>", ($plugin['total_folders'] > 1) ? esc_html__("s") : "", "<b>".esc_attr($plugin['total_attachments'])."</b>", ($plugin['total_attachments'] > 1) ? esc_html__("s") : "") ?></span>
                                        <button type="button" class="button button-primary import-folder-data in-popup"><?php esc_html_e("Import", "folders"); ?> <span class="spinner"></span></button>
                                        <button type="button" class="button button-secondary remove-folder-data in-popup"><?php esc_html_e("Delete plugin data", "folders"); ?> <span class="spinner"></span></button>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="folder-form-buttons">
                <div class=""></div>
                <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Close", 'folders'); ?></a>
            </div>
        </div>
    </div>
</div>
<?php
// } ?>

<div class="folder-popup-form" id="remove-plugin-data">
    <div class="popup-form-content">
        <div class="popup-content">
            <div class="close-popup-button">
                <a class="" href="javascript:;"><span></span></a>
            </div>
            <div class="remove-folder-title"><?php esc_html_e("Are you sure?", 'folders'); ?></div>
            <div class="remove-folder-note"></div>
            <div class="folder-form-buttons">
                <button type="submit" class="form-submit-btn delete-folder-plugin" id="remove-folder-button"><?php esc_html_e("Delete plugin data", 'folders'); ?></button>
                <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", 'folders'); ?></a>
            </div>
        </div>
    </div>
</div>

<div class="folder-popup-form remove-folders-box" id="remove-folders-data-box">
    <div class="popup-form-content">
        <div class="popup-content">
            <div class="close-popup-button close-remove-folders">
                <a class="" href="javascript:;"><span></span></a>
            </div>
            <div class="remove-folder-title"><?php esc_html_e("Are you sure?", 'folders'); ?></div>
            <div class="remove-folder-note"><?php printf(esc_html__("Folders will remove all created folders once you remove the plugin. We recommend you %1\$snot to use this feature%2\$s if you plan to use Folders in future.", 'folders'), "<b>", "</b>"); ?></div>
            <div class="folder-form-buttons">
                <a href="javascript:;" class="form-cancel-btn cancel-folders"><?php esc_html_e("Cancel", 'folders'); ?></a>
                <button type="submit" class="form-cancel-btn delete-button"><?php esc_html_e("I want to delete anyway", 'folders'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="folder-popup-form" id="remove-confirmation-box">
    <div class="popup-form-content">
        <div class="popup-content">
            <form id="remove_folders_data" autocomplete="off" >
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="remove-folder-title"></div>
                <div class="remove-folder-note text-left">
                    <b><?php esc_html_e("Type DELETE to confirm", 'folders'); ?></b>
                    <div class="input-box">
                        <input autocomplete="off" type="text" id="delete-input" name="delete" >
                    </div>
                    <div class="delete-confirmation-message"></div>
                </div>
                <div class="folder-form-buttons">
                    <input type="hidden" name="nonce" id="remove-folder-nonce" value="<?php echo esc_attr(wp_create_nonce("remove_folders_data")) ?>">
                    <input type="hidden" name="action" value="remove_all_folders_data">
                    <button disabled type="submit" class="form-submit-btn delete-button" id="remove-folders-data-button"><?php esc_html_e("Delete", 'folders'); ?></button>
                    <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", 'folders'); ?></a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
if ($wp_status == "yes" && $show_media_popup) {
    add_option("is_wp_media_popup_shown", "yes");
    ?>
    <div class="folder-popup-form" id="wordpress-popup">
        <div class="popup-form-content">
            <div class="popup-content">
                <div class="popup-form-data">
                    <div class="close-popup-button">
                        <a class="" href="javascript:;"><span></span></a>
                    </div>
                    <div class="popup-folder-title">
                        <?php esc_html_e("Seems you’re using WordPress.com", "folders") ?>
                    </div>
                    <div class="folder-form-message" style="padding: 25px 10px;" >
                        <?php esc_html_e('You need to alter a setting to make Folders compatible with WordPress.com media library.', "folders") ?>
                    </div>
                    <div class="folder-form-buttons" style="display:block;">
                        <a class="form-submit-btn a-inline" target="_blank" href="https://premio.io/help/folders/how-to-activate-folders-for-wordpress-com-media-library/" ><?php esc_html_e("Learn how to enable Folders on media library", 'folders'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php }//end if
?>

<?php
$option = get_option("folder_intro_box");
if (($option == "show" || get_option("folder_redirect_status") == 2) && $is_plugin_exists) { ?>
    <div class="folder-popup-form" id="import-third-party-plugin-data" style="display: block" ?>
        <div class="popup-form-content">
            <div class="popup-content">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="import-plugin-title"><?php esc_html_e("Import data", 'folders'); ?></div>
                <div class="import-plugin-note"><?php esc_html_e("We've detected that you use another folders plugin. Would you like the Folders plugin to import your current folders? Keep in mind you can always do it in Folders Settings -> Import", 'folders'); ?></div>
                <div class="plugin-import-table">
                    <div class="import-folder-table">
                        <table>
                            <tbody>
                            <?php foreach ($plugin_info as $slug => $plugin) {
                                if ($plugin['is_exists']) { ?>
                                    <tr class="other-plugins-<?php echo esc_attr($slug) ?>" data-plugin="<?php echo esc_attr($slug) ?>" data-nonce="<?php echo esc_attr(wp_create_nonce("import_data_from_".$slug)) ?>" data-folders="<?php echo esc_attr($plugin['total_folders']) ?>" data-attachments="<?php echo esc_attr($plugin['total_attachments']) ?>">
                                        <th class="plugin-name"><?php echo esc_attr($plugin['name']) ?></th>
                                        <td>
                                            <button type="button" class="button button-primary import-folder-data in-popup"><?php esc_html_e("Import", "folders"); ?> <span class="spinner"></span></button>
                                            <span class="import-message"><?php printf(esc_html__("%1\$s folder%2\$s and %3\$s attachment%4\$s", "folders"), "<b>".esc_attr($plugin['total_folders'])."</b>", ($plugin['total_folders'] > 1) ? esc_html__("s") : "", "<b>".esc_attr($plugin['total_attachments'])."</b>", ($plugin['total_attachments'] > 1) ? esc_html__("s") : "") ?></span>
                                        </td>
                                    </tr>
                                <?php }
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="folder-form-buttons">
                    <div class=""></div>
                    <a href="javascript:;" id="cancel-plugin-import" class="form-cancel-btn"><?php esc_html_e("Close", 'folders'); ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php
    if ($option != "show") {
        update_option("folder_redirect_status", 3);
    }
}//end if
?>

<div class="folder-popup-form" id="no-more-folder-credit">
    <div class="popup-form-content">
        <div class="popup-content">
            <div class="close-popup-button">
                <a class="" href="javascript:;"><span></span></a>
            </div>
            <div class="add-update-folder-title" id="folder-limitation-message">
                <?php esc_html_e("You've reached the 10 folder limitation!", 'folders'); ?>
            </div>
            <div class="folder-form-message">
                <?php esc_html_e("Unlock unlimited amount of folders by upgrading to one of our pro plans.", 'folders'); ?>
            </div>
            <div class="folder-form-buttons">
                <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", 'folders'); ?></a>
                <a href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" target="_blank" class="form-submit-btn"><?php esc_html_e("See Pro Plans", 'folders'); ?></a>
            </div>
        </div>
    </div>
</div>
<?php require_once "help.php" ?>

<div class="folder-popup-form" id="keyboard-shortcut">
    <div class="popup-form-content">
        <div class="popup-content">
            <div class="close-popup-button">
                <a class="" href="javascript:;"><span></span></a>
            </div>
            <div class="import-plugin-title"><?php esc_html_e("Keyboard shortcuts (Ctrl+K)", 'folders'); ?></div>
            <div class="plugin-import-table">
                <table class="keyboard-shortcut">
                    <tr>
                        <th><?php esc_html_e("Create New Folder", "folders") ?></th>
                        <td><span class="key-button">Shift</span><span class="plus-button">+</span><span class="key-button">N</span> </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Rename Folder", "folders") ?></th>
                        <td><span class="key-button">F2</span></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Copy Folder", "folders") ?></th>
                        <td><span class="key-button">Ctrl</span><span class="plus-button">+</span><span class="key-button">C</span> </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Cut Folder", "folders") ?></th>
                        <td><span class="key-button">Ctrl</span><span class="plus-button">+</span><span class="key-button">X</span> </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Paste Folder", "folders") ?></th>
                        <td><span class="key-button">Ctrl</span><span class="plus-button">+</span><span class="key-button">V</span> </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Duplicate Folder", "folders") ?></th>
                        <td><span class="key-button">Ctrl</span><span class="plus-button">+</span><span class="key-button">D</span> </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Delete Folder", "folders") ?></th>
                        <td><span class="key-button">Delete</span></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Next Folder", "folders") ?></th>
                        <td><span class="key-button"><span class="dashicons dashicons-arrow-down-alt"></span></span> </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Previous Folder", "folders") ?></th>
                        <td><span class="key-button"><span class="dashicons dashicons-arrow-up-alt"></span></span> </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Expand Folder", "folders") ?></th>
                        <td><span class="key-button"><span class="dashicons dashicons-arrow-right-alt"></span></span> </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Collapse Folder", "folders") ?></th>
                        <td><span class="key-button"><span class="dashicons dashicons-arrow-left-alt"></span></span> </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Re-order folders to upwards", "folders") ?></th>
                        <td><span class="key-button">Ctrl</span><span class="plus-button">+</span><span class="key-button"><span class="dashicons dashicons-arrow-up-alt"></span></span></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e("Re-order folders to downwards", "folders") ?></th>
                        <td><span class="key-button">Ctrl</span><span class="plus-button">+</span><span class="key-button"><span class="dashicons dashicons-arrow-down-alt"></span></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
