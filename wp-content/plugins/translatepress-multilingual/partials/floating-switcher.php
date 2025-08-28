<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @var array  $list             Languages (code, name …)
 * @var array  $config           Floater config
 * @var string $styles           Inline CSS vars
 * @var string $viewport         Current Viewport
 * @var string $positionClass    Position based class
 * @var string $is_opposite      Is "Show opposite language" checked
 * @var array  $current_language Current language element
 */

$is_dropdown = $config['type'] === 'dropdown';
$others      = array_slice( $list, 1 );

$flag_ratio    = $config['flagShape'];
$flag_position = $config['layoutCustomizer'][ $viewport ]['flagIconPosition'];

// Helper to spit out one language item
$render_item = function ( $lang, $flag_ratio, $disabled = false ) use ($flag_position) {
    $tag   = $disabled ? 'div' : 'a';
    $class = 'trp-language-item' . ( $disabled ? ' trp-language-item__current' : '' );
    $url   = $disabled ? '#' : esc_url( $this->url_converter->get_url_for_language( $lang['code'] ) );

    $aria_selected  = $disabled ? 'aria-selected="true"' : 'aria-selected="false"';
    $role           = 'role="option"';
    $no_translation = 'data-no-translation';

    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo "<{$tag} href=\"{$url}\" class=\"{$class}\" {$role} {$aria_selected} {$no_translation}>";

    if ( $flag_position === 'before' )
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is escaped inside get_flag_html().
        echo $this->get_flag_html( $lang['code'], $flag_ratio );

    if ( !empty( $lang['name'] ) )
        echo '<span class="trp-language-item-name">' . esc_html( $lang['name'] ) . '</span>';

    if ( $flag_position === 'after' )
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is escaped inside get_flag_html().
        echo $this->get_flag_html( $lang['code'], $flag_ratio );

    if ( $flag_position === 'hide' )
        echo '';

    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo "</{$tag}>";
};
?>

<div
    class="trp-language-switcher trp-floating-switcher <?php echo $is_dropdown ? 'trp-ls-dropdown ' : 'trp-ls-inline '; echo esc_attr( $positionClass ); echo $is_opposite ? ' trp-opposite-language' : ''; ?>"
    style="<?php echo esc_attr( $styles ); ?>"
    role="navigation"
    aria-label="Website language selector"
    <?php if ( $is_dropdown ): ?>
        aria-haspopup="listbox"
        aria-controls="trp-switcher-dropdown-list"
        aria-expanded="false"
        tabindex="0"
    <?php endif; ?>
    data-no-translation
>
    <?php if ( !empty( $config['showPoweredBy'] ) ) : ?>
        <div id="trp-floater-powered-by">
            Powered by
            <a href="https://translatepress.com/?utm_source=language_switcher&amp;utm_medium=clientsite&amp;utm_campaign=TPLS"
               rel="nofollow"
               target="_blank"
               title="WordPress Translation Plugin">
                TranslatePress
            </a>
        </div>
    <?php endif; ?>

    <?php if ( $is_dropdown ) : ?>
        <div class="trp-language-switcher-inner">
            <?php $render_item( $current_language, $flag_ratio, !$is_opposite ); ?>

            <div class="trp-switcher-dropdown-list" id="trp-switcher-dropdown-list" role="listbox" hidden>
                <?php foreach ( $others as $lang ) : ?>
                    <?php $render_item( $lang, $flag_ratio ); ?>
                <?php endforeach; ?>
            </div>
        </div>

    <?php else : // inline – show only 2 items ?>
        <?php
            $inline = array_slice( $list, 0, 2 );

            echo '<div class="trp-language-switcher-inner">';

            foreach ( $inline as $lang ) {
                $disabled = !$is_opposite && $lang['code'] === $current_language['code'];
                $render_item( $lang, $flag_ratio, $disabled );
            }

            echo '</div>';
        ?>
    <?php endif; ?>
</div>