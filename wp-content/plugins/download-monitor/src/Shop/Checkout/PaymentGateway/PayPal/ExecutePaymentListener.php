<?php

namespace WPChill\DownloadMonitor\Shop\Checkout\PaymentGateway\PayPal;

use WPChill\DownloadMonitor\Shop\Checkout\PaymentGateway\PayPal\CaptureOrder;
use WPChill\DownloadMonitor\Shop\Services\Services;
use PHPUnit\Runner\Exception;

class ExecutePaymentListener {

	private $gateway;

	/**
	 * ExecutePaymentListener constructor.
	 *
	 * @param PayPalGateway $gateway
	 */
	public function __construct( $gateway ) {
		$this->gateway = $gateway;
	}

	public function run() {
		if ( isset( $_GET['paypal_action'] ) && 'execute_payment' === $_GET['paypal_action'] ) {
			$this->executePayment();
		}
	}

	/**
	 * Execute payment based on GET parameters
	 */
	private function executePayment() {

		/**
		 * Get order
		 */
		$order_id   = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
		$order_hash = isset( $_GET['order_hash'] ) ? sanitize_text_field( wp_unslash($_GET['order_hash']) ) : '';

		if ( empty( $order_id ) || empty( $order_hash ) ) {
			$this->execute_failed( $order_id, $order_hash );
			return;
		}

		/** @var \WPChill\DownloadMonitor\Shop\Order\Repository $order_repo */
		$order_repo = Services::get()->service( 'order_repository' );
		try {
			$order = $order_repo->retrieve_single( $order_id );
		} catch ( \Exception $exception ) {
			/**
			 * @todo log error in PayPal error log ($exception->getMessage())
			 */
			$this->execute_failed( $order_id, $order_hash );

			return;
		}

		// Verify order_hash against the retrieved order (timing-safe) to prevent IDOR.
		if ( ! hash_equals( (string) $order->get_hash(), (string) $order_hash ) ) {
			$this->execute_failed( $order_id, $order_hash );
			return;
		}

		/**
		 * Get payment identifier (PayPal order ID / token)
		 */
		$token = '';
		if ( isset( $_GET['token'] ) ) {
			$token = sanitize_text_field( wp_unslash( $_GET['token'] ) );
		}

		if ( empty( $token ) ) {
			$this->execute_failed( $order_id, $order_hash );
			return;
		}

		// Bind token to this order: token must match one of this order's transactions (PayPal order ID).
		$transactions = $order->get_transactions();
		$token_belongs_to_order = false;
		foreach ( $transactions as $transaction ) {
			if ( hash_equals( (string) $transaction->get_processor_transaction_id(), (string) $token ) ) {
				$token_belongs_to_order = true;
				break;
			}
		}
		if ( ! $token_belongs_to_order ) {
			$this->execute_failed( $order_id, $order_hash );
			return;
		}

		/**
		 * Execute the payment
		 */
		try {

			$capture = new CaptureOrder();
			$capture->set_client( $this->gateway->get_api_context() )
					->set_order_id( $token );

			$capture_result = $capture->captureOrder();

			// Handle capture failures safely (e.g. network error, invalid token).
			if ( null === $capture_result || ! $capture_result->has_response() ) {
				$this->execute_failed( $order->get_id(), $order->get_hash() );
				return;
			}

			$response = $capture_result;

			// if payment is not approved, exit;
			if ( $response->getStatus() !== "COMPLETED" ) {
				throw new Exception( sprintf( "Execute payment state is %s", $response->getStatus() ) );
			}

			/**
			 * Update transaction in local database
			 */
			// Update the transaction that belongs to this token (already validated above).
			$transaction_updated = false;
			$transactions         = $order->get_transactions();
			foreach ( $transactions as $transaction ) {
				if ( hash_equals( (string) $transaction->get_processor_transaction_id(), (string) $token ) ) {
					$transaction->set_status( Services::get()->service( 'order_transaction_factory' )->make_status( 'success' ) );
					$transaction->set_processor_status( $response->getStatus() );

					try {
						$transaction->set_date_modified( new \DateTimeImmutable( current_time( 'mysql' ) ) );
					} catch ( \Exception $e ) {
						// ?
					}

					$order->set_transactions( $transactions );
					$transaction_updated = true;
					break;
				}
			}

			// Only complete the order if we actually updated a matching transaction (prevents token/amount mismatch).
			if ( ! $transaction_updated ) {
				$this->execute_failed( $order->get_id(), $order->get_hash() );
				return;
			}

			// set order as completed, this also persists the order
			$order->set_completed();

			/**
			 * Redirect user to "clean" complete URL
			 */
			wp_redirect( $this->gateway->get_success_url( $order->get_id(), $order->get_hash() ), 302 );
			exit;

		} catch ( \Exception $ex ) {
			/**
			 * @todo add error logging for separate PayPal log
			 */
			$this->execute_failed( $order->get_id(), $order->get_hash() );

			return;
		}

	}

	/**
	 * This method gets called when execute failed. Reason for fail will be logged in PayPal log (if enabled).
	 * User will be redirected to the checkout 'failed' endpoint.
	 *
	 * @param int $order_id
	 * @param string $order_hash
	 */
	private function execute_failed( $order_id, $order_hash ) {
		wp_redirect( $this->gateway->get_failed_url( $order_id, $order_hash ), 302 );
		exit;
	}

}