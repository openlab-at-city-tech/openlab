<?php
use function OpenLab\Attributions\Helpers\get_the_attribution;
?>
<footer id="attributions-list">
	<hr>
	<p id="attributions">Sources</p>
	<ol>
		<?php foreach ( $attributions as $attribution ) : ?>
		<?php printf(
			'<li id="ref-%1$s">%2$s <a href="#anchor-%1$s">&#8593;</a></li>',
			$attribution['id'],
			empty( $attribution['content'] ) ? get_the_attribution( $attribution ) : $attribution['content']
		); ?>
		<?php endforeach; ?>
	</ol>
</footer>
