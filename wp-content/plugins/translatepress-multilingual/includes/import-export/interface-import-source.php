<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Interface TRP_Import_Source_Interface
 *
 * Contract every import source implements (TranslatePress archives today, WPML or Polylang later).
 * The controller calls prepare() on upload, renders $job->preview as the review, calls build_steps()
 * on "Start import", then process_step() until every step is done. Sources only ever write through
 * TRP_Import_Export_Writer, so they all share the same semantics.
 */
interface TRP_Import_Source_Interface {

    /**
     * @return string Unique slug, the value of the "Import from" select.
     */
    public function get_slug();

    /**
     * @return string Human readable name.
     */
    public function get_name();

    /**
     * @return bool Whether this source can be used on this site.
     */
    public function is_available();

    /**
     * Validate the request, keep what the import needs in $job->dir and describe it in $job->preview.
     *
     * Must not write translations or settings. On error the controller deletes the job.
     * $job->preview must hold:
     * - 'file_name', 'site_url', 'exported_at' (display text), 'default_language', 'default_language_name': strings ('' when unknown)
     * - 'languages': list of array( 'code', 'name', 'is_default', 'on_site', 'dictionary_rows', 'gettext_rows', 'slug_rows' )
     * - 'settings_included': bool, whether "Also import the settings" is offered
     * - 'variants': 'without' and, when settings are included, 'with'; each array( 'notes' => string[] what is skipped,
     *   'changes' => string[] how the settings change this site ). A source may keep its own data there too.
     *
     * @param TRP_Import_Export_Job $job     New job with an empty working directory.
     * @param array                 $request Sanitized request data.
     * @param array                 $files   The $_FILES array.
     *
     * @return true|WP_Error
     */
    public function prepare( $job, $request, $files );

    /**
     * The steps of this import, in the order they run.
     *
     * Each step: array( 'key' => string, 'label' => progress text, 'total' => int, 'processed' => 0, 'done' => false )
     * plus each of TRP_Import_Export_Writer::COUNT_KEYS => 0, plus anything process_step() needs. Steps with a
     * 'report_label' are rows of the report and add to its totals. A step keyed 'settings' imports the settings:
     * the controller ends the request after it so the next one boots with them.
     *
     * @param TRP_Import_Export_Job $job     Current job.
     * @param array                 $options array( 'import_settings' => 'yes'|'no', 'conflict_mode' => TRP_Import_Export_Writer::MODE_* ).
     *
     * @return array|WP_Error Steps.
     */
    public function build_steps( $job, $options );

    /**
     * Process part of the job's current step within the time budget.
     *
     * Updates the step through $job->update_current_step() ('processed', 'done', counts) and keeps its position in $job->cursor;
     * the controller advances to the next step once 'done' is returned.
     *
     * @param TRP_Import_Export_Job    $job         Current job.
     * @param TRP_Import_Export_Writer $writer      Writer instance.
     * @param float                    $time_budget Seconds this call may use.
     *
     * @return array|WP_Error array( 'done' => bool, 'warnings' => string[] shown as notes in the report )
     */
    public function process_step( $job, $writer, $time_budget );
}
