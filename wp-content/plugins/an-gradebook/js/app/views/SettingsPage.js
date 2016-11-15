define(['jquery','backbone','underscore'],
function($,Backbone,_){
	var SettingsPage = Backbone.View.extend({
		initialize: function(options){
			this.gradebook_administrators = options.gradebook_administrators;	
			this.render();		
		},
		events: {
			'click input[type=checkbox]' : 'setAdministratorsValue',
			'submit #an-gradebook-settings-form' : 'saveAdministrators'
		},
		render: function(){
			var self = this;
			var template = _.template($('#an-gradebook-settings-template').html());
			var compiled = template({gradebook_administrators : this.gradebook_administrators});
			$('#wpbody-content').prepend(this.$el.html(compiled));	
			this.setInitialAdministratorsValue();	
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
			var self = this;		
			ev.preventDefault();
			var result = $('#an-gradebook-settings :input').serializeObject();
			result = _.mapObject(result, function(value,id_key){
					return value === 'true' ? true : false;
			});
			old_roles = _.mapObject(this.gradebook_administrators.attributes,function(value,role){
					return false;
			});
			result = _.extend(old_roles,result);			
			result.action = 'save';
			this.gradebook_administrators.save(result,{
				success: function(model){
					model.unset('action');
					self.render();
				}
			});
		}
    });
	return SettingsPage;
});