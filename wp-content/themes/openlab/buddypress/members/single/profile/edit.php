<?php do_action('bp_before_profile_edit_content') ?>

<?php
global $bp, $user_ID, $profile_template;
if (is_super_admin($user_ID)) {
    $pgroup = bp_get_current_profile_group_id();
    $account_type = bp_get_profile_field_data('field=Account Type&user_id=' . bp_displayed_user_id());
} else {
    $account_type = bp_get_profile_field_data('field=Account Type');
    $exclude_groups = openlab_get_exclude_groups_for_account_type($account_type);
}

$display_name = bp_get_profile_field_data('field=Name');

$profile_args = array();

if (isset($pgroup)) {
    $profile_args['profile_group_id'] = $pgroup;
}

if (isset($exclude_groups)) {
    $profile_args['exclude_groups'] = $exclude_groups;
}

$display_name_shown = isset($pgroup) && 1 == $pgroup;
$field_ids = array(1);
?>
<?php echo openlab_submenu_markup(); ?>



<form action="" method="post" id="profile-edit-form" class="standard-form form-panel">

    <?php if (bp_has_profile($profile_args)) : ?>

        <?php do_action('bp_before_profile_field_content') ?>

        <?php if (is_super_admin($user_ID)): ?>
            <ul class="button-nav">

                <?php bp_profile_group_tabs(); ?>

            </ul>
        <?php endif; ?>

        <div class="clear"></div>

        <div class="panel panel-default">
            <div class="panel-heading">Edit Profile</div>
            <div class="panel-body">

                <?php do_action('template_notices'); ?>

                <?php if (!$display_name_shown) { ?>
                    <div class="editfield field_1 field_name alt form-group">
                        <label for="field_1">Display Name (required)</label>
                        <input class="form-control" type="text" value="<?php echo $display_name; ?>" id="field_1" name="field_1">
                        <p class="description"></p>
                    </div>
                    <?php $display_name_shown = true ?>
                <?php } ?>

                <?php while (bp_profile_groups()) : bp_the_profile_group(); ?>

                    <?php while (bp_profile_fields()) : bp_the_profile_field(); ?>

                        <?php /* Add to the array for the field-ids input */ ?>
                        <?php $field_ids[] = bp_get_the_profile_field_id() ?>

                        <div<?php bp_field_css_class('editfield') ?>>

                            <?php if ( 'textbox' == bp_get_the_profile_field_type() || 'url' == bp_get_the_profile_field_type() ) : ?>
                                <?php if (bp_get_the_profile_field_name() == "Name") { ?>
                                    <label for="<?php bp_the_profile_field_input_name() ?>"><?php echo "Display Name"; ?> <?php if (bp_get_the_profile_field_is_required()) : ?><?php _e('(required)', 'buddypress') ?><?php endif; ?></label>
                                <?php }else { ?>
                                    <label for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if (bp_get_the_profile_field_is_required()) : ?><?php _e('(required)', 'buddypress') ?><?php endif; ?></label>
                                <?php } ?>

                                <input class="form-control" type="text" name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>" value="<?php bp_the_profile_field_edit_value() ?>" />

                            <?php endif; ?>

                            <?php if ('textarea' == bp_get_the_profile_field_type()) : ?>

                                <label for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if (bp_get_the_profile_field_is_required()) : ?><?php _e('(required)', 'buddypress') ?><?php endif; ?></label>
                                <textarea class="form-control" rows="5" cols="40" name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_edit_value() ?></textarea>

                            <?php endif; ?>

                            <?php
                            if ('selectbox' == bp_get_the_profile_field_type()) :
                                $style = "";
                                if (bp_get_the_profile_field_name() == "Account Type" && !is_super_admin($user_ID) || $account_type) {
                                    //$style="style='display:none;'";
                                }
                                ?>

                                <label <?php echo $style; ?> for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if (bp_get_the_profile_field_is_required()) : ?><?php _e('(required)', 'buddypress') ?><?php endif; ?></label>
                                <select class="form-control" <?php echo $style; ?> name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>">
                                    <?php bp_the_profile_field_options() ?>
                                </select>

                            <?php endif; ?>

                            <?php if ('multiselectbox' == bp_get_the_profile_field_type()) : ?>

                                <label for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if (bp_get_the_profile_field_is_required()) : ?><?php _e('(required)', 'buddypress') ?><?php endif; ?></label>
                                <select class="form-control" name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>" multiple="multiple">
                                    <?php bp_the_profile_field_options() ?>
                                </select>

                                <?php if (!bp_get_the_profile_field_is_required()) : ?>
                                    <a class="clear-value" href="javascript:clear( '<?php bp_the_profile_field_input_name() ?>' );"><?php _e('Clear', 'buddypress') ?></a>
                                <?php endif; ?>

                            <?php endif; ?>

                            <?php if ('radio' == bp_get_the_profile_field_type()) : ?>

                                <div class="radio">
                                    <span class="label"><?php bp_the_profile_field_name() ?> <?php if (bp_get_the_profile_field_is_required()) : ?><?php _e('(required)', 'buddypress') ?><?php endif; ?></span>

                                    <?php bp_the_profile_field_options() ?>

                                    <?php if (!bp_get_the_profile_field_is_required()) : ?>
                                        <a class="clear-value" href="javascript:clear( '<?php bp_the_profile_field_input_name() ?>' );"><?php _e('Clear', 'buddypress') ?></a>
                                    <?php endif; ?>
                                </div>

                            <?php endif; ?>

                            <?php if ('checkbox' == bp_get_the_profile_field_type()) : ?>

                                <div class="checkbox">
                                    <span class="label"><?php bp_the_profile_field_name() ?> <?php if (bp_get_the_profile_field_is_required()) : ?><?php _e('(required)', 'buddypress') ?><?php endif; ?></span>

                                    <?php bp_the_profile_field_options() ?>
                                </div>

                            <?php endif; ?>

                            <?php if ('datebox' == bp_get_the_profile_field_type()) : ?>

                                <div class="datebox">
                                    <label for="<?php bp_the_profile_field_input_name() ?>_day"><?php bp_the_profile_field_name() ?> <?php if (bp_get_the_profile_field_is_required()) : ?><?php _e('(required)', 'buddypress') ?><?php endif; ?></label>

                                    <select class="form-control" name="<?php bp_the_profile_field_input_name() ?>_day" id="<?php bp_the_profile_field_input_name() ?>_day">
                                        <?php bp_the_profile_field_options('type=day') ?>
                                    </select>

                                    <select class="form-control" name="<?php bp_the_profile_field_input_name() ?>_month" id="<?php bp_the_profile_field_input_name() ?>_month">
                                        <?php bp_the_profile_field_options('type=month') ?>
                                    </select>

                                    <select class="form-control" name="<?php bp_the_profile_field_input_name() ?>_year" id="<?php bp_the_profile_field_input_name() ?>_year">
                                        <?php bp_the_profile_field_options('type=year') ?>
                                    </select>
                                </div>

                            <?php endif; ?>

                            <?php do_action('bp_custom_profile_edit_fields') ?>

                            <p class="description"><?php bp_the_profile_field_description() ?></p>
                        </div>
                    <?php endwhile; ?>

                <?php endwhile; ?>

            </div><!--panel-body-->
        </div>


        <?php do_action('bp_after_profile_field_content') ?>

        <div class="submit">
            <input class="btn btn-primary btn-margin btn-margin-top" type="submit" name="profile-group-edit-submit" id="profile-group-edit-submit" value="<?php _e('Save Changes', 'buddypress') ?> " />
        </div>
        <input type="hidden" name="field_ids" id="field_ids" value="<?php echo implode(',', $field_ids) ?>" />
        <?php wp_nonce_field('bp_xprofile_edit') ?>

    <?php endif; ?>

</form>

<?php do_action('bp_after_profile_edit_content') ?>
