<?php
namespace Ari_Fancy_Lightbox\Models;

use Ari\Models\Model as Model;
use Ari_Fancy_Lightbox\Helpers\Helper as Helper;

class Settings extends Model {
    public function data() {
        $form = Helper::get_settings_form();

        $data = array(
            'form' => $form,
        );

        return $data;
    }
}
