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

/**
 * Class Pay_PaymentMethodsField
 */
class PaymentMethodsField extends NF_Abstracts_List {

	/**
	 * Name.
	 *
	 * @var string
	 */
	protected $_name = 'paymentmethods';

	/**
	 * Type.
	 *
	 * @var string
	 */
	protected $_type = 'paymentmethods';

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
	protected $_icon = 'paypal';

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
	 * Constructs and initializes the field object.
	 */
	public function __construct() {
		parent::__construct();

		$this->_nicename = __( 'Payment Methods', 'ninja-forms' );

		$this->_settings['options']['value'] = $this->get_options();
	}

	/**
	 * Get options.
	 *
	 * @return array
	 */
	private function get_options() {
		$order   = 0;
		$options = array();

		foreach ( PaymentMethods::get_payment_methods() as $value => $label ) {
			$options[] = array(
				'label'    => $label,
				'value'    => $value,
				'calc'     => '',
				'selected' => 0,
				'order'    => $order,
			);

			$order++;
		}

		return $options;
	}
}
