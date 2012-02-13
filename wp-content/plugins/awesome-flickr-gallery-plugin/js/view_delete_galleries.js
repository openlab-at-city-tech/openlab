function CheckAllDeleteGalleries() {
    var galleries = genparams.galleries.replace(/&quot;/g, '"');
    var jgalleries = jQuery.parseJSON(galleries);
    var delete_all = document.getElementById('delete_all_galleries');
    for(var id in jgalleries) {
        if (id == '0') continue;
        var delete_gallery = document.getElementById('delete_gallery_' + id);
        delete_gallery.checked = delete_all.checked;
    }
}

function verifySelectedGalleries() {
    var galleries = genparams.galleries.replace(/&quot;/g, '"');
    var jgalleries = jQuery.parseJSON(galleries);
    var count = 0;
    for(var id in jgalleries) {
        if (id == '0') continue;
        var delete_gallery = document.getElementById('delete_gallery_' + id);
        if (delete_gallery.checked) {
            count++;
        }
    }
    if (count == 0) {
        alert('Select at least one gallery to delete.');
        return false;
    }
    else {
        return confirm('Are you sure you want to delete selected galleries?');
    }
}
