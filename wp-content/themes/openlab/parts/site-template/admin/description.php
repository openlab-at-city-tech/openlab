<label class="screen-reader-text" for="excerpt"><?php esc_html_e( 'Description', 'cboxol-site-template-picker' ); ?></label>
<?php // phpcs:ignore WordPress.Security.EscapeOutput ?>
<textarea rows="1" cols="40" name="excerpt" id="excerpt"><?php echo $description; ?></textarea>
