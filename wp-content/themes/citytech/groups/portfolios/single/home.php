<?php do_action( 'bp_before_group_home_content' ) ?>
<?php
//
//     control the formatting of left and right side by use of variable $first_class.
//     when it is "first" it places it on left side, when it is "" it places it on right side
//
//     Initialize it to left side to start with
//
       $first_class = "first";
?>
<?php $group_slug = bp_get_group_slug(); ?>
<h1 class="entry-title"><?php echo openlab_portfolio_label( 'case=upper' ) ?> on the OpenLab</h1>
<div id="portfolio-header">
	 <div id="portfolio-header-avatar" class="alignleft">
		<a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>">
			<?php bp_group_avatar('type=full&width=225') ?>
		</a>
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