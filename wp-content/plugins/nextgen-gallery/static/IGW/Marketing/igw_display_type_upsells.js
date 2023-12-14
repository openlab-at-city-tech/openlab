igw_data.display_types = igw_data.display_types.concat(igw_display_type_upsells.display_types)

jQuery(window).on('ngg_before_igw_render', () => {
    const orig = Ngg.DisplayTab.Views.DisplayType

    Ngg.DisplayTab.Views.IGW_Upsell = Backbone.View.extend({
        tagName: 'div',
        className: 'display_type_preview',
        render: function(){
            this.$el.append(igw_display_type_upsells.igw_promo)                                                                                                                                                    
            return this;
        }
    })

    Ngg.DisplayTab.Views.DisplayType = Ngg.DisplayTab.Views.DisplayType.extend({
        // Override the clicked event to show a popup if a upsell is clicked
        clicked: function(e) {
            var displayType = this
            var displayTypeName = this.model.get('name')

            if (!igw_display_type_upsells.display_types.filter(upsell => upsell.name == displayTypeName).length) 
                this.trigger('selected', this.model.get('name'));
            else {
                e.preventDefault();
                jQuery(igw_display_type_upsells.templates[displayTypeName]).modal();
                return false;
            }
        },

        render: function() {
            if (this.model.get('name') == 'igw-promo') {
                const igw_upsell = new Ngg.DisplayTab.Views.IGW_Upsell()
                return igw_upsell.render();
            }

            return orig.prototype.render.call(this)
        }
    })
})