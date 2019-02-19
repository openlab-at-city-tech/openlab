jQuery(document).ready(function($) {
    $editFrom = $('#profile-edit-form');
    $submitFrom = $('#profile-group-edit-submit');

    $editFrom.parsley({
        trigger: 'change'
    }).on('field:error', function() {
        $editFrom.find('.error-container').addClass('error');

        $submitFrom.addClass('btn-disabled')
            .val('Please Complete Required Fields');
    }).on('field:success', function() {
        if ( ! this.parent.isValid() ) {
            return false;
        }

        $editFrom.find('.error-container').removeClass('error');

        $submitFrom.removeClass('btn-disabled')
            .val('Save Changes');
    });
});
