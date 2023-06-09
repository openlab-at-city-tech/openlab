define([
	"jquery",
	"backbone",
	"underscore",
	"views/StatisticsView",
	"views/EditStudentView",
	"views/DeleteStudentView",
	"views/CellView",
	"views/CellDropdown",
	"views/CellCheckmark",
	"views/CellMidFinal",
	"models/letterGrades",
	"models/User"
], function(
	$,
	Backbone,
	_,
	StatisticsView,
	EditStudentView,
	DeleteStudentView,
	CellView,
	CellDropdown,
	CellCheckmark,
	CellMidFinalView,
	letterGrades,
	User
) {
	var StudentView = Backbone.View.extend(
		/** @lends StudentView.prototype */
		{
			tagName: "tr",
			events: {
				"click a.delete-student": "deleteStudent",
				"click a.student-statistics": "studentStatistics",
				"click .dashicons-menu": "toggleStudentMenu",
				"click li.student-submenu-delete": "deleteStudent",
				"click li.student-submenu-stats": "studentStatistics"
			},
			/** @constructs */
			initialize: function(options) {
				var self = this;
				this._subviews = [];
				this.gradebook = options.gradebook;
				this.course = options.course;
				this.student = this.model;

				this.midGrades = new letterGrades([
					{
						label: "--",
						value: "--",
						type: "none"
					},
					{
						label: "P = Passing Work",
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

				this.listenTo(this.model, "change", this.render);
				this.listenTo(this.gradebook, "change:assignments", this.render);
			},
			render: function(pinned, assignments) {

				//give pinned a default
				if (typeof pinned === "undefined") {
					pinned = "none";
				}

				var mobile_styles = "";

				if (pinned === "pinned") {
					mobile_styles = " visible-xs visible-sm";
				}

				var self = this;
				var template = _.template($("#student-view-template").html());
				var compiled = template({
					student: this.model,
					role: this.gradebook.role,
					mobile_styles: mobile_styles,
					midGrades: this.midGrades,
					finalGrades: this.finalGrades
				});

				if(!this.$el.find('.student-tools').length){
					this.$el.html(compiled);
				}

				var midGrades = new CellMidFinalView({
					model: this.model,
					course: this.course,
					gradebook: self.gradebook,
					role: this.gradebook.role,
					grades: this.midGrades,
					type: "mid_semester"
				});

				if(!this.$el.find(".student-grades.mid-semester-grade .cell-wrapper").length){
					this.$el
					.find(".student-grades.mid-semester-grade")
					.append(midGrades.render());
				}

				var finalGrades = new CellMidFinalView({
					model: this.model,
					course: this.course,
					gradebook: self.gradebook,
					role: this.gradebook.role,
					grades: this.finalGrades,
					type: "final"
				});

				if(!this.$el.find(".student-grades.final-grade .cell-wrapper").length){
				this.$el
					.find(".student-grades.final-grade")
					.append(finalGrades.render());
				}

				if (pinned === "pinned" || pinned === "none") {
					var gbid = parseInt(self.course.get("id")); //anq: why is this not already an integer??
					var x = this.gradebook.cells.where({
						uid: parseInt(this.model.get("id")), //anq: why is this not already an integer??
						gbid: gbid
					});
					x = _.sortBy(x, function(model) {
						return model.get("assign_order");
					});
					var self = this;
					_.each(x, function(cell) {
						var _assignment = assignments.findWhere({ id: cell.get("amid") });

						if (typeof _assignment !== "undefined") {
							if (_assignment.get("assign_grade_type") === "checkmark") {
								var view = new CellCheckmark({
									course: self.course,
									gradebook: self.gradebook,
									model: cell,
									options: self.options
								});
							} else if (_assignment.get("assign_grade_type") === "letter") {
								var view = new CellDropdown({
									course: self.course,
									gradebook: self.gradebook,
									model: cell,
									options: self.options
								});
							} else {
								var view = new CellView({
									course: self.course,
									gradebook: self.gradebook,
									model: cell,
									options: self.options
								});
							}

							self._subviews.push(view);
							self.$el.append(view.render());
						}
					});
				}

				this.postLoadActions();

				return this.el;
			},
			postLoadActions: function() {
				$('[data-toggle="tooltip"]').tooltip();
			},
			clearSubViews: function() {
				var self = this;
				_.each(self._subviews, function(view) {
					view.close();
				});
				this._subviews = [];
			},
			toggleStudentMenu: function() {
				var _student_menu = $("#row-student-id-" + this.model.get("id"));
				if (_student_menu.css("display") === "none") {
					var view = this;
					_student_menu.toggle(1, function() {
						var self = this;
						$(document).one("click", function() {
							$(self).hide();
							//view.model.set({hover:false});
						});
					});
				}
			},
			selectAllStudents: function() {
				var _selected = $("#cb-select-all-1").is(":checked");
				if (_selected) {
					$("#cb-select-" + this.model.get("id")).prop("checked", true);
				} else {
					$("#cb-select-" + this.model.get("id")).prop("checked", false);
				}
			},
			selectStudent: function(ev) {
				var _selected = $("#cb-select-" + this.model.get("id")).is(":checked");
				this.model.set({ selected: _selected });
				var x = AN.GlobalVars.assignments.findWhere({
					selected: true
				});
				if (_selected) {
					$("#cb-select-" + this.model.get("id")).prop("checked", true);
				} else {
					$("#cb-select-" + this.model.get("id")).prop("checked", false);
				}
				x &&
					x.set({
						selected: false
					});
			},
			studentStatistics: function(ev) {
				ev.preventDefault();
				var view = new StatisticsView({
					model: this.model,
					options: this.options
				});
			},
			deleteStudent: function(ev) {
				ev.preventDefault();
				var view = new DeleteStudentView({
					model: this.model,
					gradebook: this.gradebook,
					course: this.course
				});
			},
			/** removes view and any subviews */
			close: function(ev) {
				this.clearSubViews();
				this.remove();
			},
		}
	);
	return StudentView;
});
