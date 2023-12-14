<?php
namespace Bookly\Lib;

class Chain
{
    /** @var ChainItem[] */
    private $items = array();

    /**
     * Add chain item.
     *
     * @param ChainItem $item
     */
    public function add( ChainItem $item )
    {
        $this->items[] = $item;
    }

    /**
     * Drop all chain items.
     */
    public function clear()
    {
        $this->items = array();
    }

    /**
     * Get chain items.
     *
     * @return ChainItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Get items data as array.
     *
     * @return array
     */
    public function getItemsData()
    {
        $data = array();
        foreach ( $this->items as $key => $item ) {
            $data[ $key ] = $item->getData();
        }

        return $data;
    }

    /**
     * Set items data from array.
     *
     * @param array $data
     */
    public function setItemsData( array $data )
    {
        foreach ( $data as $key => $item_data ) {
            $item = new ChainItem();
            $item->setData( $item_data );
            $this->items[ $key ] = $item;
        }
    }

}