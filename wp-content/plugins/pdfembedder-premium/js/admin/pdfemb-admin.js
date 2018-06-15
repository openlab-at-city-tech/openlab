
jQuery(document).ready(function() {

    function pdfembSetActionToTab(id) {
        var frm = jQuery('#pdfemb_form');
        frm.attr('action', frm.attr('action').replace(/(#.+)?$/, '#' + id));
    }

    jQuery('#pdfemb-tabs').find('a').click(function () {
        jQuery('#pdfemb-tabs').find('a').removeClass('nav-tab-active');
        jQuery('.pdfembtab').removeClass('active');
        var id = jQuery(this).attr('id').replace('-tab', '');
        jQuery('#' + id + '-section').addClass('active');
        jQuery(this).addClass('nav-tab-active');

        // Set submit URL to this tab
        pdfembSetActionToTab(id);
    });

    // Did page load with a tab active?
    var active_tab = window.location.hash.replace('#', '');
    if (active_tab != '') {
        var activeSection = jQuery('#' + active_tab + '-section');
        var activeTab = jQuery('#' + active_tab + '-tab');

        if (activeSection && activeTab) {
            jQuery('#pdfemb-tabs').find('a').removeClass('nav-tab-active');
            jQuery('.pdfembtab').removeClass('active');

            activeSection.addClass('active');
            activeTab.addClass('nav-tab-active');
            pdfembSetActionToTab(active_tab);
        }
    }

});

