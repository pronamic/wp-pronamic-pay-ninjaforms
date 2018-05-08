<?php

namespace Pronamic\WordPress\Pay\Extensions\NinjaForms;

use NF_Abstracts_Action;

class PaymentAction extends NF_Abstracts_Action {
	/**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'Pronamic Pay', 'ninja-forms' );

        //$settings = Ninja_Forms::config( 'ActionCustomSettings' );

        //$this->_settings = array_merge( $this->_settings, $settings );
    }

    public function process( $action_id, $form_id, $data ) {
    	// todo
    }

    public function test() {
    	die(var_dump('test'));
    }
}

?>