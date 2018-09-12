<?php
/**
 * Payment gateway
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\NinjaForms
 */

namespace Pronamic\WordPress\Pay\Extensions\NinjaForms;

use NF_Abstracts_PaymentGateway;
use Pronamic\WordPress\Pay\Plugin;
use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Payment gateway
 *
 * @version 1.0.0
 * @since   1.0.0
 */
final class PaymentGateway extends NF_Abstracts_PaymentGateway {
	/**
	 * Slug.
	 *
	 * @var string
	 */
	protected $_slug = 'pronamic_pay';

	/**
	 * Name.
	 *
	 * @var string
	 */
	protected $_name = '';

	/**
	 * Settings.
	 *
	 * @var array
	 */
	protected $_settings = array();

	/**
	 * Constructor for the payment gateway.
	 */
	public function __construct() {
		$this->_name = __( 'Pronamic Pay', 'pronamic_ideal' );

		$this->_settings = $this->action_settings();
	}

	/**
	 * Processing form.
	 *
	 * @param array  $action_settings Action settings.
	 * @param string $form_id Form id.
	 * @param array  $data Form data.
	 * @return array|bool
	 */
	public function process( $action_settings, $form_id, $data ) {
		$config_id = get_option( 'pronamic_pay_config_id' );

		// A valid configuration ID is needed.
		if ( false === $config_id ) {
			return;
		}

		$payment_data = new PaymentData( $action_settings, $form_id, $data );

		$payment_method = $payment_data->get_payment_method();

		$gateway = Plugin::get_gateway( $config_id );

		if ( ! $gateway ) {
			return false;
		}

		// Set default payment method if neccessary.
		if ( empty( $payment_method ) && ( null !== $payment_data->get_issuer() || $gateway->payment_method_is_required() ) ) {
			$payment_method = PaymentMethods::IDEAL;
		}

		// Only start payments for known/active payment methods.
		if ( is_string( $payment_method ) && ! PaymentMethods::is_active( $payment_method ) ) {
			return false;
		}

		$payment = Plugin::start( $config_id, $gateway, $payment_data, $payment_method );

		if ( $gateway->has_error() ) {
			$error = $gateway->get_error();

			$message = sprintf(
				'%1$s: %2$s',
				$error->get_error_code(),
				$error->get_error_message()
			);

			$data['errors']['form']['pronamic-pay']         = Plugin::get_default_error_message();
			$data['errors']['form']['pronamic-pay-gateway'] = esc_html( $message );
		} else {
			$data['actions']['redirect'] = $payment->get_action_url();
		}

		return $data;
	}

	/**
	 * Action settings.
	 *
	 * @return array
	 */
	public function action_settings() {
		return array(
			'description' => array(
				'name'           => 'pronamic_pay_description',
				'type'           => 'textbox',
				'group'          => 'primary',
				'label'          => __( 'Transaction Description', 'pronamic_ideal' ),
				'placeholder'    => '',
				'value'          => '',
				'width'          => 'full',
				'use_merge_tags' => array(
					'include' => array(
						'calcs',
					),
				),
			),
		);
	}
}
