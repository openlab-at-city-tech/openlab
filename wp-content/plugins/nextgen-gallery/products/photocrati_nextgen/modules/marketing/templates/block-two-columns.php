<?php
/**
 * @var C_Marketing_BLock_Two_Columns $block
 * @var string $link_text
 */ ?>
<div class="ngg-marketing-block-two-columns">
	<div class="wp-block-group has-background upsell" style="background-color:#fbfbfb">
		<div class="wp-block-group__inner-container">
			<h3><?php print $block->title; ?></h3>
			<?php
			if ( is_array( $block->description ) ) {
				foreach ( $block->description as $description ) {
					?>
					<p><?php print $description; ?></p>
					<?php
				}
			} else {
				?>
				<p><?php print $block->description; ?></p>
			<?php } ?>

			<div class="wp-block-columns">
				<?php
				foreach ( $block->links as $column ) {
					?>
					<div class="wp-block-column">
						<ul>
							<?php
							foreach ( $column as $link ) {
								?>
								<li>
									<?php if ( is_array( $link ) ) { ?>
										<a href="<?php print esc_attr( $link['href'] ); ?>"
											target="_blank"
											rel="noreferrer noopener"><?php print $link['title']; ?></a>
										<?php
									} else {
										print $link;
									}
									?>
								</li>
							<?php } ?>
						</ul>
					</div>
				<?php } ?>
			</div>

			<p><?php print $block->footer; ?></p>

			<div class="wp-block-buttons">
				<div class="wp-block-button">
					<a class="wp-block-button__link has-text-color has-background no-border-radius"
						href="<?php print esc_attr( $block->get_upgrade_link() ); ?>"
						style="background-color: #9ebc1b; color:#ffffff"
						target="_blank"
						rel="noreferrer noopener"><?php print $link_text; ?></a>
				</div>
			</div>
		</div>
	</div>
</div>