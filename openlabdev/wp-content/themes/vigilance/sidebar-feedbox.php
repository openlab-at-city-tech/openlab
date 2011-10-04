<?php global $vigilance; ?>
<h2 class="widgettitle"><?php if ($vigilance->feedTitle() != '' ) echo $vigilance->feedTitle(); else echo _e( 'Get Free Updates', 'vigilance' ); ?></h2>
<div id="rss-feed" class="clear">
	<p><?php if ($vigilance->feedIntro() != '' ) echo $vigilance->feedIntro(); else _e( 'Get the latest and the greatest news delivered for free to your reader or your inbox:', 'vigilance' ); ?></p>
	<a class ="rss" href="<?php bloginfo( 'rss2_url' ); ?>"><?php _e( 'RSS Feed', 'vigilance' ); ?></a>
	<a class="email" href="<?php echo $vigilance->feedEmail(); ?>"><?php _e( 'Email Updates', 'vigilance' ); ?></a>
	<?php if ($vigilance->twitterToggle() == 'true' ) : ?>
		<a class="twitter" href="<?php if ($vigilance->twitter() !== '' ) echo $vigilance->twitter(); else echo "#"; ?>"><?php _e( 'Twitter', 'vigilance' ); ?></a>
	<?php endif; ?>
</div>
