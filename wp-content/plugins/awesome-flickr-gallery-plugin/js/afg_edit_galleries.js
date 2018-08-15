mjson = jQuery.noConflict();

mjson(document).ready(function(){
    loadGallerySettings();
    showHidePerPage();
    customPhotoSize();
    });

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
        return
    }
    submit_button.disabled = false;
}

function verifyBlank() {
    var per_page = document.getElementById('afg_per_page');
    var gname = document.getElementById("afg_add_gallery_name");
    var submit_button = document.getElementById('afg_save_changes');
    if (per_page.value == '') {
        alert('Per Page can not be blank.');
        submit_button.disabled = true;
        return;
    }
    else if (gname.value == '') {
        alert('Gallery Name can not be blank.');
        submit_button.disabled = true;
        return;
    }
    submit_button.disabled = false;
}

function showHidePerPage() {
    var per_page_check = document.getElementById('afg_per_page_check');
    var per_page = document.getElementById('afg_per_page');

    if (per_page_check.checked == true) {
        per_page.disabled = true;
        per_page.value = genparams.default_per_page;
    }
    else {
        per_page.disabled = false;
        var gallery = document.getElementById('afg_photo_gallery');
        var galleries = genparams.galleries.replace(/&quot;/g, '"');
        var jgalleries = jQuery.parseJSON(galleries);
        active_gallery = jgalleries[gallery.value];
        per_page.value = active_gallery.per_page || genparams.default_per_page;
    }
}

function getPhotoSourceType() {
    var source_element = document.getElementById('afg_photo_source_type');
    var photosets_box = document.getElementById('afg_photosets_box');
    var galleries_box = document.getElementById('afg_galleries_box');
    var groups_box = document.getElementById('afg_groups_box');
    var tags_box = document.getElementById('afg_tags');
    var source_label = document.getElementById('afg_photo_source_label');
    var help_text = document.getElementById('afg_source_help');

    if (source_element.value == 'photostream' || source_element.value == 'popular') {
        source_label.style.display = 'none';
        photosets_box.style.display = 'none';
        galleries_box.style.display = 'none';
        groups_box.style.display = 'none';
        tags_box.style.display = 'none';
        help_text.style.display = 'none';
    }
    else if (source_element.value == 'gallery') {
        if (!galleries_box.value) {
            alert('You have no galleries associated with your Flickr account.');
            source_element.value = 'photostream';
            getPhotoSourceType();
        }
        else {
            source_label.style.display = 'block';
            galleries_box.style.display = 'block';
            photosets_box.style.display = 'none';
            groups_box.style.display = 'none';
            tags_box.style.display = 'none';
            help_text.style.display = 'none';
            source_label.innerHTML = 'Select Gallery';
        }
    }
    else if (source_element.value == 'photoset') {
        if (!photosets_box.value) {
            alert('You have no photosets associated with your Flickr account.');
            source_element.value = 'photostream';
            getPhotoSourceType();
        }
        else {
            source_label.style.display = 'block';
            photosets_box.style.display = 'block';
            galleries_box.style.display = 'none';
            groups_box.style.display = 'none';
            tags_box.style.display = 'none';
            help_text.style.display = 'none';
            source_label.innerHTML = "Select Photoset";
        }
    }
    else if (source_element.value == 'group') {
        if (!groups_box.value) {
            alert('You have no groups associated with your Flickr account.');
            source_element.value = 'photostream';
            getPhotoSourceType();
        }
        else {
            source_label.style.display = 'block';
            photosets_box.style.display = 'none';
            galleries_box.style.display = 'none';
            groups_box.style.display = 'block';
            tags_box.style.display = 'none';
            help_text.style.display = 'none';
            source_label.innerHTML = "Select Group";
        }
    }
    else if (source_element.value == 'tags') {
        source_label.style.display = 'block';
        photosets_box.style.display = 'none';
        galleries_box.style.display = 'none';
        groups_box.style.display = 'none';
        tags_box.style.display = 'block';
        help_text.style.display = 'block';
        source_label.innerHTML = "Tags";
    }
}

function verifyEditBlank() {
    var gname = document.getElementById("afg_edit_gallery_name");
    var submit_button = document.getElementById('afg_save_changes');
    if (gname.value == "") {
        alert('Gallery Name can not be blank.');
        submit_button.disabled = true;
        return;
    }
    submit_button.disabled = false;
}
function loadGallerySettings() {
    var gallery = document.getElementById('afg_photo_gallery');
    var gallery_name = document.getElementById('afg_edit_gallery_name');
    var gallery_descr = document.getElementById('afg_edit_gallery_descr');
    var photosets_box = document.getElementById('afg_photosets_box');
    var galleries_box = document.getElementById('afg_galleries_box');
    var groups_box = document.getElementById('afg_groups_box');
    var tags_box = document.getElementById('afg_tags');
    var source_label = document.getElementById('afg_source_label');
    var per_page = document.getElementById('afg_per_page');
    var sort_order = document.getElementById('afg_sort_order');
    var per_page_check = document.getElementById('afg_per_page_check');
    var photo_size = document.getElementById('afg_photo_size');
    var captions = document.getElementById('afg_captions');
    var descr = document.getElementById('afg_descr');
    var columns = document.getElementById('afg_columns');
    var slideshow_option = document.getElementById('afg_slideshow_option');
    var credit_note = document.getElementById('afg_credit_note');
    var bg_color = document.getElementById('afg_bg_color');
    var width = document.getElementById('afg_width');
    var pagination = document.getElementById('afg_pagination');
    var gallery_code = document.getElementById('afg_flickr_gallery_code');
    var afg_custom_size = document.getElementById("afg_custom_size");
    var afg_custom_size_square = document.getElementById("afg_custom_size_square");
    var afg_custom_size_block = document.getElementById("afg_custom_size_block");

    var galleries = genparams.galleries.replace(/&quot;/g, '"');
    var jgalleries = jQuery.parseJSON(galleries);
    active_gallery = jgalleries[gallery.value];

    source_element = document.getElementById('afg_photo_source_type');
    source_element.value = active_gallery.photo_source;

    gallery_name.value = active_gallery.name;
    gallery_descr.value = active_gallery.gallery_descr;

    if (active_gallery.per_page) {
        per_page_check.checked = false;
        per_page.disabled = false;
    }
    else {
        per_page_check.checked = true;
        per_page.disabled = true;
    }

    per_page.value = active_gallery.per_page || genparams.default_per_page;
    sort_order.value = active_gallery.sort_order || 'default';
    photo_size.value = active_gallery.photo_size || 'default';
    captions.value = active_gallery.captions || 'default';
    descr.value = active_gallery.descr || 'default';
    columns.value = active_gallery.columns || 'default';
    slideshow_option.value = active_gallery.slideshow_option || 'default';
    bg_color.value = active_gallery.bg_color || 'default';
    width.value = active_gallery.width || 'default';
    pagination.value = active_gallery.pagination || 'default';
    credit_note.value = active_gallery.credit_note || 'default';
    gallery_code.innerHTML = '[AFG_gallery id=\'' + gallery.value + '\']';

    if (photo_size.value == "custom") {
        afg_custom_size_block.style.display = "";
        afg_custom_size.value = active_gallery.custom_size;
        if (active_gallery.custom_size_square == 'true')
            afg_custom_size_square.checked = true;
        else
            afg_custom_size_square.checked = false;
    }
    else
        afg_custom_size_block.style.display = "none";

    getPhotoSourceType();

    if (source_element.value == 'photoset') {
        photosets_box.value = active_gallery.photoset_id;
        /*galleries_box.value = '';*/
        /*groups_box.value = '';*/
        /*tags_box.value = '';*/
    }
    else if (source_element.value == 'gallery') {
        galleries_box.value = active_gallery.gallery_id;
        /*photosets_box.value = '';*/
        /*groups_box.value = '';*/
        /*tags_box.value = '';*/
    }
    else if (source_element.value == 'group') {
        groups_box.value = active_gallery.group_id;
        /*photosets_box.value = '';*/
        /*galleries_box.value = '';*/
        /*tags_box.value = '';*/
    }
    else if (source_element.value == 'tags') {
        tags_box.value = active_gallery.tags;
        /*photosets_box.value = '';*/
        /*galleries_box.value = '';*/
        /*groups_box.value = '';*/
    }
}
