define(["backbone", "models/User"], function(Backbone, User) {
	var UserCourseList = Backbone.Collection.extend({
		model: User,
		search: "",
		sort_direction: "asc",
		sort_key: "last_name",
		comparator: function(model1, model2) {

			var _str1 = model1.get(this.sort_key);
			var _str2 = model2.get(this.sort_key);

			if (this.sort_direction === "asc") {
				return _str2.localeCompare(_str1);
			} else {
				return _str1.localeCompare(_str2);
			}
		},
		url: function() {
			return ajaxurl + "?action=oplb_user_list";
		}
	});
	return UserCourseList;
});
