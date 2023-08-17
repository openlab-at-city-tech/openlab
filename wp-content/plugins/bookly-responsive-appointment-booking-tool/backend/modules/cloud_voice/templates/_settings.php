<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Settings\Selects;
$voice = \Bookly\Lib\Cloud\API::getInstance()->voice;
/**
 * @var \Bookly\Backend\Modules\CloudVoice\Page $self
 */
?>
<div class="form-row">
    <div class="col-lg-6 col-xs-12">
        <?php Selects::renderSingleValue( 'bookly_cloud_voice_language', $voice->language, __( 'Language', 'bookly' ), __( 'Select the language of your notifications', 'bookly' ), $self::getLanguages() ) ?>
    </div>
</div>
<div class="form-row">
    <div class="col-lg-6 col-xs-12">
        <div class="d-flex justify-content-end">
            <?php Buttons::renderSubmit() ?>
        </div>
    </div>
</div>