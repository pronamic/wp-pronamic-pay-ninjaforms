<?php
/**
 * Payment methods field
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
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
 * @phpstan-type Option          array{label: string, value: string, calc: string, selected: int, order: int}
 * @phpstan-type SettingsOptions array{value: array<int, Option>, columns: array<string, string>}
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
	 * @override
	 * @var string
	 */
	protected $_old_classname = 'list-select';

	/**
	 * Settings.
	 *
	 * @var array{options?: SettingsOptions}
	 */
	protected $_settings = [];

	/**
	 * Constructs and initializes the field object.
	 */
	public function __construct() {
		parent::__construct();

		// Set field properties.
		$this->_nicename = __( 'Payment Methods', 'pronamic_ideal' );

		$this->_settings['options']['value'] = $this->get_pronamic_payment_method_options();

		add_filter( 'ninja_forms_render_options_' . $this->_type, [ $this, 'render_options' ] );

		// Remove calc field for options.
		unset( $this->_settings['options']['columns']['calc'] );
		unset( $this->_settings['options']['columns']['selected'] );
	}

	/**
	 * Get default Pronamic payment method options.
	 *
	 * @return array<int, Option>
	 */
	private function get_pronamic_payment_method_options() {
		$options = [];

		$order = 0;

		// Get gateway payment method options.
		$payment_methods = $this->get_pronamic_gateway_payment_methods();

		foreach ( $payment_methods as $value => $label ) {
			$options[] = [
				'label'    => $label,
				'value'    => $value,
				'calc'     => '',
				'selected' => 1,
				'order'    => $order,
			];

			++$order;
		}

		return $options;
	}

	/**
	 * Render options.
	 *
	 * @param array $options Options.
	 * @return array<string, string>
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
	 * @return array<string, string>
	 */
	private function get_pronamic_gateway_payment_methods() {
		$form_id = \filter_input( \INPUT_GET, 'form_id', \FILTER_SANITIZE_NUMBER_INT );

		$action_settings = NinjaFormsHelper::get_collect_payment_action_settings( $form_id );

		if ( null === $action_settings ) {
			return [];
		}

		$config_id = NinjaFormsHelper::get_config_id_from_action_settings( $action_settings );

		$gateway = Plugin::get_gateway( $config_id );

		if ( null === $gateway ) {
			return [];
		}

		$payment_methods = $gateway->get_payment_methods(
			[
				'status' => [ '', 'active' ],
			]
		);

		$result = [];

		foreach ( $payment_methods as $payment_method ) {
			$result[ $payment_method->get_id() ] = $payment_method->get_name();
		}

		return $result;
	}
}
