<?php namespace TheLion\OutoftheBox\API\Dropbox\Models;

class LinkPermissions extends BaseModel
{

    /**
     * Whether the caller can revoke the shared link
     *
     * @var bool
     */
    protected $can_revoke;

    /**
     * The current visibility of the link after considering the shared links policies of the 
     * team (in case the link's owner is part of a team) and the shared 
     * folder (in case the linked file is part of a shared folder). 
     * This field is shown only if the caller has access to this info (the link's owner always has access to this data). 
     * This field is optional.
     *
     * @var string
     */
    protected $resolved_visibility;

    /**
     * The shared link's requested visibility.
     * This can be overridden by the team and shared folder policies. 
     * The final visibility, after considering these policies, can be found in resolved_visibility. 
     * This is shown only if the caller is the link's owner. This field is optional.
     *
     * @var string
     */
    protected $requested_visibility;

    /**
     * The failure reason for revoking the link. 
     * This field will only be present if the can_revoke is false. 
     * This field is optional.
     *
     * @var string
     */
    protected $revoke_failure_reason;

    /**
     * Create a new LinkPermissions instance
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->can_revoke = $this->getDataProperty('can_revoke');

        //$resolved_visibility
        $resolved_visibility = $this->getDataProperty('resolved_visibility');
        if (is_array($resolved_visibility) && !empty($resolved_visibility)) {
            $this->resolved_visibility = $resolved_visibility['.tag'];
        }

        //$requested_visibility
        $requested_visibility = $this->getDataProperty('requested_visibility');
        if (is_array($requested_visibility) && !empty($requested_visibility)) {
            $this->requested_visibility = $requested_visibility['.tag'];
        }

        //$revoke_failure_reason
        $revoke_failure_reason = $this->getDataProperty('revoke_failure_reason');
        if (is_array($revoke_failure_reason) && !empty($revoke_failure_reason)) {
            $this->revoke_failure_reason = $revoke_failure_reason['.tag'];
        }
    }

    /**
     * Whether the caller can revoke the shared link
     *
     * @return bool
     */
    public function getCanRevoke()
    {
        return $this->can_revoke;
    }

    /**
     * The current visibility of the link after considering the shared links policies of the 
     * team (in case the link's owner is part of a team) and the shared 
     * folder (in case the linked file is part of a shared folder). 
     * This field is shown only if the caller has access to this info (the link's owner always has access to this data). 
     * This field is optional.
     *
     * @return string
     */
    public function getResolvedVisibility()
    {
        return $this->resolved_visibility;
    }

    /**
     * The shared link's requested visibility.
     * This can be overridden by the team and shared folder policies. 
     * The final visibility, after considering these policies, can be found in resolved_visibility. 
     * This is shown only if the caller is the link's owner. This field is optional.
     *
     * @return string
     */
    public function getRequestedVisibility()
    {
        return $this->requested_visibility;
    }

    /**
     * The failure reason for revoking the link. 
     * This field will only be present if the can_revoke is false. 
     * This field is optional.
     *
     * @return string
     */
    public function getRevokeFailureReason()
    {
        return $this->revoke_failure_reason;
    }
}
