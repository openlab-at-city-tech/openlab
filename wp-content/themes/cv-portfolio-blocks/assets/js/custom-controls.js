(function(api) {

    api.sectionConstructor['cv-portfolio-blocks-upsell'] = api.Section.extend({
        attachEvents: function() {},
        isContextuallyActive: function() {
            return true;
        }
    });

    const cv_portfolio_blocks_section_lists = ['banner', 'service'];
    cv_portfolio_blocks_section_lists.forEach(cv_portfolio_blocks_homepage_scroll);

    function cv_portfolio_blocks_homepage_scroll(item, index) {
        item = item.replace(/-/g, '_');
        wp.customize.section('cv_portfolio_blocks_' + item + '_section', function(section) {
            section.expanded.bind(function(isExpanding) {
                wp.customize.previewer.send(item, { expanded: isExpanding });
            });
        });
    }
})(wp.customize);