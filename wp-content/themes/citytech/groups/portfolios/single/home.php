<?php do_action( 'bp_before_group_home_content' ) ?>
<?php $group_slug = bp_get_group_slug(); ?>

<h1 class="entry-title"><?php echo bp_group_name(); ?> Profile</h1>

<?php if ( bp_is_group_home() ): ?>
<?php $group_type = openlab_get_group_type( bp_get_current_group_id()); ?>
<h4 class="profile-header"><?php echo ucfirst($group_type); ?> Profile</h4>
<div id="portfolio-header">
	 <div id="portfolio-header-avatar" class="alignleft">
		<a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>">
			<?php bp_group_avatar('type=full&width=225') ?>
		</a>
        <?php global $bp;
			  if (is_user_logged_in() && $bp->is_item_admin): ?>
              <div id="group-action-wrapper">
					<div id="action-edit-group"><a href="<?php echo bp_group_permalink(). 'admin/edit-details/'; ?>">Edit Profile</a></div>
            		<div id="action-edit-avatar"><a href="<?php echo bp_group_permalink(). 'admin/group-avatar/'; ?>">Change Avatar</a></div>
              </div>
        <?php endif; ?>
		<?php /* <p>Descriptive Tags associated with their profile, School, Etc, Tag, Tag, Tag, Tag, Tag, Tag, Tag</p> */ ?>
	</div><!-- #club-header-avatar -->

	<div id="club-header-content" class="alignleft">
		<h2><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></h2>
		<span class="highlight"><?php echo bp_core_get_user_displayname( openlab_get_user_id_from_portfolio_group_id( bp_get_current_group_id() ) ) ?></span>

		<?php do_action( 'bp_before_group_header_meta' ) ?>

		<div id="item-meta">
			<?php bp_group_description() ?>

			<?php do_action( 'bp_group_header_meta' ) ?>
		</div>
	</div><!-- #item-header-content -->

	<?php do_action( 'bp_after_group_header' ) ?>

	<?php do_action( 'template_notices' ) ?>

</div><!-- #item-header -->

<?php endif; ?>

<div id="club-item-body">

	<?php do_action( 'bp_before_group_body' ) ?>

	<?php if ( bp_is_group_home() ) { ?>

		<?php if ( !bp_group_is_visible() ) : ?>
			<?php /* The group is not visible, show the status message */ ?>

			<?php do_action( 'bp_before_group_status_message' ) ?>

			<div id="message" class="info">
				<p><?php bp_group_status_message() ?></p>
			</div>

			<?php do_action( 'bp_after_group_status_message' ) ?>

		<?php endif; ?>


		<?php if ( bp_group_is_visible() && bp_is_active( 'activity' )  ) : ?>

			<?php if ( wds_site_can_be_viewed() ) : ?>
				<?php show_site_posts_and_comments() ?>
			<?php endif ?>


		<?php endif; ?>

	<?php } else {

		locate_template( array( 'groups/single/wds-bp-action-logics.php' ), true );

	} ?>

	<?php do_action( 'bp_after_group_body' ) ?>

</div><!-- #item-body -->

<?php do_action( 'bp_after_group_home_content' ) ?>