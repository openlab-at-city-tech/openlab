<?php
/**
 * Expects (validated upstream):
 * @var array  $list            Current-first: ['code','name','url','flag']
 * @var array  $config
 * @var string $style_value     e.g. "--bg:#fff;--text:#000;--border-width:1px;--border-color:#ccc;--border-radius:8px"
 * @var string $flag_position   'before'|'after'
 * @var bool   $open_on_click
 * @var bool $is_editor
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$allowed_flag_html = apply_filters( 'trp_flag_html_allowed_tags', [
    'span' => [ 'class' => true, 'role' => true, 'aria-label' => true ],
    'img'  => [ 'src' => true, 'class' => true, 'alt' => true, 'role' => true, 'loading' => true ],
] );

$anchor_classes  = [ 'trp-language-switcher', 'trp-ls-dropdown', 'trp-shortcode-switcher', 'trp-shortcode-anchor' ];
$overlay_classes = [ 'trp-language-switcher', 'trp-ls-dropdown', 'trp-shortcode-switcher', 'trp-shortcode-overlay' ];
$mode_class      = $open_on_click ? 'trp-open-on-click' : 'trp-open-on-hover';

$anchor_classes[]  = $mode_class;   // harmless on anchor (layout only)
$overlay_classes[] = $mode_class;   // used by CSS/JS on overlay

$current  = $list[0];
$has_more = count( $list ) > 1;

$render_header = static function( array $item, bool $show_arrow,  bool $clickable ) use ( $flag_position, $allowed_flag_html, $has_more ): string {
    ob_start(); ?>
    <div class="trp-current-language-item__wrapper">
        <a class="trp-language-item trp-language-item__default trp-language-item__current"
           href="<?php echo !$clickable ? esc_url( $item['url'] ) : '#'; ?>"
           aria-current="true" role="option" aria-selected="true" tabindex="0" data-no-translation>
            <?php if ( $flag_position === 'before' ) echo wp_kses( $item['flag'], $allowed_flag_html ); ?>
            <?php if ( $item['name'] !== '' ) : ?>
                <span class="trp-language-item-name"><?php echo esc_html( $item['name'] ); ?></span>
            <?php endif; ?>
            <?php if ( $flag_position === 'after' ) echo wp_kses( $item['flag'], $allowed_flag_html ); ?>
        </a>
        <?php if ( $show_arrow && $has_more ) : ?>
            <svg class="trp-shortcode-arrow" width="20" height="20" viewBox="0 0 20 21" fill="none" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 8L10 13L15 8" stroke="var(--text)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        <?php endif; ?>
    </div>
    <?php
    return (string) ob_get_clean();
};

$header_html = $render_header( $current, true, $is_editor );
?>
<div class="trp-shortcode-switcher__wrapper"
     style="<?php echo esc_attr( $style_value ); ?>"
     role="group"
     data-open-mode="<?php echo esc_attr( $open_on_click ? 'click' : 'hover' ); ?>">

    <!-- ANCHOR (in-flow only; sizing/borders; inert) -->
    <div class="<?php echo esc_attr( implode( ' ', $anchor_classes ) ); ?>"
         aria-hidden="true"
         inert
         data-no-translation>
        <?php echo $header_html; // phpcs:ignore WordPress.Security.EscapeOutput ?>
    </div>

    <!-- OVERLAY (positioned; interactive surface) -->
    <div class="<?php echo esc_attr( implode( ' ', $overlay_classes ) ); ?>"
         role="listbox"
         aria-haspopup="listbox"
         aria-expanded="false"
         tabindex="0"
         data-no-translation
    >
        <?php echo $header_html; // phpcs:ignore WordPress.Security.EscapeOutput ?>

        <div class="trp-switcher-dropdown-list" hidden inert>
            <?php if ( $has_more ) : ?>
                <?php foreach ( array_slice( $list, 1 ) as $item ) : ?>
                    <a class="trp-language-item" href="<?php echo !$is_editor ? esc_url( $item['url'] ) : '#';  ?>" role="option" tabindex="-1">
                        <?php if ( $flag_position === 'before' ) echo wp_kses( $item['flag'], $allowed_flag_html ); ?>
                        <?php if ( $item['name'] !== '' ) : ?>
                            <span class="trp-language-item-name" data-no-translation><?php echo esc_html( $item['name'] ); ?></span>
                        <?php endif; ?>
                        <?php if ( $flag_position === 'after' ) echo wp_kses( $item['flag'], $allowed_flag_html ); ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
