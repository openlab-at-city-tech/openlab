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
			"click #edit-comment": "editSave",
			"click #clear-comment" : "clearComment"
		},
		initialize: function(options) {
			this.model = options.model;
			this.gradebook = options.gradebook;
			this.type = options.type;
			this.name = options.name;
			this.username = options.username;
			console.log("this.model in comment modal", this.model);
			console.log("this.type in comment modal", this.type);
			this.gbid = parseInt(this.gradebook.get("gbid"));
		},
		render: function() {
			var self = this;
			var template = _.template($("#comment-modal-template").html());
			this.comment = self.model.get("comments");

			if(this.type === 'mid_semester'){
				this.comment = self.model.get("mid_semester_comments");
			} else if (this.type === "final"){
				this.comment = self.model.get("final_comments");
			}

			var compiled = template({
				comments: this.comment,
				gradebook: self.gradebook,
				username: this.username,
				name: this.name
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
			var self = this;
			var thisElem = this.$el;

			thisElem.find(".dashicons-image-rotate").removeClass("hidden");
			thisElem.find(".button-text").text("Saving...");
			thisElem.find("#edit-comment").attr("disabled", "disabled");
			var comments = $("#comment").val();
			var cell = this.model.attributes;

			if (this.comments !== comments) {
				cell.commentsUpdate = true;
			}

			cell.comments = comments;
			console.log("cell before save", cell);

			if (this.type === "cell") {
				$.ajax({
					url:
						ajaxurl +
						"?action=cell&id=" +
						this.model.get("id") +
						"&comment_edit=1" +
						"&nonce=" +
						oplbGradebook.nonce,
					method: "POST",
					data: cell,
					dataType: "json"
				})
					.done(function(data, textStatus, jqXHR) {
						thisElem.find(".dashicons-image-rotate").addClass("hidden");
						thisElem.find(".button-text").text("Save");
						thisElem.find("#edit-comment").removeAttr("disabled", "disabled");
						self.updateModel();
					})
					.fail(function(jqXHR, textStatus, errorThrown) {
						console.log("error", jqXHR, textStatus, errorThrown);
					});
			} else {
				var grade = this.model.get("mid_semester_grade");

				if (this.type === "final") {
					grade = this.model.get("final_grade");
				}

				var uid = parseInt(this.model.get("uid"));
				var comments = this.model.get('comments');

				$.ajax({
					url:
						ajaxurl +
						"?action=oplb_student_grades&comment_edit=1" +
						"&nonce=" +
						oplbGradebook.nonce,
					method: "POST",
					data: { grade: grade, type: this.type, gbid: this.gbid, uid: uid, comments: comments},
					dataType: "json"
				})
					.done(function(data, textStatus, jqXHR) {
						thisElem.find(".dashicons-image-rotate").addClass("hidden");
						thisElem.find(".button-text").text("Save");
						thisElem.find("#edit-comment").removeAttr("disabled", "disabled");
						self.updateModel();
						Backbone.pubSub.trigger("editSuccess", data);
					})
					.fail(function(jqXHR, textStatus, errorThrown) {
						self.trigger("editError");
					});
			}
		},
		clearComment: function(){

			this.$el.find('textarea').val('');

		},
		updateModel: function() {
			var comments = $("#comment").val();

			if (this.type === 'mid_semester') {
				this.model.set({ comments: comments, mid_semester_comments: comments, commentsUpdate: false });
			} else if (this.type === 'final'){
				this.model.set({ comments: comments, final_comments: comments, commentsUpdate: false });
			} else {
				this.model.set({ comments: comments, commentsUpdate: false });
			}
			
			console.log("this model", this.model);
		}
	});

	return CommentView;
});
