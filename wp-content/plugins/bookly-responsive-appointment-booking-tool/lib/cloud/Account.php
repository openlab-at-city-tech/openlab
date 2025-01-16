<?php
namespace Bookly\Lib\Cloud;

use Bookly\Backend\Modules;
use Bookly\Lib\Config;
use Bookly\Lib\Utils;
use Bookly\Lib\Slots\DatePoint;

class Account extends Base
{
    const AUTHENTICATE                   = '/1.1/logins';                                 //POST
    const CHANGE_COUNTRY                 = '/1.0/users/%token%/country';                  //PATCH
    const CHANGE_PASSWORD                = '/1.0/users/%token%';                          //PATCH
    const CONFIRM_EMAIL                  = '/1.3/users/%token%/confirm';                  //POST
    const CREATE_PAYPAL_ORDER            = '/1.0/users/%token%/paypal/order';             //POST
    const CREATE_BILLING_AGREEMENT       = '/1.0/users/%token%/paypal/billing-agreement'; //POST
    const RENEW_PAYPAL_AUTO_RECHARGE     = '/1.1/users/%token%/paypal/renew/auto-recharge'; //POST
    const CREATE_STRIPE_CHECKOUT_SESSION = '/1.0/users/%token%/stripe/checkout/sessions'; //POST
    const RENEW_STRIPE_AUTO_RECHARGE     = '/1.0/users/%token%/stripe/renew/auto-recharge'; //POST
    const DISABLE_AUTO_RECHARGE          = '/1.0/users/%token%/auto-recharge';            //DELETE
    const GET_INVOICE                    = '/1.2/users/%token%/invoice';                  //GET
    const GET_BILLING                    = '/1.1/users/%token%/billing';                  //GET
    const GET_PRODUCT_ACTIVATION_TEXTS   = '/1.0/users/%token%/products/%product%/activation-texts'; //GET
    const LOG_OUT                        = '/1.0/users/%token%/logout';                   //GET
    const RECOVER_PASSWORD               = '/1.0/recoveries';                             //POST
    const REGISTER                       = '/1.4/users';                                  //POST
    const RESEND_CONFIRMATION            = '/1.3/users/%token%/resend-confirmation';      //GET
    const SET_INVOICE_DATA               = '/1.1/users/%token%/invoice';                  //POST
    const SEND_WEEKLY_SUMMARY            = '/1.0/users/%token%/weekly-summary/send';      //POST || DELETE
    const PRODUCTS                       = '/1.0/users/%token%/products';                 //GET

    const PRODUCT_SMS_NOTIFICATIONS = 'sms';
    const PRODUCT_STRIPE = 'stripe';
    const PRODUCT_ZAPIER = 'zapier';
    const PRODUCT_CRON = 'cron';
    const PRODUCT_VOICE = 'voice';
    const PRODUCT_SQUARE = 'square';
    const PRODUCT_GIFT = 'gift';
    const PRODUCT_WHATSAPP = 'whatsapp';
    const PRODUCT_MOBILE_STAFF_CABINET = 'mobile-staff-cabinet';

    const PRODUCT_PAYU_LATAM = 'payu-latam';
    const PRODUCT_PAYSON = 'payson';
    const PRODUCT_2CHECKOUT = '2checkout';
    const PRODUCT_AUTHORIZE_NET = 'authorize-net';
    const PRODUCT_CART = 'cart';
    const PRODUCT_COUPONS = 'coupons';
    const PRODUCT_CUSTOM_FIELDS = 'custom-fields';
    const PRODUCT_GROUP_BOOKING = 'group-booking';
    const PRODUCT_MOLLIE = 'mollie';
    const PRODUCT_STRIPE_CLASSIC = 'stripe-classic';
    const PRODUCT_SERVICE_EXTRAS = 'service-extras';

    /** @var string */
    protected $username;
    /** @var float */
    protected $balance;
    /** @var array */
    protected $support;
    /** @var string */
    protected $country;
    /** @var array */
    protected $auto_recharge;
    /** @var array */
    protected $invoice;
    /** @var array */
    protected $recharge = array();
    /** @var bool */
    protected $email_confirmed = false;
    /** @var array */
    protected $products;
    /** @var array */
    protected $subscriptions;
    /** @var bool */
    protected $notify_summary;

    /**
     * @inheritDoc
     */
    public function setup()
    {
        $this->products = get_option( 'bookly_cloud_account_products', '' );
    }

    /**
     * Register new account.
     *
     * @param string $username
     * @param string $password
     * @param string $password_repeat
     * @param string $country
     * @return array|false
     */
    public function register( $username, $password, $password_repeat, $country )
    {
        $data = array( '_username' => $username, '_password' => $password, 'country' => $country );

        if ( $password !== $password_repeat && ! empty ( $password ) ) {
            $this->api->addError( __( 'Passwords must be the same.', 'bookly' ) );

            return false;
        }

        return $this->api->sendPostRequest( self::REGISTER, $data );
    }

    /**
     * Confirm email.
     *
     * @param string $code
     * @return array|false
     */
    public function confirmEmail( $code )
    {
        $response = $this->api->sendPostRequest( self::CONFIRM_EMAIL, compact( 'code' ) );
        if ( $response ) {

            return $response;
        }

        return false;
    }

    /**
     * Resend confirmation email.
     *
     * @return bool
     */
    public function resendConfirmation()
    {
        $response = $this->api->sendGetRequest( self::RESEND_CONFIRMATION );
        if ( $response ) {

            return $response;
        }

        return false;
    }

    /**
     * User forgot password
     *
     * @param null $username
     * @param null $step
     * @param null $code
     * @param null $password
     * @return array|false
     */
    public function forgotPassword( $username = null, $step = null, $code = null, $password = null )
    {
        $data = array( '_username' => $username, 'step' => $step );
        switch ( $step ) {
            case 0:
                break;
            case 1:
                $data['code'] = $code;
                break;
            case 2:
                $data['code'] = $code;
                $data['password'] = $password;
                break;
        }
        $response = $this->api->sendPostRequest( self::RECOVER_PASSWORD, $data );

        if ( $response ) {

            return $response;
        }

        return false;
    }

    /**
     * Log in.
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function login( $username, $password )
    {
        $data = array( '_username' => $username, '_password' => $password );

        $response = $this->api->sendPostRequest( self::AUTHENTICATE, $data );
        if ( $response ) {
            update_option( 'bookly_cloud_token', $response['token'] );
            $this->api->setToken( $response['token'] );

            return true;
        }

        return false;
    }

    /**
     * Log out.
     */
    public function logout()
    {
        update_option( 'bookly_cloud_token', '' );
        update_option( 'bookly_cloud_account_products', '' );

        if ( $this->api->getToken() ) {
            $this->api->sendGetRequest( self::LOG_OUT );
        }
        $this->api->setToken( null );

        $this->api->dispatch( Events::ACCOUNT_LOGGED_OUT );
    }

    /**
     * Load user profile
     *
     * @return bool
     */
    public function loadProfile()
    {
        $this->api->general->loadInfo();

        return $this->username !== null;
    }

    /**
     * Change country.
     *
     * @param string $country
     * @return array|false
     */
    public function changeCountry( $country )
    {
        $data = array( 'country' => $country );

        $response = $this->api->sendPatchRequest( self::CHANGE_COUNTRY, $data );
        if ( $response ) {

            return $response;
        }

        return false;
    }

    /**
     * Change password.
     *
     * @param string $new_password
     * @param string $old_password
     * @return bool
     */
    public function changePassword( $new_password, $old_password )
    {
        $data = array( '_old_password' => $old_password, '_new_password' => $new_password );

        $response = $this->api->sendPatchRequest( self::CHANGE_PASSWORD, $data );
        if ( $response ) {

            return true;
        }

        return false;
    }

    /**
     * Get PayPal billing agreement url, (for enabling auto recharge)
     *
     * @param string $recharge_id
     * @param string $url
     * @return bool|string
     */
    public function getBillingAgreementUrl( $recharge_id, $url )
    {
        if ( $this->api->getToken() ) {
            $response = $this->api->sendPostRequest(
                self::CREATE_BILLING_AGREEMENT,
                array(
                    'recharge' => $recharge_id,
                    'enabled_url' => $url . '#auto-recharge=enabled',
                    'cancelled_url' => $url . '#auto-recharge=cancelled',
                )
            );
            if ( $response ) {
                return $response['redirect_url'];
            }
        }

        return false;
    }

    /**
     * Get PayPal PreApproval url, (for renew auto recharge)
     *
     * @param string $return_url
     * @return bool|string
     */
    public function getPayPalRenewAutoRechargeUrl( $return_url )
    {
        if ( $this->api->getToken() ) {
            $response = $this->api->sendPostRequest(
                self::RENEW_PAYPAL_AUTO_RECHARGE,
                array(
                    'enabled_url' => $return_url . '#auto-recharge=renewed',
                    'cancelled_url' => $return_url . '#auto-recharge=cancelled',
                )
            );
            if ( $response ) {
                return $response['redirect_url'];
            }
        }

        return false;
    }

    /**
     * Create Stripe Checkout session, (for renew auto recharge)
     *
     * @param string $return_url
     * @return bool|string
     */
    public function getStripeRenewAutoRechargeUrl( $return_url )
    {
        if ( $this->api->getToken() ) {
            $response = $this->api->sendPostRequest(
                self::RENEW_STRIPE_AUTO_RECHARGE,
                array(
                    'enabled_url' => $return_url . '#auto-recharge=renewed',
                    'cancelled_url' => $return_url . '#auto-recharge=cancelled',
                )
            );
            if ( $response ) {
                return $response['redirect_url'];
            }
        }
    }

    /**
     * Disable auto-recharge
     *
     * @return bool
     */
    public function disableAutoRecharge()
    {
        if ( $this->api->getToken() ) {
            $response = $this->api->sendDeleteRequest( self::DISABLE_AUTO_RECHARGE, array() );
            if ( $response ) {

                return true;
            }
        }

        return false;
    }

    /**
     * Set invoice data
     *
     * @param array $settings
     * @return bool
     */
    public function setInvoiceData( array $settings )
    {
        if ( $this->api->getToken() ) {
            $response = $this->api->sendPostRequest( self::SET_INVOICE_DATA, array( 'invoice' => $settings ) );
            if ( $response ) {

                return true;
            }
        }

        return false;
    }

    /**
     * Get link for downloading invoice file.
     *
     * @return string
     */
    public function getInvoiceLink()
    {
        return $this->api->buildUrl( self::GET_INVOICE );
    }

    /**
     * Get purchases list.
     *
     * @param null $start_date
     * @param null $end_date
     * @return array
     */
    public function getPurchasesList( $start_date = null, $end_date = null )
    {
        if ( $this->api->getToken() ) {
            $response = $this->api->sendGetRequest(
                self::GET_BILLING,
                compact( 'start_date', 'end_date' )
            );
            if ( $response ) {
                array_walk( $response['list'], function ( &$item ) {
                    $date_time = Utils\DateTime::UTCToWPTimeZone( $item['datetime'] );
                    $item['date'] = Utils\DateTime::formatDate( $date_time );
                    $item['time'] = Utils\DateTime::formatTime( $date_time );
                } );

                return $response;
            }
        }

        return array( 'success' => false, 'list' => array() );
    }

    /**
     * Create Stripe Checkout session
     *
     * @param int $recharge
     * @param string $mode
     * @param string $url
     * @return array|false
     */
    public function createStripeCheckoutSession( $recharge, $mode, $url )
    {
        if ( $this->api->getToken() ) {
            $response = $this->api->sendPostRequest( self::CREATE_STRIPE_CHECKOUT_SESSION, array(
                'mode' => $mode,
                'recharge' => $recharge,
                'success_url' => $url . ( $mode == 'setup' ? '#auto-recharge=enabled' : '#payment=accepted' ),
                'cancel_url' => $url . ( $mode == 'setup' ? '#auto-recharge=cancelled' : '#payment=cancelled' ),
            ) );
            if ( $response ) {

                return $response;
            }
        }

        return false;
    }

    /**
     * Create PayPal order
     *
     * @param int $recharge
     * @param string $url
     * @return array|false
     */
    public function createPayPalOrder( $recharge, $url )
    {
        if ( $this->api->getToken() ) {
            $response = $this->api->sendPostRequest( self::CREATE_PAYPAL_ORDER, array(
                'recharge' => $recharge,
                'success_url' => $url . '#payment=accepted',
                'cancel_url' => $url . '#payment=cancelled',
            ) );
            if ( $response ) {

                return $response['order_url'];
            }
        }

        return false;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->username;
    }

    /**
     * Get email_confirmed.
     *
     * @return string
     */
    public function getEmailConfirmed()
    {
        return $this->email_confirmed;
    }

    /**
     * Get balance.
     *
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Get country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Whether auto-recharge enabled or not.
     *
     * @return bool
     */
    public function autoRechargeEnabled()
    {
        return $this->auto_recharge['enabled'];
    }

    /**
     * Get auto-recharge amount.
     *
     * @return float
     */
    public function getAutoRechargeAmount()
    {
        return $this->auto_recharge['amount'];
    }

    /**
     * Get auto-recharge bonus.
     *
     * @return float
     */
    public function getAutoRechargeBonus()
    {
        return $this->auto_recharge['bonus'];
    }

    /**
     * Get auto-recharge till at.
     *
     * @return string
     */
    public function getAutoRechargeEndAt()
    {
        return $this->autoRechargeEnabled()
            ? $this->auto_recharge['end_at']
            : '';
    }

    /**
     * @return string
     */
    public function getAutoRechargeGateway()
    {
        return $this->autoRechargeEnabled()
            ? $this->auto_recharge['gateway']
            : '';
    }

    /**
     * Get cloud support end date.
     *
     * @return string
     */
    public function getCloudSupportEndAt()
    {
        return $this->support && isset( $this->support['cloud_support_end_at'] )
            ? $this->support['cloud_support_end_at']
            : null;
    }

    /**
     * Get cloud support active days
     *
     * @return int
     */
    public function getCloudSupportDays()
    {
        $support_end_at = $this->getCloudSupportEndAt();

        if ( $support_end_at === null ) {
            return -1;
        }

        $diff = DatePoint::fromStr( 'midnight' )->value()->diff( DatePoint::fromStr( $support_end_at )->value() );

        return $diff->invert ? -1 : $diff->days;
    }

    /**
     * Client data for invoice.
     *
     * @return array
     */
    public function getInvoiceData()
    {
        return (array) $this->invoice;
    }

    /**
     * Get cloud product activation texts
     *
     * @param string $product
     * @return mixed
     */
    public function getProductActivationTexts( $product )
    {
        $response = $this->api->sendGetRequest(
            self::GET_PRODUCT_ACTIVATION_TEXTS,
            array(
                '%product%' => $product,
                'locale' => Config::getShortLocale(),
            )
        );

        if ( $response ) {
            return $response['content'];
        }

        return false;
    }

    /**
     * Get recharge data
     *
     * @return array
     */
    public function getRechargeData()
    {
        return $this->recharge;
    }

    /**
     * Check whether given product is active or not
     *
     * @param string $product
     * @return bool
     */
    public function productActive( $product )
    {
        if ( ! is_array( $this->products ) ) {
            if ( $this->api->getToken() ) {
                $this->api->general->loadInfo();
            } else {
                $this->products = array();
            }
        }

        return in_array( $product, $this->products );
    }

    /**
     * Gets subscriptions
     *
     * @return array
     */
    public function getSubscriptions()
    {
        if ( ! is_array( $this->subscriptions ) ) {
            if ( $this->api->getToken() ) {
                $this->api->general->loadInfo();
            } else {
                $this->subscriptions = array();
            }
        }

        return $this->subscriptions;
    }

    /**
     * @return array
     */
    public function getEndPoints()
    {
        $response = $this->api->sendGetRequest( self::PRODUCTS );
        if ( $response ) {
            return $response['endpoints'];
        }

        return array();
    }

    /**
     * Gets notify_summary
     *
     * @return bool
     */
    public function getNotifySummary()
    {
        return $this->notify_summary;
    }

    /**
     * @inheritDoc
     */
    public function translateError( $error_code )
    {
        switch ( $error_code ) {
            case 'ERROR_EMPTY_PASSWORD':
                return __( 'Empty password.', 'bookly' );
            case 'ERROR_INCORRECT_PASSWORD':
                return __( 'Incorrect password.', 'bookly' );
            case 'ERROR_INCORRECT_RECOVERY_CODE':
                return __( 'Incorrect recovery code.', 'bookly' );
            case 'ERROR_INCORRECT_USERNAME_OR_PASSWORD':
                return __( 'Incorrect email or password.', 'bookly' );
            case 'ERROR_INVALID_USERNAME':
                return __( 'Invalid email.', 'bookly' );
            case 'ERROR_LOW_BALANCE':
                return __( 'Recharge your account with one of the standard amounts', 'bookly' );
            case 'ERROR_PENDING_SENDER_ID_ALREADY_EXISTS':
                return __( 'Pending sender ID already exists.', 'bookly' );
            case 'ERROR_PRODUCT_NOT_FOUND':
                return __( 'Product not found.', 'bookly' );
            case 'ERROR_RECHARGE_NOT_AVAILABLE':
                return __( 'Recharge not available.', 'bookly' );
            case 'ERROR_RECOVERY_CODE_EXPIRED':
                return __( 'Recovery code expired.', 'bookly' );
            case 'ERROR_SENDING_EMAIL':
                return __( 'Error sending email.', 'bookly' );
            case 'ERROR_SUBSCRIPTION_NOT_AVAILABLE':
                return __( 'Subscription not available.', 'bookly' );
            case 'ERROR_USER_NOT_FOUND':
                return __( 'User not found.', 'bookly' );
            case 'ERROR_USERNAME_ALREADY_EXISTS':
                return __( 'Email already in use.', 'bookly' );
            default:
                return null;
        }
    }

    /**
     * @inheritDoc
     */
    protected function setupListeners()
    {
        $account = $this;

        $this->api->listen( Events::GENERAL_INFO_LOADED, function ( $response ) use ( $account ) {
            if ( isset ( $response['account'] ) ) {
                $account->username = $response['account']['username'];
                $account->balance = $response['account']['balance'];
                $account->support = $response['account']['support'];
                $account->country = $response['account']['country'];
                $account->auto_recharge = $response['account']['auto_recharge'];
                $account->invoice = $response['account']['invoice'];
                $account->recharge = $response['account']['recharge'];
                $account->email_confirmed = $response['account']['email_confirmed'];
                $account->products = $response['account']['products'];
                $account->subscriptions = $response['account']['subscriptions'];
                $account->notify_summary = $response['account']['notify_summary'];

                update_option( 'bookly_cloud_account_products', $account->products );

                if ( $account->autoRechargeEnabled() ) {
                    $end_at = get_option( 'bookly_cloud_auto_recharge_end_at' );
                    if ( $end_at != $account->getAutoRechargeEndAt() ) {
                        $end_at_ts = date_create( $account->getAutoRechargeEndAt() )->getTimestamp();
                        update_option( 'bookly_cloud_auto_recharge_end_at_ts', $end_at_ts );
                        update_option( 'bookly_cloud_renew_auto_recharge_notice_hide_until', $end_at_ts - 2 * WEEK_IN_SECONDS );

                        Utils\Common::updateBlogUsersMeta( 'bookly_notice_renew_auto_recharge_hide_until', '0' );
                    }
                }
                update_option( 'bookly_cloud_auto_recharge_gateway', $account->getAutoRechargeGateway() );
                update_option( 'bookly_cloud_auto_recharge_end_at', $account->getAutoRechargeEndAt() );

                $account->api->dispatch( Events::ACCOUNT_PROFILE_LOADED, $response );
            } else {
                $account->products = array();
                $account->subscriptions = array();

                $account->api->dispatch( Events::ACCOUNT_PROFILE_NOT_LOADED );
            }
        } );

        $this->api->listen( Events::GENERAL_INFO_NOT_LOADED, function () use ( $account ) {
            $account->products = array();
            $account->subscriptions = array();

            $account->api->dispatch( Events::ACCOUNT_PROFILE_NOT_LOADED );
        } );

        $this->api->listen( Events::ACCOUNT_LOW_BALANCE, function () use ( $account ) {
            if ( get_option( 'bookly_cloud_notify_low_balance' ) ) {
                $account->sendLowBalanceNotification();
            }
        } );
    }

    /**
     * Send weekly summary
     *
     * @return bool
     */
    public function enableSendingWeeklySummary()
    {
        return $this->api->sendPostRequest( self::SEND_WEEKLY_SUMMARY );
    }

    /**
     * Unsubscribe summary
     *
     * @return bool
     */
    public function disableSendingWeeklySummary()
    {
        return $this->api->sendDeleteRequest( self::SEND_WEEKLY_SUMMARY );
    }

    /**
     * Send notification to administrators about low balance
     */
    protected function sendLowBalanceNotification()
    {
        $add_money_url = admin_url( 'admin.php?' . build_query( array( 'page' => Modules\CloudSms\Page::pageSlug() ) ) ) . '#recharge';
        $message = sprintf( __( "Dear Bookly Cloud customer.\nWe would like to notify you that your Bookly Cloud balance fell below 5 USD. To use our service without interruptions please recharge your balance by visiting Bookly Cloud page <a href='%s'>here</a>.\n\nIf you want to stop receiving these notifications, please update your settings <a href='%s'>here</a>.", 'bookly' ), $add_money_url, $add_money_url );
        if ( get_option( 'bookly_email_send_as' ) == 'html' ) {
            $message = wpautop( $message );
        }
        $subject = __( 'Bookly Cloud - Low Balance', 'bookly' );

        foreach ( Utils\Common::getAdminEmails() as $email ) {
            Utils\Mail::send( $email, $subject, $message );
        }
    }

    /**
     * @return string
     */
    public function getAutoRechargeTitle()
    {
        $titles = array(
            'stripe' => __( 'Card', 'bookly' ),
            'paypal' => 'PayPal',
        );

        return array_key_exists( $this->getAutoRechargeGateway(), $titles ) ? $titles[ $this->getAutoRechargeGateway() ] : '';
    }
}
