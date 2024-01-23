<?php

class TRP_Machine_Translator_Logger {
    protected $settings;
    protected $query;
    protected $url_converter;
    protected $counter;
    protected $counter_date;
    protected $limit;

    /**
     * TRP_Machine_Translator_Logger constructor.
     *
     * @param array $settings       Settings option.
     */
    public function __construct( $settings ){
        $this->settings     = $settings;
        $this->counter      = intval( $this->get_mt_option('machine_translation_counter', 0) );
        $this->counter_date = $this->get_mt_option('machine_translation_counter_date', date ("Y-m-d" ));
        $this->limit        = intval( $this->get_mt_option('machine_translation_limit', 1000000) );
        // if a new day has passed, update the counter and date
        $this->maybe_reset_counter_date();
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
        $this->counter += $this->count($strings);

        $this->update_options( array( array( 'name' => 'machine_translation_counter', 'value' => $this->counter ) ) );

        return $this->counter;
    }

    public function quota_exceeded(){
        if ( $this->limit  >=  $this->counter )
        {
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
            // clear the counter
            array(
                'name'  => 'machine_translation_counter',
                'value' => 0,
            ),
            // clear the notification
            array(
                'name'  => 'machine_translation_trigger_quota_notification',
                'value' => false,
            ),
        );

        $this->update_options( $options );

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

        if( isset( $machine_translation_settings['machine_translation_counter'] ) )
            $mt_settings['machine_translation_counter'] = $machine_translation_settings['machine_translation_counter'];

        if( isset( $machine_translation_settings['machine_translation_counter_date'] ) )
            $mt_settings['machine_translation_counter_date'] = $machine_translation_settings['machine_translation_counter_date'];

        if( !empty( $mt_settings['machine_translation_log'] ) )
            $mt_settings['machine_translation_log'] = sanitize_text_field( $mt_settings['machine_translation_log']  );
        else
            $mt_settings['machine_translation_log'] = 'no';


        return $mt_settings;
    }
}
