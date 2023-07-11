<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Utils\Common;
use Bookly\Lib\Utils\DateTime;
use Bookly\Lib\Entities\Service;
use Bookly\Backend\Components\Dialogs\Service\Edit\Proxy;
/**
 * @var array $service
 */
?>
<div class="bookly-js-service-time-container">
    <div class="form-group bookly-js-service bookly-js-service-simple">
        <label for="bookly-service-duration">
            <?php esc_html_e( 'Duration', 'bookly' ) ?>
        </label>
        <?php
            $options = Common::getDurationSelectOptions( $service['duration'] );
            $options = Proxy\CustomDuration::prepareServiceDurationOptions( $options, $service );
        ?>
        <select id="bookly-service-duration" class="bookly-js-duration form-control custom-select" name="duration">
            <?php foreach ( $options as $option ): ?>
                <option value="<?php echo esc_attr( $option['value'] ) ?>" <?php echo esc_attr( $option['selected'] ) ?>><?php echo esc_html( $option['label'] ) ?></option>
            <?php endforeach ?>
        </select>
        <?php Proxy\CustomDuration::renderServiceDurationHelp() ?>
    </div>
    <?php Proxy\CustomDuration::renderServiceDurationFields( $service ) ?>
    <div class="form-row border-left ml-4 pl-3 bookly-js-start-time-info"<?php if ( $service['duration'] < DAY_IN_SECONDS ) : ?> style="display:none;"<?php endif ?>>
        <div class="col">
            <div class="form-group bookly-js-service bookly-js-service-simple">
                <label for="bookly-service-start-time-info"><?php esc_html_e( 'Start and end times of the appointment', 'bookly' ) ?></label>
                <div class="form-row">
                    <div class="col-6">
                        <input id="bookly-service-start-time-info" class="form-control" type="text" name="start_time_info" value="<?php echo esc_attr( $service['start_time_info'] ) ?>"/>
                    </div>
                    <div class="col-6">
                        <input class="form-control" type="text" name="end_time_info" value="<?php echo esc_attr( $service['end_time_info'] ) ?>"/>
                    </div>
                </div>
                <small class="form-text text-muted"><?php esc_html_e( 'Allows to set the start and end times for an appointment for services with the duration of 1 day or longer. This time will be displayed in notifications to customers, backend calendar and codes for booking form.', 'bookly' ) ?></small>
            </div>
        </div>
    </div>
    <div class="bookly-js-service-slot-length">
        <div class="form-group bookly-js-service bookly-js-service-simple bookly-js-service-collaborative" <?php if ( $service['duration'] < DAY_IN_SECONDS ) : ?> style="display:none;"<?php endif ?>>
            <label for="bookly-service-slot-length">
                <?php esc_html_e( 'Time slot length', 'bookly' ) ?>
            </label>
            <select id="bookly-service-slot-length" class="form-control custom-select" name="slot_length">
                <option value="<?php echo Service::SLOT_LENGTH_DEFAULT ?>"<?php selected( $service['slot_length'], Service::SLOT_LENGTH_DEFAULT ) ?>><?php esc_html_e( 'Default', 'bookly' ) ?></option>
                <?php if ( $service['type'] === Service::TYPE_SIMPLE ) : ?>
                    <option value="<?php echo Service::SLOT_LENGTH_AS_SERVICE_DURATION ?>"<?php selected( $service['slot_length'], Service::SLOT_LENGTH_AS_SERVICE_DURATION ) ?>><?php esc_html_e( 'Slot length as service duration', 'bookly' ) ?></option>
                <?php endif ?>
                <?php foreach ( array( 120, 240, 300, 600, 720, 900, 1200, 1800, 2700, 3600, 5400, 7200, 10800, 14400, 21600 ) as $duration ): ?>
                    <option value="<?php echo esc_attr( $duration ) ?>"<?php selected( $service['slot_length'], $duration ) ?>><?php echo esc_html( DateTime::secondsToInterval( $duration ) ) ?></option>
                <?php endforeach ?>
            </select>
            <small class="form-text text-muted"><?php esc_html_e( 'The time interval which is used as a step when building all time slots for the service at the Time step. The setting overrides global settings in Settings > General. Use Default to apply global settings.', 'bookly' ) ?></small>
        </div>
    </div>
    <?php Proxy\Pro::renderPadding( $service ) ?>
    <?php Proxy\Tasks::renderSubForm( $service ) ?>
</div>