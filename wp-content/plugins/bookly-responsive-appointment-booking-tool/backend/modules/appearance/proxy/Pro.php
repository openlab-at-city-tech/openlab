<?php
namespace Bookly\Backend\Modules\Appearance\Proxy;

use Bookly\Lib;

/**
 * @method static void renderModernAppearance() Render modern appearance
 * @method static void renderAddress() Render inputs for address fields in appearance.
 * @method static void renderBirthday() Render inputs for birthday fields in appearance.
 * @method static void renderBookingStatesSelector() Render single/multiple/100% off booking selector on Payment step.
 * @method static void renderBookingStatesText() Render multiple or 100% off booking text option on Payment step.
 * @method static void renderGiftCards() Render gift cards on Payment step.
 * @method static void renderPaymentImpossible() Render payment impossible alert.
 * @method static void renderFacebookButton() Render facebook login button on Time step.
 * @method static void renderShowStepDetailsSettings() render 'Show Birthday Fields' on Details Step.
 * @method static void renderShowGiftCards() Render 'Show gift cards' on Payment step.
 * @method static void renderShowTips() Render 'Show tips' on Payment step.
 * @method static void renderShowQRCode() Render 'Show QR code' on Done step.
 * @method static void renderQRCode() Render QR code on Done step.
 * @method static void renderTips() Render tips on Payment step.
 * @method static void renderTimeZoneSwitcher() Render timezone switcher on Time step.
 * @method static void renderTimeZoneSwitcherCheckbox() Render 'Show time zone switcher' on Time step.
 */
abstract class Pro extends Lib\Base\Proxy
{

}