<div class="tab-content <?php echo esc_attr(($setting_page == "folders-by-user") ? "active" : "") ?>" id="folders-by-user">
    <?php
    $folders_by_users = !isset($customize_folders['folders_by_users']) ? "off" : $customize_folders['folders_by_users'];
    $dynamic_folders_for_admin_only = !isset($customize_folders['dynamic_folders_for_admin_only']) ? "off" : $customize_folders['dynamic_folders_for_admin_only'];
    ?>
    <?php if ($setting_page == "folders-by-user") {
        include "folder-user-settings.php";
    }//end if
    ?>
</div>