<?php
/**
 * Portfolio import settings.
 */

namespace OpenLab\Portfolio\Import;

use const OpenLab\Portfolio\ROOT_DIR;
use const OpenLab\Portfolio\ROOT_FILE;
use WP_Error;
use OpenLab\Portfolio\Contracts\Registerable;
use OpenLab\Portfolio\Logger\ServerSentEventsLogger;

class Service implements Registerable {

	const STEP_UPLOAD = 0;

	const STEP_SETTINGS = 1;

	const STEP_IMPORT = 2;

	/**
	 * Register our settings page.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', [ $this, 'register_page' ] );
		add_action( 'wp_ajax_ol-portfolio-import', [ $this, 'stream_import' ] );
	}

	/**
	 * Register custom tools page.
	 *
	 * @return void
	 */
	public function register_page() {
		$type = openlab_get_site_type( get_current_blog_id() );

		if ( 'portfolio' !== $type ) {
			return;
		}

		add_submenu_page(
			'tools.php',
			'Import Portfolio',
			'Import Portfolio',
			'import',
			'import_portfolio',
			[ $this, 'render' ]
		);
	}

	/**
	 * Get URL for the importer.
	 *
	 * @param int $step
	 * @return string
	 */
	public function get_url( $step = 0 ) {
		$path = 'admin.php?page=import_portfolio';

		if ( $step ) {
			$path = add_query_arg( 'step', (int) $step, $path );
		}

		return admin_url( $path );
	}

	/**
	 * Display import process errors.
	 *
	 * @param WP_Error $error
	 * @return void
	 */
	protected function display_error( WP_Error $error ) {
		extract( [ 'error' => $error ], EXTR_SKIP );
		require ROOT_DIR . '/views/import/error.php';
	}

	/**
	 * Enqueue import assets.
	 *
	 * @return void
	 */
	protected function enqueue_assets( $step = 0 ) {
		wp_enqueue_style(
			'ol-portfolio-import-styles',
			plugins_url( 'assets/css/import.css', ROOT_FILE ),
			[],
			'20190808'
		);

		if ( $step !== static::STEP_IMPORT ) {
			return;
		}

		$args = [
			'action' => 'ol-portfolio-import',
			'id'     => (int) $_POST['import_id'],
		];

		$script_data = [
			'url' => add_query_arg( urlencode_deep( $args ), admin_url( 'admin-ajax.php' ) ),
			'strings' => [
				'complete' => 'Step 3: Import Complete. Check out your site!',
				'error'    => 'Import unsuccessful. <a href="https://openlab.citytech.cuny.edu/blog/help/contact-us/">Contact the OpenLab team</a> for support.',
			],
		];

		$url = plugins_url( 'assets/js/import.js', ROOT_FILE );
		wp_enqueue_script( 'ol-portfolio-import', $url, [ 'jquery' ], '20190723', true );
		wp_localize_script( 'ol-portfolio-import', 'ImportData', $script_data );
	}

	/**
	 * Render the import page.
	 *
	 * @return void
	 */
	public function render() {
		$step = empty( $_GET['step'] ) ? static::STEP_UPLOAD : (int) $_GET['step'];

		$this->enqueue_assets( $step );

		switch ( $step ) {
			case static::STEP_UPLOAD:
				$this->render_upload_step();
				break;
			case static::STEP_SETTINGS:
				$this->render_settings_step();
				break;
			case static::STEP_IMPORT:
				$this->render_import_step();
				break;
		}
	}

	/**
	 * Render import header.
	 *
	 * @return void
	 */
	public function render_header() {
		require ROOT_DIR . '/views/import/header.php';
	}

	/**
	 * Render import upload screen.
	 *
	 * @return void
	 */
	public function render_upload_step() {
		require ROOT_DIR . '/views/import/upload.php';
	}

	/**
	 * Render import settings screen.
	 *
	 * @return void
	 */
	public function render_settings_step() {
		$upload = $this->handle_upload();

		if ( is_wp_error( $upload ) ) {
			$this->display_error( $upload );
			return;
		}

		require ROOT_DIR . '/views/import/settings.php';
	}

	/**
	 * Render import progress screen.
	 *
	 * @return void
	 */
	public function render_import_step() {
		$args = wp_unslash( $_POST );
		if ( ! isset( $args['import_id'] ) ) {
			// Missing import ID.
			$error = new WP_Error( 'ol.portfolio.import.missing_id', 'Missing import file ID from request.' );
			$this->display_error( $error );
			return;
		}

		check_admin_referer( sprintf( 'portfolio.import:%d', (int) $args['import_id'] ) );

		require ROOT_DIR . '/views/import/import.php';
	}

	/**
	 * Handles archvie upload.
	 *
	 * @return WP_Error|bool
	 */
	protected function handle_upload() {
		check_admin_referer( 'import-upload' );

		$uploader = new ArchiveUpload( 'importzip' );
		$id       = $uploader->handle();

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$this->id = $id;

		return true;
	}

	/**
	 * Run an import, and send an event-stream response.
	 *
	 * @return void
	 */
	public function stream_import() {
		// Turn off PHP output compression
		$previous = error_reporting( error_reporting() ^ E_WARNING );
		ini_set( 'output_buffering', 'off' );
		ini_set( 'zlib.output_compression', false );
		error_reporting( $previous );

		if ( $GLOBALS['is_nginx'] ) {
			// Setting this header instructs Nginx to disable fastcgi_buffering
			// and disable gzip for this request.
			header( 'X-Accel-Buffering: no' );
			header( 'Content-Encoding: none' );
		}

		// Start the event stream.
		header( 'Content-Type: text/event-stream' );

		$this->id = wp_unslash( (int) $_REQUEST['id'] );

		if ( ! isset( $this->id ) ) {
			// Tell the browser to stop reconnecting.
			status_header( 204 );
			exit;
		}

		// 2KB padding for IE
		echo ':' . str_repeat( ' ', 2048 ) . "\n\n";

		// Time to run the import!
		set_time_limit( 0 );

		// Ensure we're not buffered.
		wp_ob_end_flush_all();
		flush();

		$decompressor = new Decompressor( $this->id );
		$extract_path = $decompressor->extract();

		if ( is_wp_error( $extract_path ) ) {
			$this->emit_sse_message( [
				'action' => 'complete',
				'error'  => $extract_path->get_error_message(),
			] );
			exit;
		}

		// Skip processing author data.
		add_filter( 'wxr_importer.pre_process.user', '__return_null' );

		$importer = $this->get_importer( $extract_path );
		$status   = $importer->import( $extract_path . '/wordpress.xml' );

		// Clean up.
		$decompressor->cleanup();
		unset( $this->id );

		// Let the browser know we're done.
		$complete = [
			'action' => 'complete',
			'error'  => false,
		];

		if ( is_wp_error( $status ) ) {
			$complete['error'] = $status->get_error_message();
		}

		$this->emit_sse_message( $complete );
		exit;
	}

	/**
	 * Get the importer instance.
	 *
	 * @param string $extract_path
	 * @return Importer
	 */
	protected function get_importer( $extract_path ) {
		$options = [
			'fetch_attachments'     => true,
			'aggressive_url_search' => true,
			'default_author'        => get_current_user_id(),
		];

		$importer = new Importer( $options, $extract_path );
		$logger   = new ServerSentEventsLogger;
		$importer->set_logger( $logger );

		return $importer;
	}

	/**
	 * Emit a Server-Sent Events message.
	 *
	 * @param mixed $data Data to be JSON-encoded and sent in the message.
	 */
	protected function emit_sse_message( $data ) {
		echo "event: message\n";
		echo 'data: ' . wp_json_encode( $data ) . "\n\n";

		// Extra padding.
		echo ':' . str_repeat( ' ', 2048 ) . "\n\n";

		flush();
	}
}
