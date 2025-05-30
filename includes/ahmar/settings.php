<?php

/**
 * @package Escrow config and settings
 */

// add filter to add escrow settings page
add_filter('ae_admin_menu_pages', 'fre_escrow_settings');
function fre_escrow_settings( $pages ) {

    $options = AE_Options::get_instance();
    $sections = array();

    $sections[] = array(
        'args' => array(
            'title' => __("Settings", ET_DOMAIN) ,
            'id' => 'escrow-settings',
            'icon' => 'y',
            'class' => ''
        ) ,
        'groups' => array(
            array(
                'args' => array(
                    'title' => __("Using Escrow", ET_DOMAIN) ,
                    'id' => 'use-escrow',
                    'class' => '',
                    'desc' => __("Enabling this will activate the Escrow system. Dispute feature is only activated when you use the Escrow system.", ET_DOMAIN)
                ) ,
                'fields' => array(
                    array(
                        'id' => 'use_escrow',
                        'type' => 'switch',
                        'title' => __("use escrow", ET_DOMAIN) ,
                        'name' => 'use_escrow',
                        'class' => ''
                    )
                )
            ),
            array(
                'args' => array(
                    'title' => __("Commission", ET_DOMAIN) ,
                    'id' => 'commission-amount',
                    'class' => '',
                    'desc' => __("Decide the amount of commission to be paid for using the service.", ET_DOMAIN)
                ) ,
                'fields' => array(
                    array(
                        'id' => 'commission',
                        'type' => 'text',
                        'title' => __("commission", ET_DOMAIN) ,
                        'name' => 'commission',
                        'class' => 'positive_int'
                    )
                )
            ),
            array(
                'args' => array(
                    'title' => __("Commission type", ET_DOMAIN) ,
                    'id' => 'commission-type',
                    'class' => '',
                    'desc' => __("Select the type of commission you want to use.", ET_DOMAIN)
                ) ,
                'fields' => array(
                    array(
                        'id' => 'commission_type',
                        'type' => 'select',
                        'title' => __("commission", ET_DOMAIN) ,
                        'name' => 'commission_type',
                        'class' => '',
                        'data' => array(
                                'percent' => __("By percentage", ET_DOMAIN) ,
                                'currency' => __("By specific amount", ET_DOMAIN)
                        )
                    )
                )
            ),
            array(
                'args' => array(
                    'title' => __("Payer of commission", ET_DOMAIN) ,
                    'id' => 'commision-fees',
                    'class' => '',
                    'desc' => __("Select the user role to pay for the commission.", ET_DOMAIN)
                ) ,
                'fields' => array(
                    array(
                        'id' => 'payer_of_commission',
                        'type' => 'select',
                        'title' => __("Payer of fees", ET_DOMAIN) ,
                        'name' => 'payer_of_commission',
                        'class' => '',
                        'data' => array(
                                'project_owner' => __("Project Owner", ET_DOMAIN) ,
                                'worker' => __("Freelancer", ET_DOMAIN),
                                // Commission - By: eK
                                'both' => __("Both of Them", ET_DOMAIN),
                        )
                    )
                )
            )
        )

    );
    $api_link = " <a class='find-out-more' target='_blank' rel='noopener noreferrer' href='http://docs.enginethemes.com/article/293-set-up-the-paypal-adaptive-in-escrow-system' >"
                    .__("Read more here", ET_DOMAIN).
                " <span class='icon' data-icon='i' ></span></a>";
    $sections[] = array(
        'args' => array(
            'title' => __("Gateways", ET_DOMAIN) ,
            'id' => 'escrow-gateways',
            'icon' => '$',
            'class' => 'payment-gateways'
        ) ,
        'groups' => apply_filters('fre_escrow_payment_gateway_settings', array(

                /* payment test mode settings */
                array(
                    'args' => array(
                        'title' => __("Payment Test Mode", ET_DOMAIN) ,
                        'id' => 'payment-test-mode',
                        'class' => 'payment-test-mode',
                        'desc' => __("Enabling this will allow you to test payment without charging your account.", ET_DOMAIN) ,
                        // 'name' => 'currency'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'test-mode',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'test_mode',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Stripe Settings", ET_DOMAIN),
                        'id' => 'use-escrow-stripe',
                        'class' => '',
                        'name' => 'escrow_stripe',
                        'desc' => __("You need to configure your Stripe API keys to use this feature.", ET_DOMAIN)
                    ),
                    'fields' => array(
                        array(
                            'id' => 'stripe_publishable_key',
                            'type' => 'text',
                            'label' => __("Stripe Publishable Key", ET_DOMAIN),
                            'name' => 'stripe_publishable_key',
                            'class' => ''
                        ),
                        array(
                            'id' => 'stripe_secret_key',
                            'type' => 'text',
                            'label' => __("Stripe Secret Key", ET_DOMAIN),
                            'name' => 'stripe_secret_key',
                            'class' => ''
                        ),
                        array(
                            'id' => 'stripe_webhook_secret',
                            'type' => 'text',
                            'label' => __("Stripe Webhook Secret", ET_DOMAIN),
                            'name' => 'stripe_webhook_secret',
                            'class' => ''
                        )
                    )
                ),


                //manual_transfer
                array(
                    'args' => array(
                        'title' => __("Manual Transfer", ET_DOMAIN) ,
                        'id' => 'manual_transfer-escrow',
                        'class' => '',
                        'desc' => __("Enabling this will allow you to manually transfer the money when the project's completed.", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'manual_transfer',
                            'type' => 'switch',
                            'title' => __("Manual Transfer", ET_DOMAIN) ,
                            'name' => 'manual_transfer',
                            'class' => ''
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Paypal Settings", ET_DOMAIN) ,
                        'id' => 'use-escrow-paypal',
                        'class' => '',
                        'name' => 'escrow_paypal',
                        'desc' => __("You need to apply for PayPal Adaptive API and configure correctly to use this feature.", ET_DOMAIN) . $api_link
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'use_escrow_paypal',
                            'type' => 'text',
                            'label' => __("Your paypal business email", ET_DOMAIN) ,
                            'name' => 'business_mail',
                            'class' => 'require multiemails'
                        ),
                        array(
                            'id' => 'paypal_fee',
                            'type' => 'select',
                            'title' => __("Paypal fees", ET_DOMAIN) ,
                            'label' => __("Paypal fees", ET_DOMAIN) ,
                            'name' => 'paypal_fee',
                            'class' => 'required',
                            'data' => array(
                                // 'SENDER' => __("Sender pays all fees", ET_DOMAIN) ,
                                'PRIMARYRECEIVER' => __("Admin will pay all fees", ET_DOMAIN),
                                'EACHRECEIVER' => __("Both admin & freelancer pay the fee", ET_DOMAIN), 
                                'SECONDARYONLY' => __("Freelancers will pay all fees", ET_DOMAIN)
                            )
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Paypal API", ET_DOMAIN) ,
                        'id' => 'use-escrow-paypal',
                        'class' => '',
                        'name' => 'escrow_paypal_api',
                        // 'desc' => __("Your Paypal Adaptive API", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'username',
                            'type' => 'text',
                            //'title' => __("Your paypal API username", ET_DOMAIN) ,
                            'name' => 'username',
                            'label' => __("Your paypal API username", ET_DOMAIN) ,
                            'class' => ''
                        ),
                        array(
                            'id' => 'password',
                            'type' => 'text',
                            //'placeholder' => __("Your paypal API password", ET_DOMAIN) ,
                            'label' => __("Your paypal API password", ET_DOMAIN) ,
                            'name' => 'password',
                            'class' => ''
                        ),
                        array(
                            'id' => 'signature',
                            'type' => 'text',
                            'label' => __("Your paypal API signature", ET_DOMAIN) ,
                            'name' => 'signature',
                            'class' => ''
                        ),
                        array(
                            'id' => 'appID',
                            'type' => 'text',
                            'label' => __("Your Paypal Adaptive AppID", ET_DOMAIN) ,
                            'name' => 'appID',
                            'class' => ''
                        )
                    )
                )
            )
        )

    );


    $temp = array();
    foreach ($sections as $key => $section) {
        $temp[] = new AE_section($section['args'], $section['groups'], $options);
    }

    $orderlist = new AE_container(array(
        'class' => 'escrow-settings',
        'id' => 'settings',
    ) , $temp, $options);
    $pages[] = array(
        'args' => array(
            'parent_slug' => 'et-overview',
            'page_title' => __('Escrow', ET_DOMAIN) ,
            'menu_title' => __('ESCROW CONFIGURATION', ET_DOMAIN) ,
            'cap' => 'administrator',
            'slug' => 'fre-escrow',
            'icon' => '%',
            'desc' => __("Setting up a trustworthy environment for freelancers and employers.", ET_DOMAIN)
        ) ,
        'container' => $orderlist
    );

    return $pages;
}


function action_before_save_option($name){
    if($name == 'escrow_paypal_api'){
        $value = $_REQUEST['value'];
        $exploded = array();
        parse_str($value, $exploded);
        if(isset($exploded['username']) && !empty($exploded['username'])){
            $ppadaptive = AE_PPAdaptive::get_instance();
            $order_data = array(
                    'accountIdentifier.emailAddress' =>  $exploded['username'],
                    'matchCriteria' => 'NONE',
                    'requestEnvelope' => 'en_US'
                );
            $response = $ppadaptive->getVerifiedAccount($order_data);

            if($response->responseEnvelope->ack == 'Failure'){
                $error = $response->error;
                $mess = $error[0]->message;
                $response = array(
                    'success' => false,
                    'msg' => $mess
                );
                wp_send_json($response);
                return;
            }
        }
    }
}
// disable veryfied paypal business account.
// add_action('ae_before_save_option', 'action_before_save_option', 10, 1);