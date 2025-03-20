<?php
/**
 * @package wp-content-aware-engine
 * @author Joachim Jensen <joachim@dev.institute>
 * @license GPLv3
 * @copyright 2023 by Joachim Jensen
 */
?>
<script type="text/template" id="wpca-template-group">
    <div class="cas-group-sep" data-vm="classes:{'wpca-group-negate':any(statusExcept,statusNegated)}">
    <span data-vm="text:statusLabel"></span>
</div>
<div class="cas-group-body">
    <div class="cas-group-actions">
        <div class="alignleft">
            <select class="wpca-conditions-add js-wpca-add-and">
                <option></option>
            </select>
        </div>
        <div class="alignright">
            <span class="spinner"></span>
            <button class="js-wpca-save-group button button-small hide-if-js" type="button"><?php _e('Save Changes', WPCA_DOMAIN); ?></button>
            <?php do_action('wpca/group/actions', $post_type); ?>
            <button type="button" class="button button-small js-wpca-options"><span class="dashicons dashicons-admin-generic"></span> <?php _e('Settings', WPCA_DOMAIN) ?></button>
        </div>
    </div>
    <ul class="cas-group-options hide-if-js">
        <?php do_action('wpca/group/settings', $post_type); ?>
    </ul>
    <div class="cas-group-cell">
        <div class="cas-content" data-vm="collection:$collection"></div>
    </div>
</div>
</script>