<?php
use function OpenLab\Attributions\Helpers\get_the_attribution;
?>
<footer id="attributions-list">
	<hr>
	<p id="attributions"><?php esc_html_e( 'Sources', 'openlab-attributions' ); ?></p>
	<ol>
		<?php foreach ( $attributions as $attribution ) : ?>
			<?php
			$attribution_content = empty( $attribution['content'] ) ? get_the_attribution( $attribution ) : $attribution['content'];

			printf(
				'<li id="ref-%1$s">%2$s <a href="#anchor-%1$s">&#8593;</a></li>',
				esc_attr( $attribution['id'] ),
				wp_kses_post( $attribution_content )
			);
			?>
		<?php endforeach; ?>
	</ol>
</footer>
