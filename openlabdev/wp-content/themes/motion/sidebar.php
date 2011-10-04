<div id="sidebar">
	<ul>
	<?php if ( !dynamic_sidebar( 'sidebar' ) ) : ?>

		<li class="boxed">
			<h3>Recent entries</h3>
			<ul>
				<?php wp_get_archives( 'type=postbypost&limit=10' ); ?>
			</ul>
		</li>

		<li class="boxed" id="tagbox">
			<h3>Browse popular tags</h3>
			<?php wp_tag_cloud( 'smallest=8&largest=15&number=30' ); ?>
		</li>

		<li class="boxed">
			<h3>Meta</h3>
			<ul>
				<?php wp_register(); ?>
				<li><?php wp_loginout(); ?></li>
				<li><a href="<?php bloginfo( 'rss2_url' ); ?>">Entries RSS</a></li>
				<li><a href="<?php bloginfo( 'comments_rss2_url' ); ?>">Comments RSS</a></li>
				<?php wp_meta(); ?>
			</ul>
		</li>

	<?php endif; ?>
	</ul>
</div><!-- /sidebar -->