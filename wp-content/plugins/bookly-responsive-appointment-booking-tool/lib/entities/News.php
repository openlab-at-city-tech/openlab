<?php
namespace Bookly\Lib\Entities;

use Bookly\Lib;

/**
 * Class News
 *
 * @package Bookly\Lib\Entities
 */
class News extends Lib\Base\Entity
{
    /** @var  int */
    protected $news_id;
    /** @var  string */
    protected $title;
    /** @var  string */
    protected $media_type;
    /** @var  string */
    protected $media_url;
    /** @var  string */
    protected $text;
    /** @var  string */
    protected $button_url;
    /** @var  string */
    protected $button_text;
    /** @var  int */
    protected $seen = 0;
    /** @var  string */
    protected $updated_at;
    /** @var  string */
    protected $created_at;

    protected static $table = 'bookly_news';

    protected static $schema = array(
        'id' => array( 'format' => '%d' ),
        'news_id' => array( 'format' => '%d' ),
        'title' => array( 'format' => '%s' ),
        'media_type' => array( 'format' => '%s' ),
        'media_url' => array( 'format' => '%s' ),
        'text' => array( 'format' => '%s' ),
        'button_url' => array( 'format' => '%s' ),
        'button_text' => array( 'format' => '%s' ),
        'seen' => array( 'format' => '%d' ),
        'updated_at' => array( 'format' => '%s' ),
        'created_at' => array( 'format' => '%s' ),
    );

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * @return int
     */
    public function getNewsId()
    {
        return $this->news_id;
    }

    /**
     * @param int $news_id
     * @return News
     */
    public function setNewsId( $news_id )
    {
        $this->news_id = $news_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return News
     */
    public function setTitle( $title )
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getMediaType()
    {
        return $this->media_type;
    }

    /**
     * @param string $media_type
     * @return News
     */
    public function setMediaType( $media_type )
    {
        $this->media_type = $media_type;

        return $this;
    }

    /**
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->media_url;
    }

    /**
     * @param string $media_url
     * @return News
     */
    public function setMediaUrl( $media_url )
    {
        $this->media_url = $media_url;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return News
     */
    public function setText( $text )
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getButtonUrl()
    {
        return $this->button_url;
    }

    /**
     * @param string $button_url
     * @return News
     */
    public function setButtonUrl( $button_url )
    {
        $this->button_url = $button_url;

        return $this;
    }

    /**
     * @return string
     */
    public function getButtonText()
    {
        return $this->button_text;
    }

    /**
     * @param string $button_text
     * @return News
     */
    public function setButtonText( $button_text )
    {
        $this->button_text = $button_text;

        return $this;
    }

    /**
     * @return int
     */
    public function getSeen()
    {
        return $this->seen;
    }

    /**
     * @param int $seen
     * @return News
     */
    public function setSeen( $seen )
    {
        $this->seen = $seen;

        return $this;
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param string $updated_at
     * @return News
     */
    public function setUpdatedAt( $updated_at )
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param string $created_at
     * @return News
     */
    public function setCreatedAt( $created_at )
    {
        $this->created_at = $created_at;

        return $this;
    }

}