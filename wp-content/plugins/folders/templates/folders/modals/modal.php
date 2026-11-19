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
    'show_footer'              => true,
    'has_close_button'         => true,
    'default_visible'          => false,
    'before_title'             => '',
    'close_button_class'       => '',
];
$args    = shortcode_atts( $default, $args );
?>
<div id="<?php echo esc_attr( $args['id'] ); ?>" class="folders-modal custom-folder-modal <?php echo esc_attr( $args['default_visible']?'active':'' ); ?> <?php echo esc_attr( $args['id'] ); ?>">
    <!-- Overlay for click-to-close -->
    <div class="folder-modal-overlay"></div>

    <!-- Modal Content -->
    <div class="folder-modal-content folders-wrap">
        <div class="popup-form-effect"></div>
        <!-- Header -->
        <?php if ( $args['has_close_button'] ) { ?>
            <button type="button" class="close-modal <?php echo esc_attr( $args['close_button_class'] ); ?>">
                <svg class="close-icon" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 1L1 13M1 1L13 13" stroke="#181D27" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="sr-only"><?php esc_html_e('Hide modal', 'folders'); ?></span>
            </button>
        <?php } ?>
        <?php if ( $args['before_title'] ) { ?>
            <div class="before-title">
                <?php
                echo $args['before_title'];
                ?>
            </div>
        <?php } ?>
        <?php if ( $args['title'] ) { ?>
            <div class="modal-title <?php echo esc_attr( $args['title_class'] ); ?>"><?php echo esc_attr( $args['title'] ); ?></div>
        <?php } ?>

        <!-- Body -->
        <div class="modal-body">
            <?php echo $args['content']; // Ensure content is sanitized before passing if needed. ?>
        </div>

        <!-- Footer (Optional) -->
        <?php if ( $args['show_footer'] ) { ?>
            <div class="folders-flex gap-2-5 folders-button-wrap">
                <?php if ( $args['primary_button_text'] ) {
                    $id = $args['primary_button_id'] ? esc_attr( $args['primary_button_id'] ) : 'button-primary-'.uniqid();
                    ?>
                    <button id="<?php echo esc_attr($id) ?>" type="button" class="primary-folder-button form-submit-button <?php echo esc_attr($args['primary_button_classes']) ?>" >
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
        <?php } ?>
    </div>
</div>