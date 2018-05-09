<?php

namespace Pronamic\WordPress\Pay\Extensions\NinjaForms;

use NF_Abstracts_Action;
use Pronamic\WordPress\Pay\Plugin;

final class PaymentAction extends NF_Abstracts_Action {
	/**
	 * Payment methods
	 *
	 * @var array
	 */
    public $payment_methods = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'init', array( $this, 'init' ) );

		$this->_name = 'pronamicpay';

		$this->_nicename = __( 'Pronamic Pay', 'ninja-forms' );

		$settings = Extension::config( 'PaymentActionSettings' );

		$this->_settings = array_merge( $this->_settings, $settings );
		$this->_tags = array(
			'payment',
			'gateway'
		);

		add_action( 'ninja_forms_register_actions', array( $this, 'register_actions' ) );
	}

	public function init() {
		$this->payment_methods = \Pronamic\WordPress\Pay\Plugin::get_config_select_options();
	}

	public function register_actions( $actions ) {
		$actions[ 'pronamicpay' ] = new PaymentAction();

		return $actions;
	}

	public function process( $action_settings, $form_id, $data ) {
		//wp_nonce_field( 'pronamic_pay_save_form_options', 'pronamic_pay_nonce' );
		$fields = array();
		$description = $action_settings['description'];
		$amount = $action_settings['amount'];

		$i = 0;

		foreach ( $data['fields'] as $field ) {
			if ( 'submit' !== $field['type'] ) {
				$fields[$i]->id = $field['id'];
				$fields[$i]->value = $field['value'];
				$fields[$i]->type = $field['type'];
			}

			$i++;
		}

		$data[ 'actions' ][ 'redirect' ] = 'http://google.com/';
		return $data;
	}

	/**
	 * Get payment method options.
	 */
	public static function get_payment_methods() {
		$payment_methods = array();

		$config_id = get_option( 'pronamic_pay_config_id' );

		$gateway = Plugin::get_gateway( $config_id );

		if ( ! $gateway ) {
			return $payment_methods;
		}

		$options = $gateway->get_payment_method_field_options();

		$error = $gateway->get_error();

		if ( is_wp_error( $error ) || ! $options ) {
			return $payment_methods;
		}

		foreach ( $options as $payment_method => $name ) {
			$value = 'pronamic_pay';

			if ( ! empty( $payment_method ) ) {
				$value = sprintf( 'pronamic_pay_%s', $payment_method );
			}

			$payment_methods[] = array(
				'label' => $name,
				'value' => $value,
			);
		}

		return $payment_methods;
	}
}

?>