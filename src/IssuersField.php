<?php
/**
 * Issuers field
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\NinjaForms
 */

namespace Pronamic\WordPress\Pay\Extensions\NinjaForms;

use NF_Abstracts_List;
use Pronamic\WordPress\Pay\Fields\IDealIssuerSelectField;
use Pronamic\WordPress\Pay\Plugin;
use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Issuers field
 *
 * @version 1.0.3
 * @since   1.0.0
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
	protected $_nicename = 'Issuer';

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
	protected $_settings = [];

	/**
	 * Form ID.
	 *
	 * @var int
	 */
	public $form_id;

	/**
	 * Constructs and initializes the field object.
	 */
	public function __construct() {
		parent::__construct();

		$this->_nicename = __( 'Issuer', 'pronamic_ideal' );

		$this->_settings['options']['value'] = [];

		// Actions.
		\add_action( 'ninja_forms_render_options_' . $this->_type, [ $this, 'render_options' ], 10, 0 );
		\add_action( 'nf_get_form_id', [ $this, 'set_form_id' ] );
	}

	/**
	 * Set form ID.
	 *
	 * @param int $form_id Form ID.
	 * @return void
	 */
	public function set_form_id( $form_id ) {
		$this->form_id = $form_id;
	}

	/**
	 * Get settings.
	 *
	 * @return array
	 */
	public function get_settings() {
		// Hide options settings in form builder.
		if ( is_admin() ) {
			unset( $this->_settings['options'] );
		}

		return $this->_settings;
	}

	/**
	 * Get options.
	 *
	 * @return array
	 */
	public function render_options() {
		$action_settings = NinjaFormsHelper::get_collect_payment_action_settings( $this->form_id );

		if ( null === $action_settings ) {
			return [];
		}

		$config_id = NinjaFormsHelper::get_config_id_from_action_settings( $action_settings );

		$gateway = Plugin::get_gateway( $config_id );

		if ( null === $gateway ) {
			return [];
		}

		$issuer_field = $gateway->first_payment_method_field( PaymentMethods::IDEAL, IDealIssuerSelectField::class );

		if ( null === $issuer_field ) {
			return [];
		}

		$options = [];

		$order = 0;

		foreach ( $issuer_field->get_flat_options() as $option ) {
			$options[] = [
				'label'    => $option->label,
				'value'    => $option->value,
				'calc'     => '',
				'selected' => 0,
				'order'    => ++$order,
			];
		}

		return $options;
	}
}
