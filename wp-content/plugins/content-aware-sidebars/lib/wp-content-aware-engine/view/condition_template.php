<?php
/**
 * @package wp-content-aware-engine
 * @author Joachim Jensen <joachim@dev.institute>
 * @license GPLv3
 * @copyright 2023 by Joachim Jensen
 */
?>
<script type="text/template" id="wpca-template-condition">
    <div class="cas-group-sep"><span><?php _e('And', WPCA_DOMAIN); ?></span></div>
    <div class="cas-group-icon" data-vm="html:getIcon"></div>
    <div class='cas-group-label'>
        <span class='js-wpca-condition-remove wpca-condition-remove dashicons dashicons-trash'></span>
        <span data-vm="html:label"></span>
    </div>
    <div class="cas-group-input">
        <select class="js-wpca-suggest" multiple="multiple"></select>
    </div>
</script>