define([
	"jquery",
	"backbone",
	"underscore",
	"models/letterGrades",
	"views/CommentView",
	"models/User"
], function($, Backbone, _, letterGrades, CommentView, User) {
	var CellMidFinal = Backbone.View.extend({
		tagName: "span",
		events: {
			"change select.grade-selector.mid": "editMid",
			"change select.grade-selector.final": "editFinal"
		},
		initialize: function(options) {

            Backbone.pubSub.on("editSuccess", this.editSuccess, this);

			this.course = options.course;
			this.gradebook = options.gradebook;
			this.grades = options.grades;
			this.type = options.type;
			this.model = options.model;
            this.listenTo(this.gradebook.assignments, "change:hover", this.hoverCell);

            this.midGrades = new letterGrades([
                {
                    label: "--",
                    value: "--",
                    type: "none"
                },
                {
                    label: "P = Making Satisfactory Progress",
                    value: "passing_display",
                    type: "display_item"
                },
                {
                    label: "P",
                    value: "passing",
                    type: "display_value"
                },
                {
                    label: "N = Needs Improvement",
                    value: "needs_improvement_display",
                    type: "display_item"
                },
                {
                    label: "N",
                    value: "needs_improvement",
                    type: "display_value"
                },
                {
                    label: "SA = Stopped Attending",
                    value: "stopped_attending_display",
                    type: "display_item"
                },
                {
                    label: "SA",
                    value: "stopped_attending",
                    type: "display_value"
                }
            ]);

            this.finalGrades = new letterGrades([
                {
                    label: "--",
                    value: "--",
                    type: "none"
                },
                {
                    label: "A",
                    value: "a",
                    type: "none"
                },
                {
                    label: "A-",
                    value: "a_minus",
                    type: "none"
                },
                {
                    label: "B+",
                    value: "b_plus",
                    type: "none"
                },
                {
                    label: "B",
                    value: "b",
                    type: "none"
                },
                {
                    label: "B-",
                    value: "b_minus",
                    type: "none"
                },
                {
                    label: "C+",
                    value: "c_plus",
                    type: "none"
                },
                {
                    label: "C",
                    value: "c",
                    type: "none"
                },
                {
                    label: "D",
                    value: "d",
                    type: "none"
                },
                {
                    label: "F",
                    value: "f",
                    type: "none"
                },
                {
                    label: "WF = withdrew, failing",
                    value: "wf_display",
                    type: "display_item"
                },
                {
                    label: "WF",
                    value: "wf",
                    type: "display_value"
                },
                {
                    label: "WN = withdrew, never attended (academic penalty)",
                    value: "wn_display",
                    type: "display_item"
                },
                {
                    label: "WN",
                    value: "wn",
                    type: "display_value"
                },
                {
                    label: "*WN = administrative withdrawl, never attended",
                    value: "wn_admin_display",
                    type: "display_item"
                },
                {
                    label: "*WN",
                    value: "wn_admin",
                    type: "display_value"
                },
                {
                    label: "WU = Unofficial Withdrawl",
                    value: "wu_display",
                    type: "display_item"
                },
                {
                    label: "WU",
                    value: "wu",
                    type: "display_value"
                }
            ]);

            this.studentGradeLabels("mid");
            this.studentGradeLabels("final");
		},
		render: function() {
			var self = this;

			var template = _.template(
				$("#edit-cell-dropdown-mid-final-template").html()
            );

			var compiled = template({
				student: this.model,
				grades: this.grades,
                role: this.gradebook.role,
				type: this.type
			});
            this.$el.html(compiled);

            var comments = this.model.get('mid_semester_comments');

            if(this.type === 'final'){
                comments = this.model.get('final_comments');
            }

			if (
				self.gradebook.role === "instructor" ||
				(self.gradebook.role === "student" && comments)
			) {

                var name = 'Mid-semester Grade';

                if(this.type === 'final'){
                    name = 'Final Grade';
                }

				var comment = new CommentView({
					model: this.model,
                    gradebook: self.gradebook,
                    name: name,
                    username: this.model.get('user_login'),
                    type: this.type
				});

				this.$el.find(".cell-wrapper").append(comment.render());
			}

			return this.el;
		},
		updateOnEnter: function(e) {
			if (e.keyCode == 13) {
				this.$el.blur();
			}
		},
		hideInput: function(value) {
			var self = this;
			this.model.save(
				{ assign_points_earned: value },
				{
					wait: true,
					success: function(model, response) {
						self.render();
						Backbone.pubSub.trigger("updateAverageGrade", response);
					}
				}
			);
		},
		editMid: function() {
			this.edit("mid");
		},
		editFinal: function() {
			this.edit("final");
		},
		edit: function(ev) {
			this.$el.attr("contenteditable", "false");

			this.$el
				.closest("#gradebookWrapper")
				.find("#savingStatus")
				.removeClass("hidden");

			var targetSelector = ".grade-selector." + ev;

			this.$el.find(targetSelector).attr("disabled", "disabled");

			var value = this.$el.find(targetSelector).val();

			if (value.indexOf("_display") !== -1) {
				value = value.replace(/_display/, "");
				this.$el.find(targetSelector).val(value);
			}

			var type = this.$el.find(targetSelector).data("type");
			var uid = this.$el.find(targetSelector).data("uid");
			var gbid = parseInt(this.course.get("id"));

			var toedit = new User();
			toedit.updateStudentGrade(value, type, uid, gbid);

			if (value && value !== undefined) {
				if (type === "mid") {
					this.model.attributes.mid_semester_grade = value;
				} else {
					this.model.attributes.final_grade = value;
				}
			}

			this.handleTooltips(ev);
		},
		handleTooltips: function(ev) {
			var targetSelector = ".grade-selector." + ev;
			var value = this.$el.find(targetSelector).val();

			var toSearch = this.midGrades;

			if (ev === "final") {
				toSearch = this.finalGrades;
			}

			var title = "";
			toSearch.each(function(grade) {
				if (grade.get("value") === value + "_display" && title === "") {
					title = grade.get("label");
				} else if (grade.get("value") === value && title === "") {
					title = grade.get("label");
				}
			});

			this.$el
				.find(targetSelector)
				.closest(".student-grades")
				.find(".fa-info-circle")
				.attr("title", title)
				.tooltip("fixTitle");
		},
		editSuccess: function() {
			this.$el
				.closest("#gradebookWrapper")
				.find("#savingStatus")
				.addClass("hidden");
			this.$el.find(".grade-selector").removeAttr("disabled");
		},
		editError: function() {
			this.$el.find(".grade-selector").removeAttr("disabled");
        },
        studentGradeLabels: function(ev) {
            var toSearch = this.midGrades;
            var studentVal = this.model.get("mid_semester_grade");
            var title = "";

            if (ev === "final") {
                toSearch = this.finalGrades;
                studentVal = this.model.get("final_grade");
            }

            _.each(toSearch.models, function(grade) {
                if (grade.get("value") === studentVal + "_display" && title === "") {
                    title = grade.get("label");
                } else if (grade.get("value") === studentVal && title === "") {
                    title = grade.get("label");
                }
            });

            if (title === "") {
                title = "--";
            }

            if (ev === "final") {
                this.model.set("tool_tip_final", title);
            } else {
                this.model.set("tool_tip_mid", title);
            }
        },
		hoverCell: function(ev) {
			if (this.model.get("amid") === ev.get("id")) {
				this.model.set({
					hover: ev.get("hover")
				});
				this.$el.toggleClass("hover", ev.get("hover"));
			}
		},
		close: function(ev) {
			this.remove();
		}
	});
	return CellMidFinal;
});
