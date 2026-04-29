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
            'trp_failed_translations_cleanup_315' => array(
                'file'              => 'tasks/class-failed-translations-cleanup.php',
                'class'             => 'TRP_Failed_Translations_Cleanup',
                'completion_notice' => sprintf( __( 'TranslatePress has detected and cleaned up some failed translation entries in your database following %1$san isolated incident%2$s. No further action is required.', 'translatepress-multilingual' ), '<a href="https://translatepress.com/docs/failed-translations-incident/?utm_source=wp-dashboard&utm_medium=client-site&utm_campaign=troubleshooting" target="_blank">', '</a>' ),
            ),
        ) );
    }
}
