<?php

namespace WPChill\DownloadMonitor\Shop\Admin\Fields;

class GatewayOverview extends \DLM_Admin_Fields_Field {

	/** @var \WPChill\DownloadMonitor\Shop\Checkout\PaymentGateway\PaymentGateway[] */
	private $gateways;

	/**
	 * GatewayOverview constructor.
	 *
	 * @param \WPChill\DownloadMonitor\Shop\Checkout\PaymentGateway\PaymentGateway[] $gateways
	 */
	public function __construct( $gateways ) {
		$this->gateways = $gateways;
		parent::__construct( '', '', '' );
	}

	/**
	 * Renders field
	 *
	 */
	public function render() {

		$gateways = $this->gateways;

		if ( ! empty( $gateways ) ) : ?>
            <ul>
				<?php foreach ( $gateways as $gateway ) : ?>
					<?php
					$checkbox_name = "dlm_gateway_" . $gateway->get_id() . "_enabled";
					$is_checked   = ( $gateway->is_enabled() ? ' checked="checked"' : '' );
					?>
                    <li>
                        <input type="checkbox" name="<?php echo esc_attr( $checkbox_name ); ?>" id="<?php echo esc_attr( $checkbox_name ); ?>"
                               value="1"<?php echo esc_attr( $is_checked ); ?>/>
                        <label for="<?php echo esc_attr( $checkbox_name ); ?>"><?php echo esc_html( $gateway->get_title() ); ?></label>
                    </li>
				<?php endforeach; ?>
            </ul>
		<?php endif; ?>
		<?php
	}
}