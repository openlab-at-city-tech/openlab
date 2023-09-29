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
        $("#upload-file").click(function(){
            $("#media_file").click();
        });

        // file selected
        $("#media_file").change(function(){
            var fd = new FormData();

            var files = $('#media_file')[0].files[0];

            fd.append('media_file',files);

            uploadData(this);
        });

        $(document).on("change", "input[name='date_options']:checked", function(){
            if($(this).val() == "custom_date") {
                $("#custom-date").show();
            } else {
                $("#custom-date").hide();
            }
            setBoxHeight();
        });

        $(document).on("click", "#replacement_option", function(){
            if($(this).is(":checked")) {
                $("#custom-path").show();
            } else {
                $("#custom-path").hide();
            }
            setBoxHeight();
        });

        if($("#media_file").length) {
            //new SimpleDropit(document.getElementById('media_file'));
        }

        setBoxHeight();
    });

    function setBoxHeight() {
        $(".media-setting").css("height", "auto");
        if(parseInt($(".media-bottom-box-left .media-setting").height()) > parseInt($(".media-bottom-box-right .media-setting").height())) {
            $(".media-bottom-box-right .media-setting").height(parseInt($(".media-bottom-box-left .media-setting").height()));
        } else {
            $(".media-bottom-box-left .media-setting").height(parseInt($(".media-bottom-box-right .media-setting").height()));
        }
    }

    function uploadData(input) {
        console.log(234132);
        //$(".image-preview .img-overlay").html("");
        if($("#media_file").val() != "") {
            $("#image-preview").addClass("has-item");
            var fileName = $("#media_file").val();
            fileName = fileName.toLowerCase();
            fileName = fileName.split(".");
            var fileExt = fileName[fileName.length - 1];
            $(".pre-image, .file-icon-preview").remove();
            $("#img-overlay > div").hide();
            if(fileExt == "jpg" || fileExt == "png" || fileExt == "jpeg" || fileExt == "gif" || fileExt == "svg") {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $(".image-preview").append("<img class='pre-image' id='pre-image' >");
                        $('#pre-image').attr('src', e.target.result);

                        var image = new Image();
                        image.src = e.target.result;
                        image.onload = function () {
                            $("#file-dimension").html("<span class='file-size'>Dimension: "+this.width+" x "+this.height+"</span>").show();
                        };
                    }

                    reader.readAsDataURL(input.files[0]); // convert to base64 string
                }
            } else {
                $(".image-preview").append('<div class="file-icon-preview"><span class="dashicons dashicons-media-document"></span></div>');
            }

            setFileSize(input.files[0].size);

            $("#file-name").html("Name: "+input.files[0].name).show();
            $("#file-type").html("Name: "+input.files[0].type).show();

            $(".replace-message").removeClass("active");
            if(fileExt.toLowerCase() != ($("#file_ext").val()).toLowerCase()) {
                $(".file-type").addClass("active");
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
        $("#file-size").html("<span class='file-size'>Size: "+fileSize+"</span>").show();
    }

}));
