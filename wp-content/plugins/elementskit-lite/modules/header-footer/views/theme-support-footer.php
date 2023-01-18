<?php do_action( 'elementskit/template/before_footer' ); ?>
<div class="ekit-template-content-markup ekit-template-content-footer ekit-template-content-theme-support">
<?php
	$template = \ElementsKit_Lite\Modules\Header_Footer\Activator::template_ids();
	echo \ElementsKit_Lite\Utils::render_elementor_content( $template[1] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --  Displaying with Elementor content rendering
?>
</div>
<?php do_action( 'elementskit/template/after_footer' ); ?>
<?php wp_footer(); ?>

</body>
</html>
