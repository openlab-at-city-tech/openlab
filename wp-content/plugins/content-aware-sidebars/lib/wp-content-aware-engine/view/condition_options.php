<?php
/**
 * @package wp-content-aware-engine
 * @author Joachim Jensen <joachim@dev.institute>
 * @license GPLv3
 * @copyright 2023 by Joachim Jensen
 */
?>
<li>
    <label><?php _e('Page Types', WPCA_DOMAIN); ?></label>
    <div class="wpca-pull-right">
        <select data-vm="value:exposure">
            <option value="0"><?php _e('Single Pages', WPCA_DOMAIN); ?>
            </option>
            <option value="2"><?php _e('Archive Pages', WPCA_DOMAIN); ?>
            </option>
            <option value="1"><?php _e('All', WPCA_DOMAIN); ?>
            </option>
        </select>
    </div>
</li>
<li>
    <label class="cae-toggle">
        <input data-vm="checked:statusExcept" type="checkbox" />
        <div class="cae-toggle-bar wpca-pull-right"></div><?php _e('Exception', WPCA_DOMAIN); ?>
    </label>
</li>
<li>
    <label class="cae-toggle">
        <input data-vm="checked:binary(_ca_autoselect)" type="checkbox" />
        <div class="cae-toggle-bar wpca-pull-right"></div><?php _e('Auto-select new children of selected items', WPCA_DOMAIN); ?>
    </label>
</li>