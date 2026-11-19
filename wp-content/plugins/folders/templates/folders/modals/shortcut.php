<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
$shortcodes = \Folders\Folders\KeyboardShortcut::get_shortcodes();
ob_start();
?>
<div class="folders-wrap">
    <div class="folders-shortcodes folders-flex flex-col gap-3 font-inter">
        <?php foreach ( $shortcodes as $shortcode ) { ?>
            <div class="folders-flex items-center gap-2 justify-between">
                <div>
                    <?php echo esc_html($shortcode['title']); ?>
                </div>
                <div class="shortcode-separator"></div>
                <div class="folders-flex gap-2 items-center">
                    <?php foreach($shortcode['key'] as $count => $key) { ?>
                        <?php if($key == 'Right-click') {  ?>
                            <div class="shortcode-box inline-flex gap-2 items-center">
                                <?php esc_html_e('Right-click', 'folders'); ?>
                                <svg class="h-5 w-auto" width="9" height="20" viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.59365 3.53527C4.58295 3.52318 4.5737 3.51121 4.56108 3.49978C4.42127 3.37424 4.41751 3.4113 4.57056 3.27023C4.68393 3.16618 4.82151 3.08477 4.94617 2.99476C5.27596 2.75639 5.49304 2.43714 5.63979 2.05866C5.96905 1.20884 4.99884 0.621719 4.51197 0.118773C4.16023 -0.245133 3.60641 0.309915 3.95781 0.673053C4.22929 0.954087 4.57896 1.18195 4.81526 1.49436C5.07987 1.84411 4.67664 2.23 4.40036 2.42471C3.97323 2.72566 3.60399 3.12536 3.68932 3.55399C1.77238 3.73308 0 4.78371 0 6.63585V11.8349C0 14.1316 1.86837 16 4.16522 16C6.46195 16 8.33051 14.1316 8.33051 11.8349V6.63543C8.33062 4.6134 6.44932 3.63414 4.59365 3.53527ZM0.784172 6.63543C0.784172 5.26064 2.27655 4.46462 3.82226 4.33241V9.12752C2.41225 9.05654 1.26747 8.73534 0.784172 8.57854V6.63543ZM7.54653 11.8345C7.54653 13.6993 6.02963 15.2159 4.16533 15.2159C2.3011 15.2159 0.784172 13.6993 0.784172 11.8345V9.4028C1.48432 9.61443 2.80225 9.9354 4.36 9.9354C5.35764 9.9354 6.45312 9.80193 7.54657 9.42567L7.54653 11.8345ZM7.54653 8.59173C6.55499 8.97091 5.54238 9.11912 4.60631 9.13926V4.32082C6.11822 4.41148 7.54653 5.13664 7.54653 6.63574V8.59173ZM7.2813 8.29489C7.2813 8.29489 5.49005 8.79646 4.88083 8.68896V4.71185C6.50223 5.01648 7.54564 5.5068 7.2813 8.29489Z" fill="#535862"/>
                                </svg>
                                <?php esc_html_e('On', 'folders'); ?>
                                <span class="pfolder-folders-close"></span>
                                <?php esc_html_e('icon', 'folders'); ?>
                            </div>
                        <?php } else if(strpos($key, 'dashicons') !== false || strpos($key, 'pfolder-folders-close') !== false) { ?>
                            <div class="shortcode-box"><span class="w-3! h-3! dashicons text-xs! <?php echo esc_html($key); ?>"></span></div>
                        <?php } else { ?>
                            <div class="shortcode-box"><?php echo esc_html($key); ?></div>
                        <?php } ?>
                        <?php if(count($shortcode['key']) - $count > 1) { ?>
                            <div class="inline-flex">+</div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php
$content = ob_get_clean();
$args = [
    'id' => 'keyboard-shortcut-modal',
    'title' => esc_html__('Keyboard shortcuts (Ctrl+/)', 'folders'),
    'content' => $content,
    'show_footer' => false,
    'default_visible' => false,
];
\Folders\Folders\FolderModals::render_modal($args);