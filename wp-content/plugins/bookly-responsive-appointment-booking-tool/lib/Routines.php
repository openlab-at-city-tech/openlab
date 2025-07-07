<?php
namespace Bookly\Lib;

use Bookly\Lib\Base\Schema;
use Bookly\Lib\Cloud\Account;
use Bookly\Lib\Entities\Appointment;
use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib\Entities\Log;
use Bookly\Lib\Entities\MailingCampaign;
use Bookly\Lib\Entities\MailingListRecipient;
use Bookly\Lib\Entities\MailingQueue;
use Bookly\Lib\Entities\NotificationQueue;
use Bookly\Lib\Entities\Payment;
use Bookly\Lib\Entities\SmsLog;

abstract class Routines
{
    /**
     * Init routines.
     */
    public static function init()
    {
        // Register daily routine.
        add_action( 'bookly_daily_routine', array( __CLASS__, 'doDailyRoutine' ), 10, 0 );

        // Register hourly routine.
        add_action( 'bookly_hourly_routine', array( __CLASS__, 'doRoutine' ), 10, 0 );

        // Schedule daily routine.
        if ( ! wp_next_scheduled( 'bookly_daily_routine' ) ) {
            wp_schedule_event( current_time( 'timestamp' ), 'daily', 'bookly_daily_routine' );
        }

        // Schedule hourly routine.
        if ( ! wp_next_scheduled( 'bookly_hourly_routine' ) ) {
            wp_schedule_event( current_time( 'timestamp' ), 'hourly', 'bookly_hourly_routine' );
        }
    }

    public static function doRoutine()
    {
        $transient_name = 'bookly_do_routine';
        $lock = (int) get_transient( $transient_name );
        if ( $lock + 120 < time() ) {
            set_transient( $transient_name, time(), 120 );
            set_time_limit( 120 );
            // Email and SMS notifications routine.
            Notifications\Routine::sendNotifications();
            // Handle outdated unpaid payments
            self::handleUnpaidPayments();
            // Let add-ons do their hourly routines.
            Proxy\Shared::doHourlyRoutine();
            // Handle mailing campaigns
            self::mailing();
            // Handle expired sessions
            self::clearSessions();
            // Handle expired logs
            self::clearLogs();
        }

        self::doDailyRoutine();
    }

    public static function doDailyRoutine()
    {
        $transient_name = 'bookly_do_daily_routine';
        $lock = (int) get_transient( $transient_name );
        if ( $lock + DAY_IN_SECONDS <= time() ) {
            set_transient( $transient_name, time(), DAY_IN_SECONDS );
            // Daily info routine.
            self::handleDailyInfo();
            // Cloud routine.
            self::loadCloudInfo();
            // Statistics routine.
            self::sendDailyStatistics();
            // Calculate goal by number of customer appointments achieved
            self::calculateGoalOfCA();
            self::clearNotificationQueue();
            self::clearSmsLog();
            // Let add-ons do their daily routines.
            Proxy\Shared::doDailyRoutine();
        }
    }

    /**
     * Handle outdated payments
     */
    public static function handleUnpaidPayments()
    {
        $payments = array();
        $timeout = (int) get_option( 'bookly_cloud_stripe_timeout' );
        if ( $timeout ) {
            // Get list of outdated unpaid Cloud Stripe payments
            $payments = Payment::query()
                ->where( 'type', Payment::TYPE_CLOUD_STRIPE )
                ->where( 'status', Payment::STATUS_PENDING )
                ->whereLt( 'created_at', date_create( current_time( 'mysql' ) )->modify( sprintf( '- %s seconds', $timeout ) )->format( 'Y-m-d H:i:s' ) )
                ->fetchCol( 'id' );
        }

        $timeout = (int) get_option( 'bookly_cloud_square_timeout' );
        if ( $timeout ) {
            // Get list of outdated unpaid Cloud Square payments
            $payments = array_merge( $payments, Payment::query()
                ->where( 'type', Payment::TYPE_CLOUD_SQUARE )
                ->where( 'status', Payment::STATUS_PENDING )
                ->whereLt( 'created_at', date_create( current_time( 'mysql' ) )->modify( sprintf( '- %s seconds', $timeout ) )->format( 'Y-m-d H:i:s' ) )
                ->fetchCol( 'id' ) );
        }

        // Mark unpaid appointments as rejected.
        $payments = \Bookly\Lib\Payment\Proxy\Shared::prepareOutdatedUnpaidPayments( $payments );
        if ( ! empty( $payments ) ) {
            Proxy\Shared::unpaidPayments( $payments );
            Payment::query()
                ->update()
                ->set( 'status', Payment::STATUS_REJECTED )
                ->whereIn( 'id', $payments )
                ->execute();
            CustomerAppointment::query()
                ->update()
                ->set( 'status', CustomerAppointment::STATUS_REJECTED )
                ->set( 'status_changed_at', current_time( 'mysql' ) )
                ->whereIn( 'payment_id', $payments )
                ->execute();
            $affected_appointments = Appointment::query( 'a' )
                ->leftJoin( 'CustomerAppointment', 'ca', 'a.id = ca.appointment_id' )
                ->whereNot( 'a.start_date', null )
                ->whereIn( 'ca.payment_id', $payments )
                ->fetchCol( 'a.id' );
            // Reject recurring appointments when customer pay only for first one.
            $series = CustomerAppointment::query()
                ->whereIn( 'payment_id', $payments )
                ->whereNot( 'series_id', null )
                ->fetchCol( 'series_id' );
            if ( ! empty( $series ) ) {
                CustomerAppointment::query()
                    ->update()
                    ->set( 'status', CustomerAppointment::STATUS_REJECTED )
                    ->set( 'status_changed_at', current_time( 'mysql' ) )
                    ->whereIn( 'series_id', $series )
                    ->execute();
                $affected_appointments = array_merge( $affected_appointments, Appointment::query( 'a' )
                    ->leftJoin( 'CustomerAppointment', 'ca', 'a.id = ca.appointment_id' )
                    ->whereNot( 'a.start_date', null )
                    ->whereIn( 'ca.series_id', $series )
                    ->fetchCol( 'a.id' ) );
            }

            /** @var Appointment $appointment */
            foreach ( array_unique( $affected_appointments ) as $appointment_id ) {
                $appointment = Appointment::find( $appointment_id );
                Proxy\Shared::syncOnlineMeeting( array(), $appointment, Entities\Service::find( $appointment->getServiceId() ) );
                Utils\Common::syncWithCalendars( $appointment );
            }
        }
    }

    /**
     * Daily info routine.
     */
    public static function handleDailyInfo()
    {
        $date = Entities\News::query( 'n' )
            ->select( 'MAX(updated_at) AS max_date' )
            ->fetchRow();

        $data = API::getInfo( $date['max_date'] );

        if ( is_array( $data ) ) {
            if ( isset ( $data['plugins'] ) ) {
                $seen = Entities\Shop::query()->count() ? 0 : 1;
                foreach ( $data['plugins'] as $plugin ) {
                    $shop = new Entities\Shop();
                    if ( $plugin['id'] && $plugin['envatoPrice'] ) {
                        $shop->loadBy( array( 'plugin_id' => $plugin['id'] ) );
                        $shop
                            ->setPluginId( $plugin['id'] )
                            ->setType( $plugin['type'] ? 'bundle' : 'plugin' )
                            ->setHighlighted( $plugin['highlighted'] ?: 0 )
                            ->setPriority( $plugin['priority'] ?: 0 )
                            ->setDemoUrl( $plugin['demoUrl'] )
                            ->setTitle( $plugin['title'] )
                            ->setSlug( $plugin['slug'] )
                            ->setDescription( $plugin['envatoDescription'] )
                            ->setUrl( $plugin['envatoUrl'] )
                            ->setIcon( $plugin['envatoIcon'] )
                            ->setImage( $plugin['envatoImage'] )
                            ->setPrice( $plugin['envatoPrice'] )
                            ->setSales( $plugin['envatoSales'] )
                            ->setRating( $plugin['envatoRating'] )
                            ->setReviews( $plugin['envatoReviews'] )
                            ->setPublished( isset ( $plugin['envatoPublishedAt']['date'] )
                                ? date_create( $plugin['envatoPublishedAt']['date'] )->format( 'Y-m-d H:i:s' )
                                : current_time( 'mysql' )
                            )
                            ->setCreatedAt( current_time( 'mysql' ) )
                            ->setSeen( $shop->isLoaded() ? $shop->getSeen() : $seen )
                            ->save();
                    }
                }
            }

            if ( isset( $data['news'] ) ) {
                foreach ( $data['news'] as $incoming_news ) {
                    $news = new Entities\News();
                    $news->loadBy( array( 'news_id' => $incoming_news['id'] ) );
                    if ( $news->isLoaded() && ! $incoming_news['published'] ) {
                        $news->delete();
                    } else {
                        $news
                            ->setNewsId( $incoming_news['id'] )
                            ->setTitle( $incoming_news['title'] )
                            ->setText( $incoming_news['text'] )
                            ->setMediaType( $incoming_news['mediaType'] )
                            ->setMediaUrl( $incoming_news['mediaUrl'] )
                            ->setButtonText( $incoming_news['buttonText'] )
                            ->setButtonUrl( $incoming_news['buttonUrl'] )
                            ->setSeen( $news->isLoaded() ? $news->getSeen() : 0 )
                            ->setCreatedAt( $incoming_news['createdAt'] )
                            ->setUpdatedAt( $incoming_news['updatedAt'] )
                            ->save();
                    }
                }
            }
        }
    }

    /**
     * Load Bookly Cloud products, promotions, etc.
     */
    public static function loadCloudInfo()
    {
        Cloud\API::getInstance()->general->loadInfo();
    }

    /**
     * Statistics routine.
     */
    public static function sendDailyStatistics()
    {
        if ( get_option( 'bookly_gen_collect_stats' ) ) {
            API::sendStats();
        }
    }

    public static function calculateGoalOfCA()
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        $ca_count = get_option( 'bookly_сa_count' );
        $log10 = (int) log10( Entities\CustomerAppointment::query()->count() );
        $current = $log10 > 0 ? pow( 10, $log10 ) : 0;

        if ( $ca_count != $current ) {
            // New goal by number of customer appointments achieved,
            // corresponding hide until values are reset to show call to rate Bookly on WP
            $wpdb->query( $wpdb->prepare(
                'UPDATE `' . $wpdb->usermeta . '` SET `meta_value` = %d WHERE `meta_key` = \'bookly_notice_rate_on_wp_hide_until\' AND meta_value != 0',
                time()
            ) );

            update_option( 'bookly_сa_count', $current );
        }
    }

    protected static function clearNotificationQueue()
    {
        $list = NotificationQueue::query()->whereRaw( 'created_at < DATE(NOW() - INTERVAL 2 DAY)', array() )->find();
        foreach ( $list as $notification ) {
            $notification->delete();
        }
    }

    protected static function clearSmsLog()
    {
        SmsLog::query()->delete()
            ->whereRaw( 'created_at < DATE(NOW() - INTERVAL 1 MONTH)', array() )
            ->execute();
    }

    public static function mailing()
    {
        $cloud = Cloud\API::getInstance();
        if ( $cloud->getToken() ) {
            global $wpdb;
            /** @var MailingCampaign $mc */
            while ( $mc = MailingCampaign::query()
                ->where( 'state', MailingCampaign::STATE_PENDING )
                ->whereLte( 'send_at', current_time( 'mysql' ) )
                ->findOne() ) {
                $mc->setState( MailingCampaign::STATE_IN_PROGRESS )->save();
                $query = 'INSERT INTO `' . MailingQueue::getTableName() . '` (phone, name, text, sent, campaign_id, created_at)
                          SELECT mlr.phone, mlr.name, %s, 0, %d, %s
                            FROM `' . MailingListRecipient::getTableName() . '` AS mlr
                           WHERE mlr.mailing_list_id = %s';
                $wpdb->query( $wpdb->prepare( $query, $mc->getText(), $mc->getId(), current_time( 'mysql' ), $mc->getMailingListId() ) );
                if ( Schema::getAffectedRows() == 0 ) {
                    $mc->setState( MailingCampaign::STATE_COMPLETED )->save();
                }
            }

            /** @var MailingQueue[] $sms_items */
            $query = MailingQueue::query()->where( 'sent', '0' )->sortBy( 'campaign_id' )->limit( 2 );
            $sms_sender = $cloud->getProduct( Account::PRODUCT_SMS_NOTIFICATIONS );
            while ( $sms_items = $query->find() ) {
                $sms = $sms_items[0];
                $sms->setSent( 1 )->save();

                $codes = new Notifications\Assets\Mailing\Codes( $sms );
                $message = $codes->replaceForSms( $sms->getText() );
                $sms_sender->sendSms( $sms->getPhone(), $message['personal'], $message['impersonal'], 60 );
                if ( count( $sms_items ) === 1 || $sms_items[0]->getCampaignId() != $sms_items[1]->getCampaignId() ) {
                    MailingCampaign::query()
                        ->update()
                        ->set( 'state', MailingCampaign::STATE_COMPLETED )
                        ->where( 'id', $sms_items[0]->getCampaignId() )
                        ->execute();
                }
            }
        }
    }

    public static function clearSessions()
    {
        Entities\Session::query()->delete()->whereLt( 'expire', current_time( 'mysql' ) )->execute();
    }

    public static function clearLogs()
    {
        Log::query()->delete()
            ->whereRaw( 'created_at < DATE(NOW() - INTERVAL %s DAY)', array( get_option( 'bookly_logs_expire', 30 ) ) )
            ->execute();

        foreach ( Utils\Log::getTypes() as $type ) {
            $option_value = get_option( $type );
            if ( $option_value && ( date_create( $option_value ) < date_create() ) ) {
                update_option( $type, '0' );
            }
        }
    }
}