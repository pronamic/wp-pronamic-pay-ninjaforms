<?php if ( ! defined( 'ABSPATH' ) ) exit;

return array(

    'description' => array(
        'name' => 'description',
        'type' => 'textbox',
        'group' => 'primary',
        'label' => __( 'Transaction Description', 'ninja-forms' ),
        'placeholder' => '',
        'value' => '',
        'width' => 'full',
        'use_merge_tags' => array(
            'include' => array(
                'calcs',
            ),
        ),
    ),

    'amount' => array(
        'name' => 'amount',
        'type' => 'textbox',
        'group' => 'primary',
        'label' => __( 'Payment Amount', 'ninja-forms' ),
        'placeholder' => '',
        'value' => '',
        'width' => 'one-half',
        'help'  => __( 'Select the correct field using the icon on the right, or enter a fixed amount.', 'ninja-forms' ),
        'use_merge_tags' => array(
            'include' => array(
                'calcs',
            ),
        ),
    ),

    'method' => array(
        'name' => 'method',
        'type' => 'field-select',
        'group' => 'primary',
        'label' => __( 'Payment method', 'ninja-forms' ),
        'placeholder' => '',
        'value' => '',
        'width' => 'one-half',
        'help'  => __( 'Use the special "Payment Methods" field for this.', 'ninja-forms' ),
        'field_types' => array(
            'paymentmethods'
        ),
        'use_merge_tags' => array(
            'include' => array(
                'calcs',
            ),
        ),
    ),
);