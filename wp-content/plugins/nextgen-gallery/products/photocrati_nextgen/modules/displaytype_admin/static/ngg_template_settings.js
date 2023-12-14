jQuery(function($) {
    const $template_fields = $('.ngg_settings_template');
    $template_fields.each(function() {
        const $this = $(this);

        const template_field = $this.parents('tr').siblings('tr[id$="display_view"]');

        $this.select2({
            placeholder: ngg_template_settings.placeholder_text,
            allowClear: true,
            width: 350
        });

        $this.on('change', function(event) {
            const val = $this.val();
            if (val.match(/.*\/modules\/ngglegacy\/view\/gallery-carousel\.php/)) {
                template_field.find('select').val('carousel-view.php');
                $this.val(null).trigger('change');
            }
            if (val.match(/.*\/modules\/ngglegacy\/view\/gallery-caption\.php/)) {
                template_field.find('select').val('caption-view.php');
                $this.val(null).trigger('change');
            }
            if (val.match(/.*\/modules\/ngglegacy\/view\/gallery\.php/)) {
                template_field.find('select').val('default-view.php');
                $this.val(null).trigger('change');
            }
            if (val.match(/.*\/modules\/ngglegacy\/view\/album-compact\.php/)) {
                template_field.find('select').val('default-view.php');
                $this.val(null).trigger('change');
            }
            if (val.match(/.*\/modules\/ngglegacy\/view\/album-extend\.php/)) {
                template_field.find('select').val('default-view.php');
                $this.val(null).trigger('change');
            }
        });

    });

});