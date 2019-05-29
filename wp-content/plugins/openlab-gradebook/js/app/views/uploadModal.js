define([
	"jquery",
	"backbone",
	"underscore",
	"models/User",
	"models/UserList",
	"bootstrap3-typeahead"
], function($, Backbone, _, User, UserList, typeahead) {
	var uploadModal = Backbone.View.extend({
		id: "upload-modal",
		className: "modal fade",
		events: {
			"shown.bs.modal": "renderUploader"
		},
		initialize: function(options) {
			$("body").append(this.render().el);
			this.course = options.course;
			return this;
		},
		render: function() {
			var self = this;
			var template = _.template($("#upload-modal").html());
			var compiled = template({});
			self.$el.html(compiled);
			this.$el.modal("show");
			return self.el;
		},
		renderUploader: function() {
            console.log('renderUploader', oplbGradebook, oplbGradebook.ajaxURL + '?action=oplb_gradebook_upload_csv&nonce=' + oplbGradebook.nonce);
			$("#upload-csv-input").fileinput({
				uploadUrl: oplbGradebook.ajaxURL + '/?action=oplb_gradebook_upload_csv&nonce=' + oplbGradebook.nonce + '&gbid=' + this.course.get('id'),
				maxFileCount: 1
			}).on('fileuploaded', function(e, params) {
				console.log('file uploaded', e, params);
			});
		}
	});

	return uploadModal;
});
