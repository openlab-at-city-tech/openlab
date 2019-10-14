<?php
use function OpenLab\Attributions\Helpers\get_the_attribution;
?>
<footer>
	<p id="attributions">Attribtuions</p>
	<ol>
		<?php foreach ( $attributions as $attribution ) : ?>
		<?php printf(
			'<li id="ref-%1$s">%2$s.<a href="#anchor-%1$s">&#8617;</a></li>',
			$attribution['id'],
			get_the_attribution( $attribution )
		); ?>
		<?php endforeach; ?>
	</ol>
</footer>
