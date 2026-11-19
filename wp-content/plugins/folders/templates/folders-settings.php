<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
delete_transient( 'premio_folders_without_trash' );
$active_tab = apply_filters( 'folders_active_tab', 'general-settings' );
$menu_items = [
    'general-settings' => esc_html__( "General Settings", "folders" ),
    'customize-folders' => esc_html__( "Customize Folders", "folders" ),
    'user-restrictions' => esc_html__( "User Restrictions", "folders" ),
    'notifications' => esc_html__( "Notifications", "folders" ),
    'tools-and-maintenance' => esc_html__( "Tools & Maintenance", "folders" )
];
?>
<div class="max-w-[1080px] mx-auto pt-0 relative mt-6">
    <div class="flex flex-wrap items-center justify-between gap-2 sticky top-0 sm:top-8 z-[99] bg-[#f0f0f1] bg-white rounded shadow px-4 py-4">
        <div class="flex items-center gap-2.5">
            <svg class="w-12 h-auto" width="51" height="43" viewBox="0 0 51 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.65329 13.305C5.22728 18.1698 6.18386 25.2326 6.62612 32.1046C6.76051 34.1927 8.07047 36.1016 10.9937 35.8609C13.917 35.6201 32.1774 34.3517 36.1254 34.1928C40.0734 34.0338 40.1154 32.0221 40.2664 30.5762C40.4174 29.1303 40.1209 16.3644 40.1043 13.305C40.0878 10.2455 38.0803 10.0023 36.5317 9.94977C34.9832 9.89726 21.9301 11.4155 20.7734 10.9083C19.6167 10.4011 17.5675 8.30538 16.8655 8.19008C16.1635 8.07477 10.5449 8.61189 7.96321 8.87991C5.38151 9.14793 4.3655 10.8658 4.65329 13.305Z" fill="#F4A261"/>
                <path d="M8.15556 10.9789C8.05278 12.9411 8.15556 24.7228 8.15556 30.7545C8.02936 32.9453 8.99094 34.2184 11.9236 34.1657C14.8563 34.113 33.1605 34.0185 37.1105 34.1132C41.0606 34.2078 41.2316 32.2029 41.475 30.7697C41.7185 29.3365 42.2414 16.5779 42.4212 13.5236C42.601 10.4694 40.6132 10.0979 39.0712 9.94617C37.5292 9.79443 24.4056 10.4722 23.2838 9.89181C22.1621 9.31145 20.2515 7.08863 19.5583 6.92852C18.8652 6.76842 15.0735 6.82666 12.4799 6.92852C9.88634 7.03038 8.28402 8.52619 8.15556 10.9789Z" fill="#F6D365"/>
                <path d="M13.4486 8.80143C13.2114 10.7519 11.6778 22.848 10.9407 28.6522C10.6643 30.8291 11.5362 32.1653 14.4656 32.3141C17.395 32.4629 35.6624 33.6258 39.5966 33.9915C43.5308 34.3572 43.8391 32.3688 44.1804 30.9557C44.5217 29.5426 45.9197 16.85 46.3088 13.8153C46.698 10.7807 44.7404 10.2735 43.2125 10.0162C41.6845 9.75895 28.5453 9.53377 27.4661 8.87773C26.3868 8.2217 24.6334 5.87291 23.9529 5.66558C23.2724 5.45825 20.0814 5.28925 17.487 5.21274C14.8925 5.13622 13.7453 6.36329 13.4486 8.80143Z" fill="#2A9D8F"/>
                <path d="M18.7814 7.41725C18.2214 9.30067 14.6879 20.9703 12.9911 26.5696C12.3548 28.6698 12.9911 30.1329 15.8544 30.7692C18.7177 31.4055 36.5339 35.605 40.3516 36.623C44.1694 37.6411 44.8057 35.7322 45.3784 34.396C45.951 33.0598 49.4506 20.7794 50.3414 17.8524C51.2322 14.9255 49.387 14.0983 47.9235 13.5893C46.46 13.0802 33.5433 10.6623 32.5889 9.83516C31.6344 9.00798 30.2982 6.39918 29.6619 6.08104C29.0257 5.76289 25.9078 5.06297 23.3627 4.55394C20.8175 4.0449 19.4813 5.06297 18.7814 7.41725Z" fill="#CE136D"/>
                <g clip-path="url(#clip0_1766_1906)">
                    <path d="M40.3398 33.7573C40.3398 34.4919 40.7668 35.1267 41.3858 35.4275L40.5005 33.002C40.3975 33.2328 40.3398 33.4882 40.3398 33.7573Z" fill="white"/>
                    <path d="M43.4481 33.6645C43.4481 33.4352 43.3657 33.2763 43.2951 33.1527C43.201 32.9998 43.1128 32.8703 43.1128 32.7175C43.1128 32.5469 43.2422 32.3881 43.4245 32.3881C43.4327 32.3881 43.4405 32.3891 43.4485 32.3895C43.1183 32.087 42.6784 31.9023 42.1952 31.9023C41.5468 31.9023 40.9764 32.235 40.6445 32.7389C40.6881 32.7402 40.7291 32.7411 40.764 32.7411C40.9581 32.7411 41.2586 32.7175 41.2586 32.7175C41.3586 32.7116 41.3704 32.8586 41.2705 32.8704C41.2705 32.8704 41.1699 32.8822 41.0581 32.8881L41.7339 34.8984L42.1401 33.6803L41.8509 32.888C41.751 32.8822 41.6563 32.8703 41.6563 32.8703C41.5563 32.8645 41.568 32.7116 41.668 32.7175C41.668 32.7175 41.9745 32.741 42.1569 32.741C42.351 32.741 42.6515 32.7175 42.6515 32.7175C42.7516 32.7116 42.7634 32.8585 42.6634 32.8703C42.6634 32.8703 42.5627 32.8822 42.451 32.888L43.1217 34.8831L43.3068 34.2645C43.3871 34.0078 43.4481 33.8234 43.4481 33.6645Z" fill="white"/>
                    <path d="M42.2268 33.9199L41.6699 35.538C41.8362 35.5869 42.012 35.6136 42.1942 35.6136C42.4103 35.6136 42.6176 35.5762 42.8105 35.5084C42.8055 35.5004 42.801 35.492 42.7973 35.4828L42.2268 33.9199Z" fill="white"/>
                    <path d="M43.8244 32.8672C43.8324 32.9263 43.8369 32.9898 43.8369 33.058C43.8369 33.2464 43.8018 33.4581 43.6958 33.7228L43.1289 35.3618C43.6807 35.0401 44.0518 34.4423 44.0518 33.7576C44.0518 33.435 43.9694 33.1315 43.8244 32.8672Z" fill="white"/>
                    <path d="M42.1947 31.5938C41.0018 31.5938 40.0312 32.5642 40.0312 33.7571C40.0312 34.9501 41.0018 35.9205 42.1947 35.9205C43.3876 35.9205 44.3583 34.9501 44.3583 33.7571C44.3582 32.5642 43.3876 31.5938 42.1947 31.5938ZM42.1947 35.8214C41.0565 35.8214 40.1304 34.8953 40.1304 33.7571C40.1304 32.6189 41.0565 31.6929 42.1947 31.6929C43.3329 31.6929 44.2589 32.6189 44.2589 33.7571C44.2589 34.8953 43.3329 35.8214 42.1947 35.8214Z" fill="white"/>
                </g>
                <defs>
                    <clipPath id="clip0_1766_1906">
                        <rect width="4.32678" height="4.32678" fill="white" transform="translate(40.0312 31.5938)"/>
                    </clipPath>
                </defs>
            </svg>
            <div class="font-bold text-primary text-2xl"><?php esc_html_e("Folders", 'folders'); ?></div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="folders-submit-button" class="form-button bg-primary text-white py-2 px-4 rounded-lg text-base! flex items-center gap-2">
                <span class="folders-loader w-5! h-5!"></span>
                <svg class="default-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15.8333 17.5H4.16667C3.72464 17.5 3.30072 17.3244 2.98816 17.0118C2.67559 16.6993 2.5 16.2754 2.5 15.8333V4.16667C2.5 3.72464 2.67559 3.30072 2.98816 2.98816C3.30072 2.67559 3.72464 2.5 4.16667 2.5H13.3333L17.5 6.66667V15.8333C17.5 16.2754 17.3244 16.6993 17.0118 17.0118C16.6993 17.3244 16.2754 17.5 15.8333 17.5Z" stroke="white" stroke-width="2.08" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M14.1663 17.5006V10.834H5.83301V17.5006" stroke="white" stroke-width="2.08" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5.83301 2.5V6.66667H12.4997" stroke="white" stroke-width="2.08" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <?php esc_html_e('Save Changes' , 'folders'); ?>
            </button>
        </div>
    </div>
    <div class="bg-white rounded shadow p-6 mt-6 relative">
        <div class="pb-3 mb-5 font-semibold text-xl text-grey border-b border-grey200"><?php esc_html_e('Folders Settings', 'folders'); ?></div>
        <div class="flex flex-col lg:flex-row gap-5 relative items-start">
            <div class="w-full pb-2 lg:pb-0 lg:w-[210px] block sticky top-26.5 bg-white z-[9] border-b border-grey200 lg:border-transparent">
                <div id="folders-tab-menu" class="flex flex-row flex-wrap lg:flex-nowrap lg:flex-col gap-0.5 [&>a]:block [&>a]:rounded-r-lg [&>a]:py-3 [&>a]:px-4 [&>a]:text-base! [&>a]:border-l-[2px] [&>a]:border-white [&>a]:border-solid [&>a]:bg-grey100 [&>a]:text-grey! [&>a]:font-medium [&>a:hover]:bg-primary/4 [&>a:hover]:text-primary! [&>a:hover:not(.active)]:border-transparent! [&>a:focus-visible]:bg-primary/4 [&>a.active]:bg-primary/4 lg:[&>a.active]:border-primary! [&>a.active]:text-primary!">
                    <?php foreach ($menu_items as $menu_slug => $menu_item) { ?>
                        <a href="#<?php echo esc_attr($menu_slug); ?>" class="<?php echo esc_attr($menu_slug == $active_tab ? 'active' : '') ?>"><?php echo esc_html($menu_item) ?></a>
                    <?php }
                    $page_settings = \Folders\Admin\Settings::get_field_settings('general_settings', 'show_folder_in_settings');
                    if($page_settings) {
                        $proURl = \Folders\Admin\Settings::get_setting_page_url();
                        $menu_title = esc_html__( "Upgrade to Pro", "folders" ); ?>
                            <a href="<?php echo esc_url($proURl) ?>&tab=manage-folders-plan" class="<?php echo esc_attr('manage-folders-plan' == $active_tab ? 'active' : '') ?>"><?php echo esc_html($menu_title) ?></a>
                    <?php } ?>

                </div>
            </div>
            <div class="flex-1 lg:border-l lg:border-grey200 lg:pl-5 rtl:pl-0 rtl:pr-5 rtl:lg:border-0 rtl:lg:border-r">
                <?php
                do_action('folders_settings_tab');
                do_action('folders_customization_tab');
                do_action('folders_users_tab');
                do_action('folders_notifications_tab');
                do_action('folders_maintenance_tab');
                ?>
            </div>
        </div>
    </div>
    <?php do_action('folders_settings_modal'); ?>
</div>
