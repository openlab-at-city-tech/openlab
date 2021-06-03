<?php namespace TheLion\OutoftheBox\API\Dropbox\Models;

class MetadataCollection
{

    /**
     * Collection Items Key
     *
     * @const string
     */
    public static $COLLECTION_ITEMS_KEY = array('entries', 'links', 'matches');

    /**
     * Collection Cursor Key
     *
     * @const string
     */
    public static $COLLECTION_CURSOR_KEY = array('cursor', 'start');

    /**
     * Collection has-more-items Key
     *
     * @const string
     */
    public static $COLLECTION_HAS_MORE_ITEMS_KEY = array('has_more', 'more');

    /**
     * Collection Data
     *
     * @var array
     */
    protected $data;

    /**
     * List of Files/Folder Metadata
     *
     * @var \TheLion\OutoftheBox\API\Dropbox\Models\ModelCollection
     */
    protected $items = null;

    /**
     * Cursor for pagination and updates
     *
     * @var string
     */
    protected $cursor;

    /**
     * If more items are available
     *
     * @var boolean
     */
    protected $hasMoreItems;

    /**
     * Create a new Metadata Collection
     *
     * @param array $data Collection Data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->cursor = isset($data[$this->getCollectionCursorKey()]) ? $data[$this->getCollectionCursorKey()] : '';
        $this->hasMoreItems = isset($data[$this->getCollectionHasMoreItemsKey()]) && $data[$this->getCollectionHasMoreItemsKey()] ? true : false;

        $items = isset($data[$this->getCollectionItemsKey()]) ? $data[$this->getCollectionItemsKey()] : [];
        $this->processItems($items);
    }

    /**
     * Get the Collection Items Key
     *
     * @return string
     */
    public function getCollectionItemsKey()
    {

        foreach (self::$COLLECTION_ITEMS_KEY as $collection_items_key) {
            if (isset($this->data[$collection_items_key])) {
                return $collection_items_key;
            }
        }
        return null;
    }

    /**
     * Get the Collection has-more-items Key
     *
     * @return string
     */
    public function getCollectionHasMoreItemsKey()
    {
        foreach (self::$COLLECTION_HAS_MORE_ITEMS_KEY as $collection_has_more_items_key) {
            if (isset($this->data[$collection_has_more_items_key])) {
                return $collection_has_more_items_key;
            }
        }

        return null;
    }

    /**
     * Get the Collection Cursor Key
     *
     * @return string
     */
    public function getCollectionCursorKey()
    {
        foreach (self::$COLLECTION_CURSOR_KEY as $collection_cursor_key) {
            if (isset($this->data[$collection_cursor_key])) {
                return $collection_cursor_key;
            }
        }

        return null;
    }

    /**
     * Get the Collection data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the Items
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\ModelCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Get the cursor
     *
     * @return string
     */
    public function getCursor()
    {
        return $this->cursor;
    }

    /**
     * More items are available
     *
     * @return boolean
     */
    public function hasMoreItems()
    {
        return $this->hasMoreItems;
    }

    /**
     * Process items and cast them
     * to their respective Models
     *
     * @param array $items Unprocessed Items
     *
     * @return void
     */
    protected function processItems(array $items)
    {
        $processedItems = [];

        foreach ($items as $entry) {
            $processedItems[] = ModelFactory::make($entry);
        }

        $this->items = new ModelCollection($processedItems);
    }
}
