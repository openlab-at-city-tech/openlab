<?php
$customize_folders = get_option("customize_folders");
if (isset($customize_folders['show_folder_in_settings']) && $customize_folders['show_folder_in_settings'] == "yes") {
$redirectURL = admin_url("options-general.php?page=wcp_folders_settings&setting_page=folders-import");
} else {
$redirectURL = admin_url("admin.php?page=wcp_folders_settings&setting_page=folders-import");
}
?>
<script>
    (function (factory) {
        "use strict";
        if(typeof define === 'function' && define.amd) {
            define(['jquery'], factory);
        }
        else if(typeof module !== 'undefined' && module.exports) {
            module.exports = factory(require('jquery'));
        }
        else {
            factory(jQuery);
        }
    }(function ($, undefined) {
        function showToast(message, classname) {
            // Create the toast element
            var $toast = $('<div class="toast '+classname+'">' + message + '</div>');

            // Append the toast to the container
            $('#toast-container').append($toast);
            $('#toast-container').attr("class", "");
            $('#toast-container').addClass(classname);

            // Show the toast with a fade-in effect
            $toast.fadeIn();

            // Hide the toast after 3 seconds
            setTimeout(function () {
                $('#toast-container .toast:first-child').fadeOut(500, function(){
                    $(this).remove()
                });
            }, 3500);
        }
        var hasSubFolders = 0;
        $(document).ready(function() {
            $(document).on("click", "#import-folder-pop-up .popup-form-content, #import-folder-pop-up", function () {
                console.log("wawas");
                $("#import_file").val('');
            });

            $(document).on("click", ".export-folderswithin-button:not(.disabled)", function () {
                $(this).addClass("disabled");
                $(".import-export-loader").show();
                $.ajax({
                    url: "<?php echo esc_url(admin_url("admin-ajax.php")) ?>",
                    data: {
                        'action': 'folders_export',
                        'nonce': "<?php echo esc_attr(wp_create_nonce("wcp_folders_export")) ?>"
                    },
                    type: 'post',
                    success: function (res) {
                        res = JSON.parse(res);
                        console.log(res);
                        var downloadLink = document.createElement("a");
                        var fileData = ['\ufeff' + JSON.stringify(res.data)];

                        var blobObject = new Blob(fileData, {
                            type: "text/json;charset=utf-8;"
                        });

                        var url = URL.createObjectURL(blobObject);
                        downloadLink.href = url;
                        var da = slugify(window.location.origin);
                        downloadLink.download = "folders-" + da + ".json";

                        document.body.appendChild(downloadLink);
                        downloadLink.click();
                        document.body.removeChild(downloadLink);
                        $(".export-folderswithin-button").removeClass("disabled");
                        $(".import-export-loader").hide();
                        showToast("<?php esc_html_e("Your folder structure has been successfully exported", "folders"); ?>" ,"success-msg");
                    }
                });
            });

            function slugify(str) {
                return str
                    .toLowerCase()
                    .replace(/[^a-z0-9]/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
            $(document).on("change", "#import_file", function () {
                fileName = $(this).val();
                if(fileName == "") {
                    return;
                }
                fileExt = fileName.split('.').pop().toLowerCase();
                if(fileExt != "json") {
                    showToast("<?php esc_html_e("Invalid file type, please upload json file", "folders"); ?>" ,"error-msg");
                    $("#import_file").val('');
                } else {
                    var myFile = document.getElementById("import_file");
                    var fileReader = new FileReader();
                    fileReader.onload = function (e) {
                        $("#folder-import-table tbody").html("");
                        $("#got_file_content").html("");
                        var data = e.target.result;
                        $("#got_file_content").html(data);
                        data = JSON.parse(data);
                        try {
                            var totalFolders = hasSubFolders = 0;
                            if(data.length) {
                                for (i = 0; i < data.length; i++) {
                                    var totalSubFoldes = 0;
                                    for (j = 0; j < data[i].folders.length; j++) {
                                        if(parseInt(data[i].folders[j].children.length) > 0) {
                                            totalSubFoldes += parseInt(data[i].folders[j].children.length);
                                            hasSubFolders = true;
                                        }
                                    }
                                    var totalFolder = parseInt(data[i]['folders'].length) + totalSubFoldes;
                                    $("#folder-import-table tbody").append(
                                            `<tr>
                                    <td>${data[i].post_title}</td>
                                    <td>${data[i]['folders'].length}</td>
                                    <td>${totalSubFoldes}</td>
                                    <td>${totalFolder}</td>
                                    </tr>`
                                );
                                    totalFolders++;
                                }
                                if(totalFolders) {
                                    $("#import-folder-pop-up").show();
                                } else {
                                    $("#import_file").val('');
                                    showToast("<?php esc_html_e("No data found.", "folders"); ?>" ,"error-msg");
                                }
                            }
                        }
                        catch (e) {
                            $("#import_file").val('');
                            showToast("<?php esc_html_e("Error during uploading file.", "folders"); ?>" ,"error-msg");
                        }
                    };
                    fileReader.readAsText(myFile.files[0]);
                }
            });
            $(document).on("click", ".import-json-file", function (e) {
                e.preventDefault();
                if($(this).hasClass("check-for-sub") && hasSubFolders) {
                    $("#import-folder-pop-up").hide();
                    $("#confirm-for-sub-folder-data").show();
                    return;
                }
                $(".import-export-loader").show();
                $.ajax({
                    url: "<?php echo esc_url(admin_url("admin-ajax.php")) ?>",
                    data: {
                        'action': 'folders_import',
                        'uploaded_data': $("#got_file_content").html(),
                        'nonce': "<?php echo esc_attr(wp_create_nonce("wcp_folders_import")) ?>"
                    },
                    type: 'post',
                    success: function (res) {
                        res = JSON.parse(res);
                        $(".import-file-input").hide();
                        $("#got_file_content").html('');
                        $("#import_file").val('');
                        $(".folders-toast-message .toast-message").text(res.message);
                        $(".import-export-loader").hide();
                        if (res.status == 1) {
                            showToast("<?php esc_html_e("Your folder structure has been imported successfully", "folders"); ?>" ,"success-msg");
                        } else {
                            showToast("<?php esc_html_e("Error during uploading file", "folders"); ?>" ,"error-msg");
                        }
                    }
                });
            });
        });
    }));

</script>
<div class="tab-content <?php echo esc_attr(($setting_page == "folders-import") ? "active" : "") ?>" id="folder-import">
    <?php
    $remove_folders_when_removed = !isset($customize_folders['remove_folders_when_removed']) ? "off" : $customize_folders['remove_folders_when_removed'];
    ?>
    <input type="hidden" name="customize_folders[remove_folders_when_removed]" value="off" />
    <div class="folder-danger-zone">
        <table class="import-export-table">
            <?php if ($is_plugin_exists) { ?>
                <tr class="has-other-plugins">
                    <td>
                        <span class="folder-info"><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e("Import", "folders"); ?></span>
                        <span class="folder-text"><span><?php esc_html_e("External folders found.", "folders"); ?></span> <?php esc_html_e("Click import to start importing external folders.", "folders"); ?></span>
                    </td>
                    <td class="last-td">
                        <a href="#" class="import-folders-button"><?php esc_html_e("Import", "folders"); ?></a>
                    </td>
                </tr>
            <?php } ?>
            <tr class="no-more-plugins <?php echo (!$is_plugin_exists) ? "active" : "" ?>">
                <td>
                    <span class="folder-info"><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e("Import from other Plugins", "folders"); ?></span>
                    <span class="folder-text"><?php esc_html_e("Couldn't detect any external folders that can be imported. Please contact us if you have external folders that were not detected", "folders"); ?></span>
                </td>
                <td class="last-td">
                    <a href="https://premio.io/contact/" target="_blank" class="contact-folders-button"><?php esc_html_e("Contact Us", "folders"); ?></a>
                </td>
            </tr>
            <tr class="">
                <td colspan="2">
                    <div class="import-export-data">
                        <div class="import-export-left">
                            <div class="folder-info"> <?php esc_html_e("Folders Import / Export", "folders"); ?></div>
                            <div class="folder-text">
                                <?php esc_html_e("Export or import folder structure within Folders plugin", "folders"); ?>
                                <span class="folder-tooltip" data-title="<?php esc_html_e("If you previously had Folders plugin, by clicking the import button, upload the folders structure file (JSON format) you exported by clicking the export button. This structure is the skeletal structure of how your folders are arranged in the plugin. Actual files/posts/pages/custom posts aren't imported/exported.", "folders") ?>">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                            </div>
                        </div>

                        <div class="last-td-export-import">
                            <a href="#" class="export-folderswithin-button button button-primary" style="margin-right: 8px"><?php esc_html_e("Export", "folders"); ?></a>
                            <label for="import_file" class="import-folderswithin-button button button-secondary" >
                                <input type="file" class="sr-only" name="importing_file" id="import_file" accept="application/json" />
                                <?php esc_html_e("Import", "folders"); ?>
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr id="got_file_content" style="display: none;">

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
<div class="folder-popup-form import-folder-pop-up" id="import-folder-pop-up">
    <div class="popup-form-content">
        <div class="popup-content">
            <div class="close-popup-button close-remove-folders">
                <a class="" href="javascript:;"><span></span></a>
            </div>
            <div class="folder-title"><?php esc_html_e("Import Folder Structure", 'folders'); ?></div>
            <div class="folder-note"><?php esc_html_e("The following folders will be imported to your plugin", "folders"); ?></div>
            <div class="folder-body">
                <div class="folder-table">
                    <table id="folder-import-table" class="folder-import-table">
                        <thead>
                        <tr>
                            <th><?php esc_html_e("Category", "folders"); ?></th>
                            <th><?php esc_html_e("No. of Folders", "folders"); ?></th>
                            <th><?php esc_html_e("No. of Subfolders", "folders"); ?></th>
                            <th><?php esc_html_e("Total", "folders"); ?></th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="folder-desc-note">
                <?php esc_html_e('Clicking “Import” will create the imported folders to your Folders plugin and will enable the plugin for all the categories above', "folders"); ?>
            </div>
            <div class="folder-import-buttons">
                <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", 'folders'); ?></a>
                <button type="button" class="form-cancel-btn import-json-file check-for-sub"><?php esc_html_e("Import", 'folders'); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="folder-popup-form" id="confirm-for-sub-folder-data">
    <div class="popup-form-content">
        <div class="popup-content">
            <div class="close-popup-button close-remove-folders">
                <a class="" href="javascript:;"><span></span></a>
            </div>
            <div class="folder-title"><?php esc_html_e("Subfolders will be converted into main folders", 'folders'); ?></div>
            <div class="folder-note"><?php printf(esc_html__("As subfolders are only available in the %1\$s of Folders, all of the subfolders will be converted into the main folders.", "folders"), "<a target='_blank' href='".esc_url($this->getFoldersUpgradeURL())."'>".esc_html__("Pro version", "folders")."</a>"); ?></div>
            <div class="folder-import-buttons">
                <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", 'folders'); ?></a>
                <button type="button" class="form-cancel-btn import-json-file"><?php esc_html_e("Continue", 'folders'); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="import-export-loader">
    <div class="cv-spinner">
        <span class="import-spinner"></span>
    </div>
</div>
<div id="toast-container"></div>