<?php
/**
 * Testimonial External Link.
 */
if ( !empty( $testimonial['link']['url'] ) ):
	$this->add_link_attributes( 'link-' . $testimonial['_id'], $testimonial['link'] );
?>
	<a <?php echo $this->get_render_attribute_string( 'link-' . $testimonial['_id'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?> class="ekit-testimonial--link">
		<?php echo !empty( $testimonial['client_name'] ) ? esc_html( $testimonial['client_name'] ) : esc_html( $testimonial['_id'] ); ?>
	</a>
<?php
endif;
