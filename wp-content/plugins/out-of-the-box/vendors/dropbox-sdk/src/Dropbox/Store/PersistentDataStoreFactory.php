<?php
namespace TheLion\OutoftheBox\API\Dropbox\Store;

use InvalidArgumentException;
use TheLion\OutoftheBox\API\Dropbox\Exceptions\DropboxClientException;
use TheLion\OutoftheBox\API\Dropbox\Store\PersistentDataStoreInterface;

/**
 * Thanks to Facebook
 *
 * @link https://developers.facebook.com/docs/php/PersistentDataInterface
 */
class PersistentDataStoreFactory
{
    /**
     * Make Persistent Data Store
     *
     * @param null|string|\TheLion\OutoftheBox\API\Dropbox\Store\PersistentDataStoreInterface $store
     *
     * @throws InvalidArgumentException
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Store\PersistentDataStoreInterface
     */
    public static function makePersistentDataStore($store = null)
    {
        if (is_null($store) || $store === 'session') {
            return new SessionPersistentDataStore();
        }

        if ($store instanceof PersistentDataStoreInterface) {
            return $store;
        }

        throw new InvalidArgumentException('The persistent data store must be set to null, "session" or be an instance of use \TheLion\OutoftheBox\API\Dropbox\Store\PersistentDataStoreInterface');
    }
}
