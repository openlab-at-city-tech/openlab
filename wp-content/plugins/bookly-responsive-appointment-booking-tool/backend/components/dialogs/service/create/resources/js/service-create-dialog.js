jQuery(function ($) {
    'use strict';

    var $modal        = $('#bookly-create-service-modal'),
        $serviceTitle = $('#bookly-new-service-title', $modal),
        $serviceType  = $('#bookly-new-service-type', $modal),
        $saveBtn      = $('.bookly-js-save', $modal),
        $servicesList = $('#services-list')
    ;

    function format(option) {
        return option.id && option.element.dataset.icon ? '<i class="far fa-fw ' + option.element.dataset.icon + '"></i> ' + option.text : option.text;
    }

    $serviceType.booklySelect2({
        minimumResultsForSearch: -1,
        width: '100%',
        theme: 'bootstrap4',
        dropdownParent: '#bookly-tbs',
        allowClear: false,
        templateResult: format,
        templateSelection: format,
        escapeMarkup: function (m) {
            return m;
        }
    });
    $modal.on('shown.bs.modal', function () {
        $serviceTitle.focus();
    });
    $saveBtn.on('click', function (e) {
        e.preventDefault();
        let ladda = Ladda.create(this);
        ladda.start();
        $.post(ajaxurl, booklySerialize.buildRequestDataFromForm('bookly_create_service', $modal), function (response) {
            if (response.success) {
                $servicesList.DataTable().ajax.reload();
                $serviceTitle.val('');
                $serviceType.val('simple').trigger('change');
                $modal.booklyModal('hide');

                BooklyServiceOrderDialogL10n.services.push({id: response.data.id, title: response.data.title});
                $(document.body).trigger('service.edit', [response.data.id]);
            } else {
                requiredBooklyPro();
            }
            ladda.stop();
        });
    });
});