define([
	"jquery",
	"backbone",
	"underscore",
	"models/User",
	"models/UserList",
	"views/uploadCSV",
	"bootstrap3-typeahead"
], function($, Backbone, _, User, UserList, uploadCSV, typeahead) {
	var uploadModal = Backbone.View.extend({
		id: "upload-modal",
		className: "modal fade",
		events: {
			"hidden.bs.modal": "editCancel",
			"click button#modal-download-csv": "downloadCSV",
			"click button#modal-upload-csv": "uploadCSV"
		},
		initialize: function(options) {
			this.model = options.model;
			this.course = options.course;
			this.gradebook = options.gradebook;

			Backbone.pubSub.on("closeUploadCSV", this.removeSecondary, this);

			return this;
		},
		render: function(newGradebook) {
			console.log("newGradebook", newGradebook);
			if (newGradebook !== undefined) {
				this.model = newGradebook;
			}

			var self = this;
			var template = _.template($("#upload-modal").html());
			var compiled = template({});
			self.$el.html(compiled);
			this.$el.modal("show");
			console.log("this.$el in uploadModal", this.$el);
			return self.el;
		},
		downloadCSV: function(e) {
			e.preventDefault();

			$(".download-buttons")
				.addClass("success")
				.find("#modal-download-csv")
				.text("Download Again");

			this.course.export2csv();
		},
		uploadCSV: function(e) {
			e.preventDefault();
			var self = this;

			setTimeout(
				function() {
					self.$el.addClass("secondary-modal");
				},
				10,
				self
			);

			var view = new uploadCSV({
				course: this.course,
				gradebook: this.gradebook
			});

			$("body").append(view.render());
		},
		removeSecondary: function(){
			var self = this;
			$('body').addClass('modal-open');
			console.log('removeSecondary');

			setTimeout(
				function() {
					self.$el.removeClass("secondary-modal");
				},
				10,
				self
			);
		},
		editCancel: function() {
			console.log("editCancel");

			this.$el.data("modal", null);
			this.remove();
			return false;
		}
	});

	return uploadModal;
});
