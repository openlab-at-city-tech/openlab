define([
	"jquery",
	"backbone",
	"underscore",
	"models/User",
	"models/UserList",
	"bootstrap3-typeahead"
], function($, Backbone, _, User, UserList, typeahead) {
	var uploadModal = Backbone.View.extend({
		id: "upload-csv",
		className: "modal fade",
		events: {
			"shown.bs.modal": "renderUploader",
			"hidden.bs.modal": "editCancel"
		},
		initialize: function(options) {
			$("body").append(this.render().el);
			this.course = options.course;
			return this;
		},
		render: function() {
			var self = this;
			var template = _.template($("#upload-csv").html());
			var compiled = template({});
			self.$el.html(compiled);
			this.$el.modal("show");
			return self.el;
		},
		renderUploader: function() {
			$("#upload-csv-input")
				.fileinput({
					uploadUrl:
						oplbGradebook.ajaxURL +
						"/?action=oplb_gradebook_upload_csv&nonce=" +
						oplbGradebook.nonce +
						"&gbid=" +
						this.course.get("id"),
					maxFileCount: 1,
					hideThumbnailContent: true,
				})
				.on("fileuploaded", function(e, params) {
					console.log("file uploaded", e, params);
				});
		},
		editCancel: function() {
			this.$el.data("modal", null);
			this.remove();
			return false;
		}
	});

	return uploadModal;
});
