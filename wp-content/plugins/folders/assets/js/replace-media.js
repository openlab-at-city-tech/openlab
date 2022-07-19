(function (factory) {
    "use strict";
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    }
    else if(typeof module !== 'undefined' && module.exports) {
        module.exports = factory(require('jquery'));
    }
    else {
        factory(jQuery);
    }
}(function ($, undefined) {
    $(document).ready(function (){
        // Open file selector on div click
        $(document).on("click", "#upload-file", function(e){
            $("#media_file").click();
        });

        // file selected
        $(document).on("change", "#media_file", function(e){
            var fd = new FormData();

            var files = $('#media_file')[0].files[0];

            fd.append('media_file',files);

            uploadData(this);
        });

        $(document).on("click", ".upgrade-btn-box a", function(e){
            e.stopPropagation();
            e.stopImmediatePropagation();
        });
    });

    function uploadData(input) {
        if($("#media_file").val() != "") {
            $(".new-image-box .file-size").addClass("hide-it");
            $("#upload-file").removeClass("active");

            var fileName = $("#media_file").val();
            fileName = fileName.toLowerCase();
            fileName = fileName.split(".");
            var fileExt = fileName[fileName.length - 1];
            $(".new-image-box .image-size").remove();
            if(fileExt == "jpg" || fileExt == "png" || fileExt == "jpeg" || fileExt == "gif") {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $(".drag-and-drop-title").html("<img class='pre-image' id='pre-image' >");
                        $('#pre-image').attr('src', e.target.result);

                        var image = new Image();
                        image.src = e.target.result;
                        image.onload = function () {
                            $(".new-image-box img").after('<span class="image-size">Height x Width</span>').show();
                        };
                    }

                    reader.readAsDataURL(input.files[0]); // convert to base64 string
                }
            } else {
                $(".drag-and-drop-title").html('<span class="dashicons dashicons-media-document"></span>');
            }

            setFileSize(input.files[0].size);

            $(".replace-message").removeClass("active");
            if(fileExt != $("#file_ext").val()) {
                $(".file-type").addClass("active");
                $("#rename-file").prop("checked", true);
            } else {
                $("#replace-file").prop("checked", true);
            }
            $(".button-primary").prop("disabled", false);
        } else {
            $(".button-primary").prop("disabled", true);
        }
    }

    function setFileSize(fileSize) {
        fileSize = parseInt(fileSize);
        if(fileSize > 1000000) {
            fileSize = parseFloat(fileSize/1000000).toFixed(2)+" MB";
        } else if(fileSize > 1000) {
            fileSize = parseFloat(fileSize/1000).toFixed(2)+" KB";
        } else {
            fileSize = fileSize+" B";
        }
        $(".new-image-box .file-size").removeClass("hide-it");
        $("#upload-file").addClass("active");
    }

}));