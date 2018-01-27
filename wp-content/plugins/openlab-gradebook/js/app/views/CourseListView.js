define(['jquery', 'backbone', 'underscore', 'views/EditCourseView', 'views/CourseView'],
function ($, Backbone, _, EditCourseView, CourseView) {
    /**
             * A module representing a course list view.
             * @exports views/CourseListView
             */
            var CourseListView = Backbone.View.extend({
                initialize: function () {
                    var self = this;
                    this._subviews = [];
                    this.courseList = this.collection;
                    this.listenTo(self.courseList, 'add', self.render);
                    this.listenTo(self.courseList, 'remove', self.render);
                    this.render();
                },
                loadingData: function () {
                    var self = this;
                },
                events: {
                    'click a#add-course': 'editCourse',
                    'click [class^=course-column-]': 'sortCourseList'
                },
                checkStudentSortDirection: function () {
                    if (this.courseList.sort_direction === 'asc') {
                        this.courseList.sort_direction = 'desc';
                    } else {
                        this.courseList.sort_direction = 'asc';
                    }
                },
                sortCourseList: function (ev) {
                    var column = ev.target.className.replace('course-column-', '');
                    this.courseList.sort_key = column;
                    this.checkStudentSortDirection();
                    this.courseList.sort();
                    this.render();
                },
                clearSubViews: function () {
                    var self = this;
                    _.each(self._subviews, function (view) {
                        view.close();
                    });
                    this._subviews = [];
                },
                render: function () {
                    var self = this;
                    var template = _.template($('#course-list-template').html());
                    var compiled = template({});
                    $('#wpbody-content').prepend(this.$el.html(compiled));

                    if(self.courseList.models.length){

                    _.each(self.courseList.models, function (course) {
                        var courseView = new CourseView({model: course, collection: self.courseList});
                        $('.angb-course-list-tbody').append(courseView.render());
                    });
                
                    } else {

                        this.editCourse(true);

                    }

                    return this;
                },
                editCourse: function (isInit) {

                    if(typeof isInit === 'undefined'){
                        isInit = false;
                    }

                    var view = new EditCourseView({collection: this.courseList, isInit: isInit});
                    return false;
                },
                addCourse: function (course) {
                    var view = new CourseView({model: course, collection: this.courseList, options: this.options});
                    $('#courses').append(view.render().el);
                },
                close: function () {
                    this.clearSubViews();
                    this.remove();
                }
            });
            return CourseListView;
        });