<?php
/**
 * Creates the term settings.
 *
 * @package Genesis
 */

add_action( 'admin_init', 'genesis_add_taxonomy_archive_options' );
/**
 * Loop through the custom taxonomies and add our archive options
 * to each custom taxonomy edit screen.
 *
 * @since 1.6
 */
function genesis_add_taxonomy_archive_options() {
	foreach ( get_taxonomies( array( 'show_ui' => true ) ) as $tax_name) {
		add_action( $tax_name . '_edit_form', 'genesis_taxonomy_archive_options', 10, 2 );
	}
}
/**
 * This function, hooked to display on the category/tag edit forms,
 * adds new fields for display on archives. The variables $tag and
 * $taxonomy are passed via the hook so that we can use them.
 *
 * @since 1.6
 */
function genesis_taxonomy_archive_options( $tag, $taxonomy ) {

	$tax = get_taxonomy( $taxonomy );
?>

	<h3><?php _e('Genesis Archive Options', 'genesis'); ?></h3>
	<table class="form-table"><tbody>

	<tr>
		<th scope="row" valign="top"><label><?php _e('Display Title/Description', 'genesis'); ?></label></th>
		<td>
			<label><input name="meta[display_title]" type="checkbox" value="1" <?php checked(1, $tag->meta['display_title']); ?> /> <?php printf( __('Display %s title at the top of archive pages?', 'genesis'), esc_html( $tax->labels->singular_name ) ); ?></label><br />
			<label><input name="meta[display_description]" type="checkbox" value="1" <?php checked(1, $tag->meta['display_description']); ?> /> <?php printf( __('Display %s description at the top of archive pages?', 'genesis'), esc_html( $tax->labels->singular_name ) ); ?></label>
		</td>
	</tr>

	</tbody></table>

<?php
}

add_action( 'admin_init', 'genesis_add_taxonomy_seo_options' );
/**
 * Loop through the custom taxonomies and add our SEO options
 * to each custom taxonomy edit screen.
 *
 * @since 1.3
 */
function genesis_add_taxonomy_seo_options() {
	foreach ( get_taxonomies( array( 'show_ui' => true ) ) as $tax_name) {
		add_action( $tax_name . '_edit_form', 'genesis_taxonomy_seo_options', 10, 2 );
	}
}
/**
 * This function, hooked to display on the category/tag edit forms,
 * adds new fields for SEO. The variables $tag and $taxonomy are passed
 * via the hook so that we can use them.
 *
 * @since 1.2
 */
function genesis_taxonomy_seo_options( $tag, $taxonomy ) {

	$tax = get_taxonomy( $taxonomy );
?>

	<h3><?php _e('Genesis SEO Options and Settings', 'genesis'); ?></h3>
	<table class="form-table"><tbody>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="meta[doctitle]"><?php printf( __('Custom Document %s', 'genesis'), '<code>&lt;title&gt;</code>' ); ?></label></th>
		<td><input name="meta[doctitle]" id="meta[doctitle]" type="text" value="<?php echo esc_attr( $tag->meta['doctitle'] ); ?>" size="40" />
		</td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="meta[description]"><?php printf( __('%s Description', 'genesis'), '<code>META</code>' ); ?></label></th>
		<td><textarea name="meta[description]" id="meta[description]" rows="3" cols="50"><?php echo esc_html( $tag->meta['description'] ); ?></textarea></td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="meta[keywords]"><?php printf( __('%s Keywords', 'genesis'), '<code>META</code>' ); ?></label></th>
		<td><input name="meta[keywords]" id="meta[keywords]" type="text" value="<?php echo esc_attr( $tag->meta['keywords'] ); ?>" size="40" />
		<p class="description"><?php _e('Comma separated list', 'genesis'); ?></p></td>
	</tr>

	<tr>
		<th scope="row" valign="top"><label><?php _e('Robots Meta', 'genesis'); ?></label></th>
		<td>
			<label><input name="meta[noindex]" id="meta[noindex]" type="checkbox" value="1" <?php checked(1, $tag->meta['noindex']); ?> /> <?php printf( __('Apply %s to this archive?', 'genesis'), '<code>noindex</code>' ); ?></label><br />
			<label><input name="meta[nofollow]" id="meta[nofollow]" type="checkbox" value="1" <?php checked(1, $tag->meta['nofollow']); ?> /> <?php printf( __('Apply %s to this archive?', 'genesis'), '<code>nofollow</code>' ); ?></label><br />
			<label><input name="meta[noarchive]" id="meta[noarchive]" type="checkbox" value="1" <?php checked(1, $tag->meta['noarchive']); ?> /> <?php printf( __('Apply %s to this archive?', 'genesis'), '<code>noarchive</code>' ); ?></label>
		</td>
	</tr>

	</tbody></table>

<?php
}

add_action('admin_init', 'genesis_add_taxonomy_layout_options');
/**
 * Loop through the custom taxonomies and add our SEO options
 * to each custom taxonomy edit screen.
 *
 * @since 1.4
 */
function genesis_add_taxonomy_layout_options() {
		foreach ( get_taxonomies( array( 'show_ui' => true ) ) as $tax_name) {
				add_action($tax_name . '_edit_form', 'genesis_taxonomy_layout_options', 10, 2);
		}
}
/**
 * This function, hooked to display on the category/tag edit forms,
 * adds new fields for SEO. The variables $tag and $taxonomy are passed
 * via the hook so that we can use them.
 *
 * @since 1.4
 */
function genesis_taxonomy_layout_options($tag, $taxonomy) {

	$tax = get_taxonomy( $taxonomy );
?>

	<h3><?php _e('Genesis Layout Options', 'genesis'); ?></h3>
	<table class="form-table"><tbody>

	<tr>
		<th scope="row" valign="top"><label><?php _e('Choose Layout', 'genesis'); ?></label></th>
		<td>
			<p>
			<input type="radio" name="meta[layout]" id="default-layout" value="" <?php checked('', $tag->meta['layout']); ?> /> <label class="default" for="default-layout"><?php printf( __('Default Layout set in <a href="%s">Theme Settings</a>', 'genesis'), menu_page_url( 'genesis', 0 ) ); ?></label>
			</p>

			<p class="clear">
			<?php
			foreach ( genesis_get_layouts() as $id => $data ) {

				printf( '<label class="box"><input type="radio" name="meta[layout]" id="%s" value="%s" %s /> <img src="%s" alt="%s" /></label>', esc_attr( $id ), esc_attr( $id ), checked($id, $tag->meta['layout'], false), esc_url( $data['img'] ), esc_attr( $data['label'] ) );

			}
			?>
			<br class="clear" /></p>
		</td>
	</tr>

	</tbody></table>

<?php
}

add_action('edit_term', 'genesis_term_meta_save', 10, 2);
/**
 * This function executes, via a hook, whenever the user edits
 * a term (category/tag/etc) so that when the term gets saved,
 * its meta information gets saved as well.
 */
function genesis_term_meta_save($term_id, $tt_id) {

	$term_meta = (array) get_option('genesis-term-meta');

	$term_meta[$term_id] = isset( $_POST['meta'] ) ? (array) $_POST['meta'] : array();

	update_option('genesis-term-meta', $term_meta);

}

add_action('delete_term', 'genesis_term_meta_delete', 10, 2);
/**
 * This function executes, via a hook, whenever the user deletes
 * a term (category/tag/etc) so that when a term is deleted,
 * its meta info gets deleted as well.
 */
function genesis_term_meta_delete($term_id, $tt_id) {

	$term_meta = (array) get_option('genesis-term-meta');

	unset( $term_meta[$term_id] );

	update_option('genesis-term-meta', (array) $term_meta);

}