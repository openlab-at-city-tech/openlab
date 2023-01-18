<?php
defined( 'ABSPATH' ) || die;

// ThickBox JS and CSS
add_thickbox();

$blocks_list_saved = get_option( 'advgb_blocks_list' );
$advgb_blocks      = [];

if ( gettype( $blocks_list_saved ) === 'array' ) {
    foreach ( $blocks_list_saved as $block ) {
        if ( strpos( $block['name'], 'advgb/' ) === false ) {
            continue;
        } else {
            $block['icon'] = htmlentities( $block['icon'] );
            array_push( $advgb_blocks, $block );
        }
    }
}

/**
 * Sort array
 *
 * @param string $key Array key to sort
 *
 * @return Closure
 */
function sortBy( $key )
{
    return function ( $a, $b ) use ( $key ) {
        return strnatcmp( $a[$key], $b[$key] );
    };
}

usort( $advgb_blocks, sortBy( 'title' ) );
$excluded_blocks_config = [
    'advgb/container',
    'advgb/accordion-item',
    'advgb/accordion',
    'advgb/tabs',
    'advgb/tab',
    'advgb/recent-posts',
    'advgb/login-form',
    'advgb/search-bar',
    'advgb/countdown',
    'advgb/feature-list',
    'advgb/feature',
    'advgb/pricing-table'
];

$new_titles = [
    'advgb/accordions' => __( 'Accordion - PublishPress', 'advanced-gutenberg' ),
    'advgb/button' => __( 'Button - PublishPress', 'advanced-gutenberg' ),
    'advgb/icon' => __( 'Icon - PublishPress', 'advanced-gutenberg' ),
    'advgb/image' => __( 'Image - PublishPress', 'advanced-gutenberg' ),
    'advgb/list' => __( 'List - PublishPress', 'advanced-gutenberg' ),
    'advgb/table' => __( 'Table - PublishPress', 'advanced-gutenberg' ),
    'advgb/adv-tabs' => __( 'Tabs - PublishPress', 'advanced-gutenberg' ),
    'advgb/video' => __( 'Video - PublishPress', 'advanced-gutenberg' ),
    'advgb/columns' => __( 'Columns - PublishPress', 'advanced-gutenberg' ),
    'advgb/column' => __( 'Column - PublishPress', 'advanced-gutenberg' ),
    'advgb/contact-form' => __( 'Contact Form - PublishPress', 'advanced-gutenberg' ),
    'advgb/count-up' => __( 'Count Up - PublishPress', 'advanced-gutenberg' ),
    'advgb/images-slider' => __( 'Images Slider - PublishPress', 'advanced-gutenberg' ),
    'advgb/infobox' => __( 'Info Box - PublishPress', 'advanced-gutenberg' ),
    'advgb/map' => __( 'Map - PublishPress', 'advanced-gutenberg' ),
    'advgb/newsletter' => __( 'Newsletter - PublishPress', 'advanced-gutenberg' ),
    'advgb/social-links' => __( 'Social Links - PublishPress', 'advanced-gutenberg' ),
    'advgb/summary' => __( 'Table of Contents - PublishPress', 'advanced-gutenberg' ),
    'advgb/testimonial' => __( 'Testimonial - PublishPress', 'advanced-gutenberg' ),
    'advgb/woo-products' => __( 'Woo Products - PublishPress', 'advanced-gutenberg' )
];

// Pro
if( defined( 'ADVANCED_GUTENBERG_PRO' ) ) {
    if ( method_exists( 'PPB_AdvancedGutenbergPro\Utils\Definitions', 'advgb_pro_default_block_settings' ) ) {
        $excludedProBlocks = PPB_AdvancedGutenbergPro\Utils\Definitions::advgb_pro_default_block_settings( 'excluded_blocks' );
        foreach ( $excludedProBlocks as $excludedProBlock ) {
            array_push(
                $excluded_blocks_config,
                $excludedProBlock
            );
        }
    }
}
?>
<div class="publishpress-admin wrap">
    <header>
        <h1 class="wp-heading-inline">
            <?php esc_html_e( 'PublishPress Blocks', 'advanced-gutenberg' ) ?>
        </h1>
    </header>
    <div class="wrap">
        <div class="advgb-search-wrapper" style="padding-bottom: 20px;">
            <input type="text"
                   class="advgb-search-input blocks-config-search"
                   placeholder="<?php esc_attr_e( 'Search blocks', 'advanced-gutenberg' ) ?>"
            >
        </div>
        <ul class="blocks-config-list clearfix">
            <?php foreach ( $advgb_blocks as $block ) : ?>
                <?php $iconColor = '';
                if ( in_array( $block['name'], $excluded_blocks_config ) ) {
                    continue;
                }
                if ( isset( $block['iconColor'] ) ) :
                    $iconColor = 'style="color:' . esc_attr( $block['iconColor'] ) . '"';
                endif;

                // Use new block title
                if( isset( $new_titles[$block['name']] ) ) {
                    $block['title'] = $new_titles[$block['name']];
                    //$block['title'] = str_replace( 'PublishPress', '', $new_titles[$block['name']] ); // Remove 'PublishPress'
                    //$block['title'] = str_replace( '-', '', $block['title'] ); // Remove hyphen in RTL and LTR
                }
                ?>
            <li class="block-config-item advgb-settings-option" title="<?php echo esc_attr( __( $block['title'], 'advanced-gutenberg' ) ); ?>">
                <span class="block-icon" <?php echo $iconColor ?>>
                    <?php echo html_entity_decode( html_entity_decode( stripslashes( $block['icon'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped ?>
                </span>
                <span class="block-title"><?php echo esc_html( __( $block['title'], 'advanced-gutenberg' ) ); ?></span>
                <i class="dashicons dashicons-admin-generic block-config-button"
                   title="<?php esc_attr_e( 'Edit', 'advanced-gutenberg' ) ?>"
                   data-block="<?php echo esc_attr( $block['name'] ); ?>"
                ></i>
            </li>
            <?php endforeach; ?>
        </ul>

        <?php if ( count( $advgb_blocks ) === 0 ) : ?>
            <div class="blocks-not-loaded" style="text-align: center">
                <p>
                    <?php esc_html_e( 'No blocks available. Please go to a post edit (without saving either modifying anything). Then come back to Block Settings to see the blocks list.', 'advanced-gutenberg' ); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>
