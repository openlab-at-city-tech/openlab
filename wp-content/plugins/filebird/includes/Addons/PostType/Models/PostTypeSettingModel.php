<?php
namespace FileBird\Addons\PostType\Models;

use FileBird\Utils\Singleton;

defined( 'ABSPATH' ) || exit;

class PostTypeSettingModel {
	use Singleton;

    private $type;
    private $user_id;

	protected $settings = array();

    /**
     * PostTypeSettingModel constructor.
     *
     * @param array $settings An array of settings.
     * @param int $user_id The user ID.
     * @param string $type The type of post.
     */
	public function __construct( array $settings, $user_id, $type ) {
		$this->settings = $settings;
        $this->user_id  = $user_id;
        $this->type     = $type;
	}

	/**
	 * Get the value of a specific setting by key.
	 *
	 * @param string $key The key of the setting to retrieve.
	 * @return mixed|null The value of the setting if it exists, null otherwise.
	 */
	public function getSetting( $key ) {
		$meta = get_user_meta( $this->user_id, $this->settings[ $key ]['meta'], true );

        if ( $meta === '0' || $meta === 0 || ! empty( $meta ) ) {
            return $meta;
        } else {
			if ( array_key_exists( $key, $this->settings ) ) {
				return $this->settings[ $key ]['default'];
			}
			return null;
        }

		return null;
	}

	/**
	 * Set a setting value for the given key.
	 *
	 * @param string $key The key of the setting to set.
	 * @param mixed $value The value to set for the given key.
	 * @return void
	 */
	public function setSetting( $key, $value ) {
		update_user_meta( $this->user_id, $this->settings[ $key ]['meta'], $value );
	}

	/**
	 * Deletes a setting from the user meta based on the given key.
	 *
	 * @param string $key The key of the setting to be deleted.
	 * @return void
	 */
	public function deleteSetting( $key ) {
		delete_user_meta( $this->user_id, $this->settings[ $key ]['meta'] );
	}

	public function getAllSettings() {
		$result = array();

		foreach ( $this->settings as $key => $setting ) {
			$result[ $key ] = $this->getSetting( $key );
		}

		return $result;
	}
}