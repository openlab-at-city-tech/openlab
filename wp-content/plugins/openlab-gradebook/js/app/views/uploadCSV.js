define([
	"jquery",
	"backbone",
	"underscore",
	"models/User",
	"models/UserList",
	"bootstrap3-typeahead"
], function($, Backbone, _, User, UserList, typeahead) {
	var newGradebook = {};
	var error = "";
	var uploadModal = Backbone.View.extend({
		id: "upload-csv",
		className: "modal fade",
		events: {
			"shown.bs.modal": "renderUploader",
			"hidden.bs.modal": "editCancel",
			"click #uploadExternal": "uploadFile"
		},
		initialize: function(options) {
			this.course = options.course;
			this.gradebook = options.gradebook;
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
			var self = this;

			$("#upload-csv-input")
				.fileinput({
					uploadUrl:
						oplbGradebook.ajaxURL +
						"?action=oplb_gradebook_upload_csv&nonce=" +
						oplbGradebook.nonce +
						"&gbid=" +
						this.course.get("id"),
					maxFileCount: 1,
					hideThumbnailContent: true,
					showClose: false,
					msgUploadThreshold: "Adding to Gradebook...",
					layoutTemplates: {
						main1:
							"{preview}\n" +
							'<div class="kv-upload-progress kv-hidden"></div><div class="clearfix"></div>\n' +
							'<div class="input-group {class}">\n' +
							"  {caption}\n" +
							'<div class="input-group-btn input-group-append">\n' +
							"      {remove}\n" +
							"      {cancel}\n" +
							"      {pause}\n" +
							"      {browse}\n" +
							"    </div>\n" +
							"</div>",
						actions:
							'<div class="file-actions">\n' +
							'    <div class="file-footer-buttons">\n' +
							"        {zoom} {other}" +
							"    </div>\n" +
							"</div>\n" +
							"{drag}\n" +
							'<div class="clearfix"></div>',
						indicator: ""
					}
				})
				.on("fileselect", function(e, numfiles, label) {
					console.log("fileselect");
					error = "";
					self.$el.find("#upload-csv-error-message").remove();
					$('#upload-csv .modal-content').addClass('upload-active');
				})
				.on("fileuploaded", function(e, params) {
					$(".file-input").html(
						params.response.message
					);
					newGradebook = params.response.content;
					$("#upload-csv-input").fileinput("clear");
					$('#upload-csv .modal-content').addClass('upload-successful');
					self.updateGradebook();
				})
				.on("filecleared", function(e) {
					console.log("filecleared");
					$('#upload-csv .modal-content').removeClass('upload-active');
					if (error !== "") {
						self.$el.find(".file-input .kv-fileinput-error").after(error);
					}
				})
				.on("fileuploaderror", function(e, params) {
					error = params.response.error;
					$("#upload-csv-input").fileinput("clear");
				});
		},
		editCancel: function() {
			this.$el.data("modal", null);
			this.remove();
			Backbone.pubSub.trigger("closeUploadCSV", newGradebook);
			return false;
		},
		updateGradebook: function() {
			console.log("self.newGradebook in updateGradebook", newGradebook);
			Backbone.pubSub.trigger("newGradebookCSV", newGradebook);
		},
		uploadFile: function() {
			console.log('go uploadFile');
			$("#upload-csv-input").fileinput("upload");
		}
	});

	return uploadModal;
});
