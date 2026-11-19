<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
$user_meta = get_userdata($user_id);
if(!isset($user_meta->roles)) {
    return;
}
$user_roles = $user_meta->roles;
$folder_access = get_user_meta($user_id, "folders_access_role", true);

$userRoles = $this->get_user_roles();
$folderRoles = apply_filters( 'folders_user_roles', []);
$folderFeatures= apply_filters( 'folders_user_features', []);
if ($folder_access === false || empty($folder_access) || !isset($userRoles[$folder_access])) {
    $folder_access = 'default';
}
$first_name = get_user_meta($user_id, 'first_name', true);
$last_name = get_user_meta($user_id, 'last_name', true);
$first_name = trim($first_name . " " . $last_name);
if (empty($first_name) && !empty($user->display_name)) {
    $first_name = $user->display_name;
}
$user_image_url = get_avatar_url($user_id);
$default_role = self::get_user_default_role($user_id);
?>
<div class="role-settings" data-role="<?php echo esc_attr(strtolower($folder_access)) ?>">
    <div class="flex gap-3 justify-between items-center py-3" data-role="<?php echo esc_attr(strtolower($folder_access)) ?>">
        <div class="flex items-start gap-2">
            <div>
                <?php if(!empty($user_image_url)) { ?>
                    <img class="w-6 h-6 rounded-full" src="<?php echo esc_url($user_image_url) ?>" alt="<?php echo esc_attr($first_name) ?>">
                <?php } else { ?>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.5708 2.09956L19.2082 4.32652C19.9512 4.57461 20.4535 5.25711 20.4575 6.02198L20.4998 12.6626C20.5129 14.6758 19.779 16.6282 18.435 18.1579C17.8169 18.8601 17.0246 19.4631 16.0128 20.0025L12.4449 21.9097C12.3332 21.9686 12.2103 21.999 12.0865 22C11.9627 22.0009 11.8389 21.9715 11.7281 21.9137L8.12702 20.0505C7.10417 19.52 6.30482 18.9258 5.68064 18.2335C4.3145 16.7194 3.55542 14.7758 3.54233 12.7597L3.50002 6.12397C3.49602 5.35811 3.98932 4.67071 4.72827 4.41281L11.3405 2.10643C11.7332 1.96718 12.1711 1.96424 12.5708 2.09956Z" fill="#CE136D"/>
                        <path d="M12.125 12.4737C14.2943 12.4737 16.125 12.8262 16.125 14.1862C16.125 15.5467 14.2823 15.8867 12.125 15.8867C9.95619 15.8867 8.125 15.5342 8.125 14.1742C8.125 12.8137 9.96769 12.4737 12.125 12.4737ZM12.125 5.88672C13.5946 5.88672 14.772 7.06373 14.772 8.53225C14.772 10.0008 13.5946 11.1783 12.125 11.1783C10.6559 11.1783 9.47801 10.0008 9.47801 8.53225C9.47801 7.06373 10.6559 5.88672 12.125 5.88672Z" fill="white"/>
                    </svg>
                <?php } ?>
            </div>
            <div class="flex flex-col gap-0.5">
                <div class="text-grey900 text-base font-medium">
                    <?php echo esc_html($first_name) ?>
                </div>
                <div class="text-grey500 text-sm" id="user-role-feature-<?php echo esc_attr($user_id) ?>">
                    <?php echo sprintf($folderFeatures[$folder_access], $first_name) ?>
                </div>
            </div>
        </div>
        <div class="relative group user-role-settings" id="user-menu-<?php echo esc_attr($user_id) ?>">
            <div class="hidden" id="button-text-<?php echo esc_attr($user_id) ?>"><?php echo sprintf(esc_html__("Update permission for %s", 'folders'), $first_name) ?></div>
            <?php if(in_array('administrator', $user_roles)) { ?>
                <div class="text-grey900 flex gap-1 text-base font-medium items-center">
                    <?php esc_html_e('Admin', 'folders') ?>
                    <svg class="w-4 h-4" width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 10V6C5 4.67392 5.52678 3.40215 6.46447 2.46447C7.40215 1.52678 8.67392 1 10 1C11.3261 1 12.5979 1.52678 13.5355 2.46447C14.4732 3.40215 15 4.67392 15 6V10M3 10H17C18.1046 10 19 10.8954 19 12V19C19 20.1046 18.1046 21 17 21H3C1.89543 21 1 20.1046 1 19V12C1 10.8954 1.89543 10 3 10Z" stroke="#717680" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            <?php } else { ?>
                <button type="button" class="user-role-menu text-grey900! flex gap-1 text-base! font-medium! items-center hover:text-primary! group">
                    <span class="user-role-text"><?php echo esc_html($folderRoles[$folder_access]) ?></span>
                    <svg class="w-5 h-5" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path class="group-hover:stroke-primary" d="M6 9L12 15L18 9" stroke="#717680" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="absolute top-full right-0 mt-2 w-48 py-1 px-1 bg-white rounded-md shadow-lg border border-grey200 opacity-0 invisible group-[.active]:opacity-100 group-[.active]:visible transition-opacity duration-200 z-50">
                    <?php
                    $count = 0;
                    foreach ($folderRoles as $role_key => $folder_role) {
                        $count++;
                        ?>
                        <div class="w-full py-px">
                            <button data-default-role="<?php echo esc_attr($default_role) ?>" data-role-id="<?php echo esc_attr($count) ?>" data-name="<?php echo esc_html($first_name) ?>" data-nonce="<?php echo wp_create_nonce('change-user-folder-access-'.$user_id) ?>" data-user-id="<?php echo esc_attr($user_id) ?>" data-role="<?php echo esc_attr($role_key) ?>" type="button" class="change-user-role flex rounded-md w-full items-center gap-1 py-1! px-1.5! group/btn text-sm! hover:bg-primary/4 hover:text-primary! [&.active]:text-primary! <?php echo ($folder_access == $role_key) ? 'active' : '' ?>">
                                <svg class="opacity-0 group-[.active]/btn:opacity-100" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.3332 4L5.99984 11.3333L2.6665 8" stroke="#E6386C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                <?php echo esc_html($folder_role) ?>
                            </button>
                        </div>
                        <?php if($role_key != 'no-access') { ?>
                            <div class="h-px w-full bg-grey200/50"></div>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="h-px bg-grey200 w-full"></div>
