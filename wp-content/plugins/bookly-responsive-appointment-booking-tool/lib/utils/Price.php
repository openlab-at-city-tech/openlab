<?php
namespace Bookly\Lib\Utils;

use Bookly\Lib\Config;

/**
 * Class Price
 * @package Bookly\Lib\Utils
 */
abstract class Price
{
    /** @var array */
    private static $currencies = array(
        'AED' => array( 'symbol' => 'AED', 'format' => '{price|2} {symbol}' ),
        'ALL' => array( 'symbol' => 'Lek', 'format' => '{price} {symbol}' ),
        'AMD' => array( 'symbol' => 'դր.', 'format' => '{price|2} {symbol}' ),
        'ANG' => array( 'symbol' => 'ƒ', 'format' => '{symbol}{price|2}' ),
        'AOA' => array( 'symbol' => 'Kz', 'format' => '{symbol} {price|2}' ),
        'ARS' => array( 'symbol' => '$', 'format' => '{symbol}{price|2}' ),
        'AUD' => array( 'symbol' => 'A$', 'format' => '{symbol}{price|2}' ),
        'BAM' => array( 'symbol' => 'KM', 'format' => '{price|2} {symbol}' ),
        'BDT' => array( 'symbol' => '৳', 'format' => '{symbol}{price|2}' ),
        'BGN' => array( 'symbol' => 'лв.', 'format' => '{price|2} {symbol}' ),
        'BHD' => array( 'symbol' => 'BHD', 'format' => '{symbol} {price|2}' ),
        'BRL' => array( 'symbol' => 'R$', 'format' => '{symbol} {price|2}' ),
        'BWP' => array( 'symbol' => 'P', 'format' => '{symbol}{price|2}' ),
        'CAD' => array( 'symbol' => 'C$', 'format' => '{symbol}{price|2}' ),
        'CHF' => array( 'symbol' => 'CHF', 'format' => '{price|2} {symbol}' ),
        'CLF' => array( 'symbol' => 'UF', 'format' => '{symbol}{price|0}' ),
        'CLP' => array( 'symbol' => '$', 'format' => '{symbol}{price|2}' ),
        'CNY' => array( 'symbol' => '¥', 'format' => '{price|2} {symbol}' ),
        'COP' => array( 'symbol' => '$', 'format' => '{symbol}{price|0}' ),
        'CRC' => array( 'symbol' => '₡', 'format' => '{symbol}{price|2}' ),
        'CUC' => array( 'symbol' => 'CUC$', 'format' => '{price|2} {symbol}' ),
        'CZK' => array( 'symbol' => 'Kč', 'format' => '{price|2} {symbol}' ),
        'DKK' => array( 'symbol' => 'kr', 'format' => '{price|2} {symbol}' ),
        'DOP' => array( 'symbol' => 'RD$', 'format' => '{symbol}{price|2}' ),
        'DZD' => array( 'symbol' => 'DA', 'format' => '{price|2} {symbol}' ),
        'EGP' => array( 'symbol' => 'EGP', 'format' => '{symbol} {price|2}' ),
        'EUR' => array( 'symbol' => '€', 'format' => '{symbol}{price|2}' ),
        'FJD' => array( 'symbol' => 'F$', 'format' => '{symbol}{price|2}' ),
        'GBP' => array( 'symbol' => '£', 'format' => '{symbol}{price|2}' ),
        'GEL' => array( 'symbol' => '₾', 'format' => '{price|2} {symbol}' ),
        'GHS' => array( 'symbol' => 'GH¢', 'format' => '{symbol} {price|2}' ),
        'GTQ' => array( 'symbol' => 'Q', 'format' => '{symbol}{price|2}' ),
        'HKD' => array( 'symbol' => 'HK$', 'format' => '{symbol}{price|2}' ),
        'HRK' => array( 'symbol' => 'kn', 'format' => '{price|2} {symbol}' ),
        'HUF' => array( 'symbol' => 'Ft', 'format' => '{price|2} {symbol}' ),
        'IDR' => array( 'symbol' => 'Rp', 'format' => '{price|2} {symbol}' ),
        'ILS' => array( 'symbol' => '₪', 'format' => '{price|2} {symbol}' ),
        'INR' => array( 'symbol' => '₹', 'format' => '{price|2} {symbol}' ),
        'IRR' => array( 'symbol' => '﷼', 'format' => '{price} {symbol}' ),
        'ISK' => array( 'symbol' => 'kr', 'format' => '{price|0} {symbol}' ),
        'JMD' => array( 'symbol' => '$', 'format' => '{symbol}{price}' ),
        'JOD' => array( 'symbol' => 'JD', 'format' => '{symbol}{price|2}' ),
        'JPY' => array( 'symbol' => '¥', 'format' => '{symbol}{price|3}' ),
        'KES' => array( 'symbol' => 'KSh', 'format' => '{symbol} {price|2}' ),
        'KRW' => array( 'symbol' => '₩', 'format' => '{price|2} {symbol}' ),
        'KWD' => array( 'symbol' => 'KD', 'format' => '{price|2} {symbol}' ),
        'KZT' => array( 'symbol' => 'тг.', 'format' => '{price|2} {symbol}' ),
        'LAK' => array( 'symbol' => '₭', 'format' => '{price|0} {symbol}' ),
        'LBP' => array( 'symbol' => 'ل.ل.', 'format' => '{symbol} {price}' ),
        'LKR' => array( 'symbol' => 'Rs.', 'format' => '{symbol} {price|2}' ),
        'MAD' => array( 'symbol' => 'Dh', 'format' => '{price|2} {symbol}' ),
        'MDL' => array( 'symbol' => 'L', 'format' => '{price|2} {symbol}' ),
        'MKD' => array( 'symbol' => 'ден.', 'format' => '{price|2} {symbol}' ),
        'MUR' => array( 'symbol' => 'Rs', 'format' => '{symbol}{price|2}' ),
        'MXN' => array( 'symbol' => '$', 'format' => '{symbol}{price|2}' ),
        'MYR' => array( 'symbol' => 'RM', 'format' => '{price|2} {symbol}' ),
        'MZN' => array( 'symbol' => 'MT', 'format' => '{price|2} {symbol}' ),
        'NAD' => array( 'symbol' => 'N$', 'format' => '{symbol}{price|2}' ),
        'NGN' => array( 'symbol' => '₦', 'format' => '{symbol}{price|2}' ),
        'NOK' => array( 'symbol' => 'Kr', 'format' => '{symbol} {price|2}' ),
        'NZD' => array( 'symbol' => '$', 'format' => '{symbol}{price|2}' ),
        'OMR' => array( 'symbol' => 'OMR', 'format' => '{price|3} {symbol}' ),
        'PEN' => array( 'symbol' => 'S/.', 'format' => '{symbol}{price|2}' ),
        'PHP' => array( 'symbol' => '₱', 'format' => '{price|2} {symbol}' ),
        'PKR' => array( 'symbol' => 'Rs.', 'format' => '{symbol} {price|0}' ),
        'PLN' => array( 'symbol' => 'zł', 'format' => '{price|2} {symbol}' ),
        'PYG' => array( 'symbol' => '₲', 'format' => '{symbol}{price|2}' ),
        'QAR' => array( 'symbol' => 'QAR', 'format' => '{price|2} {symbol}' ),
        'RON' => array( 'symbol' => 'lei', 'format' => '{price|2} {symbol}' ),
        'RSD' => array( 'symbol' => 'din.', 'format' => '{symbol}{price|0}' ),
        'RUB' => array( 'symbol' => 'руб.', 'format' => '{price|2} {symbol}' ),
        'SAR' => array( 'symbol' => 'SAR', 'format' => '{price|2} {symbol}' ),
        'SCR' => array( 'symbol' => '₨', 'format' => '{symbol} {price|2}' ),
        'SEK' => array( 'symbol' => 'kr', 'format' => '{price|2} {symbol}' ),
        'SGD' => array( 'symbol' => '$', 'format' => '{symbol}{price|2}' ),
        'THB' => array( 'symbol' => '฿', 'format' => '{price|2} {symbol}' ),
        'TND' => array( 'symbol' => 'TD', 'format' => '{price|2} {symbol}' ),
        'TRY' => array( 'symbol' => 'TL', 'format' => '{price|2} {symbol}' ),
        'TTD' => array( 'symbol' => '$', 'format' => '{symbol}{price|2}' ),
        'TWD' => array( 'symbol' => 'NT$', 'format' => '{price|2} {symbol}' ),
        'TZS' => array( 'symbol' => 'TSh', 'format' => '{price} {symbol}' ),
        'UAH' => array( 'symbol' => '₴', 'format' => '{price|2} {symbol}' ),
        'UGX' => array( 'symbol' => 'UGX', 'format' => '{symbol} {price|0}' ),
        'USD' => array( 'symbol' => '$', 'format' => '{symbol}{price|2}' ),
        'VND' => array( 'symbol' => 'VNĐ', 'format' => '{price|0} {symbol}' ),
        'XAF' => array( 'symbol' => 'FCFA', 'format' => '{price|0} {symbol}' ),
        'XOF' => array( 'symbol' => 'CFA', 'format' => '{symbol} {price|2}' ),
        'XPF' => array( 'symbol' => 'FCFP', 'format' => '{price|0} {symbol}' ),
        'ZAR' => array( 'symbol' => 'R', 'format' => '{symbol} {price|2}' ),
        'ZMW' => array( 'symbol' => 'K', 'format' => '{symbol}{price|2}' ),
    );

    /** @var array */
    private static $formats = array(
        '{sign}{symbol}{price|2}',
        '{symbol}{sign}{price|2}',
        '{sign}{symbol}{price|1}',
        '{symbol}{sign}{price|1}',
        '{sign}{symbol}{price|0}',
        '{symbol}{sign}{price|0}',
        '{sign}{symbol} {price|2}',
        '{symbol} {sign}{price|2}',
        '{sign}{symbol} {price|1}',
        '{symbol} {sign}{price|1}',
        '{sign}{symbol} {price|0}',
        '{symbol} {sign}{price|0}',
        '{sign}{symbol}{price|3}',
        '{symbol}{sign}{price|3}',
        '{sign}{symbol} {price|3}',
        '{symbol} {sign}{price|3}',
        '{sign}{price|2}{symbol}',
        '{sign}{price|1}{symbol}',
        '{sign}{price|0}{symbol}',
        '{sign}{price|3} {symbol}',
        '{sign}{price|2} {symbol}',
        '{sign}{price|1} {symbol}',
        '{sign}{price|0} {symbol}',
    );

    /**
     * Format price.
     *
     * @param float $price
     * @return string
     */
    public static function format( $price )
    {
        $price = (float) $price;
        $currency = Config::getCurrency();
        $format = get_option( 'bookly_pmt_price_format' );
        $symbol = self::$currencies[ $currency ]['symbol'];

        if ( preg_match( '/{price\|(\d)}/', $format, $match ) ) {
            return strtr( $format, array(
                '{sign}' => $price < 0 ? '-' : '',
                '{symbol}' => $symbol,
                "{price|{$match[1]}}" => html_entity_decode( number_format_i18n( abs( $price ), $match[1] ) )
            ) );
        }

        return html_entity_decode( number_format_i18n( $price, 2 ) );
    }

    /**
     * Format price options.
     *
     * @return array
     */
    public static function formatOptions()
    {
        global $wp_locale;

        $format = get_option( 'bookly_pmt_price_format' );
        preg_match( '/{price\|(\d)}/', $format, $match );
        $format = strtr( $format, array( $match[0] => '{price}', '{symbol}' => self::$currencies[ Config::getCurrency() ]['symbol'] ) );
        $decimals = $match[1];
        if ( $wp_locale ) {
            $decimal_separator = $wp_locale->number_format['decimal_point'];
            $thousands_separator = $wp_locale->number_format['thousands_sep'];
        } else {
            $decimal_separator = '.';
            $thousands_separator = ',';
        }

        return compact( 'format', 'decimals', 'decimal_separator', 'thousands_separator' );
    }

    /**
     * Get supported currencies.
     *
     * @return array
     */
    public static function getCurrencies()
    {
        return self::$currencies;
    }

    /**
     * Get supported price formats.
     *
     * @return array
     */
    public static function getFormats()
    {
        return self::$formats;
    }

    /**
     * @param double $price
     * @param double $discount
     * @param double $deduction
     * @return float|int
     */
    public static function correction( $price, $discount, $deduction )
    {
        $price = (float) $price;
        $discount = (float) $discount;
        $deduction = (float) $deduction;
        $amount = round( $price * ( 100 - $discount ) / 100 - $deduction, 2 );

        return max( $amount, 0 );
    }

    /**
     * @param float $price
     * @param string $gateway
     * @return float
     */
    public static function gatewayPrice( $price, $gateway )
    {
        $increase = (float) get_option( 'bookly_' . $gateway . '_increase' );
        $addition = (float) get_option( 'bookly_' . $gateway . '_addition' );
        return self::correction( $price, -$increase, -$addition );
    }
}