<?php
namespace Bookly\Backend\Modules\Diagnostics\Tests;

abstract class Test
{
    /** @var bool */
    protected $hidden = false;
    /** @var array */
    protected $errors = array();
    /** @var string */
    protected $slug;
    /** @var string */
    protected $title;
    /** @var string */
    protected $description;
    /** @var array */
    public $ignore_csrf = array();
    /** @var string */
    public $error_type = 'warning';

    /**
     * Execute test
     *
     * @return bool
     */
    public function execute()
    {
        try {
            return $this->run();
        } catch ( \Exception $e ) {
            return false;
        }
    }

    /**
     * Execute test
     *
     * @return bool
     */
    public function run()
    {
        return true;
    }

    /**
     * Get test errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Add error.
     *
     * @param string $error
     */
    public function addError( $error )
    {
        $this->errors[] = $error;
    }

    /**
     * Get test slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Get test title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get test description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get test hidden.
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * Get error type.
     *
     * @return string
     */
    public function getErrorType()
    {
        return $this->error_type;
    }
}