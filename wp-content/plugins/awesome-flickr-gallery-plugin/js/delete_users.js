function CheckAllDeleteUsers() {
    var users = genparams.users.replace(/&quot;/g, '"');
    var jusers = jQuery.parseJSON(users);
    var delete_all = document.getElementById('delete_all_users');
    for(var id in jusers) {
        if (id == '0') continue;
        var delete_user = document.getElementById('delete_user_' + id);
        delete_user.checked = delete_all.checked;
    }
}

function verifySelectedUsers() {
alert('hey')
    var users = genparams.users.replace(/&quot;/g, '"');
    var jusers = jQuery.parseJSON(users);
    var count = 0;
    for(var id in jusers) {
        if (id == '0') continue;
        var delete_user = document.getElementById('delete_user_' + id);
        if (delete_user.checked) {
            count++;
        }
    }
    if (count == 0) {
        alert('Select at least one User to delete.');
        return false;
    }
    else {
        return confirm('Are you sure you want to delete selected Users?');
    }
}
