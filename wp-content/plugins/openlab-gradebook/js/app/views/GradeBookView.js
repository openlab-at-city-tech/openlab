define(['jquery', 'backbone', 'underscore', 'views/StudentView', 'views/AssignmentView', 'views/EditStudentView', 'views/EditAssignmentView', 'models/uploadFrame', 'models/Course'],
        function ($, Backbone, _, StudentView, AssignmentView, EditStudentView, EditAssignmentView, uploadFrame, Course) {

            Backbone.pubSub = _.extend({}, Backbone.Events);

            var GradebookView = Backbone.View.extend({
                initialize: function (options) {
                    var self = this;
                    var _request = 0;
                    this.resizeTimer;
                    this.xhrs = [];
                    this._subviews = [];
                    this.scrollObj = {};
                    this.options = options;
                    this.filter_option = "-1";
                    this.course = options.course;
                    this.renderControl = 0;
                    this.gradebook = options.gradebook;
                    this.listenTo(self.gradebook.students, 'add remove', self.render);
                    this.listenTo(self.gradebook.cells, 'add remove change:assign_order', self.render);
                    this.listenTo(self.gradebook.assignments, 'add remove change:assign_order change:assign_category', self.render);
                    this.listenTo(self.gradebook.assignments, 'change:assign_grade_type', self.render);
                    this.listenTo(self.gradebook.assignments, 'change:total_weight', self.render);
                    this.listenTo(self.gradebook.assignments, 'change:sorted', self.sortByAssignment);

                    console.log('gradebook init', oplbGradebook);

                    Backbone.pubSub.on('updateAverageGrade', this.updateAverageGrade, this);
                    Backbone.pubSub.on('updateWeightInfo', this.updateWeightInfo, this);

                    this.queue = wp.Uploader.queue;
                    //safety first
                    this.queue.off('remove change:uploading', this.mediaUpdate, this);

                    //add listener for uploaded CSV
                    this.queue.on('remove change:uploading', this.mediaUpdate, this);
                    this.render();

                    $(window).on('resize', function (e) {

                        clearTimeout(this.resizeTimer);
                        this.resizeTimer = setTimeout(function () {

                            self.adjustCellWidths();

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
                    'click button#add-student': 'addStudent',
                    'click button#upload-csv': 'uploadCSV',
                    'click button#download-csv': 'downloadCSV',
                    'click button#add-assignment': 'addAssignment',
                    'click button#filter-assignments': 'filterAssignments',
                    'click [class^=gradebook-student-column-]': 'sortGradebookBy',
                },
                render: function () {
                    var self = this;
                    this.clearSubViews();
                    this.renderControl = 0;
                    var course = this.course;
                    var _x = _.map(self.gradebook.assignments.models, function (model) {
                        return model.get('assign_category').trim();
                    });
                    var _assign_categories = _.without(_.uniq(_x), "") || null;
                    var template = _.template($('#gradebook-interface-template').html());

                    var totalWeight = self.getTotalWeight();

                    console.log('self.gradebook', self.gradebook);

                    var compiled = template({course: self.course, assign_categories: _assign_categories, role: this.role, total_weight: totalWeight});
                    $('#wpbody-content').append(self.$el.html(compiled));
                    $('#filter-assignments-select').val(this.filter_option);
                    switch (this.gradebook.sort_key) {
                        case 'cell':
                            _.each(this.sort_column, function (cell) {
                                var view = new StudentView({
                                    model: self.gradebook.students.get(cell.get('uid')), course: self.course, gradebook: self.gradebook, options: self.options
                                });
                                self._subviews.push(view);
                                $('#students').append(view.render('pinned', self.gradebook.assignments));
                            });
                            var y = self.gradebook.assignments.models;
                            y = _.sortBy(y, function (assign) {
                                return assign.get('assign_order');
                            });

                            _.each(this.sort_column, function (cell) {
                                var view = new StudentView({
                                    model: self.gradebook.students.get(cell.get('uid')), course: self.course, gradebook: self.gradebook, options: self.options
                                });
                                self._subviews.push(view);
                                $('#students-pinned').append(view.render('static', self.gradebook.assignments));
                            });
                            var y = self.gradebook.assignments.models;
                            y = _.sortBy(y, function (assign) {
                                return assign.get('assign_order');
                            });

                            _.each(y, function (assignment) {
                                var view = new AssignmentView({
                                    model: assignment, course: self.course, gradebook: self.gradebook
                                });
                                self._subviews.push(view);
                                console.log('go cell in GradeBookView render');
                                $('#students-header tr').append(view.render());
                            });
                            break;
                        case 'student':
                            _.each(this.gradebook.sort_column.models, function (student) {
                                var view = new StudentView({model: student, course: self.course, gradebook: self.gradebook, options: self.options});
                                self._subviews.push(view);
                                $('#students').append(view.render('pinned', self.gradebook.assignments));
                            });
                            _.each(this.gradebook.sort_column.models, function (student) {
                                var view = new StudentView({model: student, course: self.course, gradebook: self.gradebook, options: self.options});
                                self._subviews.push(view);
                                $('#students-pinned').append(view.render('static', self.gradebook.assignments));
                            });
                            var y = self.gradebook.assignments;
                            y = _.sortBy(y.models, function (assign) {
                                return assign.get('assign_order');
                            });
                            _.each(y, function (assignment) {
                                var view = new AssignmentView({model: assignment, course: self.course, gradebook: self.gradebook});
                                self._subviews.push(view);
                                console.log('go student in GradeBookView render');
                                $('#students-header tr').append(view.render());
                            });
                            break;
                    }
                    this.filterAssignments();
                    this.adjustCellWidths();
                    this.postLoadActions();
                    return this;
                },
                filterAssignments: function () {
                    var _x = $('#filter-assignments-select').val();
                    this.filter_option = _x;
                    var _toHide = this.gradebook.assignments.filter(
                            function (assign) {
                                return assign.get('assign_category') != _x;
                            }
                    );
                    var _toShow = this.gradebook.assignments.filter(
                            function (assign) {
                                return assign.get('assign_category') === _x;
                            }
                    );

                    if (_x === "-1") {
                        this.gradebook.assignments.each(function (assign) {
                            assign.set({visibility: true});
                        });
                    } else {
                        _.each(_toHide, function (assign) {
                            assign.set({visibility: false});
                        });
                        _.each(_toShow, function (assign) {
                            assign.set({visibility: true});
                        });
                    }

                    if (typeof this.scrollObj.data !== 'undefined') {
                        var jsAPI = this.scrollObj.data('jsp');

                        if (typeof jsAPI !== 'undefined') {
                            jsAPI.reinitialise();
                        }
                    }
                },
                adjustCellWidths: function () {

                    var pinnedTable = $('.pinned .table');
                    var columnsToAdjust = pinnedTable.find('.adjust-widths');

                    if (columnsToAdjust.lenght < 1) {
                        return false;
                    }

                    var pinnedTable_w = pinnedTable.width();

                    columnsToAdjust.each(function () {

                        var thisElem = $(this);
                        var target_w = thisElem.data('targetwidth');

                        var target_pct = (target_w / pinnedTable_w) * 100;
                        thisElem.css({
                            'width': target_pct + '%'
                        });

                    });


                },
                postLoadActions: function () {

                    $('[data-toggle="tooltip"]').tooltip();
                    this.scrollObj = $('.table-wrapper .scrollable')
                            .bind('jsp-initialised', this.calculateScrollBarPosition)
                            .jScrollPane();

                },
                calculateScrollBarPosition: function (event, isScrollable) {

                    var targetTable = $('#an-gradebook-container');
                    console.log('targetTable, targetTable height', targetTable, targetTable.height());
                    if (targetTable.height() < 500) {

                        var targetTable_padding = 500 - targetTable.height();
                        targetTable.closest('.jspContainer').css({
                            'padding-bottom': targetTable.height() + targetTable_padding + 'px'
                        });
                        targetTable.closest('.jspContainer').find('.jspHorizontalBar').css({
                            'bottom': (targetTable_padding - 18) + 'px'
                        });
                    }

                },
                addAssignment: function (ev) {
                    var view = new EditAssignmentView({course: this.course, gradebook: this.gradebook});
                },
                addStudent: function (ev) {
                    var view = new EditStudentView({course: this.course, gradebook: this.gradebook});
                    $('body').append(view.render());
                },
                uploadCSV: function (e) {
                    e.preventDefault();

                    if (typeof _wpPluploadSettings !== 'undefined') {
                        _wpPluploadSettings.defaults.multipart_params.gbid = this.course.get('id');
                    }

                    this.buildFrame().open();
                },
                downloadCSV: function (e) {
                    e.preventDefault();

                    this.course.export2csv();

                },
                buildFrame: function () {

                    if (this._frame)
                        return this._frame;

                    console.log('oplbGradebook', oplbGradebook);
                    wp.media.view.settings.post.id = oplbGradebook.storagePage.ID;
                    console.log('wp.media.view.settings in demo', wp.media.view.settings);

                    this._frame = new uploadFrame({
                        title: 'Upload CSV',
                        button: {
                            text: 'Select CSV'
                        },
                        multiple: false,
                        library: {
                            order: 'ASC',
                            orderby: 'title',
                            type: 'text/csv',
                            search: null,
                            uploadedTo: null
                        }
                    });

                    return this._frame;

                },
                checkStudentSortDirection: function () {
                    if (this.gradebook.students.sort_direction === 'asc') {
                        this.gradebook.students.sort_direction = 'desc';
                    } else {
                        this.gradebook.students.sort_direction = 'asc';
                    }
                },
                sortGradebookBy: function (ev) {
                    var column = ev.target.className.replace('gradebook-student-column-', '');
                    this.gradebook.sort_key = 'student';
                    this.gradebook.students.sort_key = column;
                    this.checkStudentSortDirection();
                    this.gradebook.students.sort();
                    this.render();
                },
                sortByAssignment: function (ev) {
                    var x = this.gradebook.cells.where({amid: parseInt(ev.get('id'))});
                    this.sort_column = _.sortBy(x, function (cell) {
                        if (ev.get('sorted') === 'asc') {
                            return cell.get('assign_points_earned');
                        } else {
                            return -1 * cell.get('assign_points_earned');
                        }
                    });
                    this.gradebook.sort_key = 'cell';
                    this.render();
                },
                close: function () {
                    this.clearSubViews();
                    _.map(this.xhrs, function (xhr) {
                        xhr.abort()
                    });
                    this.remove();
                },
                mediaUpdate: function (data) {

                    if (this.renderControl === 0) {
                        this.renderControl = 1;
                        var checkFile = $('.upload-csv-modal:visible').find('.upload-details .upload-index').text();
                        console.log('go render', $('.upload-csv-modal:visible').find('.upload-details .upload-index'), parseInt(checkFile));
                        if (parseInt(checkFile) === 1) {
                            Backbone.history.loadUrl();
                        }
                    }

                },
                getTotalWeight: function () {
                    var self = this;

                    console.log('getTotalWeight', self.gradebook.assignments, self.gradebook.attributes);

                    var totalWeight = 0;
                    _.each(self.gradebook.assignments.models, function (assignment) {

                        totalWeight = totalWeight + parseFloat(assignment.get('assign_weight'));

                    });

                    var message = 'Total Weight: ' + totalWeight;

                    if (totalWeight === 100) {
                        message += ' <span class="text-warning">Any assignments that do not have a set weight will not be included in the average calculation.</span>';
                    } else if (totalWeight > 100) {
                        message += ' <span class="text-warning">Total weight is over 100%. Any assignments that do not have a set weight will not be included in the average calculation.</span>';
                    } else if (totalWeight < 100) {
                        message += ' <span class="text-warning">Total weight is under 100%. Any assignments that do not have a set weight will be given a calculated distribution.';
                    }

                    return message;

                },
                updateTotalWeight: function () {
                    console.log('total_weight on updateTotalWeight', window.oplbGlobals.total_weight);
                },
                updateAverageGrade: function (data) {

                    console.log('updateGradeAverage', data);

                    var studentID = parseInt(data.uid);
                    var target = $('#average' + studentID);
                    target.html(data.current_grade_average);

                    target.attr('title', data.current_grade_average)
                            .tooltip('fixTitle');

                },
                updateWeightInfo: function (data) {

                    console.log('updateWeightInfo', data);

                }
            });
            return GradebookView;
        });