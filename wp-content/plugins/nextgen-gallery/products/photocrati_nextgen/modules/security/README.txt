Example pseudo-code:
	
$security = $this->get_registry()->get_utility('I_Security_Manager');


// generating some kind of security-enabled request
$sec_token = $security->get_request_token('nextgen_edit_thumbnail', array('id' => 1));

$markup = '<form id="form-1" [...]>' . $sec_token->get_form_html(array('prefix' => 'form-1')) . '</form>';


// somewhere else, in POST/GET handler
$sec_token = $security->get_request_token('nextgen_edit_thumbnail', array('id' => 1));
$sec_actor = $security->get_current_actor();

if ($sec_token->check_current_request() && $sec_actor->is_allowed('nextgen_edit_thumbnail', array('id' => 1)))
{
	// proceed with action nextgen_edit_thumbnail
}
else
{
	// security error!
}

