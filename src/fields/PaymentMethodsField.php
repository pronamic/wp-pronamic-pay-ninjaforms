<?php

namespace Pronamic\WordPress\Pay\Extensions\NinjaForms;

use NF_Abstracts_List;
use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Class Pay_PaymentMethodsField
 */
class Pay_PaymentMethodsField extends NF_Abstracts_List {
    // @codingStandardsIgnoreStart Generic.WhiteSpace.DisallowSpaceIndent.SpacesUsed
    protected $_name = 'paymentmethods';

    protected $_type = 'paymentmethods';

    protected $_nicename = 'Payment Methods';

    protected $_section = 'pricing';

    protected $_icon = 'paypal';

    protected $_templates = 'listselect';

    protected $_old_classname = 'list-select';

    public function __construct() {
        parent::__construct();

        $this->_nicename = __( 'Payment Methods', 'ninja-forms' );

        $this->_settings['options']['value'] = $this->get_options();
    }

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
    //@codingStandardsIgnoreEnd
}
