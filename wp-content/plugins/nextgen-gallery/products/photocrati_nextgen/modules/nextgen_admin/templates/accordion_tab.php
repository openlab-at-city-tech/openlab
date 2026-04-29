<h3 class="accordion_tab" id="<?php echo esc_attr( $id ); ?>"><a href="#"><?php echo esc_html( $title ); ?></a></h3>
<div id="<?php echo esc_attr( $id ); ?>_content">
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $content contains safe HTML for accordion tab content
	echo $content;
	?>
</div>