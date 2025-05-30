<?php

/**
 * Paypal Adaptive class
 */
class AE_PPAdaptive
{
    static $instance;
    public $appID;
    public $api_username;
    public $api_password;
    public $api_signature;

    /**
     * return class $instance
     */
    public static function get_instance() {
        if (self::$instance == null) {

            self::$instance = new AE_PPAdaptive();
        }
        return self::$instance;
    }

    /**
     * description
     * @param $appID
     * @param $api_username
     * @param $api_password
     * @param $api_signature
     * @param $enpoint_url
     * @since 1.2
     * @author Dakachi
     */
    function __construct($data = array()) {
        // if (isset($DataArray['Sandbox'])) $this->Sandbox = $DataArray['Sandbox'];
        // elseif (isset($DataArray['BetaSandbox'])) $this->Sandbox = $DataArray['BetaSandbox'];
        // else $this->Sandbox = true;

        $api = ae_get_option('escrow_paypal_api');

        $this->api_username = isset($api['username']) ? $api['username'] : 'dinhle1987-biz_api1.yahoo.com';
        $this->api_password = isset($api['password']) ? $api['password'] : '1362804968';
        $this->api_signature = isset($api['signature']) ? $api['signature'] : 'A6LFoneN6dpKOQkj2auJBwoVZBiLAE-QivfFWXkjxrvJZ6McADtMu8Pe';
        $this->appID = isset($api['appID']) ? $api['appID'] : 'APP-80W284485P519543T';

        $testmode = ae_get_option('test_mode');
        // test mod is on
        $this->endpoint = 'https://svcs.sandbox.paypal.com/AdaptivePayments/';
        $this->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_ap-payment&paykey=';
        // live mod is on
        if (!$testmode) {
            $this->endpoint = 'https://svcs.paypal.com/AdaptivePayments/';
            $this->paypal_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_ap-payment&paykey=';
        }
    }

    public function get_pending_message($pendingReason) {
        $reason = array(
            'ECHECK' => __('The payment is pending because it was made by an eCheck that has not yet cleared.', ET_DOMAIN) ,
            'MULTI_CURRENCY' => __('The receiver does not have a balance in the currency sent, and does not have the Payment Receiving Preferences set to automatically convert and accept this payment. Receiver must manually accept or deny this payment from the Account Overview.', ET_DOMAIN) ,
            'UPGRADE' => __('The payment is pending because it was made via credit card and the receiver must upgrade the account to a Business account or Premier status to receive the funds. It can also mean that receiver has reached the monthly limit for transactions on the account', ET_DOMAIN) ,
            'VERIFY' => __('The payment is pending because the receiver is not yet verified.', ET_DOMAIN) ,
            'RISK' => __('The payment is pending while it is being reviewed by PayPal for risk.', ET_DOMAIN) ,
            'OTHER' => __(' The payment is pending for review. For more information, contact PayPal Customer Service.', ET_DOMAIN) ,
        );
        if (isset($reason[$pendingReason])) {
            return $reason[$pendingReason];
        }
        return $reason['OTHER'];
    }
    /**
     * The GetVerifiedStatus API operation lets you determine whether the specified PayPal account's status is verified or unverified.
     */
    function getVerifiedAccount($info) {

        $testmode = ae_get_option('test_mode');
        // test mod is on
        $endpoint = 'https://svcs.sandbox.paypal.com/AdaptiveAccounts/';
        // live mod is on
        if (!$testmode) {
            $endpoint = 'https://svcs.paypal.com/AdaptiveAccounts/';
        }

        $endpoint = $endpoint . 'GetVerifiedStatus';
        $headers = $this->BuildHeaders();
        $a = wp_remote_post($endpoint, array(
            'headers' => $headers,
            'body' => $info,
            'httpversion' => '1.1'
        ));

        if(!is_wp_error( $a )) {
            return json_decode($a['body']);
        }
        return array('success' => false, 'msg' => $a->get_error_message());
    }

    function BuildHeaders() {
        $headers = array(
            'X-PAYPAL-APPLICATION-ID' => $this->appID,
            'X-PAYPAL-SECURITY-USERID' => $this->api_username,
            'X-PAYPAL-SECURITY-PASSWORD' => $this->api_password,
            'X-PAYPAL-SECURITY-SIGNATURE' => $this->api_signature,
            // 'X-PAYPAL-SECURITY-SUBJECT: ' . $this->APISubject,
            // 'X-PAYPAL-SECURITY-VERSION: ' . $this->APIVersion,
            'X-PAYPAL-REQUEST-DATA-FORMAT' => 'NV',
            'X-PAYPAL-RESPONSE-DATA-FORMAT' => 'JSON'
            // 'X-PAYPAL-DEVICE-ID: ' . $this->DeviceID,
            // 'X-PAYPAL-DEVICE-IPADDRESS: ' . $this->IPAddress
        );

        return $headers;
    }

    /**
     * The ExecutePayment API operation lets you execute a payment set up with
     * the Pay API operation with the actionType CREATE.
     * To pay receivers identified in the Pay call,
     * pass the pay key you received from the PayResponse message in the ExecutePaymentRequest message.
     *
     * https://developer.paypal.com/docs/classic/api/adaptive-payments/ExecutePayment_API_Operation/
     *
     * @param $envelope (Required) Information common to each API operation, such as the language
     *        in which an error message is returned.
     * @param $payKey (Optional) The pay key that identifies the payment to be executed.
     *          This is the pay key returned in the PayResponse message.
     * @param $actionType (Optional) Describes the action that is performed.
     * @since snippet.
     * @author Dakachi
     */
    function executePayment($paykey) {
        $endpoint = $this->endpoint . 'ExecutePayment';
        $headers = $this->BuildHeaders();
        $a = wp_remote_post($endpoint, array(
            'headers' => $headers,
            'timeout' => 400,
            'body' => array(
                'payKey' => $paykey,
                'requestEnvelope.errorLanguage' => 'vi_VN'
            ),
            'httpversion' => '1.1'
        ));
        if(!is_wp_error( $a )) {
            return json_decode($a['body']);
        }
        return array('success' => false, 'msg' => $a->get_error_message());

    }

    /**
     * Use the CancelPreapproval API operation to handle the canceling of preapprovals. Preapprovals can be canceled regardless of the state they are in, such as active, expired, deactivated, and previously canceled.
     * @param snippet
     * @since 1.3
     * @author Dakachi
     */
    function cancelApproval() {
    }

    /**
     * Use the ConvertCurrency API operation to request the current foreign exchange (FX) rate for a specific amount and currency.
     * @param $baseAmountList
     * @since 1.3
     * @author Dakachi
     */
    function ConvertCurrency() {
    }

    /**
     * Use the GetFundingPlans API operation to determine the funding sources that are available for a specified payment,
     * identified by its key, which takes into account the preferences and country of the receiver as well as the payment amount.
     * You must be both the sender of the payment and the caller of this API operation
     * @param $payKey
     * @since 1.3
     * @author Dakachi
     */
    function GetFundingPlans($payKey) {
    }

    /**
     * Use the GetPaymentOptions API to retrieve the options previously specified in the SetPaymentOptions API.
     * @param $payKey
     * @since 1.3
     * @author Dakachi
     */
    function GetPaymentOptions($payKey) {
    }

    /**
     * Use the GetPrePaymentDisclosure API to get the pre-Payment disclosure and response.
     * This API is specific for merchants who must support the Consumer Financial Protection Bureau's Remittance Transfer Rule.
     * @param $payKey
     * @param $receiverInfoList
     * @since 1.3
     * @author Dakachi
     */
    function GetPrePaymentDisclosure() {
    }

    function GetShippingAddresses() {
    }

    /**
     * Use the Pay API operation to transfer funds from a sender's PayPal account to one or more receivers' PayPal accounts.
     * You can use the Pay API for simple payments, chained payments, and parallel payments.
     * Payments can be explicitly approved, preapproved, or implicitly approved.
     * @param $action
     * @param receiverList.receiver(0).email
     * @param receiverList.receiver(0).amount
     * @param currencyCode
     * @param cancelUrl
     * @param returnUrl
     * @since 1.3
     * @author Dakachi
     */
    function Pay($order) {
        $endpoint = $this->endpoint . 'Pay';
        $headers = $this->BuildHeaders();
        $a = wp_remote_post($endpoint, array(
            'headers' => $headers,
            'body' => $order,
            'httpversion' => '1.1'
        ));

        if(!is_wp_error( $a )) {
            return json_decode($a['body']);
        }
        return array('success' => false, 'msg' => $a->get_error_message());
    }

    /**
     * Use the PaymentDetails API operation to obtain information about a payment.
     * You can identify the payment by the tracking ID, the PayPal transaction ID in an IPN message,
     * or the pay key associated with the payment.
     * @param $payKey
     * @param $requestEnvelope
     * @param $transactionId
     * @param $trackingId
     * @since 1.3
     * @author Dakachi
     */
    function PaymentDetails($paykey) {
        $endpoint = $this->endpoint . 'PaymentDetails';
        $headers = $this->BuildHeaders();
        $a = wp_remote_post($endpoint, array(
            'headers' => $headers,
            'body' => array(
                'payKey' => $paykey,
                'requestEnvelope.errorLanguage' => 'vi_VN',
            ),
            'httpversion' => '1.1'
        ));

        if(!is_wp_error( $a )) {
            return json_decode($a['body']);
        }
        return array('success' => false, 'msg' => $a->get_error_message());
    }

    /**
     * Use the Preapproval API operation to set up an agreement between yourself
     * and a sender for making payments on the sender's behalf.
     * You set up a preapprovals for a specific maximum amount over a specific period of time and,
     * optionally, by any of the following constraints:
     *  - the number of payments,
     *  - a maximum per-payment amount,
     *  - a specific day of the week or the month,
     *  - and whether or not a PIN is required for each payment request.
     * @param $endingDate Last date for which the preapproval is valid. It cannot be later than one year from the starting date. Contact PayPal if you do not want to specify an ending date.
     * @param $startingDate First date for which the preapproval is valid. It cannot be before today's date or after the ending date.
     * @since 1.2
     * @author Dakachi
     */
    function preApproval() {
        $endpoint = $this->endpoint . 'Preapproval';
        $headers = $this->BuildHeaders();

        $post_fields = array(
            'returnUrl' => 'http://localhost/wp/fre/process-payment',
            'cancelUrl' => 'http://localhost/wp/fre/process-payment',
            'startingDate' => '2014-11-14T10:45:52Z',
            'endingDate' => '2014-11-14T10:45:52Z',
            'maxAmountPerPayment' => '35.00',
            'currencyCode' => 'USD',
            'feesPayer' => 'SENDER',
            'receiverList' => array(
                array(
                    'amount' => '5',
                    'email' => 'dinhle1987-biz_api1.yahoo.com',
                    'primary' => 'false'
                ) ,
                array(
                    'amount' => '25',
                    'email' => 'dinhle1987-pers2@yahoo.com',
                    'primary' => 'false'
                )
            ) ,
            'requestEnvelope' => array(
                'errorLanguage' => 'en_US'
            ) ,
            'memo' => 'pay for a freelancer',
        );

        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, $endpoint);
        curl_setopt($s, CURLOPT_HEADER, $headers);
        curl_setopt($s, CURLOPT_POST, true);
        curl_setopt($s, CURLOPT_POSTFIELDS, $this->_postFields);
    }

    function PreapprovalDetails() {
    }

    function SetPaymentOptions() {
    }

    /**
     * Use the Refund API operation to refund all or part of a payment.
     * @param $payKey
     * @since 1.3
     * https://developer.paypal.com/docs/classic/api/adaptive-payments/Refund_API_Operation/
     * @author Dakachi
     */
    function Refund($order) {
        $endpoint = $this->endpoint . 'Refund';
        $headers = $this->BuildHeaders();

        // partial refund 
        $a = wp_remote_post($endpoint, array(
            'headers' => $headers,
            'body' => $order,
            'httpversion' => '1.1'
        ));
        if(!is_wp_error( $a )) {
            return json_decode($a['body']);
        }
        return array('success' => false, 'msg' => $a->get_error_message());
    }
}

class AE_Stripe {
    private static $instance;
    private $secret_key;

    public static function get_instance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        $options = AE_Options::get_instance();
        $this->secret_key = $options->stripe_secret_key;
        \Stripe\Stripe::setApiKey($this->secret_key);
    }

    public function createPaymentIntent($amount, $currency = 'usd') {
        return \Stripe\PaymentIntent::create([
            'amount' => $amount,
            'currency' => $currency,
        ]);
    }

    public function handleWebhook($payload, $signature, $webhook_secret) {
        try {
            return \Stripe\Webhook::constructEvent(
                $payload, 
                $signature, 
                $webhook_secret
            );
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            throw new Exception("Invalid Stripe Webhook Signature");
        }
    }
}
