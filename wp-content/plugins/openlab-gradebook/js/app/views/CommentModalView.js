define([
	"jquery",
	"backbone",
	"underscore",
	"models/User",
	"models/UserList",
	"bootstrap3-typeahead"
], function($, Backbone, _, User, UserList, typeahead) {
	var CommentView = Backbone.View.extend({
		id: "base-modal",
		className: "modal fade",
		events: {
			"hidden.bs.modal": "editCancel",
			"click #edit-comment": "editSave"
		},
		initialize: function(options) {
			this.model = options.model;
			console.log("this.model on comment modal", this.model);
			this.gradebook = options.gradebook;
		},
		render: function() {
			var self = this;
            var template = _.template($("#comment-modal-template").html());
            
            console.log('self.gradebook on render modal', self.gradebook);

			var compiled = template({
                comments: self.model.get("comments"),
                gradebook: self.gradebook
			});
			this.$el.html(compiled);
			this.$el.modal("show");
			return this.el;
		},
		editCancel: function() {
			this.$el.data("modal", null);
			this.remove();
			return false;
		},
		editSave: function(ev) {
			ev.preventDefault();
			var thisElem = this.$el;

			thisElem.find(".dashicons-image-rotate").removeClass("hidden");
			thisElem.find(".button-text").text("Saving...");
			thisElem.find("#edit-comment").attr("disabled", "disabled");
			var comments = $("#comment").val();
			this.model.set({
				comments: comments
			});
			console.log("this.model", this.model);
			var cell = this.model.attributes;

			$.ajax({
				url:
					ajaxurl +
					"?action=cell&id=" +
					this.model.get("id") +
					"&nonce=" +
					oplbGradebook.nonce,
				method: "POST",
				data: cell,
				dataType: "json"
			})
				.done(function(data, textStatus, jqXHR) {
					console.log("success", data, textStatus, jqXHR, thisElem);
					thisElem.find(".dashicons-image-rotate").addClass("hidden");
					thisElem.find(".button-text").text("Save");
					thisElem.find("#edit-comment").removeAttr("disabled", "disabled");
				})
				.fail(function(jqXHR, textStatus, errorThrown) {
					console.log("error", jqXHR, textStatus, errorThrown);
				});
		}
	});

	return CommentView;
});
