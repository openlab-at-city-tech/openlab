<textarea id="annotation" class="large-text" name="portfolio_annotation" rows="5"><?php echo esc_textarea( $data ); ?></textarea>
<?php wp_nonce_field( 'portfolio_annotation', 'portfolio_annotation_nonce' ); ?>
