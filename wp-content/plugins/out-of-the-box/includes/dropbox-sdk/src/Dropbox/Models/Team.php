<?php namespace Kunnu\Dropbox\Models;

class Team extends BaseModel
{

    /**
     * The team's unique ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The name of the team.
     *
     * @var string
     */
    protected $name;

    /**
     * Create a new TeamMemberInfo instance
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->id = $this->getDataProperty('id');
        $this->name = $this->getDataProperty('name');
    }

    /**
     *  The team's unique ID.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * The name of the team.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}
