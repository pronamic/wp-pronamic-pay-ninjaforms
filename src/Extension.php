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
		add_filter( 'ninja_forms_register_fields', array( $this, 'register_fields' ), 10, 3  );

		$payment_action = new PaymentAction();
	}

	/**
	 * Register custom fields
	 */
	public function register_fields( $fields ) {
		include self::$dir . 'fields/PaymentMethodsField.php';
		$fields['paymentmethods'] = new Pay_PaymentMethodsField;

        return $fields;
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
		
	}

	/**
	 * Registered form actions.
	 *
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
	 * Source text.
	 *
	 * @param string  $text    Source text.
	 * @param Payment $payment Payment.
	 *
	 * @return string
	 */
	public static function source_text( $text, Payment $payment ) {
		$text = __( 'Ninja Forms', 'pronamic_ideal' ) . '<br />';

		$text .= sprintf(
			'<a href="%s">%s</a>',
			add_query_arg( array(
				'page'       => 'ninja-forms',
				'frm_action' => 'show',
				'id'         => $payment->get_source_id(),
			), admin_url( 'admin.php' ) ),
			/* translators: %s: payment source id */
			sprintf( __( 'Entry #%s', 'pronamic_ideal' ), $payment->get_source_id() )
		);

		return $text;
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