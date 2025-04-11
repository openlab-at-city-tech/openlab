<?php

if ( !defined('ABSPATH' ) )
    exit();

$current_language_preference = $this->add_shortcode_preferences($shortcode_settings, $current_language['code'], $current_language['name']);

?>
<div class="trp_language_switcher_shortcode">
<div class="trp-language-switcher trp-language-switcher-container" data-no-translation <?php echo ( isset( $_GET['trp-edit-translation'] ) && $_GET['trp-edit-translation'] == 'preview' ) ? 'data-trp-unpreviewable="trp-unpreviewable"' : '' ?>>
    <div class="trp-ls-shortcode-current-language">
        <a href="#" class="trp-ls-shortcode-disabled-language trp-ls-disabled-language" title="<?php echo esc_attr( $current_language['name'] ); ?>" onclick="event.preventDefault()">
			<?php echo $current_language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>
		</a>
    </div>
    <div class="trp-ls-shortcode-language">
        <?php if ( apply_filters('trp_ls_shortcode_show_disabled_language', true, $current_language, $current_language_preference, $this->settings ) ){ ?>
        <a href="#" class="trp-ls-shortcode-disabled-language trp-ls-disabled-language"  title="<?php echo esc_attr( $current_language['name'] ); ?>" onclick="event.preventDefault()">
			<?php echo $current_language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>
		</a>
        <?php } ?>
    <?php foreach ( $other_languages as $code => $name ){

        $language_preference = $this->add_shortcode_preferences($shortcode_settings, $code, $name);
        ?>
        <a href="<?php echo (isset($is_editor) && $is_editor) ? '#' : esc_url( $this->url_converter->get_url_for_language($code, false) ); /* phpcs:ignore */ /* $is_editor is not outputted */ ?>" title="<?php echo esc_attr( $name ); ?>">
            <?php echo $language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>
        </a>

    <?php } ?>
    </div>
    <script type="application/javascript">
        // need to have the same with set from JS on both divs. Otherwise it can push stuff around in HTML
        var trp_ls_shortcodes = document.querySelectorAll('.trp_language_switcher_shortcode .trp-language-switcher');
        if ( trp_ls_shortcodes.length > 0) {
            // get the last language switcher added
            var trp_el = trp_ls_shortcodes[trp_ls_shortcodes.length - 1];

            var trp_shortcode_language_item = trp_el.querySelector( '.trp-ls-shortcode-language' )
            // set width
            var trp_ls_shortcode_width                                               = trp_shortcode_language_item.offsetWidth + 16;
            trp_shortcode_language_item.style.width                                  = trp_ls_shortcode_width + 'px';
            trp_el.querySelector( '.trp-ls-shortcode-current-language' ).style.width = trp_ls_shortcode_width + 'px';

            // We're putting this on display: none after we have its width.
            trp_shortcode_language_item.style.display = 'none';
        }
    </script>
</div>
</div>