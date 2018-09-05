<?php
/**
 * Payment Methods Field
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\NinjaForms
 */

namespace Pronamic\WordPress\Pay\Extensions\NinjaForms;

use NF_Abstracts_List;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Class PaymentMethodsField
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
	protected $_section = 'pricing';

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
		// Construct parent with filtered field settings options columns.
		add_filter( 'ninja_forms_field_settings', array( $this, 'field_settings_options' ), 10, 1 );

		parent::__construct();

		remove_filter( 'ninja_forms_field_settings', array( $this, 'field_settings_options' ) );

		// Set field properties.
		$this->_nicename = __( 'Payment Methods', 'pronamic_ideal' );

		$this->_settings['options']['value'] = $this->get_options();

		// Actions.
		add_action( 'ninja_forms_render_options_' . $this->_type, array( $this, 'render_options' ), 10, 2 );
	}

	/**
	 * Get default options.
	 *
	 * @return array
	 */
	private function get_options() {
		$options = $this->_settings['options']['value'];

		if ( ! is_admin() ) {
			return $options;
		}

		if ( ! empty( $options ) ) {
			return $options;
		}

		$options = array();
		$order   = 0;

		// Get gateway payment method options.
		$payment_methods = $this->get_gateway_payment_methods();

		foreach ( $payment_methods as $value => $label ) {
			$options[] = array(
				'label'    => $label,
				'value'    => $value . '" disabled="disabled" "',
				'calc'     => '',
				'selected' => 1,
				'order'    => ++$order,
			);
		}

		return $options;
	}

	/**
	 * Field settings options columns.
	 *
	 * @param array $settings Field settings.
	 *
	 * @return array
	 */
	public function field_settings_options( $settings ) {
		// Remove default options (one, two, three").
		$settings['options']['value'] = array();

		// Remove calc field for options.
		unset( $settings['options']['columns']['calc'] );

		// Remove checkmark icon.
		$settings['options']['columns']['selected']['header'] = '';

		return $settings;
	}

	/**
	 * Render options.
	 *
	 * @param array $options  Field select options.
	 * @param array $settings Field settings.
	 *
	 * @return array
	 */
	public function render_options( $options, $settings ) {
		$options = wp_list_filter( $options, array( 'selected' => 1 ) );

		foreach ( $options as &$option ) {
			$option['value'] = str_replace( '" disabled="disabled" "', '', $option['value'] );
		}

		return $options;
	}

	/**
	 * Get gateway available payment methods.
	 *
	 * @return array
	 */
	private function get_gateway_payment_methods() {
		$config_id       = get_option( 'pronamic_pay_config_id' );
		$gateway         = Plugin::get_gateway( $config_id );
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
