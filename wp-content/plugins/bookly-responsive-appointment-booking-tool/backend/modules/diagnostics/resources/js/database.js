jQuery(function ($) {
    "use strict"

    function executeDatabaseJob(job) {
        return $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bookly_diagnostics_ajax',
                tool: 'Database',
                ajax: 'executeJob',
                job: job,
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    booklyAlert({success: [response.data.message]});
                } else {
                    booklyAlert({error: [response.data.message]});
                }
            },
            error: function(XHR) {
                booklyAlert({error: ['Status code: ' + XHR.status]});
            }
        });
    }

    $('[data-action=drop-column]').on('click', function (e) {
        e.preventDefault();
        let $li = $(this).closest('li'),
            table = $li.closest('.card').data('table'),
            column = $li.data('column'),
            job = $li.data('job'),
            text = 'If there are foreign keys for <b>' + column +  '</b>, they will be dropped with the column.<br><br>',
            query = 'ALTER TABLE `' + table + '`\nDROP COLUMN `' + column + '`'
        ;

        booklyModal('Drop column: ' + column, text + $('<pre>').html(query).prop('outerHTML'), 'Cancel', 'Drop')
            .on('bs.click.main.button', function (event, modal, mainButton) {
                let ladda = Ladda.create(mainButton);
                ladda.start();
                executeDatabaseJob(job).then(function (response) {
                    if (response.success) {
                        modal.booklyModal('hide');
                        hideDatabaseItem($li);
                    }
                }).always(function () {
                    ladda.stop();
                });
            });
    });

    $('[data-action=drop-constraint]').on('click', function (e) {
        e.preventDefault();
        let $li = $(this).closest('li'),
            table = $li.closest('.card').data('table'),
            key = $li.data('key'),
            job = $li.data('job'),
            query = '     ALTER TABLE `' + table + '`\nDROP FOREIGN KEY `' + key + '`'
        ;

        booklyModal('Drop foreign key', $('<pre>').html(query), 'Cancel', 'Drop')
            .on('bs.click.main.button', function (event, modal, mainButton) {
                let ladda = Ladda.create(mainButton);
                ladda.start();
                executeDatabaseJob(job).then(function (response) {
                    if (response.success) {
                        modal.booklyModal('hide');
                        hideDatabaseItem($li);
                    }
                }).always(function () {
                    ladda.stop();
                });
            });
    });

    $('[data-action=add-constraint]').on('click', function (e) {
        e.preventDefault();
        let $li = $(this).closest('li'),
            table = $li.closest('.card').data('table'),
            data = $li.data('data'),
            job = $li.data('job'),
            query = '   ALTER TABLE `' + table + '`\nADD CONSTRAINT\n   FOREIGN KEY (`' + data.column + '`)\n    REFERENCES `' + data.ref_table_name + '` (`' + data.ref_column_name + '`)\n     ON DELETE ' + data.rules.DELETE_RULE + '\n     ON UPDATE ' + data.rules.UPDATE_RULE
        ;

        let $modal = booklyModal('Add constraint', $('<pre>').html(query), 'Close', 'Add')
            .on('bs.click.main.button', function (event, modal, mainButton) {
                let ladda = Ladda.create(mainButton);
                ladda.start();
                executeDatabaseJob(job).then(function (response) {
                    if (response.success) {
                        modal.booklyModal('hide');
                        hideDatabaseItem($li);
                    } else {
                        let $updateBtn =  $(mainButton).text('UPDATE…').attr( 'title', 'UPDATE ' + table + ' SET ' + data.column + ' = NULL' ),
                            $deleteBtn = jQuery('<button>', {class: 'btn ladda-button btn-default', type: 'button', title: 'DELETE FROM ' + table, 'data-spinner-size': 40, 'data-style': 'zoom-in'})
                                .append('<span>', {class: 'ladda-label'}).text('DELETE…');

                        $deleteBtn
                            .on('click', function(e) {
                                e.stopPropagation();
                                modal.trigger('bs.click.delete.button', [modal, $deleteBtn.get(0)]);
                            });

                        $updateBtn
                            .off()
                            .on('click', function(e) {
                                e.stopPropagation();
                                modal.trigger('bs.click.update.button', [modal, $updateBtn.get(0)]);
                            }).removeClass('btn-success').addClass('btn-default');

                        $deleteBtn.insertBefore($updateBtn);

                        if (data.rules.hasOwnProperty('fix')) {
                            if (data.rules.fix.action === 'METHOD') {
                                let $customBtn = jQuery('<button>', {class: 'btn ladda-button btn-success', type: 'button', title: data.rules.fix.description, 'data-spinner-size': 40, 'data-style': 'zoom-in'})
                                    .append('<span>', {class: 'ladda-label'}).text(data.rules.fix.button);
                                $customBtn
                                    .on('click', function(e) {
                                        e.stopPropagation();
                                        modal.trigger('bs.click.custom.button', [modal, $customBtn.get(0)]);
                                    });
                                $customBtn.insertBefore($deleteBtn);
                            }
                        }
                        if (data.rules['DELETE_RULE'] === 'CASCADE') {
                            $deleteBtn.removeClass('btn-default').addClass('btn-success');
                        } else {
                            $updateBtn.removeClass('btn-default').addClass('btn-success');
                        }
                    }
                }).always(function () {
                    ladda.stop();
                });
            })
            .on('bs.click.delete.button', function (event, modal, mainButton) {
                modal.booklyModal('hide');
                let info = 'If you don\'t know what will happen after this query execution? Click cancel.<br><br>',
                    query = 'DELETE FROM `' + table + "`\n" + '      WHERE `' + data.column + '`\n     NOT IN ( SELECT `' + data.ref_column_name + '`\n                FROM `' + data.ref_table_name + '`\n             )';
                booklyModal('Delete from: ' + table, info + $('<pre>').html(query).prop('outerHTML'), 'Cancel', 'Delete')
                    .on('bs.click.main.button', function (event, modal, mainButton) {
                        let ladda = Ladda.create(mainButton);
                        ladda.start();
                        executeDatabaseJob(job + '~delete').then(function (response) {
                            if (response.success) {
                                modal.booklyModal('hide');
                                hideDatabaseItem($li);
                            }
                        }).always(function () {
                            ladda.stop();
                        });
                    });
            })
            .on('bs.click.update.button', function (event, modal, mainButton) {
                modal.booklyModal('hide');
                let info = 'If you don\'t know what will happen after this query execution? Click cancel.<br><br>',
                    query = 'UPDATE `' + table + "`\n" + '   SET `' +  data.column + '` = NULL' + "\n" + ' WHERE `' +  data.column + '`\nNOT IN ( SELECT `' + data.ref_column_name + '`\n           FROM `' + data.ref_table_name + '`\n        )';
                booklyModal('Update table: ' + table, info + $('<pre>').html(query).prop('outerHTML'), 'Cancel', 'Update')
                    .on('bs.click.main.button', function (event, modal, mainButton) {
                        let ladda = Ladda.create(mainButton);
                        ladda.start();
                        executeDatabaseJob(job + '~update').then(function (response) {
                            if (response.success) {
                                modal.booklyModal('hide');
                                hideDatabaseItem($li);
                            }
                        }).always(function () {
                            ladda.stop();
                        });
                    });
            })
            .on('bs.click.custom.button', function (event, modal, mainButton) {
                modal.booklyModal('hide');
                booklyModal('Custom action', data.rules.fix.description, 'Cancel', 'Execute')
                    .on('bs.click.main.button', function (event, modal, mainButton) {
                        let ladda = Ladda.create(mainButton);
                        ladda.start();
                        executeDatabaseJob(job + '~custom').then(function (response) {
                            if (response.success) {
                                modal.booklyModal('hide');
                                hideDatabaseItem($li);
                            }
                        }).always(function () {
                            ladda.stop();
                        });
                    });
            });

        $('.modal-dialog', $modal).addClass('modal-lg');
    });

    $('[data-action=fix-charset_collate-table]')
        .on('click', function (e) {
            e.preventDefault();
            let $button = $(this),
                table = $button.closest('.card').data('table'),
                query = '    ALTER TABLE `' + table + '`\n',
                job = $button.data('job'),
                title
            ;

            switch ($button.attr('data-fix')) {
                case '["character_set","collate"]':
                    title = 'Fix CHARACTER SET and COLLATION'
                    query += '  CHARACTER SET ' + $button.data('charset') + '\n        COLLATE ' + $button.data('collate') + ';'
                    break;
                case '["character_set"]':
                    title = 'Fix CHARACTER SET'
                    query += '  CHARACTER SET ' + $button.data('charset') + ';'
                    break;
            }

            booklyModal(title, $('<pre>').html(query), 'Cancel', 'Fix')
                .on('bs.click.main.button', function (event, modal, mainButton) {
                    let ladda = Ladda.create(mainButton);
                    ladda.start();
                    executeDatabaseJob(job).then(function (response) {
                        if (response.success) {
                            modal.booklyModal('hide');
                            let $button_container = $button.closest('div');
                            $button_container.next('div').remove();
                            $button_container.remove();
                        }
                    }).always(function () {
                        ladda.stop();
                    });
                });
        });

    $('#bookly-fix-all-silent').on('click', function () {
        booklyModal('Confirmation', 'Execute automatic fixing issues found in database schema?', 'Cancel', 'Fix')
            .on('bs.click.main.button', function (event, modal, mainButton) {
                let ladda = Ladda.create(mainButton);
                ladda.start();
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'bookly_diagnostics_ajax',
                        tool: 'Database',
                        ajax: 'fixDatabaseSchema',
                        csrf_token: BooklyL10nGlobal.csrf_token
                    },
                    dataType: 'json',
                    success: function (response) {
                        booklyAlert({success: [response.data.message]});
                        if (!response.success) {
                            booklyAlert({error: response.data.errors});
                        }
                        ladda.stop();
                    },
                    error: function () {
                        booklyAlert({error: ['Error: in query execution.']});
                        ladda.stop();
                    },
                }).always(function () {
                    modal.booklyModal('hide');
                    let $modal = booklyModal('Are you sure you want to reload this page?', null, 'No', 'Reload')
                        .on('bs.click.main.button', function (event, modal, mainButton) {
                            location.reload();
                        });
                    setTimeout(function () {
                        $('.modal-footer .btn-success', $modal).focus();
                    }, 500);
                });
            });
    });

    function hideDatabaseItem($item) {
        if ($item.siblings('li').length === 0) {
            let $tableCard = $item.closest('div[data-table]');
            $item.closest('div').remove();
            if ($('.list-group', $tableCard).length === 0) {
                $tableCard.remove();
            }
        } else {
            $item.remove();
        }
    }
});