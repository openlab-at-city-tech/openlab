window.addEventListener("load", (event) => {
    am4core.useTheme(am4themes_animated);
    var chartID = document.getElementsByClassName("ays_quiz_chart_div");

    console.log(am4core);
    console.log(chartID);

    if( chartID.length > 0 ){

        for (var i = 0; i < chartID.length; i++) {
            
            var chart_id = chartID[i].id;

            if(document.getElementById(chart_id)){

                var data_id = document.getElementById(chart_id).getAttribute("data-id");
                var chart_data = JSON.parse(window.atob(window.aysQuizIntervalsChartData[data_id]));

                // Create chart
                var chart6 = am4core.create(chart_id, am4charts.PieChart);
                chart6.hiddenState.properties.opacity = 0; // this creates initial fade-in
                
                chart6.data = ( typeof  chart_data != "undefined" ) ? chart_data.keywords : {};
                var data_percentage = ( typeof chart_data != "undefined" ) ? chart_data.keyword_percentage : {};

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

				var if_mobile = aysQuizdetectMob();
				if( if_mobile ){
                	pieSeries.responsive.enabled = true;
				}

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
        }

    }

});

function aysQuizdetectMob() {
    var toMatch = new Array(
        /Android/i,
        /webOS/i,
        /iPhone/i,
        /iPad/i,
        /iPod/i,
        /BlackBerry/i,
        /Windows Phone/i
    );
    
    return toMatch.some( function(toMatchItem) {
        return navigator.userAgent.match(toMatchItem);
    });
}

