<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- do not change here, Free/Pro URL Change -->
<link rel='stylesheet' href='<?php echo WCP_FOLDER_URL ?>assets/css/settings.css?ver=<?php echo WCP_FOLDER_VERSION ?>' type='text/css' media='all' />
<link rel='stylesheet' href='<?php echo WCP_FOLDER_URL ?>assets/css/folder-icon.css?ver=<?php echo WCP_FOLDER_VERSION ?>' type='text/css' media='all' />
<link rel='stylesheet' href='<?php echo WCP_FOLDER_URL ?>assets/css/spectrum.min.css?ver=<?php echo WCP_FOLDER_VERSION ?>' type='text/css' media='all' />
<?php if($setting_page == "folder-settings") { ?>
    <link rel='stylesheet' href='<?php echo WCP_FOLDER_URL ?>assets/css/select2.min.css?ver=<?php echo WCP_FOLDER_VERSION ?>' type='text/css' media='all' />
    <script type="text/javascript" src='<?php echo WCP_FOLDER_URL ?>assets/js/select2.min.js?ver=<?php echo WCP_FOLDER_VERSION ?>'  ></script>
<?php } ?>
<script src="<?php echo WCP_FOLDER_URL ?>assets/js/spectrum.min.js"></script>
<style>
    <?php if ( function_exists( 'is_rtl' ) && is_rtl() ) { ?>
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
        var importDesc = "<?php esc_html_e("Are you sure you'd like to import %d folders from %plugin%?", "folders"); ?>";
        var removeTitle = "<?php esc_html_e("Are you sure?", "folders"); ?>";
        var removeDesc = "<?php esc_html_e("You're about to delete %plugin%'s folders. Are you sure you'd like to proceed?", "folders"); ?>";
        $(document).ready(function(){
            <?php if($setting_page == "folder-settings") { ?>
                $(".select2-box").select2();
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
            $(document).on("change", "#folder_size", function(){
                if($(this).val() == "folders-pro" || $(this).val() == "folders-pro-item") {
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
                        $(".delete-confirmation-message").html("<?php esc_html_e('This will delete all existing folders & settings', WCP_FOLDER); ?>");
                    } else {
                        $("#remove-folders-data-button").prop("disabled", true);
                        if (inputVal != "") {
                            var textLen = inputVal.length;
                            var curStr = ("delete").substring(0, textLen);
                            if (curStr != inputVal) {
                                $(".delete-confirmation-message").html("<?php esc_html_e('Please type DELETE and click on the "Delete" button to confirm', WCP_FOLDER); ?>");
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
                        url: "<?php echo admin_url("admin-ajax.php") ?>",
                        data: {
                            action: 'wcp_remove_all_folders_data',
                            nonce: $("#remove-folder-nonce").val()
                        },
                        type: 'post',
                        success: function(res) {
                            <?php
                            $redirectURL = $this->getFolderSettingsURL();
                            if(!empty($redirectURL)) {
                                $page = isset($_POST['tab_page'])?$_POST['tab_page']:"";
                                $type = filter_input(INPUT_GET, 'setting_page', FILTER_SANITIZE_STRING);
                                $type = empty($type)?"":"&setting_page=".$type;
                                $redirectURL = $redirectURL.$type;
                                if(!empty($page)) {
                                    $redirectURL .= "&setting_page=".$page;
                                }
                            }
                            $redirectURL = $redirectURL."&note=2";
                            ?>
                            window.location = "<?php echo $redirectURL ?>";
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
            /*$(document).on("click", "#remove-folders-data-button", function(e){
                e.preventDefault();
                $(".folder-popup-form").hide();
                $("#remove-confirmation-box").show();
            });*/
            $(document).on("click", "#import-folder-button", function(e){
                importPluginData();
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
        });

        function setFoldersRemoveStatus(status) {
            $.ajax({
                url: "<?php echo admin_url("admin-ajax.php") ?>",
                data: {
                    'action': 'wcp_update_folders_uninstall_status',
                    'status': status,
                    'nonce': "<?php echo wp_create_nonce("wcp_folders_uninstall_status") ?>"
                },
                type: 'post',
                success: function (res) {

                }
            });
        }

        function importPluginData() {
            $("#import-folder-button").addClass("button");
            $("#import-folder-button").prop("disabled", true);
            $(".other-plugins-"+selectedItem+" .import-folder-data").prop("disabled", true);
            $(".other-plugins-"+selectedItem+" .import-folder-data .spinner").addClass("active");
            $.ajax({
                url: "<?php echo admin_url("admin-ajax.php") ?>",
                data: {
                    'plugin': $(".other-plugins-"+selectedItem).data("plugin"),
                    'nonce': $(".other-plugins-"+selectedItem).data("nonce"),
                    'action': 'wcp_import_plugin_folders_data'
                },
                type: 'post',
                success: function(res){
                    var response = $.parseJSON(res);
                    if(response.status == -1) {
                        $(".other-plugins-"+selectedItem+" .import-folder-data").prop("disabled", false);
                        $(".other-plugins-"+selectedItem+" .import-folder-data .spinner").removeClass("active");
                        $("#import-third-party-plugin-data").hide();
                        $("#no-more-folder-credit").show();
                        $("#import-folder-button").removeClass("button");
                        $("#import-folder-button").prop("disabled", false);
                    } else if(response.status) {
                        $(".other-plugins-"+response.data.plugin+" .import-message").html(response.message).addClass("success-import");
                        $(".other-plugins-"+response.data.plugin+" .import-folder-data").remove();
                    } else {
                        $(".other-plugins-"+response.data.plugin+" .import-message").html(response.message).addClass("error-import");
                        $(".other-plugins-"+response.data.plugin+" .import-folder-data").remove();
                    }
                    $("#import-folder-button").prop("disabled", false);
                    $("#import-plugin-data").hide();
                }
            });
        }

        function removePluginData() {
            $(".other-plugins-"+selectedItem+" .remove-folder-data .spinner").addClass("active");
            $.ajax({
                url: "<?php echo admin_url("admin-ajax.php") ?>",
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
                $('head').append('<link href="https://fonts.googleapis.com/css?family=' + font_val + ':400,600,700" rel="stylesheet" type="text/css" class="chaty-google-font">');
                $('.preview-box').css('font-family', font_val);
            } else {
                $('.preview-box').css('style', "");
            }
            if($("#folder_size").val() != "") {
                $(".folder-list li a span, .header-posts a, .un-categorised-items a").css("font-size", $("#folder_size").val()+"px");
            } else {
                $(".folder-list li a span, .header-posts a, .un-categorised-items a").css("font-size", "14px");
            }
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
<div class="wrap">
    <h1><?php esc_html_e( 'Folders Settings', WCP_FOLDER ); ?></h1>
    <?php
    settings_fields('folders_settings');
    settings_fields('default_folders');
    settings_fields('customize_folders');
    $options = get_option('folders_settings');
    $default_folders = get_option('default_folders');
    $customize_folders = get_option('customize_folders');
    $default_folders = (empty($default_folders) || !is_array($default_folders))?array():$default_folders;
    do_settings_sections( __FILE__ );
    ?>
    <?php if(isset($_GET['note']) && $_GET['note'] == 1) { ?>
        <div class="folder-notification notice notice-success is-dismissible">
            <div class="folder-notification-title"><?php esc_html_e("Changes Saved", "folders") ?></div>
            <div class="folder-notification-note"><?php esc_html_e("Your changes have been saved.", "folders") ?></div>
        </div>
    <?php } if(isset($_GET['note']) && $_GET['note'] == 2) {?>
        <div class="folder-notification notice notice-error is-dismissible">
            <div class="folder-notification-title"><?php esc_html_e("Folders Deleted", "folders") ?></div>
            <div class="folder-notification-note"><?php esc_html_e("All folders has been successfully deleted.", "folders") ?></div>
        </div>
    <?php } ?>
    <?php if($setting_page!="license-key") { ?>
        <form action="options.php" method="post" id="setting-form">
            <input type="hidden" name="tab_page" value="<?php echo esc_attr($setting_page) ?>">
    <?php } ?>
        <div class="folders-tabs">
            <div class="folder-tab-menu">
                <ul>
                    <li><a class="<?php echo esc_attr(($setting_page=="folder-settings")?"active":"") ?>" href="<?php echo esc_url($settingURL."&setting_page=folder-settings") ?>"><?php esc_html_e( 'Folders Settings', WCP_FOLDER ); ?></a></li>
                    <li><a class="<?php echo esc_attr(($setting_page=="customize-folders")?"active":"") ?>" href="<?php echo esc_url($settingURL."&setting_page=customize-folders") ?>"><?php esc_html_e( 'Customize Folders', WCP_FOLDER ); ?></a></li>
                    <li><a class="<?php echo esc_attr(($setting_page=="folders-import")?"active":"") ?>" href="<?php echo esc_url($settingURL."&setting_page=folders-import") ?>"><?php esc_html_e( 'Tools', WCP_FOLDER ); ?></a></li>
                    <?php if($isInSettings) { ?>
                        <li><a class="<?php echo esc_attr(($setting_page=="upgrade-to-pro")?"active":"") ?>" href="<?php echo esc_url($settingURL."&setting_page=upgrade-to-pro") ?>"><?php esc_html_e( 'Upgrade to Pro', WCP_FOLDER ); ?></a></li>
                    <?php } ?>
                </ul>
            </div>
            <div class="folder-tab-content">
                <div class="tab-content <?php echo esc_attr(($setting_page=="folder-settings")?"active":"") ?>" id="folder-settings">
                    <div class="accordion-content no-bp">
                        <div class="accordion-left">
                            <table class="form-table">
                                <tboby>
                                    <?php
                                    $post_types = get_post_types( array( ), 'objects' );
                                    $post_array = array("page", "post", "attachment");
                                    foreach ( $post_types as $post_type ) : ?>
                                        <?php
                                        if ( ! $post_type->show_ui) continue;
                                        $is_checked = !in_array( $post_type->name, $options )?"hide-option":"";
                                        $selected_id = (isset($default_folders[$post_type->name]))?$default_folders[$post_type->name]:"all";
	                                    $is_exists = WCP_Folders::check_for_setting($post_type->name, "default_folders");
	                                    $is_customized = WCP_Folders::check_for_setting($post_type->name, "folders_settings");
                                        if(in_array($post_type->name, $post_array) || $is_customized === true){
                                            ?>
                                            <tr>
                                                <td class="no-padding">
                                                    <label label for="folders_<?php echo esc_attr($post_type->name); ?>" class="custom-checkbox">
                                                        <input type="checkbox" class="folder-select sr-only" id="folders_<?php echo esc_attr($post_type->name); ?>" name="folders_settings[]" value="<?php echo esc_attr($post_type->name); ?>"<?php if ( in_array( $post_type->name, $options ) ) echo ' checked="checked"'; ?>/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td class="" width="260px">
                                                    <label for="folders_<?php echo esc_attr($post_type->name); ?>" ><?php esc_html_e( 'Use Folders with: ', WCP_FOLDER )." ".esc_html_e($post_type->label); ?></label>
                                                </td>
                                                <td class="default-folder">
                                                    <label class="hide-show-option <?php echo esc_attr($is_checked) ?>" for="folders_for_<?php echo esc_attr($post_type->name); ?>" ><?php esc_html_e( 'Default folder: ', WCP_FOLDER ) ?></label>
                                                </td>
                                                <td>
                                                    <select class="hide-show-option <?php echo esc_attr($is_checked) ?>" id="folders_for_<?php echo esc_attr($post_type->name); ?>" name="default_folders[<?php echo esc_attr($post_type->name); ?>]" ?>
                                                        <option value="">All <?php echo esc_attr($post_type->label) ?> Folder</option>
                                                        <option value="-1" <?php echo ($selected_id == -1)?"selected":"" ?>>Unassigned <?php echo esc_attr($post_type->label) ?></option>
                                                        <?php
                                                        if(isset($terms_data[$post_type->name]) && !empty($terms_data[$post_type->name])) {
                                                            foreach ($terms_data[$post_type->name] as $term) {
                                                                if(empty($is_exists) || $is_exists === false) {
	                                                                echo "<option class='pro-select-item' value='folders-pro'>" . esc_attr( $term->name ). " (Pro) 🔑</option>";
                                                                } else {
	                                                                $selected = ( $selected_id == $term->slug ) ? "selected" : "";
	                                                                echo "<option " . esc_attr( $selected ) . " value='" . esc_attr( $term->slug ) . "'>" . esc_attr( $term->name ) . "</option>";
                                                                }
                                                            }
                                                        } ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <?php
                                        } else {
	                                        $show_media_details = "off";
                                            ?>
                                            <tr>
                                                <td style="padding: 15px 10px 15px 0px" colspan="4">
                                                    <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                                        <label for="" class="custom-checkbox send-user-to-pro">
                                                            <input disabled type="checkbox" class="sr-only" name="customize_folders[show_media_details]" id="show_media_details" value="on" <?php checked($show_media_details, "on") ?>>
                                                            <span></span>
                                                        </label>
                                                        <label for="" class="send-user-to-pro">
                                                            <?php esc_html_e( 'Use Folders with: ', WCP_FOLDER )." ".esc_html_e($post_type->label); ?>
                                                            <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", WCP_FOLDER) ?></button>
                                                        </label>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php }
                                    endforeach; ?>
                                    <?php
                                    $show_in_page = !isset($customize_folders['folders_enable_replace_media'])?"yes":$customize_folders['folders_enable_replace_media'];
                                    ?>
                                    <tr>
                                        <td class="no-padding">
                                            <input type="hidden" name="customize_folders[folders_enable_replace_media]" value="no">
                                            <label for="folders_enable_replace_media" class="custom-checkbox">
                                                <input id="folders_enable_replace_media" class="sr-only" <?php checked($show_in_page, "yes") ?> type="checkbox" name="customize_folders[folders_enable_replace_media]" value="yes">
                                                <span></span>
                                            </label>
                                        </td>
                                        <td colspan="3">
                                            <label for="folders_enable_replace_media" ><?php esc_html_e( 'Enable Replace Media', WCP_FOLDER ); ?> <span class="folder-tooltip" data-title="<?php esc_html_e("The Replace Media feature will allow you to replace your media files throughout your website with the click of a button,  which means the file will be replaced for all your posts, pages, etc ", "folders") ?>"><span class="dashicons dashicons-editor-help"></span></span></label>
                                        </td>
                                    </tr>
                                    <?php
                                    $show_in_page = !isset($customize_folders['show_folder_in_settings'])?"no":$customize_folders['show_folder_in_settings'];
                                    ?>
                                    <tr>
                                        <td class="no-padding">
                                            <input type="hidden" name="customize_folders[show_folder_in_settings]" value="no">
                                            <label for="show_folder_in_settings" class="custom-checkbox">
                                                <input id="show_folder_in_settings" class="sr-only" <?php checked($show_in_page, "yes") ?> type="checkbox" name="customize_folders[show_folder_in_settings]" value="yes">
                                                <span></span>
                                            </label>
                                        </td>
                                        <td colspan="3">
                                            <label for="show_folder_in_settings" ><?php esc_html_e( 'Place the Folders settings page nested under "Settings"', WCP_FOLDER ); ?></label>
                                        </td>
                                    </tr>
	                                <?php $val = get_option("folders_show_in_menu"); ?>
                                    <input type="hidden" name="folders_show_in_menu" value="off" />
                                    <tr>
                                        <td width="20" class="no-padding">
                                            <label for="folders_show_in_menu" class="custom-checkbox">
                                                <input class="sr-only" type="checkbox" id="folders_show_in_menu" name="folders_show_in_menu" value="on" <?php checked($val, "on") ?>/>
                                                <span></span>
                                            </label>
                                        </td>
                                        <td colspan="3">
                                            <label for="folders_show_in_menu" ><?php esc_html_e( 'Show the folders also in WordPress menu', WCP_FOLDER ); ?></label>
                                        </td>
                                    </tr>
                                    <!-- Do not make changes here, Only for Free -->
                                    <tr>
                                        <td style="padding: 15px 10px 15px 0px" colspan="4">
                                            <input type="hidden" name="folders_settings1" value="folders">
	                                        <?php
	                                        $show_media_details = !isset($customize_folders['show_media_details'])?"on":$customize_folders['show_media_details'];
	                                        $show_media_details = "off";
	                                        ?>
                                            <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                                <label for="" class="custom-checkbox send-user-to-pro">
                                                    <input disabled type="checkbox" class="sr-only" name="customize_folders[show_media_details]" id="show_media_details" value="on" <?php checked($show_media_details, "on") ?>>
                                                    <span></span>
                                                </label>
                                                <label for="" class="send-user-to-pro">
			                                        <?php esc_html_e("Show media details on hover", "folders"); ?>
                                                    <button type="button" class="upgrade-link" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>"><?php esc_html_e("Upgrade to Pro", WCP_FOLDER) ?></button>
                                                    <span class="html-tooltip bottom">
                                                    <span class="dashicons dashicons-editor-help"></span>
                                                    <span class="tooltip-text top" style="">
                                                        Show useful metadata including title, size, type, date, dimension & more on hover.
                                                        <img src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/folders-media.gif") ?>">
                                                    </span>
                                                </span>
                                                </label>
                                            </a>
                                        </td>
                                    </tr>
                                </tboby>
                            </table>
                            <input type="hidden" name="customize_folders[show_media_details]" value="off">
                            <div class="media-details">
                                <div class="">
                                    <?php
                                    $media_settings = array(
                                        'image_title' => array(
                                            "title" => esc_html__("Title", "folders"),
                                            "default" => "on",
                                        ),
                                        'image_alt_text' =>  array(
                                            "title" => esc_html__("Alternative Text", "folders"),
                                            "default" => "off",
                                        ),
                                        'image_file_url' =>  array(
                                            "title" => esc_html__("File URL", "folders"),
                                            "default" => "off",
                                        ),
                                        'image_dimensions' =>  array(
                                            "title" => esc_html__("Dimensions", "folders"),
                                            "default" => "on",
                                        ),
                                        'image_size' =>  array(
                                            "title" => esc_html__("Size", "folders"),
                                            "default" => "off",
                                        ),
                                        'image_file_name' =>  array(
                                            "title" => esc_html__("Filename", "folders"),
                                            "default" => "off",
                                        ),
                                        'image_type' =>  array(
                                            "title" => esc_html__("Type", "folders"),
                                            "default" => "on",
                                        ),
                                        'image_date' =>  array(
                                            "title" => esc_html__("Date", "folders"),
                                            "default" => "on",
                                        ),
                                        'image_uploaded_by' =>  array(
                                            "title" => esc_html__("Uploaded by", "folders"),
                                            "default" => "off",
                                        )
                                    );
                                    $media_col_settings = isset($customize_folders['media_col_settings']) && is_array($customize_folders['media_col_settings'])?$customize_folders['media_col_settings']:array("image_title","image_dimensions","image_type","image_date");
                                    ?>
                                    <input type="hidden" name="customize_folders[media_col_settings][]" value="all">
                                    <div class="media-setting-box active send-user-to-pro" >
                                        <div class="normal-box">
                                            <select disabled multiple="multiple" name="customize_folders[media_col_settings][]" class="select2-box">
                                                <?php foreach($media_settings as $key=>$media) {
                                                    $selected = $media['default'];
                                                    ?>
                                                    <option <?php selected($selected, "on") ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_attr($media['title']) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <a class="upgrade-box" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                            <button type="button"><?php esc_html_e("Upgrade to Pro", WCP_FOLDER) ?></button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-right">
                            <div class="premio-help">
                                <a href="https://premio.io/help/folders/?utm_source=pluginspage" target="_blank">
                                    <div class="premio-help-btn">
                                        <img src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/premio-help.png") ?>" alt="Premio Help" class="Premio Help" />
                                        <div class="need-help">Need Help</div>
                                        <div class="visit-our">Visit our</div>
                                        <div class="knowledge-base">knowledge base</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="submit-button">
                            <?php submit_button(); ?>
                        </div>
                    </div>
                </div>
                <div class="tab-content <?php echo esc_attr(($setting_page=="customize-folders")?"active":"") ?>" id="customize-folders">
                    <div class="accordion-content">
                        <div class="accordion-left">
                            <table class="form-table">
                                <?php
                                $colors = array(
	                                "#FA166B",
	                                "#0073AA",
	                                "#484848"
                                );
                                $color = !isset($customize_folders['new_folder_color'])||empty($customize_folders['new_folder_color'])?"#FA166B":$customize_folders['new_folder_color'];
                                $setting_color = WCP_Folders::check_for_setting("new_folder_color", "customize_folders");
                                ?>
                                <tr>
                                    <td width="255px" class="no-padding">
                                        <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                            <label for="new_folder_color" ><b>"New Folder"</b> button color <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", WCP_FOLDER) ?></button></label>
                                        </a>
                                    </td>
                                    <td>
                                        <ul class="color-list">
                                            <?php $field_name = "new_folder_color"; foreach ($colors as $key=>$value) { ?>
                                                <li>
                                                    <label class="color-checkbox <?php echo ($color == $value)?"active":"" ?>" for="<?php echo esc_attr($field_name)."-".$key ?>">
                                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".$key ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($value) ?>" <?php checked($color, $value) ?> />
                                                        <span style="background: <?php echo esc_attr($value) ?>"></span>
                                                    </label>
                                                </li>
                                            <?php } $key = 3; ?>
                                            <?php if($setting_color !== false && $setting_color != "#FA166B") { ?>
                                                <li>
                                                    <label class="color-checkbox <?php echo ($color == $setting_color)?"active":"" ?>" for="<?php echo esc_attr($field_name)."-".$key ?>">
                                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".$key ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($setting_color) ?>" <?php checked($color, $setting_color) ?> />
                                                        <span style="background: <?php echo esc_attr($setting_color) ?>"></span>
                                                    </label>
                                                </li>
                                            <?php } ?>
                                            <li>
                                                <a class="upgrade-box-link d-block" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                                    <span class="color-box"><span class="gradient"></span> <span class="color-box-area">Custom</span></span>
                                                    <span class="upgrade-link"><?php echo esc_html_e("Upgrade to Pro", "folders") ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                    <td rowspan="4" >

                                    </td>
                                </tr>
                                <?php
                                $color = !isset($customize_folders['bulk_organize_button_color'])||empty($customize_folders['bulk_organize_button_color'])?"#FA166B":$customize_folders['bulk_organize_button_color'];
                                $setting_color = WCP_Folders::check_for_setting("bulk_organize_button_color", "customize_folders");
                                ?>
                                <tr>
                                    <td class="no-padding">
                                        <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                            <label for="bulk_organize_button_color" ><b>"Bulk Organize"</b> button color <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", WCP_FOLDER) ?></button></label>
                                        </a>
                                    </td>
                                    <td>
                                        <ul class="color-list">
                                            <?php $field_name = "bulk_organize_button_color"; foreach ($colors as $key=>$value) { ?>
                                                <li>
                                                    <label class="color-checkbox <?php echo ($color == $value)?"active":"" ?>" for="<?php echo esc_attr($field_name)."-".$key ?>">
                                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".$key ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($value) ?>" <?php checked($color, $value) ?> />
                                                        <span style="background: <?php echo esc_attr($value) ?>"></span>
                                                    </label>
                                                </li>
                                            <?php } $key = 3; ?>
                                            <?php if($setting_color !== false && $setting_color != "#FA166B") { ?>
                                                <li>
                                                    <label class="color-checkbox <?php echo ($color == $setting_color)?"active":"" ?>" for="<?php echo esc_attr($field_name)."-".$key ?>">
                                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".$key ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($setting_color) ?>" <?php checked($color, $setting_color) ?> />
                                                        <span style="background: <?php echo esc_attr($setting_color) ?>"></span>
                                                    </label>
                                                </li>
                                            <?php } ?>
                                            <li>
                                                <a class="upgrade-box-link d-block" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                                    <span class="color-box"><span class="gradient"></span> <span class="color-box-area">Custom</span></span>
                                                    <span class="upgrade-link"><?php echo esc_html_e("Upgrade to Pro", "folders") ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                                <?php
                                $color = !isset($customize_folders['media_replace_button'])||empty($customize_folders['media_replace_button'])?"#FA166B":$customize_folders['media_replace_button'];
                                $setting_color = WCP_Folders::check_for_setting("media_replace_button", "customize_folders");
                                ?>
                                <tr>
                                    <td class="no-padding">
                                        <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                            <label for="media_replace_button" ><b>"Replace File"</b> media library button <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", WCP_FOLDER) ?></button></label>
                                        </a>
                                    </td>
                                    <td>
                                        <ul class="color-list">
                                            <?php $field_name = "media_replace_button"; foreach ($colors as $key=>$value) { ?>
                                                <li>
                                                    <label class="color-checkbox <?php echo ($color == $value)?"active":"" ?>" for="<?php echo esc_attr($field_name)."-".$key ?>">
                                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".$key ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($value) ?>" <?php checked($color, $value) ?> />
                                                        <span style="background: <?php echo esc_attr($value) ?>"></span>
                                                    </label>
                                                </li>
                                            <?php } $key = 3; ?>
                                            <?php if($setting_color !== false && $setting_color != "#FA166B") { ?>
                                                <li>
                                                    <label class="color-checkbox <?php echo ($color == $setting_color)?"active":"" ?>" for="<?php echo esc_attr($field_name)."-".$key ?>">
                                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".$key ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($setting_color) ?>" <?php checked($color, $setting_color) ?> />
                                                        <span style="background: <?php echo esc_attr($setting_color) ?>"></span>
                                                    </label>
                                                </li>
                                            <?php } ?>
                                            <li>
                                                <a class="upgrade-box-link d-block" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                                    <span class="color-box"><span class="gradient"></span> <span class="color-box-area">Custom</span></span>
                                                    <span class="upgrade-link"><?php echo esc_html_e("Upgrade to Pro", "folders") ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                                <?php
                                $color = !isset($customize_folders['dropdown_color'])||empty($customize_folders['dropdown_color'])?"#484848":$customize_folders['dropdown_color'];
                                $setting_color = WCP_Folders::check_for_setting("dropdown_color", "customize_folders");
                                ?>
                                <tr>
                                    <td class="no-padding">
                                        <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                            <label for="dropdown_color" >Dropdown color <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", WCP_FOLDER) ?></button></label>
                                        </a>
                                    </td>
                                    <td>
                                        <ul class="color-list">
                                            <?php $field_name = "dropdown_color"; foreach ($colors as $key=>$value) { ?>
                                                <li>
                                                    <label class="color-checkbox <?php echo ($color == $value)?"active":"" ?>" for="<?php echo esc_attr($field_name)."-".$key ?>">
                                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".$key ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($value) ?>" <?php checked($color, $value) ?> />
                                                        <span style="background: <?php echo esc_attr($value) ?>"></span>
                                                    </label>
                                                </li>
                                            <?php } $key = 3; ?>
                                            <?php if($setting_color !== false && $setting_color != "#484848") { ?>
                                                <li>
                                                    <label class="color-checkbox <?php echo ($color == $setting_color)?"active":"" ?>" for="<?php echo esc_attr($field_name)."-".$key ?>">
                                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".$key ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($setting_color) ?>" <?php checked($color, $setting_color) ?> />
                                                        <span style="background: <?php echo esc_attr($setting_color) ?>"></span>
                                                    </label>
                                                </li>
                                            <?php } ?>
                                            <li>
                                                <a class="upgrade-box-link d-block" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                                    <span class="color-box"><span class="gradient"></span> <span class="color-box-area">Custom</span></span>
                                                    <span class="upgrade-link"><?php echo esc_html_e("Upgrade to Pro", "folders") ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                                <?php
                                $color = !isset($customize_folders['folder_bg_color'])||empty($customize_folders['folder_bg_color'])?"#FA166B":$customize_folders['folder_bg_color'];
                                $setting_color = WCP_Folders::check_for_setting("folder_bg_color", "customize_folders");
                                ?>
                                <tr>
                                    <td class="no-padding">
                                        <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                            <label for="folder_bg_color" >Folders background color <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", WCP_FOLDER) ?></button></label>
                                        </a>
                                    </td>
                                    <td>
                                        <ul class="color-list">
                                            <?php $field_name = "folder_bg_color"; foreach ($colors as $key=>$value) { ?>
                                                <li>
                                                    <label class="color-checkbox <?php echo ($color == $value)?"active":"" ?>" for="<?php echo esc_attr($field_name)."-".$key ?>">
                                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".$key ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($value) ?>" <?php checked($color, $value) ?> />
                                                        <span style="background: <?php echo esc_attr($value) ?>"></span>
                                                    </label>
                                                </li>
                                            <?php } $key = 3; ?>
                                            <?php if($setting_color !== false && $setting_color != "#FA166B") { ?>
                                                <li>
                                                    <label class="color-checkbox <?php echo ($color == $setting_color)?"active":"" ?>" for="<?php echo esc_attr($field_name)."-".$key ?>">
                                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".$key ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($setting_color) ?>" <?php checked($color, $setting_color) ?> />
                                                        <span style="background: <?php echo esc_attr($setting_color) ?>"></span>
                                                    </label>
                                                </li>
                                            <?php } ?>
                                            <li>
                                                <a class="upgrade-box-link d-block" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                                    <span class="color-box"><span class="gradient"></span> <span class="color-box-area">Custom</span></span>
                                                    <span class="upgrade-link"><?php echo esc_html_e("Upgrade to Pro", "folders") ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                                <?php
                                $font = !isset($customize_folders['folder_font'])||empty($customize_folders['folder_font'])?"":$customize_folders['folder_font'];
                                $setting_font = WCP_Folders::check_for_setting("folder_font", "customize_folders");
                                $index = 0;
                                ?>
                                <tr>
                                    <td class="no-padding">
                                        <label for="folder_font" >
	                                        <?php if($setting_font !== false && $setting_font != "" && !in_array($setting_font, array("Arial","Tahoma","Verdana","Helvetica","Times New Roman","Trebuchet MS","Georgia"))) {
		                                        esc_html_e("Folders font", WCP_FOLDER);
	                                        } else { ?>
                                                <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
			                                        <?php esc_html_e( 'Folders font', WCP_FOLDER ) ?> <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", WCP_FOLDER) ?></button>
                                                </a>
	                                        <?php } ?>
                                        </label>
                                    </td>
                                    <td colspan="2">
                                        <select name="customize_folders[folder_font]" id="folder_font" >
                                            <?php $group = '';
                                            foreach ($fonts as $key => $value):
                                                $title = $key;
                                                if($index == 0) {
                                                    $key = "";
                                                }
                                                $index++;
                                                if ($value != $group) {
                                                    echo '<optgroup label="' . $value . '">';
                                                    $group = $value;
                                                }
	                                            if(($setting_font !== false && $setting_font != "" && !in_array($setting_font, array("Arial","Tahoma","Verdana","Helvetica","Times New Roman","Trebuchet MS","Georgia"))) || $value != "Google Fonts" ) { ?>
                                                    <option value="<?php echo $key; ?>" <?php selected($font, $key); ?>><?php echo $title; ?></option>
                                                <?php } else { ?>
                                                    <option class="pro-select-item" value="folders-pro"><?php echo $title; ?> (Pro) 🔑</option>
                                                <?php } ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                                <?php
                                $size  = ! isset( $customize_folders['folder_size'] ) || empty( $customize_folders['folder_size'] ) ? "16" : $customize_folders['folder_size'];
                                $folder_size = WCP_Folders::check_for_setting("folder_size", "customize_folders");
                                ?>
                                <tr>
                                    <td class="no-padding">
                                        <label for="folder_size" >
                                            <?php if($folder_size === false || intval($folder_size) === 16) { ?>
                                                <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
		                                            <?php esc_html_e( 'Folders size', WCP_FOLDER ) ?> <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", WCP_FOLDER) ?></button>
                                                </a>
                                            <?php } else { ?>
                                                <?php esc_html_e("Folders size", WCP_FOLDER); ?>
                                            <?php } ?>
                                        </label>
                                    </td>
                                    <td colspan="2">
                                        <?php
                                        if($folder_size === false || intval($folder_size) == 16) {
	                                        $sizes = array(
		                                        "folders-pro" => "Small (Pro) 🔑",
		                                        "16" => "Medium",
		                                        "folders-pro-item" => "Large (Pro) 🔑"
	                                        );
	                                        $size = 16;
                                        } else {
	                                        $sizes = array(
		                                        "12" => "Small",
		                                        "16" => "Medium",
		                                        "20" => "Large"
	                                        );
                                        }
                                        ?>
                                        <select name="customize_folders[folder_size]" id="folder_size" >
                                            <?php
                                            foreach ($sizes as $key=>$value) {
                                                $selected = ($key == $size)?"selected":"";
                                                echo "<option ".$selected." value='".$key."'>".$value."</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <?php
                                $show_in_page = isset($customize_folders['show_in_page'])?$customize_folders['show_in_page']:"hide";
                                $show_folder = WCP_Folders::check_for_setting("show_in_page", "customize_folders");
                                if(empty($show_in_page)) {
                                    $show_in_page = "hide";
                                }
                                ?>
                                <tr>
                                    <td colspan="3" style="padding: 15px 20px 15px 0">
                                        <input type="hidden" name="customize_folders[show_in_page]" value="hide">
                                        <?php if($show_folder === false || $show_folder === "hide") { ?>
                                            <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                                <label for="" class="custom-checkbox send-user-to-pro">
                                                    <input disabled type="checkbox" class="sr-only" name="customize_folders[show_in_page]" id="show_in_page" value="on" <?php checked($show_in_page, "show") ?>>
                                                    <span></span>
                                                </label>
                                                <label for="" class="send-user-to-pro">
			                                        <?php esc_html_e("Show Folders in upper position", "folders"); ?>
                                                    <button type="button" class="upgrade-link" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>"><?php esc_html_e("Upgrade to Pro", WCP_FOLDER) ?></button>
                                                </label>
                                            </a>
                                        <?php } else { ?>
                                            <div class="custom-checkbox">
                                                <input id="show_folders" class="sr-only" <?php checked($show_in_page, "show") ?> type="checkbox" name="customize_folders[show_in_page]" value="show">
                                                <span></span>
                                            </div>
                                            <label for="show_folders">Show Folders in upper position</label>
                                        <?php } ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="accordion-right">
                            <div class="preview-text">
                                Preview
                                <div class="preview-text-info">See the full functionality on your media library, posts, pages, and custom posts</div>
                            </div>
                            <div class="preview-inner-box">
                                <div class="preview-box">
                                    <div class="wcp-custom-form">
                                        <div class="form-title">
                                            Folders
                                            <a href="javascript:;" class="add-new-folder" id="add-new-folder">
                                                <span class="create_new_folder"><i class="pfolder-add-folder"></i></span>
                                                <span>New Folder</span>
                                            </a>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="form-options">
                                            <ul>
                                                <li>
                                                    <div class="custom-checkbox">
                                                        <input type="checkbox" class="sr-only" >
                                                        <span></span>
                                                    </div>
                                                </li>
                                                <li>
                                                    <a href="javascript:;" id="inline-update"><span class="icon pfolder-edit-folder"><span class="path2"></span></span> <span class="text">Rename</span> </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:;" id="inline-remove"><span class="icon pfolder-remove"></span> <span class="text">Delete</span> </a>
                                                </li>
                                                <li class="last">
                                                    <a href="javascript:;" id="expand-collapse-list" data-tooltip="Expand"><span class="icon pfolder-arrow-down"></span></a>
                                                </li>
                                                <li class="last">
                                                    <a href="javascript:;" ><span class="icon pfolder-arrow-sort"></span></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="shadow-box">
                                        <div class="header-posts">
                                            <a href="javascript:;" class="all-posts active-item-link">All Files <span class="total-count">215</span></a>
                                        </div>
                                        <div class="un-categorised-items  ui-droppable">
                                            <a href="javascript:;" class="un-categorized-posts">Unassigned Files <span class="total-count total-empty">191</span> </a>
                                        </div>
                                        <div class="separator"></div>
                                        <ul class="folder-list">
                                            <li><a href="javascript:;"><i class="wcp-icon pfolder-folder-close"></i> <span>Folder 1</span><span class="total-count">20</span><span class="clear"></span></a></li>
                                            <li><a href="javascript:;"><i class="wcp-icon pfolder-folder-close"></i> <span>Folder 2</span><span class="total-count">13</span><span class="clear"></span></a></li>
                                            <li><a href="javascript:;"><i class="wcp-icon pfolder-folder-close"></i> <span>Folder 3</span><span class="total-count">5</span><span class="clear"></span></a></li>
                                        </ul>
                                        <div class="separator"></div>
                                        <div class="media-buttons">
                                            <select class="media-select">
                                                <option>All Files</option>
                                                <option>Folder 1</option>
                                                <option>Folder 2</option>
                                                <option>Folder 3</option>
                                            </select>
                                            <button type="button" class="button organize-button">Bulk Organize</button>
                                            <div style="clear: both;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <?php submit_button(); ?>
                    </div>
                </div>
                <div class="tab-content <?php echo esc_attr(($setting_page=="folders-import")?"active":"") ?>" id="folder-import">
                    <?php
                    $remove_folders_when_removed = !isset($customize_folders['remove_folders_when_removed'])?"off":$customize_folders['remove_folders_when_removed'];
                    ?>
                    <input type="hidden" name="customize_folders[remove_folders_when_removed]" value="off" />
                    <div class="folder-danger-zone">
                        <table class="import-export-table">
                            <?php if($is_plugin_exists) { ?>
                            <tr class="has-other-plugins">
                                <td>
                                    <span class="folder-info"><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e("Export/Import", "folders"); ?></span>
                                    <span class="folder-text"><span><?php esc_html_e("External folders found.", "folders"); ?></span> <?php esc_html_e("Click import to start importing external folders.", "folders"); ?></span>
                                </td>
                                <td class="last-td">
                                    <a href="#" class="import-folders-button"><?php esc_html_e("Import", "folders"); ?></a>
                                </td>
                            </tr>
                            <?php } ?>
                            <tr class="no-more-plugins <?php echo (!$is_plugin_exists)?"active":"" ?>">
                                <td>
                                    <span class="folder-info"><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e("Export/Import", "folders"); ?></span>
                                    <span class="folder-text"><?php esc_html_e("Couldn't detect any external folders that can be imported. Please contact us if you have external folders that were not detected", "folders"); ?></span>
                                </td>
                                <td class="last-td">
                                    <a href="https://premio.io/contact/" target="_blank" class="contact-folders-button"><?php esc_html_e("Contact Us", "folders"); ?></a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><span class="danzer-title"><span class="dashicons dashicons-warning"></span> <?php esc_html_e("Danger Zone", "folders"); ?></span></td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="danger-info"><?php esc_html_e("Delete plugin data upon deletion", "folders"); ?></span>
                                    <span class="danger-data"><?php esc_html_e("Delete all folders when the plugin is removed. This feature will remove all existing folders created by the plugin upon deletion.", "folders"); ?> <b><?php esc_html_e("(Not recommended)", "folders"); ?></span></b>
                                </td>
                                <td class="last-td" >
                                    <div class="inline-checkbox">
                                        <label class="folder-switch" for="remove_folders_when_removed">
                                            <input type="checkbox" class="sr-only change-folder-status" name="customize_folders[remove_folders_when_removed]" id="remove_folders_when_removed" value="on" <?php checked($remove_folders_when_removed, "on") ?>>
                                            <div class="folder-slider round"></div>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="danger-info"><?php esc_html_e("Manual Data Removal", "folders"); ?></span>
                                    <span class="danger-data"><?php esc_html_e("Delete all folders data manually This feature will remove all existing folders created by the plugin. Use this feature with caution.", "folders"); ?>
                                </td>
                                <td class="last-td">
                                    <a href="#" class="remove-folders-data"><?php esc_html_e("Delete Now", "folders"); ?></a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="tab-content <?php echo esc_attr(($setting_page=="license-key")?"active":"") ?>">
                    <?php if($setting_page=="license-key") { ?>
                        <?php
                        $type = filter_input(INPUT_GET, 'm', FILTER_SANITIZE_STRING);
                        if(isset($type) && !empty($type)) {
                            switch ($type) {
                                case "error": ?>
                                    <div class='folder-error-message'><?php esc_attr_e("Your license key is not valid",WCP_FOLDER ) ?></div>
                                    <?php break;
                                case "valid": ?>
                                    <div class='folder-success-message'><?php esc_attr_e("Your license key is activated successfully",WCP_FOLDER ) ?></div>
                                    <?php break;
                                case "unactivated": ?>
                                    <div class='folder-success-message'><?php esc_attr_e("Your license key is deactivated successfully",WCP_FOLDER ) ?></div>
                                    <?php break;
                                case "expired": ?>
                                    <div class='folder-error-message'><?php esc_attr_e("Your license has been expired",WCP_FOLDER ) ?></div>
                                    <?php break;
                                case "no_activations": ?>
                                    <div class='folder-error-message'><?php esc_attr_e("Your license was activated for another domain, please visit your ",WCP_FOLDER ) ?><a target="_blank" href="https://go.premio.io"><?php esc_attr_e("Premio account",WCP_FOLDER ) ?></a></div>
                                    <?php break;
                            }
                        }
                        ?>
                        <div class="key-table">
                            <div class="license-key-footer">
                                <?php
                                $licenseKey = get_option("wcp_folder_license_key");
                                $licenseData = array();
                                $active_status = 0;
                                delete_transient("folder_license_key_data");
                                if(!empty($licenseKey)) {
                                    $licenseData = $this->get_license_key_data($licenseKey);
                                    if(!empty($licenseData)) {
                                        if($licenseData['license'] == "valid") {
                                            $active_status = 1;
                                        } else if($licenseData['license'] == "expired") {
                                            $active_status = 2;
                                        }
                                    } else {
                                        $licenseKey = "";
                                    }
                                }
                                if(!$active_status) {
                                    esc_attr_e("To receive updates, please enter your valid license key.", WCP_FOLDER );
                                } else if ($active_status == 1 && $licenseData['expires'] == "lifetime") {
                                    esc_attr_e("You have a lifetime license", WCP_FOLDER);
                                } else if($active_status == 1 ){
                                    esc_attr_e("Your license will expire on ".date("d M, Y",strtotime($licenseData['expires'])));
                                } else if($active_status == 2 ){
                                    $url = WCP_PRO_FOLDER_API_URL.'/checkout/?edd_license_key='.$licenseKey."&download_id=".WCP_PRO_FOLDER_PR0DUCT_ID; ?>
                                    <span class='error-message'><?php esc_attr_e("Your license has been expired on ", WCP_FOLDER).esc_attr(date("d M, Y",strtotime($licenseData['expires']))) ?></span><a target="_blank" href="<?php echo esc_url($url) ?>" class="button button-primary" ><?php esc_attr_e("Renew Now", WCP_FOLDER ) ?></a> <?php
                                }
                                ?>
                            </div>
                            <form action="" id="license_key_form">
                                <div class="input-field">
                                    <label for="license_key"><?php esc_attr_e("License Key", WCP_FOLDER ) ?></label>
                                    <input class="license-key" id="license_key" name="license_key" value="<?php echo esc_attr($licenseKey) ?>">
                                </div>
                                <div class="submit">
                                    <?php if($active_status != 0) { ?>
                                        <a href="javascript:;" class="button secondary-button" id="deactivate_key"><?php esc_attr_e("Deactivate Key", WCP_FOLDER ) ?></a>
                                    <?php } ?>
                                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e("Activate Key", WCP_FOLDER ) ?>">
                                </div>
                            </form>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
        ?>
        <input type="hidden" name="folder_nonce" value="<?php echo wp_create_nonce("folder_settings") ?>">
        <input type="hidden" name="folder_page" value="<?php echo $_SERVER['REQUEST_URI'] ?>">
        <?php if($setting_page!="upgrade-to-pro") { ?>
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
                <button type="submit" class="form-submit-btn" id="import-folder-button"><?php esc_html_e("Import", WCP_FOLDER); ?></button>
                <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", WCP_FOLDER); ?></a>
            </div>
        </div>
    </div>
</div>

<?php //if($plugin['is_exists']) { ?>
<div class="folder-popup-form" id="import-folders-popup">
    <div class="popup-form-content">
        <div class="popup-content">
            <div class="close-popup-button">
                <a class="" href="javascript:;"><span></span></a>
            </div>
            <div class="import-plugin-title"><?php esc_html_e("Import data", WCP_FOLDER); ?></div>
            <div class="plugin-import-table">
                <div class="import-folder-table">
                    <table>
                        <tbody>
						<?php foreach ($plugin_info as $slug=>$plugin) { ?>
							<?php if($plugin['is_exists']) { ?>
                                <tr class="other-plugins-<?php echo esc_attr__($slug) ?>" data-plugin="<?php echo esc_attr__($slug) ?>" data-nonce="<?php echo wp_create_nonce("import_data_from_".$slug) ?>" data-folders="<?php echo esc_attr($plugin['total_folders']) ?>" data-attachments="<?php echo esc_attr($plugin['total_attachments']) ?>">
                                    <th class="plugin-name"><?php echo esc_attr__($plugin['name']) ?></th>
                                    <td>
                                        <span class="import-message"><?php printf(esc_html__("%s folder%s and %s attachment%s", "folders"), "<b>".$plugin['total_folders']."</b>", ($plugin['total_folders']>1)?esc_html__("s"):"" ,"<b>".$plugin['total_attachments']."</b>", ($plugin['total_attachments']>1)?esc_html__("s"):"") ?></span>
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
                <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Close", WCP_FOLDER); ?></a>
            </div>
        </div>
    </div>
</div>
<?php //} ?>

<div class="folder-popup-form" id="remove-plugin-data">
    <div class="popup-form-content">
        <div class="popup-content">
            <div class="close-popup-button">
                <a class="" href="javascript:;"><span></span></a>
            </div>
            <div class="remove-folder-title">Are you sure?</div>
            <div class="remove-folder-note">You're about to delete $plugin's folders. Are you sure you'd like to proceed?</div>
            <div class="folder-form-buttons">
                <button type="submit" class="form-submit-btn delete-folder-plugin" id="remove-folder-button"><?php esc_html_e("Delete plugin data", WCP_FOLDER); ?></button>
                <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", WCP_FOLDER); ?></a>
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
            <div class="remove-folder-title"><?php esc_html_e("Are you sure?", WCP_FOLDER); ?></div>
            <div class="remove-folder-note"><?php echo sprintf(esc_html__("Folders will remove all created folders once you remove the plugin. We recommend you %s if you plan to use Folders in future.", WCP_FOLDER), "<b>".esc_html__("not to use this feature", WCP_FOLDER)."</b>"); ?></div>
            <div class="folder-form-buttons">
                <a href="javascript:;" class="form-cancel-btn cancel-folders"><?php esc_html_e("Cancel", WCP_FOLDER); ?></a>
                <button type="submit" class="form-cancel-btn delete-button"><?php esc_html_e("I want to delete anyway", WCP_FOLDER); ?></button>
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
                    <b><?php esc_html_e("Type DELETE to confirm", WCP_FOLDER); ?></b>
                    <div class="input-box">
                        <input autocomplete="off" type="text" id="delete-input" name="delete" >
                    </div>
                    <div class="delete-confirmation-message"><?php esc_html_e('', WCP_FOLDER); ?></div>
                </div>
                <div class="folder-form-buttons">
                    <input type="hidden" name="nonce" id="remove-folder-nonce" value="<?php echo wp_create_nonce("remove_folders_data") ?>">
                    <input type="hidden" name="action" value="remove_all_folders_data">
                    <button disabled type="submit" class="form-submit-btn delete-button" id="remove-folders-data-button"><?php esc_html_e("Delete", WCP_FOLDER); ?></button>
                    <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", WCP_FOLDER); ?></a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$option = get_option("folder_intro_box");
if(($option == "show" || get_option("folder_redirect_status") == 2) && $is_plugin_exists) { ?>
    <div class="folder-popup-form" id="import-third-party-plugin-data" style="display: block" ?>
        <div class="popup-form-content">
            <div class="popup-content">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="import-plugin-title"><?php esc_html_e("Import data", WCP_FOLDER); ?></div>
                <div class="import-plugin-note"><?php esc_html_e("We've detected that you use another folders plugin. Would you like the Folders plugin to import your current folders? Keep in mind you can always do it in Folders Settings -> Import", WCP_FOLDER) ?></div>
                <div class="plugin-import-table">
                    <div class="import-folder-table">
                        <table>
                            <tbody>
                            <?php foreach ($plugin_info as $slug=>$plugin) {
                                if($plugin['is_exists']) { ?>
                                    <tr class="other-plugins-<?php echo esc_attr__($slug) ?>" data-plugin="<?php echo esc_attr__($slug) ?>" data-nonce="<?php echo wp_create_nonce("import_data_from_".$slug) ?>" data-folders="<?php echo esc_attr($plugin['total_folders']) ?>" data-attachments="<?php echo esc_attr($plugin['total_attachments']) ?>">
                                        <th class="plugin-name"><?php echo esc_attr__($plugin['name']) ?></th>
                                        <td>
                                            <button type="button" class="button button-primary import-folder-data in-popup"><?php esc_html_e("Import", "folders"); ?> <span class="spinner"></span></button>
                                            <span class="import-message"><?php printf(esc_html__("%s folder%s and %s attachment%s", "folders"), "<b>".$plugin['total_folders']."</b>", ($plugin['total_folders']>1)?esc_html__("s"):"" ,"<b>".$plugin['total_attachments']."</b>", ($plugin['total_attachments']>1)?esc_html__("s"):"") ?></span>
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
                    <a href="javascript:;" id="cancel-plugin-import" class="form-cancel-btn"><?php esc_html_e("Close", WCP_FOLDER); ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php
    if($option != "show") {
        update_option("folder_redirect_status", 3);
    }
} ?>
<div class="folder-popup-form" id="no-more-folder-credit">
    <div class="popup-form-content">
        <div class="popup-content">
            <div class="close-popup-button">
                <a class="" href="javascript:;"><span></span></a>
            </div>
            <div class="add-update-folder-title" id="folder-limitation-message">
                You've reached the 10 folder limitation!
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
