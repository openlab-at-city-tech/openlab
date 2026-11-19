<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
$upgradeURL = \Folders\Admin\License::get_pro_url();
$tab = filter_input(INPUT_GET, 'tab');
?>
<div class="max-w-[1080px] mx-auto pt-5">
    <div class="pb-6 flex items-center justify-between gap-2">
        <div class="flex items-center gap-2.5">
            <div class="font-bold text-primary text-2xl"><?php esc_html_e("Folders", 'folders'); ?></div>
            <?php if($tab == 'manage-folders-plan') {
                $proURl = \Folders\Admin\Settings::get_setting_page_url();
                ?>
                <a href="<?php echo esc_url($proURl) ?>" class="border-1 inline-flex items-center gap-1.5 border-grey300 text-grey900! text-base! font-semibold rounded-lg px-3 py-1.5 flex items-center hover:bg-white">
                    <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.75 10.75L0.75 5.75L5.75 0.75" stroke="#717680" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?php esc_html_e('Go Back', 'folders'); ?>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="p-6 bg-white rounded-2xl">
        <div class="flex rounded-2xl license-page-grid">
            <div class="flex-1 p-5 sm:p-10 flex flex-col gap-4 justify-center items-center font-inter">
                <img class="h-18" src="<?php echo esc_url(FOLDERS_IMAGE_URL."license/register.png") ?>" alt="" />
                <div class="font-inter font-bold text-2xl"><?php esc_html_e("Unlock More With Folders Pro", 'folders'); ?></div>
                <div class="font-inter text-base text-center max-w-100"><?php esc_html_e("Get subfolders, custom colors, notifications, user access management, and more.", 'folders'); ?></div>
                <div class="field-button max-w-100 w-full">
                    <a href="<?php echo esc_url($upgradeURL) ?>" target="_blank" class="form-button bg-primary hover:bg-primaryDark ease-in-out duration-300 w-full text-white py-2 px-4 rounded-lg text-sm! flex justify-center items-center gap-2">
                        <?php esc_html_e("Upgrade to Pro", 'folders'); ?>
                    </a>
                </div>
            </div>
            <div class="flex-1 p-10 hidden sm:flex! justify-center items-center">
                <img class="max-h-100" src="<?php echo esc_url(FOLDERS_IMAGE_URL."license/key.png") ?>" alt="" />
            </div>
        </div>
    </div>
</div>
