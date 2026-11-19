<?php
/**
 * Folder user access
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="folders-by-user">
    <div class="send-user-to-pro">
        <div class="normal-box">
            <table class="import-export-table">
                <tr>
                    <td>
                        <span class="danger-info"><?php esc_html_e("Restrict users to their folders only", "folders"); ?></span>
                        <span class="danger-data"><?php esc_html_e("Users will only be able to access their folders and media. Only Admin users will be able to view all folders", "folders"); ?>
                    </td>
                    <td class="last-td" >
                        <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                            <span>
                                <label class="folder-switch send-user-to-pro" for="dynamic_folders_for_admin_only">
                                    <input type="hidden">
                                    <div class="folder-slider round"></div>
                                </label>
                            </span>
                            <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", 'folders') ?></button>
                        </a>
                    </td>
                </tr>
            </table>
        </div>
        <a class="upgrade-box" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
            <button type="button"><?php esc_html_e("Upgrade to Pro", 'folders'); ?></button>
        </a>
    </div>
    <div class="send-user-to-pro">
        <div class="normal-box">
            <table class="import-export-table">
                <tr>
                    <td>
                        <span class="danger-info"><?php esc_html_e("Restrict access to dynamic folders", "folders"); ?></span>
                        <span class="danger-data"><?php esc_html_e("Regular users will not access dynamic folders.", "folders"); ?></span>
                        <span class="danger-data"><?php esc_html_e("Only Admin users will be able to view dynamic folders.", "folders"); ?></span>
                    </td>
                    <td class="last-td" >
                        <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                            <span>
                                <label class="folder-switch send-user-to-pro" for="folders_by_users">
                                    <input type="hidden">
                                    <div class="folder-slider round"></div>
                                </label>
                            </span>
                            <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", 'folders') ?></button>
                        </a>
                    </td>
                </tr>
            </table>
        </div>
        <a class="upgrade-box" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
            <button type="button"><?php esc_html_e("Upgrade to Pro", 'folders'); ?></button>
        </a>
    </div>
    <?php
    $user = wp_get_current_user();
    $roles = ( array ) $user->roles;
    if(!empty($roles) && in_array('administrator', $roles)) { ?>
    <div class="normal-box">
        <table class="import-export-table">
            <tr>
                <td>
                    <div class="folder-access">
                        <?php esc_html_e("Folders Access Management", "folders"); ?>
                        <div class="folder-html-tooltip">
                            <div class="html-tooltip-text">
                                <p><?php esc_html_e("Give specific users or general WordPress user roles granular access to Folders:", "folders") ?></p>
                                <ol>
                                    <li><?php esc_html_e("Admin: Full access to modifying, creating and viewing folders", "folders") ?></li>
                                    <li><?php esc_html_e("View & Edit: These users can only update and view currently existing folders", "folders") ?></li>
                                    <li><?php esc_html_e("View only: These users can only view the folders", "folders") ?></li>
                                    <li><?php esc_html_e("No Access: These users have no access to folders", "folders") ?></li>
                                </ol>
                            </div>
                            <span class="dashicons dashicons-editor-help"></span>
                        </div>
                        <a class="user-upgrade-inline-btn" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" target="_blank"><?php esc_html_e("Upgrade to Pro", "folders"); ?></a>
                    </div>
                    <span class="danger-data">
                        <?php esc_html_e("Set Folders permissions for user roles and specific users.", "folders"); ?>
                    </span>
                </td>
                <td class="pos-relative">
                    <div class="inline-checkbox">

                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="folder-user-settings active">
        <div class="user-role-tab">
            <ul>
                <li class="active"><a href="#settings-roles"><?php esc_html_e("Roles", "folders") ?></a></li>
                <li><a href="#settings-users"><?php esc_html_e("Users", "folders") ?></a></li>
            </ul>
        </div>
        <div class="folders-by-user">
            <div class="user-role-setting active" id="settings-roles">
                <table class="import-export-table">
                    <tr>
                        <td>
                            <?php
                            global $wp_roles;
                            $folderRoles = [
                                'admin' => esc_html__("Admin", "folders"),
                                'view-edit' => esc_html__("View & Edit", "folders"),
                                'view-only' => esc_html__("View Only", "folders"),
                                'no-access' => esc_html__("No Access", "folders"),
                            ];
                            if(isset($wp_roles->roles) && count($wp_roles->roles)) {
                                $allRoles = $wp_roles->roles;
                                ?>
                                <div class="role-setting-search">
                                    <input type="search" id="role-search" placeholder="<?php esc_html_e("Search by user roles", "folders"); ?>" />
                                    <button type="button">
                                        <svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.18065 0.72168C11.14 0.72168 14.3606 3.94225 14.3606 7.9016C14.3606 9.7696 13.6437 11.4733 12.4706 12.752L14.7789 15.0555C14.995 15.2715 14.9957 15.621 14.7797 15.837C14.672 15.9462 14.5297 16 14.3881 16C14.2473 16 14.1058 15.9462 13.9974 15.8385L11.6612 13.5088C10.4322 14.493 8.87401 15.0822 7.18065 15.0822C3.2213 15.0822 0 11.8609 0 7.9016C0 3.94225 3.2213 0.72168 7.18065 0.72168ZM7.18065 1.82764C3.83106 1.82764 1.10596 4.552 1.10596 7.9016C1.10596 11.2512 3.83106 13.9763 7.18065 13.9763C10.5295 13.9763 13.2546 11.2512 13.2546 7.9016C13.2546 4.552 10.5295 1.82764 7.18065 1.82764Z" fill="#ABABAB"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="role-settings default">
                                    <div class="role-setting-left"><?php esc_html_e("Roles", "folders"); ?></div>
                                    <div class="role-setting-right"><?php esc_html_e("Permissions", "folders"); ?></div>
                                </div>
                                <?php
                                $roleSettings = get_option("folders_role_access_settings");
                                $roleSettings = is_array($roleSettings)?$roleSettings:[];
                                foreach($allRoles as $key=>$role) {
                                    $defaultRole = "no-access";
                                    if(isset($role['capabilities']['manage_categories']) && $role['capabilities']['manage_categories']) {
                                        $defaultRole = "admin";
                                    } else if((isset($role['capabilities']['edit_posts']) && $role['capabilities']['edit_posts']) || (isset($role['capabilities']['edit_pages']) && $role['capabilities']['edit_pages'])) {
                                        $defaultRole = "view-edit";
                                    } else if(isset($role['capabilities']['upload_files']) && $role['capabilities']['upload_files']) {
                                        $defaultRole = "view-only";
                                    }
                                    if(isset($roleSettings[$key])) {
                                        $currentRole = $roleSettings[$key];
                                    } else {
                                        $currentRole = $defaultRole;
                                    }
                                    ?>
                                    <div class="role-settings active" data-role="<?php echo esc_attr($key) ?>" data-nonce="<?php echo esc_attr(wp_create_nonce("change_folders_role_".$key)) ?>" >
                                        <div class="role-setting-left">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12.5708 2.09956L19.2082 4.32652C19.9512 4.57461 20.4535 5.25711 20.4575 6.02198L20.4998 12.6626C20.5129 14.6758 19.779 16.6282 18.435 18.1579C17.8169 18.8601 17.0246 19.4631 16.0128 20.0025L12.4449 21.9097C12.3332 21.9686 12.2103 21.999 12.0865 22C11.9627 22.0009 11.8389 21.9715 11.7281 21.9137L8.12702 20.0505C7.10417 19.52 6.30482 18.9258 5.68064 18.2335C4.3145 16.7194 3.55542 14.7758 3.54233 12.7597L3.50002 6.12397C3.49602 5.35811 3.98932 4.67071 4.72827 4.41281L11.3405 2.10643C11.7332 1.96718 12.1711 1.96424 12.5708 2.09956Z" fill="#E6386C"/>
                                                <path d="M12.1255 12.4737C14.2948 12.4737 16.1255 12.8262 16.1255 14.1862C16.1255 15.5467 14.2828 15.8867 12.1255 15.8867C9.95667 15.8867 8.12549 15.5342 8.12549 14.1742C8.12549 12.8137 9.96818 12.4737 12.1255 12.4737ZM12.1255 5.88672C13.595 5.88672 14.7725 7.06373 14.7725 8.53225C14.7725 10.0008 13.595 11.1783 12.1255 11.1783C10.6564 11.1783 9.4785 10.0008 9.4785 8.53225C9.4785 7.06373 10.6564 5.88672 12.1255 5.88672Z" fill="white"/>
                                            </svg>
                                            <span class="role-title"><?php echo esc_attr($role['name']) ?></span>
                                        </div>
                                        <div class="role-setting-right">
                                            <?php if($key != "administrator") { ?>
                                                <div class="user-folder-access">
                                                    <div class="access-title">
                                                        <span class="access-role-title"><?php echo esc_attr($folderRoles[$currentRole]) ?></span>
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M4 6L8 10L12 6" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </div>
                                                    <div class="user-access-list">
                                                        <ul>
                                                            <?php foreach($folderRoles as $key=>$role_type) { ?>
                                                                <li data-role="<?php echo esc_attr($key) ?>" class="change-folders-role-access <?php echo esc_attr(($key == $currentRole)?"active":"")  ?>">
                                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M13.3332 4L5.99984 11.3333L2.6665 8" stroke="#E6386C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    </svg>
                                                                    <span><?php echo esc_attr($role_type) ?></span>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <div class="access-title">
                                                    <span class="access-role-title"><?php echo esc_attr($folderRoles[$currentRole]) ?></span>
                                                    <span class="dashicons dashicons-lock"></span>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="pro-feature-popup">
            <div class="pro-feature-content">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.0002 2.66663C12.3335 2.66663 9.3335 5.66663 9.3335 9.33329V12H12.0002V9.33329C12.0002 7.13329 13.8002 5.33329 16.0002 5.33329C18.2002 5.33329 20.0002 7.13329 20.0002 9.33329V12H22.6668V9.33329C22.6668 5.66663 19.6668 2.66663 16.0002 2.66663Z" fill="#424242"/>
                    <path d="M24.0002 29.3333H8.00016C6.5335 29.3333 5.3335 28.1333 5.3335 26.6667V14.6667C5.3335 13.2 6.5335 12 8.00016 12H24.0002C25.4668 12 26.6668 13.2 26.6668 14.6667V26.6667C26.6668 28.1333 25.4668 29.3333 24.0002 29.3333Z" fill="#FB8C00"/>
                    <path d="M16 22.6666C17.1046 22.6666 18 21.7712 18 20.6666C18 19.5621 17.1046 18.6666 16 18.6666C14.8954 18.6666 14 19.5621 14 20.6666C14 21.7712 14.8954 22.6666 16 22.6666Z" fill="#C76E00"/>
                </svg>
                <div class="pro-user-title"><?php esc_html_e("Give Access To Your Teammates", "folders") ?></div>
                <div class="pro-user-desc"><?php esc_html_e("Upgrade to Pro to have granular access management, dynamic folders, subfolders and more", "folders") ?></div>
                <a href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" target="_blank">
                    <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.9998 3.75C17.9998 4.44031 17.4401 5 16.7498 5C16.7421 5 16.7356 4.99603 16.7278 4.99594L15.1491 13.6803C15.0623 14.1531 14.6498 14.5 14.1654 14.5H3.83418C3.35105 14.5 2.93668 14.1544 2.85043 13.6791L1.27199 4.99688C1.26418 4.99688 1.25762 5 1.22168 5C0.531367 5 -0.0283203 4.44031 -0.0283203 3.75C-0.0283203 3.05969 0.559492 2.5 1.22168 2.5C1.88387 2.5 2.47168 3.05969 2.47168 3.75C2.47168 4.03119 2.36165 4.27781 2.2049 4.48656L5.00584 6.72719C5.50302 7.125 6.24021 6.96294 6.5249 6.39344L8.3249 2.79344C7.97168 2.57313 7.72168 2.19813 7.72168 1.75C7.72168 1.05969 8.30918 0.5 8.9998 0.5C9.69043 0.5 10.2217 1.05969 10.2217 1.75C10.2217 2.19813 9.97284 2.57313 9.61855 2.79375L11.4186 6.39375C11.7033 6.96313 12.4407 7.125 12.9376 6.7275L15.7386 4.48688C15.6092 4.27813 15.4998 4.00313 15.4998 3.75C15.4998 3.05938 16.0592 2.5 16.7498 2.5C17.4404 2.5 17.9998 3.05938 17.9998 3.75Z" fill="white"/>
                    </svg>
                    <?php esc_html_e("Upgrade to Pro", "folders") ?>
                </a>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
