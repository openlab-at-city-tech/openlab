define([
	"jquery",
	"backbone",
	"underscore",
	"views/CommentModalView",
	"models/User",
	"models/UserList",
	"bootstrap3-typeahead"
], function($, Backbone, _, CommentModalView, User, UserList, typeahead) {
	var CommentView = Backbone.View.extend({
		tagName: "span",
		className: "comment-icon",
		events: {
			"click button.add-comment": "addComment"
		},
		initialize: function(options) {
			this.model = options.model;
			this.gradebook = options.gradebook;
			this.type = options.type;
			this.name = options.name;
			this.username = options.username;
		},
		render: function(newModal) {
			if (newModal !== undefined) {
				this.model = newModal;
			}

			var statusClass = this.model.get("comments") ? "active" : "inactive";

			if (this.type === "mid_semester") {
				console.log('this in render mid_semester comment', this);
				statusClass = this.model.get("mid_semester_comments")
					? "active"
					: "inactive";
			} else if (this.type === "final") {
				statusClass = this.model.get("final_comments") ? "active" : "inactive";
			}

			this.$el.removeClass("active inactive");
			this.$el.addClass(statusClass);
			var template = _.template($("#comment-icon-template").html());
			var compiled = template({});
			this.$el.html(compiled);
			return this.el;
		},
		addComment: function(ev) {

			var view = new CommentModalView({
				model: this.model,
				gradebook: this.gradebook,
				name: this.name,
				username: this.username,
				type: this.type
			});
			$("body").append(view.render());
			this.listenTo(view.model, "change", this.render, {
				newModal: view.model
			});
		}
	});

	return CommentView;
});
