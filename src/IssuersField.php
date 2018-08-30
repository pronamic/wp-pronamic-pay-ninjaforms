<?php
/**
 * Issuers Field.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\NinjaForms
 */

namespace Pronamic\WordPress\Pay\Extensions\NinjaForms;

use NF_Abstracts_List;
use Pronamic\WordPress\Pay\Plugin;
use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Class IssuersField.
 */
class IssuersField extends NF_Abstracts_List {

	/**
	 * Name.
	 *
	 * @var string
	 */
	protected $_name = 'pronamic_pay_issuer';

	/**
	 * Type.
	 *
	 * @var string
	 */
	protected $_type = 'pronamic_pay_issuer';

	/**
	 * Nice name for display.
	 *
	 * @var string
	 */
	protected $_nicename = 'Bank Select';

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
	protected $_icon = 'bank';

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

		$this->_nicename = __( 'Bank Select', 'pronamic_ideal' );

		$this->_settings['options']['value'] = $this->get_options();
	}

	/**
	 * Get options.
	 *
	 * @return array
	 */
	private function get_options() {
		$options = array();
		$order   = 0;

		$config_id = get_option( 'pronamic_pay_config_id' );
		$gateway   = Plugin::get_gateway( $config_id );

		$gateway->set_payment_method( PaymentMethods::IDEAL );

		$issuers = $gateway->get_issuers();

		foreach ( $issuers[0]['options'] as $value => $label ) {
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
