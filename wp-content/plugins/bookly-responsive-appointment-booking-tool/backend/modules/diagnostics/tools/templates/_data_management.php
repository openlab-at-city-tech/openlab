<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Inputs;

$tables = array( 'appointments' => false, 'payments' => false, 'files' => true, 'mailing queue' => true, 'sessions' => true, 'logs' => true );
?>
<div class="form-row">
    <div class="col-auto">
        <form action="<?php echo admin_url( 'admin-ajax.php?action=bookly_export_data' ) ?>" method="POST">
            <?php Inputs::renderCsrf() ?>
            <div class="btn-group">
                <button type="submit" class="btn btn-default" id="bookly-export">Export data</button>
                <button type="button" class="btn btn-default bookly-dropdown-toggle bookly-dropdown-toggle-split" data-toggle="bookly-dropdown" aria-haspopup="true" aria-expanded="false"></button>
                <div class="bookly-dropdown-menu overflow-hidden bookly-js-tables-dropdown">
                    <h6 class="bookly-dropdown-header">Select sections to ignore</h6>
                    <div class="bookly-dropdown-divider"></div>
                    <?php foreach ( $tables as $table => $ignore ) : ?>
                        <div class="px-3 py-2">
                            <?php Inputs::renderCheckBox( ucfirst( $table ), $table, $ignore, array( 'name' => 'ignore[]' ) ) ?>
                        </div>
                    <?php endforeach ?>
                    <div class="bookly-dropdown-divider"></div>
                    <div class="px-3 py-2">
                        <?php Inputs::renderCheckBox( 'Safe export', null, false, array( 'name' => 'safe' ) ) ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-auto">
        <div class="input-group">
            <div class="btn btn-primary btn-file" style="border-top-right-radius: unset; border-bottom-right-radius: unset;">
                <span class="bookly-js-text">Import data</span>
                <span class="spinner-border spinner-border-sm bookly-js-spinner" style="display: none"></span>
                <input type="file" id="bookly_import_file" name="import" class="w-100">
            </div>
            <div class="input-group-append" style="border-left: 1px solid white;">
                <button type="button" class="btn btn-primary bookly-dropdown-toggle bookly-dropdown-toggle-split" data-toggle="bookly-dropdown" aria-haspopup="true" aria-expanded="false"></button>
                <div class="bookly-dropdown-menu overflow-hidden bookly-js-tables-dropdown">
                    <div class="px-3 py-2">
                        <?php Inputs::renderCheckBox( 'Safe import', null, true, array( 'name' => 'safe' ) ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>