<?php namespace TheLion\OutoftheBox\API\Dropbox\Models;

class FolderLinkMetadata extends BaseModel
{

    /**
     * URL of the shared link.
     *
     * @var string
     */
    protected $url;

    /**
     * The last component of the path (including extension).
     *
     * @var string
     */
    protected $name;

    /**
     * The link's access permissions.
     *
     * @var \TheLion\OutoftheBox\API\Dropbox\Models\LinkPermissions
     */
    protected $link_permissions;

    /**
     * A unique identifier of the file
     *
     * @var string
     */
    protected $id;

    /**
     * Expiration time, if set. By default the link won't expire. 
     * This field is optional.
     *
     * @var DateTime
     */
    protected $expires;

    /**
     * The lowercased full path in the user's Dropbox.
     *
     * @var string
     */
    protected $path_lower;

    /**
     * The team membership information of the link's owner. 
     * This field will only be present if the link's owner is a team member. 
     * This field is optional.
     *
     * @var \TheLion\OutoftheBox\API\Dropbox\Models\TeamMemberInfo
     */
    protected $team_member_info;

    /**
     * The team information of the content's owner. 
     * This field will only be present if the content's owner is a team member and 
     * the content's owner team is different from the link's owner team. 
     * This field is optional.
     *
     * @var \TheLion\OutoftheBox\API\Dropbox\Models\Team
     */
    protected $content_owner_team_info;

    /**
     * Create a new FileMetadata instance
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->url = $this->getDataProperty('url');
        $this->name = $this->getDataProperty('name');
        $this->id = $this->getDataProperty('id');
        $this->expires = $this->getDataProperty('expires');
        $this->path_lower = $this->getDataProperty('path_lower');

        //Make LinkPermissions
        $linkPermissions = $this->getDataProperty('link_permissions');
        if (is_array($linkPermissions)) {
            $this->link_permissions = new LinkPermissions($linkPermissions);
        }

        //Make TeamMemberInfo
        $teamMemberInfo = $this->getDataProperty('team_member_info');
        if (is_array($teamMemberInfo)) {
            $this->team_member_info = new TeamMemberInfo($teamMemberInfo);
        }

        //Make ContentOwnerTeamInfo
        $contentOwnerTeamInfo = $this->getDataProperty('content_owner_team_info');
        if (is_array($contentOwnerTeamInfo)) {
            $this->content_owner_team_info = new Team($contentOwnerTeamInfo);
        }
    }

    /**
     * Get the 'id' property of the file model.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the 'name' property of the file model.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the 'url' property of the file model.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get the 'path_lower' property of the file model.
     *
     * @return string
     */
    public function getPathLower()
    {
        return $this->path_lower;
    }

    /**
     * Get the 'link_permissions' property of the file model.
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\LinkPermissions
     */
    public function getLinkPermissions()
    {
        return $this->link_permissions;
    }

    /**
     * Get the 'team_member_info' property of the file model.
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\TeamMemberInfo
     */
    public function getTeamMemberInfo()
    {
        return $this->team_member_info;
    }

    /**
     * Get the 'content_owner_team_info' property of the file model.
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Models\Team
     */
    public function getContentOwnerTeamInfo()
    {
        return $this->content_owner_team_info;
    }

    /**
     * Get the 'expires' property of the file model.
     *
     * @return DateTime
     */
    public function getExpires()
    {
        return $this->expires;
    }
}
