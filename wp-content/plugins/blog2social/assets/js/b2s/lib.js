b2s = typeof(b2s) == "undefined" ? {network: {}} : b2s;

b2s.network.showImageDialog = function(data){
    var authId = data.network_auth_id;
    jQuery('.b2s-image-change-this-network').attr('data-network-auth-id', authId);
    jQuery('.b2s-upload-image').attr('data-network-auth-id', authId);

    var content = "<img class='b2s-post-item-network-image-selected-account' height='22px' src='" + jQuery('.b2s-post-item-network-image[data-network-auth-id="' + authId + '"]').attr('src') + "' /> " + jQuery('.b2s-post-item-details-network-display-name[data-network-auth-id="' + authId + '"]').html();
    jQuery('.b2s-selected-network-for-image-info').html(content);
    jQuery('#b2sInsertImageType').val("0");
    jQuery('#b2s-network-select-image').modal('show');

    jQuery('.networkImage').each(function () {
        var width = this.naturalWidth;
        var height = this.naturalHeight;
        jQuery(this).parents('.b2s-image-item').find('.b2s-image-item-caption-resolution').html(width + 'x' + height);
    });

    return false;
};