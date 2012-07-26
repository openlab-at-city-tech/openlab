<?php do_action( 'bp_before_group_home_content' ) ?>
<h1 class="entry-title">Club on the OpenLab</h1>

<div id="club-header">
<?php
	global $bp;
	
	if ($bp->current_action == "home"): ?>
	 <div id="club-header-avatar" class="alignleft">
		<a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>">
			<?php bp_group_avatar('type=full&width=225') ?>
		</a>
		<?php /* <p>Descriptive Tags associated with their profile, School, Etc, Tag, Tag, Tag, Tag, Tag, Tag, Tag</p> */ ?>
	</div><!-- #club-header-avatar -->

	<div id="club-header-content" class="alignleft">
		<h2><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></h2>
		<div class="info-line"><span class="highlight"><?php bp_group_type() ?></span> <span class="activity"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ) ?></span></div>
	
		<?php do_action( 'bp_before_group_header_meta' ) ?>
	
		<div id="item-meta">
			<?php bp_group_description() ?>
	
			<?php do_action( 'bp_group_header_meta' ) ?>
		</div>
	</div><!-- #item-header-content -->

	<?php do_action( 'bp_after_group_header' ) ?>
	
	<?php do_action( 'template_notices' ) ?>
	
    <?php endif; ?>
    
</div><!-- #item-header -->