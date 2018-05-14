<?php
namespace Pronamic\WordPress\Pay\Extensions\NinjaForms;

use Pronamic\WordPress\Pay\Payments\PaymentData as Pay_PaymentData;
use Pronamic\WordPress\Pay\Payments\Item;
use Pronamic\WordPress\Pay\Payments\Items;

/**
 * Title: WordPress pay Ninja Forms payment data
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Ruben Droogh
 */
class PaymentData extends Pay_PaymentData {

	/**
	 * Form ID
	 *
	 * @var string
	 */
	private $form_id;

	/**
	 * Action
	 *
	 * @var array
	 */
	private $form_data;

	/**
	 * Constructs and initializes an Formidable Forms payment data object.
	 *
	 * @param string  $entry_id
	 * @param string  $form_id
	 * @param WP_Post $action
	 */
	public function __construct( $form_id, $data ) {
		parent::__construct();

		$this->form_id   = $form_id;
		$this->form_data = $data;
	}

	/**
	 * Get items
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_items()
	 * @return Items
	 */
	public function get_items() {
		// Items
		$items = new Items();

		// Item
		// We only add one total item, because iDEAL cant work with negative price items (discount)
		$item = new Item();
		$item->setNumber( $this->get_order_id() );
		$item->setDescription( $this->get_description() );
		$item->setPrice( $this->get_amount_from_field() );
		$item->setQuantity( 1 );

		$items->addItem( $item );

		return $items;
	}

	/**
	 * Get amount
	 *
	 * @return float
	 */
	private function get_amount_from_field() {
		$amount = 0;

		die(var_dump($this->form_data));

		// $amount_field = $this->action->post_content['pronamic_pay_amount_field'];

		// if ( ! empty( $amount_field ) && isset( $this->entry->metas[ $amount_field ] ) ) {
		// 	$amount = $this->entry->metas[ $amount_field ];
		// }

		return $amount;
	}
}

?>