define(['jquery', 'backbone', 'underscore', 'views/StatisticsView', 'views/EditStudentView', 'views/DeleteStudentView', 'views/CellView', 'views/CellDropdown', 'views/CellCheckmark', 'models/letterGrades', 'models/User'],
        function ($, Backbone, _, StatisticsView, EditStudentView, DeleteStudentView, CellView, CellDropdown, CellCheckmark, letterGrades, User) {
            var StudentView = Backbone.View.extend(
                    /** @lends StudentView.prototype */
                            {
                                tagName: 'tr',
                                events: {
                                    'click a.delete-student': 'deleteStudent',
                                    'click a.student-statistics': 'studentStatistics',
                                    'click .dashicons-menu': 'toggleStudentMenu',
                                    'click li.student-submenu-delete': 'deleteStudent',
                                    'click li.student-submenu-stats': 'studentStatistics',
                                    'change select.grade-selector.mid': 'editMid',
                                    'change select.grade-selector.final': 'editFinal'
                                },
                                /** @constructs */
                                initialize: function (options) {
                                    var self = this;
                                    this._subviews = [];
                                    this.gradebook = options.gradebook;
                                    this.course = options.course;
                                    this.student = this.model;
                                    this.listenTo(this.model, 'change', this.render);
                                    this.listenTo(this.gradebook, 'change:assignments', this.render);

                                    Backbone.pubSub.on('editSuccess', this.editSuccess, this);

                                    this.midGrades = new letterGrades([
                                        {
                                            label: '--',
                                            value: '--',
                                        },
                                        {
                                            label: 'P = Passing Work',
                                            value: 'passing',
                                        },
                                        {
                                            label: 'BL = Borderline',
                                            value: 'borderline',
                                        },
                                        {
                                            label: 'U = Unsatisfactory',
                                            value: 'unsatisfactory',
                                        },
                                        {
                                            label: 'SA = Stopped Attending',
                                            value: 'stopped_attending',
                                        },
                                    ]);

                                    this.finalGrades = new letterGrades([
                                        {
                                            label: '--',
                                            value: '--',
                                        },
                                        {
                                            label: 'A',
                                            value: 'a',
                                        },
                                        {
                                            label: 'A-',
                                            value: 'a_minus',
                                        },
                                        {
                                            label: 'B+',
                                            value: 'b_plus',
                                        },
                                        {
                                            label: 'B',
                                            value: 'b',
                                        },
                                        {
                                            label: 'B-',
                                            value: 'b_minus',
                                        },
                                        {
                                            label: 'C+',
                                            value: 'c_plus',
                                        },
                                        {
                                            label: 'C',
                                            value: 'c',
                                        },
                                        {
                                            label: 'D',
                                            value: 'd',
                                        },
                                        {
                                            label: 'F',
                                            value: 'f',
                                        },
                                        {
                                            label: 'WF - withdrew, failing',
                                            value: 'wf',
                                        },
                                        {
                                            label: 'WN = withdrew, never attended (academic penalty)',
                                            value: 'wn',
                                        },
                                        {
                                            label: '* WN = administrative withdrawl, never attended',
                                            value: 'wn_admin',
                                        },
                                        {
                                            label: 'WU = Unofficial Withdrawl',
                                            value: '',
                                        }
                                    ]);
                                },
                                render: function (pinned, assignments) {
                                    //give pinned a default
                                    if (typeof pinned === 'undefined') {
                                        pinned = 'none';
                                    }

                                    var mobile_styles = '';

                                    if (pinned === 'pinned') {
                                        mobile_styles = ' visible-xs';
                                    }

                                    var self = this;
                                    console.log('this in studentView render', this);
                                    var template = _.template($('#student-view-template').html());
                                    var compiled = template({student: this.model, role: this.gradebook.role, mobile_styles: mobile_styles, midGrades: this.midGrades, finalGrades: this.finalGrades});
                                    this.$el.html(compiled);

                                    if (pinned === 'pinned' || pinned === 'none') {
                                        var gbid = parseInt(self.course.get('id')); //anq: why is this not already an integer??
                                        var x = this.gradebook.cells.where({
                                            uid: parseInt(this.model.get('id')), //anq: why is this not already an integer??
                                            gbid: gbid
                                        });
                                        x = _.sortBy(x, function (model) {
                                            return model.get('assign_order');
                                        });
                                        var self = this;
                                        _.each(x, function (cell) {
                                            var _assignment = assignments.findWhere({id: cell.get('amid')});

                                            if (typeof _assignment !== 'undefined') {
                                                if (_assignment.get('assign_grade_type') === 'checkmark') {
                                                    var view = new CellCheckmark({course: self.course, gradebook: self.gradebook, model: cell, options: self.options});
                                                } else if (_assignment.get('assign_grade_type') === 'letter') {
                                                    var view = new CellDropdown({course: self.course, gradebook: self.gradebook, model: cell, options: self.options});
                                                } else {
                                                    var view = new CellView({course: self.course, gradebook: self.gradebook, model: cell, options: self.options});
                                                }

                                                self._subviews.push(view);
                                                self.$el.append(view.render());
                                            }
                                        });
                                    }

                                    return this.el;
                                },
                                clearSubViews: function () {
                                    var self = this;
                                    _.each(self._subviews, function (view) {
                                        view.close();
                                    });
                                    this._subviews = [];
                                },
                                toggleStudentMenu: function () {
                                    var _student_menu = $('#row-student-id-' + this.model.get('id'));
                                    if (_student_menu.css('display') === 'none') {
                                        var view = this;
                                        _student_menu.toggle(1, function () {
                                            var self = this;
                                            $(document).one('click', function () {
                                                $(self).hide();
                                                //view.model.set({hover:false}); 
                                            });
                                        });
                                    }
                                },
                                selectAllStudents: function () {
                                    var _selected = $('#cb-select-all-1').is(':checked');
                                    if (_selected) {
                                        $('#cb-select-' + this.model.get('id')).prop('checked', true);
                                    } else {
                                        $('#cb-select-' + this.model.get('id')).prop('checked', false);
                                    }
                                },
                                selectStudent: function (ev) {
                                    var _selected = $('#cb-select-' + this.model.get('id')).is(':checked');
                                    this.model.set({selected: _selected})
                                    var x = AN.GlobalVars.assignments.findWhere({
                                        selected: true
                                    });
                                    if (_selected) {
                                        $('#cb-select-' + this.model.get('id')).prop('checked', true);
                                    } else {
                                        $('#cb-select-' + this.model.get('id')).prop('checked', false);
                                    }
                                    x && x.set({
                                        selected: false
                                    });
                                },
                                studentStatistics: function (ev) {
                                    ev.preventDefault();
                                    var view = new StatisticsView({model: this.model, options: this.options});
                                },
                                deleteStudent: function (ev) {
                                    ev.preventDefault();
                                    var view = new DeleteStudentView({model: this.model, gradebook: this.gradebook, course: this.course});
                                },
                                /** removes view and any subviews */
                                close: function (ev) {
                                    this.clearSubViews();
                                    this.remove();
                                },
                                editMid: function(){
                                    this.edit('mid');
                                },
                                editFinal: function(){
                                    this.edit('final');
                                },
                                edit: function (ev) {
                                    this.$el.attr('contenteditable', 'false');

                                    var targetSelector = '.grade-selector.' + ev;

                                    this.$el.find(targetSelector).attr('disabled', 'disabled');
                                    var value = this.$el.find(targetSelector).val();
                                    var type = this.$el.find(targetSelector).data('type');
                                    var uid = this.$el.find(targetSelector).data('uid');
                                    var gbid = parseInt(this.course.get('id'));

                                    var toedit = new User();
                                    toedit.updateStudentGrade(value, type, uid, gbid);
                                    
                                },
                                editSuccess: function(){
                                    console.log('edit success');
                                    this.$el.find('.grade-selector').removeAttr('disabled');
                                },
                                editError: function(){
                                    console.log('edit error');
                                    this.$el.find('.grade-selector').removeAttr('disabled');
                                }
                            });
                    return StudentView;
                });