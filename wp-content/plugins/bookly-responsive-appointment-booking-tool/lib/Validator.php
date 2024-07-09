<?php
namespace Bookly\Lib;

use Bookly\Lib\Notifications\Verification\Sender;
use Bookly\Frontend\Modules\Booking\Proxy as BookingProxy;

class Validator
{
    private $errors = array();

    /**
     * Validate email.
     *
     * @param string $field
     * @param array $data
     */
    public function validateEmail( $field, $data )
    {
        if ( $data['email'] == '' && ( Config::emailRequired() || get_option( 'bookly_cst_create_account', 0 ) ) ) {
            $this->errors[ $field ] = Utils\Common::getTranslatedOption( 'bookly_l10n_required_email' );
        } else {
            if ( $data['email'] != '' && ! is_email( trim( $data['email'] ) ) ) {
                $this->errors[ $field ] = __( 'Invalid email', 'bookly' );
            }
            // Check email for uniqueness when a new WP account is going to be created.
            if ( get_option( 'bookly_cst_create_account', 0 ) && ! get_current_user_id() ) {
                $customer = new Entities\Customer();
                // Try to find customer by phone or email.
                $customer->loadBy(
                    Config::phoneRequired()
                        ? array( 'phone' => $data['phone'] )
                        : array( 'email' => $data['email'] )
                );
                if ( ( ! $customer->isLoaded() || ! $customer->getWpUserId() ) && email_exists( $data['email'] ) ) {
                    $this->errors[ $field ] = Utils\Common::getTranslatedOption( 'bookly_l10n_email_in_use' );
                }
            }
        }
    }

    /**
     * Validate email confirm.
     *
     * @param string $field
     * @param array $data
     */
    public function validateEmailConfirm( $field, $data )
    {
        if ( Config::showEmailConfirm() && $data['email'] != $data['email_confirm'] ) {
            $this->errors[ $field ] = Utils\Common::getTranslatedOption( 'bookly_l10n_email_confirm_not_match' );
        }
    }

    public function validateBirthday( $field_name, array $data )
    {
        $required = get_option( 'bookly_cst_required_birthday' );

        // Day
        $day = (int) $data['day'];
        $month = (int) $data['month'];
        $year = (int) $data['year'];

        $last_day = (int) date( 't', strtotime( $year . '-' . $month . '-01' ) );

        if ( $day < 1 ) {
            if ( $required ) {
                $this->errors[ $field_name . '_day' ] = Utils\Common::getTranslatedOption( 'bookly_l10n_required_day' );
            }
        } elseif ( $day > $last_day ) {
            $this->errors[ $field_name . '_day' ] = Utils\Common::getTranslatedOption( 'bookly_l10n_invalid_day' );
        }

        // Month
        if ( $required && ( $month < 1 || $month > 12 ) ) {
            $this->errors[ $field_name . '_month' ] = Utils\Common::getTranslatedOption( 'bookly_l10n_required_month' );
        }

        // Year
        $max_year = (int) Slots\DatePoint::now()->format( 'Y' );
        $min_year = $max_year - 100;

        if ( $required && ( $year < $min_year || $year > $max_year ) ) {
            $this->errors[ $field_name . '_year' ] = Utils\Common::getTranslatedOption( 'bookly_l10n_required_year' );
        }
    }

    /**
     * @param string $field_name
     * @param string $value
     * @param bool $required
     */
    public function validateAddress( $field_name, $value, $required = false )
    {
        $value = $value === null ? '' : trim( $value );
        if ( empty( $value ) && $required ) {
            $this->errors[ $field_name ] = Utils\Common::getTranslatedOption( 'bookly_l10n_required_' . $field_name );
        }
    }

    /**
     * Validate phone.
     *
     * @param string $field
     * @param string $phone
     * @param bool $required
     */
    public function validatePhone( $field, $phone, $required = false )
    {
        if ( $phone == '' && $required ) {
            $this->errors[ $field ] = Utils\Common::getTranslatedOption( 'bookly_l10n_required_phone' );
        }
    }

    /**
     * Validate name.
     *
     * @param string $field
     * @param string $name
     */
    public function validateName( $field, $name )
    {
        if ( $name != '' ) {
            $max_length = 255;
            if ( preg_match_all( '/./su', $name, $matches ) > $max_length ) {
                $this->errors[ $field ] = sprintf(
                    __( '"%s" is too long (%d characters max).', 'bookly' ),
                    $name,
                    $max_length
                );
            }
        } else {
            switch ( $field ) {
                case 'full_name' :
                    $this->errors[ $field ] = Utils\Common::getTranslatedOption( 'bookly_l10n_required_name' );
                    break;
                case 'first_name' :
                    $this->errors[ $field ] = Utils\Common::getTranslatedOption( 'bookly_l10n_required_first_name' );
                    break;
                case 'last_name' :
                    $this->errors[ $field ] = Utils\Common::getTranslatedOption( 'bookly_l10n_required_last_name' );
                    break;
            }
        }
    }

    /**
     * Validate number.
     *
     * @param string $field
     * @param mixed $number
     * @param bool $required
     */
    public function validateNumber( $field, $number, $required = false )
    {
        if ( $number != '' ) {
            if ( ! is_numeric( $number ) ) {
                $this->errors[ $field ] = __( 'Invalid number', 'bookly' );
            }
        } elseif ( $required ) {
            $this->errors[ $field ] = __( 'Required', 'bookly' );
        }
    }

    /**
     * Validate date.
     *
     * @param string $field
     * @param string $date
     * @param bool $required
     */
    public function validateDate( $field, $date, $required = false )
    {
        if ( $date != '' ) {
            if ( date_create( $date ) === false ) {
                $this->errors[ $field ] = __( 'Invalid date', 'bookly' );
            }
        } elseif ( $required ) {
            $this->errors[ $field ] = __( 'Required', 'bookly' );
        }
    }

    /**
     * Validate time.
     *
     * @param string $field
     * @param string $time
     * @param bool $required
     */
    public function validateTime( $field, $time, $required = false )
    {
        if ( $time != '' ) {
            if ( ! preg_match( '/^-?\d{2}:\d{2}$/', $time ) ) {
                $this->errors[ $field ] = __( 'Invalid time', 'bookly' );
            }
        } elseif ( $required ) {
            $this->errors[ $field ] = __( 'Required', 'bookly' );
        }
    }

    /**
     * Post-validate customer.
     *
     * @param array $data
     * @param UserBookingData $userData
     */
    public function postValidateCustomer( $data, UserBookingData $userData )
    {
        if ( empty ( $this->errors ) ) {
            $user_id = get_current_user_id();
            $customer = new Entities\Customer();
            if ( $user_id > 0 ) {
                // Try to find customer by WP user ID.
                $customer->loadBy( array( 'wp_user_id' => $user_id ) );
            }
            $verify_customer_details = get_option( 'bookly_cst_verify_customer_details', false );
            if ( ! $customer->isLoaded() ) {
                $entity = BookingProxy\Pro::getCustomerByFacebookId( $userData->getFacebookId() );
                if ( $entity ) {
                    $customer = $entity;
                }
                if ( ! $customer->isLoaded() ) {
                    // Try to find customer by 'primary' identifier.
                    $identifier = Config::phoneRequired() ? 'phone' : 'email';
                    if ( $data[ $identifier ] !== '' ) {
                        $customer->loadBy( array( $identifier => $data[ $identifier ] ) );
                    }
                    if ( ! $customer->isLoaded() ) {
                        // Try to find customer by 'secondary' identifier.
                        $identifier = Config::phoneRequired() ? 'email' : 'phone';
                        if ( $data[ $identifier ] !== '' ) {
                            $customer->loadBy( array( 'phone' => '', 'email' => '', $identifier => $data[ $identifier ] ) );
                        }
                    }
                    if ( Config::allowDuplicates() ) {
                        if ( Config::showFirstLastName() ) {
                            $customer_data = array(
                                'first_name' => $data['first_name'],
                                'last_name' => $data['last_name'],
                            );
                        } else {
                            $customer_data = array( 'full_name' => $data['full_name'] );
                        }
                        if ( $data['email'] != '' ) {
                            $customer_data['email'] = $data['email'];
                        }
                        if ( $data['phone'] != '' ) {
                            $customer_data['phone'] = $data['phone'];
                        }
                        $customer->loadBy( $customer_data );
                    } elseif ( ! isset ( $data['force_update_customer'] ) && $customer->isLoaded() ) {
                        // Find difference between new and existing data.
                        $diff = array();
                        $fields = array(
                            'phone' => Utils\Common::getTranslatedOption( 'bookly_l10n_label_phone' ),
                            'email' => Utils\Common::getTranslatedOption( 'bookly_l10n_label_email' ),
                        );
                        $current = $customer->getFields();
                        if ( Config::showFirstLastName() ) {
                            $fields['first_name'] = Utils\Common::getTranslatedOption( 'bookly_l10n_label_first_name' );
                            $fields['last_name'] = Utils\Common::getTranslatedOption( 'bookly_l10n_label_last_name' );
                        } else {
                            $fields['full_name'] = Utils\Common::getTranslatedOption( 'bookly_l10n_label_name' );
                        }
                        foreach ( $fields as $field => $name ) {
                            if (
                                $data[ $field ] !== '' &&
                                $current[ $field ] !== '' &&
                                strcasecmp( $data[ $field ], $current[ $field ] ) !== 0
                            ) {
                                $diff[] = $name;
                            }
                        }
                        if ( ! empty ( $diff ) ) {
                            if ( $verify_customer_details === 'on_update' && $data['verification_code'] != $userData->getVerificationCode() ) {
                                $this->errors['verify'] = $identifier;
                            } else {
                                $this->errors['customer'] = sprintf(
                                    __( 'Your %s: %s is already associated with another %s.<br/>Press Update if we should update your user data, or press Cancel to edit entered data.', 'bookly' ),
                                    $fields[ $identifier ],
                                    $data[ $identifier ],
                                    implode( ', ', $diff )
                                );
                            }
                        }
                    }
                }
            }
            // Add customer name, email and phone to send notification
            if ( ! $customer->isLoaded() ) {
                if ( Config::showFirstLastName() ) {
                    $customer->setFirstName( $data['first_name'] );
                    $customer->setLastName( $data['last_name'] );
                } else {
                    $customer->setFullName( $data['full_name'] );
                }
                $customer->setEmail( $data['email'] );
                $customer->setPhone( $data['phone'] );
            }

            // Verify customer details
            if ( in_array( $verify_customer_details, array( 'always_phone', 'always_email' ) ) && $data['verification_code'] != $userData->getVerificationCode() ) {
                $this->errors['verify'] = $verify_customer_details === 'always_phone' ? 'phone' : 'email';
            }

            // Send message with verification code
            if ( isset( $this->errors['verify'] ) ) {
                $recipient = $this->errors['verify'] == 'phone' ? $customer->getPhone() : $customer->getEmail();
                $this->errors['verify_text'] = $this->errors['verify'] == 'phone' ? __( 'Enter verification code from SMS', 'bookly' ) : __( 'Enter verification code from email', 'bookly' );
                $this->errors['incorrect_code_text'] = $this->errors['verify'] == 'phone' ? Utils\Common::getTranslatedOption( 'bookly_l10n_incorrect_phone_verification_code' ) : Utils\Common::getTranslatedOption( 'bookly_l10n_incorrect_email_verification_code' );
                if ( $userData->getVerificationCodeSent() !== $recipient ) {
                    Sender::send( $customer, $userData->getVerificationCode(), $this->errors['verify'] );
                    $userData->setVerificationCodeSent( $recipient );
                }
            }

            // Check "skip payment" custom groups settings
            if ( BookingProxy\CustomerGroups::getSkipPayment( $customer ) ) {
                $this->errors['group_skip_payment'] = true;
            }
            // Check appointments limit
            $data = array();
            foreach ( $userData->cart->getItems() as $cart_item ) {
                if ( $cart_item->toBePutOnWaitingList() ) {
                    // Skip waiting list items.
                    continue;
                }

                $service = $cart_item->getService();
                $slots = $cart_item->getSlots();

                $data[ $service->getId() ]['service'] = $service;
                $data[ $service->getId() ]['dates'][] = $slots[0][2];
            }
            foreach ( $data as $service_data ) {
                if ( $service_data['service']->appointmentsLimitReached( $customer->getId(), $service_data['dates'] ) ) {
                    $this->errors['appointments_limit_reached'] = true;
                    break;
                }
            }
        }
    }

    /**
     * Validate info fields.
     *
     * @param array $info_fields
     */
    public function validateInfoFields( array $info_fields )
    {
        $this->errors = Proxy\CustomerInformation::validate( $this->errors, $info_fields );
    }

    /**
     * Validate cart.
     *
     * @param array $cart
     * @param int $form_id
     */
    public function validateCart( $cart, $form_id )
    {
        foreach ( $cart as $cart_key => $cart_parameters ) {
            foreach ( $cart_parameters as $parameter => $value ) {
                switch ( $parameter ) {
                    case 'custom_fields':
                        $this->errors = Proxy\CustomFields::validate( $this->errors, $value, $form_id, $cart_key );
                        break;
                }
            }
        }
    }

    /**
     * Get errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}