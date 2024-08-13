<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Inputs;

/** @var array $list */
?>
<div class="accordion" role="tablist" aria-multiselectable="true">
    <?php if ( $list ) : ?>
        <?php foreach ( $list as $option => $value ) : ?>
            <div class="card bookly-collapse-with-arrow mb-0 bookly-js-advanced-option-card">
                <div class="card-header d-flex align-items-center bookly-js-table py-2 px-2" role="tab">
                    <div class="form-row w-100 d-flex align-items-center bookly-cursor-pointer">
                        <div class="bookly-collapsed col" data-toggle="bookly-collapse" href="#advanced-options-<?php echo esc_attr( $option ) ?>" aria-expanded="false" aria-controls="<?php echo esc_attr( $option ) ?>">
                            <span class="bookly-collapse-title">
                                <?php echo esc_html( $option ) ?>
                            </span>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-success ladda-button" type="button" data-action="set-default-option" data-option="<?php echo esc_attr( $option ) ?>" data-spinner-size="40" data-style="zoom-in"><span class="ladda-label">Set default</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body bookly-collapse p-0" id="advanced-options-<?php echo esc_attr( $option ) ?>">
                    <div class="form-row p-2">
                        <div class="col-6">
                            <b>Current</b><br/>
                            <?php echo esc_html( $value['current'] ) ?>
                        </div>
                        <div class="col-6">
                            <b>Default</b><br/>
                            <?php echo esc_html( $value['default'] ) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    <?php else: ?>
        No changes were detected
    <?php endif ?>
</div>
<hr/>
<div>
    <div class="form-group">
        <label for="bookly-advanced-options-option-name">Option name</label>
        <div class="input-group mb-3 bookly-js-advanced-options-option-name">
            <input value="" id="bookly-advanced-options-option-name" class="form-control" type="text"/>
            <div class="input-group-append">
                <button class="btn btn-default ladda-button" type="button" data-spinner-size="40" data-style="zoom-in" data-spinner-color="#666666">View</button>
            </div>
        </div>
    </div>
    <div class="bookly-collapse bookly-js-advanced-options-set-option">
        <div class="form-group">
            <label for="bookly-advanced-options-option-current-value">Current value</label>
            <textarea class="form-control" id="bookly-advanced-options-option-current-value" readonly></textarea>
        </div>
        <div class="form-group">
            <label for="bookly-advanced-options-option-default-value">Default value</label>
            <textarea class="form-control" id="bookly-advanced-options-option-default-value" readonly></textarea>
        </div>
        <div class="form-group">
            <label for="bookly-advanced-options-option-value">New value</label>
            <textarea class="form-control" id="bookly-advanced-options-option-value"></textarea>
        </div>
        <div class="w-100 text-right">
            <button class="btn btn-success ladda-button" type="button" data-spinner-size="40" data-style="zoom-in"><span class="ladda-label">Set new value</button>
        </div>
    </div>
</div>