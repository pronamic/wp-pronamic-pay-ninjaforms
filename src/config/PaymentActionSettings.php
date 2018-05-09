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
        'label' => __( 'Amount Field', 'ninja-forms' ),
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
        'type' => 'select',
        'group' => 'primary',
        'label' => __( 'Payment method', 'ninja-forms' ),
        'options' => array(
            array( 'label' => __( 'Method 1', 'ninja-forms' ), 'value' => '5' ),
            array( 'label' => __( 'Method 2', 'ninja-forms' ), 'value' => '5' )
        ),
        'placeholder' => '',
        'value' => '',
        'width' => 'one-half',
        'use_merge_tags' => array(
            'include' => array(
                'calcs',
            ),
        ),
    ),
);