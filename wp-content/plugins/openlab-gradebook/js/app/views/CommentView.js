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
		},
		render: function() {
			var self = this;
			var template = _.template($("#comment-icon-template").html());
			var compiled = template({});
			this.$el.html(compiled);
			return this.el;
		},
		addComment: function(ev) {
			console.log('this.gradebook', this.gradebook);
			var view = new CommentModalView({
				model: this.model,
				gradebook: this.gradebook
			});
			$("body").append(view.render());
		}
	});

	return CommentView;
});
