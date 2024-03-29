define(['jquery', 'underscore', 'backbone', 'views/CourseListView', 'views/GradeBookView',
    'models/CourseList', 'models/Course', 'models/CourseGradebook',
],
        /**
         * @exports GradeBookRouter
         */

                function ($, _, Backbone, CourseListView, GradeBookView, CourseList, Course, CourseGradebook) {
                    Backbone.emulateHTTP = true;
                    var GradeBookRouter = Backbone.Router.extend({
                        initialize: function () {
                            this._views = [];
                            this.courseList = new CourseList();
                            Backbone.history.start();

                            return this;
                        },
                        routes: {
                            "courses": "courses",
                            "gradebook/:id": "show-gradebook"
                        },
                        initPage: function () {
                            $('#wpcontent').css('padding-left', '0px');
                        },
                        clearViews: function () {
                            var self = this;
                            this.initPage();
                            _.each(self._views, function (view) {
                                view.close();
                            });
                            this._views = [];
                        },
                        courses: function () {
                            var self = this;
                            this.clearViews();
                            $('#wpbody-content').prepend($('#ajax-template').html());
                            this.courseList.fetchCourses().then(function (val) {
                                $('.ajax-loader-container').remove();
                                var homeView = new CourseListView({collection: self.courseList});
                                self._views.push(homeView);
                            });
                        },
                        "show-gradebook": function (id) {
                            var self = this;
                            this.clearViews();
                            this.course = new Course({id: parseInt(id)});
                            this.gradebook = new CourseGradebook({gbid: parseInt(id)});
                            $('#wpbody-content').prepend($('#ajax-template').html());
                            Promise.all([this.course.fetchCourse(), this.gradebook.fetchCourseGradebook()]).then(function (values) {
                                $('.ajax-loader-container').remove();
                                var gradeBookView = new GradeBookView({gradebook: self.gradebook, course: self.course});
                                self._views.push(gradeBookView);
                            });
                        }
                    });
                    return GradeBookRouter;
                });