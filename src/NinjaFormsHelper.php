<?php
/**
 * Ninja Forms Helper
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2021 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\NinjaForms
 */

namespace Pronamic\WordPress\Pay\Extensions\NinjaForms;

/**
 * Ninja Forms Helper
 *
 * @version 1.3.0
 * @since   1.3.0
 */
class NinjaFormsHelper {
	/**
	 * Get source ID from submission data.
	 *
	 * @param array $data Submission data.
	 * @return string|int
	 */
	public static function get_source_id_from_submission_data( $data ) {
		/**
		 * Ninja Forms form submissions are only saved if a save action has been set.
		 * Only in this case a form submission ID is available.
		 */
		if ( isset( $data['actions']['save']['sub_id'] ) ) {
			return $data['actions']['save']['sub_id'];
		}

		return \time();
	}

	/**
	 * Get Collect Payment action settings.
	 *
	 * @param int|string $form_id Form ID.
	 * @return array|null
	 */
	public static function get_collect_payment_action_settings( $form_id ) {
		$actions = \Ninja_Forms()->form( $form_id )->get_actions();

		foreach ( $actions as $action ) {
			$action_settings = $action->get_settings();

			// Check Collect Payment action.
			if ( 'collectpayment' !== $action_settings['type'] ) {
				continue;
			}

			// Check gateway.
			if ( 'pronamic_pay' !== $action_settings['payment_gateways'] ) {
				continue;
			}

			return $action_settings;
		}

		return null;
	}

	/**
	 * Get config ID from action settings or use default config.
	 *
	 * @param array $action_settings Action settings.
	 * @return string
	 */
	public static function get_config_id_from_action_settings( $action_settings ) {
		$config_id = $action_settings['pronamic_pay_config_id'];

		if ( empty( $config_id ) ) {
			$config_id = \get_option( 'pronamic_pay_config_id' );
		}

		return $config_id;
	}

	/**
	 * Get description from action settings.
	 *
	 * @param array $action_settings Action settings.
	 * @return string
	 */
	public static function get_description_from_action_settings( $action_settings ) {
		return $action_settings['pronamic_pay_description'];
	}

	/**
	 * Get currency from form.
	 *
	 * @param int $form_id Form ID.
	 * @return string
	 */
	public static function get_currency_from_form_id( $form_id ) {
		$form = \Ninja_Forms()->form( $form_id )->get();

		$currency = $form->get_setting( 'currency' );

		if ( ! empty( $currency ) ) {
			// Return currency from form settings.
			return $currency;
		}

		// Return currency from Ninja Forms settings.
		return \Ninja_Forms()->get_setting( 'currency' );
	}

	/**
	 * Get payment method from submission data.
	 *
	 * @param array $data Form submission data.
	 * @return string|null
	 */
	public static function get_payment_method_from_submission_data( $data ) {
		$payment_method = null;

		// Get payment method from a payment method field if it exists.
		foreach ( $data['fields'] as $field ) {
			if ( 'pronamic_pay_payment_method' !== $field['type'] ) {
				continue;
			}

			$value = $field['value'];

			if ( ! empty( $value ) ) {
				$payment_method = $value;

				break;
			}
		}

		return $payment_method;
	}

	/**
	 * Get issuer from submission data.
	 *
	 * @param array $data Submission data.
	 * @return string|null
	 */
	public static function get_issuer_from_submission_data( $data ) {
		$issuer = null;

		// Get issuer from an issuers field if it exists.
		foreach ( $data['fields'] as $field ) {
			if ( 'pronamic_pay_issuer' !== $field['type'] ) {
				continue;
			}

			$issuer = $field['value'];

			break;
		}

		return $issuer;
	}

	/**
	 * Get page link from action settings.
	 *
	 * @param array  $action_settings Action settings.
	 * @param string $key             Setting key.
	 * @return string|null
	 */
	public static function get_page_link_from_action_settings( $action_settings, $key ) {
		if ( ! \array_key_exists( $key, $action_settings ) ) {
			return null;
		}

		$page_id = $action_settings[ $key ];

		if ( 'publish' !== get_post_status( $page_id ) ) {
			return null;
		}

		return \get_permalink( $page_id );
	}
}
