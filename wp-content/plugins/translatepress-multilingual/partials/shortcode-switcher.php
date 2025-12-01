<?php
/**
 * Expects (validated upstream):
 * @var array  $list            Current-first: ['code','name','url','flag']
 * @var array  $config
 * @var string $style_value     e.g. "--bg:#fff;--text:#000;--border-width:1px;--border-color:#ccc;--border-radius:8px"
 * @var string $flag_position   'before'|'after'
 * @var bool   $open_on_click
 * @var bool   $is_editor
 * @var bool   $is_opposite     Show only the “opposite button” (single current language, no dropdown)
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

if ( ! empty( $is_opposite ) ) {
    $anchor_classes[]  = 'trp-opposite-button';
    $overlay_classes[] = 'trp-opposite-button';
}

$current  = $list[0];
$has_more = count( $list ) > 1;

$list_id = 'trp-shortcode-dropdown-' . uniqid();

$render_header = static function( array $item, bool $show_arrow, bool $clickable, bool $is_opposite, string $flag_position, array $allowed_flag_html, bool $has_more, bool $as_control, string $list_id ): string {
    ob_start(); ?>
    <div class="trp-current-language-item__wrapper<?php echo $show_arrow ? '' : ' trp-hide-arrow'; ?>">
        <?php
            // Link target: control uses '#'; plain link uses real URL.
            $href = ( $as_control ) ? '#' : ( $clickable ? '#' : esc_url( $item['url'] ) );

            $tag = ( $as_control && ! $is_opposite ) ? 'div' : 'a';
            $classes = 'trp-language-item trp-language-item__default' . ( $is_opposite ? '' : ' trp-language-item__current' );

            $attrs = [
                'class' => $classes,
                'data-no-translation' => null,
            ];

            if ( $as_control && ! $is_opposite ) {
                // Control (toggle): floated model
                $attrs['role'] = 'button';
                $attrs['aria-expanded'] = 'false';
                $attrs['tabindex'] = '0';
                $attrs['aria-label'] = __( 'Change language', 'translatepress-multilingual' );
                $attrs['aria-controls'] = $list_id;
            } else {
                // Language switcher link
                $attrs['href'] = $href;
                $attrs['title'] = esc_html( $item['name'] );
            }

            echo '<' . $tag; // phpcs:ignore WordPress.Security.EscapeOutput
            foreach ( $attrs as $k => $v ) {
                echo $v === null ? ' ' . esc_attr( $k ) : ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
            }
            echo '>';
                if ( $flag_position === 'before' ) echo wp_kses( $item['flag'], $allowed_flag_html );
                if ( $item['name'] !== '' ) echo '<span class="trp-language-item-name">' . esc_html( $item['name'] ) . '</span>';
                if ( $flag_position === 'after' ) echo wp_kses( $item['flag'], $allowed_flag_html );
            echo '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
        ?>
        <?php if ( $show_arrow && $has_more ) : ?>
            <svg class="trp-shortcode-arrow" width="20" height="20" viewBox="0 0 20 21" fill="none" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 8L10 13L15 8" stroke="var(--text)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        <?php endif; ?>
    </div>
    <?php
    return (string) ob_get_clean();
};

// In opposite mode we still render the header, but without arrow and without dropdown.
$header_html_anchor  = $render_header( $current, empty( $is_opposite ), $is_editor, ! empty( $is_opposite ), $flag_position, $allowed_flag_html, $has_more, false, '' );
$header_html_control = $render_header( $current, empty( $is_opposite ), true,         ! empty( $is_opposite ), $flag_position, $allowed_flag_html, $has_more, true,  $list_id );
?>
<div class="trp-shortcode-switcher__wrapper"
     style="<?php echo esc_attr( $style_value ); ?>"
     role="group"
     data-open-mode="<?php echo esc_attr( $open_on_click ? 'click' : 'hover' ); ?>">

    <?php if ( ! empty( $is_opposite ) ) : ?>

        <!-- Opposite mode: render ONLY the in-flow (relative) element so it holds space -->
        <div class="<?php echo esc_attr( implode( ' ', $anchor_classes ) ); ?>"
             role="navigation"
             aria-label="<?php echo esc_attr__( 'Website language selector', 'translatepress-multilingual' ); ?>"
             data-no-translation>
            <?php echo $header_html_anchor; // phpcs:ignore WordPress.Security.EscapeOutput ?>
        </div>

    <?php else : ?>

        <!-- ANCHOR (in-flow only; sizing/borders; inert) -->
        <div class="<?php echo esc_attr( implode( ' ', $anchor_classes ) ); ?>"
             aria-hidden="true"
             inert
             data-no-translation>
            <?php echo $header_html_anchor; // phpcs:ignore WordPress.Security.EscapeOutput ?>
        </div>

        <!-- OVERLAY (positioned; interactive surface) -->
        <div class="<?php echo esc_attr( implode( ' ', $overlay_classes ) ); ?>"
             role="navigation"
             aria-label="<?php echo esc_attr__( 'Website language selector', 'translatepress-multilingual' ); ?>"
             data-no-translation
        >
            <?php echo $header_html_control; // phpcs:ignore WordPress.Security.EscapeOutput ?>

            <div class="trp-switcher-dropdown-list"
                 id="<?php echo esc_attr( $list_id ); ?>"
                 role="group"
                 aria-label="<?php echo esc_attr__( 'Available languages', 'translatepress-multilingual' ); ?>"
                 hidden
                 inert
            >
                <?php if ( $has_more ) : ?>
                    <?php foreach ( array_slice( $list, 1 ) as $item ) : ?>
                        <a class="trp-language-item" href="<?php echo ! $is_editor ? esc_url( $item['url'] ) : '#'; ?>" title="<?php echo esc_html( $item['name'] ); ?>">
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

    <?php endif; ?>
</div>
