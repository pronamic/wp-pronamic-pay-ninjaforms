<?php
/**
 * Extension
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\NinjaForms
 */

namespace Pronamic\WordPress\Pay\Extensions\NinjaForms;

use Pronamic\WordPress\Pay\Payments\Payment;

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
	 * Bootstrap.
	 */
	public static function bootstrap() {
		new self();
	}

	/**
	 * Construct.
	 */
	public function __construct() {
		add_filter( 'pronamic_payment_source_text_' . self::SLUG, array( $this, 'source_text' ), 10, 2 );
		add_filter( 'pronamic_payment_source_description_' . self::SLUG, array( $this, 'source_description' ), 10, 2 );
		add_filter( 'pronamic_payment_source_url_' . self::SLUG, array( $this, 'source_url' ), 10, 2 );

		add_filter( 'ninja_forms_register_fields', array( $this, 'register_fields' ), 10, 3 );
		add_filter( 'ninja_forms_register_payment_gateways', array( $this, 'register_payment_gateways' ), 10, 1 );
	}

	/**
	 * Register custom fields
	 *
	 * @param array $fields Fields from Ninja Forms.
	 * @return array $fields
	 */
	public function register_fields( $fields ) {
		$fields['pronamic_pay_payment_method'] = new PaymentMethodsField();
		$fields['pronamic_pay_issuer']         = new IssuersField();

		return $fields;
	}

	/**
	 * Register payment gateways.
	 *
	 * @param array $gateways Payment gateways.
	 *
	 * @return array
	 */
	public function register_payment_gateways( $gateways ) {
		$gateways['pronamic_pay'] = new PaymentGateway();

		return $gateways;
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
		$source_id = $payment->get_source_id();

		if ( empty( $source_id ) ) {
			return $url;
		}

		$source_id = intval( $source_id );

		// Source ID could be a submission ID.
		if ( 'nf_sub' === get_post_type( $source_id ) ) {
			$url = add_query_arg(
				array(
					'post'   => $source_id,
					'action' => 'edit',
				),
				admin_url( 'post.php' )
			);
		}

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

		$source_id = $payment->get_source_id();

		if ( empty( $source_id ) ) {
			return $text;
		}

		$source_id = intval( $source_id );

		if ( 'nf_sub' === get_post_type( $source_id ) ) {
			$text .= sprintf(
				'<a href="%s">%s</a>',
				add_query_arg(
					array(
						'post'   => $source_id,
						'action' => 'edit',
					),
					admin_url( 'post.php' )
				),
				/* translators: %s: payment source id */
				sprintf( __( 'Entry #%s', 'pronamic_ideal' ), $source_id )
			);
		} else {
			/* translators: %s: payment source id */
			$text .= sprintf( __( '#%s', 'pronamic_ideal' ), $source_id );
		}

		return $text;
	}

	/**
	 * Source description.
	 *
	 * @param string  $description Description.
	 * @param Payment $payment     Payment.
	 *
	 * @return string
	 */
	public function source_description( $description, Payment $payment ) {
		$description = __( 'Ninja Forms', 'pronamic_ideal' );

		$source_id = $payment->get_source_id();

		if ( empty( $source_id ) ) {
			return $description;
		}

		$source_id = intval( $source_id );

		if ( 'nf_sub' === get_post_type( $source_id ) ) {
			$description = __( 'Ninja Forms Entry', 'pronamic_ideal' );
		}

		return $description;
	}
}
