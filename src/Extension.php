<?php
/**
 * Extension
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\NinjaForms
 */

namespace Pronamic\WordPress\Pay\Extensions\NinjaForms;

use Pronamic\WordPress\Pay\AbstractPluginIntegration;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;

/**
 * Extension
 *
 * @version 1.3.0
 * @since   1.0.0
 */
class Extension extends AbstractPluginIntegration {
	/**
	 * Slug
	 *
	 * @var string
	 */
	const SLUG = 'ninja-forms';

	/**
	 * Construct Ninja Forms plugin integration.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name' => __( 'Ninja Forms', 'pronamic_ideal' ),
			)
		);

		// Dependencies.
		$dependencies = $this->get_dependencies();

		$dependencies->add( new NinjaFormsDependency() );
	}

	/**
	 * Setup.
	 */
	public function setup() {
		\add_filter( 'pronamic_payment_source_description_' . self::SLUG, array( $this, 'source_description' ), 10, 2 );
		\add_filter( 'pronamic_payment_source_text_' . self::SLUG, array( $this, 'source_text' ), 10, 2 );

		// Check if dependencies are met and integration is active.
		if ( ! $this->is_active() ) {
			return;
		}

		\add_filter( 'pronamic_payment_source_url_' . self::SLUG, array( $this, 'source_url' ), 10, 2 );
		\add_filter( 'pronamic_payment_redirect_url_' . self::SLUG, array( $this, 'redirect_url' ), 10, 2 );

		\add_filter( 'ninja_forms_field_type_sections', array( $this, 'field_type_sections' ) );
		\add_filter( 'ninja_forms_register_fields', array( $this, 'register_fields' ), 10, 3 );
		\add_filter( 'ninja_forms_register_payment_gateways', array( $this, 'register_payment_gateways' ), 10, 1 );
		\add_filter( 'ninja_forms_field_settings_groups', array( $this, 'register_settings_groups' ) );
	}

	/**
	 * Filter field type sections.
	 *
	 * @param array $sections Field type sections.
	 *
	 * @return array
	 */
	public function field_type_sections( $sections ) {
		$sections['pronamic_pay'] = array(
			'id'         => 'pronamic_pay',
			'nicename'   => __( 'Pronamic Pay', 'pronamic_ideal' ),
			'fieldTypes' => array(),
		);

		return $sections;
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
	 * Register settings groups.
	 *
	 * @param array $groups Settings groups.
	 *
	 * @return array
	 */
	public function register_settings_groups( $groups ) {
		$groups['pronamic_pay_status_pages'] = array(
			'id'       => 'pronamic_pay_status_pages',
			'label'    => __( 'Pronamic Pay Status Pages', 'pronamic_ideal' ),
			'priority' => 200,
		);

		return $groups;
	}

	/**
	 * Payment redirect URL filter.
	 *
	 * @param string  $url     Redirect URL.
	 * @param Payment $payment Payment.
	 *
	 * @return string
	 * @since 1.1.1
	 */
	public function redirect_url( $url, $payment ) {
		$form_id   = $payment->get_meta( 'ninjaforms_payment_form_id' );
		$action_id = $payment->get_meta( 'ninjaforms_payment_action_id' );

		if ( empty( $form_id ) || empty( $action_id ) ) {
			return $url;
		}

		$action_settings = Ninja_Forms()->form( $form_id )->get_action( $action_id )->get_settings();

		$status_url = null;

		switch ( $payment->status ) {
			case PaymentStatus::CANCELLED:
				$status_url = NinjaFormsHelper::get_page_link_from_action_settings( $action_settings, 'pronamic_pay_cancel_page_id' );

				break;
			case PaymentStatus::EXPIRED:
				$status_url = NinjaFormsHelper::get_page_link_from_action_settings( $action_settings, 'pronamic_pay_expired_page_id' );

				break;
			case PaymentStatus::FAILURE:
				$status_url = NinjaFormsHelper::get_page_link_from_action_settings( $action_settings, 'pronamic_pay_error_page_id' );

				break;
			case PaymentStatus::SUCCESS:
				$status_url = NinjaFormsHelper::get_page_link_from_action_settings( $action_settings, 'pronamic_pay_completed_page_id' );

				break;
			case PaymentStatus::OPEN:
			default:
				$status_url = NinjaFormsHelper::get_page_link_from_action_settings( $action_settings, 'pronamic_pay_unknown_page_id' );

				break;
		}

		if ( ! empty( $status_url ) ) {
			return $status_url;
		}

		return $url;
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
	public function source_text( $text, Payment $payment ) {
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
				/* translators: %s: source id */
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
