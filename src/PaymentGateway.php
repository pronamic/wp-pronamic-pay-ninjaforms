<?php
/**
 * Payment Gateway.
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
 * Payment Action class
 *
 * @author Ruben Droogh
 * @since 1.0.0
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
		parent::__construct();

		$this->_name = __( 'Pronamic Pay', 'pronamic_ideal' );

		$this->_settings = $this->action_settings();
	}

	/**
	 * Processing form.
	 *
	 * @param array  $action_settings Action settings.
	 * @param string $form_id Form id.
	 * @param array  $data Form data.
	 * @return array
	 */
	public function process( $action_settings, $form_id, $data ) {
		$config_id = get_option( 'pronamic_pay_config_id' );

		$payment_data   = new PaymentData( $form_id, $action_settings );
		$payment_method = $payment_data->get_payment_method();

		$gateway = Plugin::get_gateway( $config_id );

		if ( ! $gateway ) {
			return;
		}

		if ( empty( $payment_method ) && ( null !== $payment_data->get_issuer() || $gateway->payment_method_is_required() ) ) {
			$payment_method = PaymentMethods::IDEAL;
		}

		// Only start payments for known/active payment methods.
		if ( ! PaymentMethods::is_active( $payment_method ) ) {
			return;
		}

		$payment = Plugin::start( $config_id, $gateway, $payment_data, $payment_method );

		$error = $gateway->get_error();

		if ( ! is_wp_error( $error ) ) {
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
				'name'           => 'description',
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
