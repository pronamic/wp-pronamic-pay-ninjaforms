<?php
/**
 * Payment methods field
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2021 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\NinjaForms
 */

namespace Pronamic\WordPress\Pay\Extensions\NinjaForms;

use NF_Abstracts_List;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Payment methods field
 *
 * @version 1.0.1
 * @since   1.0.0
 */
class PaymentMethodsField extends NF_Abstracts_List {

	/**
	 * Name.
	 *
	 * @var string
	 */
	protected $_name = 'pronamic_pay_payment_method';

	/**
	 * Type.
	 *
	 * @var string
	 */
	protected $_type = 'pronamic_pay_payment_method';

	/**
	 * Nice name for display.
	 *
	 * @var string
	 */
	protected $_nicename = 'Payment Methods';

	/**
	 * Section.
	 *
	 * @var string
	 */
	protected $_section = 'pronamic_pay';

	/**
	 * Icon.
	 *
	 * @var string
	 */
	protected $_icon = 'credit-card';

	/**
	 * Template.
	 *
	 * @var string
	 */
	protected $_templates = 'listselect';

	/**
	 * Old classname for earlier versions.
	 *
	 * @var string
	 */
	protected $_old_classname = 'list-select';

	/**
	 * Settings.
	 *
	 * @var array
	 */
	protected $_settings = array();

	/**
	 * Constructs and initializes the field object.
	 */
	public function __construct() {
		parent::__construct();

		// Set field properties.
		$this->_nicename = __( 'Payment Methods', 'pronamic_ideal' );

		$this->_settings['options']['value'] = $this->get_pronamic_payment_method_options();

		add_filter( 'ninja_forms_render_options_' . $this->_type, array( $this, 'render_options' ) );

		// Remove calc field for options.
		unset( $this->_settings['options']['columns']['calc'] );
		unset( $this->_settings['options']['columns']['selected'] );
	}

	/**
	 * Get default Pronamic payment method options.
	 *
	 * @return array
	 */
	private function get_pronamic_payment_method_options() {
		$options = array();

		$order = 0;

		// Get gateway payment method options.
		$payment_methods = $this->get_pronamic_gateway_payment_methods();

		foreach ( $payment_methods as $value => $label ) {
			$options[] = array(
				'label'    => $label,
				'value'    => $value,
				'calc'     => '',
				'selected' => 1,
				'order'    => $order,
			);

			$order++;
		}

		return $options;
	}

	/**
	 * Render options.
	 *
	 * @param array $options Options.
	 *
	 * @return array
	 */
	public function render_options( $options ) {
		foreach ( $options as &$option ) {
			if ( 0 === $option['value'] ) {
				$option['value'] = '';
			}
		}

		return $options;
	}

	/**
	 * Get gateway available payment methods.
	 *
	 * @return array
	 */
	private function get_pronamic_gateway_payment_methods() {
		$payment_methods = array();

		$config_id = get_option( 'pronamic_pay_config_id' );
		$gateway   = Plugin::get_gateway( $config_id );

		if ( null === $gateway ) {
			return $payment_methods;
		}

		$payment_methods = $gateway->get_payment_method_field_options();

		if ( empty( $payment_methods ) ) {
			$active_methods = PaymentMethods::get_active_payment_methods();

			foreach ( $active_methods as $payment_method ) {
				$payment_methods[ $payment_method ] = PaymentMethods::get_name( $payment_method );
			}
		}

		return $payment_methods;
	}
}
