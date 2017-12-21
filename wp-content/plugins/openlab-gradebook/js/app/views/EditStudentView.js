define(['jquery', 'backbone', 'underscore', 'models/User', 'models/UserList', 'bootstrap3-typeahead'],
        function ($, Backbone, _, User, UserList, typeahead) {
            var EditStudentView = Backbone.View.extend({
                id: 'base-modal',
                className: 'modal fade',
                events: {
                    'hidden.bs.modal': 'editCancel',
                    'shown.bs.modal': 'populateStudentDropdown',
                    'keyup': 'keyPressHandler',
                    'click #edit-student-save': 'submitForm',
                    'submit #edit-student-form': 'editSave',
                    'input #user_login': 'loginSearch',
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
                    console.log('this.userList on render', this.userList);
                    var compiled = template({student: this.student, course: this.course});
                    self.$el.html(compiled);
                    this.$el.modal('show');
                    return self.el;
                },
                keyPressHandler: function (e) {
                    if (e.keyCode == 27)
                        this.editCancel();
                    if (e.keyCode == 13)
                        this.submitForm();
                    return this;
                },
                getUsersLogin: function () {
                    this.availableTags = [
                        "testing"
                    ];
                },
                editCancel: function () {
                    this.$el.data('modal', null);
                    this.remove();
                    return false;
                },
                submitForm: function () {
                    $('#edit-student-form').submit();
                },
                loginSearch: function () {
                    var self = this;
                    this.userList.search = $('#user_login').val();
                    if (this.userList.search.length < 2) {
                        $('#user_login').typeahead('destroy');
                        return false;
                    }
                    if (this.userList.search.length === 2) {
                        this.userList.fetch({success: function () {
                                var users = _.map(self.userList.models, function (user) {
                                    var filtered_user = user.get('data').user_login;
                                    return filtered_user;
                                });
                                $('#user_login').typeahead({source: users});
                            }});
                    }
                    return this;
                },
                editSave: function (ev) {
                    var self = this;
                    var studentInformation = $(ev.currentTarget).serializeObject();
                    console.log('this.student in editSave', this.student);
                    if (this.student) {
                        studentInformation.id = parseInt(studentInformation.id);
                        this.student.save(studentInformation, {
                            wait: true,
                            success: function (model, response) {
                                console.log('model on student editSave success', model, response);
                                Backbone.pubSub.trigger('updateWeightInfo', response);
                            }
                        });
                        this.$el.modal('hide');
                    } else {
                        delete(studentInformation['id']);
                        var toadds = new User(studentInformation);
                        toadds.save(studentInformation, {success: function (model) {
                                console.log('model in toadds editSave editStudentView', model);
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
                    }
                    return false;
                },
                handleStudentRangeSelection: function () {

                    $('#studentAddWrapper').toggleClass('add-all add-single');

                },
                populateStudentDropdown: function () {
                    var self = this;

                    this.userList.fetch({success: function (model, response, options) {

                            if (typeof response.error !== 'undefined') {

                                var message = 'Problem retrieving student list';
                                
                                console.log('response.error', response.error);

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

                            console.log('done');

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