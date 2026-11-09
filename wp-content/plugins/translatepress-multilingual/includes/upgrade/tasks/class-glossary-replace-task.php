<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * Batch task: finish a large glossary "search & replace in existing translations"
 * job that didn't complete in its originating admin-ajax request.
 *
 * Job state lives in the `trp_glossary_replace_job` option (written by
 * TRP_Glossary_Queries::replace_in_dictionary before hand-off):
 *
 *   array(
 *     'languages' => array(
 *        'it_IT' => array( 'table' => ..., 'existing' => ..., 'new' => ...,
 *                          'cursor' => 123, 'done' => false, 'count' => 45 ),
 *        ...
 *     ),
 *   )
 *
 * The per-language id cursor is persisted in that option (not in the todo item),
 * because TRP_Batch_Processor passes the todo item by value and does not persist a
 * returned cursor. One todo item = one language.
 */
class TRP_Glossary_Replace_Task {

    /**
     * Rows processed per execute() call. The processor loops execute() within its
     * own time budget, so this only bounds a single query's memory.
     */
    const BATCH_SIZE = 500;

    /**
     * One todo item per language that still has work.
     *
     * @return array
     */
    public function build_todo_list() {
        $job   = get_option( TRP_Glossary_Queries::BATCH_JOB, array() );
        $items = array();

        if ( ! empty( $job['languages'] ) && is_array( $job['languages'] ) ) {
            foreach ( $job['languages'] as $lang => $info ) {
                if ( empty( $info['done'] ) ) {
                    $items[] = array( 'lang' => $lang, 'status' => 'not_completed', 'error' => null );
                }
            }
        }

        return $items;
    }

    /**
     * Process one batch for the item's language, advancing its id cursor.
     *
     * @param array $item Todo item ( 'lang' => language code ).
     * @return array { string status, int affected_rows, string error }
     */
    public function execute( $item ) {
        $result = array( 'status' => 'not_completed', 'affected_rows' => 0, 'error' => '' );

        $lang = isset( $item['lang'] ) ? $item['lang'] : '';
        $job  = get_option( TRP_Glossary_Queries::BATCH_JOB, array() );

        if ( $lang === '' || empty( $job['languages'][ $lang ] ) || ! empty( $job['languages'][ $lang ]['done'] ) ) {
            $result['status'] = 'completed';
            return $result;
        }

        $info       = $job['languages'][ $lang ];
        $batch_size = max( 1, (int) apply_filters( 'trp_glossary_replace_batch_size', self::BATCH_SIZE ) );
        $batch      = TRP_Glossary_Queries::replace_batch_in_table(
            $info['table'],
            $info['existing'],
            $info['new'],
            (int) $info['cursor'],
            $batch_size,
            $lang
        );

        if ( $batch['error'] !== '' ) {
            // Persist "done" so a persistent error can't spin forever, and surface it.
            $job['languages'][ $lang ]['done'] = true;
            update_option( TRP_Glossary_Queries::BATCH_JOB, $job, false );
            $result['error'] = $batch['error'];
            return $result;
        }

        $job['languages'][ $lang ]['cursor'] = $batch['cursor'];
        $job['languages'][ $lang ]['count']  = (int) $job['languages'][ $lang ]['count'] + $batch['changed'];
        $result['affected_rows']             = $batch['changed'];

        if ( $batch['done'] ) {
            $job['languages'][ $lang ]['done'] = true;
            $result['status']                  = 'completed';
        }

        update_option( TRP_Glossary_Queries::BATCH_JOB, $job, false );

        return $result;
    }
}
