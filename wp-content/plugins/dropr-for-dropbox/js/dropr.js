var Dropr = (function($) {
    'use strict';

    // @rel: dropr guten-block
    var isDroprBlockActive = function() {
        var isActive = false;
        if(typeof wp.blocks !== 'undefined') {
            var dropr_block = wp.blocks.getBlockType('dropr-for-dropbox/dropr');
            if(typeof dropr_block !== 'undefined') {
                isActive = true;
            }
        }
        return isActive;
    };

    var dropbox_api = dropr_options.wpdropboxapikey,
        ajaxurl = dropr_options.ajaxurl,
        dropr_nonce = dropr_options.dropr_nonce,
        dropURL = '',
        localUrl = '',
        pluginurl = '',
        drpbox = '',
        drpfiledata = '',
        localfile = false,
        $handle = '',
        $image_handle = $('#wpdpx-embed-image'),
        $document_handle = $('#wpdpx-embed-document'),
        $video_handle = $('#wpdpx-embed-video'),
        $audio_handle = $('#wpdpx-embed-audio'),
        $mediainsert = $('#wpdpx-drop-insert'),
        $linkhandle = $('#wpdpx-embed-link'),
        $popuphandle = $('#wpdrop-popup'),
        init = function() {
            reset();
            bind_functions();
            mediaViews();
            featuredImgDropboxHandler();
            //$(window).resize(popup_position);
        },
        bind_functions = function() {
            // Close embed dialog
            $('#wpdrop-popup').on('click', '.cancel_embed', function(e) {
                e.preventDefault();
                $.magnificPopup.close();
            });
            // Insert
            $('#wpdrop-popup').on('click', '#wpdpx-drop-insert', function(e) {
                e.preventDefault();
                var embeditem = $("#wpdrop-popup input[name='insert-type']:checked").val();
                switch (embeditem) {
                    case 'link':
                        $handle = $linkhandle;
                        linkhandle();
                        break;
                    case 'document':
                        documenthandle();
                        break;
                    case 'video':
                        mediahandle('video');
                        break;
                    case 'audio':
                        mediahandle('audio');
                        break;
                    case 'image':
                        imagehandle();
                        break;
                }
                var wpdropbox = $handle.find('.wp-dropbox').val();
                // @rel: dropr guten-block
                if(! isDroprBlockActive()) {
                    wp.media.editor.insert(wpdropbox);
                }
                $.magnificPopup.close();
            });
            // @rel: dropr guten-block
            $(".block-editor").on('click', '.wp-dropr-block a', function(e) {
                e.preventDefault();
            });
            $('#wpdrop-popup').on('click', '.add-media-source', function(e) {
                e.preventDefault();
                var $c = $(this);
                filepicker($c.data('ext'));
            });
            $(".awsmdrop_link").change(function() {
                if ($(this).val() == 'custom') {
                    $handle.find('.drop-url').show();

                } else {
                    $handle.find('.drop-url').hide();

                }
            });
            $("#form-tabs input").on("click", function() {
                var CurItem = jQuery(this).data("item");
                $('#form-tabs li').removeClass('active');
                $(this).parent().addClass('active');
                var currentTab = jQuery(this).attr('href');
                $('div.wpdpx-tab-item').addClass('hidden');
                $(CurItem).removeClass('hidden');
            });
            $('#wpdrop-popup').on('click', '.removefile', function(e) {
                e.preventDefault();
                $(this).parent().remove();
                var ext = $(this).data('extr')
                $handle.find("[data-ext='." + ext + "']").show();
            });
            $(document).on('click', '.awsm-dropr', opendropbox);
        },
        opendropbox = function() {
            if (!dropbox_api) {
                $('#wpdrop-nokey').removeClass('hidden');
                $('#wpdrop-popup').addClass('api-key-pop');
                $('#wpdrop-popup-settings').addClass('hidden');
                popup();
                return false;
            }
            Dropbox.init({
                appKey: dropbox_api
            });
            Dropbox.choose({
                linkType: "preview",
                multiselect: false, // or true
                success: function(files) {
                    openpop(files);
                }
            });
        },
        popup = function() {
            $.magnificPopup.open({
                items: {
                    src: '#wpdrop-popup'
                },
                closeBtnInside: true,
                type: 'inline',
                alignTop: false,
                callbacks: {
                    open: function() {
                        $('body').addClass('wpdpx-popup');
                    },
                    close: function() {
                        $('body').removeClass('wpdpx-popup');
                    }
                }
            });
        },
        openpop = function(files) {
            drpbox = files[0];
            var iconurl = '';
            dropURL = drpbox.link.replace("dl=0", "raw=1");
            filecheck(drpbox);
            drpbox.link = dropURL;
            popup();
            if (drpbox.thumbnailLink) {
                var baseThumbnail = drpbox.thumbnailLink.split('?')[0];
                var cropped = baseThumbnail + '?' + $.param({
                    mode: 'fit',
                    bounding_box: 256
                });
                iconurl = cropped;
                $('#awsmdrop_img').attr("src", iconurl);
                $('.wpdpx-img-placeholder').show();
            } else {
                iconurl = drpbox.icon;
                $('#awsmdrop_icon').attr("src", iconurl).show();
                $('.wpdpx-img-placeholder').hide();
            }
            $('#awsmdrop_filesize').html(humanfilesize(drpbox.bytes));
            $('#awsmdrop_filename').html(drpbox.name);
        },
        newmediahandle = function(files, ext) {
            var mediaurl = files[0].link.replace("dl=0", "raw=1"),
                extenstion = ext.replace('.', "");
            if (mediaurl) {
                var new_media = '<div><label>' + extenstion + '</label><input type="text" class="awsmdrop_video " data-setting="' + extenstion + '" value="' + mediaurl + '" readonly/><a href="#" class="removefile" data-extr="' + extenstion + '">Remove</a></div>';
                $handle.find('.new_media').append(new_media);
                $handle.find('[data-ext="' + ext + '"]').hide();
                if (!$handle.find('.add-media-source:visible').length) {
                    $handle.find('.newmediamsg').hide();
                }
            }
        },
        filepicker = function(ext) {
            var extensions = new Array(ext),
                mediaurl = '';
            Dropbox.choose({
                linkType: "preview",
                multiselect: false, // or true
                extensions: extensions,
                success: function(files) {
                    newmediahandle(files, ext);
                },
                cancel: function(files) {
                    return false;
                }
            });
        },
        updatelink = function() {
            var linkurl = "",
                linkTo = $handle.find('.awsmdrop_link').val();
            if (linkTo == 'custom') {
                var linkurl = $handle.find('.link-to-custom').val();
            } else if (linkTo == 'file') {
                var linkurl = dropURL;
            }
            return linkurl;
        },
        imagehandle = function() {
            var imgAttributes = {};
            var imagehtml = "",
                linkurl = "";
            if (dropURL) {
                var captioninclude = ["width", "align"],
                    Imageattr = "",
                    captionattr = "";
                $.each($handle.find('[data-setting]'), function(node) {
                    if ($(this).val()) {
                        var attr = $(this).data('setting');
                        attr = attr === 'align' ? 'class' : attr;
                        var attrVal = $(this).val();
                        Imageattr += attr + '="' + attrVal + '" ';
                        imgAttributes[attr] = attrVal;
                        if ($.inArray(attr, captioninclude) !== -1) {
                            captionattr += attr + '="' + attrVal + '" ';
                        }
                    }
                });
                var linkurl = updatelink();
                imagehtml = '<img src="' + dropURL + '" ' + Imageattr + '/>';
                imgAttributes.src = dropURL;

                if (linkurl) {
                    imagehtml = '<a href="' + linkurl + '">' + imagehtml + '</a>';
                    imgAttributes.customURL = linkurl;
                }

                if ($('#dropr_caption').val()) {
                    var caption = $('#dropr_caption').val();
                    imagehtml = '[caption id="" ' + captionattr + ']' + imagehtml;
                    imagehtml += caption + '[/caption]';
                    imgAttributes.caption = caption;
                }
                $handle.data('blockAttrs', imgAttributes);
                $handle.find('.wp-dropbox').val(imagehtml);
            }
        },
        documenthandle = function() {
            var documentAttributes = {};
            var documenthtml = "";
            if (dropURL) {
                var Embedattr = "";
                $.each($handle.find('[data-setting]'), function(node) {
                    if ($(this).val()) {
                        var attr = $(this).data('setting');
                        var attrVal = $(this).val();
                        Embedattr += attr + '="' + attrVal + '" ';
                        documentAttributes[attr] = attrVal;
                    }
                });
                Embedattr = Embedattr.trim();
                documenthtml = '[docembed url="' + dropURL + '" ' + Embedattr + ']';
                documentAttributes.url = dropURL;
                $handle.data('blockAttrs', documentAttributes);
                $handle.find('.wp-dropbox').val(documenthtml);
            }
        },
        linkhandle = function() {
            var linkAttributes = {};
            var linkhtml = "",
                mc = false,
                linktxt = dropURL;
            if (dropURL) {
                var Linkattr = " ";
                $.each($handle.find('[data-setting]'), function(node) {
                    if ($(this).attr('type') == 'checkbox') {
                        mc = $(this).is(':checked');
                    } else {
                        mc = $(this).val();
                    }
                    if (mc) {
                        var attr = $(this).data('setting');
                        var attrVal = $(this).val();
                        Linkattr += attr + '="' + attrVal + '" ';
                        linkAttributes[attr] = attrVal;
                    }
                });
                var linkinurl = dropURL.replace("raw=1", "");
                linkinurl += $handle.find('.link-url option:selected:selected').data('suffix');
                if ($handle.find('.awsm-link').val()) {
                    linktxt = $handle.find('.awsm-link').val();
                } else {
                    if (dropr_options.linktext) {
                        linktxt = dropr_options.linktext;
                    } else {
                        linktxt = linkinurl;
                    }

                }
                linkhtml = '<a href=' + linkinurl + Linkattr + '>' + linktxt + '</a>';
                linkAttributes.target = typeof linkAttributes.target !== 'undefined' ? true : false;
                linkAttributes.src = linkinurl;
                linkAttributes.text = linktxt;
                $handle.data('blockAttrs', linkAttributes);
                $handle.find('.wp-dropbox').val(linkhtml);
            }
        },
        mediahandle = function(media) {
            var mediaAttributes = {};
            var mediahtml = "",
                mc = false;
            if (dropURL) {
                var mediaattr = " ";
                $.each($handle.find('[data-setting]'), function(node) {
                    if ($(this).attr('type') == 'checkbox') {
                        mc = $(this).is(':checked');
                    } else {
                        mc = $(this).val();
                    }
                    if (mc) {
                        var attr = $(this).data('setting');
                        var attrVal = $(this).val();
                        mediaattr += attr + '="' + attrVal + '" ';
                        mediaAttributes[attr] = attrVal;
                    }
                });
                mediahtml = '[' + media + mediaattr + '][/' + media + ']';
                mediaAttributes.autoplay = typeof mediaAttributes.autoplay !== 'undefined' ? true : false;
                mediaAttributes.loop = typeof mediaAttributes.loop !== 'undefined' ? true : false;
                $handle.data('blockAttrs', mediaAttributes);
                $handle.find('.wp-dropbox').val(mediahtml);
            }
        },
        filehandle = function(filedata) {
            $('#wpdpx-drop-preload').hide();
            drpfiledata = filedata;
            $handle = $('#wpdpx-embed-' + drpfiledata.filetype);
            $('#insert-type-embed').val(drpfiledata.filetype);
            $('.awsm_media').addClass('hidden');
            $handle.removeClass('hidden');
            fileprepare(drpfiledata);
        },
        fileprepare = function(filedata) {
            var filetype = filedata.filetype;
            switch (filetype) {
                case 'image':
                    var imgdata = new Image();
                    imgdata.src = dropURL;
                    $mediainsert.val($mediainsert.data('loading')).attr('disabled', true);
                    imgdata.onload = function() {
                        $handle.find('.awsmdrop_width').val(imgdata.width);
                        $handle.find('.awsmdrop_height').val(imgdata.height);
                        $mediainsert.val($mediainsert.data('txt')).removeAttr('disabled');
                    };
                    break;
                case 'document':
                    var $provider = $("#awsmdrop_provider option[value='microsoft']");
                    if (filedata.embed) {
                        $provider.attr({
                            'disabled': false,
					        'hidden': false
                        });
                        if (!filedata.microsoft) {
                            $provider.attr({
                                'disabled': true,
                                'hidden': true
                            });
                        }
                    }
                    break;
                case 'video':
                    $('#video_title').html(filedata.ext);
                    $('#main_video').val(dropURL).data('setting', filedata.ext);
                    $handle.find('[data-ext=".' + filedata.ext + '"]').hide();
                    break;
                case 'audio':
                    $('#audio_title').html(filedata.ext);
                    $('#main_audio').data('setting', filedata.ext).val(dropURL);
                    $handle.find('[data-ext=".' + filedata.ext + '"]').hide();
                    break;
                case 'link':
                    $('#form-tabs li').removeClass('active first-item');
                    $('#linkitem').addClass('active first-item');
                    $('#embeditem').addClass('disabled');
                    $('#wpdpx-embed').addClass('hidden');
                    $('#insert-type-link').prop('checked', true);
                    $('#insert-type-embed').attr('disabled', true);
                    break;
            }
        },
        humanfilesize = function(bytes) {
            var thresh = 1024;
            if (bytes < thresh) return bytes + ' B';
            var units = ['KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
            var u = -1;
            do {
                bytes /= thresh;
                ++u;
            } while (bytes >= thresh);
            return bytes.toFixed(1) + ' ' + units[u];
        },
        reset = function() {
            dropURL = drpbox = drpfiledata = '', localfile = false;
            $('#wpdpx-drop-preload').show();
            $linkhandle.addClass('hidden');
            $('.wpdpx-img-placeholder').hide();
            $('#awsmdrop_icon').attr("src", '').hide();
            $('#awsmdrop_filesize').html('');
            $('#awsmdrop_filename').html('');
            $('#awsm-image-link').val('');
            $('#form-tabs li').removeClass('active first-item');
            $('#embeditem').addClass('active first-item');
            $('#wpdpx-embed').removeClass('hidden');
            $('#insert-type-embed').prop('checked', true);
            $('#insert-type-embed').attr('disabled', false);
            $('.drop-url').hide();
            $('#embeditem').removeClass('disabled');
            $('.wpdpx-advanced-options .droprest').each(function() {
                $(this).val('');
            });
            $('.wpdpx-advanced-options input:checkbox').removeAttr('checked');
            $('.new_media').html('');
            $('.dropr-support').html($('.dropr-support').closest('droprmedialist').html());
            $('.dropr-support .add-media-source').show()
            $('.newmediamsg').show();

        },
        filecheck = function(file) {
            var fileurl = file.link.replace("?dl=0", "");
            var extension = fileurl.substr((fileurl.lastIndexOf('.') + 1));
            var filedata = {
                ext: extension,
                valid: false,
                embed: false,
                filetype: 'link'
            };
            if(!extension){
            	 return filehandle(filedata);
            }
            extension = extension.toLowerCase();
            var mimes = {
                    "image": ["jpg", "jpeg", "jpe", "gif", "png", "bmp", "tif", "tiff", "ico"],
                    "video": ["mp4", "m4v", "webm", "ogv", "wmv", "flv"],
                    "audio": ["mp3", "m4a", "ogg", "wav", "wma"],
                    "document": ["css", "js", "pdf", "ai", "tif", "tiff", "doc", "txt", "asc", "c", "cc", "h", "pot", "pps", "ppt", "xla", "xls", "xlt", "xlw", "docx", "dotx", "dotm", "xlsx", "xlsm", "pptx", "pages", "svg"]
                },
                services = {
                    "google": ["html", "txt", "csv", "htm", "css", "js", "pdf", "ai", "tif", "tiff", "doc", "txt", "asc", "c", "cc", "h", "pot", "pps", "ppt", "xla", "xls", "xlt", "xlw", "docx", "dotx", "dotm", "xlsx", "xlsm", "pptx", "pages", "svg"],
                    "microsoft": ["doc", "pot", "pps", "ppt", "xla", "xls", "xlt", "xlw", "docx", "dotx", "dotm", "xlsx", "xlsm", "pptx"]
                }
 
            $.each(mimes, function(key, checkmime) {
                if ($.inArray(extension, checkmime) != -1) {
                    filedata.valid = true;
                    filedata.filetype = key;
                    return false;
                } else {
                    filedata.valid = false;
                    filedata.filetype = 'link';
                }
            });
            $.each(services, function(key, checkmime) {
                if ($.inArray(extension, checkmime) != -1) {
                    filedata[key] = true;
                    filedata.embed = true;
                } else {
                    filedata[key] = false;
                }
            });
            
            filehandle(filedata);
        };

        /* -------- Media Views -------- */
        function mediaViews() {
            var Library = wp.media.controller.Library;
            var oldMediaFrame = wp.media.view.MediaFrame.Post;
            var medial10n = wp.media.view.l10n;
            
            function renderUploader(controller, state) {
                var uploading = state.get('droprUploading');
                var buttonClass = uploading ? ' disabled' : '';
                var template = wp.template('awsm-dropr-uploader');
                controller.$el.find('.uploader-inline').html(template({
                    message: medial10n.noItemsFound,
                    buttonClass: buttonClass,
                    isUploading: uploading
                }));
                controller.$el.find('.awsm-dropr-media-btn').on('click', function() {
                    showDroprFilePicker(controller, true);
                });
            }

            function showDroprFilePicker(controller, reRender) {
                reRender = typeof reRender !== 'undefined' ? reRender : false;
                var state = controller.state();
                var uploading = state.get('droprUploading');
                if (!uploading) {
                    var dropboxOptions = {
                        linkType: "preview",
                        multiselect: true,
                        extensions: dropr_options.extensions,
                        success: function(files) {
                            controller.$el.find('.awsm-dropr-media-uploader-status').hide();
                            state.set('droprUploading', true);
                            if (reRender) {
                                renderUploader(controller, state);
                            }
                            wp.media.post('dropr_add_to_media_library', {
                                files: files,
                                droprKey: dropr_nonce
                            }).done(function(res) {
                                state.set('droprUploading', false);
                                if (reRender) {
                                    renderUploader(controller, state);
                                }
                                controller.content.mode('browse');

                                $.each(res, function(i, value) {
                                    var attachment = wp.media.model.Attachment.create(value);
                                    state.get('library').add([attachment]);
                                    state.get('selection').add(attachment);
                                });                                
                            }).fail(function() {
                                state.set('droprUploading', false);
                                if (reRender) {
                                    renderUploader(controller, state);
                                }
                                controller.$el.find('.awsm-dropr-media-uploader-status').show();
                            });
                        }
                    };
                    if (dropr_options.storage.media_library === 'local') {
                        dropboxOptions.linkType = 'direct';
                        dropboxOptions.sizeLimit = dropr_options.max_upload_size;
                    }

                    Dropbox.init({
                        appKey: dropbox_api
                    });
                    Dropbox.choose(dropboxOptions);
                }
            }

            var View = wp.media.View;
            wp.media.view.DroprUploader = View.extend({
                tagName:   'div',
                className: 'uploader-inline',
                template:  wp.template('awsm-dropr-uploader'),
                events: {
                    'click .awsm-dropr-media-btn': 'showFilePicker'
                },
                initialize: function() {
                    _.defaults( this.options, {
                        message: '',
                        buttonClass: '',
                        isUploading: false
                    });
                    if (this.options.isUploading) {
                        this.options.buttonClass = ' disabled';
                    }

                    this.listenTo(this.model, 'change:droprUploading', this.refresh);
                },
                refresh: function() {
                    var uploading = this.model.get('droprUploading');
                    this.options.isUploading = uploading;
                    this.options.buttonClass = uploading ? ' disabled' : '';
                    this.render();
                },
                showFilePicker: function() {
                    var controller = this.controller;
                    showDroprFilePicker(controller);
                }
            });

            wp.media.view.MediaFrame.Post = oldMediaFrame.extend({
                initialize: function() {
                    oldMediaFrame.prototype.initialize.apply(this, arguments);
                    var options = this.options;

                    this.states.add([
                        new Library({
                            id:  'dropr-add-media',
                            title: 'Add from Dropbox',
                            priority: 250,
                            toolbar: 'main-insert',
                            router: 'upload',
                            library: wp.media.query($.extend({
                                'dropr_meta': '_awsm_dropr_attached_file'
                            }, options.library)),
                            filterable: 'all',
                            displaySettings: true,
                            displayUserSettings: true
                        })
                    ]);

                    this.on('content:render:upload', this.showDropboxUploader, this);
                    this.on('content:activate:browse', this.showMediaLibrary, this);
                    this.on('router:activate', this.showMediaLibrary, this);
                },
                showDropboxUploader: function() {
                    var state = this.state();
                    if (state.get('id') === 'dropr-add-media') {
                        this.content.set(new wp.media.view.DroprUploader({
                            controller: this,
                            model: state,
                            isUploading: state.get('droprUploading')
                        }));
                    }
                },
                showMediaLibrary: function() {
                    var controller = this;
                    var state = this.state();
                    var mode = controller.content.mode();
                    if (state.get('id') === 'dropr-add-media' && !controller.isModeActive('grid') && mode === 'browse') {
                        renderUploader(controller, state);
                    }
                }
            });
        }

        /* -------- Handling Featured Image -------- */

        function featuredImgAjaxHandle(files, $link) {
            var onErrorHandle = function() {
                $("#dropr-holder").html('Oops, something went wrong. Please try again later');
                $("#droper-featured").removeClass("dropr-loading").show();
                $('.droprLoader').hide();
            };
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    action: 'dropruploadimage',
                    files: files,
                    droprKey: dropr_nonce,
                    pid: $('#post_ID').val(),
                },
                success: function(data) {
                    if (data) {
                        if (data.status) {
                            // @rel: dropr guten-block
                            if(isDroprBlockActive()) {
                                var attachment_id = data.attachment_id;
                                if(typeof wp.data !== 'undefined') {
                                    $("#droper-featured").removeClass("dropr-loading");
                                    $(".droprLoader").hide();
                                    wp.data.dispatch( 'core/editor' ).editPost( { featured_media: attachment_id } );
                                    $('.editor-post-featured-image .editor-post-featured-image__preview').show();
                                }
                            } else {
                                $(".inside", "#postimagediv").html(data.html);
                            }
                        } else {
                            onErrorHandle();
                        }
                    } else{
                        onErrorHandle();
                    }
                },
                error: function(jqXHR, textStatus){
                    onErrorHandle();
                },
            });
        }
        
        function featuredImgDropboxHandler() {
            if(typeof dropbox_api !== 'undefined' && dropbox_api.length > 0) {
                $(document).on('click', '#droper-featured', function(e) {
                    e.preventDefault();
                    var $link = $(this);
                    $("#dropr-holder").html("");
                    var dropboxOptions = {
                        linkType: "direct",
                        extensions: [".jpg",".jpeg",".jpe",".gif",".png"],
                        multiselect: false,
                        success: function(files) {
                            $link.addClass('dropr-loading').hide();
                            $('.droprLoader').show();
                            $(".editor-post-featured-image .editor-post-featured-image__toggle").hide();
                            featuredImgAjaxHandle(files, $link);
                        }
                    };
                    if (dropr_options.storage.featured_img === 'dropbox') {
                        dropboxOptions.linkType = 'preview';
                    } else {
                        dropboxOptions.sizeLimit = dropr_options.max_upload_size;
                    }

                    Dropbox.init({
                        appKey: dropbox_api
                    });
                    Dropbox.choose(dropboxOptions);
                });
                setInterval(function() {
                    if ($('#remove-post-thumbnail').is(':visible') || $('.components-button.editor-post-featured-image__preview').is(':visible') || $('#droper-featured').hasClass('dropr-loading')) {
                        $('#droper-featured').hide();
                    }else {
                        $('#droper-featured').show();
                    }
                }, 200);
            }
        }
    return {
        init: init,
    };
})(jQuery);
jQuery(Dropr.init());
