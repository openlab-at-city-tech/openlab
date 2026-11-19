<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
$shortcodes = \Folders\Folders\KeyboardShortcut::get_shortcodes();
?>
<div>
    <button type="button" class="folder-sidebar-button keyboard-shortcut gap-1" id="keyboard-shortcut">
        <span class="pfolder-keyboard"></span>
    </button>
</div>