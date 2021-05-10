<?php namespace TheLion\OutoftheBox\API\Dropbox\Models;

class SharedLinkSettings extends BaseModel
{

    /**
     * The requested access for this shared link. This field is optional.
     * 
     * public       Anyone who has received the link can access it. No login required.
     * team_only    Only members of the same team can access the link. Login is required.
     * password     A link-specific password is required to access the link. Login is not required.
     * 
     * @var string
     */
    protected $requested_visibility;

    /**
     * If requested_visibility is RequestedVisibility.password this is needed to specify the password to access the link.
     * This field is optional.
     *
     * @var string
     */
    protected $link_password;

    /**
     * Expiration time of the shared link. By default the link won't expire.
     * This field is optional.
     *
     * @var DateTime
     */
    protected $expires;

    /**
     * Create a new LinkPermissions instance
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->requested_visibility = $this->getDataProperty('requested_visibility');
        $this->link_password = $this->getDataProperty('link_password');
        $this->expires = $this->getDataProperty('expires');
    }

    /**
     * The requested access for this shared link.
     *
     * @return string
     */
    public function getRequestedVisibility()
    {
        return $this->requested_visibility;
    }

    /**
     * If requested_visibility is RequestedVisibility.password this is needed to specify the password to access the link.
     *
     * @return string
     */
    public function getLinkPassword()
    {
        return $this->link_password;
    }

    /**
     * Expiration time of the shared link. By default the link won't expire. 
     *
     * @return DateTime
     */
    public function getExpires()
    {
        return $this->expires;
    }
}
