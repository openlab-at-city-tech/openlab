mjson = jQuery.noConflict();

mjson(document).ready(function(){
    customPhotoSize();
    });

function verifyPerPageBlank() {
    var per_page = document.getElementById("afg_per_page");
    var submit_button = document.getElementById('afg_save_changes');
    if (per_page.value == "") {
        alert('Per Page can not be blank.');
        submit_button.disabled = true;
        per_page.focus();
        return;
    }
    submit_button.disabled = false;
}

function customPhotoSize() {
    var afg_photo_size = document.getElementById("afg_photo_size");
    var afg_custom_size_block = document.getElementById("afg_custom_size_block");

    if (afg_photo_size.value == "custom")
        afg_custom_size_block.style.display = "";
    else
        afg_custom_size_block.style.display = "none";
}

function verifyCustomSizeBlank() {
    var afg_photo_size = document.getElementById("afg_photo_size");
    var submit_button = document.getElementById('afg_save_changes');
    var afg_custom_size = document.getElementById('afg_custom_size');
    
    if (afg_photo_size.value == "custom" && afg_custom_size.value == "") {
        alert('Custom Width can not be blank if you want to use custom size.');
        submit_button.disabled = true;
        afg_custom_size.focus();
        return
    }
    submit_button.disabled = false;
}
