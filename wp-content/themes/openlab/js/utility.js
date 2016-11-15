(function ($) {

    if (window.OpenLab === undefined) {
        var OpenLab = {};
    }

    var resizeTimer;

    OpenLab.utility = {
        newMembers: {},
        newMembersHTML: {},
        protect: 0,
        mapCheck: {},
        uiCheck: {},
        selectDisplay: {},
        customSelectHTML: '',
        init: function () {

            if ($('.truncate-on-the-fly').length) {
                OpenLab.utility.truncateOnTheFly(true);
            }
            OpenLab.utility.adjustLoginBox();
            OpenLab.utility.sliderFocusHandler();
            OpenLab.utility.eventValidation();
            OpenLab.utility.refreshActivity();

            //EO Calendar JS filtering
            if (typeof wp !== 'undefined' && typeof wp.hooks !== 'undefined') {
                wp.hooks.addFilter('eventorganiser.fullcalendar_options', OpenLab.utility.calendarFiltering);
            }

            //BP EO Editor tweaks
            //doing this client-side for now
            OpenLab.utility.BPEOTweaks();

        },
        detectZoom: function () {

            var zoom = detectZoom.zoom();
            var device = detectZoom.device();

        },
        adjustLoginBox: function () {
            if ($('#user-info')) {

                var userInfo = $('#user-info');
                var helpInfo = $('#login-help')
                var avatar = userInfo.find('.avatar');
                if (userInfo.height() > avatar.height()) {
                    userInfo.addClass('multi-line');
                    helpInfo.addClass('multi-line');
                } else {
                    userInfo.removeClass('multi-line');
                    helpInfo.removeClass('multi-line');
                }

            }
        },
        sliderFocusHandler: function () {

            if ($('.camera_wrap_sr').length) {

                $('.camera_wrap_sr .camera_content a').each(function () {

                    var thisLink = $(this);
                    thisLink.on('focus', function () {

                        thisLink.closest('.camera_content').addClass('focus');

                    });
                    thisLink.on('blur', function () {

                        thisLink.closest('.camera_content').removeClass('focus');

                    });

                });

            }

        },
        eventValidation: function () {

            var eventPublish = $('.action-events #publish');
            var groupMetaBox = $('#bp_event_organiser_metabox .inside');
            var eventDetailMetaBox = $('#eventorganiser_detail .inside');

            if (eventPublish.length) {

                eventPublish.on('click', function (e) {

                    //can't submit an event without a group selection
                    var groupSelection = $('#bp_event_organiser_metabox .select2-selection__rendered .select2-selection__choice');

                    if (!groupSelection.length) {
                        e.preventDefault();

                        var message = '<div class="bp-template-notice error">Events must be associated with at least one group.</div>';
                        groupMetaBox.prepend(message);
                    } else {
                        groupMetaBox.find('.bp-template-notice').remove();
                    }

                    //can't submit an event if the end time is *before* the start time (or vice versa)
                    var rawStartTime = eventDetailMetaBox.find('#eo-start-time').val();
                    var rawStartDate = eventDetailMetaBox.find('#eo-start-date').val();
                    var rawEndTime   = eventDetailMetaBox.find('#eo-end-time').val();
                    var rawEndDate   = eventDetailMetaBox.find('#eo-end-date').val();

		    var startTime = OpenLab.utility.buildTime( rawStartDate, rawStartTime );
		    var endTime   = OpenLab.utility.buildTime( rawEndDate, rawEndTime );
                    
                    if (startTime > endTime) {
                        e.preventDefault();
                        var message = '<div class="bp-template-notice error">Start Time must be earlier than the End Time.</div>';
                        
                        //clean up first before adding new error message
                        eventDetailMetaBox.find('.bp-template-notice').remove();
                        eventDetailMetaBox.prepend(message);
                    } else {
                        eventDetailMetaBox.find('.bp-template-notice').remove();
                    }

                });

            }

        },
        venueMapControl: function () {

            var latCheck = $('#eo_venue_Lat');
            var venueMap = $('#venuemap');

            //if there is no venue present, time to quit
            if (typeof eovenue === 'undefined' && !venueMap.length) {
                return;
            }

            //on initial load, hide map if we have no LatLng values
            if (latCheck.val() === 'NaN' || parseInt(latCheck.val()) === 0) {
                venueMap.css('display', 'none');
            }

            OpenLab.utility.protect++;

            //going to use an interval to pick up on the map obj
            if (typeof eovenue.maps !== 'undefined' && Object.keys(eovenue.maps).length > 0) {

                //saftey first
                clearTimeout(OpenLab.utility.menuCheck);

                eovenue.maps.venuemap.map.addListener('center_changed', function () {
                    if (latCheck.val() === 'NaN' || parseInt(latCheck.val()) === 0) {
                        venueMap.css('display', 'none');
                    } else {
                        venueMap.css('display', 'block');
                    }

                });

            } else {

                if (OpenLab.utility.protect < 2000) {
                    OpenLab.utility.mapCheck = setTimeout(OpenLab.utility.venueMapControl(), 50);
                }

            }

        },
        venueDropdownControl: function () {

            var dropdownSelector = $('#venue_select');

            OpenLab.utility.protect++;

            if (dropdownSelector.length) {

                var comboBoxSelector = $('#venue_select.ui-combobox-input');

                if (comboBoxSelector.length) {

                    //safety first
                    clearTimeout(OpenLab.utility.uiCheck);

                    comboBoxSelector.on("autocompletesearch", function (event, ui) {

                        event.preventDefault();

                    });

                } else {

                    if (OpenLab.utility.protect < 2000) {
                        OpenLab.utility.uiCheck = setTimeout(OpenLab.utility.venueDropdownControl(), 50);
                    }

                }
            }

        },
        convertTimeToNum: function (time) {
            var hoursMinutes = time.split(/[.:]/);
            var hours = parseInt(hoursMinutes[0], 10);

            var partOfDay = 0;

            if (hoursMinutes[1].indexOf('pm') !== -1) {
                partOfDay = 12;
            }

            var minutes = hoursMinutes[1] ? parseInt(hoursMinutes[1], 10) : 0;

            return partOfDay + hours + minutes / 60;
        },
	buildTime: function( date, time ) {
	    var d = new Date();
	    var dateParts = date.split( '-' );
	    d.setFullYear( dateParts[2] );
	    d.setMonth( dateParts[0] );
	    d.setDate( dateParts[1] );

	    var timeParts = time.split( /[.:]/ );
	    var hour = parseInt( timeParts[0] );
	    var min = parseInt( timeParts[1].substr( 0, 2 ) );
	    var amOrPm = timeParts[1].substr( 2 );
            
	    if ( 'pm' === amOrPm && hour < 12) {
	        hour = hour + 12;
	    } else if ('am' === amOrPm && hour === 12){
                //clock strikes midnight
                hour = 0;
            }
	    
	    d.setHours( hour );
	    d.setMinutes( min );

	    return d;
	},
        calendarFiltering: function (args, calendar) {

            if (calendar.defaultview === 'agendaWeek') {
                args.scrollTime = '08:00:00';
                args.viewRender = function (view, element) {
                    OpenLab.utility.calendarScrollBarPadding(view, element);
                }
            } else {
                args.viewRender = function (view, element) {
                    OpenLab.utility.calendarScrollBarPadding(view, element);
                }
            }

            return args;

        },
        calendarScrollBarPadding: function (view, element) {

            if (view.name === 'agendaWeek') {

                var width = OpenLab.utility.getScrollBarWidth();

                console.log('width', width);

                $('.eo-fullcalendar .fc-row.fc-widget-header').wrap("<div class='fc-header-wrapper'></div>");

                $('.eo-fullcalendar .fc-day-grid, .eo-fullcalendar .fc-header-wrapper').css({
                    'border-right': width + 'px #f3f3f3 solid'
                });

            }

        },
        getScrollBarWidth: function () {

            var scrollDiv = document.createElement("div");
            scrollDiv.className = "scrollbar-measure";
            document.body.appendChild(scrollDiv);

            var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth;

            document.body.removeChild(scrollDiv);

            return scrollbarWidth;

        },
        BPEOTweaks: function () {

            var bpeo_metabox = $('#bp_event_organiser_metabox');

            if (bpeo_metabox.length) {

                var desc = ' <span class="bold">The event will appear in the OpenLab sitewide calendar unless one or more of the groups selected is private.</span>';

                bpeo_metabox.find('.inside .bp_event_organiser_desc').append(desc);
                bpeo_metabox.find('.hndle span').text('Display');

            }

        },
        setUpNewMembersBox: function (resize) {

            if (resize) {
                //OpenLab.utility.newMembers.html(OpenLab.utility.newMembersHTML);
                OpenLab.utility.newMembers.trigger('refreshCarousel', '[all]')
            } else {
                OpenLab.utility.newMembers = $('#home-new-member-wrap');
                OpenLab.utility.newMembersHTML = $('#home-new-member-wrap').html();

                //this is for the new OpenLab members slider on the homepage
                OpenLab.utility.newMembers.jCarouselLite({
                    circular: true,
                    btnNext: ".next",
                    btnPrev: ".prev",
                    vertical: false,
                    visible: 2,
                    auto: true,
                    speed: 200,
                    autoWidth: true,
                });
            }

            $('#home-new-member-wrap').css('visibility', 'visible').hide().fadeIn(700, function () {

                OpenLab.utility.truncateOnTheFly(false, true);

            });
        },
        refreshActivity: function () {

            var refreshActivity = $('#refreshActivity');

            if (!refreshActivity.length) {
                return;
            }

            var activityContainer = $('#whatsHappening');

            //safety first
            refreshActivity.off('click');

            refreshActivity.on('click', function (e) {

                e.preventDefault();
                refreshActivity.addClass('fa-spin');

                $.ajax({
                    type: 'GET',
                    url: ajaxurl,
                    data:
                            {
                                action: 'openlab_ajax_return_latest_activity',
                                nonce: localVars.nonce
                            },
                    success: function (data, textStatus, XMLHttpRequest)
                    {
                        refreshActivity.removeClass('fa-spin');
                        if (data === 'exit') {
                            //for right now, do nothing
                        } else {
                            activityContainer.html(data);
                        }
                    },
                    error: function (MLHttpRequest, textStatus, errorThrown) {
                        refreshActivity.removeClass('fa-spin');
                        console.log(errorThrown);
                    }
                });

            });

        },
        truncateOnTheFly: function (onInit, loadDelay) {
            if (onInit === undefined) {
                var onInit = false;
            }

            if (loadDelay === undefined) {
                var loadDelay = false;
            }

            $('.truncate-on-the-fly').each(function () {

                var thisElem = $(this);

                if (!loadDelay && thisElem.hasClass('load-delay')) {
                    return true;
                }

                var truncationBaseValue = thisElem.data('basevalue');
                var truncationBaseWidth = thisElem.data('basewidth');

                if (!onInit) {
                    var originalCopy = thisElem.parent().find('.original-copy').html();

                    thisElem.css('opacity', '1.0');
                    thisElem.html(originalCopy);
                }

                var container_w = thisElem.parent().width();

                if (thisElem.data('link')) {

                    var omissionText = 'See More';

                    //for screen reader only append
                    //provides screen reader with addtional information in-link
                    if (thisElem.data('includename')) {

                        var nameTrunc = thisElem.data('includename');

                        //if the groupname is truncated, let's use that
                        var srprovider = thisElem.closest('.truncate-combo').find('[data-srprovider]');

                        if (srprovider.length) {
                            nameTrunc = srprovider.text();
                        }

                        omissionText = omissionText + ' <div class="sr-only sr-only-groupname">' + nameTrunc + '</div>';

                    }

                    var thisOmission = '<a href="' + thisElem.data('link') + '">' + omissionText + '</a>';
                } else {
                    var thisOmission = '';
                }

                if (container_w < truncationBaseWidth) {

                    var truncationValue = truncationBaseValue - (Math.round(((truncationBaseWidth - container_w) / truncationBaseWidth) * 100));
                    thisElem.find('.omission').remove();

                    if (!onInit) {
                        OpenLab.utility.truncateMainAction(thisElem, truncationValue, thisOmission);
                    }

                } else {

                    var truncationValue = truncationBaseValue;

                    if (!onInit) {
                        OpenLab.utility.truncateMainAction(thisElem, truncationValue, thisOmission);
                    }

                }

                if (onInit) {
                    OpenLab.utility.truncateMainAction(thisElem, truncationValue, thisOmission);
                }

                thisElem.animate({
                    opacity: '1.0'
                });

            });
        },
        truncateMainAction: function (thisElem, truncationValue, thisOmission) {

            if (thisElem.data('minvalue')) {
                if (truncationValue < thisElem.data('minvalue')) {
                    truncationValue = thisElem.data('minvalue');
                }
            }

            if (truncationValue > 10) {
                thisElem.succinct({
                    size: truncationValue,
                    omission: '<span class="omission">&hellip; ' + thisOmission + '</span>'
                });

                //if we have an included groupname in the screen reader only link text
                //let's truncate it as well
                if (thisElem.data('srprovider')) {
                    var srLink = thisElem.closest('.truncate-combo').find('.sr-only-groupname');
                    srLink.text(thisElem.text());
                }

            } else {
                thisElem.html('<span class="omission">' + thisOmission + '</span>');
            }

        },
        customSelects: function (resize) {
            //custom select arrows
            if (resize) {
                $('.custom-select-parent').html(OpenLab.utility.customSelectHTML);
                $('.custom-select select').select2({
                    minimumResultsForSearch: Infinity,
                    width: "100%",
                    escapeMarkup: function (text) {
                        return text;
                    }
                });
            } else {
                OpenLab.utility.customSelectHTML = $('.custom-select-parent').html();
                $('.custom-select select').select2({
                    minimumResultsForSearch: Infinity,
                    width: "100%",
                    escapeMarkup: function (text) {
                        return text;
                    }
                });
            }

            OpenLab.utility.filterAjax();

        },
        filterAjax: function () {

            //safety first
            $('#schoolSelect select').off('select2:select');

            //ajax functionality for courses archive
            $('#schoolSelect select').on('select2:select', function () {

                var school = $(this).val();
                var nonce = $('#nonce-value').text();

                //disable the dept dropdown
                $('#dept-select').attr('disabled', 'disabled');
                $('#dept-select').addClass('processing');
                $('#dept-select').html('<option value=""></option>');

                if (school == "" || school == "school_all") {
                    var defaultOption = '<option value="dept_all" selected="selected">All Departments</option>';
                    $('#dept-select').html(defaultOption);
                    $('#dept-select').trigger('render');
                    $('#select2-dept-select-container').text('All Departments');
                    $('#select2-dept-select-container').attr('title', 'All Departments');
                    return;
                }

                $.ajax({
                    type: 'GET',
                    url: ajaxurl,
                    data:
                            {
                                action: 'openlab_ajax_return_course_list',
                                school: school,
                                nonce: nonce
                            },
                    success: function (data, textStatus, XMLHttpRequest)
                    {
                        console.log('school', school);
                        $('#dept-select').removeAttr('disabled');
                        $('#dept-select').removeClass('processing');
                        $('#dept-select').html(data);
                        $('#dept-select').trigger('render');
                        $('#select2-dept-select-container').text('All Departments');
                        $('#select2-dept-select-container').attr('title', 'All Departments');
                    },
                    error: function (MLHttpRequest, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            });
        }
    };

    var related_links_count,
            $add_new_related_link,
            $cloned_related_link_fields;

    $(document).ready(function () {

        OpenLab.utility.init();

        // Workshop fields on Contact Us
        function toggle_workshop_meeting_items() {
            if (!!contact_us_topic) {
                if ('Request a Workshop / Meeting' == contact_us_topic.value) {
                    $workshop_meeting_items.slideDown('fast');
                } else {
                    $workshop_meeting_items.slideUp('fast');
                }
            }
        }

        function toggle_other_details() {
            if ('Other (please specify)' == $reason_for_request.val()) {
                $other_details.slideDown('fast');
            } else {
                $other_details.slideUp('fast');
            }
        }

        // + button on Related Links List Settings
        $add_new_related_link = $('#add-new-related-link');
        $add_new_related_link.css('display', 'inline-block');
        $add_new_related_link.on('click', function () {
            create_new_related_link_field();
        });

        var contact_us_topic = document.getElementById('contact-us-topic');
        $workshop_meeting_items = jQuery('#workshop-meeting-items');
        jQuery('#contact-us-topic').on('change', function () {
            toggle_workshop_meeting_items();
        });
        toggle_workshop_meeting_items();

        // Move the contact form output field to the bottom of the form.
        var contact_us_response_output = jQuery('.wpcf7-response-output');
        if (contact_us_response_output.length > 0) {
            contact_us_response_output.appendTo(contact_us_response_output.closest('form'));
        }

        $other_details = jQuery('#other-details');
        $reason_for_request = jQuery('#reason-for-request');
        $reason_for_request.on('change', function () {
            toggle_other_details();
        });
        toggle_other_details();

        jQuery('#wds-accordion-slider').easyAccordion({
            autoStart: true,
            slideInterval: 6000,
            slideNum: false
        });

        jQuery("#header #menu-item-40 ul li ul li a").prepend("+ ");

        // this add an onclick event to the "New Topic" button while preserving 
        // the original event; this is so "New Topic" can have a "current" class
        $('.show-hide-new').click(function () {
            var origOnClick = $('.show-hide-new').onclick;
            return function (e) {
                if (origOnClick != null && !origOnClick()) {
                    return false;
                }
                return true;
            }
        });

        window.new_topic_is_visible = $('#new-topic-post').is(":visible");
        $('.show-hide-new').click(function () {
            if (window.new_topic_is_visible) {
                $('.single-forum #message').slideUp(300);
                window.new_topic_is_visible = false;
            } else {
                $('.single-forum #message').slideDown(300);
                window.new_topic_is_visible = true;
            }
        });

        //printing page
        if ($('.print-page').length) {
            $('.print-page').on('click', function (e) {
                e.preventDefault();
                window.print();
            });
        }

        function clear_form() {
            document.getElementById('group_seq_form').reset();
        }

        //member profile friend/cancel friend hover fx
        if ($('.btn.is_friend.friendship-button').length) {
            var allButtons = $('.btn.is_friend.friendship-button');
            allButtons.each(function () {
                var thisButton = $(this);
                var thisButtonHTML = $(this).html();
                thisButton.hover(function () {
                    thisButton.html('<span class="pull-left"><i class="fa fa-user"></i> Cancel Friend</span><i class="fa fa-minus-circle pull-right"></i>');
                }, function () {
                    thisButton.html(thisButtonHTML);
                });
            });
        }

        //member notificatoins page - injecting Bootstrap classes
        if ($('table.notification-settings').length) {
            $('table.notification-settings').each(function () {
                $(this).addClass('table');
            });
        }

        //clear login form
        if ($('#user-login').length) {
            $('#sidebar-user-login, #sidebar-user-pass').on('focus', function () {
                $(this).attr('placeholder', '');
            });
        }

    });//end document.ready

    $(window).on('resize', function (e) {

        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {

            OpenLab.utility.truncateOnTheFly();
            OpenLab.utility.adjustLoginBox();
            OpenLab.utility.customSelects(true);

            if ($('#home-new-member-wrap').length) {
                OpenLab.utility.setUpNewMembersBox(true);
            }

        }, 250);

    });

    $(window).load(function () {

        $('html').removeClass('page-loading');
        OpenLab.utility.detectZoom();
        OpenLab.utility.customSelects(false);
        OpenLab.utility.venueMapControl();
        OpenLab.utility.venueDropdownControl();

        //setting equal rows on homepage group list
        equal_row_height();

        //camera js slider on home
        if ($('.camera_wrap').length) {
            $('.camera_wrap').camera({
                autoAdvance: true,
                loader: 'none',
                fx: 'simpleFade',
                playPause: false,
                height: '295px',
                navigation: false,
                navigationHover: false,
                onLoaded: function () {

                    var cameraImages = $('.camera_wrap .camera_target');
                    var cameraSource = $('.camera_src');

                    //have to do this because on first load, the first image is not
                    //actually 'loaded' per se
                    if (!cameraImages.hasClass('fully-loaded')) {

                        cameraImages.addClass('fully-loaded');
                        cameraImages.find('.cameraCont .cameraSlide_0 img').attr('alt', cameraSource.find('div').eq(0).data('alt'));

                    } else {

                        var currentImage = cameraImages.find('.cameraCont .cameracurrent');
                        currentImage.find('img').attr('alt', cameraSource.find('div').eq(currentImage.index()).data('alt'));


                    }

                }
            });
        }

        if ($('#home-new-member-wrap').length) {
            OpenLab.utility.setUpNewMembersBox(false);
        }

    });

    $(document).ajaxComplete(function () {

        if ($('.wpcf7').length && !$('.wpcf7-mail-sent-ok').length) {
            $('.wpcf7-form-control-wrap').each(function () {
                var thisElem = $(this);
                if (thisElem.find('.wpcf7-not-valid-tip').text()) {

                    thisElem.remove('.wpcf7-not-valid-tip');

                    var thisText = 'Please enter your ' + thisElem.find('.wpcf7-form-control').attr('name');
                    var newValidTip = '<div class="bp-template-notice error" style="display: none;"><p>' + thisText + '</p></div>';

                    thisElem.prepend(newValidTip);
                    thisElem.find('.bp-template-notice.error').css('visiblity', 'visible').hide().fadeIn(550);

                }
            });
        }
        if ($('.wpcf7').length && $('.wpcf7-mail-sent-ok').length) {
            $('.wpcf7-form-control-wrap').each(function () {
                var thisElem = $(this);
                if (thisElem.find('.bp-template-notice.error')) {
                    thisElem.remove('.bp-template-notice.error');
                }
            });
        }

    });

    function create_new_related_link_field() {
        $cloned_related_link_fields = $add_new_related_link.closest('li').clone();

        // Get count of existing link fields for the iterator
        related_links_count = $('.related-links-edit-items li').length + 1;

        // Swap label:for and input:id attributes
        $cloned_related_link_fields.html(function (i, old_html) {
            return old_html.replace(/(related\-links\-)[0-9]+\-(name|url)/g, '$1' + related_links_count + '-$2');
        });

        // Swap name iterator
        $cloned_related_link_fields.html(function (i, old_html) {
            return old_html.replace(/(related\-links\[)[0-9]+(\])/g, '$1' + related_links_count + '$2');
        });

        // Remove current button from the DOM, as the cloned fields contain the new one
        $add_new_related_link.remove();

        // Add new fields to the DOM
        $('.related-links-edit-items').append($cloned_related_link_fields);

        // Remove values
        $('#related-links-' + related_links_count + '-name').val('');
        $('#related-links-' + related_links_count + '-url').val('');

        // Reindex new Add button and bind click event
        $add_new_related_link = $('#add-new-related-link');
        $add_new_related_link.on('click', function () {
            create_new_related_link_field();
        });
    }

    /*this is for the homepage group list, so that cells in each row all have the same height 
     - there is a possiblity of doing this template-side, but requires extensive restructuring of the group list function*/
    function equal_row_height() {
        /*first we get the number of rows by finding the column with the greatest number of rows*/
        var $row_num = 0;
        $('.activity-list').each(function () {
            var $row_check = $(this).find('.activity-item').length;

            if ($row_check > $row_num) {
                $row_num = $row_check;
            }
        });

        //build a loop to iterate through each row
        var $i = 1;

        while ($i <= $row_num) {
            //check each cell in the row - find the one with the greatest height
            var $greatest_height = 0;

            $('.row-' + $i).each(function () {
                var $cell_height = $(this).outerHeight();

                if ($cell_height > $greatest_height) {
                    $greatest_height = $cell_height;
                }

            });

            //now apply that height to the other cells in the row
            $('.row-' + $i).css('height', $greatest_height + 'px');

            //iterate to next row
            $i++;
        }

        //there is an inline script that hides the lists from the user on load (just so the adjusment isn't jarring) - this will show the lists
        $('.activity-list').css('visibility', 'visible').hide().fadeIn(700);

    }

})(jQuery);
