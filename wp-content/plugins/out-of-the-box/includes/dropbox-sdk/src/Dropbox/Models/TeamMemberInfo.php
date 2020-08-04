<?php namespace Kunnu\Dropbox\Models;

class TeamMemberInfo extends BaseModel
{

    /**
     * The display name of the user.
     *
     * @var string
     */
    protected $display_name;

    /**
     * ID of user as a member of a team. 
     * This field will only be present if the member is in the same team as current user. 
     * This field is optional.
     *
     * @var string
     */
    protected $member_id;

    /**
     * TeamInfodata
     *
     * @var \Kunnu\Dropbox\Models\Team
     */
    protected $team_info;

    /**
     * Create a new TeamMemberInfo instance
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->display_name = $this->getDataProperty('display_name');
        $this->member_id = $this->getDataProperty('member_id');

        //Make TeamInfo
        $teamInfo = $this->getDataProperty('team_info');
        if (is_array($teamInfo)) {
            $this->team_info = new Team($teamInfo);
        }
    }

    /**
     * get the 'member_id' property of user as a member of a team. 
     * This field will only be present if the member is in the same team as current user. 
     * This field is optional.
     *
     * @return string
     */
    public function getMemberId()
    {
        return $this->member_id;
    }

    /**
     * The display name of the user.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->display_name;
    }

    /**
     * The metadata for the photo/video.
     *
     * @return \Kunnu\Dropbox\Models\TeamInfo
     */
    public function getTeamInfo()
    {
        return $this->team_info;
    }
}
