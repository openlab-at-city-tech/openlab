define(['jquery', 'backbone', 'underscore', 'chart'],
        function ($, Backbone, _, Chart) {
            var StatisticsView = Backbone.View.extend({
                id: 'base-modal',
                className: 'modal fade',
                events: {
                    'hidden.bs.modal': 'editCancel',
                    'shown.bs.modal': 'displayChart',
                    'keyup': 'keyPressHandler'
                },
                initialize: function (options) {
                    this.render();
                },
                displayChart: function () {
                    var self = this;
                    $.ajax({
                        url: ajaxurl,
                        data: {
                            action: 'oplb_statistics',
                            chart_type: 'line_chart',
                            gbid: this.model.get('gbid'),
                            uid: this.model.get('id'),
                            nonce: oplbGradebook.nonce
                        },
                        dataType: 'json',
                        success: function (data) {
                            console.log('data', data);
                            var ctx = $('#myChart').get(0).getContext("2d");
                            var options = {
                                legend: {
                                    display: true,
                                    labels: {
                                        fontColor: 'rgb(0, 0, 0)'
                                    }
                                }
                            };
                            var myNewChart = new Chart(ctx, {
                                type: 'line',
                                data: data,
                                options: options,
                            });
                        }
                    });
                },
                render: function () {
                    var self = this;
                    var student = this.model;
                    var template = _.template($('#stats-student-template').html());
                    var compiled = template({student: student});
                    $('body').append(self.$el.html(compiled).el);
                    this.$el.modal('show');
                },
                editCancel: function () {
                    this.$el.data('modal', null);
                    this.remove();
                    return false;
                },
                keyPressHandler: function (e) {
                    if (e.keyCode == 27)
                        this.editCancel();
                    return this;
                }
            });
            return StatisticsView;
        });
	