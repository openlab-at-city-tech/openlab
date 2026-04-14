<?php

/**
 * Class responsible for encrypting and decrypting data.
 *
 * @since 2.9.4/5.12.4
 * @access private
 * @ignore
 */
class SB_Instagram_Data_Encryption
{
	/**
	 * Key to use for encryption.
	 *
	 * @since 2.9.4/5.12.4
	 * @var string
	 */
	private $key;

	/**
	 * Salt to use for encryption.
	 *
	 * @since 2.9.4/5.12.4
	 * @var string
	 */
	private $salt;

	/**
	 * Constructor.
	 *
	 * @since 2.9.4/5.12.4
	 */
	public function __construct($remote = array())
	{
		if (!empty($remote)) {
			$this->key = $remote['key'];
			$this->salt = $remote['salt'];
		} else {
			$this->key = $this->get_default_key();
			$this->salt = $this->get_default_salt();
		}
	}

	/**
	 * Gets the default encryption key to use.
	 *
	 * @return string Default (not user-based) encryption key.
	 * @since 2.9.4/5.12.4
	 */
	private function get_default_key()
	{
		if (defined('SBI_ENCRYPTION_KEY') && '' !== SBI_ENCRYPTION_KEY) {
			return SBI_ENCRYPTION_KEY;
		}

		if (defined('LOGGED_IN_KEY') && '' !== LOGGED_IN_KEY) {
			return LOGGED_IN_KEY;
		}

		// If this is reached, you're either not on a live site or have a serious security issue.
		return 'das-ist-kein-geheimer-schluessel';
	}

	/**
	 * Gets the default encryption salt to use.
	 *
	 * @return string Encryption salt.
	 * @since 2.9.4/5.12.4
	 */
	private function get_default_salt()
	{
		if (defined('SBI_ENCRYPTION_SALT') && '' !== SBI_ENCRYPTION_SALT) {
			return SBI_ENCRYPTION_SALT;
		}

		if (defined('LOGGED_IN_SALT') && '' !== LOGGED_IN_SALT) {
			return LOGGED_IN_SALT;
		}

		// If this is reached, you're either not on a live site or have a serious security issue.
		return 'das-ist-kein-geheimes-salz';
	}

	/**
	 * Encrypts a value that may already be encrypted.
	 *
	 * If a user-based key is set, that key is used. Otherwise the default key is used.
	 *
	 * @param string $raw_value Value to encrypt.
	 * @return string|bool encrypted value, or false on failure.
	 * @since 6.0
	 */
	public function maybe_encrypt($raw_value)
	{
		$maybe_decrypted = $this->decrypt($raw_value);

		if ($maybe_decrypted) {
			return $this->encrypt($maybe_decrypted);
		}

		return $this->encrypt($raw_value);
	}

	/**
	 * Decrypts a value.
	 *
	 * If a user-based key is set, that key is used. Otherwise the default key is used.
	 *
	 * @param string $raw_value Value to decrypt.
	 * @return string|bool Decrypted value, or false on failure.
	 * @since 2.9.4/5.12.4
	 */
	public function decrypt($raw_value)
	{
		if (!sbi_doing_openssl()) {
			return $raw_value;
		}

		$raw_value = base64_decode($raw_value, true);

		$method = 'aes-256-ctr';
		$ivlen = openssl_cipher_iv_length($method);
		$iv = substr($raw_value, 0, $ivlen);

		$raw_value = substr($raw_value, $ivlen);

		$value = openssl_decrypt($raw_value, $method, $this->key, 0, $iv);
		if (!$value || substr($value, -strlen($this->salt)) !== $this->salt) {
			return false;
		}

		return substr($value, 0, -strlen($this->salt));
	}

	/**
	 * Encrypts a value.
	 *
	 * If a user-based key is set, that key is used. Otherwise the default key is used.
	 *
	 * @param string $value Value to encrypt.
	 * @return string|bool Encrypted value, or false on failure.
	 * @since 2.9.4/5.12.4
	 */
	public function encrypt($value)
	{
		if (!sbi_doing_openssl()) {
			return $value;
		}

		$method = 'aes-256-ctr';
		$ivlen = openssl_cipher_iv_length($method);
		$iv = openssl_random_pseudo_bytes($ivlen);

		$raw_value = openssl_encrypt($value . $this->salt, $method, $this->key, 0, $iv);
		if (!$raw_value) {
			return false;
		}

		return base64_encode($iv . $raw_value);
	}

	/**
	 * Uses a raw value and attempts to decrypt it
	 *
	 * @param $value
	 * @return bool|string
	 * @since 6.0.8
	 */
	public function maybe_decrypt($value)
	{
		if (!is_string($value)) {
			return $value;
		}
		if (strpos($value, '{') === 0) {
			return $value;
		}

		$decrypted = $this->decrypt($value);

		if (!$decrypted) {
			return $value;
		}

		return $decrypted;
	}
}
