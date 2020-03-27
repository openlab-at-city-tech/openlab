define(["backbone", "jquery"], function(Backbone, $) {
	var User = Backbone.Model.extend({
		defaults: {
			first_name: "john",
			last_name: "doe",
			selected: false,
			user_login: null
		},
		url: function() {
			if (this.get("id")) {
				return (
					ajaxurl +
					"?action=oplb_user&id=" +
					this.get("id") +
					"&gbid=" +
					this.get("gbid")
				);
			} else {
				return ajaxurl + "?action=oplb_user";
			}
		},
		updateStudentGrade: function(grade, type, uid, gbid) {
			var self = this;
			$.post(
				ajaxurl + "?action=oplb_student_grades&nonce=" + oplbGradebook.nonce,
				{ grade: grade, type: type, gbid: gbid, uid: uid },
				function(data) {
					Backbone.pubSub.trigger("editSuccess", data);
				},
				"json"
			).error(self.trigger("editError"));
		}
	});
	return User;
});
