<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
$hasValidKey = \Folders\Admin\License::is_license_active();
$upgradeURL = \Folders\Admin\License::get_pro_url();
$active_tab = apply_filters( 'folders_active_tab', 'general-settings' );
?>
<div id="notifications" class="folders-tab <?php echo esc_attr($active_tab == 'notifications' ? 'active' : ''); ?>">
    <form id="notifications-form" class="folder-form" method="post" action="">
        <div class="flex gap-2 items-start">
            <a href="<?php echo esc_url($upgradeURL) ?>" target="_blank" for="notification_setting" class="relative flex items-start cursor-pointer gap-2 pro-feature">
                <input disabled id="notification_setting" type="checkbox" value="on" name="notification_setting[allow_notification]" class="sr-only peer">
                <span class="relative mt-1 w-9 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-teal"></span>
                <span class="flex flex-col w-full h-full flex-1">
                    <span class="text-base text-grey900 flex items-center gap-2">
                        <img class="w-5 h-5" src="<?php echo esc_url( FOLDERS_IMAGE_URL . 'settings/pro-crown.svg' ); ?>" alt="">
                        <?php esc_html_e('Allow Notifications', 'folders'); ?>
                        <span class="license-url"><?php esc_html_e("Upgrade to Pro", "folders") ?></span>
                    </span>
                    <span class="text-sm text-[#717680]"><?php esc_html_e('Get notified when folders or their contents are updated.', 'folders'); ?></span>
                </span>
            </a>
        </div>
        <div class="pt-5 h-100 overflow-hidden relative">
            <div class="flex flex-col gap-4">
                <div class="notification-emails">
                    <div class="text-base text-grey900 pb-2">
                        <?php esc_html_e('Notification Email', 'folders'); ?>
                        <div class="relative group inline-flex items-center gap-1 align-middle">
                            <div class="text-grey cursor-pointer w-4 h-4">
                                <svg class="w-4 h-4" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.33333 12.6667V9.33333M9.33333 6H9.34167M17.6667 9.33333C17.6667 13.9357 13.9357 17.6667 9.33333 17.6667C4.73096 17.6667 1 13.9357 1 9.33333C1 4.73096 4.73096 1 9.33333 1C13.9357 1 17.6667 4.73096 17.6667 9.33333Z" stroke="#A4A7AE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </div>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-[280px] p-3 bg-white text-sm text-grey rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-grey200 font-normal leading-normal">
                                <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-b border-r border-grey200 rotate-45"></div>
                                <div class="flex flex-col gap-2">
                                    <div class="font-inter">
                                        <?php esc_html_e('Notification Will be sent to this email', 'folders'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button disabled id="add-another-mail" type="button" class="mt-1.5 text-primary! text-base! hover:text-primaryDark!"><?php esc_html_e('+ Add Another Email', 'folders'); ?></button>
                </div>
            </div>
            <div class="absolute flex h-full w-full bg-linear-to-t from-white justify-center flex-col gap-2 items-center to-transparent top-0 left-0 backdrop-blur-[1px]">
                <div>
                    <svg width="47" height="47" viewBox="0 0 47 47" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_1876_1166)">
                            <path d="M23.4987 0C16.4722 0.00862891 10.7782 5.70261 10.7695 12.7292V20.5625C10.7695 21.1033 11.208 21.5417 11.7487 21.5417H15.6654C16.2062 21.5417 16.6446 21.1033 16.6446 20.5625V12.7292C16.6445 8.94368 19.7133 5.875 23.4987 5.875C27.2842 5.875 30.3529 8.94368 30.3529 12.7292V20.5625C30.3529 21.1033 30.7913 21.5417 31.3321 21.5417H35.2487C35.7895 21.5417 36.2279 21.1033 36.2279 20.5625V12.7292C36.2193 5.70261 30.5252 0.00862891 23.4987 0Z" fill="black"/>
                            <path d="M11.7513 19.584H35.2513C37.9551 19.584 40.1471 21.7759 40.1471 24.4798V42.1048C40.1471 44.8088 37.9551 47.0007 35.2513 47.0007H11.7513C9.04739 47.0007 6.85547 44.8088 6.85547 42.1049V24.4799C6.85547 21.7759 9.04739 19.584 11.7513 19.584Z" fill="#FFB743"/>
                            <path d="M28.3971 30.354C28.4085 27.6501 26.2258 25.4489 23.522 25.4375C20.8181 25.4262 18.6169 27.6088 18.6055 30.3127C18.5976 32.1817 19.6545 33.892 21.3295 34.7211L20.5735 40.0086C20.4978 40.544 20.8705 41.0394 21.406 41.1152C21.4513 41.1216 21.4971 41.1248 21.5429 41.1248H25.4596C26.0004 41.1303 26.4432 40.6964 26.4486 40.1556C26.4491 40.1058 26.4459 40.0559 26.4387 40.0066L25.6828 34.7191C27.3373 33.8911 28.3864 32.204 28.3971 30.354Z" fill="black"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_1876_1166">
                                <rect width="47" height="47" fill="white"/>
                            </clipPath>
                        </defs>
                    </svg>
                </div>
                <div class="text-grey900 text-base font-semibold"><?php esc_html_e('Stay Notified', 'folders'); ?></div>
                <div class="text-grey600 text-sm max-w-100 text-center"><?php esc_html_e('Get notified whenever changes are made to Pages, Posts, Plugins, or Media Files', 'folders'); ?></div>
                <div class="pt-3">
                    <a href="<?php echo esc_url($upgradeURL) ?>" target="_blank" class="inline-flex bg-white items-center py-2 px-3 rounded-lg border-1 border-primary text-primary! gap-1.5 text-base! font-semibold! hover:bg-primary! hover:text-white!">
                        <img class="w-5 h-5" src="<?php echo esc_url( FOLDERS_IMAGE_URL . 'settings/pro-crown.svg' ); ?>" alt="">
                        <span class=""><?php esc_html_e('Upgrade to Pro', 'folders'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
