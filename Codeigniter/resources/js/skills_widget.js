/*
 Logbook Skills Widget JS

 JS / jQuery Stuff for the logbook skills widget on the dashboard

 (c) Christian Kundruss (CK), 2012
 <christian.kundruss@fh-duesseldorf.de>
 */
$(function() {

    // global variables
    var skill_widget_chart;

    /*
     * If the document has finished loading, prepare and display the widgets chart
     */
    $(document).ready(function(){

        // setup and display the widget chart
        setupSkillWidgetChart();



    });

    /*
     * Setups and displays the skill_widget_chart
     */
    function setupSkillWidgetChart() {
        attendanceChart = new Highcharts.Chart({
            colors: ["#CCCCCC", "#228811"],
            chart: {
                renderTo: 'chartContainer',
                type: 'bar',
                backgroundColor:'rgba(255, 255, 255, 0.0)'
            },

            xAxis: {
                labels: {
                    enabled: false
                }
            },

            yAxis: {
                title: {
                    text: null
                },
                labels: {
                    enabled: false
                },
                plotLines: [{
                    color: 'rgba(48, 48, 48, 0.8)',
                    value: 190,
                    width: '2px'
                }]
            },

            legend: {
                enabled: false
            },

            credits: {
                enabled: false
            },
            tooltip: {
                formatter: function() {
                    var tooltipMessage = '' +
                        this.series.name + ': ' + this.y + ' %';
                    return tooltipMessage;
                }
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            return this.y + ' %';
                        },
                        align: 'center',
                        color: '#FFFFFF'
                    }
                },
                series: {
                    stacking: 'normal',
                    animation: {
                        duration: 2000,
                        easing: 'swing'
                    }
                }
            },


            series: [{
                name: 'fehlendes Wissen',
                data: [{
                    color: '#EEE',
                    // der Wert für bereits erreichte CP
                    // müsste vorher errechnet und via JSON
                    // ausgeliefert werden
                    y: missing_skills
                }],
                dataLabels: {
                    color: '#999999',
                    fontWeight: 'bold'
                }
            },
                {
                    name: 'vorhandenes Wissen',
                    data: [{
                        color: '#228811',
                        y: act_skills}]
                }]
        });
    }
});