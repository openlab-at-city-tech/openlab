<?php
class Fldr_Form_Fields {
    public function __construct() {
        add_action( 'folders_field_prefix_settings', array( $this, 'field_prefix_settings' ), 10, 2 );
        add_action( 'folders_field_label', array( $this, 'field_label' ), 10, 1 );
        add_action( 'folders_field_input', array( $this, 'field_input' ), 10, 4 );
        add_action( 'folders_field_label_postfix', array( $this, 'field_label_postfix' ), 10, 1 );
        add_action( 'folders_field_tooltip', array( $this, 'field_tooltip' ), 10, 1 );
    }

    public function field_prefix_settings($field, $value = 'no') {

    }

    public function field_label($field) {
        if($field['type'] == 'input') { ?>
            <div class="form-label">
                <label class="folder-label" for="<?php echo esc_attr($field['id']) ?>"><?php esc_attr($field['label']) ?></label>
            </div>
        <?php }
    }

    public function field_input($field, $value = '', $is_valid = false, $upgrade_url = '') {
        $disabled = (!$is_valid && $field['is_pro'])?'disabled':'';
        $value = (!$is_valid && $field['is_pro'])?'':$value;
        if($field['type'] == 'input') { ?>
            <div class="form-input">
                <input id="<?php echo esc_attr($field['id']) ?>" type="text" name="<?php echo esc_attr($field['name']) ?>" value="<?php echo esc_attr($value) ?>" />
            </div>
        <?php } elseif($field['type'] == 'timeout') { ?>
            <div class="form-input">
                <label class="folder-label" for="<?php echo esc_attr($field['id']) ?>"><?php echo esc_attr($field['label']) ?></label>
                <div class="seconds-box">
                    <input id="<?php echo esc_attr($field['id']) ?>" type="number" name="<?php echo esc_attr($field['name']) ?>" value="<?php echo esc_attr($value) ?>" />
                </div>
            </div>
        <?php }  elseif($field['type'] == 'upload_size') { ?>
            <div class="form-input">
                <label class="folder-label" for="<?php echo esc_attr($field['id']) ?>"><?php echo esc_attr($field['label']) ?></label>
                <div class="mb-box">
                    <input id="<?php echo esc_attr($field['id']) ?>" type="number" name="<?php echo esc_attr($field['name']) ?>" value="<?php echo esc_attr($value) ?>" />
                </div>
            </div>
        <?php } elseif($field['type'] == 'checkbox') { ?>
            <div class="form-label <?php echo esc_attr($field['label_class'])?> ">
                <input class="sr-only" <?php checked($value, $field['value']) ?> type="hidden" name="<?php echo esc_attr($field['name']) ?>" value="off">
                <?php if($field['is_pro'] && !$is_valid) { ?>
                    <a class="inline-flex upgrade-box-link" href="<?php echo esc_url($upgrade_url) ?>" target="_blank"  >
                        <label class="switch-label" for="">
                <?php } else {?>
                    <label class="switch-label" for="<?php echo esc_attr($field['id']) ?>">
                <?php } ?>
                    <input <?php echo esc_attr($disabled) ?> type="checkbox" id="<?php echo esc_attr($field['id']) ?>" class="sr-only" <?php checked($value, $field['value']) ?> name="<?php echo esc_attr($field['name']) ?>" value="<?php echo esc_attr($field['value']) ?>">
                    <span class="form-switch"></span>
                    <?php echo esc_attr($field['label']) ?>
                    <?php do_action('folders_field_tooltip', $field); ?>
                    <?php do_action('folders_field_label_prefix', $field); ?>
                    <?php if($field['is_pro'] && !$is_valid) { ?>
                        <button type="button" class="upgrade-link">Upgrade to Pro</button>
                    <?php } ?>
                </label>
                <?php if($field['is_pro'] && !$is_valid) { ?>
                    </a>
                <?php } ?>
                <?php do_action('folders_field_label_postfix', $field); ?>
            </div>
        <?php }
    }

    public function field_label_postfix($field) {
        if($field['id'] == 'use_shortcuts') { ?>
            <a href="#" class="view-shortcodes inline-flex" >(
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span><?php esc_html_e('View shortcuts', 'folders'); ?></span>)
            </a>
        <?php }
    }

    public function field_tooltip($field) {
        if($field['has_tooltip'] && !empty($field['tooltip'])) {
            if(isset($field['tooltip_image']) && !empty($field['tooltip_image'])) { ?>
                <span class="html-tooltip dynamic">
                    <span class="dashicons dashicons-editor-help"></span>
                    <span class="tooltip-text top" style="">
                        <?php echo esc_attr($field['tooltip']) ?>
                        <img src="<?php echo esc_url($field['tooltip_image']) ?>">
                    </span>
                </span>
            <?php } else { ?>
                <span class="folder-tooltip" data-title="<?php echo esc_attr($field['tooltip']) ?>">
                    <span class="dashicons dashicons-editor-help"></span>
                </span>
            <?php }
        }
    }
}
new Fldr_Form_Fields();