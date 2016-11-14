define(['jquery','backbone','underscore','models/Settings'],
function($,Backbone,_,Settings){
	var ImportStudentsPage = Backbone.View.extend({
		initialize: function(){
			var self = this;
			this.gradebook_administrators = new Settings({});
			this.gradebook_administrators.fetch({
				success: function(model){
					self.gradebook_administrators.set(model.get('gradebook_administrators'));			
					self.render().setInitialAdministratorsValue();				
				}
			});
		},
		events: {
			'click input[type=checkbox]' : 'setAdministratorsValue',
			'submit #an-gradebook-settings-form' : 'saveAdministrators'
		},
		render: function(){
			var template = _.template($('#an-gradebook-import-students-template').html());
			var compiled = template({gradebook_administrators : this.gradebook_administrators});
			$('#wpbody-content').append(this.$el.html(compiled));
			return this;
		},
		setInitialAdministratorsValue: function(){	
			_.each(this.gradebook_administrators.attributes,function(value, id_name){
				$('#'+id_name).prop('checked',value);
			});		
		},		
		setAdministratorsValue: function(ev){
			var _ev = ev.currentTarget
			if($(_ev).prop('checked')){
				$(_ev).attr('value',true);
			} else {
				$(_ev).attr('value',false);			
			}
			$('#an-gradebook-settings-form').trigger('submit');
		},
		saveAdministrators: function(ev){
			ev.preventDefault();
			var self = this;
			var result = $(ev.currentTarget).serializeObject();
			result.action = 'save';
			this.gradebook_administrators.clear().set(result);
			this.gradebook_administrators.save({
				success: function(model){
					model.unset('action');
					self.gradebook_administrators.clear().set(model.get('gradebook_administrators'));
					self.render();
				}
			});
		}
    });
	return ImportStudentsPage;
});