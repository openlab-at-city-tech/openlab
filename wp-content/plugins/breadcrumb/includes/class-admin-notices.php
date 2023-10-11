<?php
if (!defined('ABSPATH')) exit; // if direct access 

class class_breadcrumb_notices
{

    public function __construct()
    {
        add_action('admin_notices', array($this, 'data_update'));
    }






    public function data_update()
    {

        $breadcrumb_info = get_option('breadcrumb_info');
        $v1_5_39 = isset($breadcrumb_info['v1_5_39']) ? $breadcrumb_info['v1_5_39'] : 'no';


        // delete_option('breadcrumb_info');

        ob_start();

        if ($v1_5_39 != 'yes') {
?>
            <div class="notice">
                <p>
                    <?php
                    echo sprintf(__('Data update required for breadcrumb plugin <strong><a href="%s">click here</a></strong> to update data', 'post-grid-pro'), esc_url(admin_url() . 'admin.php?page=breadcrumb-data-update'))
                    ?>
                </p>
            </div>
<?php
        }


        echo (ob_get_clean());
    }
}

new class_breadcrumb_notices();
