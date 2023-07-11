<?php
namespace Bookly\Frontend\Modules\Booking;

use Bookly\Lib;
use Bookly\Frontend\Components\Booking\InfoText;
use Bookly\Frontend\Modules\Booking\Lib\Steps;
use Bookly\Frontend\Modules\Booking\Lib\Errors;
use Bookly\Frontend\Modules\Booking\Proxy as BookingProxy;

/**
 * Class Ajax
 *
 * @package Bookly\Frontend\Modules\Booking
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'anonymous' );
    }

    /**
     * 1. Step service.
     * response JSON
     */
    public static function renderService()
    {
        $form_id = self::parameter( 'form_id' );

        if ( $form_id ) {
            $userData = new Lib\UserBookingData( $form_id );
            if ( ! self::parameter( 'reset_form' ) ) {
                $userData->load();
            }

            self::_handleTimeZone( $userData );

            if ( self::hasParameter( 'new_chain' ) ) {
                $userData->resetChain();
            }

            if ( self::hasParameter( 'edit_cart_item' ) ) {
                $cart_key = self::parameter( 'edit_cart_item' );
                $userData->setEditCartKeys( array( $cart_key ) );
                $userData->setChainFromCartItem( $cart_key );
            }

            $progress_tracker = self::_prepareProgressTracker( Steps::SERVICE, $userData );
            $info_text = InfoText::prepare( Steps::SERVICE, Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_info_service_step' ), $userData );

            // Available days and times.
            $days_times = Lib\Config::getDaysAndTimes();
            // Prepare week days that need to be checked.
            $days_checked = $userData->getDays();
            if ( empty( $days_checked ) ) {
                // Check all available days.
                $days_checked = array_keys( $days_times['days'] );
            }
            $bounding = Lib\Config::getBoundingDaysForPickadate();

            $casest = Lib\Config::getCaSeSt();

            if ( Lib\Config::locationsActive() ) {
                $locasest = $casest['locations'];
            } else {
                $locasest = array();
            }

            $response = Proxy\Shared::stepOptions( array(
                'success' => true,
                'csrf_token' => Lib\Utils\Common::getCsrfToken(),
                'html' => self::renderTemplate( '1_service', array(
                    'progress_tracker' => $progress_tracker,
                    'info_text' => $info_text,
                    'userData' => $userData,
                    'days' => $days_times['days'],
                    'times' => $days_times['times'],
                    'days_checked' => $days_checked,
                    'show_cart_btn' => self::_showCartButton( $userData ),
                ), false ),
                'categories' => $casest['categories'],
                'chain' => $userData->chain->getItemsData(),
                'date_max' => $bounding['date_max'],
                'date_min' => $bounding['date_min'],
                'locations' => $locasest,
                'services' => $casest['services'],
                'staff' => $casest['staff'],
                'show_category_info' => (bool)get_option( 'bookly_app_show_category_info' ),
                'show_service_info' => (bool)get_option( 'bookly_app_show_service_info' ),
                'show_staff_info' => (bool)get_option( 'bookly_app_show_staff_info' ),
                'show_ratings' => (bool)get_option( 'bookly_ratings_app_show_on_frontend' ),
                'service_name_with_duration' => (bool)get_option( 'bookly_app_service_name_with_duration' ),
                'staff_name_with_price' => (bool)get_option( 'bookly_app_staff_name_with_price' ),
                'collaborative_hide_staff' => (bool)get_option( 'bookly_collaborative_hide_staff' ),
                'required' => array(
                    'staff' => (int)get_option( 'bookly_app_required_employee' ),
                ),
                'l10n' => array(
                    'category_label' => Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_label_category' ),
                    'category_option' => Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_option_category' ),
                    'service_label' => Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_label_service' ),
                    'service_option' => Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_option_service' ),
                    'service_error' => Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_required_service' ),
                    'staff_label' => Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_label_employee' ),
                    'staff_option' => Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_option_employee' ),
                    'staff_error' => Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_required_employee' ),
                ),
            ), 'service', $userData );
            $userData->sessionSave();
        } else {
            $response = array( 'success' => false, 'error' => Errors::FORM_ID_ERROR );
        }

        // Output JSON response.
        wp_send_json( $response );
    }

    /**
     * 2. Step Extras.
     * response JSON
     */
    public static function renderExtras()
    {
        $userData = new Lib\UserBookingData( self::parameter( 'form_id' ) );
        $loaded = ! self::parameter( 'reset_form' ) && $userData->load();
        if ( ! $loaded ) {
            // all previous steps are skipped
            self::_setDataForSkippedServiceStep( $userData );
            $userData->setFirstStep( Steps::EXTRAS );
            $loaded = true;
        }

        if ( $loaded ) {
            if ( self::hasParameter( 'new_chain' ) ) {
                self::_handleTimeZone( $userData );
                self::_setDataForSkippedServiceStep( $userData );
            }

            if ( self::hasParameter( 'edit_cart_item' ) ) {
                $cart_key = self::parameter( 'edit_cart_item' );
                $userData
                    ->setEditCartKeys( array( $cart_key ) )
                    ->setChainFromCartItem( $cart_key );
            }

            $progress_tracker = self::_prepareProgressTracker( Steps::EXTRAS, $userData );
            $info_text = InfoText::prepare( Steps::EXTRAS, Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_info_extras_step' ), $userData );
            $show_cart_btn = self::_showCartButton( $userData );

            $response = Proxy\Shared::stepOptions( array(
                'success' => true,
                'csrf_token' => Lib\Utils\Common::getCsrfToken(),
                'format_price' => Lib\Utils\Price::formatOptions(),
                'html' => Proxy\ServiceExtras::getStepHtml(
                    $userData,
                    $show_cart_btn,
                    $info_text,
                    $progress_tracker,
                    $userData->getFirstStep() != Steps::EXTRAS
                ),
            ), 'extras', $userData );

            $userData->sessionSave();

            // Output JSON response.
            wp_send_json( $response );
        }

        Errors::sendSessionError();
    }

    /**
     * 3. Step time.
     * response JSON
     */
    public static function renderTime()
    {
        $userData = new Lib\UserBookingData( self::parameter( 'form_id' ) );
        $loaded = ! self::parameter( 'reset_form' ) && $userData->load();

        if ( ! $loaded ) {
            // all previous steps are skipped
            self::_setDataForSkippedServiceStep( $userData );
            $userData->setFirstStep( Steps::TIME );
            $loaded = true;
        }

        if ( $loaded ) {
            self::_handleTimeZone( $userData );

            if ( self::hasParameter( 'new_chain' ) ) {
                self::_setDataForSkippedServiceStep( $userData );
            }

            if ( self::hasParameter( 'edit_cart_item' ) ) {
                $cart_key = self::parameter( 'edit_cart_item' );
                $userData
                    ->setEditCartKeys( array( $cart_key ) )
                    ->setChainFromCartItem( $cart_key );
            }

            $find_first_free_slot = true;
            $progress_tracker = self::_prepareProgressTracker( Steps::TIME, $userData );
            $info_text = InfoText::prepare( Steps::TIME, Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_info_time_step' ), $userData );
            $last_date = new Lib\Slots\DatePoint( date_create( 'today' ) );
            if ( Lib\Config::showCalendar() ) {
                $last_date = $last_date->modify( Lib\Config::getMaximumAvailableDaysForBooking() . ' days' );
                // If the customer has selected a date on the time step,
                // there is no need to search the first date with a free slot
                $find_first_free_slot = ! self::hasParameter( 'selected_date' );
            }

            $show_blocked_slots = Lib\Config::showBlockedTimeSlots();
            $finder = new Lib\Slots\Finder( $userData, null, null, null, array(), null, Lib\Config::showSingleTimeSlotPerDay() );
            $finder->setSelectedDate( Lib\Config::showSingleTimeSlot() ? null : self::parameter( 'selected_date', $userData->getDateFrom() ) );
            $slots_data = array();
            $block_time_slots = false;
            while ( true ) {
                $finder->prepare()->load();
                foreach ( $finder->getSlots() as $group => $group_slots ) {
                    /** @var Lib\Slots\Range[] $group_slots */
                    $slots_data[ $group ] = array(
                        'title' => date_i18n( ( $finder->isServiceDurationInDays() ? 'M' : 'D, M d' ), strtotime( $group ) ),
                        'slots' => array(),
                    );
                    foreach ( $group_slots as $slot ) {
                        $slots_data[ $group ]['slots'][] = array(
                            'data' => $slot->buildSlotData(),
                            'time_text' => $slot->start()->toClientTz()->formatI18n( $finder->isServiceDurationInDays() ? 'D, M d' : get_option( 'time_format' ) ),
                            'status' => $block_time_slots ? 'booked' : ( $slot->waitingListEverStarted() ? 'waiting-list' : ( $slot->fullyBooked() ? 'booked' : '' ) ),
                            'additional_text' => $slot->waitingListEverStarted() ? '(' . $slot->maxOnWaitingList() . ')' : ( Lib\Config::groupBookingActive() ? Proxy\GroupBooking::getTimeSlotText( $slot ) : '' ),
                            'slot' => $slot,
                        );
                        if ( ! $slot->waitingListEverStarted() && $slot->notFullyBooked() && Lib\Config::showSingleTimeSlot() ) {
                            $block_time_slots = true;
                            if ( ! Lib\Config::showBlockedTimeSlots() ) {
                                break;
                            }
                        }
                    }
                }
                if ( Lib\Config::showCalendar() && $find_first_free_slot ) {
                    $select_next_month = empty( $slots_data ) || ( Lib\Config::showSingleTimeSlot() && ! $block_time_slots );
                    if ( ! $select_next_month && $show_blocked_slots ) {
                        foreach ( $slots_data as $group => $data ) {
                            foreach ( $data['slots'] as $slot ) {
                                if ( $slot['status'] != 'booked' ) {
                                    $finder->setSelectedDate( $group );
                                    break 3;
                                }
                            }
                        }
                        $select_next_month = true;
                    }
                    if ( $select_next_month ) {
                        // Couldn't find any slots for current month for Time step
                        $next_month = new Lib\Slots\DatePoint( date_create( $finder->getSelectedDate() ) );
                        $next_month = $next_month->modify( 'first day of next month' );
                        if ( $last_date->gte( $next_month ) ) {
                            // Looking for slots in the next month
                            $finder->setSelectedDate( $next_month->format( 'Y-m-d' ) );
                        } else {
                            break;
                        }
                    } else {
                        break;
                    }
                } else {
                    break;
                }
            }

            // Render slots by groups (day or month).
            $slots = $userData->getSlots();
            $selected_date = isset ( $slots[0][2] ) ? $slots[0][2] : null;

            $slots_data = Proxy\Shared::prepareSlotsData( $slots_data );

            $slots_data = array_map( function ( $slot_data ) {
                unset ( $slot_data['slot'] );

                return $slot_data;
            }, $slots_data );

            // Set response.
            $response = Proxy\Shared::stepOptions( array(
                'success' => true,
                'csrf_token' => Lib\Utils\Common::getCsrfToken(),
                'has_slots' => ! empty ( $slots_data ),
                'has_more_slots' => $finder->hasMoreSlots(),
                'day_one_column' => Lib\Config::showDayPerColumn(),
                'slots_data' => $slots_data,
                'selected_date' => $selected_date,
                'time_slots_wide' => Lib\Config::showWideTimeSlots(),
                'show_calendar' => Lib\Config::showCalendar(),
                'is_rtl' => is_rtl(),
                'html' => self::renderTemplate( '3_time', array(
                    'progress_tracker' => $progress_tracker,
                    'info_text' => $info_text,
                    'date' => Lib\Config::showCalendar() ? $finder->getSelectedDateForPickadate() : null,
                    'has_slots' => ! empty ( $slots_data ),
                    'show_cart_btn' => self::_showCartButton( $userData ),
                    'userData' => $userData,
                ), false ),
            ), 'time', $userData );

            if ( Lib\Config::showCalendar() ) {
                if ( Lib\Config::showSingleTimeSlot() ) {
                    $date_with_slot = null;
                    if ( ! empty ( $slots_data ) ) {
                        $last_slot = end( $slots_data );
                        if ( ! empty( $last_slot ) && isset( $last_slot['slots'][0] ) ) {
                            $last_slot_date = Lib\Slots\DatePoint::fromStr( $last_slot['slots'][0]['data'][0][2] )->toClientTz()->value();
                            $date_with_slot = array(
                                $last_slot_date->format( 'Y' ) + 0,
                                $last_slot_date->format( 'n' ) - 1,
                                $last_slot_date->format( 'j' ) + 0,
                            );
                        }
                        $response['date_max'] = $date_with_slot;
                        $response['date_min'] = $date_with_slot;
                    }
                } else {
                    $bounding = Lib\Config::getBoundingDaysForPickadate( $userData->chain );
                    $response['date_max'] = $bounding['date_max'];
                    $response['date_min'] = $bounding['date_min'];
                    $response['disabled_days'] = $finder->getDisabledDaysForPickadate();
                }
            }
            $userData->sessionSave();

            // Output JSON response.
            wp_send_json( $response );
        }

        Errors::sendSessionError();
    }

    /**
     * Render next time for step Time.
     * response JSON
     */
    public static function renderNextTime()
    {
        $userData = new Lib\UserBookingData( self::parameter( 'form_id' ) );

        if ( $userData->load() ) {
            $finder = new Lib\Slots\Finder( $userData, null, null, null, array(), null, Lib\Config::showSingleTimeSlotPerDay() );
            $finder->setLastFetchedSlot( self::parameter( 'last_slot' ) );
            $finder->prepare()->load();

            $slots = $userData->getSlots();
            $selected_date = isset ( $slots[0][2] ) ? $slots[0][2] : null;

            $slots_data = array();
            foreach ( $finder->getSlots() as $group => $group_slots ) {
                /** @var Lib\Slots\Range[] $group_slots */
                $slots_data[ $group ] = array(
                    'title' => date_i18n( ( $finder->isServiceDurationInDays() ? 'M' : 'D, M d' ), strtotime( $group ) ),
                    'slots' => array(),
                );
                foreach ( $group_slots as $slot ) {
                    $slots_data[ $group ]['slots'][] = array(
                        'data' => $slot->buildSlotData(),
                        'time_text' => $slot->start()->toClientTz()->formatI18n( $finder->isServiceDurationInDays() ? 'D, M d' : get_option( 'time_format' ) ),
                        'status' => $slot->waitingListEverStarted() ? 'waiting-list' : ( $slot->fullyBooked() ? 'booked' : '' ),
                        'additional_text' => $slot->waitingListEverStarted() ? '(' . $slot->maxOnWaitingList() . ')' : ( Lib\Config::groupBookingActive() ? Proxy\GroupBooking::getTimeSlotText( $slot ) : '' ),
                        'slot' => $slot,
                    );
                }
            }

            $slots_data = Proxy\Shared::prepareSlotsData( $slots_data );

            $slots_data = array_map( function ( $slot_data ) {
                unset ( $slot_data['slot'] );

                return $slot_data;
            }, $slots_data );

            // Set response.
            $response = array(
                'success' => true,
                'slots_data' => $slots_data,
                'has_slots' => ! empty( $slots_data ),
                'has_more_slots' => $finder->hasMoreSlots(), // show/hide the next button
                'selected_date' => $selected_date,
            );
            $userData->sessionSave();

            wp_send_json( $response );
        }

        Errors::sendSessionError();
    }

    /**
     * 4. Step repeat.
     * response JSON
     */
    public static function renderRepeat()
    {
        $form_id = self::parameter( 'form_id' );
        $userData = new Lib\UserBookingData( $form_id );

        if ( $userData->load() ) {
            $show_cart_btn = self::_showCartButton( $userData );
            $info_text = InfoText::prepare( Steps::REPEAT, Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_info_repeat_step' ), $userData );
            $progress_tracker = self::_prepareProgressTracker( Steps::REPEAT, $userData );

            $response = Proxy\Shared::stepOptions( array(
                'success' => true,
                'html' => Proxy\RecurringAppointments::getStepHtml( $userData, $show_cart_btn, $info_text, $progress_tracker ),
            ), 'repeat', $userData );
            $userData->sessionSave();

            // Output JSON response.
            wp_send_json( $response );
        }

        Errors::sendSessionError();
    }

    /**
     * 5. Step cart.
     * response JSON
     */
    public static function renderCart()
    {
        $userData = new Lib\UserBookingData( self::parameter( 'form_id' ) );
        $loaded = ! self::parameter( 'reset_form' ) && $userData->load();
        if ( ! $loaded ) {
            // all previous steps are skipped
            self::_setDataForSkippedServiceStep( $userData );
            $userData->setFirstStep( Steps::CART );
            $loaded = true;
        }

        if ( $loaded ) {
            $userData = Proxy\Tasks::prepareUserData( $userData );
            if ( self::hasParameter( 'add_to_cart' ) ) {
                $userData->addChainToCart();
            }
            $progress_tracker = self::_prepareProgressTracker( Steps::CART, $userData );
            $info_text = InfoText::prepare( Steps::CART, Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_info_cart_step' ), $userData );

            $response = Proxy\Shared::stepOptions( array(
                'success' => true,
                'html' => Proxy\Cart::getStepHtml( $userData, $progress_tracker, $info_text, $userData->getFirstStep() != Steps::CART ),
            ), 'cart', $userData );
            $userData->sessionSave();

            // Output JSON response.
            wp_send_json( $response );
        }

        Errors::sendSessionError();
    }

    /**
     * 6. Step details.
     *
     * @throws
     */
    public static function renderDetails()
    {
        $userData = new Lib\UserBookingData( self::parameter( 'form_id' ) );
        $loaded = ! self::parameter( 'reset_form' ) && $userData->load();
        if ( ! $loaded ) {
            // all previous steps are skipped
            self::_setDataForSkippedServiceStep( $userData );
            $userData->setFirstStep( Steps::DETAILS );
            $loaded = true;
        }

        if ( $loaded ) {
            $userData = Proxy\Tasks::prepareUserData( $userData );
            if ( ! Lib\Config::showStepCart() ) {
                $userData->addChainToCart();
            }

            $info_text = Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_info_details_step' );
            $info_text_guest = ! get_current_user_id() && ! $userData->getFacebookId()
                ? Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_info_details_step_guest' )
                : '';

            // Render main template.
            $html = self::renderTemplate( '6_details', array(
                'progress_tracker' => self::_prepareProgressTracker( Steps::DETAILS, $userData ),
                'info_text' => InfoText::prepare( Steps::DETAILS, $info_text, $userData ),
                'info_text_guest' => InfoText::prepare( Steps::DETAILS, $info_text_guest, $userData ),
                'userData' => $userData,
                'show_back_btn' => $userData->getFirstStep() != Steps::DETAILS,
            ), false );

            // Render additional templates.
            $html .= self::renderTemplate( '_customer_duplicate_msg', array(), false );
            if ( get_option( 'bookly_cst_verify_customer_details', false ) ) {
                $html .= self::renderTemplate( '_customer_verification_code', array(), false );
            }
            if (
                ! get_current_user_id() &&
                ! $userData->getFacebookId() && (
                    Lib\Config::showLoginButton() ||
                    strpos( $info_text . $info_text_guest, '{login_form}' ) !== false
                )
            ) {
                $html .= self::renderTemplate( '_login_form', array(), false );
            }

            $response = Proxy\Shared::stepOptions( array(
                'success' => true,
                'html' => $html,
                'update_details_dialog' => (int)get_option( 'bookly_cst_show_update_details_dialog' ),
                'intlTelInput' => get_option( 'bookly_cst_phone_default_country' ) != 'disabled' ? array(
                    'enabled' => 1,
                    'utils' => plugins_url( 'intlTelInput.utils.js', Lib\Plugin::getDirectory() . '/frontend/resources/js/intlTelInput.utils.js' ),
                    'country' => get_option( 'bookly_cst_phone_default_country' ),
                ) : array(
                    'enabled' => 0,
                ),
                'woocommerce' => array(
                    'enabled' => 0,
                ),
                'l10n' => array(
                    'terms_error' => Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_error_terms' ),
                ),
            ), 'details', $userData );
            $userData->sessionSave();

            // Output JSON response.
            wp_send_json( $response );
        }

        Errors::sendSessionError();
    }

    /**
     * 7. Step payment.
     * response JSON
     */
    public static function renderPayment()
    {
        $userData = new Lib\UserBookingData( self::parameter( 'form_id' ) );

        if ( $userData->load() ) {
            /** @var string $payment_step 'skip' | 'show' | 'show-100%-discount' */
            $payment_step = Lib\Config::paymentStepDisabled() ? 'skip' : 'show';
            $show_cart = Lib\Config::showStepCart();
            if ( ! $show_cart ) {
                $userData->addChainToCart();
            }

            if ( $userData->getCouponCode() ) {
                if ( ! $userData->getCoupon()->validForCart( $userData->cart ) || ! $userData->getCoupon()->validForCustomer( $userData->getCustomer() ) ) {
                    $userData->deleteCoupon();
                }
            }

            $cart_info = $userData->cart->getInfo();
            $balance = $cart_info->getGiftCard() ? $cart_info->getGiftCard()->getBalance() : 0;
            if ( $cart_info->getPayNowWithoutGiftCard() <= $balance ) {
                $payment_step = $cart_info->withDiscount() ? 'show-100%-discount' : 'skip';
            }

            if ( $payment_step !== 'skip' ) {
                $progress_tracker = self::_prepareProgressTracker( Steps::PAYMENT, $userData );
                $payment_options = array();

                // Prepare info texts.
                if ( $payment_step === 'show' ) {
                    $gateways = self::getGateways( $userData, $cart_info );

                    if ( $gateways ) {
                        $order = Lib\Config::getGatewaysPreference();

                        if ( $order ) {
                            foreach ( $order as $payment_system ) {
                                if ( array_key_exists( $payment_system, $gateways ) ) {
                                    $payment_options[ $payment_system ] = $gateways[ $payment_system ]['html'];
                                }
                            }
                        }
                        foreach ( $gateways as $slug => $data ) {
                            if ( ! $order || ! in_array( $slug, $order ) ) {
                                if ( $data['pay'] == 0 ) {
                                    $payment_step = 'show-100%-discount';
                                    $payment_options = array();
                                    break;
                                }
                                $payment_options[ $slug ] = $data['html'];
                            }
                        }
                    } else {
                        $payment_step = 'payment-impossible';
                    }
                }

                if ( $payment_step === 'payment-impossible' ) {
                    $html = Proxy\Pro::getHtmlPaymentImpossible( $progress_tracker, $userData );
                } else {
                    if ( $payment_step === 'show-100%-discount' ) {
                        $info_text_tpl = Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_info_payment_step_with_100percents_off_price' );
                    } else {
                        $info_text_tpl = Lib\Utils\Common::getTranslatedOption(
                            count( $userData->cart->getItems() ) > 1
                                ? 'bookly_l10n_info_payment_step_several_apps'
                                : 'bookly_l10n_info_payment_step_single_app'
                        );
                    }

                    $info_text = InfoText::prepare( Steps::PAYMENT, $info_text_tpl, $userData );
                    $html = self::renderTemplate( '7_payment', array(
                        'form_id' => self::parameter( 'form_id' ),
                        'progress_tracker' => $progress_tracker,
                        'info_text' => $info_text,
                        'payment_options' => $payment_options,
                        'page_url' => self::parameter( 'page_url' ),
                        'userData' => $userData,
                    ), false );
                }

                // Set response.
                $response = Proxy\Shared::stepOptions( array(
                    'success' => true,
                    'disabled' => false,
                    'html' => $html,
                ), 'payment', $userData );
            } else {
                $response = array(
                    'success' => true,
                    'disabled' => true,
                );
            }
            $userData->sessionSave();
            // Output JSON response.
            wp_send_json( $response );
        }

        Errors::sendSessionError();
    }

    /**
     * 8. Step done ( complete ).
     * response JSON
     */
    public static function renderComplete()
    {
        $userData = new Lib\UserBookingData( self::parameter( 'form_id' ) );
        if ( $userData->load() ) {
            $progress_tracker = self::_prepareProgressTracker( Steps::DONE, $userData );
            $state = self::parameter( 'error' );
            $codes = InfoText::getCodes( Steps::DONE, $userData );
            if ( $state === 'appointments_limit_reached' ) {
                $info_text = InfoText::replace( Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_info_complete_step_limit_error' ), $codes );
            } else {
                if ( $state === 'group_skip_payment' && Lib\Config::customerGroupsActive() ) {
                    $info_text = InfoText::replace( Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_info_complete_step_group_skip_payment' ), $codes );
                } else {
                    $payment = $userData->extractPaymentStatus( null );
                    do {
                        if ( $payment ) {
                            switch ( $payment['status'] ) {
                                case 'processing':
                                    $state = 'processing';
                                    $info_text = Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_info_complete_step_processing' );
                                    break ( 2 );
                            }
                        }
                        $state = 'completed';
                        $info_text = Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_info_complete_step' );
                    } while ( 0 );
                }
                $info_text = ( Lib\Config::proActive() ? Proxy\Pro::prepareHtmlContentDoneStep( $userData, $codes ) : '' ) . InfoText::replace( $info_text, $codes, true, true, array( 'online_meeting_url', 'online_meeting_join_url' ) );
            }
            $response = Proxy\Shared::stepOptions( array(
                'success' => true,
                'html' => self::renderTemplate( '8_complete', compact( 'progress_tracker', 'info_text', 'state' ), false ),
            ), 'complete', $userData );
            $userData->sessionSave();

            // Output JSON response.
            wp_send_json( $response );
        }

        Errors::sendSessionError();
    }

    /**
     * Save booking data in session.
     */
    public static function sessionSave()
    {
        $form_id = self::parameter( 'form_id' );
        $errors = array();
        if ( $form_id ) {
            $userData = new Lib\UserBookingData( $form_id );
            if ( $userData->load() ) {
                $parameters = self::parameters();
                $errors = $userData->validate( $parameters );
                if ( empty ( $errors ) || $errors === array( 'group_skip_payment' => true ) ) {
                    if ( self::hasParameter( 'no_extras' ) ) {
                        foreach ( $parameters['chain'] as &$item ) {
                            $item['extras'] = array();
                        }
                    } elseif ( self::hasParameter( 'extras' ) ) {
                        $parameters['chain'] = $userData->chain->getItemsData();
                        foreach ( $parameters['chain'] as $key => &$item ) {
                            // Decode extras.
                            $item['extras'] = json_decode( $parameters['extras'][ $key ], true );
                        }
                    } elseif ( self::hasParameter( 'slots' ) ) {
                        // Decode slots.
                        $parameters['slots'] = json_decode( $parameters['slots'], true );
                    } elseif ( self::hasParameter( 'cart' ) ) {
                        $parameters['captcha_ids'] = json_decode( $parameters['captcha_ids'], true );
                        foreach ( $parameters['cart'] as &$service ) {
                            // Remove captcha from custom fields.
                            $custom_fields = array_filter( json_decode( $service['custom_fields'], true ), function ( $field ) use ( $parameters ) {
                                return ! in_array( $field['id'], $parameters['captcha_ids'] );
                            } );
                            // Index the array numerically.
                            $service['custom_fields'] = array_values( $custom_fields );
                        }
                        // Copy custom fields to all cart items.
                        $cart = array();
                        $cf_per_service = Lib\Config::customFieldsPerService();
                        $merge_cf = Lib\Config::customFieldsMergeRepeating();
                        foreach ( $userData->cart->getItems() as $cart_key => $_cart_item ) {
                            $cart[ $cart_key ] = $cf_per_service
                                ? $parameters['cart'][ $merge_cf ? $_cart_item->getService()->getId() : $cart_key ]
                                : $parameters['cart'][0];
                        }
                        $parameters['cart'] = $cart;
                    }
                    $userData->fillData( $parameters );
                }
                $userData->sessionSave();
            } else {
                Errors::sendSessionError();
            }
        }
        $errors['success'] = empty( $errors );

        wp_send_json( $errors );
    }

    /**
     * Save cart appointments.
     */
    public static function saveAppointment()
    {
        $userData = new Lib\UserBookingData( self::parameter( 'form_id' ) );

        if ( $userData->load() ) {
            $failed_cart_key = $userData->cart->getFailedKey();
            if ( $failed_cart_key === null ) {
                $cart_info = $userData->cart->getInfo();
                $is_payment_disabled = Lib\Config::paymentStepDisabled();
                $skip_payment = BookingProxy\CustomerGroups::getSkipPayment( $userData->getCustomer() );
                $gateways = self::getGateways( $userData, clone $cart_info );

                if ( $is_payment_disabled || isset( $gateways['local'] ) || $cart_info->getPayNow() <= 0 || $skip_payment ) {
                    // Handle coupon.
                    $coupon = $userData->getCoupon();
                    if ( $coupon ) {
                        $coupon->claim()->save();
                    }
                    // Handle payment.
                    $payment = null;
                    if ( ! $is_payment_disabled && ! $skip_payment ) {
                        if ( $cart_info->getTotal() <= 0 ) {
                            // Check if all items in waiting list.
                            $waiting_list_only = true;
                            foreach ( $userData->cart->getItems() as $item ) {
                                if ( ! $item->toBePutOnWaitingList() ) {
                                    $waiting_list_only = false;
                                    break;
                                }
                            }
                            if ( ! $waiting_list_only ) {
                                $payment = new Lib\Entities\Payment();
                                $payment
                                    ->setType( Lib\Entities\Payment::TYPE_FREE )
                                    ->setStatus( Lib\Entities\Payment::STATUS_COMPLETED )
                                    ->setPaidType( Lib\Entities\Payment::PAY_IN_FULL )
                                    ->setTotal( 0 )
                                    ->setPaid( 0 )
                                    ->save();
                            }
                        } else {
                            $payment = new Lib\Entities\Payment();
                            $status = Lib\Entities\Payment::STATUS_PENDING;
                            $type = Lib\Entities\Payment::TYPE_LOCAL;
                            foreach ( $gateways as $gateway => $data ) {
                                if ( $data['pay'] == 0 ) {
                                    $status = Lib\Entities\Payment::STATUS_COMPLETED;
                                    $type = Lib\Entities\Payment::TYPE_FREE;
                                    $cart_info->setGateway( $gateway );
                                    $payment->setGatewayPriceCorrection( $cart_info->getPriceCorrection() );
                                    break;
                                }
                            }

                            $payment
                                ->setType( $type )
                                ->setStatus( $status )
                                ->setPaidType( Lib\Entities\Payment::PAY_IN_FULL )
                                ->setTotal( $cart_info->getTotal() )
                                ->setTax( $cart_info->getTotalTax() )
                                ->setPaid( 0 )
                                ->save();
                        }
                    }
                    // Save cart.
                    $order = $userData->save( $payment );
                    if ( $payment !== null ) {
                        $payment->setDetailsFromOrder( $order, $cart_info )->save();
                    }
                    // Send notifications.
                    Lib\Notifications\Cart\Sender::send( $order );
                    $response = array(
                        'success' => true,
                    );
                } else {
                    $response = array(
                        'success' => false,
                        'error' => Errors::PAY_LOCALLY_NOT_AVAILABLE,
                    );
                }
            } else {
                $response = array(
                    'success' => false,
                    'failed_cart_key' => $failed_cart_key,
                    'error' => Errors::CART_ITEM_NOT_AVAILABLE,
                );
            }
            $userData->sessionSave();

            wp_send_json( $response );
        }

        Errors::sendSessionError();
    }

    /**
     * Check cart.
     */
    public static function checkCart()
    {
        $userData = new Lib\UserBookingData( self::parameter( 'form_id' ) );

        if ( $userData->load() ) {
            $failed_cart_key = $userData->cart->getFailedKey();
            if ( $failed_cart_key === null ) {
                $response = array( 'success' => true );
            } else {
                $response = array(
                    'success' => false,
                    'failed_cart_key' => $failed_cart_key,
                    'error' => Errors::CART_ITEM_NOT_AVAILABLE,
                );
            }

            wp_send_json( $response );
        }

        Errors::sendSessionError();
    }

    /**
     * Cancel Appointment using token.
     */
    public static function cancelAppointment()
    {
        $customer_appointment = new Lib\Entities\CustomerAppointment();

        $allow_cancel = true;
        if ( $customer_appointment->loadBy( array( 'token' => self::parameter( 'token' ) ) ) && $customer_appointment->cancelAllowed() ) {
            $customer_appointment->cancel( self::parameter( 'reason', '' ) );
        } else {
            $allow_cancel = false;
        }

        Lib\Utils\Common::cancelAppointmentRedirect( $allow_cancel );
    }

    /**
     * Approve appointment using token.
     */
    public static function approveAppointment()
    {
        $url = get_option( 'bookly_url_approve_denied_page_url' );

        // Decode token.
        $token = Lib\Utils\Common::xorDecrypt( self::parameter( 'token' ), 'approve' );
        $ca_to_approve = new Lib\Entities\CustomerAppointment();
        if ( $ca_to_approve->loadBy( array( 'token' => $token ) ) ) {
            $item = Lib\DataHolders\Booking\Item::collect( $ca_to_approve, Lib\Proxy\CustomStatuses::prepareFreeStatuses( array(
                Lib\Entities\CustomerAppointment::STATUS_APPROVED,
                Lib\Entities\CustomerAppointment::STATUS_REJECTED,
                Lib\Entities\CustomerAppointment::STATUS_CANCELLED,
                Lib\Entities\CustomerAppointment::STATUS_DONE,
            ) ) );

            if ( $item ) {
                $success = true;
                foreach ( $item->getItems() as $simple ) {
                    $ca = $simple->getCA();
                    if ( $ca->getStatus() == Lib\Entities\CustomerAppointment::STATUS_WAITLISTED ) {
                        $info = $simple->getAppointment()->getNopInfo();
                        if ( $info['total_nop'] + $ca->getNumberOfPersons() > $info['capacity_max'] ) {
                            $success = false;
                            break;
                        }
                    }
                }
                if ( $success ) {
                    $item->setStatus( Lib\Entities\CustomerAppointment::STATUS_APPROVED );
                    foreach ( $item->getItems() as $simple ) {
                        if ( $simple->getCA()->save() ) {
                            $appointment = $simple->getAppointment();
                            // Google Calendar.
                            Lib\Proxy\Pro::syncGoogleCalendarEvent( $appointment );
                            // Outlook Calendar.
                            Lib\Proxy\OutlookCalendar::syncEvent( $appointment );
                            // Waiting list.
                            Lib\Proxy\WaitingList::handleParticipantsChange( false, $appointment );
                        }
                    }
                    Lib\Notifications\Booking\Sender::send( $item );
                    $url = get_option( 'bookly_url_approve_page_url' );
                }
            }
        }

        wp_redirect( $url );
        Lib\Utils\Common::redirect( $url );
        exit ( 0 );
    }

    /**
     * Reject appointment using token.
     */
    public static function rejectAppointment()
    {
        $url = get_option( 'bookly_url_reject_denied_page_url' );

        // Decode token.
        $token = Lib\Utils\Common::xorDecrypt( self::parameter( 'token' ), 'reject' );
        $ca_to_reject = new Lib\Entities\CustomerAppointment();
        if ( $ca_to_reject->loadBy( array( 'token' => $token ) ) ) {
            $item = Lib\DataHolders\Booking\Item::collect( $ca_to_reject, Lib\Proxy\CustomStatuses::prepareFreeStatuses( array(
                Lib\Entities\CustomerAppointment::STATUS_REJECTED,
                Lib\Entities\CustomerAppointment::STATUS_CANCELLED,
                Lib\Entities\CustomerAppointment::STATUS_DONE,
            ) ) );

            if ( $item ) {
                $item->setStatus( Lib\Entities\CustomerAppointment::STATUS_REJECTED );
                Lib\Notifications\Booking\Sender::send( $item );

                foreach ( $item->getItems() as $simple ) {
                    if ( $simple->getCA()->save() ) {
                        $appointment = $simple->getAppointment();
                        if ( $simple->getExtras() != '[]' ) {
                            $extras_duration = $appointment->getMaxExtrasDuration();
                            if ( $appointment->getExtrasDuration() != $extras_duration ) {
                                $appointment->setExtrasDuration( $extras_duration );
                                $appointment->save();
                            }
                        }
                        // Google Calendar.
                        Lib\Proxy\Pro::syncGoogleCalendarEvent( $appointment );
                        // Outlook Calendar.
                        Lib\Proxy\OutlookCalendar::syncEvent( $appointment );
                        // Waiting list.
                        Lib\Proxy\WaitingList::handleParticipantsChange( false, $appointment );
                    }
                }
                $url = get_option( 'bookly_url_reject_page_url' );
            }
        }

        wp_redirect( $url );
        Lib\Utils\Common::redirect( $url );
        exit ( 0 );
    }

    /**
     * Log in to WordPress in the Details step.
     */
    public static function wpUserLogin()
    {
        $userData = new Lib\UserBookingData( self::parameter( 'form_id' ) );

        if ( $userData->load() ) {
            add_action( 'set_logged_in_cookie', function ( $logged_in_cookie ) {
                $_COOKIE[ LOGGED_IN_COOKIE ] = $logged_in_cookie;
            } );
            /** @var \WP_User $user */
            $user = wp_signon();
            if ( is_wp_error( $user ) ) {
                $response = array( 'success' => false, 'error' => Errors::INCORRECT_USERNAME_PASSWORD );
            } else {
                wp_set_current_user( $user->ID, $user->user_login );
                $customer = new Lib\Entities\Customer();
                if ( $customer->loadBy( array( 'wp_user_id' => $user->ID ) ) ) {
                    $user_info = array(
                        'email' => $customer->getEmail(),
                        'full_name' => $customer->getFullName(),
                        'first_name' => $customer->getFirstName(),
                        'last_name' => $customer->getLastName(),
                        'phone' => $customer->getPhone(),
                        'country' => $customer->getCountry(),
                        'state' => $customer->getState(),
                        'postcode' => $customer->getPostcode(),
                        'city' => $customer->getCity(),
                        'street' => $customer->getStreet(),
                        'street_number' => $customer->getStreetNumber(),
                        'additional_address' => $customer->getAdditionalAddress(),
                        'birthday' => $customer->getBirthday(),
                        'info_fields' => json_decode( $customer->getInfoFields() ),
                    );
                } else {
                    $user_info = array(
                        'email' => $user->user_email,
                        'full_name' => $user->display_name,
                        'first_name' => $user->user_firstname,
                        'last_name' => $user->user_lastname,
                    );
                }
                $userData->fillData( $user_info );
                $response = array(
                    'success' => true,
                    'data' => $user_info + array( 'csrf_token' => Lib\Utils\Common::getCsrfToken() ),
                );
            }
            $userData->sessionSave();

            // Output JSON response.
            wp_send_json( $response );
        }

        Errors::sendSessionError();
    }

    /**
     * Download ICS file for order
     */
    public static function downloadIcs()
    {
        $userData = new Lib\UserBookingData( self::parameter( 'form_id' ) );

        if ( $userData->load() && $userData->getOrderId() ) {

            $ics = Lib\Utils\Ics\Feed::createFromBookingData( $userData );

            header( 'Content-Type: text/calendar' );
            header( 'Content-Type: application/octet-stream', false );
            header( 'Content-Disposition: attachment; filename="Bookly_' . $userData->getOrderId() . '.ics"' );
            header( 'Content-Transfer-Encoding: binary' );

            echo $ics->render();
        }

        exit();
    }

    /**
     * Render progress tracker into a variable.
     *
     * @param int $step
     * @param Lib\UserBookingData $userData
     * @return string
     */
    private static function _prepareProgressTracker( $step, Lib\UserBookingData $userData )
    {
        $result = '';

        if ( get_option( 'bookly_app_show_progress_tracker' ) ) {
            $skip_payment_step = Lib\Config::paymentStepDisabled();
            if ( ! $skip_payment_step && $step > Steps::SERVICE ) {
                if ( $step < Steps::CART ) {
                    // step Cart.
                    // Assume that payment is disabled and check chain items.
                    // If one is incomplete or its price is more than zero then the payment step should be displayed.
                    $skip_payment_step = true;
                    foreach ( $userData->chain->getItems() as $item ) {
                        if ( $item->hasPayableExtras() ) {
                            $skip_payment_step = false;
                            break;
                        } else {
                            if ( $item->getService()->getType() == Lib\Entities\Service::TYPE_SIMPLE ) {
                                $staff_ids = $item->getStaffIds();
                                $staff = null;
                                if ( count( $staff_ids ) === 1 ) {
                                    $staff = Lib\Entities\Staff::find( $staff_ids[0] );
                                }
                                if ( $staff ) {
                                    $staff_service = new Lib\Entities\StaffService();
                                    $staff_service->loadBy( array(
                                        'staff_id' => $staff->getId(),
                                        'service_id' => $item->getService()->getId(),
                                        'location_id' => Lib\Proxy\Locations::prepareStaffLocationId( $item->getLocationId(), $staff->getId() ) ?: null,
                                    ) );
                                    if ( $staff_service->getPrice() > 0 ) {
                                        $skip_payment_step = false;
                                        break;
                                    }
                                } else {
                                    $skip_payment_step = false;
                                    break;
                                }
                            } else {
                                // Service::TYPE_COMPOUND
                                if ( $item->getService()->getPrice() > 0 ) {
                                    $skip_payment_step = false;
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    $cart_info = $userData->cart->getInfo();
                    if ( $cart_info->getTotal() == 0 || $cart_info->getDeposit() == 0 ) {
                        $skip_payment_step = ! $cart_info->withDiscount();
                    }
                }
            }

            $skip_time_step = Lib\Config::tasksActive() && get_option( 'bookly_tasks_show_time_step' ) === '0';

            if ( $skip_time_step && $step > Steps::SERVICE ) {
                if ( $step < Steps::CART ) {
                    foreach ( $userData->chain->getItems() as $item ) {
                        if ( $item->getService()->getTimeRequirements() !== Lib\Entities\Service::START_TIME_OFF ) {
                            $skip_time_step = false;
                        }
                    }
                } else {
                    foreach ( $userData->cart->getItems() as $item ) {
                        if ( $item->getService()->getTimeRequirements() !== Lib\Entities\Service::START_TIME_OFF ) {
                            $skip_time_step = false;
                        }
                    }
                }
            }

            $result = self::renderTemplate( '_progress_tracker', array(
                'step' => $step,
                'skip_steps' => array(
                    'service' => Lib\Session::hasFormVar( self::parameter( 'form_id' ), 'skip_service_step' ),
                    'extras' => ! ( Lib\Config::serviceExtrasActive() && get_option( 'bookly_service_extras_enabled' ) ),
                    'time' => $skip_time_step,
                    'repeat' => $skip_time_step || ! Lib\Config::recurringAppointmentsActive() || ! get_option( 'bookly_recurring_appointments_enabled' ) || Lib\Config::showSingleTimeSlot(),
                    'cart' => ! Lib\Config::showStepCart(),
                    'payment' => $skip_payment_step,
                ),
                // step extras before step time
                'step_extras_active' => $step > 3 || ( $step >= 2 && self::parameter( 'action' ) == 'bookly_render_extras' ),
            ), false );
        }

        return $result;
    }

    /**
     * Check if cart button should be shown.
     *
     * @param Lib\UserBookingData $userData
     * @return bool
     */
    private static function _showCartButton( Lib\UserBookingData $userData )
    {
        return Lib\Config::showStepCart() && count( $userData->cart->getItems() );
    }

    /**
     * Add data for the skipped Service step.
     *
     * @param Lib\UserBookingData $userData
     */
    private static function _setDataForSkippedServiceStep( Lib\UserBookingData $userData )
    {
        // Staff ids.
        $defaults = Lib\Session::getFormVar( self::parameter( 'form_id' ), 'defaults' );
        if ( $defaults !== null ) {
            $service_id = $defaults['service_id'];
            if ( $defaults['staff_id'] == 0 ) {
                $service = Lib\Entities\Service::find( $defaults['service_id'] );
                if ( $service && $service->withSubServices() ) {
                    $sub_services = $service->getSubServices();
                    $service_id = reset( $sub_services )->getId();
                }
                $staff_ids = Lib\Entities\StaffService::query( 'ss' )
                    ->leftJoin( 'Staff', 's', 's.id = ss.staff_id' )
                    ->where( 'ss.service_id', $service_id )
                    ->where( 's.visibility', 'public' )
                    ->fetchCol( 'ss.staff_id' );
            } else {
                $staff_ids = array( $defaults['staff_id'] );
            }
            // Date.
            $date_from = isset( $defaults['date_from'] ) && $defaults['date_from'] ? Lib\Slots\DatePoint::fromStr( $defaults['date_from'] ) : Lib\Slots\DatePoint::now()->modify( Lib\Proxy\Pro::getMinimumTimePriorBooking( $service_id ) );
            // Days and times.
            $days_times = Lib\Config::getDaysAndTimes();

            $time_from = isset( $defaults['time_from'] ) && $defaults['time_from'] ? $defaults['time_from'] : key( $days_times['times'] );
            end( $days_times['times'] );

            $userData->chain->clear();
            $chain_item = new Lib\ChainItem();
            $chain_item
                ->setNumberOfPersons( 1 )
                ->setQuantity( 1 )
                ->setUnits( isset( $defaults['units'] ) && $defaults['units'] ? $defaults['units'] : 1 )
                ->setServiceId( $defaults['service_id'] )
                ->setStaffIds( $staff_ids )
                ->setLocationId( $defaults['location_id'] ?: null );
            $userData->chain->add( $chain_item );

            $userData->fillData( array(
                'date_from' => $date_from->toClientTz()->format( 'Y-m-d' ),
                'days' => array_keys( $days_times['days'] ),
                'edit_cart_keys' => array(),
                'slots' => array(),
                'time_from' => $time_from,
                'time_to' => isset( $defaults['time_to'] ) && $defaults['time_to'] ? $defaults['time_to'] : key( $days_times['times'] ),
            ) );
        } else {
            Errors::sendSessionError();
        }
    }

    /**
     * Handle time zone parameters.
     *
     * @param Lib\UserBookingData $userData
     */
    private static function _handleTimeZone( Lib\UserBookingData $userData )
    {
        $time_zone = null;
        $time_zone_offset = null;  // in minutes

        if ( self::hasParameter( 'time_zone_offset' ) ) {
            // Browser values.
            $time_zone = self::parameter( 'time_zone' );
            $time_zone_offset = self::parameter( 'time_zone_offset' );
        } elseif ( self::hasParameter( 'time_zone' ) ) {
            // WordPress value.
            $time_zone = self::parameter( 'time_zone' );
            if ( preg_match( '/^UTC[+-]/', $time_zone ) ) {
                $offset = preg_replace( '/UTC\+?/', '', $time_zone );
                $time_zone = null;
                $time_zone_offset = -$offset * 60;
            } else {
                $time_zone_offset = -timezone_offset_get( timezone_open( $time_zone ), new \DateTime() ) / 60;
            }
        }

        if ( $time_zone !== null || $time_zone_offset !== null ) {
            // Client time zone.
            $userData
                ->setTimeZone( $time_zone )
                ->setTimeZoneOffset( $time_zone_offset )
                ->applyTimeZone();
        }
    }

    /**
     * Return suitable gateways for customer and all staff members
     *
     * @param Lib\UserBookingData $userData
     * @param Lib\CartInfo $cart_info
     * @return array
     */
    private static function getGateways( $userData, $cart_info )
    {
        $gateways = array();
        if ( Lib\Config::payLocallyEnabled() && Proxy\CustomerGroups::allowedGateway( 'local', $userData ) !== false ) {
            $gateways['local'] = array(
                'html' => self::renderTemplate( '_payment_local', array( 'form_id' => self::parameter( 'form_id' ) ), false ),
                'pay' => $cart_info->getPayNow(),
            );
        }
        if ( Proxy\CustomerGroups::allowedGateway( Lib\Entities\Payment::TYPE_CLOUD_STRIPE, $userData ) !== false ) {
            $pay_cloud_stripe = Lib\Cloud\API::getInstance()->account->productActive( Lib\Cloud\Account::PRODUCT_STRIPE ) && get_option( 'bookly_cloud_stripe_enabled' );
            if ( $pay_cloud_stripe ) {
                $cart_info->setGateway( Lib\Entities\Payment::TYPE_CLOUD_STRIPE );
                $gateways[ Lib\Entities\Payment::TYPE_CLOUD_STRIPE ] = array(
                    'html' => self::renderTemplate(
                        '_cloud_stripe_option',
                        array(
                            'form_id' => self::parameter( 'form_id' ),
                            'url_cards_image' => plugins_url( 'frontend/resources/images/payments.svg', Lib\Plugin::getMainFile() ),
                            'show_price' => Lib\Proxy\Shared::showPaymentSpecificPrices( false ),
                            'cart_info' => $cart_info,
                            'payment_status' => $userData->extractPaymentStatus( $cart_info->getGateway() ),
                        ),
                        false
                    ),
                    'pay' => $cart_info->getPayNow(),
                );
            }
        }

        $gateways = Proxy\Shared::preparePaymentOptions(
            $gateways,
            self::parameter( 'form_id' ),
            Lib\Proxy\Shared::showPaymentSpecificPrices( false ),
            $cart_info,
            $userData
        );

        return Proxy\Pro::filterGateways( $gateways, $userData );
    }

    /**
     * Override parent method to exclude actions from CSRF token verification.
     *
     * @param string $action
     * @return bool
     */
    protected static function csrfTokenValid( $action = null )
    {
        $excluded_actions = array(
            'approveAppointment',
            'cancelAppointment',
            'rejectAppointment',
            'renderService',
            'renderExtras',
            'renderTime',
        );

        return in_array( $action, $excluded_actions ) || parent::csrfTokenValid( $action );
    }
}