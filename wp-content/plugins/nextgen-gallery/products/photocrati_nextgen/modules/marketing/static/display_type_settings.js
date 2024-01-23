var ourAddChangeListener = el => el.addEventListener('change', e => {
    e.preventDefault();
    showUpsell(el);
    return false;
});

var showUpsell = el => {
    const { upsells } = ngg_display_type_settings_marketing;
    const no_input = document.getElementById(el.id + '_no');
    no_input.setAttribute('checked', 'checked');
    no_input.checked = 'checked';
    jQuery(upsells[el.dataset.upsell]).modal();
};

[...document.querySelectorAll(".ngg_display_type_setting_marketing")].map(ourAddChangeListener);