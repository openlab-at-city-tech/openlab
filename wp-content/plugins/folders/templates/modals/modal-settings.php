<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
$default = [
    'id'                       => '',
    'title'                    => '',
    'title_class'              => '',
    'content'                  => '',
    'primary_button_text'      => '',
    'secondary_button_text'    => '',
    'primary_button_classes'   => '',
    'secondary_button_classes' => '',
    'primary_button_id'        => '',
    'secondary_button_id'      => '',
    'footer_sidebar'           => '',
    'show_footer'              => true,
    'has_close_button'         => true,
    'default_visible'          => false,
    'disabled'                 => false,
    'primary_button_disabled'  => false,
];
$args    = shortcode_atts( $default, $args );
?>
<div id="<?php echo esc_attr( $args['id'] ); ?>" class="folders-modal fixed inset-0 z-99999 flex items-center justify-center w-full h-full bg-grey/50 backdrop-blur-sm <?php echo esc_attr( !$args['default_visible']?'hidden':'' ); ?>">
    <!-- Overlay for click-to-close -->
    <div class="modal-overlay absolute inset-0 w-full h-full <?php echo esc_attr($args['disabled']?'disabled':'') ?>"></div>

    <!-- Modal Content -->
    <div class="relative modal-content bg-white rounded-lg p-10 shadow-xl max-h-fit w-full max-w-xl mx-4 overflow-y-auto animate-fade-in-up">
        <div class="popup-form-effect"></div>
        <!-- Header -->
        <?php if ( $args['has_close_button'] ) { ?>
            <button type="button" class="absolute flex flex-nowrap items-center justify-center right-4 top-4 h-6 w-6 close-modal text-grey hover:scale-125 origin-center transition-transform">
                <svg class="w-3 h-3" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 1L1 13M1 1L13 13" stroke="#181D27" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        <?php } ?>
        <?php if ( $args['title'] ) { ?>
            <div class="<?php echo esc_attr( $args['title_class'] ); ?> font-bold font-inter mb-4 text-grey text-2xl"><?php echo esc_attr( $args['title'] ); ?></div>
        <?php } ?>

        <!-- Body -->
        <div class="modal-body">
            <?php
            $content      = isset( $args['content'] ) ? $args['content'] : '';
            $allowed_html = apply_filters( 'folders_modal_allowed_html', wp_kses_allowed_html( 'post' ) );
            $allowed_html['iframe'] = array(
                'src'             => true,
                'height'          => true,
                'width'           => true,
                'frameborder'     => true,
                'allowfullscreen' => true,
                'allow'           => true,
                'title'           => true,
                'style'           => true,
                'class'           => true,
                'id'              => true,
            );
            $allowed_html['svg'] = array(
                'xmlns' => true,
                'width' => true,
                'height' => true,
                'viewbox' => true,
                'fill' => true,
                'class' => true,
                'style' => true,
                'id' => true,
                'aria-hidden' => true,
                'focusable' => true,
            );
            $allowed_html['path'] = array(
                'd' => true,
                'fill' => true,
                'stroke' => true,
                'stroke-width' => true,
                'stroke-linecap' => true,
                'stroke-linejoin' => true,
                'fill-rule' => true,
                'clip-rule' => true,
            );
            echo wp_kses( $content, $allowed_html );
            ?>
        </div>

        <!-- Footer (Optional) -->
        <?php if ( $args['show_footer'] ) { ?>
            <div class="pt-6 bg-grey100/30 flex justify-between items-center gap-3">
                <div class="flex justify-start items-center gap-3 w-full">
                    <?php if ( $args['primary_button_text'] ) {
                        $id = $args['primary_button_id'] ? esc_attr( $args['primary_button_id'] ) : 'button-primary-'.uniqid();
                        ?>
                        <button <?php echo esc_attr($args['primary_button_disabled'] ? 'disabled' : '') ?> id="<?php echo esc_attr($id) ?>" type="button" class="primary-folder-button px-4 py-2 inline-flex flex-nowrap items-center gap-2 font-semibold! text-white text-base! bg-[#CE136D] border border-[#CE136D] rounded-md hover:bg-[#a1004c]! <?php echo esc_attr($args['primary_button_classes']) ?>" >
                            <span class="button-text">
                                <?php echo esc_attr( $args['primary_button_text'] ); ?>
                            </span>
                            <span class="folders-loader w-4! h-4!"></span>
                        </button>
                    <?php } ?>
                    <?php if ( $args['secondary_button_text'] ) {
                        $id = $args['secondary_button_id'] ? esc_attr( $args['secondary_button_id'] ) : 'button-secondary-'.uniqid();
                        ?>
                        <button id="<?php echo esc_attr($id) ?>" type="button" class="px-4 py-2 text-base! font-semibold! text-grey bg-white border border-grey300 rounded-md hover:bg-grey100! <?php echo esc_attr($args['secondary_button_classes']) ?>">
                            <?php echo esc_attr( $args['secondary_button_text'] ); ?>
                        </button>
                    <?php } ?>
                    <!-- Add Save/Action buttons here if needed -->
                </div>
                <?php echo $args['footer_sidebar']; ?>
            </div>
        <?php } ?>
    </div>
</div>