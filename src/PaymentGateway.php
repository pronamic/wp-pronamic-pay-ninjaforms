<?php
/**
 * Payment gateway
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2021 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\NinjaForms
 */

namespace Pronamic\WordPress\Pay\Extensions\NinjaForms;

use NF_Abstracts_PaymentGateway;
use Pronamic\WordPress\Money\Currency;
use Pronamic\WordPress\Money\TaxedMoney;
use Pronamic\WordPress\Pay\Plugin;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Payment gateway
 *
 * @version 1.5.1
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
		// Check if resuming form action processing.
		if ( \defined( 'PRONAMIC_PAY_NINJA_FORMS_RESUME' ) && PRONAMIC_PAY_NINJA_FORMS_RESUME ) {
			return false;
		}

		// Gateway.
		$config_id = NinjaFormsHelper::get_config_id_from_action_settings( $action_settings );
		$gateway   = Plugin::get_gateway( $config_id );

		if ( ! $gateway ) {
			return false;
		}

		/**
		 * Build payment.
		 */
		$payment = new Payment();

		$payment->source    = 'ninja-forms';
		$payment->source_id = NinjaFormsHelper::get_source_id_from_submission_data( $data );
		$payment->order_id  = $payment->source_id;

		$payment->description = NinjaFormsHelper::get_description_from_action_settings( $action_settings );

		if ( empty( $payment->description ) ) {
			$payment->description = sprintf(
				'%s #%s',
				__( 'Submission', 'pronamic_ideal' ),
				$payment->source_id
			);
		}

		$payment->title = sprintf(
			/* translators: %s: payment data title */
			__( 'Payment for %s', 'pronamic_ideal' ),
			$payment->description
		);

		// Currency.
		$currency = Currency::get_instance( NinjaFormsHelper::get_currency_from_form_id( $form_id ) );

		// Amount.
		$payment->set_total_amount( new TaxedMoney( $action_settings['payment_total'], $currency ) );

		// Method.
		$payment->method = NinjaFormsHelper::get_payment_method_from_submission_data( $data );

		// Issuer.
		$payment->issuer = NinjaFormsHelper::get_issuer_from_submission_data( $data );

		// Configuration.
		$payment->config_id = $config_id;

		// Set default payment method if necessary.
		if ( empty( $payment->method ) && ( null !== $payment->issuer || $gateway->payment_method_is_required() ) ) {
			$payment->method = PaymentMethods::IDEAL;
		}

		// Only start payments for known/active payment methods.
		if ( is_string( $payment->method ) && ! PaymentMethods::is_active( $payment->method ) ) {
			return false;
		}

		try {
			$payment = Plugin::start_payment( $payment );

			// Save form and action ID in payment meta for use in redirect URL.
			$payment->set_meta( 'ninjaforms_payment_action_id', $action_settings['id'] );
			$payment->set_meta( 'ninjaforms_payment_form_id', $form_id );

			// Save session cookie in payment meta for processing delayed actions.
			\Ninja_Forms()->session()->set( 'pronamic_payment_id', $payment->get_id() );

			$headers = headers_list();

			foreach ( $headers as $header ) {
				if ( 'set-cookie' !== substr( strtolower( $header ), 0, 10 ) ) {
					continue;
				}

				$cookie = \explode( ';', $header );

				$cookie = trim( \substr( $cookie[0], 12 ) );

				$cookie = \explode( '=', $cookie );

				$payment->set_meta( 'ninjaforms_session_cookie', $cookie[1] );
			}

			// Set form processing data.
			$data['halt']                         = true;
			$data['actions']['redirect']          = $payment->get_pay_redirect_url();
			$data['actions']['success_message']   = __( 'Please wait while you are redirected to complete the payment.', 'pronamic_ideal' );
			$data['extra']['pronamic_payment_id'] = $payment->get_id();
		} catch ( \Exception $e ) {
			$message = sprintf( '%1$s: %2$s', $e->getCode(), $e->getMessage() );

			$data['errors']['form']['pronamic-pay']         = Plugin::get_default_error_message();
			$data['errors']['form']['pronamic-pay-gateway'] = esc_html( $message );
		}

		return $data;
	}

	/**
	 * Action settings.
	 *
	 * @return array
	 */
	public function action_settings() {
		$settings = array();

		// Configuration.
		$settings['config_id'] = array(
			'label'   => __( 'Configuration', 'pronamic_ideal' ),
			'name'    => 'pronamic_pay_config_id',
			'group'   => 'pronamic_pay',
			'type'    => 'select',
			'width'   => 'full',
			'options' => array(),
		);

		foreach ( Plugin::get_config_select_options() as $value => $label ) {
			if ( 0 === $value ) {
				$label = \__( '— Default Gateway —', 'pronamic_ideal' );
			}

			$settings['config_id']['options'][] = array(
				'label' => $label,
				'value' => $value,
			);
		}

		// Description.
		$settings['description'] = array(
			'name'           => 'pronamic_pay_description',
			'type'           => 'textbox',
			'group'          => 'pronamic_pay',
			'label'          => __( 'Transaction Description', 'pronamic_ideal' ),
			'placeholder'    => '',
			'value'          => '',
			'width'          => 'full',
			'use_merge_tags' => array(
				'include' => array(
					'calcs',
				),
			),
		);

		/*
		 * Status pages.
		 */
		$settings['pronamic_pay_status_pages'] = array(
			'name'     => 'pronamic_pay_status_pages',
			'type'     => 'fieldset',
			'label'    => __( 'Payment Status Pages', 'pronamic_ideal' ),
			'width'    => 'full',
			'group'    => 'pronamic_pay',
			'settings' => array(),
		);

		$options = array(
			array(
				'label' => __( '— Select —', 'pronamic_ideal' ),
			),
		);

		foreach ( \get_pages() as $page ) {
			$options[] = array(
				'label' => $page->post_title,
				'value' => $page->ID,
			);
		}

		// Add settings fields.
		foreach ( \pronamic_pay_plugin()->get_pages() as $id => $label ) {
			$settings['pronamic_pay_status_pages']['settings'][] = array(
				'name'        => $id,
				'type'        => 'select',
				'group'       => 'pronamic_pay',
				'label'       => $label,
				'placeholder' => '',
				'value'       => '',
				'width'       => 'full',
				'options'     => $options,
			);
		}

		/*
		 * Delayed actions.
		 */
		$form_id = \filter_input( \INPUT_GET, 'form_id', \FILTER_SANITIZE_NUMBER_INT );

		if ( null !== $form_id ) {
			$settings['pronamic_pay_delayed_actions'] = array(
				'name'     => 'pronamic_pay_delayed_actions',
				'type'     => 'fieldset',
				'label'    => __( 'Delayed actions', 'pronamic_ideal' ),
				'width'    => 'full',
				'group'    => 'pronamic_pay',
				'settings' => array(),
			);

			$actions = \Ninja_Forms()->form( $form_id )->get_actions();

			$no_delay_types = array( 'successmessage' );

			foreach ( $actions as $action ) {
				$action_type = $action->get_setting( 'type' );

				// Check action timing and priority. Only `late` (1) actions can be delayed
				// with a priority higher than the `collectpayment` action (`0`).
				$type = \Ninja_Forms()->actions[ $action_type ];

				if ( null === $type ) {
					continue;
				}

				if ( ! ( 1 === $type->get_timing() && $type->get_priority() > 0 ) ) {
					continue;
				}

				// Check if action type can be delayed.
				if ( \in_array( $action_type, $no_delay_types, true ) ) {
					continue;
				}

				// Add setting.
				$settings['pronamic_pay_delayed_actions']['settings'][] = array(
					'name'  => sprintf( 'pronamic_pay_delayed_action_%d', $action->get_id() ),
					'type'  => 'toggle',
					'width' => 'full',
					'label' => $action->get_setting( 'label' ),
				);
			}
		}

		return $settings;
	}
}
