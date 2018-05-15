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
		$config_id = get_option( 'pronamic_pay_config_id' );
		$payment_data = new PaymentData( $form_id, $action_settings );

		$gateway = Plugin::get_gateway( $config_id );

		if ( ! $gateway ) {
			return;
		}

		Plugin::start( $form_id, $form_id, $payment_data, $gateway );

		return $data;
	}
}

?>