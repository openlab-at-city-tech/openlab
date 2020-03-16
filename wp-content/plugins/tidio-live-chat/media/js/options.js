/* global jQuery */
jQuery(function ($) {
    var TidioChatWP = {
        apiUrl: 'https://api-v2.tidio.co',
        chatUrl: 'https://www.tidio.com',
        token: null,
        email: '',
        init: function () {
            this.error = $('.error');
            this.form = $('#tidio-start');
            this.form.show();
            var emailField = this.form.find('#email');
            emailField.val('');
            this.form.off().submit(this.onStartSubmit.bind(this));
        },
        onStartSubmit: function () {
            var emailField = this.form.find('#email');
            var submitButton = this.form.find('button');
            if (emailField.val() === '') {
                this.showError('Can’t be empty!');
                return false;
            }
            if (emailField.is(':invalid')) {
                this.showError('Email is invalid!');
                return false;
            }
            this.hideError();
            this.email = emailField.val();
            submitButton.prop('disabled', true).text('Loading...');

            $.get(TidioChatWP.apiUrl + '/access/checkIfEmailIsRegistered', {
                email: emailField.val(),
            }).done(function (data) {
                if (data.status === true && data.value &&
                    data.value.registered === true) {
                    this.form.hide();
                    submitButton.prop('disabled', false).text('Start using Tidio');
                    this.showLoginForm(emailField.val());
                } else {
                    this.redirectToPanel();
                }
            }.bind(this)).fail((function(error) {
                submitButton.prop('disabled', false).text('Start using Tidio');
                if (error && error.status === 429) {
                    this.showError('You have been blocked for too many failed attempts. Please try again in an hour.');
                } else {
                    this.showError('Something went wrong.');
                }

            }).bind(this));
            return false;
        },
        showError: function (message) {
            this.error.text(message).fadeIn();
        },
        hideError: function () {
            this.error.hide();
        },
        showLoginForm: function (emailValue) {
            this.form = $('#tidio-login');
            this.form.css('display', 'flex');
            var emailField = this.form.find('#email');
            emailField.val(emailValue);
            var passwordField = this.form.find('#password');
            passwordField.val('');

            this.form.off().submit(this.onLoginSubmit.bind(this));
        },
        onLoginSubmit: function () {
            var emailField = this.form.find('#email');
            var passwordField = this.form.find('#password');
            var submitButton = this.form.find('button');
            if (emailField.val() === '') {
                this.showError('Email can’t be empty!');
                return false;
            }
            if (emailField.is(':invalid')) {
                this.showError('Email is invalid!');
                return false;
            }
            if (passwordField.val() === '') {
                this.showError('Password can’t be empty!');
                return false;
            }
            this.hideError();
            submitButton.prop('disabled', true).text('Loading...');

            var email = emailField.val();
            var password = document.querySelector(
                '#tidio-login #password').value;
            $.get(TidioChatWP.apiUrl + '/access/getUserToken', {
                email: email,
                password: password,
            }, (function (data) {
                if (data.status === true && data.value !==
                    'ERR_DATA_INVALID') {
                    TidioChatWP.token = data.value;
                    this.getProjects(TidioChatWP.token);
                } else {
                    this.showError('Wrong email or password');
                }
                submitButton.prop('disabled', false).text('Go to Tidio panel');
            }).bind(this), 'json');
            return false;
        },
        addEmailToRedirectLink: function(url) {
            return url + '&tour_default_email=' + encodeURIComponent(this.email);
        },
        redirectToPanel: function () {
            var redirect = function (response) {
                var url = this.addEmailToRedirectLink(response);
                window.open(url, '_blank');
                TidioChatWP.setRedirectLink(url);
                this.form.fadeOut('fast', function () {
                    $('#after-install-text').fadeIn('fast');
                });
            }.bind(this);

            $.post(ajaxurl, {
                    'action': 'get_private_key',
                    '_wpnonce': nonce,
                },
                function (response) {
                    if (response === 'error') {
                        // load through ajax url
                        TidioChatWP.accessThroughXHR(redirect);
                        return false;
                    }
                    redirect(response);
                });
        },
        setRedirectLink: function (url) {
            $('a[href="admin.php?page=tidio-chat"]').
                attr('href', url).
                attr('target', '_blank');
            $('#open-panel-link').attr('href', url);
        },
        renderProjects: function (data) {
            var select_project = $('#select-tidio-project');
            var defaultOption = select_project.children()[0];
            select_project.children().remove();
            select_project.append(defaultOption);
            var selected = false;
            if (data.value.length === 1) {
                selected = true;
            }
            for (var i in data.value) {
                var project = data.value[i];
                var value = {
                    project_id: project.id,
                    private_key: project.private_key,
                    public_key: project.public_key,
                };

                var option = $(
                    '<option value="' + project.id + '" ' + (selected ? 'selected="selected"' : '') + '>' + project.name +
                    '</option>');
                option.data('value', value);
                select_project.append(option);
            }
            this.renderCustomSelect();

        },
        getProjects: function (token) {
            $.get(TidioChatWP.apiUrl + '/project', {
                api_token: token,
            }, (function (response) {
                if (response.value.length === 1) {
                    this.renderProjects(response);
                    this.onProjectSubmit();
                } else {
                    this.form.hide();
                    this.form = $('#tidio-project');
                    this.form.show();
                    this.renderProjects(response);
                    this.form.off().submit(this.onProjectSubmit.bind(this));
                    var startOver = $('#start-over');
                    startOver.click(this.startOver.bind(this));
                }
            }).bind(this), 'json');
        },
        onProjectSubmit: function () {
            var details = $('#select-tidio-project option:selected').data('value');
            $.extend(details, {
                'action': 'set_project_keys',
                'api_token': TidioChatWP.token,
                '_wpnonce': nonce,
            });

            $.post(ajaxurl, details, (function (response) {
                var url = this.addEmailToRedirectLink(response);
                window.open(url, '_blank');
                TidioChatWP.setRedirectLink(url);
                this.form.fadeOut('fast', function () {
                    $('#after-install-text').fadeIn('fast');
                });
            }).bind(this));
            return false;
        },
        startOver: function () {
            this.deleteCustomSelect();
            this.form.hide();
            this.init();
        },
        deleteCustomSelect: function() {
            var selectSelected = this.form.find('.custom-select .select-selected');
            if (selectSelected.length) {
                selectSelected.off().remove();
            }
            var selectItems = this.form.find('.custom-select .select-items');
            if (selectItems.length) {
                selectItems.off().remove();
            }
        },
        renderCustomSelect: function () {

            var customSelect, i, j, select, selectedItem, options, option;
            /* Look for any elements with the class "custom-select": */
            customSelect = document.getElementsByClassName('custom-select');
            for (i = 0; i < customSelect.length; i++) {
                select = customSelect[i].getElementsByTagName('select')[0];
                /* For each element, create a new DIV that will act as the selected item: */
                selectedItem = document.createElement('DIV');
                selectedItem.setAttribute('class', 'select-selected disabled');
                selectedItem.innerHTML = select.options[select.selectedIndex].innerHTML;
                customSelect[i].appendChild(selectedItem);
                /* For each element, create a new DIV that will contain the option list: */
                options = document.createElement('DIV');
                options.setAttribute('class', 'select-items select-hide');
                for (j = 1; j < select.length; j++) {
                    /* For each option in the original select element,
                    create a new DIV that will act as an option item: */
                    option = document.createElement('DIV');
                    option.innerHTML = select.options[j].innerHTML;
                    option.addEventListener('click', function () {
                        /* When an item is clicked, update the original select box,
                        and the selected item: */
                        var y, i, k, s, h;
                        s = this.parentNode.parentNode.getElementsByTagName(
                            'select')[0];
                        h = this.parentNode.previousSibling;
                        for (i = 0; i < s.length; i++) {
                            if (s.options[i].innerHTML === this.innerHTML) {
                                s.selectedIndex = i;
                                h.innerHTML = this.innerHTML;
                                y = this.parentNode.getElementsByClassName(
                                    'same-as-selected');
                                for (k = 0; k < y.length; k++) {
                                    y[k].removeAttribute('class');
                                }
                                this.setAttribute('class', 'same-as-selected');
                                break;
                            }
                        }
                        h.click();
                    });
                    options.appendChild(option);
                }
                customSelect[i].appendChild(options);
                selectedItem.addEventListener('click', function (event) {
                    /* When the select box is clicked, close any other select boxes,
                    and open/close the current select box: */
                    event.stopPropagation();
                    event.preventDefault();
                    closeAllSelect(this);
                    this.nextSibling.classList.toggle('select-hide');
                    this.classList.toggle('select-arrow-active');
                    if (!this.classList.contains('select-arrow-active')) {
                        this.classList.remove('disabled');
                    }
                });
            }

            function closeAllSelect(element) {
                /* A function that will close all select boxes in the document,
                except the current select box: */
                var items, selected, i, arrNo = [];
                items = document.getElementsByClassName('select-items');
                selected = document.getElementsByClassName('select-selected');
                for (i = 0; i < selected.length; i++) {
                    if (element == selected[i]) {
                        arrNo.push(i);
                    } else {
                        selected[i].classList.remove('select-arrow-active');
                    }
                }
                for (i = 0; i < items.length; i++) {
                    if (arrNo.indexOf(i)) {
                        items[i].classList.add('select-hide');
                    }
                }
            }

            /* If the user clicks anywhere outside the select box,
            then close all select boxes: */
            document.addEventListener('click', closeAllSelect);
        },
        accessThroughXHR: function (_func) {

            var xhr_url = TidioChatWP.apiUrl + '/access/external/create?url=' +
                location.protocol + '//' + location.host +
                '&platform=wordpress';
            $.getJSON(xhr_url, {}, function (r) {
                if (!r || !r.value) {
                    alert('Error occured while creating, please try again!');
                    return false;
                }
                _func(TidioChatWP.chatUrl + '/access?privateKey=' +
                    r.value.private_key +
                    '&app=chat&utm_source=platform&utm_medium=wordpress');

                // save this in wordpress database
                $.post(ajaxurl, {
                    'action': 'tidio_chat_save_keys',
                    'public_key': r.value.public_key,
                    'private_key': r.value.private_key,
                    '_wpnonce': nonce,
                });
            }).fail(function () {
                alert('Error occured while creating, please try again!');
            });

        },
    };

    TidioChatWP.init();
});
