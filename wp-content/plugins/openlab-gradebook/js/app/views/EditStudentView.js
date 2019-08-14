define(['jquery', 'backbone', 'underscore', 'models/User', 'models/UserList', 'bootstrap3-typeahead'],
        function ($, Backbone, _, User, UserList, typeahead) {
            var EditStudentView = Backbone.View.extend({
                id: 'base-modal',
                className: 'modal fade',
                events: {
                    'hidden.bs.modal': 'editCancel',
                    'shown.bs.modal': 'populateStudentDropdown',
                    'click #edit-student-save': 'submitForm',
                    'submit #edit-student-form': 'editSave',
                    'change #selectStudentRange': 'handleStudentRangeSelection'
                },
                initialize: function (options) {
                    this.course = options.course;
                    this.gradebook = options.gradebook;
                    this.minLength = 2;
                    this.student = this.model || null;
                    this.userList = new UserList();
                    $('body').append(this.render().el);
                    return this;
                },
                render: function () {
                    var self = this;
                    var template = _.template($('#edit-student-template').html());
                    var compiled = template({student: this.student, course: this.course});
                    self.$el.html(compiled);
                    this.$el.modal('show');
                    return self.el;
                },
                editCancel: function () {
                    this.$el.data('modal', null);
                    this.remove();
                    return false;
                },
                submitForm: function () {
                    $('#edit-student-form').submit();
                },
                editSave: function (ev) {
                    var self = this;
                    var studentInformation = $(ev.currentTarget).serializeObject();

                    delete(studentInformation['id']);
                    var toadds = new User(studentInformation);
                    toadds.save(studentInformation, {success: function (model) {
                            _.each(model.get('cells'), function (cell) {
                                self.gradebook.cells.add(cell);
                            });

                            if (model.get('type') === 'all') {

                                _.each(model.get('students'), function (_student) {
                                    self.gradebook.students.add(_student);
                                });

                            } else {
                                var _student = new User(model.get('student'));
                                self.gradebook.students.add(_student);
                            }

                            self.$el.modal('hide');
                        }
                    });
                    
                    return false;
                },
                handleStudentRangeSelection: function () {

                    $('#studentAddWrapper').toggleClass('add-all add-single');

                },
                populateStudentDropdown: function () {
                    var self = this;

                    this.$el.find('#studentRangeAll').focus();

                    this.userList.fetch({success: function (model, response, options) {

                            if (typeof response.error !== 'undefined') {

                                var message = 'Problem retrieving student list';
                                
                                switch (response.error) {
                                    case 'no_bp':

                                        //in the case of no BuddyPress install, just switch to a regular input field
                                        var new_field = '<input class="form-control" type="text" name="id-exists" id="user_login"/>';
                                        self.$el.find('#user_login_wrapper').html(new_field);
                                        self.$el.find('#edit-student-save').removeAttr('disabled').text('Add');

                                        return false;

                                        break;
                                    case 'no_site':

                                        message = 'Unable to find site';
                                        self.$el.closest('.modal-content').find('#edit-student-save').text('No Site');

                                        break;
                                    case 'no_students':

                                        message = 'No students have joined this course.';
                                        self.$el.find('#user_login_wrapper select').attr('disabled', 'disabled');
                                        self.$el.find('#edit-student-save').text('No Students');

                                        break;
                                }

                                var optionOut = '<option value="0">' + message + '</option>';
                                self.$el.find('#user_login').html(optionOut);

                                return false;

                            }

                            self.$el.find('#user_login').html('');

                            _(self.userList.models).each(function (user) {

                                var name = user.get('user_login');

                                if (user.get('first_name') !== '' && user.get('last_name') !== '') {
                                    name = user.get('first_name') + ' ' + user.get('last_name');
                                }

                                var optionOut = '<option value="' + user.get('user_login') + '">' + name + '</option>';
                                self.$el.find('#user_login').append(optionOut);

                            });

                            self.$el.find('#user_login_wrapper select').removeAttr('disabled');
                            self.$el.find('#edit-student-save').removeAttr('disabled').text('Add');

                        }});

                }
            });
            return EditStudentView;
        });