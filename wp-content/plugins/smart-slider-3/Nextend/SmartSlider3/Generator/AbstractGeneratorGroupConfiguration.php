<?php


namespace Nextend\SmartSlider3\Generator;


use Nextend\Framework\Pattern\MVCHelperTrait;

abstract class AbstractGeneratorGroupConfiguration {

    const CSRF_LENGTH = 32;

    /** @var AbstractGeneratorGroup */
    protected $generatorGroup;

    /**
     * AbstractGeneratorGroupConfiguration constructor.
     *
     * @param AbstractGeneratorGroup $generatorGroup
     */
    public function __construct($generatorGroup) {

        $this->generatorGroup = $generatorGroup;
    }

    /**
     * @return bool
     */
    public abstract function wellConfigured();

    /**
     * @return array
     */
    public abstract function getData();

    /**
     * @param      $data
     * @param bool $store
     */
    public abstract function addData($data, $store = true);

    /**
     * @param MVCHelperTrait $MVCHelper
     */
    public abstract function render($MVCHelper);

    /**
     * @param MVCHelperTrait $MVCHelper
     */
    public abstract function startAuth($MVCHelper);

    /**
     * @param MVCHelperTrait $MVCHelper
     */
    public abstract function finishAuth($MVCHelper);

    protected function generateRandomState() {

        if (function_exists('random_bytes')) {
            return $this->bytesToString(random_bytes(self::CSRF_LENGTH));
        }

        if (function_exists('mcrypt_create_iv')) {
            /** @noinspection PhpDeprecationInspection */
            $binaryString = mcrypt_create_iv(self::CSRF_LENGTH, MCRYPT_DEV_URANDOM);

            if ($binaryString !== false) {
                return $this->bytesToString($binaryString);
            }
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            $wasCryptographicallyStrong = false;

            $binaryString = openssl_random_pseudo_bytes(self::CSRF_LENGTH, $wasCryptographicallyStrong);

            if ($binaryString !== false && $wasCryptographicallyStrong === true) {
                return $this->bytesToString($binaryString);
            }
        }

        return $this->randomStr(self::CSRF_LENGTH);
    }

    private function bytesToString($binaryString) {
        return substr(bin2hex($binaryString), 0, self::CSRF_LENGTH);
    }

    private function randomStr($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        $str = '';
        $max = strlen($keyspace) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }

        return $str;
    }
}