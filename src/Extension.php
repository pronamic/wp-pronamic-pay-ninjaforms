<?php

namespace Pronamic\WordPress\Pay\Extensions\NinjaForms;

use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Core\Statuses;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Title: Ninja Forms extension
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Ruben Droogh
 */
class Extension {
	/**
	 * Slug
	 *
	 * @var string
	 */
	const SLUG = 'ninja-forms';

	/**
     * Plugin Directory
     *
     * @since 3.0
     * @var string $dir
     */
    public static $dir = '';

	/**
	 * Bootstrap.
	 */
	public static function bootstrap() {
		new self();
	}

	/**
	 * Construct.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );

		add_action( 'pronamic_payment_status_update_' . self::SLUG, array( $this, 'update_status' ), 10, 2 );
		add_filter( 'pronamic_payment_source_text_' . self::SLUG, array( $this, 'source_text' ), 10, 2 );
		add_filter( 'pronamic_payment_source_description_' . self::SLUG, array( $this, 'source_description' ), 10, 2 );
		add_filter( 'pronamic_payment_source_url_' . self::SLUG, array( $this, 'source_url' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_filter( 'ninja_forms_enable_credit_card_fields', '__return_true' );

		add_action( 'ninja_trigger_pronamic_pay_create_action', array( $this, 'create_action' ), 10, 3 );

		$payment_action = new PaymentAction();
	}

	/**
	 * Initialize.
	 */
	public function init() {
		
	}

	/**
	 * Admin enqueue scripts.
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();

		// $in_form_editor = ( 'toplevel_page_formidable' === $screen->id && 'edit' === filter_input( INPUT_GET, 'frm_action', FILTER_SANITIZE_STRING ) );
		// $in_settings    = ( 'toplevel_page_formidable' === $screen->id && 'settings' === filter_input( INPUT_GET, 'frm_action', FILTER_SANITIZE_STRING ) );

		// if ( ! $in_form_editor && ! $in_settings ) {
		// 	return;
		// }

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_style(
			'pronamic-pay-ninja-forms',
			plugins_url( 'css/admin' . $min . '.css', dirname( __FILE__ ) ),
			array(),
			'1.0.0'
		);

		wp_register_script(
			'pronamic-pay-ninja-forms',
			plugins_url( 'js/admin' . $min . '.js', dirname( __FILE__ ) ),
			array( 'jquery' ),
			'1.0.0',
			true
		);

		wp_enqueue_style( 'pronamic-pay-ninja-forms' );

		wp_enqueue_script( 'pronamic-pay-ninja-forms' );
	}

	/**
	 * Update entry payment status of the specified payment
	 *
	 * @param Payment $payment
	 * @param bool    $can_redirect
	 *
	 * @since unreleased
	 */
	public function update_status( Payment $payment, $can_redirect = false ) {

	}

	/**
	 * Source text.
	 *
	 * @param string  $text    Source text.
	 * @param Payment $payment Payment.
	 *
	 * @return string
	 */
	public static function source_text( $text, Payment $payment ) {

	}

	/**
	 * Source description.
	 *
	 * @param string  $description Source description.
	 * @param Payment $payment     Payment.
	 *
	 * @return string|void
	 */
	public function source_description( $description, Payment $payment ) {
		return __( 'Ninja Forms Entry', 'pronamic_ideal' );
	}

	/**
	 * Registered form actions.
	 *
	 * @see https://github.com/wp-premium/formidable-paypal/blob/3.02/controllers/FrmPaymentSettingsController.php#L125-L128
	 * @see https://github.com/wp-premium/formidable-paypal/blob/3.02/models/FrmPaymentAction.php
	 *
	 * @param array $actions Formidable Forms form actions.
	 *
	 * @return array
	 */
	public function registered_form_actions( $actions ) {
		$actions['pronamic_pay'] = __NAMESPACE__ . '\PaymentAction';

		return $actions;
	}

	/**
	 * Create action.
	 *
	 * @see https://github.com/wp-premium/formidable/blob/2.0.21/classes/controllers/FrmFormActionsController.php#L299-L308
	 * @see https://github.com/wp-premium/formidable-paypal/blob/3.02/controllers/FrmPaymentsController.php#L186-L193
	 *
	 * @param $action
	 * @param $entry
	 * @param $form
	 */
	public function create_action( $action, $entry, $form ) {
		$act = new PaymentAction();
	}

	/**
	 * Source URL.
	 *
	 * @param string  $url     Source URL.
	 * @param Payment $payment Payment.
	 *
	 * @return string
	 */
	public function source_url( $url, Payment $payment ) {
		$url = add_query_arg( array(
			'page'       => 'ninja-entries',
			'frm_action' => 'show',
			'id'         => $payment->get_source_id(),
		), admin_url( 'admin.php' ) );

		return $url;
	}

	/**
     * Config
     *
     * @param $file_name
     * @return mixed
     */
    public static function config( $file_name ) {
        return include self::$dir . 'config/' . $file_name . '.php';
    }
}

?>