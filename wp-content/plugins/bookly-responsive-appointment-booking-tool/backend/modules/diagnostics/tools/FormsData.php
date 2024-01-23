<?php
namespace Bookly\Backend\Modules\Diagnostics\Tools;

use Bookly\Lib\Session;

class FormsData extends Tool
{
    protected $slug = 'forms-data';
    protected $hidden = true;
    protected $template = '_forms_data';


    public function __construct()
    {
        $this->title = 'Forms data';
    }

    public function render()
    {
        $all_forms_data = Session::getAllFormsData();
        $last_touched_form_id = 0;
        $last_touched = 0;
        foreach ( $all_forms_data as $form_id => $data ) {
            if ( isset( $data['last_touched'] ) && $last_touched < $data['last_touched'] ) {
                $last_touched = $data['last_touched'];
                $last_touched_form_id = $form_id;
            }
        }

        return self::renderTemplate( '_forms_data', array( 'forms' => $all_forms_data, 'active' => $last_touched_form_id ), false );
    }

    public function destroy()
    {
        $form_id = self::parameter( 'form_id' );
        Session::destroyFormData( $form_id );

        wp_send_json_success();
    }
}