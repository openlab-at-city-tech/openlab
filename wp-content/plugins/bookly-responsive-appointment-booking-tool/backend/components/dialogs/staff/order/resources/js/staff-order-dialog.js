jQuery(function($) {
    'use strict';

    $(document.body)
        .on('staff.saved', {},
            function(event, tab, staffData) {
                if (tab == 'staff-details') {
                    let staff = BooklyStaffOrderDialogL10n.staff
                        .find(function(s) { return s.id == staffData.id; });

                    if (staff === undefined) {
                        BooklyStaffOrderDialogL10n.staff.push({id: staffData.id, full_name: staffData.full_name})
                    } else {
                        staff.full_name = staffData.full_name;
                    }
                }
            })
        .on('staff.deleted', {},
            function(event, staff) {
                staff.forEach(function(id) {
                    BooklyStaffOrderDialogL10n.staff.forEach(function(s, index) {
                        if (s.id === parseInt(id)) {
                            BooklyStaffOrderDialogL10n.staff.splice(index, 1);
                        }
                    });
                });
            });

    var $dialog = $('#bookly-staff-order-modal'),
        $list = $('#bookly-list', $dialog),
        $template = $('#bookly-staff-template'),
        $table = $('#services-list'),
        $save = $('#bookly-save', $dialog)
    ;

    // Save categories
    $save.on('click', function(e) {
        e.preventDefault();
        let ladda = Ladda.create(this),
            staff = [],
            list = [];
        ladda.start();
        $('li', $list).each(function(index, elem) {
            let id = $('[name="id"]', $(elem)).val();
            staff.push(id);
            list.push({id: id, full_name: $('.bookly-js-full_name', $(elem)).html()});
        });

        $.post(ajaxurl, booklySerialize.buildRequestData('bookly_update_staff_positions', {staff: staff}),
            function(response) {
                if (response.success) {
                    $dialog.booklyModal('hide');
                    BooklyStaffOrderDialogL10n.staff = list;
                }
                ladda.stop();
            });
    });

    $dialog.off().on('show.bs.modal', function() {
        $list.html('');
        BooklyStaffOrderDialogL10n.staff.forEach(function(staff) {
            $list.append(
                $template.clone().show().html()
                    .replace(/{{id}}/g, staff.id)
                    .replace(/{{full_name}}/g, staff.full_name + (staff.archived == '1' ? ' — <i>' + BooklyStaffOrderDialogL10n.archived + '</i>': ''))
                    .replace(/{{class}}/g, staff.archived == '1'?' text-muted':'')
            );
        });
    });

    Sortable.create($list[0], {
        handle: '.bookly-js-draghandle',
    });
    $('[data-target="#bookly-staff-order-modal"]').prop('disabled', false);
});