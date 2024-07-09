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
<div id="folder-add-update-content">
    <div class="folder-popup-form" id="add-update-folder">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <form action="" method="post" id="save-folder-form">
                    <div id="add-update-folder-title" class="add-update-folder-title">
                        <?php esc_html_e("Add a new folder", "folders") ?>
                    </div>
                    <div class="add-folder-note">
                        <?php esc_html_e("Enter your folder's name (or create more than one folder by separating the name with a comma)", "folders") ?>
                    </div>
                    <div class="folder-form-input">
                        <div class="folder-group">
                            <input id="add-update-folder-name" autocomplete="off" required="required">
                            <span class="highlight"></span><span class="folder-bar"></span>
                            <label for="add-update-folder-name"><?php esc_html_e("Folder name", "folders") ?></label>
                        </div>
                    </div>
                    <div class="folder-form-errors">
                        <span class="dashicons dashicons-info"></span> <?php esc_html_e("Please enter folder name", "folders") ?>
                    </div>
                    <div class="folder-form-buttons hide-it pro-message" id="pro-notice">
                            <span class="pro-tip">
                                <?php esc_html_e("Pro tip", "folders") ?>
                            </span>
                        <div class="pro-notice">
                            <?php printf(esc_html__("%1\$sUpgrade to Pro%2\$s to create subfolders (with 20+ amazing features) & premium support 🎉", "folders"), '<a class="inline-button" target="_blank" href="'.esc_url($this->getFoldersUpgradeURL()).'">', "</a>"); ?>
                        </div>
                    </div>
                    <div class="folder-form-buttons">
                        <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", "folders") ?></a>
                        <button type="submit" class="form-submit-btn" id="save-folder-data" style="width: 160px"><?php esc_html_e("Submit", "folders") ?></button>
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
                        <?php esc_html_e("Rename folder", "folders") ?>
                    </div>
                    <div class="folder-form-input">
                        <div class="folder-group">
                            <input id="update-folder-item-name" autocomplete="off" required="required">
                            <span class="highlight"></span><span class="folder-bar"></span>
                            <label for="update-folder-item-name"><?php esc_html_e("Folder name", "folders") ?></label>
                        </div>
                    </div>
                    <div class="folder-form-errors">
                        <span class="dashicons dashicons-info"></span> <?php esc_html_e("Please enter folder name", "folders") ?>
                    </div>
                    <div class="folder-form-buttons">
                        <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", "folders") ?></a>
                        <button type="submit" class="form-submit-btn" id="update-folder-data" style="width: 160px"><?php esc_html_e("Submit", "folders") ?></button>
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
                    <?php esc_html_e("Are you sure you want to delete the selected folder?", "folders") ?>
                </div>
                <div class="folder-form-message" id="remove-folder-notice">
                    <?php esc_html_e("Items in the folder will not be deleted.", "folders") ?>
                </div>
                <div class="folder-form-buttons">
                    <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("No, Keep it", "folders") ?></a>
                    <a href="javascript:;" class="form-submit-btn" id="remove-folder-item"><?php esc_html_e("Yes, Delete it!", "folders") ?></a>
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
                    <?php esc_html_e("Unlock unlimited amount of folders by activating license key.", "folders") ?>
                </div>
                <div class="folder-form-buttons">
                    <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", "folders") ?></a>
                    <a href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" target="_blank" class="form-submit-btn"><?php esc_html_e("Upgrade to Pro", "folders") ?></a>
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
                    <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Close", "folders") ?></a>
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
                    <?php esc_html_e("Sub-folders is a pro feature", "folders") ?>
                </div>
                <div class="folder-form-message" style="padding: 25px 10px;" >
                    <?php esc_html_e("Hey, it looks like you want to create sub-folders on Folders. Sub-folders is a premium feature. Upgrade to Pro to create, access and organize your files with sub-folders.", "folders") ?>
                </div>
                <div class="folder-form-buttons">
                    <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", "folders") ?></a>
                    <a href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" target="_blank" class="form-submit-btn"><?php esc_html_e("Upgrade to Pro", "folders") ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="sub-drag-folder-popup">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="add-update-folder-title">
                    <?php esc_html_e("Sub-folders is a pro feature", "folders") ?>
                </div>
                <div class="folder-form-message" style="padding: 25px 0 15px;" >
                    <?php esc_html_e("Hey, it looks like you want to create sub-folders on Folders. Sub-folders is a premium feature. Upgrade to Pro to create, access and organize your files with sub-folders.", "folders") ?>
                </div>
                <div class="folder-form-message" style="padding: 0 10px 25px;" >
                    <?php esc_html_e("You can still create unlimited folders in the free version.", "folders") ?>
                </div>
                <div class="checkbox-content">
                    <?php $check_status = get_option("premio_hide_child_popup"); ?>
                    <label for="do_not_show_again"><input type="checkbox" id="do_not_show_again" <?php checked($check_status, 1) ?>> <?php esc_html_e("Don't show this popup again", "folders") ?></label>
                </div>
                <div class="folder-form-buttons">
                    <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", "folders") ?></a>
                    <a href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" target="_blank" class="form-submit-btn"><?php esc_html_e("Upgrade to Pro", "folders") ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="folder-popup-form" id="confirm-your-change">
        <div class="popup-form-content">
            <div class="popup-form-data">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="add-update-folder-title">
                    <?php esc_html_e("Confirm your change", "folders") ?>
                </div>
                <div class="folder-form-message" style="padding: 25px 10px;" >
                    <?php esc_html_e("Hey, it looks like you want to move the file to \"Unassigned Files.\" Do you want to move the file from the current folder only or from all the folders where the file exists?", "folders") ?>
                </div>
                <div class="folder-form-buttons">
                    <input type="hidden" id="unassigned_folders" />
                    <a href="javascript:;" class="form-cancel-btn remove-from-all-folders" id="remove-from-all-folders"><?php esc_html_e("From all folders", "folders") ?></a>
                    <a href="javascript:;" class="form-submit-btn remove-from-current-folder" id="remove-from-current-folder"><?php esc_html_e("Just from this folder", "folders") ?></a>
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
                    <?php esc_html_e("Add a new folder", "folders") ?>
                </div>
                <div class="folder-form-input">
                    <div class="folder-group">
                        <input id="update-folder-item-name" autocomplete="off" required="required" readonly>
                        <span class="highlight"></span><span class="folder-bar"></span>
                        <label for="update-folder-item-name"><?php esc_html_e("Folder name", "folders") ?></label>
                    </div>
                </div>
                <div class="folder-form-buttons">
                    <span class="pro-tip">
                        <?php esc_html_e("Pro tip", "folders") ?>
                    </span>
                    <div class="pro-notice">
                        <?php printf(esc_html__("%1\$sUpgrade to Pro%2\$s to create subfolders (with 20+ amazing features) & premium support 🎉", "folders"), '<a class="inline-button" target="_blank" href="'.esc_url($this->getFoldersUpgradeURL()).'">', "</a>"); ?>
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
                        <?php esc_html_e("Select Folder", "folders") ?>
                    </div>
                    <div class="select-box">
                        <select id="bulk-select">
                            <option value=""><?php esc_html_e("Select Folder", "folders") ?></option>
                        </select>
                    </div>
                    <div class="folder-form-buttons">
                        <a href="javascript:;" class="form-cancel-btn"><?php esc_html_e("Cancel", "folders") ?></a>
                        <button type="submit" class="form-submit-btn" id="move-to-folder" style="width: 200px"><?php esc_html_e("Move to Folder", "folders") ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="folders-undo-notification" id="do-undo">
        <div class="folders-undo-body">
            <a href="javascript:;" class="close-undo-box"><span></span></a>
            <div class="folders-undo-header"><?php esc_html_e("Action performed successfully", "folders") ?></div>
            <div class="folders-undo-body"><?php printf(esc_html__("Your action has been successfully completed. Click the %1\$sUndo%2\$s button to reverse the action", "folders"), "<b>", "</b>"); ?></div>
            <div class="folders-undo-footer"><button class="undo-button" type="button"><?php esc_html_e("Undo", "folders") ?></button></div>
        </div>
    </div>

    <div class="folders-undo-notification" id="undo-done">
        <div class="folders-undo-body" style="padding: 0">
            <a href="javascript:;" class="close-undo-box"><span></span></a>
            <div class="folders-undo-header" style="color: #014737; padding: 0"><?php esc_html_e("Action reversed successfully", "folders") ?></div>
        </div>
    </div>

    <?php
    $upgrade_popup_status = get_option("show_folder_upgrade_popup");
    if($upgrade_popup_status != "hide") { ?>
        <div class="folder-popup-form" id="upgrade-modal-popup">
            <div class="popup-form-content upgrade-modal">
                <div class="popup-content" style="position: relative;">
                    <div class="close-popup-button">
                        <a class="upgrade-model-button" href="javascript:;"><span></span></a>
                    </div>
                    <img style="width: auto; margin: 0 auto" src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/upgrade-image.png") ?>">
                    <div class="upgrade-title"><?php esc_html_e("Great job! 🎉", "folders"); ?></div>
                    <div class="upgrade-desc"><?php esc_html_e("You've successfully organized your files into folders for the first time.", "folders"); ?></div>
                    <div class="upgrade-content">
                        <div class="upgrade-content-title"><?php esc_html_e("Upgrade to Pro today", "folders"); ?></div>
                        <ul>
                            <li><?php esc_html_e("⚙️ Create Unlimited folders and subfolders", "folders"); ?></li>
                            <li><?php esc_html_e("🗂️ Automatically filter posts, pages, media files based on author, date, type and more", "folders"); ?></li>
                            <li><?php esc_html_e("🛠 Upload folders directly and download them as ZIP", "folders"); ?></li>
                            <li><?php esc_html_e("🔒 Restrict users within their own folders", "folders"); ?></li>
                        </ul>
                    </div>
                    <div class="upgrade-footer">
                        <div>
                            <a class="upgrade-button" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" target="_blank"><?php esc_html_e("Upgrade to Pro", "folders"); ?></a>
                        </div>
                        <a class="hide-upgrade-popup" href="#"><?php esc_html_e("Close", "folders"); ?></a>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php
    $page_views = intval(get_option("get_folders_page_views"));
    if($page_views >= 2) {
        ?>
        <div class="folder-popup-form" id="rating-modal-popup" >
            <div class="popup-form-content upgrade-modal rating-modal">
                <div class="popup-content" style="position: relative;">
                    <div class="close-popup-button">
                        <a class="hide-upgrade-modal" href="javascript:;" ><span></span></a>
                    </div>
                    <img class="rating-logo" style="width: auto; margin: 0 auto" src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/folder-icon.png") ?>">
                    <div class="rating-modal-steps active" id="step-1">
                        <div class="upgrade-title"><?php esc_html_e("Seems like Folders is bringing you value 🥳", "folders"); ?></div>
                        <div class="upgrade-desc"><?php esc_html_e("Can you please show us some love and rate Folders? It'll really help us spread the word", "folders"); ?></div>
                        <div class="upgrade-rating">
                            <div id="folder-rating"></div>
                        </div>
                        <div class="upgrade-user-rating"><span>0/5</span> <?php esc_html_e("rating", "folders"); ?></div>
                    </div>
                    <div class="rating-modal-steps" id="step-2">
                        <div class="upgrade-title"><?php esc_html_e("Share Your Experience", "folders"); ?></div>
                        <div class="upgrade-rating">
                            <div id="folder-rated-rating" class="folder-rated-rating"></div>
                        </div>
                        <div class="upgrade-user-rating"><span>0/5</span> <?php esc_html_e("rating", "folders"); ?></div>
                        <div class="upgrade-review-textarea">
                            <label for="upgrade-review-comment"><?php esc_html_e("Review (optional)", "folders"); ?><span>1000</span></label>
                            <textarea id="upgrade-review-comment" placeholder="<?php esc_html_e("Please write your review here", "folders"); ?>"></textarea>
                        </div>
                        <div class="upgrade-modal-button">
                            <button type="button" id="upgrade-review-button" class="upgrade-review-button"><?php esc_html_e("Submit", "folders"); ?></button>
                        </div>
                    </div>
                    <div class="rating-modal-steps" id="step-3">
                        <div class="upgrade-title"><?php esc_html_e("Would you like to be reminded in the future?", "folders"); ?></div>
                        <div class="upgrade-review-textarea">
                            <label for="upgrade-review-reminder"><?php esc_html_e("Remind me after", "folders"); ?></label>
                            <select id="upgrade-review-reminder" class="upgrade-review-reminder">
                                <option value="7"><?php esc_html_e("7 Days", "folders"); ?></option>
                                <option value="14"><?php esc_html_e("14 Days", "folders"); ?></option>
                                <option value="-1"><?php esc_html_e("Don't remind me", "folders"); ?></option>
                            </select>
                        </div>
                        <div class="upgrade-modal-button">
                            <button type="button" id="update-review-time" class="upgrade-review-button"><?php esc_html_e("Submit", "folders"); ?></button>
                        </div>
                    </div>
                    <div class="rating-modal-steps" id="step-4">
                        <div class="upgrade-title">
                            <?php esc_html_e("Five Stars!", "folders"); ?>
                            <div class="folder-rated-rating">
                                <div class="jq-star"><svg shape-rendering="geometricPrecision" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="305px" height="305px" viewBox="60 -62 309 309" style="enable-background:new 64 -59 305 305; stroke-width:0px;" xml:space="preserve"> <polygon data-side="center" className="svg-empty-28" points="281.1,129.8 364,55.7 255.5,46.8 214,-59 172.5,46.8 64,55.4 146.8,129.7 121.1,241 212.9,181.1 213.9,181 306.5,241 " style="fill: transparent; stroke: #ffa83e;"></polygon> <polygon data-side="left" className="svg-empty-28" points="281.1,129.8 364,55.7 255.5,46.8 214,-59 172.5,46.8 64,55.4 146.8,129.7 121.1,241 213.9,181.1 213.9,181 306.5,241 " style="stroke-opacity: 0;"></polygon> <polygon data-side="right" className="svg-empty-28" points="364,55.7 255.5,46.8 214,-59 213.9,181 306.5,241 281.1,129.8 " style="stroke-opacity: 0;"></polygon> </svg></div>
                                <div class="jq-star"><svg shape-rendering="geometricPrecision" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="305px" height="305px" viewBox="60 -62 309 309" style="enable-background:new 64 -59 305 305; stroke-width:0px;" xml:space="preserve"> <polygon data-side="center" className="svg-empty-28" points="281.1,129.8 364,55.7 255.5,46.8 214,-59 172.5,46.8 64,55.4 146.8,129.7 121.1,241 212.9,181.1 213.9,181 306.5,241 " style="fill: transparent; stroke: #ffa83e;"></polygon> <polygon data-side="left" className="svg-empty-28" points="281.1,129.8 364,55.7 255.5,46.8 214,-59 172.5,46.8 64,55.4 146.8,129.7 121.1,241 213.9,181.1 213.9,181 306.5,241 " style="stroke-opacity: 0;"></polygon> <polygon data-side="right" className="svg-empty-28" points="364,55.7 255.5,46.8 214,-59 213.9,181 306.5,241 281.1,129.8 " style="stroke-opacity: 0;"></polygon> </svg></div>
                                <div class="jq-star"><svg shape-rendering="geometricPrecision" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="305px" height="305px" viewBox="60 -62 309 309" style="enable-background:new 64 -59 305 305; stroke-width:0px;" xml:space="preserve"> <polygon data-side="center" className="svg-empty-28" points="281.1,129.8 364,55.7 255.5,46.8 214,-59 172.5,46.8 64,55.4 146.8,129.7 121.1,241 212.9,181.1 213.9,181 306.5,241 " style="fill: transparent; stroke: #ffa83e;"></polygon> <polygon data-side="left" className="svg-empty-28" points="281.1,129.8 364,55.7 255.5,46.8 214,-59 172.5,46.8 64,55.4 146.8,129.7 121.1,241 213.9,181.1 213.9,181 306.5,241 " style="stroke-opacity: 0;"></polygon> <polygon data-side="right" className="svg-empty-28" points="364,55.7 255.5,46.8 214,-59 213.9,181 306.5,241 281.1,129.8 " style="stroke-opacity: 0;"></polygon> </svg></div>
                                <div class="jq-star"><svg shape-rendering="geometricPrecision" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="305px" height="305px" viewBox="60 -62 309 309" style="enable-background:new 64 -59 305 305; stroke-width:0px;" xml:space="preserve"> <polygon data-side="center" className="svg-empty-28" points="281.1,129.8 364,55.7 255.5,46.8 214,-59 172.5,46.8 64,55.4 146.8,129.7 121.1,241 212.9,181.1 213.9,181 306.5,241 " style="fill: transparent; stroke: #ffa83e;"></polygon> <polygon data-side="left" className="svg-empty-28" points="281.1,129.8 364,55.7 255.5,46.8 214,-59 172.5,46.8 64,55.4 146.8,129.7 121.1,241 213.9,181.1 213.9,181 306.5,241 " style="stroke-opacity: 0;"></polygon> <polygon data-side="right" className="svg-empty-28" points="364,55.7 255.5,46.8 214,-59 213.9,181 306.5,241 281.1,129.8 " style="stroke-opacity: 0;"></polygon> </svg></div>
                                <div class="jq-star"><svg shape-rendering="geometricPrecision" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="305px" height="305px" viewBox="60 -62 309 309" style="enable-background:new 64 -59 305 305; stroke-width:0px;" xml:space="preserve"> <polygon data-side="center" className="svg-empty-28" points="281.1,129.8 364,55.7 255.5,46.8 214,-59 172.5,46.8 64,55.4 146.8,129.7 121.1,241 212.9,181.1 213.9,181 306.5,241 " style="fill: transparent; stroke: #ffa83e;"></polygon> <polygon data-side="left" className="svg-empty-28" points="281.1,129.8 364,55.7 255.5,46.8 214,-59 172.5,46.8 64,55.4 146.8,129.7 121.1,241 213.9,181.1 213.9,181 306.5,241 " style="stroke-opacity: 0;"></polygon> <polygon data-side="right" className="svg-empty-28" points="364,55.7 255.5,46.8 214,-59 213.9,181 306.5,241 281.1,129.8 " style="stroke-opacity: 0;"></polygon> </svg></div>
                            </div>
                        </div>
                        <div class="upgrade-desc"><?php esc_html_e("Feel free to connect for questions and suggestions. Thank you for choosing us!", "folders"); ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="folder-popup-form" id="keyboard-shortcut">
        <div class="popup-form-content">
            <div class="popup-content" style="position: relative;">
                <div class="close-popup-button">
                    <a class="" href="javascript:;"><span></span></a>
                </div>
                <div class="import-plugin-title" style="font-weight: bold; padding: 0 0 20px 0; font-size: 16px;"><?php esc_html_e("Keyboard shortcuts (Ctrl+K)", 'folders'); ?></div>
                <div class="plugin-import-table">
                    <table class="keyboard-shortcut">
                        <tr>
                            <th><?php esc_html_e("Create New Folder", "folders") ?></th>
                            <td><span class="key-button">Alt</span><span class="plus-button">+</span><span class="key-button">N</span> </td>
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
</div>
