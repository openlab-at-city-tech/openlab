<footer>
	<p id="attributions"><?php echo esc_html( $settings['title'] ); ?></p>
	<p><?php echo esc_html( $settings['description'] ); ?></p>
	<ol>
		<?php foreach ( $refs as $id => $note ) : ?>
		<?php printf( '<li id="attr-%1$d">%2$s<a href="#%1$d-anchor">&#8617;</a></li>', $id, $note ); ?>
		<?php endforeach; ?>
	</ol>
</footer>
