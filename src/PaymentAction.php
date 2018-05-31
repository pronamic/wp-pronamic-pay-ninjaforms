<?php
/**
 * Payment Action
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\NinjaForms
 */

namespace Pronamic\WordPress\Pay\Extensions\NinjaForms;

use NF_Abstracts_Action;
use Pronamic\WordPress\Pay\Plugin;
use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Payment Action class
 *
 * @author Ruben Droogh
 * @since 1.0.0
 */
final class PaymentAction extends NF_Abstracts_Action {
	/**
	 * Payment methods.
	 *
	 * @var array
	 */
	public $payment_methods = array();

	/**
	 * Name.
	 *
	 * @var string
	 */
	protected $_name = '';

	/**
	 * Nice name.
	 *
	 * @var string
	 */
	protected $_nicename = '';

	/**
	 * Settings.
	 *
	 * @var array
	 */
	protected $_settings = array();

	/**
	 * Tags.
	 *
	 * @var array
	 */
	protected $_tags = array();

	/**
	 * Constructor for the payment action.
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'init', array( $this, 'init' ) );

		$this->_name = 'pronamicpay';

		$this->_nicename = __( 'Pronamic Pay', 'pronamic_ideal' );

		$settings = $this->action_settings();

		$this->_settings = array_merge( $this->_settings, $settings );
		$this->_tags     = array(
			'payment',
			'gateway',
		);

		add_action( 'ninja_forms_register_actions', array( $this, 'register_actions' ) );
	}

	/**
	 * Init.
	 */
	public function init() {
		$this->payment_methods = \Pronamic\WordPress\Pay\Plugin::get_config_select_options();
	}

	/**
	 * Register actions.
	 *
	 * @param array $actions Actions array from Ninja Forms.
	 * @return array $actions
	 */
	public function register_actions( $actions ) {
		$actions['pronamicpay'] = new PaymentAction();

		return $actions;
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

		// Only start payments for known/active payment methods.
		if ( ! PaymentMethods::is_active( $payment_method ) ) {
			return;
		}

		if ( empty( $payment_method ) && ( null !== $payment_data->get_issuer() || $gateway->payment_method_is_required() ) ) {
			$payment_method = PaymentMethods::IDEAL;
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

			'amount'      => array(
				'name'           => 'amount',
				'type'           => 'textbox',
				'group'          => 'primary',
				'label'          => __( 'Payment Amount', 'pronamic_ideal' ),
				'placeholder'    => '',
				'value'          => '',
				'width'          => 'one-half',
				'help'           => __( 'Select the correct field using the icon on the right, or enter a fixed amount.', 'pronamic_ideal' ),
				'use_merge_tags' => array(
					'include' => array(
						'calcs',
					),
				),
			),

			'method'      => array(
				'name'           => 'method',
				'type'           => 'field-select',
				'group'          => 'primary',
				'label'          => __( 'Payment method', 'pronamic_ideal' ),
				'placeholder'    => '',
				'value'          => '',
				'width'          => 'one-half',
				'help'           => __( 'Use the special "Payment Methods" field for this.', 'pronamic_ideal' ),
				'field_types'    => array(
					'paymentmethods',
				),
				'use_merge_tags' => array(
					'include' => array(
						'calcs',
					),
				),
			),

			'bank'        => array(
				'name'           => 'bank',
				'type'           => 'field-select',
				'group'          => 'primary',
				'label'          => __( 'Bank Select Field', 'pronamic_ideal' ),
				'placeholder'    => '',
				'value'          => '',
				'width'          => 'one-half',
				'field_types'    => array(
					'bankselect',
				),
				'use_merge_tags' => array(
					'include' => array(
						'calcs',
					),
				),
			),
		);
	}
}
