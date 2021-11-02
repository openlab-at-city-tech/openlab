define([
	"jquery",
	"backbone",
	"underscore",
	"views/StudentView",
	"views/AssignmentView",
	"views/EditStudentView",
	"views/EditAssignmentView",
	"views/uploadModal",
	"models/Course",
], function (
	$,
	Backbone,
	_,
	StudentView,
	AssignmentView,
	EditStudentView,
	EditAssignmentView,
	uploadModal,
	Course
) {
	Backbone.pubSub = _.extend({}, Backbone.Events);

	var GradebookView = Backbone.View.extend({
		initialize: function (options) {
			var self = this;
			var _request = 0;
			var currentScrollSize;
			this.studentHeader;
			this.scrollSize = 0;
			this.resizeTimer;
			this.xhrs = [];
			this._subviews = [];
			this.scrollObj = {};
			this.options = options;
			this.filter_option = "default";
			this.course = options.course;
			this.renderControl = 0;
			this.gradebook = options.gradebook;
			this.listenTo(
				self.gradebook.students,
				"add remove",
				_.debounce(_.bind(this.render, this), 128)
			);
			this.listenTo(
				self.gradebook.cells,
				"add remove",
				_.debounce(_.bind(this.render, this), 128)
			);
			this.listenTo(
				self.gradebook.cells,
				"change:assign_order",
				_.debounce(_.bind(this.render, this), 128)
			);
			this.listenTo(
				self.gradebook.assignments,
				"add",
				_.debounce(_.bind(this.initRender, this), 128)
			);
			this.listenTo(
				self.gradebook.assignments,
				"remove",
				_.debounce(_.bind(this.handleDelete, this), 128)
			);
			this.listenTo(
				self.gradebook.assignments,
				"remove",
				_.debounce(_.bind(this.initRender, this), 128)
			);
			this.listenTo(
				self.gradebook.assignments,
				"change:assign_grade_type change:assign_weight",
				_.debounce(_.bind(this.render, this), 128)
			);
			this.listenTo(
				self.gradebook.assignments,
				"change:sorted",
				self.sortByAssignment
			);
			this.listenTo(
				self.gradebook.assignments,
				"change:assign_category",
				_.debounce(_.bind(this.initRender, this), 128)
			);
			this.listenTo(
				self.gradebook.assignments,
				"change:assign_order",
				this.render
			);

			Backbone.pubSub.on("updateAverageGrade", this.updateAverageGrade, this);
			Backbone.pubSub.on("newGradebookCSV", this.initRender, this);

			this.render();

			this.initRender();

			$(window).on("resize", function (e) {
				clearTimeout(this.resizeTimer);
				this.resizeTimer = setTimeout(function () {
					self.handleTableResize(true);
				}, 250);
			});

			return this;
		},
		clearSubViews: function () {
			var self = this;
			_.each(self._subviews, function (view) {
				view.close();
			});
			this._subviews = [];
		},
		events: {
			"click button#add-student": "addStudent",
			"click button#upload-modal": "uploadModal",
			"click button#download-csv": "downloadCSV",
			"click button#download-csv-mobile": "downloadCSV",
			"click button#add-assignment": "addAssignment",
			"click button#filter-assignments": "filterAssignments",
			"click [class^=gradebook-student-column-]": "sortGradebookBy",
			"click [class^=gradebook-student-column-] span": "sortGradebookBy",
		},
		initRender: function (ev) {
			this.scrollSize = 0;
			var self = this;
			this.clearSubViews();
			this.renderControl = 0;

			console.log("ev on initRender", ev);
			if (typeof ev !== "undefined") {
				console.log("ev, self.gradebook", ev, self.gradebook);

				if (typeof ev.cells !== "undefined") {
					this.gradebook.cells.set(ev.cells);
				}

				if (typeof ev.students !== "undefined") {
					this.gradebook.students.set(ev.students);
				}

				if (typeof ev.assignments !== "undefined") {
					this.gradebook.assignments.set(ev.assignments);
				}
			}

			var _x = _.map(self.gradebook.assignments.models, function (model) {
				return model.get("assign_category").trim();
			});
			var _assign_categories = _.without(_.uniq(_x), "") || null;
			var template = _.template($("#gradebook-interface-template").html());

			var totalWeight = self.getTotalWeight();

			var compiled = template({
				course: self.course,
				assign_categories: _assign_categories,
				role: this.gradebook.role,
				total_weight: totalWeight,
				assign_length: self.gradebook.assignments.length,
			});
			$("#wpbody-content").append(self.$el.html(compiled));

			var studentHeaderTemplate = _.template(
				$("#gradebook-interface-template-student-header").html()
			);
			var compiledStudentHeader = studentHeaderTemplate({
				role: this.gradebook.role,
			});
			self.$el.find("#students-header tr").append(compiledStudentHeader);
			this.studentHeader = compiledStudentHeader;

			if (this.filter_option === undefined) {
				this.filter_option = "default";
			}

			$("#filter-assignments-select").val(this.filter_option);

			this.render();

			return this;
		},
		handleTableResize: function (widthChange) {
			if (widthChange === undefined) {
				widthChange = false;
			}

			this.adjustCellWidths();
			if (typeof this.scrollObj.data !== "undefined") {
				var jsAPI = this.scrollObj.data("jsp");

				if (typeof jsAPI !== "undefined") {
					currentScrollSize = $("#an-gradebook-container").width();

					if (
						parseInt(this.scrollSize) !== parseInt(currentScrollSize) ||
						widthChange
					) {
						this.scrollSize = currentScrollSize;
						jsAPI.destroy();
						this.scrollObj = $(".table-wrapper .scrollable")
							.bind("jsp-initialised", this.calculateScrollBarPosition);
					} else {
						var scrollContainerElem = $("#an-gradebook-container").closest(
							".jspContainer"
						);

						var scrollContainerDims = {
							height: scrollContainerElem.height(),
						};

						scrollContainerElem.css({
							"max-height": scrollContainerDims.height + 29 + "px",
						});
					}
				}
			}
		},
		render: function () {
			var self = this;

			switch (this.gradebook.sort_key) {
				case "cell":
					$("#students").html("");
					$("#students-pinned").html("");
					$("#students-header tr").html(this.studentHeader);
					_.each(this.sort_column, function (cell) {
						var view = new StudentView({
							model: self.gradebook.students.get(cell.get("uid")),
							course: self.course,
							gradebook: self.gradebook,
							options: self.options,
						});
						self._subviews.push(view);
						$("#students").append(
							view.render("pinned", self.gradebook.assignments)
						);
					});
					var y = self.gradebook.assignments.models;
					y = _.sortBy(y, function (assign) {
						return assign.get("assign_order");
					});

					_.each(this.sort_column, function (cell) {
						var view = new StudentView({
							model: self.gradebook.students.get(cell.get("uid")),
							course: self.course,
							gradebook: self.gradebook,
							options: self.options,
						});
						self._subviews.push(view);
						$("#students-pinned").append(
							view.render("static", self.gradebook.assignments)
						);
					});
					var y = self.gradebook.assignments.models;
					y = _.sortBy(y, function (assign) {
						return assign.get("assign_order");
					});

					_.each(y, function (assignment) {
						var view = new AssignmentView({
							model: assignment,
							course: self.course,
							gradebook: self.gradebook,
						});
						self._subviews.push(view);
						$("#students-header tr").append(view.render());
					});
					break;
				case "student":
					$("#students").html("");
					$("#students-pinned").html("");
					$("#students-header tr").html(this.studentHeader);

					_.each(this.gradebook.sort_column.models, function (student) {
						var view = new StudentView({
							model: student,
							course: self.course,
							gradebook: self.gradebook,
							options: self.options,
						});
						self._subviews.push(view);
						$("#students").append(
							view.render("pinned", self.gradebook.assignments)
						);
					});
					_.each(this.gradebook.sort_column.models, function (student) {
						var view = new StudentView({
							model: student,
							course: self.course,
							gradebook: self.gradebook,
							options: self.options,
						});
						self._subviews.push(view);
						$("#students-pinned").append(
							view.render("static", self.gradebook.assignments)
						);
					});
					var y = self.gradebook.assignments;
					y = _.sortBy(y.models, function (assign) {
						return assign.get("assign_order");
					});
					_.each(y, function (assignment) {
						var view = new AssignmentView({
							model: assignment,
							course: self.course,
							gradebook: self.gradebook,
						});
						self._subviews.push(view);
						$("#students-header tr").append(view.render());
					});
					break;
			}

			this.filterAssignments();

			if (this.scrollSize === 0) {
				this.scrollSize = this.$el.find("#an-gradebook-container").width();
				this.gradebook.sort_key = "student";
				this.gradebook.students.sort_key = "last_name";
				this.gradebook.students.sort_direction = "desc";
				this.gradebook.students.sort();
				this.render();

				//table scroll
				this.scrollObj = $(".table-wrapper .scrollable")
					.bind("jsp-initialised", this.calculateScrollBarPosition);

				$('[data-toggle="tooltip"]').tooltip();

				new ResizeSensor(jQuery("#an-gradebook-container"), function () {
					self.handleTableResize();
				});
			}

			return this;
		},
		filterAssignments: function () {
			var self = this;
			if (self.gradebook.role !== "instructor") {
				return false;
			}

			var _x = $("#filter-assignments-select").val();

			this.filter_option = _x;
			var _toHide = this.gradebook.assignments.filter(function (assign) {
				return assign.get("assign_category") !== _x;
			});
			var _toShow = this.gradebook.assignments.filter(function (assign) {
				return assign.get("assign_category") === _x;
			});

			if (_x === "default") {
				this.gradebook.assignments.each(function (assign) {
					assign.set({ visibility: true });
				});
			} else {
				_.each(_toHide, function (assign) {
					assign.set({ visibility: false });
				});
				_.each(_toShow, function (assign) {
					assign.set({ visibility: true });
				});
			}

			if (typeof this.scrollObj.data !== "undefined") {
				var jsAPI = this.scrollObj.data("jsp");

				if (typeof jsAPI !== "undefined") {
					jsAPI.reinitialise();
				}
			}
		},
		adjustCellWidths: function () {
			var pinnedTable = $(".pinned .table");
			var columnsToAdjust = pinnedTable.find(".adjust-widths");

			if (columnsToAdjust.lenght < 1) {
				return false;
			}

			var pinnedTable_w = pinnedTable.width();

			columnsToAdjust.each(function () {
				var thisElem = $(this);
				var target_w = thisElem.data("targetwidth");

				var target_pct = (target_w / pinnedTable_w) * 100;
				thisElem.css({
					width: target_pct + "%",
				});
			});
		},
		calculateScrollBarPosition: function (event, isScrollable) {
			var targetTable = $("#an-gradebook-container");
			var scrollContainerElem = targetTable.closest(".jspContainer");
			var adjustment = 16;
			$("#an-gradebook-container").css("width", "auto");

			if (targetTable.height() < 500) {
				var targetTable_padding = 500;

				scrollContainerElem.css({
					"padding-bottom": targetTable_padding + "px",
				});
				scrollContainerElem.find(".jspHorizontalBar").css({
					bottom: targetTable_padding + "px",
				});
			}

			var scrollContainerDims = {
				height: scrollContainerElem.height(),
			};

			scrollContainerElem.css({
				height: scrollContainerDims.height + 29 + "px",
			});

			var paneLocation = $("#an-gradebook-container").offset();
			scrollContainerElem.find(".jspHorizontalBar").css({
				position: "fixed",
				left: paneLocation.left - adjustment + "px",
			});

			var waypoint = new Waypoint({
				element: document.getElementById("an-gradebook-container"),
				handler: function (direction) {
					var target = scrollContainerElem.find(".jspHorizontalBar");

					if (direction === "up") {
						target.css({
							position: "fixed",
							left: paneLocation.left - adjustment + "px",
						});
					} else {
						target.css({
							position: "absolute",
							left: 0 - adjustment + "px",
						});
					}
				},
				offset: "bottom-in-view",
			});
		},
		addAssignment: function (ev) {
			var checkElem = $("body").find("#modalDialogEditAssignment");

			//prevent double modals
			if (checkElem.length) {
				return false;
			}

			var view = new EditAssignmentView({
				course: this.course,
				gradebook: this.gradebook,
			});
		},
		addStudent: function (ev) {
			var checkElem = $("body").find("#modalDialogEditStudent");

			//prevent double modals
			if (checkElem.length) {
				return false;
			}

			var view = new EditStudentView({
				course: this.course,
				gradebook: this.gradebook,
			});
			$("body").append(view.render());
		},
		downloadCSV: function (e) {
			e.preventDefault();

			this.course.export2csv();
		},
		uploadModal: function (e) {
			var checkElem = $("body").find("#modalDialogUpload");

			//prevent double modals
			if (checkElem.length) {
				return false;
			}

			var view = new uploadModal({
				course: this.course,
				gradebook: this.gradebook,
				model: this.model,
			});

			$("body").append(view.render());
		},
		checkStudentSortDirection: function () {
			if (this.gradebook.students.sort_direction === "asc") {
				this.gradebook.students.sort_direction = "desc";
			} else {
				this.gradebook.students.sort_direction = "asc";
			}
		},
		sortGradebookBy: function (ev) {
			ev.stopPropagation();

			var thisElem = $(ev.srcElement);
			var thisParent = thisElem.closest("th");

			if (ev.srcElement.nodeName === "TH") {
				thisParent = thisElem;
			}

			var targetElem = thisParent.find(".tooltip-wrapper");
			var column = targetElem.data("target");
			var sortTarget = thisParent.find(".header-wrapper");

			var currentSort = sortTarget.attr("class");
			var direction = "sort-up";

			if (currentSort.indexOf("sort-up") !== -1) {
				direction = "sort-down";
			}

			this.gradebook.sort_key = "student";
			this.gradebook.students.sort_key = column;
			this.checkStudentSortDirection();
			this.gradebook.students.sort();
			this.render();

			$(".header-wrapper").removeClass("sort-up sort-down");
			$(".gradebook-student-column-" + column)
				.find(".header-wrapper")
				.addClass(direction);
		},
		sortByAssignment: function (ev) {
			var x = this.gradebook.cells.where({ amid: parseInt(ev.get("id")) });
			this.sort_column = _.sortBy(x, function (cell) {
				if (ev.get("sorted") === "asc") {
					return cell.get("assign_points_earned");
				} else {
					return -1 * cell.get("assign_points_earned");
				}
			});
			this.gradebook.sort_key = "cell";
			this.render();
		},
		handleDelete: function (ev) {
			this.$el
				.closest("#gradebookWrapper")
				.find("#savingStatus")
				.removeClass("hidden");
		},
		close: function () {
			this.clearSubViews();
			_.map(this.xhrs, function (xhr) {
				xhr.abort();
			});
			this.remove();
		},
		getTotalWeight: function () {
			var self = this;

			var totalWeight = 0;
			_.each(self.gradebook.assignments.models, function (assignment) {
				totalWeight = totalWeight + parseFloat(assignment.get("assign_weight"));
			});

			var message = "";

			if (totalWeight >= 100) {
				message +=
					"<strong>Percentage of Total Grade:</strong> " +
					totalWeight +
					"% of the total grade has been designated. Any assignments that do not have a set percentage will not be included in the average calculation.";
			} else if (totalWeight < 100) {
				message +=
					"<strong>Percentage of Total Grade:</strong> " +
					totalWeight +
					"% of the total grade has been designated. The rest of the grade average will be calculated evenly.";
			}

			if (self.gradebook.role === "instructor") {
				message += " Percentages can be edited in the dropdown menus.";
			}

			return message;
		},
		updateAverageGrade: function (data) {
			var studentID = parseInt(data.uid);
			var target = $("#average" + studentID);
			target.html(data.current_grade_average);

			var index = 0;
			_.each(this.gradebook.students.models, function (student) {
				if (parseInt(student.get("id")) === studentID) {
					student.set(
						{ current_grade_average: data.current_grade_average },
						{ silent: true }
					);
				}
				index++;
			});

			target.attr("title", data.current_grade_average).tooltip("fixTitle");
		},
	});
	return GradebookView;
});
