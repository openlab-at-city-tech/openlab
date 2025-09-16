<?php
/**
 * Media View Class.
 *
 * @since 2.5.0
 * @package SoliloquyWP Lite
 * @author SoliloquyWP Team <support@soliloquywp.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Soliloquy Media View
 *
 * @since 2.5.0
 */
class Soliloquy_Media_View_Lite {

	/**
	 * Holds the class object.
	 *
	 * @since 2.5
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Path to the file.
	 *
	 * @since 2.5
	 *
	 * @var string
	 */
	public $file = __FILE__;

	/**
	 * Holds the base class object.
	 *
	 * @since 2.5
	 *
	 * @var object
	 */
	public $base;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Base.
		$this->base = Soliloquy_Lite::get_instance();

		// Modals.
		add_filter( 'Soliloquy_Media_View_Lite_strings', [ $this, 'media_view_strings' ] );
		add_action( 'print_media_templates', [ $this, 'print_media_templates' ] );
	}

	/**
	 * Adds media view (modal) strings
	 *
	 * @since 2.5
	 *
	 * @param    array $strings Media View Strings.
	 * @return   array Media View Strings
	 */
	public function media_view_strings( $strings ) {

		return $strings;
	}

	/**
	 * Outputs backbone.js wp.media compatible templates, which are loaded into the modal
	 * view
	 *
	 * @since 2.5
	 */
	public function print_media_templates() {

		// Get the Gallery Post and Config.
		global $post;

		if ( isset( $post ) ) {
			$post_id = absint( $post->ID );
		} else {
			$post_id = 0;
		}

		// Bail if we're not editing an soliloquy Gallery.
		if ( get_post_type( $post_id ) !== 'soliloquy' ) {
			return;
		}

		// Meta Editor.
		// Use: wp.media.template( 'soliloquy-meta-editor' ).
		?>
		<script type="text/html" id="tmpl-soliloquy-meta-editor">

			<div class="edit-media-header">

				<button class="left dashicons"><span class="screen-reader-text"><?php esc_html_e( 'Edit previous media item' ); ?></span></button>

				<button class="right dashicons"><span class="screen-reader-text"><?php esc_html_e( 'Edit next media item' ); ?></span></button>

			</div>

			<div class="media-frame-title">
				<h1><?php esc_html_e( 'Edit Item', 'soliloquy' ); ?></h1>
			</div>
			<div class="media-frame-content">

				<div class="attachment-details save-ready">
					<!-- Left -->

					<div class="attachment-media-view portrait">

						<# if ( data.type  !== 'html' ) { #>

						<div class="thumbnail thumbnail-image">

							<img class="details-image" src="{{ data.src }}" draggable="false" />

								<# if ( data.type  === 'video' ) { #>

								<!-- Choose Video Placeholder Image + Remove Video Placeholder Image -->
								<a href="#" class="soliloquy-thumbnail button button-primary" data-field="soliloquy-src" title="Choose Video Placeholder Image"><?php esc_html_e( 'Choose Video Placeholder Image', 'soliloquy' ); ?></a>
								<a href="#" class="soliloquy-thumbnail-delete button button-secondary" data-field="soliloquy-src" title="Remove Video Placeholder Image"><?php esc_html_e( 'Remove Video Placeholder Image', 'soliloquy' ); ?></a>

								<# } #>

						</div>

						<# } #>

						<# if ( data.type  === 'html' ) { #>

							<div class="soliloquy-code-preview">

								{{ data.code }}

							</div>

						<# } #>

					</div>

					<!-- Right -->
					<div class="attachment-info">
						<!-- Settings -->
						<div class="settings">
							<!-- Attachment ID -->
							<input type="hidden" name="id" value="{{ data.id }}" />

							<input type="hidden" name="type" value="{{ data.type }}" />

							<!-- Title -->
							<div class="soliloquy-meta">

								<label class="setting">

									<span class="name"><?php esc_html_e( 'Title', 'soliloquy' ); ?></span>

									<input type="text" name="title" value="{{ data.title }}" />

									<span class="description">
										<?php esc_html_e( 'Enter the title for your slide.', 'soliloquy' ); ?>
									</span>

								</label>


							</div>

							<div class="soliloquy-meta">

								<label class="setting">

									<span class="name"><?php esc_html_e( 'Alt Text', 'soliloquy' ); ?></span>

									<input type="text" name="alt" value="{{ data.alt }}" />

								<span class="description">
									<?php esc_html_e( 'Describes the image for search engines and screen readers. Important for SEO and accessibility.', 'soliloquy' ); ?>
								</span>

								</label>




							</div>

							<!-- Caption -->
							<# if ( data.type  !== 'html' ) { #>
							<!-- Caption -->
							<div class="soliloquy-meta">
								<div class="setting">
									<span class="name"><?php esc_html_e( 'Caption', 'soliloquy' ); ?></span>
									<?php
									wp_editor(
										'',
										'caption',
										[
											'media_buttons' => false,
											'wpautop'   => false,
											'tinymce'   => false,
											'textarea_name' => 'caption',
											'quicktags' => [
												'buttons' => 'strong,em,link,ul,ol,li,close',
											],
										]
									);

									?>
									<span class="description">
										<?php esc_html_e( 'Displayed over the slide image. Field accepts any valid HTML.', 'soliloquy' ); ?>
									</span>

								</div>

							</div>
							<# } #>


							<# if ( data.type  === 'image' ) { #>
							<div class="soliloquy-meta">
								<label class="setting">
									<span class="name"><?php esc_html_e( 'URL', 'soliloquy' ); ?></span>
									<input type="text" name="link" value="{{ data.link }}" />
									<# if ( typeof( data.id ) === 'number' ) { #>
										<span class="buttons">
											<button class="button button-small media-file"><?php esc_html_e( 'Media File', 'soliloquy' ); ?></button>
											<button class="button button-small attachment-page"><?php esc_html_e( 'Attachment Page', 'soliloquy' ); ?></button>
										</span>
									<# } #>
									<span class="description">
										<?php esc_html_e( 'Enter a hyperlink to link this slide to another page.', 'soliloquy' ); ?>
									</span>

								</label>

								<!-- Link in New Window -->
								<label class="setting">
									<span class="name"><?php esc_html_e( 'Open URL in New Window?', 'soliloquy' ); ?></span>
									<input type="checkbox" name="linktab" value="1"<# if ( data.linktab == '1' ) { #> checked <# } #> /><span class="check-label"><?php esc_html_e( 'Opens your image links in a new browser window / tab.', 'soliloquy' ); ?></span>
								</label>

								</div>
							<# } #>

							<# if ( data.type  === 'video' ) { #>
							<div class="soliloquy-meta">
								<!-- Link -->
								<label class="setting">
									<span class="name"><?php esc_html_e( 'URL', 'soliloquy' ); ?></span>
									<input type="text" name="link" value="{{ data.url }}" />
								</label>
							</div>
							<# } #>

							<# if ( data.type  === 'html' ) { #>
							<div class="soliloquy-meta code">
								<!-- Link -->
									<label class="code">
										<span class="name"><?php esc_html_e( 'Code', 'soliloquy' ); ?></span>
										<textarea class="soliloquy-html-slide-code" name="code">{{ data.code }}</textarea>
									</label>
							</div>
							<# } #>
							<!-- Addons can populate the UI here -->
							<div class="addons"></div>

						</div>
						<!-- /.settings -->

						<!-- Actions -->
						<div class="actions">

							<a href="#" class="soliloquy-meta-submit button media-button button-large button-primary media-button-insert" title="<?php esc_attr_e( 'Save Metadata', 'soliloquy' ); ?>">
								<?php esc_html_e( 'Save Metadata', 'soliloquy' ); ?>
							</a>

							<!-- Save Spinner -->
							<span class="settings-save-status">
								<span class="spinner"></span>
								<span class="saved"><?php esc_html_e( 'Saved.', 'soliloquy' ); ?></span>
							</span>
						</div>
						<!-- /.actions -->
					</div>
				</div>
			</div>
		</script>

		<?php
		// Bulk Image Editor.
		// Use: wp.media.template( 'soliloquy-meta-bulk-editor' ).
		?>
		<script type="text/html" id="tmpl-soliloquy-meta-bulk-editor">

			<div class="media-frame-title">
				<h1><?php esc_html_e( 'Bulk Edit', 'soliloquy' ); ?></h1>
			</div>

			<div class="media-frame-content">
				<div class="attachment-details save-ready">
					<!-- Left -->
					<div class="attachment-media-view portrait">
						<ul class="attachments soliloquy-bulk-edit">
						</ul>
					</div>

					<!-- Right -->
					<div class="attachment-info">
						<!-- Settings -->
						<div class="settings">
							<!-- Attachment ID -->

							<!-- Title -->
							<div class="soliloquy-meta">

								<label class="setting">

									<span class="name"><?php esc_html_e( 'Alt Text', 'soliloquy' ); ?></span>

									<input type="text" name="alt" value="{{ data.alt }}" />
									<span class="description">
										<?php esc_html_e( 'Describes the image for search engines and screen readers. Important for SEO and accessibility.', 'soliloquy' ); ?>
									</span>

								</label>

							</div>

							<!-- Caption -->
							<div class="soliloquy-meta">
								<div class="setting">
									<span class="name"><?php esc_html_e( 'Caption', 'soliloquy' ); ?></span>
									<?php
									wp_editor(
										'',
										'caption',
										[
											'media_buttons' => false,
											'wpautop'   => false,
											'tinymce'   => false,
											'textarea_name' => 'caption',
											'quicktags' => [
												'buttons' => 'strong,em,link,ul,ol,li,close',
											],
										]
									);

									?>
									<span class="description">
										<?php esc_html_e( 'Displayed over the slide image. Field accepts any valid HTML.', 'soliloquy' ); ?>
									</span>

								</div>

							</div>

							<# if ( data.type  === 'image' ) { #>
							<div class="soliloquy-meta">
								<label class="setting">
									<span class="name"><?php esc_html_e( 'URL', 'soliloquy' ); ?></span>
									<input type="text" name="link" value="{{ data.link }}" />
									<# if ( typeof( data.id ) === 'number' ) { #>
										<span class="buttons">
											<button class="button button-small media-file"><?php esc_html_e( 'Media File', 'soliloquy' ); ?></button>
											<button class="button button-small attachment-page"><?php esc_html_e( 'Attachment Page', 'soliloquy' ); ?></button>
										</span>
									<# } #>
									<span class="description">
										<strong><?php esc_html_e( 'URL', 'soliloquy' ); ?></strong>
										<?php esc_html_e( 'Enter a hyperlink to link this slide to another page.', 'soliloquy' ); ?>
									</span>
								</label>



								<!-- Link in New Window -->
								<label class="setting">
									<span class="name"><?php esc_html_e( 'Open URL in New Window?', 'soliloquy' ); ?></span>
									<input type="checkbox" name="link_new_window" value="1"<# if ( data.link_new_window == '1' ) { #> checked <# } #> />
									<span class="check-label"><?php esc_html_e( 'Opens your image links in a new browser window / tab.', 'soliloquy' ); ?></span>
								</label>

								</div>
							<# } #>

							<# if ( data.type  === 'video' ) { #>
							<div class="soliloquy-meta">
								<!-- Link -->
								<label class="setting">
									<span class="name"><?php esc_html_e( 'URL', 'soliloquy' ); ?></span>
									<input type="text" name="link" value="{{ data.url }}" />
								</label>
							</div>
							<# } #>

							<# if ( data.type  === 'html' ) { #>
							<div class="soliloquy-meta">
								<!-- Link -->
								<label class="code">
									<span class="name"><?php esc_html_e( 'Code', 'soliloquy' ); ?></span>
									<textarea class="soliloquy-html-slide-code" name="code">{{ data.code }}</textarea>
								</label>
							</div>
							<# } #>
							<!-- Addons can populate the UI here -->
							<div class="addons"></div>

						</div>
						<!-- /.settings -->

						<!-- Actions -->
						<div class="actions">
							<a href="#" class="soliloquy-meta-submit button media-button button-large button-primary media-button-insert" title="<?php esc_attr_e( 'Save Metadata', 'soliloquy' ); ?>">
								<?php esc_html_e( 'Save Metadata', 'soliloquy' ); ?>
							</a>

							<!-- Save Spinner -->
							<span class="settings-save-status">
								<span class="spinner"></span>
								<span class="saved"><?php esc_html_e( 'Saved.', 'soliloquy' ); ?></span>
							</span>
						</div>
						<!-- /.actions -->
					</div>

				</div>
			</div>
		</script>

		<?php
		// Bulk Image Editor Image.
		// Use: wp.media.template( 'soliloquy-meta-bulk-editor-image' ).
		?>
		<script type="text/html" id="tmpl-soliloquy-meta-bulk-editor-slides">
			<div class="attachment-preview">
				<div class="thumbnail">
					<div class="centered">
					<# if ( data.type  !== 'html' ) { #>

						<img src={{ data.src }} />

					<# } #>
						<# if ( data.type  === 'html' ) { #>

							<div class="soliloquy-code-preview">

								<img src="<?php echo esc_url( plugins_url( 'assets/images/html.png', $this->base->file ) ); ?>" />

							</div>

						<# } #>

					</div>
				</div>
			</div>
		</script>

		<?php
		do_action( 'soliloquy_print_templates' );
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 2.5
	 *
	 * @return object The Soliloquy_Media_View_Lite object.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Soliloquy_Media_View_Lite ) ) {
			self::$instance = new Soliloquy_Media_View_Lite();
		}

		return self::$instance;
	}
}

// Load the media class.
$soliloquy_media_view = Soliloquy_Media_View_Lite::get_instance();
