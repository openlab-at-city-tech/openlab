define(["jquery", "backbone", "underscore"], function ($, Backbone, _) {
	var DeleteAssignmentView = Backbone.View.extend({
		id: "base-modal",
		className: "modal fade",
		events: {
			"hidden.bs.modal": "deleteCancel",
			"keyup": "keyPressHandler",
			"click #delete-assignment-delete": "submitForm",
			"submit #delete-assignment-form": "deleteSave",
		},
		initialize: function (options) {
			this.gradebook = options.gradebook;
			this.course = options.course;
            this.assignment = options.assignment;
			$("body").append(this.render().el);
			return this;
		},
		render: function () {
			var self = this;
			if (this.assignment) {
				var template = _.template($("#delete-assignment-template").html());
				var compiled = template({ assignment: this.assignment, course: this.course });
				this.$el.html(compiled);
			}
			this.$el.modal("show");
			return this;
		},
		keyPressHandler: function (e) {
			if (e.keyCode == 27) this.deleteCancel();
			if (e.keyCode == 13) this.submitForm();
			return this;
		},
		deleteCancel: function () {
			this.$el.data("modal", null);
			this.remove();
			return false;
		},
		submitForm: function () {
			$("#delete-assignment-form").submit();
		},
		deleteSave: function (ev) {
			ev.preventDefault();
            console.log('go deleteSave', this.assignment);
			var self = this;
			this.assignment.destroy({
				success: function (model, response) {
					var _cells = self.gradebook.cells.where({ amid: model.get("id") });
					self.gradebook.cells.remove(_cells);
					var _x = model.get("assign_order");
					if (self.gradebook.assignments.models.length) {
						var _y = _.max(self.gradebook.assignments.models, function (
							assignment
						) {
							return assignment.get("assign_order");
						});
						for (i = _x; i < _y.get("assign_order"); i++) {
							var _z = self.gradebook.assignments.findWhere({
								assign_order: i + 1,
							});

							if (typeof _z !== "undefined") {
								_z.save({ assign_order: i });
							}
						}
					}

                    self.checkForAverageGradeUpdates(response);
                    self.$el.modal('hide');
				},
			});
        },
        checkForAverageGradeUpdates: function (response) {

            if (typeof response.student_grade_update === 'undefined' || response.student_grade_update.length < 1) {
                return false;
            }
            
            this.gradebook.attributes.distributed_weight = response.distributed_weight;
            
            _.each(response.student_grade_update, function (update) {
                Backbone.pubSub.trigger('updateAverageGrade', update);
            });

        }
	});
	return DeleteAssignmentView;
});
