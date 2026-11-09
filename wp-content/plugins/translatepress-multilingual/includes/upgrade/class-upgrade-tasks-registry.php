<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

class TRP_Upgrade_Tasks_Registry {

    /**
     * Returns the list of registered batch upgrade tasks.
     *
     * Each task maps an option flag name to a file and class that implements:
     *   - build_todo_list(): array of todo items
     *   - execute( $item ): array with 'affected_rows' and optionally 'error'
     *
     * @return array
     */
    public static function get_tasks() {
        return apply_filters( 'trp_batch_upgrade_tasks', array(
            'trp_gettext_tables_optimization_330' => array(
                'file'              => 'tasks/class-gettext-tables-optimization.php',
                'class'             => 'TRP_Gettext_Tables_Optimization',
                'running_notice'     => sprintf( __( '%s is optimizing gettext database tables in the background. Translations continue to work while this runs.', 'translatepress-multilingual' ), '<strong>TranslatePress</strong>' ),
                'completion_notice' => __( 'TranslatePress gettext database optimization completed successfully.', 'translatepress-multilingual' ),
                'failure_notice'    => __( 'TranslatePress gettext database optimization failed.', 'translatepress-multilingual' ),
                'always_show_completion_notice' => true,
            ),
            'trp_failed_translations_cleanup_315' => array(
                'file'              => 'tasks/class-failed-translations-cleanup.php',
                'class'             => 'TRP_Failed_Translations_Cleanup',
                'completion_notice' => sprintf(
                    /* translators: %s: Link with the text "an isolated incident". */
                    __( 'TranslatePress has detected and cleaned up some failed translation entries in your database following %s. No further action is required.', 'translatepress-multilingual' ),
                    '<a href="https://translatepress.com/docs/failed-translations-incident/?utm_source=wp-dashboard&utm_medium=client-site&utm_campaign=troubleshooting" target="_blank">' . esc_html__( 'an isolated incident', 'translatepress-multilingual' ) . '</a>'
                ),
            ),
        ) );
    }
}
