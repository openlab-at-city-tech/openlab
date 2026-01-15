<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @var array  $list             Languages (code, name …)
 * @var array  $config           Floater config
 * @var string $styles           Inline CSS vars
 * @var string $viewport         Current Viewport
 * @var string $positionClass    Position based class
 * @var bool   $is_opposite      Is "Show opposite language" checked
 * @var array  $current_language Current language element
 */

$is_dropdown = $config['type'] === 'dropdown';
$others      = array_slice( $list, 1 );

$flag_ratio          = $config['flagShape'];
$flag_position       = $config['layoutCustomizer'][ $viewport ]['flagIconPosition'];
$language_name_mode  = $config['layoutCustomizer'][ $viewport ]['languageNames'];

$dropdown_id = 'trp-switcher-dropdown-list';

// Helper to spit out one language item.
// If $as_control === true, render the current language as a focusable disclosure control.
$render_item = function ( $lang, $flag_ratio, $as_control = false ) use ( $flag_position, $language_name_mode, $dropdown_id ) {
    $class           = 'trp-language-item' . ( $as_control ? ' trp-language-item__current' : '' );
    $title           = $lang['name'] ? 'title="' . esc_html( $lang['name'] ) . '"' : '';
    $no_translation  = 'data-no-translation';
    $item_has_label  = $language_name_mode !== 'none';

    if ( $as_control ) {
        $current_language_label = __( 'Change language', 'translatepress-multilingual' );
        // Toggle control: NOT a link; carries state and relationship to the popup
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '<div class="' . esc_attr( $class ) . '" ' . $title . ' role="button" tabindex="0" aria-expanded="false" aria-label="' . esc_attr( $current_language_label ) . '" aria-controls="' . esc_attr( $dropdown_id ) . '" ' . $no_translation . '>';

        if ( $flag_position === 'before' ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $this->get_flag_html( $lang['code'], $flag_ratio, $item_has_label );
        }
        if ( ! empty( $lang['name'] ) ) {
            echo '<span class="trp-language-item-name">' . esc_html( $lang['name'] ) . '</span>';
        }
        if ( $flag_position === 'after' ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $this->get_flag_html( $lang['code'], $flag_ratio, $item_has_label );
        }

        echo '</div>';
        return;
    }

    // Regular navigational item (link)
    $url = esc_url( $this->url_converter->get_url_for_language( $lang['code'] ) );

    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo '<a href="' . $url . '" class="' . esc_attr( $class ) . '" ' . $title . ' ' . $no_translation . '>';

    if ( $flag_position === 'before' ) {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $this->get_flag_html( $lang['code'], $flag_ratio, $item_has_label );
    }
    if ( ! empty( $lang['name'] ) ) {
        echo '<span class="trp-language-item-name">' . esc_html( $lang['name'] ) . '</span>';
    }
    if ( $flag_position === 'after' ) {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $this->get_flag_html( $lang['code'], $flag_ratio, $item_has_label );
    }

    echo '</a>';
};
?>

<nav
    class="trp-language-switcher trp-floating-switcher <?php echo $is_dropdown ? 'trp-ls-dropdown ' : 'trp-ls-inline '; echo esc_attr( $positionClass ); echo $is_opposite ? ' trp-opposite-language' : ''; ?>"
    style="<?php echo esc_attr( $styles ); ?>"
    role="navigation"
    aria-label="<?php echo esc_attr__( 'Website language selector', 'translatepress-multilingual' ); ?>"
    data-no-translation
>
    <?php if ( ! empty( $config['showPoweredBy'] ) ) : ?>
        <div id="trp-floater-powered-by">
            Powered by
            <a href="https://translatepress.com/?utm_source=frontend-ls&amp;utm_medium=client-site&amp;utm_campaign=powered-by-tp"
               rel="nofollow"
               target="_blank"
               title="<?php echo esc_attr__( 'WordPress Translation Plugin', 'translatepress-multilingual'); ?>">
                TranslatePress
            </a>
        </div>
    <?php endif; ?>

    <?php if ( $is_dropdown ) : ?>
        <div class="trp-language-switcher-inner">
            <?php
            // If "Show opposite language" is OFF, make current language the control (not a link).
            $render_item( $current_language, $flag_ratio, !$is_opposite );
            ?>

            <div
                class="trp-switcher-dropdown-list"
                id="<?php echo esc_attr( $dropdown_id ); ?>"
                role="group"
                aria-label="<?php echo esc_attr__( "Available languages", 'translatepress-multilingual' ); ?>"
                hidden
                inert
            >
                <?php foreach ( $others as $lang ) : ?>
                    <?php $render_item( $lang, $flag_ratio, false ); ?>
                <?php endforeach; ?>
            </div>
        </div>

    <?php else : // inline — show only 2 items ?>
        <?php
        $inline = array_slice( $list, 0, 2 );
        echo '<div class="trp-language-switcher-inner">';
        foreach ( $inline as $lang ) {
            $is_current = $lang['code'] === $current_language['code'];
            if ( $is_current ) {
                ob_start();
                $render_item( $lang, $flag_ratio, false );
                $html = ob_get_clean();
                $html = preg_replace( '/<a /', '<a aria-current="page" ', $html, 1 );
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo $html;
            } else {
                $render_item( $lang, $flag_ratio, false );
            }
        }
        echo '</div>';
        ?>
    <?php endif; ?>
</nav>
