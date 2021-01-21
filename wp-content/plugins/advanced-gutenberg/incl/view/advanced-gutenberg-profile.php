<?php
defined('ABSPATH') || die;

wp_enqueue_style('advgb_profile_style');
wp_enqueue_script('advgb_profile_js');
wp_localize_script('advgb_profile_js', 'advgb', array(
    'onProfileView' => true,
    'toProfilesList' => admin_url('admin.php?page=advgb_main&view=profiles'),
));

$all_blocks_list     = get_option('advgb_blocks_list');

$postid              = $_GET['id']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- view only
$post_title          = get_the_title($postid);

$roles_access_saved = get_post_meta($postid, 'roles_access', true);
if ($roles_access_saved === '') {
    $roles_access_saved = self::$default_roles_access;
}

$users_access_saved = get_post_meta($postid, 'users_access', true);
$users_access_saved = $users_access_saved ? $users_access_saved : array();

if ($postid === 'new') {
    $roles_access_saved = self::$default_roles_access;
    $users_access_saved = array();
}

$blockCategories = array();
if (function_exists('get_block_categories')) {
    $blockCategories = get_block_categories(get_post());
} elseif (function_exists('gutenberg_get_block_categories')) {
    $blockCategories = gutenberg_get_block_categories(get_post());
}

// In profile page we load gutenberg files to retrieve all blocks, including new ones
wp_enqueue_script('wp-blocks');
wp_enqueue_script('wp-element');
wp_enqueue_script('wp-data');
wp_enqueue_script('wp-components');
wp_enqueue_script('wp-block-library');
wp_enqueue_script('wp-editor');
wp_enqueue_script('wp-edit-post');
wp_enqueue_script('wp-plugins');
do_action('enqueue_block_editor_assets');
wp_enqueue_script('advgb_update_list');
wp_localize_script('advgb_update_list', 'advgbUpdate', array('onProfile' => true));
wp_add_inline_script(
    'wp-blocks',
    sprintf('wp.blocks.setCategories( %s );', wp_json_encode($blockCategories)),
    'after'
);
?>

<form method="post">
    <?php wp_nonce_field('advgb_nonce', 'advgb_nonce_field'); ?>
    <input type="hidden" name="advgb_profile_id" value="<?php echo esc_html($postid) ?>" />
    <div id="profiles-container">
        <!--Tabs-->
        <div class="ju-top-tabs-wrapper">
            <ul class="tabs ju-top-tabs">
                <li class="tab">
                    <a href="#blocks-list-tab" class="link-tab">
                        <?php esc_html_e('Blocks List', 'advanced-gutenberg') ?>
                    </a>
                </li>
                <li class="tab">
                    <a href="#users-tab" class="link-tab">
                        <?php esc_html_e('Profile Attribution', 'advanced-gutenberg') ?>
                    </a>
                </li>
            </ul>
        </div>

        <?php if (isset($_GET['save_profile'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display message, no action ?>
            <div class="ju-notice-msg ju-notice-success">
                <?php esc_html_e('Profile saved successfully!', 'advanced-gutenberg') ?>
                <a href="<?php echo esc_attr(admin_url('admin.php?page=advgb_main&view=profiles')) ?>" target="_self">
                    <?php esc_html_e('Return to profiles list', 'advanced-gutenberg') ?>
                </a>
                <i class="dashicons dashicons-dismiss ju-notice-close"></i>
            </div>
        <?php endif; ?>

        <div class="advgb-header profile-header">
            <h1 class="header-title"><?php esc_html_e('Edit Profile', 'advanced-gutenberg') ?></h1>
            <div class="inline-button-wrapper">
                <span id="block-update-notice">
                    <?php esc_html_e('Blocks list updated.', 'advanced-gutenberg') ?>
                </span>

                <a class="button pp-default-button"
                   href="<?php echo esc_attr(admin_url('admin.php?page=advgb_main&view=profile&id=new')) ?>"
                >
                    <i class="dashicons dashicons-plus"></i>
                    <span><?php esc_html_e('New Profile', 'advanced-gutenberg') ?></span>
                </a>

                <button class="button button-primary pp-primary-button save-profile-button"
                        type="submit"
                        name="advgb_profile_save"
                >
                    <span><?php esc_html_e('Save', 'advanced-gutenberg') ?></span>
                </button>
            </div>
        </div>

        <div class="profile-title">
            <h4><?php esc_html_e('Profile title', 'advanced-gutenberg') ?></h4>
            <div class="advgb-search-wrapper">
                <input type="text" class="profile-title-input advgb-search-input"
                       name="advgb_profile_title"
                       placeholder="<?php esc_html_e('Enter title here', 'advanced-gutenberg') ?>"
                       value="<?php echo esc_html($post_title) ?>"
                >
            </div>
        </div>

        <!--Blocks list tab-->
        <div id="blocks-list-tab" class="tab-content">
            <div class="advgb-search-wrapper">
                <input type="text" class="blocks-search-input advgb-search-input"
                       placeholder="<?php esc_html_e('Search blocks', 'advanced-gutenberg') ?>"
                >
                <i class="mi mi-search"></i>
            </div>

            <div class="blocks-section">
                <input type="hidden" name="blocks_list" id="blocks_list" />
            </div>
        </div>

        <!--Users access tab-->
        <div id="users-tab" class="tab-content">
            <div class="users-block-title clearfix">
                <h3><?php esc_html_e('Active this profile for this user(s)', 'advanced-gutenberg') ?>:</h3>
                <div class="advgb-users-search-box">
                    <input type="button"
                           name="advgb-clear-btn"
                           id="advgb-clear-btn"
                           class="ju-rect-button"
                           value="<?php esc_attr_e('Clear', 'advanced-gutenberg') ?>"/>
                    <select name="advgb-roles-filter" id="advgb-roles-filter">
                        <option value=""><?php esc_html_e('Use role filter', 'advanced-gutenberg') ?></option>
                        <?php
                        global $wp_roles;
                        $roles_list = $wp_roles->get_names();
                        foreach ($roles_list as $roles => $role_name) {
                            echo '<option value="' . esc_attr($roles) . '">' . esc_html($role_name) . '</option>';
                        }
                        ?>
                    </select>
                    <div class="users-search" style="display: inline-block;">
                        <input type="text"
                               id="user-search-input"
                               name="s"
                               placeholder="<?php esc_attr_e('Search users', 'advanced-gutenberg') ?>"
                               value=""/>
                        <i class="mi mi-search users-search-toggle" title="<?php esc_attr_e('Search', 'advanced-gutenberg') ?>"></i>
                    </div>
                </div>
            </div>
            <div class="users-block">
                <table class="widefat fixed" id="advgb-users-list">
                    <thead>
                    <tr>
                        <th scope="col" id="advgb-users-select-box" class="manage-col" width="5%">
                            <input type="hidden" id="advgb-users-checkall" name="select-user" value="">
                        </th>
                        <th scope="col" id="advgb-users-name" class="manage-col">
                            <span><?php esc_html_e('Name', 'advanced-gutenberg') ?></span>
                        </th>
                        <th scope="col" id="advgb-users-username" class="manage-col">
                            <span><?php esc_html_e('Username', 'advanced-gutenberg') ?></span>
                        </th>
                        <th scope="col" id="advgb-users-email" class="manage-col">
                            <span><?php esc_html_e('Email', 'advanced-gutenberg') ?></span>
                        </th>
                        <th scope="col" id="advgb-users-role" class="manage-col" width="15%">
                            <span><?php esc_html_e('Role', 'advanced-gutenberg') ?></span>
                        </th>
                    </tr>
                    </thead>

                    <tbody id="advgb-users-body">
                    <?php
                    $users_per_page = 20;
                    $pagenum        = isset($_REQUEST['paged']) ? absint($_REQUEST['paged']) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- View request, no action
                    $pagedd         = max(1, $pagenum);
                    $args           = array(
                        'number'  => $users_per_page,
                        'offset'  => ($pagedd - 1) * $users_per_page,
                        'include' => wp_get_users_with_no_role(),
                        'fields'  => 'all_with_meta'
                    );

                    // Query the user IDs for this page
                    $wp_user_search = get_users($args);
                    $total_users    = count(get_users());
                    $total_pages    = (int)ceil($total_users / $users_per_page);

                    if (count($wp_user_search)) {
                        foreach ($wp_user_search as $userid => $user_object) {
                            echo '<tr>';
                            echo '<td class="select-box">';
                            echo '<input class="ju-checkbox" type="checkbox" name="advgb-users[]" value="' . esc_html($userid) . '" />';
                            echo '</td>';
                            echo '<td class="name column-name">';
                            echo '<span>' . esc_html($user_object->display_name) . '</span>';
                            echo '</td>';
                            echo '<td class="username column-username">';
                            echo '<strong>' . esc_html($user_object->user_login) . '</strong>';
                            echo '</td>';
                            echo '<td class="email column-email">' . esc_html($user_object->user_email) . '</td>';

                            $role_list = array();
                            global $wp_roles;
                            foreach ($user_object->roles as $roles) {
                                if (isset($wp_roles->role_names[ $roles ])) {
                                    $role_list[ $roles ] = translate_user_role($wp_roles->role_names[ $roles ]);
                                }
                            }

                            if (empty($role_list)) {
                                $role_list['none'] = _x('None', 'no user roles', 'advanced-gutenberg');
                            }
                            $roles_list = implode(', ', $role_list);

                            echo '<td class="role column-role">' . esc_html($roles_list) . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="5"> ';
                        echo esc_html__('No users found.', 'advanced-gutenberg');
                        echo '</td></tr>';
                    }
                    ?>
                    </tbody>
                    <?php $list_users_access = implode(' ', $users_access_saved); ?>
                    <input type="hidden"
                           name="advgb-users-access-list"
                           id="advgb-users-access-list"
                           value="<?php echo esc_html($list_users_access) ?>"/>
                </table>
                <p id="pagination">
                    <?php
                    $doneLeft   = false;
                    $doneRight  = false;
                    $skipLeft   = false;
                    $skipRight  = false;
                    if ($total_pages > 1) {
                        for ($i = 1; $i <= $total_pages; $i ++) {
                            if ($i < $pagenum - 2) {
                                $skipLeft = true;
                            } elseif ($i > $pagenum + 2) {
                                $skipRight = true;
                            } else {
                                $skipLeft  = false;
                                $skipRight = false;
                            }
                            if ($i === 1) {
                                if ($pagenum === 1) {
                                    echo '<i class="dashicons dashicons-controls-skipback" id="first-page"></i>';
                                } else {
                                    echo '<a class="dashicons dashicons-controls-skipback" id="first-page"></a>';
                                }
                            }
                            if (!$skipLeft && !$skipRight) {
                                if ($i === $pagenum) {
                                    echo '<strong>' . esc_html($i) . '</strong>';
                                } else {
                                    echo '<a class="switch-page">' . esc_html($i) . '</a>';
                                }
                            } elseif ($skipLeft) {
                                if (!$doneLeft) {
                                    echo '<span>...</span>';
                                    $doneLeft = true;
                                }
                            } elseif ($skipRight) {
                                if (!$doneRight) {
                                    echo '<span>...</span>';
                                    $doneRight = true;
                                }
                            }
                            if ($i === $total_pages) {
                                if ($pagenum === $total_pages) {
                                    echo '<i class="dashicons dashicons-controls-skipforward" id="last-page"></i>';
                                } else {
                                    echo '<a class="dashicons dashicons-controls-skipforward" id="last-page" '
                                        .'title="' . esc_attr__('Last page', 'advanced-gutenberg') . '"></a>';
                                }
                            }
                        }
                    } ?>
                </p>
            </div> <!--end Users blocks-->

            <h3 style="margin: 45px 0 25px"><?php esc_html_e('Active this profile for this group(s)', 'advanced-gutenberg') ?>:</h3>
            <div class="advgb-groups-block">
                <ul class="advgb-groups-list clearfix">
                    <?php
                    $roles_list = $wp_roles->get_names();
                    foreach ($roles_list as $roles => $role_name) :?>
                        <li class="clearfix ju-settings-option">
                            <label for="<?php echo esc_attr($roles) ?>" class="ju-setting-label"
                                   style="vertical-align: middle;"><?php echo esc_html($role_name) ?></label>
                            <div class="ju-switch-button">
                                <label class="switch">
                                    <input type="checkbox" class="extra-btn"
                                           name="advgb-roles[]"
                                           id="<?php echo esc_attr($roles) ?>"
                                           value="<?php echo esc_attr($roles) ?>"
                                            <?php if (in_array($roles, $roles_access_saved)) :
                                                    echo 'checked';
                                            endif; ?>
                                    />
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!--Save button-->
        <button class="button button-primary pp-primary-button save-profile-button"
                type="submit"
                name="advgb_profile_save"
                style="margin-left: 10px"
        >
            <span><?php esc_html_e('Save', 'advanced-gutenberg') ?></span>
        </button>
    </div>
</form>