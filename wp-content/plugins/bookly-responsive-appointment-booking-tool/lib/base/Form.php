<?php
namespace Bookly\Lib\Base;

abstract class Form
{
    // Protected properties.

    /**
     * Class name of entity.
     * Must be defined in child form class.
     * @staticvar string
     */
    protected static $entity_class;

    /**
     * @staticvar string
     */
    protected static $namespace = '\Bookly\Lib\Entities';

    /**
     * Entity object.
     * @var Entity|null
     */
    protected $object;

    /**
     * Fields of form.
     * @var array
     */
    protected $fields = array( 'id' );

    /**
     * Values of form.
     * @var array
     */
    protected $data = array();


    // Public methods.

    /**
     * Form constructor.
     */
    public function __construct()
    {
        // Run configuration of child form.
        $this->configure();
    }

    /**
     * Configure the form in child class.
     */
    public function configure()
    {
        // Place configuration code here, like $this->setFields(...)
    }

    /**
     * Set fields.
     *
     * @param array $fields
     */
    public function setFields( array $fields )
    {
        $this->fields = array_merge( array( 'id' ), $fields );
    }

    /**
     * Bind values to form.
     *
     * @param array $params
     * @param array $files
     */
    public function bind( array $params, array $files = array() )
    {
        foreach ( $this->fields as $field ) {
            if ( array_key_exists( $field, $params ) ) {
                $this->data[ $field ] = $params[ $field ];
            }
        }

        // Check if current form for entity.
        if ( static::$entity_class ) {
            /** @var Entity $entity_class */
            $entity_class = static::$namespace . '\\' . static::$entity_class;
            if ( $this->isNew() ) {
                // Create object of entity class.
                $this->object = new $entity_class();
            } else {
                // If we are going to update the object
                // load it from the database.
                $this->object = $entity_class::find( $this->data['id'] );
            }
        }
    }

    /**
     * Determine whether we update the object or create it.
     *
     * @return boolean Create - true, Update - false
     */
    public function isNew()
    {
        return ! ( array_key_exists( 'id', $this->data ) && $this->data['id'] );
    }

    /**
     * Save data to database.
     *
     * @return Entity
     */
    public function save()
    {
        $this->object
            ->setFields( $this->data )
            ->save();

        return $this->object;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get entity object.
     *
     * @return Entity|null
     */
    public function getObject()
    {
        return $this->object;
    }

}