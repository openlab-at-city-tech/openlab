//cahrt 1
am4core.useTheme(am4themes_animated);
if(document.getElementById("chart1_div")){
    var chart = am4core.create("chart1_div", am4charts.XYChart);

    chart.data = ( typeof chart1_data != "undefined" ) ? chart1_data : {};

    chart.dateFormatter.inputDateFormat = "yyyy-MM-dd";

    var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    // Create series
    var series = chart.series.push(new am4charts.LineSeries());
    series.dataFields.valueY = "value";
    series.dataFields.dateX = "date";
    series.tooltipText = "{value}";
    series.strokeWidth = 2;
    series.minBulletDistance = 15;

    // Drop-shaped tooltips
    series.tooltip.background.cornerRadius = 0;
    series.tooltip.background.strokeOpacity = 0;
    series.tooltip.pointerOrientation = "vertical";
    series.tooltip.label.minWidth = 40;
    series.tooltip.label.minHeight = 40;
    series.tooltip.label.textAlign = "middle";
    series.tooltip.label.textValign = "middle";

    // Make bullets grow on hover
    var bullet = series.bullets.push(new am4charts.CircleBullet());
    bullet.circle.strokeWidth = 1;
    bullet.circle.radius = 4;
    bullet.circle.fill = am4core.color("#fff");

    var bullethover = bullet.states.create("hover");
    bullethover.properties.scale = 1.3;

    // Make a panning cursor
    chart.cursor = new am4charts.XYCursor();
    chart.cursor.behavior = "panXY";
    chart.cursor.xAxis = dateAxis;
    chart.cursor.snapToSeries = series;


    // Create a horizontal scrollbar with previe and place it underneath the date axis
    chart.scrollbarX = new am4charts.XYChartScrollbar();
    chart.scrollbarX.series.push(series);
    chart.scrollbarX.parent = chart.bottomAxesContainer;
    valueAxis.integersOnly = true;

    chart.events.on("ready", function () {
        dateAxis.zoom({start:0, end:1});
    });
}

///chart 2

if(document.getElementById("chart2_div")){
    var chart2 = am4core.create("chart2_div", am4charts.RadarChart);
    // Add data
    chart2.data = ( typeof chart2_data != "undefined" ) ? chart2_data : {};

    // Make chart not full circle
    chart2.startAngle = -90;
    chart2.endAngle = 180;
    chart2.innerRadius = am4core.percent(20);

    // Set number format
    chart2.numberFormatter.numberFormat = "#.#'%'";

    // Create axes
    var categoryAxis = chart2.yAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "question";
    categoryAxis.renderer.grid.template.location = 0;
    categoryAxis.renderer.grid.template.strokeOpacity = 0;
    categoryAxis.renderer.labels.template.horizontalCenter = "right";
    categoryAxis.renderer.labels.template.fontWeight = 500;
    categoryAxis.renderer.labels.template.adapter.add("fill", function(fill, target) {
        return (target.dataItem.index >= 0) ? chart2.colors.getIndex(target.dataItem.index) : fill;
    });
    categoryAxis.renderer.minGridDistance = 10;

    var valueAxis = chart2.xAxes.push(new am4charts.ValueAxis());
    valueAxis.renderer.grid.template.strokeOpacity = 0;
    valueAxis.min = 0;
    valueAxis.max = 100;
    valueAxis.strictMinMax = true;

    // Create series
    var series1 = chart2.series.push(new am4charts.RadarColumnSeries());
    series1.dataFields.valueX = "full";
    series1.dataFields.categoryY = "question";
    series1.clustered = false;
    series1.columns.template.fill = new am4core.InterfaceColorSet().getFor("alternativeBackground");
    series1.columns.template.fillOpacity = 0.08;
    series1.columns.template.cornerRadiusTopLeft = 20;
    series1.columns.template.strokeWidth = 0;
    series1.columns.template.radarColumn.cornerRadius = 20;

    var series2 = chart2.series.push(new am4charts.RadarColumnSeries());
    series2.dataFields.valueX = "count";
    series2.dataFields.categoryY = "question";
    series2.clustered = false;
    series2.columns.template.strokeWidth = 0;
    series2.columns.template.tooltipText = "{question}: [bold]{count}[/]"+"%";
    series2.columns.template.radarColumn.cornerRadius = 20;

    series2.columns.template.adapter.add("fill", function(fill, target) {
        return chart2.colors.getIndex(target.dataItem.index);
    });

    // Add cursor
    chart2.cursor = new am4charts.RadarCursor();
}

function aysQuizzesChart(chartData){
    // Create chart
    var chart = am4core.create("chart_quizzes_div", am4charts.XYChart);
    chart.paddingRight = 20;

    chart.data = generateChartData(chartData);

    var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
    dateAxis.baseInterval = {
      "timeUnit": "minute",
      "count": 1
    };
    dateAxis.tooltipDateFormat = "d MMMM";

    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.tooltip.disabled = false;

    var series = chart.series.push(new am4charts.LineSeries());
    series.dataFields.dateX = "date";
    series.dataFields.valueY = "visits";
    series.tooltipText = AysQuizQuestionChartObj.completes + ": [bold]{valueY}[/]";
    series.fillOpacity = 0.3;


    chart.cursor = new am4charts.XYCursor();
    chart.cursor.lineY.opacity = 0;
    chart.scrollbarX = new am4charts.XYChartScrollbar();
    chart.scrollbarX.series.push(series);


    chart.events.on("datavalidated", function () {
        dateAxis.zoom({start:0.8, end:1});
    });
}

(function($){
    $(document).ready(function(){
        if(document.getElementById("chart_quizzes_div")){
            var quiz_id = 0;
            var action = 'get_current_quiz_statistic';
            var $this = $(document).find('#quiz_stat_select');
            $this.parent().find('img.loader').removeClass('display_none');
            $.ajax({
                type: "POST",
                url: quiz_maker_ajax.ajax_url,
                data: {
                    quiz_id: quiz_id,
                    action: action
                },
                dataType: "json",
                success: function (response) {
                    $("#ays_quiz_stat").remove();
                    $("#chart-container").append('<canvas id="ays_quiz_stat" width="400" height="400"></canvas>');
                    var dates = response.dates;
                    var dates_values = response.dates_values;
                    var ctx = $("#ays_quiz_stat");
                    dates_values.push(Math.max.apply(Math,dates_values)+Math.round(Math.max.apply(Math,dates_values)/5));
                    aysQuizzesChart({dates: dates, values: dates_values});
                    $('.ays-collection').children().not(':first').remove();
                    $('.ays-collection').append(response.charts);

                    $this.parent().find('img.loader').addClass('display_none');
                }
            });
        }
    });
})(jQuery);


function generateChartData(chartDatas) {
    var chartData = [];
    for (var i = 0; i < chartDatas.dates.length; i++) {
        chartData.push({
            date: chartDatas.dates[i],
            visits: chartDatas.values[i]
        });            
    }
    return chartData;
}

if(document.getElementById("chart_quiz_div")){

    // Create chart instance
    var chart = am4core.create("chart_quiz_div", am4charts.PieChart);

    // Set data
    var selected;
    var types = [];

    // Add data
    if( typeof chart3_data != "undefined" && parseInt(chart3_data.guests) !== 0){
        types.push({
            type: AysQuizQuestionChartObj.guest,
            percent: chart3_data.guests,
            color: chart.colors.getIndex(4),
        });
    }
        
    if( typeof chart3_data != "undefined" && parseInt(chart3_data.loggedIn) !== 0){
        types.push({
            type: AysQuizQuestionChartObj.loggedInUsers,
            percent: chart3_data.loggedIn,
            color: chart.colors.getIndex(1),
            subs: chart3_data.userRoles
        });
    }
    chart.data = generateChartData();
    
    // Set inner radius
//    chart.innerRadius = am4core.percent(30);
    chart.outerWidth = 0;
    chart.paddingRight = 120;
    chart.paddingLeft = 120;
    chart.responsive.enabled = true;

    // Add and configure Series
    var pieSeries = chart.series.push(new am4charts.PieSeries());
    pieSeries.dataFields.value = "percent";
    pieSeries.dataFields.category = "type";
    pieSeries.slices.template.propertyFields.fill = "color";
    pieSeries.slices.template.propertyFields.isActive = "pulled";
    pieSeries.slices.template.strokeWidth = 0;
    
    pieSeries.slices.template.stroke = am4core.color("#fff");
    pieSeries.slices.template.strokeWidth = 1;
    pieSeries.slices.template.strokeOpacity = 0.7;

    // This creates initial animation
    pieSeries.hiddenState.properties.opacity = 0;
    pieSeries.hiddenState.properties.endAngle = -90;
    pieSeries.hiddenState.properties.startAngle = -90;
    pieSeries.alignLabels = false;
    pieSeries.ticks.template.disabled = true;
    
    function generateChartData() {
      var chartData = [];
      for (var i = 0; i < types.length; i++) {
        if (i == selected) {
            if(typeof types[i].subs !== 'undefined'){
              for (var x = 0; x < types[i].subs.length; x++) {
                chartData.push({
                  type: types[i].subs[x].type+"s",
                  percent: types[i].subs[x].percent,
                  color: types[i].color,
                  pulled: true
                });
              }
            } else {
              chartData.push({
                type: types[i].type+"s",
                percent: types[i].percent,
                color: types[i].color,
                id: i
              });
            }
        } else {
          chartData.push({
            type: types[i].type+"s",
            percent: types[i].percent,
            color: types[i].color,
            id: i
          });
        }
      }
      return chartData;
    }

    pieSeries.slices.template.events.on("hit", function(event) {
      if (event.target.dataItem.dataContext.id != undefined) {
        selected = event.target.dataItem.dataContext.id;
      } else {
        selected = undefined;
      }
      chart.data = generateChartData();
    });
    
}


if(document.getElementById("chart3_div")){

    // Create chart instance
    var chart3 = am4core.create("chart3_div", am4charts.XYChart);

    // Add data
    chart3.data = ( typeof chart4_data != "undefined" ) ? chart4_data.scores : {};
    
    // Create axes
    var categoryAxis = chart3.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "score";
    categoryAxis.renderer.grid.template.location = 0;
    categoryAxis.renderer.minGridDistance = 30;
    categoryAxis.renderer.labels.template.adapter.add("text", function(text) {
        return text + "%";
    });
//    categoryAxis.renderer.labels.template.adapter.add("dy", function(dy, target) {
//      if (target.dataItem && target.dataItem.index & 2 == 2) {
//        return dy + 25;
//      }
//      return dy;
//    });

    var valueAxis = chart3.yAxes.push(new am4charts.ValueAxis());

    // Create series
    var series = chart3.series.push(new am4charts.ColumnSeries());
    series.dataFields.valueY = "count";
    series.dataFields.categoryX = "score";
    series.name = AysQuizQuestionChartObj.users;
    series.columns.template.tooltipText = AysQuizQuestionChartObj.count + ": [bold]{valueY}[/]";
    series.columns.template.fillOpacity = .8;

    var columnTemplate = series.columns.template;
    columnTemplate.strokeWidth = 2;
    columnTemplate.strokeOpacity = 1;
}

if(document.getElementById("chart4_div")){

    // Create chart
    var chart4 = am4core.create("chart4_div", am4charts.PieChart);
    chart4.hiddenState.properties.opacity = 0; // this creates initial fade-in
    
    chart4.data = ( typeof chart4_data != "undefined" ) ? chart4_data.intervals : {};

    // Add and configure Series
    var pieSeries = chart4.series.push(new am4charts.PieSeries());
    pieSeries.dataFields.value = "count";
    pieSeries.dataFields.category = "interval";

    // Let's cut a hole in our Pie chart the size of 30% the radius
    chart4.innerRadius = am4core.percent(30);

    // Put a thick white border around each Slice
    pieSeries.slices.template.stroke = am4core.color("#fff");
    pieSeries.slices.template.strokeWidth = 2;
    pieSeries.slices.template.strokeOpacity = 1;
    pieSeries.slices.template.tooltipText = AysQuizQuestionChartObj.interval + ": [bold]{interval}[/]\n"+ AysQuizQuestionChartObj.users +": [bold]{count}[/]";
    pieSeries.slices.template
      // change the cursor on hover to make it apparent the object can be interacted with
      .cursorOverStyle = [
        {
          "property": "cursor",
          "value": "pointer"
        }
      ];

    pieSeries.alignLabels = true;
    pieSeries.labels.template.bent = true;
    pieSeries.labels.template.text = "[bold]{interval}[/]: {count} "+ AysQuizQuestionChartObj.users2;
    pieSeries.labels.template.radius = 3;
    pieSeries.labels.template.padding(0,0,0,0);

    pieSeries.ticks.template.disabled = false;

    // Create a base filter effect (as if it's not there) for the hover to return to
    var shadow = pieSeries.slices.template.filters.push(new am4core.DropShadowFilter);
    shadow.opacity = 0;

    // Create hover state
    var hoverState = pieSeries.slices.template.states.getKey("hover"); // normally we have to create the hover state, in this case it already exists

    // Slightly shift the shadow and make it more prominent on hover
    var hoverShadow = hoverState.filters.push(new am4core.DropShadowFilter);
    hoverShadow.opacity = 0.7;
    hoverShadow.blur = 5;

}

if(document.getElementById("chart5_div")){
    // Create chart
    var chart5 = am4core.create("chart5_div", am4charts.PieChart);
    chart5.hiddenState.properties.opacity = 0; // this creates initial fade-in

    chart5.data = ( typeof chart5_data != "undefined" ) ? chart5_data : {};

    // Add and configure Series
    var pieSeries = chart5.series.push(new am4charts.PieSeries());
    pieSeries.dataFields.value = "percent";
    pieSeries.dataFields.category = "cat_name";

    // Let's cut a hole in our Pie chart the size of 30% the radius
    // chart4.innerRadius = am4core.percent(30);

    // Put a thick white border around each Slice
    pieSeries.slices.template.stroke = am4core.color("#fff");
    pieSeries.slices.template.strokeWidth = 2;
    pieSeries.slices.template.strokeOpacity = 1;
    pieSeries.slices.template.tooltipText = AysQuizQuestionChartObj.category + ": [bold]{cat_name}[/]\n"+ AysQuizQuestionChartObj.percent +": [bold]{percent}[/]";
    pieSeries.slices.template
      // change the cursor on hover to make it apparent the object can be interacted with
      .cursorOverStyle = [
        {
          "property": "cursor",
          "value": "pointer"
        }
      ];

    pieSeries.alignLabels = true;
    pieSeries.labels.template.bent = true;
    pieSeries.labels.template.text = "{cat_name}: [bold]{percent}[/]";
    pieSeries.labels.template.radius = 3;
    pieSeries.labels.template.padding(0,0,0,0);

    pieSeries.ticks.template.disabled = false;

    // Create a base filter effect (as if it's not there) for the hover to return to
    var shadow = pieSeries.slices.template.filters.push(new am4core.DropShadowFilter);
    shadow.opacity = 0;

    // Create hover state
    var hoverState = pieSeries.slices.template.states.getKey("hover"); // normally we have to create the hover state, in this case it already exists

    // Slightly shift the shadow and make it more prominent on hover
    var hoverShadow = hoverState.filters.push(new am4core.DropShadowFilter);
    hoverShadow.opacity = 0.7;
    hoverShadow.blur = 5;

}

if(document.getElementById("chart6_div")){

    // Create chart
    var chart6 = am4core.create("chart6_div", am4charts.PieChart);
    chart6.hiddenState.properties.opacity = 0; // this creates initial fade-in
    
    chart6.data = ( typeof chart6_data != "undefined" ) ? chart6_data.keywords : {};
    var data_percentage = ( typeof chart6_data != "undefined" ) ? chart6_data.keyword_percentage : {};

    // Add and configure Series
    var pieSeries = chart6.series.push(new am4charts.PieSeries());
    pieSeries.dataFields.value = "count";
    pieSeries.dataFields.category = "keyword";

    // Let's cut a hole in our Pie chart the size of 30% the radius
    chart6.innerRadius = am4core.percent(30);

    // Put a thick white border around each Slice
    pieSeries.slices.template.stroke = am4core.color("#fff");
    pieSeries.slices.template.strokeWidth = 2;
    pieSeries.slices.template.strokeOpacity = 1;
    pieSeries.slices.template.tooltipText = AysQuizQuestionChartObj.keyword + ": [bold]{keyword}[/]\n"+ AysQuizQuestionChartObj.count +": [bold]{count}[/]";
    pieSeries.slices.template
      // change the cursor on hover to make it apparent the object can be interacted with
      .cursorOverStyle = [
        {
          "property": "cursor",
          "value": "pointer"
        }
      ];

    pieSeries.alignLabels = true;
    pieSeries.labels.template.bent = true;
    // pieSeries.labels.template.text = "[bold]{keyword}[/]: {count} "+ AysQuizQuestionChartObj.users2;
    pieSeries.labels.template.text = "[bold]{keyword}[/]: {count} ";
    pieSeries.labels.template.radius = 3;
    pieSeries.labels.template.padding(0,0,0,0);

    pieSeries.ticks.template.disabled = false;

    // Create a base filter effect (as if it's not there) for the hover to return to
    var shadow = pieSeries.slices.template.filters.push(new am4core.DropShadowFilter);
    shadow.opacity = 0;

    // Create hover state
    var hoverState = pieSeries.slices.template.states.getKey("hover"); // normally we have to create the hover state, in this case it already exists

    // Slightly shift the shadow and make it more prominent on hover
    var hoverShadow = hoverState.filters.push(new am4core.DropShadowFilter);
    hoverShadow.opacity = 0.7;
    hoverShadow.blur = 5;

}

