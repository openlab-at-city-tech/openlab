<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'ultimate-responsive-image-slider' ) );
}

// Fetch all existing sliders
$uris_all_sliders = get_posts( array(
	'post_type'      => 'ris_gallery',
	'posts_per_page' => -1,
	'post_status'    => 'any',
	'orderby'        => 'date',
	'order'          => 'DESC',
) );
$uris_total_sliders = count( $uris_all_sliders );

// Get notices
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$uris_import_status = isset( $_GET['uris_import_status'] ) ? sanitize_text_field( wp_unslash( $_GET['uris_import_status'] ) ) : '';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$uris_imported_count = isset( $_GET['uris_count'] ) ? absint( $_GET['uris_count'] ) : 0;
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$uris_import_error  = isset( $_GET['uris_import_error'] ) ? sanitize_text_field( wp_unslash( $_GET['uris_import_error'] ) ) : '';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$uris_export_error  = isset( $_GET['uris_export_error'] ) ? sanitize_text_field( wp_unslash( $_GET['uris_export_error'] ) ) : '';
?>

<div class="wrap uris-admin-wrap">
	<!-- Page Header -->
	<div class="uris-header-card">
		<div class="uris-header-content">
			<div class="uris-header-badge">
				<span class="uris-badge-pulse"></span>
				<?php esc_html_e( 'Tools & Utilities', 'ultimate-responsive-image-slider' ); ?>
			</div>
			<h1 class="uris-header-title">
				<svg class="uris-icon-title" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
					<polyline points="7 10 12 15 17 10"></polyline>
					<line x1="12" y1="15" x2="12" y2="3"></line>
				</svg>
				<?php esc_html_e( 'Import & Export Sliders', 'ultimate-responsive-image-slider' ); ?>
			</h1>
			<p class="uris-header-desc">
				<?php esc_html_e( 'Easily backup, migrate, or restore your Ultimate Responsive Image Sliders in JSON format. Seamlessly transfer sliders across websites.', 'ultimate-responsive-image-slider' ); ?>
			</p>
		</div>
		<div class="uris-header-stats">
			<div class="uris-stat-box">
				<span class="uris-stat-number"><?php echo esc_html( $uris_total_sliders ); ?></span>
				<span class="uris-stat-label"><?php esc_html_e( 'Total Sliders', 'ultimate-responsive-image-slider' ); ?></span>
			</div>
		</div>
	</div>

	<!-- Notifications -->
	<?php if ( $uris_import_status === 'success' ) : ?>
		<div class="uris-alert uris-alert-success">
			<svg class="uris-alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
				<polyline points="22 4 12 14.01 9 11.01"></polyline>
			</svg>
			<div class="uris-alert-text">
				<strong><?php esc_html_e( 'Import Successful!', 'ultimate-responsive-image-slider' ); ?></strong>
				<?php
				/* translators: %d: count of imported sliders */
				printf( esc_html__( 'Successfully imported %d slider(s) into your library.', 'ultimate-responsive-image-slider' ), esc_html( $uris_imported_count ) );
				?>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=ris_gallery' ) ); ?>" class="uris-alert-link">
					<?php esc_html_e( 'View Sliders List &rarr;', 'ultimate-responsive-image-slider' ); ?>
				</a>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $uris_import_error ) : ?>
		<div class="uris-alert uris-alert-error">
			<svg class="uris-alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<circle cx="12" cy="12" r="10"></circle>
				<line x1="12" y1="8" x2="12" y2="12"></line>
				<line x1="12" y1="16" x2="12.01" y2="16"></line>
			</svg>
			<div class="uris-alert-text">
				<strong><?php esc_html_e( 'Import Failed:', 'ultimate-responsive-image-slider' ); ?></strong>
				<?php
				if ( $uris_import_error === 'no_file' ) {
					esc_html_e( 'Please select a valid JSON backup file to import.', 'ultimate-responsive-image-slider' );
				} elseif ( $uris_import_error === 'invalid_extension' ) {
					esc_html_e( 'Invalid file format. Please upload a .json file.', 'ultimate-responsive-image-slider' );
				} elseif ( $uris_import_error === 'invalid_format' ) {
					esc_html_e( 'The file does not contain valid URIS slider backup data.', 'ultimate-responsive-image-slider' );
				} else {
					esc_html_e( 'An error occurred during import. Please try again.', 'ultimate-responsive-image-slider' );
				}
				?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $uris_export_error ) : ?>
		<div class="uris-alert uris-alert-error">
			<svg class="uris-alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<circle cx="12" cy="12" r="10"></circle>
				<line x1="12" y1="8" x2="12" y2="12"></line>
				<line x1="12" y1="16" x2="12.01" y2="16"></line>
			</svg>
			<div class="uris-alert-text">
				<strong><?php esc_html_e( 'Export Error:', 'ultimate-responsive-image-slider' ); ?></strong>
				<?php
				if ( $uris_export_error === 'none_selected' ) {
					esc_html_e( 'Please select at least one slider to export.', 'ultimate-responsive-image-slider' );
				} elseif ( $uris_export_error === 'no_sliders' ) {
					esc_html_e( 'No sliders found in the database to export.', 'ultimate-responsive-image-slider' );
				} else {
					esc_html_e( 'An error occurred during export.', 'ultimate-responsive-image-slider' );
				}
				?>
			</div>
		</div>
	<?php endif; ?>

	<!-- Main 2-Column Grid -->
	<div class="uris-grid-two">
		<!-- EXPORT CARD -->
		<div class="uris-card uris-card-export">
			<div class="uris-card-header">
				<div class="uris-card-icon-wrap uris-icon-export-bg">
					<svg class="uris-card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
						<polyline points="7 10 12 15 17 10"></polyline>
						<line x1="12" y1="15" x2="12" y2="3"></line>
					</svg>
				</div>
				<div>
					<h2 class="uris-card-title"><?php esc_html_e( 'Export Sliders', 'ultimate-responsive-image-slider' ); ?></h2>
					<p class="uris-card-subtitle"><?php esc_html_e( 'Download a JSON file containing slider settings, layouts, and image metadata.', 'ultimate-responsive-image-slider' ); ?></p>
				</div>
			</div>

			<form method="post" action="" class="uris-form">
				<?php wp_nonce_field( 'uris_export_action_nonce', 'uris_export_nonce' ); ?>
				<input type="hidden" name="uris_export_slider_action" value="1" />

				<div class="uris-form-group">
					<label class="uris-form-label"><?php esc_html_e( 'Export Options', 'ultimate-responsive-image-slider' ); ?></label>
					
					<div class="uris-radio-group">
						<label class="uris-radio-item">
							<input type="radio" name="uris_export_type" value="all" checked="checked" id="uris_export_all_radio" />
							<span class="uris-radio-custom"></span>
							<span class="uris-radio-label">
								<strong><?php esc_html_e( 'Export All Sliders', 'ultimate-responsive-image-slider' ); ?></strong>
								<span class="uris-pill-count"><?php echo esc_html( $uris_total_sliders ); ?> <?php esc_html_e( 'Sliders', 'ultimate-responsive-image-slider' ); ?></span>
							</span>
						</label>

						<label class="uris-radio-item">
							<input type="radio" name="uris_export_type" value="selected" id="uris_export_selected_radio" <?php if ( $uris_total_sliders === 0 ) echo 'disabled="disabled"'; ?> />
							<span class="uris-radio-custom"></span>
							<span class="uris-radio-label">
								<strong><?php esc_html_e( 'Select Specific Sliders', 'ultimate-responsive-image-slider' ); ?></strong>
								<span class="uris-radio-subtext"><?php esc_html_e( 'Choose custom sliders from your list', 'ultimate-responsive-image-slider' ); ?></span>
							</span>
						</label>
					</div>
				</div>

				<!-- Slider Selection Checklist -->
				<div id="uris_slider_selection_box" class="uris-selection-box" style="display: none;">
					<div class="uris-selection-actions">
						<button type="button" class="button button-small" id="uris_select_all_btn"><?php esc_html_e( 'Select All', 'ultimate-responsive-image-slider' ); ?></button>
						<button type="button" class="button button-small" id="uris_deselect_all_btn"><?php esc_html_e( 'Clear All', 'ultimate-responsive-image-slider' ); ?></button>
					</div>
					<div class="uris-slider-list">
						<?php if ( ! empty( $uris_all_sliders ) ) : ?>
							<?php foreach ( $uris_all_sliders as $uris_slider ) : 
								$uris_slider_slides = get_post_meta( $uris_slider->ID, 'ris_all_photos_details', true );
								if ( is_string( $uris_slider_slides ) ) {
									$uris_slider_slides = maybe_unserialize( $uris_slider_slides );
								}
								$uris_slide_count = is_array( $uris_slider_slides ) ? count( $uris_slider_slides ) : 0;
							?>
								<label class="uris-checkbox-item">
									<input type="checkbox" name="uris_selected_sliders[]" value="<?php echo esc_attr( $uris_slider->ID ); ?>" class="uris-slider-checkbox" />
									<span class="uris-checkbox-title"><?php echo esc_html( $uris_slider->post_title ? $uris_slider->post_title : __( '(No title)', 'ultimate-responsive-image-slider' ) ); ?></span>
									<span class="uris-checkbox-shortcode"><code>[URIS id=<?php echo esc_html( $uris_slider->ID ); ?>]</code></span>
									<span class="uris-checkbox-count"><?php echo esc_html( $uris_slide_count ); ?> <?php esc_html_e( 'slides', 'ultimate-responsive-image-slider' ); ?></span>
								</label>
							<?php endforeach; ?>
						<?php else : ?>
							<p class="uris-no-items"><?php esc_html_e( 'No sliders found in your library yet.', 'ultimate-responsive-image-slider' ); ?></p>
						<?php endif; ?>
					</div>
				</div>

				<div class="uris-info-note">
					<svg class="uris-note-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<circle cx="12" cy="12" r="10"></circle>
						<line x1="12" y1="16" x2="12" y2="12"></line>
						<line x1="12" y1="8" x2="12.01" y2="8"></line>
					</svg>
					<span><?php esc_html_e( 'Backup file includes layout sizes, autoplay timing, transition styling, typography, colors, and slide image data.', 'ultimate-responsive-image-slider' ); ?></span>
				</div>

				<div class="uris-card-footer">
					<button type="submit" class="uris-btn uris-btn-export" <?php if ( $uris_total_sliders === 0 ) echo 'disabled="disabled"'; ?>>
						<svg class="uris-btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
							<polyline points="7 10 12 15 17 10"></polyline>
							<line x1="12" y1="15" x2="12" y2="3"></line>
						</svg>
						<?php esc_html_e( 'Download Export File (JSON)', 'ultimate-responsive-image-slider' ); ?>
					</button>
				</div>
			</form>
		</div>

		<!-- IMPORT CARD -->
		<div class="uris-card uris-card-import">
			<div class="uris-card-header">
				<div class="uris-card-icon-wrap uris-icon-import-bg">
					<svg class="uris-card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
						<polyline points="17 8 12 3 7 8"></polyline>
						<line x1="12" y1="3" x2="12" y2="15"></line>
					</svg>
				</div>
				<div>
					<h2 class="uris-card-title"><?php esc_html_e( 'Import Sliders', 'ultimate-responsive-image-slider' ); ?></h2>
					<p class="uris-card-subtitle"><?php esc_html_e( 'Upload a JSON backup file to restore or add sliders into your site.', 'ultimate-responsive-image-slider' ); ?></p>
				</div>
			</div>

			<form method="post" action="" enctype="multipart/form-data" class="uris-form">
				<?php wp_nonce_field( 'uris_import_action_nonce', 'uris_import_nonce' ); ?>
				<input type="hidden" name="uris_import_slider_action" value="1" />

				<div class="uris-form-group">
					<label class="uris-form-label"><?php esc_html_e( 'Select Backup File (.json)', 'ultimate-responsive-image-slider' ); ?></label>
					
					<!-- Drag & Drop Zone -->
					<div class="uris-dropzone" id="uris_dropzone">
						<input type="file" name="uris_import_file" id="uris_import_file" accept=".json" class="uris-file-input" required />
						<div class="uris-dropzone-inner">
							<div class="uris-dropzone-icon">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
									<path d="M12 12v9"></path>
									<path d="m16 16-4-4-4 4"></path>
								</svg>
							</div>
							<p class="uris-dropzone-prompt">
								<strong><?php esc_html_e( 'Click to browse', 'ultimate-responsive-image-slider' ); ?></strong>
								<?php esc_html_e( 'or drag and drop your JSON backup file here', 'ultimate-responsive-image-slider' ); ?>
							</p>
							<span class="uris-dropzone-hint"><?php esc_html_e( 'Maximum file size depends on PHP upload limit. Supported file: .json', 'ultimate-responsive-image-slider' ); ?></span>
							
							<div class="uris-file-selected" id="uris_file_selected" style="display: none;">
								<svg class="uris-file-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
									<polyline points="14 2 14 8 20 8"></polyline>
									<line x1="16" y1="13" x2="8" y2="13"></line>
									<line x1="16" y1="17" x2="8" y2="17"></line>
								</svg>
								<span id="uris_selected_filename"></span>
								<button type="button" id="uris_clear_file_btn" class="uris-clear-file">&times;</button>
							</div>
						</div>
					</div>
				</div>

				<div class="uris-info-note">
					<svg class="uris-note-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<circle cx="12" cy="12" r="10"></circle>
						<line x1="12" y1="16" x2="12" y2="12"></line>
						<line x1="12" y1="8" x2="12.01" y2="8"></line>
					</svg>
					<span><?php esc_html_e( 'Import will create new sliders without overwriting existing ones. Image attachments will be matched or sideloaded.', 'ultimate-responsive-image-slider' ); ?></span>
				</div>

				<div class="uris-card-footer">
					<button type="submit" class="uris-btn uris-btn-import" id="uris_import_submit_btn">
						<svg class="uris-btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
							<polyline points="17 8 12 3 7 8"></polyline>
							<line x1="12" y1="3" x2="12" y2="15"></line>
						</svg>
						<?php esc_html_e( 'Import Sliders Now', 'ultimate-responsive-image-slider' ); ?>
					</button>
				</div>
			</form>
		</div>
	</div>

	<!-- HELP & SUPPORT SECTION (Previous Data Shifted Downwards) -->
	<div class="uris-help-section">
		<div class="uris-section-header">
			<div class="uris-section-badge"><?php esc_html_e( 'Help & Support Desk', 'ultimate-responsive-image-slider' ); ?></div>
			<h2 class="uris-section-title"><?php esc_html_e( 'Need Assistance, Feedback or Contributions?', 'ultimate-responsive-image-slider' ); ?></h2>
			<p class="uris-section-desc"><?php esc_html_e( 'We are dedicated to providing the best image slider experience. Explore our documentation, share ideas, or help translate.', 'ultimate-responsive-image-slider' ); ?></p>
		</div>

		<div class="uris-help-grid">
			<!-- Help Card 1: Official Docs & Tickets -->
			<div class="uris-help-card">
				<div class="uris-help-icon-wrap" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<circle cx="12" cy="12" r="10"></circle>
						<path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
						<line x1="12" y1="17" x2="12.01" y2="17"></line>
					</svg>
				</div>
				<h3 class="uris-help-card-title"><?php esc_html_e( 'Support Docs or Ticket', 'ultimate-responsive-image-slider' ); ?></h3>
				<p class="uris-help-card-text">
					<?php esc_html_e( 'Having questions or trouble configuring your slider? Check out our official documentation or open a thread on the WordPress support forum.', 'ultimate-responsive-image-slider' ); ?>
				</p>
				<a href="https://wordpress.org/support/plugin/ultimate-responsive-image-slider" target="_blank" rel="noopener noreferrer" class="uris-btn-outline">
					<?php esc_html_e( 'View Support Docs or Open a Ticket', 'ultimate-responsive-image-slider' ); ?>
					<svg class="uris-external-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
						<polyline points="15 3 21 3 21 9"></polyline>
						<line x1="10" y1="14" x2="21" y2="3"></line>
					</svg>
				</a>
			</div>

			<!-- Help Card 2: Suggestions -->
			<div class="uris-help-card">
				<div class="uris-help-icon-wrap" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path>
					</svg>
				</div>
				<h3 class="uris-help-card-title"><?php esc_html_e( 'Share Us Your Suggestion', 'ultimate-responsive-image-slider' ); ?></h3>
				<p class="uris-help-card-text">
					<?php esc_html_e( 'If you have any suggestion or features in your mind, please share with us! We will try our best to add them into future updates.', 'ultimate-responsive-image-slider' ); ?>
				</p>
				<a href="mailto:farazfrank777@gmail.com?subject=Ultimate%20Responsive%20Image%20Slider%20Suggestion" class="uris-btn-outline">
					<?php esc_html_e( 'Send Your Ideas via Email', 'ultimate-responsive-image-slider' ); ?>
					<svg class="uris-external-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
						<polyline points="22,6 12,13 2,6"></polyline>
					</svg>
				</a>
			</div>

			<!-- Help Card 3: Language Contribution -->
			<div class="uris-help-card">
				<div class="uris-help-icon-wrap" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<circle cx="12" cy="12" r="10"></circle>
						<line x1="2" y1="12" x2="22" y2="12"></line>
						<path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
					</svg>
				</div>
				<h3 class="uris-help-card-title"><?php esc_html_e( 'Language Contribution', 'ultimate-responsive-image-slider' ); ?></h3>
				<p class="uris-help-card-text">
					<?php esc_html_e( 'How to translate plugin into any language? Contact us at farazfrank777@gmail.com. We will guide you and show you as a language contributor on WordPress.org.', 'ultimate-responsive-image-slider' ); ?>
				</p>
				<a href="mailto:farazfrank777@gmail.com?subject=Language%20Translation%20Contribution%20for%20URIS" class="uris-btn-outline">
					<?php esc_html_e( 'Become a Translator', 'ultimate-responsive-image-slider' ); ?>
					<svg class="uris-external-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
						<polyline points="22,6 12,13 2,6"></polyline>
					</svg>
				</a>
			</div>
		</div>
	</div>
</div>

<!-- Modern CSS Styles -->
<style>
/* Scoped URIS Modern Design System */
.uris-admin-wrap {
	max-width: 1200px;
	margin: 20px auto 40px;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
	color: #1e293b;
}

/* Header Card */
.uris-header-card {
	display: flex;
	justify-content: space-between;
	align-items: center;
	background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
	border-radius: 16px;
	padding: 32px 36px;
	color: #ffffff;
	box-shadow: 0 10px 25px -5px rgba(30, 58, 138, 0.25);
	margin-bottom: 24px;
	position: relative;
	overflow: hidden;
}
.uris-header-card::after {
	content: "";
	position: absolute;
	right: -40px;
	top: -40px;
	width: 220px;
	height: 220px;
	border-radius: 50%;
	background: rgba(255, 255, 255, 0.08);
	pointer-events: none;
}
.uris-header-badge {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	background: rgba(255, 255, 255, 0.18);
	backdrop-filter: blur(8px);
	padding: 4px 12px;
	border-radius: 20px;
	font-size: 12px;
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	margin-bottom: 12px;
}
.uris-badge-pulse {
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: #34d399;
	box-shadow: 0 0 0 2px rgba(52, 211, 153, 0.4);
}
.uris-header-title {
	display: flex;
	align-items: center;
	gap: 12px;
	font-size: 28px;
	font-weight: 800;
	color: #ffffff !important;
	margin: 0 0 10px 0;
	padding: 0;
	line-height: 1.2;
}
.uris-icon-title {
	width: 32px;
	height: 32px;
}
.uris-header-desc {
	font-size: 15px;
	color: #e0e7ff;
	margin: 0;
	max-width: 650px;
	line-height: 1.5;
}
.uris-header-stats {
	display: flex;
	gap: 16px;
}
.uris-stat-box {
	background: rgba(255, 255, 255, 0.15);
	backdrop-filter: blur(10px);
	border: 1px solid rgba(255, 255, 255, 0.2);
	border-radius: 12px;
	padding: 16px 24px;
	text-align: center;
	min-width: 100px;
}
.uris-stat-number {
	display: block;
	font-size: 32px;
	font-weight: 800;
	color: #ffffff;
	line-height: 1;
}
.uris-stat-label {
	display: block;
	font-size: 12px;
	font-weight: 600;
	color: #c7d2fe;
	margin-top: 6px;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

/* Alerts */
.uris-alert {
	display: flex;
	align-items: flex-start;
	gap: 14px;
	padding: 16px 20px;
	border-radius: 12px;
	margin-bottom: 24px;
	font-size: 14px;
	line-height: 1.5;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.uris-alert-icon {
	width: 22px;
	height: 22px;
	flex-shrink: 0;
	margin-top: 1px;
}
.uris-alert-success {
	background: #ecfdf5;
	border: 1px solid #a7f3d0;
	color: #065f46;
}
.uris-alert-success .uris-alert-icon {
	color: #059669;
}
.uris-alert-error {
	background: #fef2f2;
	border: 1px solid #fecaca;
	color: #991b1b;
}
.uris-alert-error .uris-alert-icon {
	color: #dc2626;
}
.uris-alert-link {
	color: #059669;
	font-weight: 700;
	margin-left: 8px;
	text-decoration: underline;
}

/* Two Column Grid */
.uris-grid-two {
	display: grid;
	grid-template-columns: repeat(2, 1fr);
	gap: 24px;
	margin-bottom: 36px;
}
@media (max-width: 900px) {
	.uris-grid-two {
		grid-template-columns: 1fr;
	}
}

/* Cards */
.uris-card {
	background: #ffffff;
	border-radius: 16px;
	border: 1px solid #e2e8f0;
	box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
	display: flex;
	flex-direction: column;
	overflow: hidden;
	transition: all 0.2s ease;
}
.uris-card:hover {
	box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
	border-color: #cbd5e1;
}
.uris-card-header {
	display: flex;
	align-items: center;
	gap: 16px;
	padding: 24px 28px;
	border-bottom: 1px solid #f1f5f9;
	background: #fafafa;
}
.uris-card-icon-wrap {
	width: 48px;
	height: 48px;
	border-radius: 12px;
	display: flex;
	align-items: center;
	justify-content: center;
	flex-shrink: 0;
}
.uris-icon-export-bg {
	background: #eff6ff;
	color: #2563eb;
}
.uris-icon-import-bg {
	background: #ecfdf5;
	color: #059669;
}
.uris-card-icon {
	width: 24px;
	height: 24px;
}
.uris-card-title {
	font-size: 20px;
	font-weight: 700;
	color: #0f172a;
	margin: 0 0 4px 0;
	padding: 0;
}
.uris-card-subtitle {
	font-size: 13px;
	color: #64748b;
	margin: 0;
}

/* Form Elements */
.uris-form {
	display: flex;
	flex-direction: column;
	flex: 1;
	padding: 24px 28px;
}
.uris-form-group {
	margin-bottom: 20px;
	flex: 1;
}
.uris-form-label {
	display: block;
	font-size: 13px;
	font-weight: 700;
	color: #334155;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	margin-bottom: 12px;
}

/* Radio Group */
.uris-radio-group {
	display: flex;
	flex-direction: column;
	gap: 10px;
}
.uris-radio-item {
	display: flex;
	align-items: center;
	padding: 12px 16px;
	border: 1.5px solid #e2e8f0;
	border-radius: 10px;
	cursor: pointer;
	transition: all 0.2s ease;
	background: #ffffff;
}
.uris-radio-item:hover {
	border-color: #3b82f6;
	background: #f8fafc;
}
.uris-radio-item input[type="radio"] {
	margin-right: 12px;
	accent-color: #2563eb;
}
.uris-radio-label {
	display: flex;
	align-items: center;
	justify-content: space-between;
	width: 100%;
	font-size: 14px;
	color: #1e293b;
}
.uris-pill-count {
	background: #eff6ff;
	color: #2563eb;
	font-size: 11px;
	font-weight: 700;
	padding: 2px 8px;
	border-radius: 12px;
}
.uris-radio-subtext {
	font-size: 12px;
	color: #64748b;
	font-weight: normal;
}

/* Checklist box */
.uris-selection-box {
	margin-top: 14px;
	border: 1px solid #e2e8f0;
	border-radius: 10px;
	background: #f8fafc;
	padding: 12px;
}
.uris-selection-actions {
	display: flex;
	gap: 8px;
	margin-bottom: 10px;
}
.uris-slider-list {
	max-height: 180px;
	overflow-y: auto;
	display: flex;
	flex-direction: column;
	gap: 6px;
}
.uris-checkbox-item {
	display: flex;
	align-items: center;
	padding: 8px 12px;
	background: #ffffff;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	cursor: pointer;
	font-size: 13px;
	transition: all 0.15s ease;
}
.uris-checkbox-item:hover {
	background: #f1f5f9;
}
.uris-checkbox-item input {
	margin-right: 10px;
}
.uris-checkbox-title {
	font-weight: 600;
	color: #1e293b;
	flex: 1;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	margin-right: 8px;
}
.uris-checkbox-shortcode {
	margin-right: 10px;
}
.uris-checkbox-shortcode code {
	font-size: 11px;
	background: #e2e8f0;
	color: #475569;
	padding: 2px 6px;
	border-radius: 4px;
}
.uris-checkbox-count {
	font-size: 11px;
	color: #64748b;
}
.uris-no-items {
	margin: 10px;
	font-size: 13px;
	color: #64748b;
	text-align: center;
}

/* Drag & Drop Area */
.uris-dropzone {
	position: relative;
	border: 2px dashed #cbd5e1;
	border-radius: 12px;
	background: #f8fafc;
	padding: 32px 20px;
	text-align: center;
	transition: all 0.2s ease;
	cursor: pointer;
}
.uris-dropzone:hover, .uris-dropzone.uris-dragover {
	border-color: #059669;
	background: #f0fdf4;
}
.uris-file-input {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	opacity: 0;
	cursor: pointer;
	z-index: 5;
}
.uris-dropzone-icon svg {
	width: 44px;
	height: 44px;
	color: #94a3b8;
	margin-bottom: 8px;
	transition: color 0.2s ease;
}
.uris-dropzone:hover .uris-dropzone-icon svg {
	color: #059669;
}
.uris-dropzone-prompt {
	font-size: 14px;
	color: #334155;
	margin: 0 0 6px 0;
}
.uris-dropzone-prompt strong {
	color: #059669;
}
.uris-dropzone-hint {
	display: block;
	font-size: 12px;
	color: #94a3b8;
}
.uris-file-selected {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	background: #ecfdf5;
	border: 1px solid #a7f3d0;
	color: #065f46;
	padding: 8px 16px;
	border-radius: 20px;
	font-size: 13px;
	font-weight: 600;
	margin-top: 14px;
	position: relative;
	z-index: 10;
}
.uris-file-icon {
	width: 16px;
	height: 16px;
}
.uris-clear-file {
	background: none;
	border: none;
	color: #065f46;
	font-size: 18px;
	cursor: pointer;
	line-height: 1;
	padding: 0;
}

/* Info Note */
.uris-info-note {
	display: flex;
	align-items: flex-start;
	gap: 10px;
	background: #f8fafc;
	border-left: 3px solid #64748b;
	padding: 12px 14px;
	border-radius: 0 8px 8px 0;
	font-size: 12px;
	color: #475569;
	margin-bottom: 24px;
	line-height: 1.5;
}
.uris-note-icon {
	width: 16px;
	height: 16px;
	flex-shrink: 0;
	margin-top: 2px;
	color: #64748b;
}

/* Card Footer & Action Buttons */
.uris-card-footer {
	margin-top: auto;
}
.uris-btn {
	width: 100%;
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 10px;
	padding: 14px 20px;
	border-radius: 10px;
	font-size: 15px;
	font-weight: 700;
	cursor: pointer;
	border: none;
	transition: all 0.2s ease;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
.uris-btn-icon {
	width: 20px;
	height: 20px;
}
.uris-btn-export {
	background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
	color: #ffffff;
}
.uris-btn-export:hover:not(:disabled) {
	background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
	transform: translateY(-1px);
	box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
}
.uris-btn-import {
	background: linear-gradient(135deg, #059669 0%, #047857 100%);
	color: #ffffff;
}
.uris-btn-import:hover:not(:disabled) {
	background: linear-gradient(135deg, #047857 0%, #065f46 100%);
	transform: translateY(-1px);
	box-shadow: 0 6px 18px rgba(5, 150, 105, 0.35);
}
.uris-btn:disabled {
	opacity: 0.55;
	cursor: not-allowed;
	transform: none !important;
	box-shadow: none !important;
}

/* HELP & SUPPORT SECTION */
.uris-help-section {
	background: #ffffff;
	border-radius: 16px;
	border: 1px solid #e2e8f0;
	padding: 36px 32px;
	box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
}
.uris-section-header {
	text-align: center;
	max-width: 650px;
	margin: 0 auto 32px;
}
.uris-section-badge {
	display: inline-block;
	background: #f1f5f9;
	color: #475569;
	font-size: 11px;
	font-weight: 700;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	padding: 4px 12px;
	border-radius: 20px;
	margin-bottom: 8px;
}
.uris-section-title {
	font-size: 24px;
	font-weight: 800;
	color: #0f172a;
	margin: 0 0 10px 0;
	padding: 0;
}
.uris-section-desc {
	font-size: 14px;
	color: #64748b;
	margin: 0;
	line-height: 1.5;
}
.uris-help-grid {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 24px;
}
@media (max-width: 850px) {
	.uris-help-grid {
		grid-template-columns: 1fr;
	}
}
.uris-help-card {
	background: #f8fafc;
	border: 1px solid #e2e8f0;
	border-radius: 14px;
	padding: 24px;
	display: flex;
	flex-direction: column;
	text-align: left;
	transition: all 0.2s ease;
}
.uris-help-card:hover {
	background: #ffffff;
	border-color: #cbd5e1;
	transform: translateY(-2px);
	box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}
.uris-help-icon-wrap {
	width: 44px;
	height: 44px;
	border-radius: 10px;
	display: flex;
	align-items: center;
	justify-content: center;
	margin-bottom: 16px;
}
.uris-help-icon-wrap svg {
	width: 22px;
	height: 22px;
}
.uris-help-card-title {
	font-size: 17px;
	font-weight: 700;
	color: #0f172a;
	margin: 0 0 8px 0;
}
.uris-help-card-text {
	font-size: 13px;
	color: #64748b;
	line-height: 1.6;
	margin: 0 0 20px 0;
	flex: 1;
}
.uris-btn-outline {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 8px;
	padding: 10px 16px;
	border: 1.5px solid #cbd5e1;
	background: #ffffff;
	color: #334155 !important;
	font-size: 13px;
	font-weight: 600;
	border-radius: 8px;
	text-decoration: none !important;
	transition: all 0.2s ease;
}
.uris-btn-outline:hover {
	border-color: #2563eb;
	color: #2563eb !important;
	background: #eff6ff;
}
.uris-external-icon {
	width: 14px;
	height: 14px;
	opacity: 0.7;
}
</style>

<!-- UI Interactions -->
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function() {
	// Toggle Specific Sliders Box
	const allRadio = document.getElementById("uris_export_all_radio");
	const selectedRadio = document.getElementById("uris_export_selected_radio");
	const selectionBox = document.getElementById("uris_slider_selection_box");

	function toggleSelectionBox() {
		if (selectedRadio && selectedRadio.checked) {
			selectionBox.style.display = "block";
		} else if (selectionBox) {
			selectionBox.style.display = "none";
		}
	}

	if (allRadio && selectedRadio) {
		allRadio.addEventListener("change", toggleSelectionBox);
		selectedRadio.addEventListener("change", toggleSelectionBox);
	}

	// Select / Deselect All
	const selectAllBtn = document.getElementById("uris_select_all_btn");
	const deselectAllBtn = document.getElementById("uris_deselect_all_btn");
	const checkboxes = document.querySelectorAll(".uris-slider-checkbox");

	if (selectAllBtn) {
		selectAllBtn.addEventListener("click", function() {
			checkboxes.forEach(cb => cb.checked = true);
		});
	}
	if (deselectAllBtn) {
		deselectAllBtn.addEventListener("click", function() {
			checkboxes.forEach(cb => cb.checked = false);
		});
	}

	// Drag & Drop / File Input Handling
	const fileInput = document.getElementById("uris_import_file");
	const dropzone = document.getElementById("uris_dropzone");
	const fileSelectedBox = document.getElementById("uris_file_selected");
	const filenameSpan = document.getElementById("uris_selected_filename");
	const clearFileBtn = document.getElementById("uris_clear_file_btn");

	if (fileInput) {
		fileInput.addEventListener("change", function(e) {
			if (this.files && this.files.length > 0) {
				const file = this.files[0];
				if (filenameSpan) filenameSpan.textContent = file.name + " (" + (file.size / 1024).toFixed(1) + " KB)";
				if (fileSelectedBox) fileSelectedBox.style.display = "inline-flex";
			}
		});
	}

	if (clearFileBtn) {
		clearFileBtn.addEventListener("click", function(e) {
			e.preventDefault();
			e.stopPropagation();
			if (fileInput) fileInput.value = "";
			if (fileSelectedBox) fileSelectedBox.style.display = "none";
		});
	}

	if (dropzone) {
		['dragenter', 'dragover'].forEach(eventName => {
			dropzone.addEventListener(eventName, function(e) {
				e.preventDefault();
				e.stopPropagation();
				dropzone.classList.add('uris-dragover');
			}, false);
		});
		['dragleave', 'drop'].forEach(eventName => {
			dropzone.addEventListener(eventName, function(e) {
				e.preventDefault();
				e.stopPropagation();
				dropzone.classList.remove('uris-dragover');
			}, false);
		});
	}
});
</script>
