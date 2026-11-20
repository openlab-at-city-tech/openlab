<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// The tool's page, form posts and URL-parameter batching for export and import.
class TRP_Import_Export {

    const PAGE_SLUG  = 'trp_import_export';
    const NONCE_NAME = 'trp_import_export';

    protected $settings;

    public function __construct( $settings ) {
        $this->settings = $settings;
    }

    protected function get_capability() {
        return apply_filters( 'trp_settings_capability', 'manage_options' );
    }

    protected function get_time_budget() {
        return (float) apply_filters( 'trp_import_export_time_budget', 2 );
    }

    // The loader calls this only on the tool's page.
    public function enqueue_scripts_and_styles() {
        // Re-enqueued under the handle TRP_Settings uses, so settings-header.php and the shared classes render the same here.
        wp_enqueue_style( 'trp-settings-style', TRP_PLUGIN_URL . 'assets/css/trp-back-end-style.css', array(), TRP_PLUGIN_VERSION );
        wp_enqueue_style( 'trp-import-export-css', TRP_PLUGIN_URL . 'assets/css/trp-import-export.css', array( 'trp-settings-style' ), TRP_PLUGIN_VERSION );
        wp_enqueue_script( 'trp-import-export-js', TRP_PLUGIN_URL . 'assets/js/trp-import-export.js', array(), TRP_PLUGIN_VERSION, true );
    }

    protected function format_time( $timestamp ) {
        return wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $timestamp );
    }

    protected function get_download_url( $job ) {
        return add_query_arg(
            array(
                'action' => 'trp_ie_download',
                'job_id' => $job->id,
                'nonce'  => wp_create_nonce( self::NONCE_NAME ),
            ),
            admin_url( 'admin-post.php' )
        );
    }

    public function render_page() {
        if ( ! current_user_can( $this->get_capability() ) ) {
            wp_die( esc_html__( 'You do not have permission to access this page.', 'translatepress-multilingual' ) );
        }

        TRP_Import_Export_Job::cleanup_stale();
        $export_job = TRP_Import_Export_Job::load( 'export' );
        $import_job = TRP_Import_Export_Job::load( 'import' );
        $run        = isset( $_GET['trp_ie_run'] ) ? sanitize_key( wp_unslash( $_GET['trp_ie_run'] ) ) : '';
        // A run URL renders while its job is in progress, so only the plain page cleans up.
        $stopped = ( $run === '' ) ? TRP_Import_Export_Job::delete_unfinished() : array();

        $export = array( 'view' => 'idle' );
        if ( $export_job && $export_job->status === 'ready' ) {
            $export = array(
                'view'         => 'ready',
                'keys'         => isset( $export_job->options['keys'] ) ? TRP_Import_Export_Writer::get_key_list( $export_job->options['keys'] ) : '',
                'file_name'    => $export_job->file,
                'file_size'    => $export_job->file_size,
                'download_url' => $this->get_download_url( $export_job ),
            );
        } elseif ( $export_job && $export_job->status === 'failed' ) {
            // Shown once: delete_unfinished() above already removed the job.
            $export = array( 'view' => 'error', 'message' => $export_job->summary['error'] );
        } elseif ( $export_job && $run === 'export' ) {
            $export = array( 'view' => 'progress', 'job' => $export_job );
        }

        $import = array( 'view' => 'upload', 'message' => $this->pull_import_notice() );
        if ( $import_job && $import_job->status === 'running' && $run === 'import' ) {
            $import = array( 'view' => 'progress', 'job' => $import_job );
        } elseif ( $import_job && $import_job->status === 'analyzed' ) {
            $import = array( 'view' => 'review', 'preview' => $import_job->preview, 'message' => $import['message'] );
        } elseif ( $import_job && $import_job->status === 'done' ) {
            // Shown once, like a failed job: delete_unfinished() above already removed it.
            $import = array( 'view' => 'report', 'report' => $this->get_import_summary( $import_job ) );
        } elseif ( $import_job && $import_job->status === 'failed' ) {
            $import['message'] = $this->get_import_failed_message( $import_job );
        }

        // The "include the keys" checkbox names the keys this site has, and is left out when it has none.
        $export['site_keys'] = TRP_Import_Export_Writer::get_key_list( ( new TRP_Import_Export_Exporter( $this->settings ) )->get_keys() );

        // The page was left while a job was running.
        if ( in_array( 'export', $stopped, true ) ) {
            $export = array( 'view' => 'error', 'site_keys' => $export['site_keys'], 'message' => __( 'The export was stopped before it finished, because the page was left while it was running. Generate the export file again.', 'translatepress-multilingual' ) );
        }
        if ( in_array( 'import', $stopped, true ) ) {
            $import['message'] = __( 'The import was stopped before it finished, because the page was left while it was running. The translations imported so far are kept. Upload the file again to import the rest.', 'translatepress-multilingual' );
        }

        $sources = array();
        foreach ( TRP_Import_Source_Registry::get_sources() as $slug => $source ) {
            $sources[ $slug ] = array( 'name' => $source->get_name(), 'available' => (bool) $source->is_available() );
        }

        $max_upload_size = (int) wp_max_upload_size();

        require TRP_PLUGIN_DIR . 'partials/import-export-page.php';
    }

    // The languages table with row counts, fetched when "What the export file contains" is opened.
    protected function render_export_summary() {
        $export_summary = ( new TRP_Import_Export_Exporter( $this->settings ) )->get_export_summary();
        require TRP_PLUGIN_DIR . 'partials/import-export-summary.php';
        exit;
    }

    protected function get_busy_message() {
        return __( 'The tool is busy. Try again in a few seconds.', 'translatepress-multilingual' );
    }

    protected function get_page_url() {
        return admin_url( 'admin.php?page=' . self::PAGE_SLUG );
    }

    // The page loads this URL again until the job is finished.
    protected function get_run_url( $job ) {
        return add_query_arg(
            array(
                'page'       => self::PAGE_SLUG,
                'trp_ie_run' => $job->type,
                'job'        => $job->id,
                '_wpnonce'   => wp_create_nonce( self::NONCE_NAME ),
            ),
            admin_url( 'admin.php' )
        );
    }

    // The page script follows the redirect itself when it asked for JSON.
    protected function redirect( $url ) {
        if ( isset( $_GET['trp_ie_fetch'] ) ) {//phpcs:ignore
            wp_send_json( array( 'redirect' => $url ) );
        }
        wp_safe_redirect( $url );
        exit;
    }

    public function handle_request() {
        $action = isset( $_POST['trp_ie_action'] ) ? sanitize_key( wp_unslash( $_POST['trp_ie_action'] ) ) : '';
        $run    = isset( $_GET['trp_ie_run'] ) ? sanitize_key( wp_unslash( $_GET['trp_ie_run'] ) ) : '';
        if ( $action === '' && $run === '' && ! isset( $_GET['trp_ie_summary'] ) ) {
            // A body larger than post_max_size reaches PHP as an empty POST.
            if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' && empty( $_POST ) && empty( $_FILES ) ) {
                $this->redirect_with_import_notice( $this->get_too_large_message() );
            }
            return;
        }

        check_admin_referer( self::NONCE_NAME );
        if ( ! current_user_can( $this->get_capability() ) ) {
            wp_die( esc_html__( 'You do not have permission to perform this action.', 'translatepress-multilingual' ), '', array( 'response' => 403 ) );
        }

        if ( isset( $_GET['trp_ie_summary'] ) ) {
            $this->render_export_summary();
        }
        if ( $action === 'export_start' ) {
            $this->start_export();
        }
        if ( $action === 'import_upload' ) {
            $this->upload_import();
        }
        if ( $action === 'import_start' ) {
            $this->start_import();
        }
        if ( $action === 'import_cancel' ) {
            $job = TRP_Import_Export_Job::load( 'import' );
            if ( $job && $job->status === 'analyzed' ) {
                $job->delete();
            }
            $this->redirect( $this->get_page_url() );
        }
        if ( $run === 'export' || $run === 'import' ) {
            $job_id = isset( $_GET['job'] ) ? sanitize_text_field( wp_unslash( $_GET['job'] ) ) : '';
            $job    = TRP_Import_Export_Job::load( $run );
            if ( ! $job || $job->id !== $job_id || $job->status !== 'running' ) {
                $this->redirect( $this->get_page_url() );
            }
            // The plain run URL only renders the progress; the page script fetches the batches.
            if ( ! isset( $_GET['trp_ie_fetch'] ) ) {
                return;
            }
            if ( TRP_Import_Export_Job::acquire_lock( $run ) ) {
                $run === 'export' ? $this->run_export( $job ) : $this->run_import( $job );
            }
            wp_send_json( $this->get_progress( $job ) );
        }
    }

    protected function start_export() {
        if ( ! TRP_Import_Export_Job::acquire_lock( 'export' ) ) {
            wp_die( esc_html( $this->get_busy_message() ), '', array( 'response' => 409, 'back_link' => true ) );
        }
        $job = TRP_Import_Export_Job::start_new( 'export' );
        if ( is_wp_error( $job ) ) {
            wp_die( esc_html( $job->get_error_message() ), '', array( 'back_link' => true ) );
        }
        $job->options = array( 'include_keys' => ( isset( $_POST['include_keys'] ) && $_POST['include_keys'] === 'yes' ) ? 'yes' : 'no' );//phpcs:ignore
        if ( $job->options['include_keys'] === 'yes' ) {
            // Which keys the file will hold, without their values, for the notice next to the finished file.
            $job->options['keys'] = array_fill_keys( array_keys( ( new TRP_Import_Export_Exporter( $this->settings ) )->get_keys() ), 'yes' );
        }
        $job->save();
        $this->redirect( $this->get_run_url( $job ) );
    }

    // Export batches for one time budget.
    protected function run_export( $job ) {
        $exporter = new TRP_Import_Export_Exporter( $this->settings );
        // Building the steps counts every table, so the first fetch does only that.
        if ( ! $job->steps ) {
            $job->steps = $exporter->build_steps();
            $job->save();
            return;
        }
        $start    = microtime( true );
        do {
            $result = $exporter->process_batch( $job );
            if ( is_wp_error( $result ) ) {
                $job->status  = 'failed';
                $job->summary = array( 'error' => $result->get_error_message() );
                $job->save();
                $this->redirect( $this->get_page_url() );
            }
            if ( $result['done'] && ! $job->advance_step() ) {
                $job->status = 'ready';
                $job->save();
                $this->redirect( $this->get_page_url() );
            }
            $job->save();
        } while ( ( microtime( true ) - $start ) < $this->get_time_budget() );
    }

    protected function get_progress( $job ) {
        if ( ! $job->steps ) {
            return array( 'percent' => 0, 'label' => __( 'Counting the translations…', 'translatepress-multilingual' ), 'counter' => '' );
        }
        $step = $job->get_current_step();
        return array(
            'percent' => (int) $job->get_percent(),
            'label'   => $step['label'],
            // Slug rows are originals, while the page counts translated slugs: no counter for that step.
            'counter' => ( $step['total'] > 1 && $step['key'] !== 'slugs' )
                /* translators: 1: rows done, 2: total rows. */
                ? sprintf( __( '%1$s of %2$s', 'translatepress-multilingual' ), number_format_i18n( $step['processed'] ), number_format_i18n( $step['total'] ) )
                : '',
        );
    }

    // The progress bar of a running job; the page script runs the batches and updates it.
    protected function render_progress( $job ) {
        $progress = $this->get_progress( $job );
        ?>
        <div class="trp-ie-progress__wrapper" data-trp-ie-run="<?php echo esc_url( add_query_arg( 'trp_ie_fetch', 1, $this->get_run_url( $job ) ) ); ?>" data-trp-ie-error="<?php esc_attr_e( 'The connection to the server was lost. Reload the page to continue.', 'translatepress-multilingual' ); ?>">
            <div class="trp-ie-progress" role="progressbar" aria-label="<?php echo esc_attr( $progress['label'] ); ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo (int) $progress['percent']; ?>">
                <div class="trp-ie-progress__bar" style="width: <?php echo (int) $progress['percent']; ?>%"></div>
            </div>
            <div class="trp-ie-progress__meta">
                <span class="trp-primary-text"><span class="spinner is-active"></span><span class="trp-ie-progress__label"><?php echo esc_html( $progress['label'] ); ?></span></span>
                <span class="trp-description-text trp-ie-progress__counter"><?php echo esc_html( $progress['counter'] ); ?></span>
            </div>
            <noscript><p><?php esc_html_e( 'JavaScript is needed to run this.', 'translatepress-multilingual' ); ?></p></noscript>
        </div>
        <?php
    }

    protected function get_import_notice_key() {
        return 'trp_ie_import_notice_' . get_current_user_id();
    }

    // A message for the import card, shown on the next page load only.
    protected function redirect_with_import_notice( $message ) {
        set_transient( $this->get_import_notice_key(), $message, 10 * MINUTE_IN_SECONDS );
        $this->redirect( $this->get_page_url() );
    }

    protected function pull_import_notice() {
        $message = get_transient( $this->get_import_notice_key() );
        if ( $message === false ) {
            return '';
        }
        delete_transient( $this->get_import_notice_key() );
        return $message;
    }

    protected function get_too_large_message() {
        /* translators: %s is a file size, e.g. 64 MB. */
        return sprintf( __( 'The uploaded file is larger than the maximum upload size allowed on this server (%s).', 'translatepress-multilingual' ), size_format( wp_max_upload_size() ) );
    }

    protected function get_import_failed_message( $job ) {
        /* translators: %s is the step that was running. */
        $message = sprintf( __( 'The import stopped before it finished this step: %s.', 'translatepress-multilingual' ), $job->summary['failed_step'] ) . ' ' . $job->summary['error'];
        if ( ! empty( $job->summary['settings_done'] ) ) {
            $changes  = $job->preview['variants']['with']['changes'];
            $message .= ' ' . __( 'The settings were already imported.', 'translatepress-multilingual' ) . ( $changes ? ' ' . implode( '. ', $changes ) . '.' : '' );
        }
        return $message;
    }

    // The source validates the upload; the job waits as 'analyzed' for the review.
    protected function upload_import() {
        $slug      = isset( $_POST['source'] ) ? sanitize_key( wp_unslash( $_POST['source'] ) ) : '';
        $source    = TRP_Import_Source_Registry::get_source( $slug );
        $file_name = isset( $_FILES['import_file']['name'] ) ? sanitize_file_name( wp_unslash( $_FILES['import_file']['name'] ) ) : '';
        if ( ! $source || ! $source->is_available() ) {
            $this->redirect_with_import_notice( __( 'This kind of import isn\'t available on this site.', 'translatepress-multilingual' ) );
        }
        if ( ! TRP_Import_Export_Job::acquire_lock( 'import' ) ) {
            wp_die( esc_html( $this->get_busy_message() ), '', array( 'response' => 409, 'back_link' => true ) );
        }

        $request = array();
        foreach ( $_POST as $key => $value ) {//phpcs:ignore
            if ( is_string( $value ) ) {
                $request[ sanitize_text_field( $key ) ] = sanitize_text_field( wp_unslash( $value ) );
            }
        }

        $job = TRP_Import_Export_Job::start_new( 'import', $slug );
        if ( is_wp_error( $job ) ) {
            $this->upload_failed( $file_name, $job );
        }
        $prepared = $source->prepare( $job, $request, $_FILES );
        if ( is_wp_error( $prepared ) ) {
            $job->delete();
            $this->upload_failed( $file_name, $prepared );
        }

        $job->status = 'analyzed';
        $job->save();
        $this->redirect( $this->get_page_url() );
    }

    protected function upload_failed( $file_name, $error ) {
        /* translators: 1: a file name, 2: an error message. */
        $message = $file_name !== '' ? sprintf( __( '%1$s: %2$s', 'translatepress-multilingual' ), $file_name, $error->get_error_message() ) : $error->get_error_message();
        // An error with a title, a list and a closing line is shown in parts.
        $parts = $error->get_error_data();
        $this->redirect_with_import_notice( is_array( $parts ) ? array_merge( $parts, array( 'text' => $message ) ) : $message );
    }

    protected function start_import() {
        $job = TRP_Import_Export_Job::load( 'import' );
        if ( ! $job || $job->status !== 'analyzed' ) {
            $this->redirect( $this->get_page_url() );
        }

        // A file with keys needs an answer: import them or not.
        $import_keys = isset( $_POST['import_keys'] ) ? sanitize_key( wp_unslash( $_POST['import_keys'] ) ) : '';
        if ( ! empty( $job->preview['keys'] ) && ! in_array( $import_keys, array( 'yes', 'no' ), true ) ) {
            $this->redirect_with_import_notice( __( 'Choose whether to import the keys this file holds, then start the import.', 'translatepress-multilingual' ) );
        }

        $job->options = array(
            'import_keys'     => ( ! empty( $job->preview['keys'] ) && $import_keys === 'yes' ) ? 'yes' : 'no',
            'import_settings' => ( isset( $_POST['import_settings'] ) && $_POST['import_settings'] === 'yes' ) ? 'yes' : 'no',
            'conflict_mode'   => ( isset( $_POST['conflict_mode'] ) && $_POST['conflict_mode'] === TRP_Import_Export_Writer::MODE_OVERWRITE ) ? TRP_Import_Export_Writer::MODE_OVERWRITE : TRP_Import_Export_Writer::MODE_KEEP,
        );
        $job->summary = array( 'warnings' => array(), 'key_changes' => array() );
        $steps        = TRP_Import_Source_Registry::get_source( $job->source )->build_steps( $job, $job->options );
        if ( is_wp_error( $steps ) ) {
            $job->delete();
            $this->redirect_with_import_notice( $steps->get_error_message() );
        }

        $job->steps   = $steps;
        $job->current = 0;
        $job->cursor  = array();
        $job->status  = 'running';
        $job->save();
        $this->redirect( $this->get_run_url( $job ) );
    }

    // Import steps for one time budget, like run_export().
    protected function run_import( $job ) {
        $source = TRP_Import_Source_Registry::get_source( $job->source );
        $writer = new TRP_Import_Export_Writer( $this->settings );
        $budget = $this->get_time_budget();
        $start  = microtime( true );
        do {
            $step   = $job->get_current_step();
            $result = $source->process_step( $job, $writer, max( 0.1, $budget - ( microtime( true ) - $start ) ) );
            if ( is_wp_error( $result ) ) {
                $job->status                 = 'failed';
                $job->summary['error']       = $result->get_error_message();
                $job->summary['failed_step'] = $step['label'];
                $job->save();
                $this->redirect( $this->get_page_url() );
            }
            $job->summary['warnings'] = array_values( array_unique( array_merge( $job->summary['warnings'], $result['warnings'] ) ) );
            if ( $result['done'] && $step['key'] === 'settings' ) {
                $job->summary['settings_done'] = true;
            }
            if ( $result['done'] && ! $job->advance_step() ) {
                $job->status = 'done';
                $job->save();
                $this->redirect( $this->get_page_url() );
            }
            $job->save();
            // The settings just changed: end the request so the next one boots with them.
            if ( $result['done'] && $step['key'] === 'settings' ) {
                return;
            }
        } while ( ( microtime( true ) - $start ) < $budget );
    }

    // Streams the export file once, then deletes the job and its files.
    public function download() {
        check_admin_referer( self::NONCE_NAME, 'nonce' );
        if ( ! current_user_can( $this->get_capability() ) ) {
            wp_die( esc_html__( 'You do not have permission to perform this action.', 'translatepress-multilingual' ), '', array( 'response' => 403 ) );
        }

        $job_id = isset( $_GET['job_id'] ) ? sanitize_text_field( wp_unslash( $_GET['job_id'] ) ) : '';
        $job    = TRP_Import_Export_Job::load( 'export' );
        $path   = $job ? $job->dir . basename( $job->file ) : '';
        if ( ! $job || $job->id !== $job_id || $job->status !== 'ready' || $job->file === '' || ! is_file( $path ) ) {
            wp_die(
                esc_html__( 'This export file is no longer on the server: it is deleted once it has been downloaded. Generate a new one.', 'translatepress-multilingual' ),
                esc_html__( 'Import & Export Translations', 'translatepress-multilingual' ),
                array(
                    'response'  => 404,
                    'link_url'  => esc_url( admin_url( 'admin.php?page=' . self::PAGE_SLUG ) ),
                    'link_text' => esc_html__( 'Back to Import & Export', 'translatepress-multilingual' ),
                )
            );
        }

        nocache_headers();
        header( 'Content-Type: application/zip' );
        header( 'Content-Disposition: attachment; filename="' . basename( $job->file ) . '"' );
        header( 'Content-Length: ' . filesize( $path ) );
        header( 'X-Content-Type-Options: nosniff' );
        while ( ob_get_level() ) {
            ob_end_clean();
        }
        readfile( $path );//phpcs:ignore
        $job->delete();
        exit;
    }

    // Totals only add up translation steps, the ones with a report label.
    protected function get_import_summary( $job ) {
        $totals = array_fill_keys( TRP_Import_Export_Writer::COUNT_KEYS, 0 );
        $steps  = array();
        foreach ( $job->steps as $step ) {
            if ( isset( $step['report_label'] ) ) {
                $steps[] = $step;
                foreach ( $totals as $key => $count ) {
                    $totals[ $key ] += $step[ $key ];
                }
            }
        }

        $with_settings = $job->options['import_settings'] === 'yes' && isset( $job->preview['variants']['with'] );
        $variant       = $job->preview['variants'][ $with_settings ? 'with' : 'without' ];
        $notes         = $job->summary['warnings'];
        if ( $totals['skipped'] > 0 ) {
            /* translators: %s is a number. */
            array_unshift( $notes, sprintf( _n( '%s translation could not be imported. The notes below say why.', '%s translations could not be imported. The notes below say why.', $totals['skipped'], 'translatepress-multilingual' ), number_format_i18n( $totals['skipped'] ) ) );
        }

        return array(
            'totals'        => $totals,
            'steps'         => $steps,
            'notes'         => $notes,
            'planned'       => $variant['notes'],
            'changes'       => array_merge( $variant['changes'], $job->summary['key_changes'] ),
            'settings'      => $with_settings,
            'keep'          => $job->options['conflict_mode'] !== TRP_Import_Export_Writer::MODE_OVERWRITE,
            'file_name'     => $job->preview['file_name'],
            'finished_text' => $this->format_time( $job->updated ),
        );
    }
}
