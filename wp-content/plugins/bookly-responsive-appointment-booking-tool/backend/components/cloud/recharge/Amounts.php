<?php
namespace Bookly\Backend\Components\Cloud\Recharge;

use Bookly\Lib\Base;
use Bookly\Lib\Cloud;

class Amounts extends Base\Cache
{
    const RECHARGE_TYPE_MANUAL = 'manual';
    const RECHARGE_TYPE_AUTO   = 'auto';

    /** @var array */
    private $items;

    /**
     * @return Amounts
     */
    public static function getInstance()
    {
        if ( ! self::hasInCache( __FUNCTION__ ) ) {
            self::putInCache( __FUNCTION__, new static() );
        }

        return self::getFromCache( __FUNCTION__ );
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $cloud = Cloud\API::getInstance();
        $recharge = $cloud->account->getRechargeData();
        if ( empty ( $recharge ) ) {
            $cloud->account->loadProfile();
            $recharge = $cloud->account->getRechargeData();
        }
        $this->items = $recharge['amounts'];
    }

    /**
     * Get items
     *
     * @param string $type
     * @return array
     */
    public function getItems( $type )
    {
        return $this->items[ $type ];
    }

    /**
     * Get item with specific tag
     *
     * @param string $tag
     * @param string $type
     * @return int|null
     */
    public function getTaggedItem( $tag, $type )
    {
        $items = $this->getItems( $type );

        foreach ( $items as $item ) {
            if ( in_array( $tag, $item['tags'] ) ) {
                return $item;
            }
        }

        return null;
    }
}