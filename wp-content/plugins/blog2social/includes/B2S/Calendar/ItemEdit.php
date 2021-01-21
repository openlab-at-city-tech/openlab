<?php
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Ship/Item.php');

class B2S_Calendar_ItemEdit extends B2S_Ship_Item {

    private $calenderItem = null;
    private $b2s_id       = null;

    public function setB2SId($value)
    {
        if(is_numeric($value))
        {
            $this->b2s_id = $value;
        }

        return $this;
    }


    private function getCalendarItem()
    {
        if(is_null($this->calenderItem))
        {
            if($this->b2s_id)
            {
                $this->calenderItem = B2S_Calendar_Filter::getById($this->b2s_id);
                return $this->calenderItem;
            }

            $filter = B2S_Calendar_Filter::getByPostId($this->getPostId());
            $items = $filter->getItems();

            if(count($filter->getItems()) > 0) {
                $this->calenderItem = $items[0];
            }
        }

        return $this->calenderItem;
    }

    protected function hook_message($message)
    {
        if($this->getCalendarItem())
        {
            $sched_data = $this->getCalendarItem()->getSchedData();
            if($sched_data)
            {
                return $sched_data['content'];
            }
        }

        return $message;
    }
    
     protected function hook_sched_data(array $schedData)
    {
        if($this->getCalendarItem())
        {
            $schedData = $this->getCalendarItem()->getSchedData();
          
        }

        return $schedData;
    }
    

    protected function hook_meta(array $meta)
    {
        if($this->getCalendarItem())
        {
            $meta['image'] = $this->getCalendarItem()->getImageUrl();
        }

        return $meta;
    }
}