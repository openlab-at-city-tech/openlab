<?php


if ( !defined('ABSPATH' ) )
    exit();

class TRP_Machine_Translator_Logger {
    protected $settings;
    protected $query;
    protected $url_converter;
    protected $counter_date;
    protected $limit;
    protected $error_manager;

    /**
     * TRP_Machine_Translator_Logger constructor.
     *
     * @param array $settings       Settings option.
     */
    public function __construct( $settings ){
        $this->settings     = $settings;
        $this->counter_date = $this->get_mt_option('machine_translation_counter_date', date ("Y-m-d" ));
        $this->limit        = intval( $this->get_mt_option('machine_translation_limit', 1000000) );
        // if a new day has passed, update the counter and date
        $this->maybe_reset_counter_date();

        add_action('trp_is_deepl_glossary_id_valid', array( $this, 'show_notice_if_glossary_id_invalid'), 10, 1 );
    }

    public function get_todays_character_count() {

        if ( $this->quota_exceeded() ) {
            return $this->limit;
        } else {
            return $this->get_current_counter();
        }
    }

    public function log( $args = array() ){

        $trp = TRP_Translate_Press::get_trp_instance();

        if ( ! $this->query )
            $this->query = $trp->get_component('query');

        if ( ! $this->url_converter )
            $this->url_converter = $trp->get_component('url_converter');

        if( empty($args) )
            return false;

        if( $this->get_mt_option('machine_translation_log', false) !== 'yes' )
            return false;

        if( !$this->query->check_machine_translation_log_table() )
            return false;

        // expected structure.
        $log = array(
            'url'         => $this->url_converter->cur_page_url(),
            'strings'     => $args['strings'],
            'characters'  => $this->count(unserialize($args['strings'])),
            'response'    => $args['response'],
            'lang_source' => $args['lang_source'],
            'lang_target' => $args['lang_target'],
            'timestamp'   => date ("Y-m-d H:i:s" )
        );

        $table_name = $this->query->db->prefix . 'trp_machine_translation_log';

        $query = "INSERT INTO `$table_name` ( `url`, `strings`, `characters`, `response`, `lang_source`, `lang_target`, `timestamp` ) VALUES (%s, %s, %s, %s, %s, %s, %s)";

        $prepared_query = $this->query->db->prepare( $query, $log );
        $this->query->db->get_results( $prepared_query, OBJECT_K  );

        if ( $this->query->db->last_error !== '' )
            return false;

        return true;
    }

    private function count($strings){
        if( !is_array($strings) )
            return 0;

        $char_number = 0;
        foreach($strings as $string)
            $char_number += strlen($string);

        return $char_number;
    }

    public function count_towards_quota($strings){

        $count = $this->count($strings);

        $this->count_machine_translated_characters( $count );

        return $this->increase_counter_with_value( $count );
        
    }

    /**
     * Increase existing counter with the provided value.
     * It does NOT replace the existing counter with the provided value. It adds to it.
     *
     * Uses a query that locks read on specific table row to avoid concurrency issues
     *
     * Returns the new character count after update
     *
     * @param $number_of_characters
     * @return int
     */
    public function increase_counter_with_value( $number_of_characters ){
        global $wpdb;

        $set_transient = false;

        // Start transaction
        $wpdb->query( 'START TRANSACTION;' );

        // Query to select the option value
        $select_query               = "
            SELECT option_value
            FROM {$wpdb->options}
            WHERE option_name = 'trp_machine_translation_counter'
            LIMIT 1
            FOR UPDATE;
        ";
        $pre_update_character_count = $wpdb->get_var( $select_query );


        if ( $pre_update_character_count === null ) {
            // option not set yet
            $insert_query = $wpdb->prepare( "
                INSERT
                INTO {$wpdb->options}
                (option_name, option_value)
                VALUES ('trp_machine_translation_counter', %d );
            ", $number_of_characters );
            $wpdb->query( $insert_query );
            $pre_update_character_count = 0;
            $set_transient = true;
        } else {

            // Query to update the option value
            $update_query = $wpdb->prepare( "
                UPDATE {$wpdb->options}
                SET option_value = %d
                WHERE option_name = 'trp_machine_translation_counter';
            ", $pre_update_character_count + $number_of_characters );
            $wpdb->query( $update_query );
        }
        // Commit the transaction
        $wpdb->query( 'COMMIT;' );

        if ( $set_transient === true ){
            $transient = get_transient('trp_machine_translation_counter_safety_reset');
            if ( $transient ){
                $wpdb->last_error = 'Machine translation counter was reset twice in a day. Unless an intentional action was performed on the DB that would affect trp_machine_translation_counter option from wp_options, please check for automatic translation character counting issues.';
            }else{
                set_transient('trp_machine_translation_counter_safety_reset', true, 12 * HOUR_IN_SECONDS);
            }
        }
        if ( !empty( $wpdb->last_error ) ) {
            if ( !$this->error_manager ) {
                $trp                 = TRP_Translate_Press::get_trp_instance();
                $this->error_manager = $trp->get_component( 'error_manager' );
            }
            $this->error_manager->record_error( array( 'last_error_updating_character_count' => $wpdb->last_error, 'disable_automatic_translations' => true ) );
            delete_transient('trp_machine_translation_counter_safety_reset');
            if ( !is_numeric( $pre_update_character_count ) ) {
                $pre_update_character_count = 0;
            }
        }

        return $pre_update_character_count + $number_of_characters;
    }

    /**
     * Use only if really needed. It always performs a query that is never cached.
     *
     * Used instead of get_option() to bypass caching
     *
     * @return void
     */
    public function get_current_counter() {
        global $wpdb;
        $select_query    = "
            SELECT option_value
            FROM {$wpdb->options}
            WHERE option_name = 'trp_machine_translation_counter' 
            LIMIT 1
        ";
        $character_count = $wpdb->get_var( $select_query );

        // option not set yet
        if ( $character_count === null ){
            $character_count = 0;
        }

        if ( !empty( $wpdb->last_error ) ) {
            if ( !$this->error_manager ) {
                $trp                 = TRP_Translate_Press::get_trp_instance();
                $this->error_manager = $trp->get_component( 'error_manager' );
            }
            $this->error_manager->record_error( array( 'last_error_selecting_character_count' => $wpdb->last_error, 'disable_automatic_translations' => true ) );
            $character_count = null;
        }
        return $character_count;
    }

    /**
     * Use only if really needed. It always performs a query that is never cached.
     *
     * @return bool
     */
    public function quota_exceeded(){
        $counter = $this->get_current_counter();
        if ( $counter !== null && $this->limit >= $counter ) {
            // quota NOT exceeded
            // for some reason this condition is hard to comprehend by my brain
            // thus the unneeded comment.
            return false;
        }

        // we've exceeded our daily quota
        $this->update_options( array( array( 'name' => 'machine_translation_trigger_quota_notification', 'value' => true ) ) );

        return true;
    }

    public function maybe_reset_counter_date(){

        // if the day has not passed
        if ( $this->counter_date === date ( "Y-m-d" ) )
            return false;

        $options = array(
            // there is a new day
            array(
                'name'  => 'machine_translation_counter_date',
                'value' => date( "Y-m-d" ),
            ),
            // clear the notification
            array(
                'name'  => 'machine_translation_trigger_quota_notification',
                'value' => false,
            ),
        );

        $this->update_options( $options );

        // clear the counter
        update_option('trp_machine_translation_counter', 0);

        return true;

    }

    private function get_mt_option($option_name, $default){

        return isset( $this->settings['trp_machine_translation_settings'][$option_name] ) ? $this->settings['trp_machine_translation_settings'][$option_name] : $default;

    }

    private function update_options( $options ){

        $machine_translation_settings = $this->settings['trp_machine_translation_settings'];

        foreach( $options as $option ){
            $this->settings['trp_machine_translation_settings'][$option['name']] = $option['value'];
            $machine_translation_settings[$option['name']] = $option['value'];
        }

        update_option( 'trp_machine_translation_settings', $machine_translation_settings );
    }

    public function sanitize_settings($mt_settings ){
        $machine_translation_settings = $this->settings['trp_machine_translation_settings'];

        if( isset( $machine_translation_settings['machine_translation_counter_date'] ) )
            $mt_settings['machine_translation_counter_date'] = $machine_translation_settings['machine_translation_counter_date'];

        if( !empty( $mt_settings['machine_translation_log'] ) )
            $mt_settings['machine_translation_log'] = sanitize_text_field( $mt_settings['machine_translation_log']  );
        else
            $mt_settings['machine_translation_log'] = 'no';


        return $mt_settings;
    }

    public function count_machine_translated_characters( $count ){

        $machine_translated_characters = get_option( 'trp_machine_translated_characters', array() );

        $current_month = date( 'm-Y' );

        if( isset( $machine_translated_characters[ $current_month ] ) )
            $machine_translated_characters[ $current_month ] = $machine_translated_characters[ $current_month ] + $count;
        else
            $machine_translated_characters[ $current_month ] = $count;

        update_option( 'trp_machine_translated_characters', $machine_translated_characters, false );

    }

    public function show_notice_if_glossary_id_invalid( $response ){

        global $wpdb;

        if ( is_array( $response ) && ! is_wp_error( $response ) && isset( $response['response'] ) &&
            isset( $response['response']['code']) && $response['response']['code'] !== 200 ) {

            $response_body = json_decode( $response['body'] );

            if ( isset( $response_body->message ) ) {

                if ( !$this->error_manager ) {
                    $trp                 = TRP_Translate_Press::get_trp_instance();
                    $this->error_manager = $trp->get_component( 'error_manager' );
                }

                if ( strpos( strtolower( $response_body->message), 'glossary' ) !== false ) {

                    $wpdb->last_error = ' The glossary ID provided for DeepL translation request was invalid. Please check again';
                    $this->error_manager->record_error( array( 'glossary_id_is_invalid' => $wpdb->last_error, 'disable_automatic_translations' => true ) );
                }
            }
        }
    }

}
