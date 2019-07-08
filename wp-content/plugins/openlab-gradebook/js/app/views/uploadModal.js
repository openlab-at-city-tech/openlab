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
			$("body").append(this.render().el);
			this.model = options.model;
			this.course = options.course;
			this.gradebook = options.gradebook;
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
			return self.el;
		},
		downloadCSV: function(e) {
			e.preventDefault();

			this.course.export2csv();
		},
		uploadCSV: function(e) {
			e.preventDefault();

			var view = new uploadCSV({
				course: this.course,
				gradebook: this.gradebook
			});

			$("body").append(view.render());
		},
		editCancel: function() {
			this.$el.data("modal", null);
			this.remove();
			return false;
		},
	});

	return uploadModal;
});
