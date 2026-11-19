<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
ob_start();
?>
    <div class="flex flex-col gap-3 font-inter">
        <div class="text-center text-grey700 text-sm pb-4">
            <?php printf(esc_html__('Select the places where you want Folders to appear (Media Library, Posts, Pages, Custom Posts). Need help? Visit our %s', 'folders'), '<a href="https://premio.io/help/folders/?utm_source=wordpressfolders" target="_blank" class="text-primary! underline text-sm">' . esc_html__('help center', 'folders') . '</a>'); ?>
        </div>
        <div class="youtube-container">
            <iframe src="https://www.youtube.com/embed/gLBOSzCWUlU?rel=0"></iframe>
        </div>
    </div>
<?php
$content = ob_get_clean();
$args = [
    'id' => 'folders-shortcuts',
    'title' => esc_html__('Welcome to Folders 🎉', 'folders'),
    'content' => $content,
    'default_visible' => true,
    'title_class'   => 'text-center',
    'primary_button_text' => esc_html__('Go to Folders', 'folders'),
    'primary_button_classes' => 'hide-folders-modal mx-auto',
    'show_footer' => true,
];
\Folders\Admin\Modals::render_modal($args);