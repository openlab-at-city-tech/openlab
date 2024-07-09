<?php
namespace Bookly\Lib\Utils;

use Bookly\Lib\Config;
use Bookly\Lib\Proxy;

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

abstract class Mail
{
    /**
     * @param string|string[] $to Array or comma-separated list of email addresses to send message.
     * @param string $subject Email subject.
     * @param string $message Message contents.
     * @param array $headers Optional. Additional headers.
     * @param string|string[] $attachments Optional. Paths to files to attach.
     * @return bool Whether the email was sent successfully.
     */
    public static function send( $to, $subject, $message, $headers = array(), $attachments = array(), $type = '-1' )
    {
        $headers = array_merge( array(
            'is_html' => Config::sendEmailAsHtml(),
            'from' => array(
                'email' => get_option( 'bookly_email_sender' ),
                'name' => get_option( 'bookly_email_sender_name' ),
            ),
        ), $headers );

        Proxy\Pro::logEmail( $to, $subject, $message, $headers, $attachments, $type );

        switch ( get_option( 'bookly_email_gateway', 'wp' ) ) {
            case 'smtp':
                return self::sendSmtp(
                    $to,
                    $subject,
                    $message,
                    $headers,
                    $attachments,
                    get_option( 'bookly_smtp_host' ),
                    get_option( 'bookly_smtp_port' ),
                    get_option( 'bookly_smtp_user' ),
                    get_option( 'bookly_smtp_password' ),
                    get_option( 'bookly_smtp_secure' )
                );
            default:
                $wp_headers = array();

                $wp_headers[] = sprintf( 'Content-Type: %s; charset=utf-8', $headers['is_html'] ? 'text/html' : 'text/plain' );
                $wp_headers[] = sprintf( 'From: %s <%s>', $headers['from']['name'], $headers['from']['email'] );
                if ( isset ( $headers['reply_to'] ) ) {
                    $wp_headers[] = sprintf( 'Reply-To: %s <%s>', $headers['reply_to']['name'], $headers['reply_to']['email'] );
                }

                return wp_mail( $to, $subject, $message, $wp_headers, $attachments );
        }
    }

    /**
     * @param $to
     * @param $subject
     * @param $message
     * @param $headers
     * @param $attachments
     * @param $host
     * @param $port
     * @param $user
     * @param $password
     * @param $secure
     * @param $debug
     * @return bool
     */
    public static function sendSmtp( $to, $subject, $message, $headers, $attachments, $host, $port, $user, $password, $secure, $debug = 0 )
    {
        try {
            require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
            require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
            require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
        } catch ( \Exception $e ) {
            require_once ABSPATH . WPINC . '/class-phpmailer.php';
            require_once ABSPATH . WPINC . '/class-smtp.php';
        }

        try {
            $phpmailer = new PHPMailer();
            $phpmailer->isSMTP();
            $phpmailer->CharSet = "UTF-8";
            $phpmailer->SMTPAuth = true;

            $phpmailer->Host = $host;
            $phpmailer->Username = $user;
            $phpmailer->Password = $password;
            $phpmailer->SMTPSecure = $secure;
            $phpmailer->Port = $port;
            $phpmailer->SMTPDebug = $debug;

            $phpmailer->setFrom( $headers['from']['email'], isset( $headers['from']['name'] ) ? $headers['from']['name'] : '' );
            $phpmailer->isHTML( $headers['is_html'] );

            if ( isset( $headers['reply_to'] ) ) {
                $phpmailer->addReplyTo( $headers['reply_to']['email'], isset( $headers['reply_to']['name'] ) ? $headers['reply_to']['name'] : '' );
            }

            // To
            if ( ! is_array( $to ) ) {
                $to = explode( ',', $to );
            }

            foreach ( $to as $address ) {
                $phpmailer->addAddress( $address );
            }

            // Attachments
            if ( ! empty( $attachments ) ) {
                foreach ( $attachments as $attachment ) {
                    try {
                        $phpmailer->addAttachment( $attachment );
                    } catch ( PHPMailerException $e ) {
                        continue;
                    }
                }
            }

            $phpmailer->Subject = $subject;
            $phpmailer->Body = $message;

            return $phpmailer->send();
        } catch ( PHPMailerException $e ) {
            return false;
        } catch ( \Exception $e ) {
            return false;
        }
    }
}