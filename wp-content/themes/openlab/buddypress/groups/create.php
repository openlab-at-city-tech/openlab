<?php /**
 * Create a group
 *
 */ ?>
<div class="col-sm-9">

    <?php
    // re-direct to courses page if user does not have permissions for course creation page
    $account_type = xprofile_get_field_data('Account Type', get_current_user_id());
    $group_type = isset($_GET['type']) ? $_GET['type'] : 'club';
    if ('course' === $group_type && !is_super_admin() && $account_type != "Faculty") {
        wp_redirect(home_url('courses'));
    }

    global $bp;

    //get group type
    if (!empty($_GET['type'])) {
        $group_type = $_GET['type'];
    } else {
        $group_type = 'club';
    }

    //this function doesn't work - explore for deprecation or fixing
    /* $group_type = openlab_get_current_group_type(); */

    // Set a group label. The (e)Portfolio logic means we have to do an extra step
    if ('portfolio' == $group_type) {
        $group_label = openlab_get_portfolio_label('case=upper&user_id=' . bp_loggedin_user_id());
        $page_title = 'Create ' . openlab_get_portfolio_label('case=upper&leading_a=1&user_id=' . bp_loggedin_user_id());
    } else {
        $group_label = $group_type;
        $page_title = 'Create a ' . ucwords($group_type);
    }

    $group_id_to_clone = 0;
    if ('course' === $group_type && !empty($_GET['clone'])) {
        $group_id_to_clone = intval($_GET['clone']);
    }
    ?>
    <h1 class="entry-title mol-title"><?php bp_loggedin_user_fullname() ?>'s Profile</h1>
    <?php
    // get account type to see if they're faculty
    $faculty = xprofile_get_field_data('Account Type', get_current_user_id());
    ?>

    <div class="submenu">
        <?php echo openlab_my_groups_submenu($group_type); ?>
    </div>

    <div id="single-course-body" class="<?php echo ( 'course' == $group_type ? 'course-create' : '' ); ?>">

        <form action="<?php bp_group_creation_form_action() ?>" method="post" id="create-group-form" class="standard-form" enctype="multipart/form-data">

            <?php do_action('bp_before_create_group') ?>

            <?php do_action('template_notices') ?>

            <div class="item-body" id="group-create-body">

                <?php /* Group creation step 1: Basic group details */ ?>
                <?php if (bp_is_group_creation_step('group-details')) : ?>

                    <?php do_action('bp_before_group_details_creation_step'); ?>

                    <?php /* Create vs Clone for Courses */ ?>
                    <?php if ('course' == $group_type) : ?>
                        <div class="create-or-clone-selector">
                            <p id="create-or-clone-head">Create New or Clone Existing?</p>
                            <p class="ol-tooltip clone-course-tooltip" id="clone-course-tooltip-2">If you taught the same course in a previous semester or year, cloning can save you time.</p>

                            <ul class="create-or-clone-options">
                                <li>
                                    <input type="radio" name="create-or-clone" id="create-or-clone-create" value="create" <?php checked(!(bool) $group_id_to_clone) ?> /> <label for="create-or-clone-create">Create a New Course</label>
                                </li>

                                <?php
                                //this is to see if the user has an courses under My Courses - if not, the Clone an Existing Course option is disabled
                                $filters['wds_group_type'] = $group_type;
                                $group_args = array(
                                    'per_page' => 12,
                                    'show_hidden' => true,
                                    'user_id' => $bp->loggedin_user->id
                                );

                                $course_num = openlab_group_post_count($filters, $group_args);
                                ?>

                                <li class="disable-if-js">
                                    <input type="radio" name="create-or-clone" id="create-or-clone-clone" value="clone" <?php checked((bool) $group_id_to_clone) ?> <?php echo ($course_num < 1 ? 'disabled' : ''); ?> /> <label for="create-or-clone-clone" <?php echo ($course_num < 1 ? 'class="disabled-opt"' : ''); ?>>Clone an Existing Course</label>

                                    <?php $user_groups = openlab_get_courses_owned_by_user(get_current_user_id()) ?>

                                    <select id="group-to-clone" name="group-to-clone">
                                        <option value="" <?php selected($group_id_to_clone, 0) ?>>- choose a course -</option>

                                        <?php foreach ($user_groups['groups'] as $user_group) : ?>
                                            <option value="<?php echo esc_attr($user_group->id) ?>" <?php selected($group_id_to_clone, $user_group->id) ?>><?php echo esc_attr($user_group->name) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </li>
                            </ul>

                            <p class="ol-clone-description" id="ol-clone-description">Note: The cloned course will copy the course profile, site set-up, and all documents, files, discussions and posts you've created. Posts will be set to "draft" mode. The cloned course will not copy course membership or member-created documents, files, discussions, comments or posts.</p>
                        </div>

                    <?php endif ?>

                    <?php /* Name/Description */ ?>
                    <?php if ('course' == $group_type) : ?>
                        <label for="group-name"><?php echo ucfirst($group_type); ?> Name <?php _e('(required)', 'buddypress') ?></label>
                        <p class="ol-tooltip clone-course-tooltip" id="clone-course-tooltip-4">Please take a moment to consider the name of your new or cloned Course. We recommend keeping your Course Name under 50 characters. You can always change it later. We recommend the following format:</p>
                        <ul class="ol-tooltip" id="clone-course-tooltip-3">
                            <li>CourseCode CourseName, Semester Year</li>
                            <li>ARCH3522 NYC Arch, FA2013</li>
                        </ul>

                        <input size="80" type="text" name="group-name" id="group-name" value="<?php bp_new_group_name() ?>" placeholder="Course Name" />
                        <label for="group-desc"><?php echo ucfirst($group_type); ?> Description <?php _e('(required)', 'buddypress') ?></label>

                    <?php elseif ('portfolio' == $group_type) : ?>
                        <p class="ol-tooltip">The suggested <?php echo $group_label ?> Name below uses your first and last name. If you do not wish to use your full name, you may change it now or at any time in the future.</p>

                        <ul class="ol-tooltip">
                            <li>FirstName LastName's <?php echo $group_label ?> </li>
                            <li>Jane Smith's <?php echo $group_label ?> (Example)</li>
                        </ul>

                        <label for="group-name"><?php echo ucfirst($group_type); ?> Name <?php _e('(required)', 'buddypress') ?></label>
                        <input size="80" type="text" name="group-name" id="group-name" value="<?php bp_new_group_name() ?>" />
                        <label for="group-desc"><?php echo ucfirst($group_type); ?> Description <?php _e('(required)', 'buddypress') ?></label>

                    <?php else : ?>
                        <label for="group-name"><?php echo ucfirst($group_type); ?> Name <?php _e('(required)', 'buddypress') ?></label>
                        <p class="ol-tooltip">Please take a moment to consider the name of your <?php echo ucwords($group_type) ?>.  Choosing a name that clearly identifies your  <?php echo ucwords($group_type) ?> will make it easier for others to find your <?php echo ucwords($group_type) ?> profile. We recommend keeping your  <?php echo ucwords($group_type) ?> name under 50 characters.</p>
                        <input size="80" type="text" name="group-name" id="group-name" value="<?php bp_new_group_name() ?>" />
                        <label for="group-desc"><?php echo ucfirst($group_type); ?> Description <?php _e('(required)', 'buddypress') ?></label>

                    <?php endif ?>

                    <textarea name="group-desc" id="group-desc"><?php bp_new_group_description() ?></textarea>

                    <?php do_action('bp_after_group_details_creation_step') ?>

                    <?php wp_nonce_field('groups_create_save_group-details') ?>

                <?php endif; ?>

                <?php /* Group creation step 2: Group settings */ ?>
                <?php if (bp_is_group_creation_step('group-settings')) : ?>

                    <?php do_action('bp_before_group_settings_creation_step'); ?>

                    <?php
                    /* Don't show Discussion toggle for portfolios */
                    /* Changed this to hidden in case this value is needed */
                    ?>
                    <?php if (!openlab_is_portfolio() && function_exists('bp_forums_is_installed_correctly')) : ?>
        <?php if (bp_forums_is_installed_correctly()) : ?>
                            <div class="checkbox">
                                <label><input type="hidden" name="group-show-forum" id="group-show-forum" value="1"<?php if (bp_get_new_group_enable_forum()) { ?> checked="checked"<?php } ?> /></label>
                            </div>
                        <?php else : ?>
            <?php if (is_super_admin()) : ?>
                                <div class="checkbox">
                                    <label><input type="hidden" disabled="disabled" name="disabled" id="disabled" value="0" /> <?php printf(__('<strong>Attention Site Admin:</strong> ' . $group_type . ' forums require the <a href="%s">correct setup and configuration</a> of a bbPress installation.', 'buddypress'), bp_get_root_domain() . '/wp-admin/admin.php?page=bb-forums-setup') ?></label>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
    <?php endif; ?>

                    <hr />

                    <?php openlab_group_privacy_settings($group_type); ?>

                <?php endif; ?>

                <?php /* Group creation step 3: Avatar Uploads */ ?>

                <?php if (bp_is_group_creation_step('group-avatar')) : ?>

                    <?php do_action('bp_before_group_avatar_creation_step'); ?>

    <?php if (!bp_get_avatar_admin_step() || 'upload-image' == bp_get_avatar_admin_step()) : ?>
                        <h4>Upload Avatar</h4>

                        <div class="left-menu">
        <?php bp_new_group_avatar() ?>
                        </div><!-- .left-menu -->

                        <div class="main-column">
                            <p><?php _e("Upload an image to use as an avatar for this " . $group_type . ". The image will be shown on the main " . $group_type . " page, and in search results.", 'buddypress') ?></p>

                            <p>
                                <input type="file" name="file" id="file" />
                                <input type="submit" name="upload" id="upload" value="<?php _e('Upload Image', 'buddypress') ?>" />
                                <input type="hidden" name="action" id="action" value="bp_avatar_upload" />
                            </p>

                            <p>To skip the avatar upload process, click the "Next Step" button.</p>
                        </div><!-- .main-column -->

                    <?php endif; ?>

    <?php if ('crop-image' == bp_get_avatar_admin_step()) : ?>

                        <h4><?php _e('Crop Avatar', 'buddypress') ?></h4>

                        <img src="<?php bp_avatar_to_crop() ?>" id="avatar-to-crop" class="avatar" alt="<?php _e('Avatar to crop', 'buddypress') ?>" />

                        <div id="avatar-crop-pane">
                            <img src="<?php bp_avatar_to_crop() ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e('Avatar preview', 'buddypress') ?>" />
                        </div>

                        <input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e('Crop Image', 'buddypress') ?>" />

                        <input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src() ?>" />
                        <input type="hidden" name="upload" id="upload" />
                        <input type="hidden" id="x" name="x" />
                        <input type="hidden" id="y" name="y" />
                        <input type="hidden" id="w" name="w" />
                        <input type="hidden" id="h" name="h" />

                    <?php endif; ?>

                    <?php do_action('bp_after_group_avatar_creation_step'); ?>

                    <?php wp_nonce_field('groups_create_save_group-avatar') ?>

                <?php endif; ?>

                <?php /* Group creation step 4: Invite friends to group */ ?>
                <?php if (bp_is_group_creation_step('group-invites')) : ?>

                    <?php do_action('bp_before_group_invites_creation_step'); ?>

    <?php if (function_exists('bp_get_total_friend_count') && bp_get_total_friend_count(bp_loggedin_user_id())) : ?>
                        <div class="left-menu">
                            <div id="invite-list">
                                <ul>
        <?php bp_new_group_invite_friend_list() ?>
                                </ul>

        <?php wp_nonce_field('groups_invite_uninvite_user', '_wpnonce_invite_uninvite_user') ?>
                            </div>
                        </div><!-- .left-menu -->

                        <div class="main-column">

                            <div id="message" class="info">
                                <p><?php _e('Select people to invite from your friends list.', 'buddypress'); ?></p>
                            </div>

                                <?php /* The ID 'friend-list' is important for AJAX support. */ ?>
                            <ul id="friend-list" class="item-list">
                                <?php if (bp_group_has_invites()) : ?>
            <?php while (bp_group_invites()) : bp_group_the_invite(); ?>

                                        <li id="<?php bp_group_invite_item_id() ?>">
                <?php bp_group_invite_user_avatar() ?>

                                            <h4><?php bp_group_invite_user_link() ?></h4>
                                            <span class="activity"><?php bp_group_invite_user_last_active() ?></span>

                                            <div class="action">
                                                <a class="remove" href="<?php bp_group_invite_user_remove_invite_url() ?>" id="<?php bp_group_invite_item_id() ?>"><?php _e('Remove Invite', 'buddypress') ?></a>
                                            </div>
                                        </li>

                                    <?php endwhile; ?>

                                    <?php wp_nonce_field('groups_send_invites', '_wpnonce_send_invites') ?>
        <?php endif; ?>
                            </ul>

                        </div><!-- .main-column -->

    <?php else : ?>
                        <div id="message" class="info">
                            <p><?php _e('Once you have built up friend connections you will be able to invite others to your ' . $group_type . '. You can send invites any time in the future by selecting the "Send Invites" option when viewing your new ' . $group_type . '.', 'buddypress'); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php wp_nonce_field('groups_create_save_group-invites') ?>
                    <?php do_action('bp_after_group_invites_creation_step'); ?>

                <?php endif; ?>

                <?php do_action('groups_custom_create_steps') // Allow plugins to add custom group creation steps  ?>

                <?php do_action('bp_before_group_creation_step_buttons'); ?>

                    <?php if ('crop-image' != bp_get_avatar_admin_step()) : ?>
                    <div class="submit" id="previous-next">
                        <?php /* Previous Button */ ?>
                        <?php if (!bp_is_first_group_creation_step()) : ?>
                            <input type="button" value="&larr; <?php _e('Previous Step', 'buddypress') ?>" id="group-creation-previous" name="previous" onclick="location.href = '<?php bp_group_creation_previous_link() ?>'" />
                        <?php endif; ?>

                        <?php /* Next Button */ ?>
                        <?php if (!bp_is_last_group_creation_step() && !bp_is_first_group_creation_step()) : ?>
                            <input type="submit" value="<?php _e('Next Step', 'buddypress') ?> &rarr;" id="group-creation-next" name="save" />
                        <?php endif; ?>

                        <?php /* Create Button */ ?>
                        <?php if (bp_is_first_group_creation_step()) : ?>
                            <input type="submit" value="<?php _e('Create ' . ucfirst($group_type) . ' and Continue', 'buddypress') ?> &rarr;" id="group-creation-create" name="save" />
                        <?php endif; ?>

                        <?php /* Finish Button */ ?>
                        <?php if (bp_is_last_group_creation_step()) : ?>
                            <input type="submit" value="<?php _e('Finish', 'buddypress') ?> &rarr;" id="group-creation-finish" name="save" />
                    <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php do_action('bp_after_group_creation_step_buttons'); ?>

<?php /* Don't leave out this hidden field */ ?>
                <input type="hidden" name="group_id" id="group_id" value="<?php bp_new_group_id() ?>" />

<?php do_action('bp_directory_groups_content') ?>

            </div><!-- .item-body -->

<?php do_action('bp_after_create_group') ?>

        </form>
    </div>
</div>
<div id="sidebar" class="sidebar widget-area col-sm-3">
    <?php bp_get_template_part('members/single/sidebar'); ?>
</div>