<?php
defined('ABSPATH') || die;

if (isset($_GET['view']) && $_GET['view'] === 'profile') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- view only
    $this->loadView('profile');
    return false;
}

wp_enqueue_style(
    'advgb_profiles_styles',
    plugins_url('assets/css/profiles.css', ADVANCED_GUTENBERG_PLUGIN),
    array(),
    ADVANCED_GUTENBERG_VERSION
);
wp_enqueue_script(
    'advgb_profiles_js',
    plugins_url('assets/js/profiles.js', ADVANCED_GUTENBERG_PLUGIN),
    array(),
    ADVANCED_GUTENBERG_VERSION
);

$args     = array(
    'post_type' => 'advgb_profiles',
    'publish'   => true,
    'orderby'   => 'date',
    'order'     => 'desc'
);
$profiles = get_posts($args);
wp_nonce_field('advgb_profiles_nonce', 'advgb_profiles_nonce');
?>

<div class="advgb-header" style="padding-top: 40px">
    <h1 class="header-title"><?php esc_html_e('Block Access Profiles', 'advanced-gutenberg') ?></h1>
</div>
<div class="profiles-list-wrapper">
    <div class="profiles-action-btn" style="float: left; margin: 25px auto">
        <a class="button pp-primary-button" id="new-profile"
           href="<?php echo esc_attr(admin_url('admin.php?page=advgb_main&view=profile&id=new')) ?>"
        >
            <i class="dashicons dashicons-plus"></i>
            <span><?php esc_html_e('New Profile', 'advanced-gutenberg') ?></span>
        </a> <button type="button" id="delete-selected-profiles" class="button pp-default-button">
            <?php esc_html_e('Delete', 'advanced-gutenberg') ?>
        </button>
    </div>
    <div class="advgb-search-wrapper" style="float: right; width: 300px">
        <input type="text" class="profiles-search-input advgb-search-input"
               placeholder="<?php esc_html_e('Search profiles by title', 'advanced-gutenberg') ?>"
        >
        <i class="mi mi-search"></i>
    </div>
    <table id="profiles-list">
        <thead>
            <tr>
                <th class="profile-header-checkbox select-box">
                    <input type="checkbox" class="select-all-profiles ju-checkbox">
                </th>
                <th class="profile-header-title sorting-header" data-sort="title">
                    <span>
                        <span><?php esc_html_e('Title', 'advanced-gutenberg') ?></span>
                        <i class="dashicons"></i>
                    </span>
                </th>
                <th class="profile-header-roles sorting-header desc" data-sort="roles">
                    <span>
                        <span><?php esc_html_e('Roles', 'advanced-gutenberg') ?></span>
                        <i class="dashicons"></i>
                    </span>
                </th>
                <th class="profile-header-users sorting-header" data-sort="users">
                    <span>
                        <span><?php esc_html_e('Users', 'advanced-gutenberg') ?></span>
                        <i class="dashicons"></i>
                    </span>
                </th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($profiles) > 0) : ?>
            <?php foreach ($profiles as $profile) : ?>
                <tr class="advgb-profile" data-profile-id="<?php echo esc_html($profile->ID) ?>">
                    <td class="profile-checkbox select-box">
                        <input type="checkbox" class="ju-checkbox" name="advgb_profile[]" value="<?php echo esc_html($profile->ID) ?>">
                    </td>
                    <td class="profile-title">
                        <a href="<?php echo esc_html(admin_url('admin.php?page=advgb_main&view=profile&id='.$profile->ID)) ?>">
                            <?php echo esc_html($profile->post_title ? $profile->post_title : __('(untitled)', 'advanced-gutenberg')) ?>
                        </a>
                        <i class="mi mi-delete-forever profile-delete"
                           title="<?php esc_attr_e('Delete', 'advanced-gutenberg') ?>"
                           data-profile-id="<?php echo esc_html($profile->ID) ?>">
                        </i>
                    </td>
                    <td class="profile-roles">
                        <?php
                        global $wp_roles;
                        $roles_access   = get_post_meta($profile->ID, 'roles_access', true);
                        $roles_realname = [];
                        if( !empty($roles_access) ) {
                            foreach($roles_access as $role_access) {
                                $roles_realname[] = translate_user_role( $wp_roles->roles[$role_access]['name'] );
                            }
                            echo implode( ', ', $roles_realname);
                        }
                        ?>
                    </td>
                    <td class="profile-users">
                        <?php
                        $users_access   = get_post_meta( $profile->ID, 'users_access', true );
                        $users_realname = [];
                        if( !empty($users_access) ) {
                            foreach($users_access as $user_id) {
                                $users_realname[] = get_userdata($user_id)->display_name;
                            }
                            echo implode( ', ', $users_realname);
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="4" class="advgb-no-profiles">
                    <?php
                    echo sprintf(
                        __('No profiles found. %sCreate a new profile%s', 'advanced-gutenberg'),
                        '<a href="' . esc_attr(admin_url('admin.php?page=advgb_main&view=profile&id=new')) . '">',
                        '</a>'
                    );
                    ?>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th class="profile-header-checkbox select-box">
                    <input type="checkbox" class="select-all-profiles ju-checkbox">
                </th>
                <th class="profile-header-title sorting-header" data-sort="title">
                    <span>
                        <span><?php esc_html_e('Title', 'advanced-gutenberg') ?></span>
                        <i class="dashicons"></i>
                    </span>
                </th>
                <th class="profile-header-roles sorting-header desc" data-sort="roles">
                    <span>
                        <span><?php esc_html_e('Roles', 'advanced-gutenberg') ?></span>
                        <i class="dashicons"></i>
                    </span>
                </th>
                <th class="profile-header-users sorting-header" data-sort="users">
                    <span>
                        <span><?php esc_html_e('Users', 'advanced-gutenberg') ?></span>
                        <i class="dashicons"></i>
                    </span>
                </th>
            </tr>
        </tfoot>
    </table>
</div>
