<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Inputs;

?>
<div class="form-row">
    <div class="col-auto">
        <form action="<?php echo admin_url( 'admin-ajax.php?action=bookly_export_data' ) ?>" method="POST">
            <?php Inputs::renderCsrf() ?>
            <button id="bookly-export" type="submit" class="btn btn-success">
                <span class="ladda-label">Export data</span>
            </button>
        </form>
    </div>
    <div class="col-auto">
        <form id="bookly_import" action="<?php echo admin_url( 'admin-ajax.php?action=bookly_import_data' ) ?>" method="POST" enctype="multipart/form-data">
            <?php Inputs::renderCsrf() ?>
            <div id="bookly-import" class="btn btn-primary btn-file">
                <span class="ladda-label">Import data</span>
                <input type="file" id="bookly_import_file" name="import" class="w-100">
            </div>
        </form>
    </div>
</div>