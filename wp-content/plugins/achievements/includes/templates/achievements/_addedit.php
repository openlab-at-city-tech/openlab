<?php do_action( 'dpa_achievement_addedit_between_fieldsets_zero' ) ?>

<fieldset class="mandatory-trigger">
	<?php do_action( 'dpa_achievement_addedit_before_trigger' ) ?>

	<legend><?php _e( "Action", 'dpa' ) ?></legend>
	<div class="container">
		<div class="label"><?php _e( "Type", 'dpa' ) ?></div>
		<div class="data achievement_type">
			<?php dpa_addedit_achievement_type() ?>
			<p class="hint"><?php _e( "An <em>award</em> is given by a site admin, whereas an <em>event</em> is unlocked automatically when its criteria are met.", 'dpa' ) ?></p>
		</div>
		<div style="clear: left;"></div>
	</div>

	<div class="container">
		<div class="event <?php dpa_addedit_achievement_type_value() ?>">
			<div class="label"><?php _e( "Event", 'dpa' ) ?></div>
			<div class="data"><select name="action_id" id="action_id"><?php dpa_addedit_action_descriptions() ?></select><?php dpa_addedit_warning( 'action_id' ) ?></div>
		</div>
		<div style="clear: left;"></div>
	</div>

	<div class="container">
		<div class="event <?php dpa_addedit_achievement_type_value() ?>">
			<div class="label"><?php _e( "When the event occurs this many times", 'dpa' ) ?></div>
			<div class="data"><input type="number" min="0" name="action_count" id="action_count" value="<?php dpa_addedit_value( 'action_count' ) ?>"><?php dpa_addedit_warning( 'action_count' ) ?></div>
		</div>
		<div style="clear: left;"></div>
	</div>

	<?php if ( bp_is_active( 'groups' ) ) : ?>
		<div class="container">
			<div class="groups initially_hidden <?php dpa_addedit_achievement_type_value() ?>">
				<div class="label"><?php _e( "In this group", 'dpa' ) ?></div>
				<div class="data"><select name="group_id" id="group_id"><?php dpa_addedit_action_groups() ?></select><?php dpa_addedit_warning( 'group_id' ) ?></div>
			</div>
			<div style="clear: left;"></div>
		</div>
	<?php endif; ?>

	<?php if ( is_multisite() && bp_is_active( 'blogs' ) ) : ?>
		<div class="container">
			<div class="event <?php dpa_addedit_achievement_type_value() ?>">
				<div class="label"><?php _e( "On this site", 'dpa' ) ?></div>
				<div class="data"><select name="site_id" id="site_id"><?php dpa_addedit_action_multisites() ?></select><?php dpa_addedit_warning( 'site_id' ) ?></div>
			</div>
			<div style="clear: left;"></div>
		</div>
	<?php endif; ?>

	<?php do_action( 'dpa_achievement_addedit_after_trigger' ) ?>
</fieldset>

<?php do_action( 'dpa_achievement_addedit_between_fieldsets_one' ) ?>

<fieldset class="mandatory-about">
	<?php do_action( 'dpa_achievement_addedit_before_about' ) ?>

	<legend><?php _e( "Info", 'dpa' ) ?></legend>
	<div class="container">
		<div class="label"><?php _e( 'Name', 'dpa' ) ?></div>
		<div class="data"><input autofocus type="text" name="name" id="name" maxlength="200" value="<?php esc_attr( dpa_addedit_value( 'name' ) ) ?>" required><?php dpa_addedit_warning( 'name' ) ?></div>
		<div style="clear: left;"></div>
	</div>

	<div class="container">
		<div class="label"><?php _e( 'Description', 'dpa' ) ?></div>
		<div class="data"><textarea name="description" id="description" required><?php dpa_addedit_value( 'description' ) ?></textarea><?php dpa_addedit_warning( 'description' ) ?><br /><p class="hint"><?php _e( "Explain how to unlock this Achievement.", 'dpa' ) ?></p></div>
		<div style="clear: left;"></div>
	</div>

	<div class="container">
		<div class="label"><?php _e( 'Points', 'dpa' ) ?></div>
		<div class="data"><input type="number" name="points" id="points" value="<?php dpa_addedit_value( 'points' ) ?>" required><?php dpa_addedit_warning( 'points' ) ?><br /><p class="hint"><?php _e( "Points are awarded when Achievements are unlocked. They are used as a measurement of contribution to your site.", 'dpa' ) ?></p></div>
		<div style="clear: left;"></div>
	</div>

	<?php do_action( 'dpa_achievement_addedit_after_about' ) ?>
</fieldset>

<?php do_action( 'dpa_achievement_addedit_between_fieldsets_two' ) ?>

<fieldset class="mandatory-advanced">
	<?php do_action( 'dpa_achievement_addedit_before_advanced' ) ?>

	<legend><?php _e( "Advanced settings", 'dpa' ) ?></legend>
	<div class="container">
		<div class="label"><?php _e( 'Active', 'dpa' ) ?></div>
		<div class="data"><input type="checkbox" name="is_active" id="is_active" <?php dpa_addedit_value( 'is_active' ) ?>><?php dpa_addedit_warning( 'is_active' ) ?><br /><p class="hint"><?php _e( "Is this Achievement active?", 'dpa' ) ?></p></div>
		<div style="clear: left;"></div>
	</div>

	<div class="container">
		<div class="label"><?php _e( 'Hidden', 'dpa' ) ?></div>
		<div class="data"><input type="checkbox" name="is_hidden" id="is_hidden" <?php dpa_addedit_value( 'is_hidden' ) ?>><br /><p class="hint"><?php _e( "Hide this Achievement in the Directory and from search results?", 'dpa' ) ?></p></div>
		<div style="clear: left;"></div>
	</div>

	<div class="container">
		<div class="label"><?php _e( 'Slug', 'dpa' ) ?></div>
		<div class="data"><input type="text" name="slug" id="slug" maxlength="200" value="<?php dpa_addedit_value( 'slug' ) ?>" required><?php dpa_addedit_warning( 'slug' ) ?><br /><p class="hint"><?php _e( "The <em>slug</em> constitutes part of the link to this Achievement.", 'dpa' ) ?></p></div>
		<div style="clear: left;"></div>
	</div>

	<?php do_action( 'dpa_achievement_addedit_after_advanced' ) ?>
</fieldset>

<?php do_action( 'dpa_achievement_addedit_between_fieldsets_three' ) ?>

<?php if ( dpa_is_achievement_edit_page() ) : ?>
	<input type="submit" name="achievement-edit" id="achievement-edit" value="<?php _e( 'Update Achievement', 'dpa' ) ?>">
<?php else: ?>
	<input type="submit" name="achievement-create" id="achievement-create" value="<?php _e( 'Create Achievement', 'dpa' ) ?>">
<?php endif; ?>