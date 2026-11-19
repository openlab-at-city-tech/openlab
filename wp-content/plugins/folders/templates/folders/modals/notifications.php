<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>

<div class="folders-undo-notification folders-wrap" id="do-undo">
    <div class="folders-undo-body">
        <button type="button" class="close-notification">
            <svg class="close-icon" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 1L1 13M1 1L13 13" stroke="#181D27" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="sr-only"><?php esc_html_e('Hide modal', 'folders'); ?></span>
        </button>
        <div class="folders-undo-header"><?php esc_html_e("Action performed successfully", 'folders') ?></div>
        <div class="folders-undo-body"><?php printf(esc_html__("Your action has been successfully completed. Click the %sUndo%s button to reverse the action", 'folders'), "<b>", "</b>"); ?></div>
        <div class="folders-undo-footer">
            <button class="undo-button" type="button"><?php esc_html_e("Undo", 'folders') ?></button>
        </div>
    </div>
</div>

<div class="folders-undo-notification folders-wrap" id="undo-done">
    <div class="folders-undo-body success">
        <button type="button" class="close-notification">
            <svg class="close-icon" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 1L1 13M1 1L13 13" stroke="#181D27" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="sr-only"><?php esc_html_e('Hide modal', 'folders'); ?></span>
        </button>
        <div class="folders-undo-header" style="color: #014737; padding: 0"><?php esc_html_e("Action reversed successfully", 'folders') ?></div>
    </div>
</div>

<div class="folders-undo-notification folders-wrap" id="copy-message">
    <div class="folders-undo-body">
        <button type="button" class="close-notification">
            <svg class="close-icon" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 1L1 13M1 1L13 13" stroke="#181D27" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="sr-only"><?php esc_html_e('Hide modal', 'folders'); ?></span>
        </button>
        <div class="folders-undo-header"><?php esc_html_e("Selection successfully copied", 'folders') ?></div>
        <div class="folders-undo-body"><?php esc_html_e("Folder successfully copied in your clipboard. Navigate to your desired location and click 'Paste' button to paste the folder", 'folders'); ?></div>
    </div>
</div>

<div class="folders-undo-notification folders-wrap" id="cut-message">
    <div class="folders-undo-body">
        <button type="button" class="close-notification">
            <svg class="close-icon" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 1L1 13M1 1L13 13" stroke="#181D27" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="sr-only"><?php esc_html_e('Hide modal', 'folders'); ?></span>
        </button>
        <div class="folders-undo-header"><?php esc_html_e("Selection successfully selected for moving", 'folders') ?></div>
        <div class="folders-undo-body"><?php esc_html_e('Navigate to your desired location and click "Paste" button to move the folder', 'folders'); ?></div>
    </div>
</div>

<div class="folders-undo-notification folders-wrap success" id="paste-message">
    <div class="folders-undo-body">
        <button type="button" class="close-notification">
            <svg class="close-icon" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 1L1 13M1 1L13 13" stroke="#181D27" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="sr-only"><?php esc_html_e('Hide modal', 'folders'); ?></span>
        </button>
        <div class="folders-undo-header"><?php esc_html_e("Selection successfully selected for moving", 'folders') ?></div>
        <div class="folders-undo-body"><?php esc_html_e("Clipboard content has been pasted successfully.", 'folders'); ?></div>
        <div class="folders-undo-footer">
            <button class="undo-button copy-paste-action" type="button"><?php esc_html_e("Undo", 'folders') ?></button>
        </div>
    </div>
</div>

<div class="folders-undo-notification folders-wrap success" id="folder-upload-message">
    <div class="folders-undo-body">
        <button type="button" class="close-notification">
            <svg class="close-icon" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 1L1 13M1 1L13 13" stroke="#181D27" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="sr-only"><?php esc_html_e('Hide modal', 'folders'); ?></span>
        </button>
        <div class="folders-undo-header"><?php esc_html_e("Folder successfully uploaded", 'folders') ?></div>
        <div class="folders-undo-body"><?php esc_html_e("Your folder has successfully been uploaded.", 'folders'); ?></div>
    </div>
</div>