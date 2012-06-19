/* Widget Basis */

$(function() {
    /**
     * Gray theme for Highcharts JS
     * @author Torstein Hønsi
     */

    Highcharts.theme = {
        colors: ["#8ACD01", "#FFFFFF", "#55BF3B", "#DF5353", "#aaeeee", "#ff0066", "#eeaaee",
                  "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
        chart: {
            backgroundColor: {
                linearGradient: [255, 255, 255, 50],
                stops: [
                    [0, 'rgba(255, 255, 255, .1)']
                ]
            },
            borderWidth: 0,
            borderRadius: 3,
            plotBackgroundColor: null,
            plotShadow: false,
            plotBorderWidth: 0
        },
        credits: {
            text: ""
        },
        title: {
            style: {
                color: '#FFF',
                font: '16px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
            }
        },
        subtitle: {
            style: {
                color: '#DDD',
                font: '12px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
            }
        },
        xAxis: {
            gridLineWidth: 0,
            lineColor: '#999',
            tickColor: '#FFF',
            categories: ['1','2','3','4','5','6','7'],
            labels: {
                style: {
                    step: 1,
                    color: '#999',
                    font: '9px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
                }
            },
            title: {
                style: {
                    color: '#AAA',
                    font: 'bold 8px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
                }
            }
        },
        yAxis: {
            alternateGridColor: null,
            minorTickInterval: null,
            gridLineColor: 'rgba(255, 255, 255, .1)',
            lineWidth: 0,
            tickWidth: 0,
            labels: {
                style: {
                    color: '#999',
                    font: '9px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
                }
            },
            title: {
                style: {
                    color: '#F00',
                    font: 'bold 9px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
                }
            }
        },
        legend: {
            itemStyle: {
                color: '#CCC'
            },
            itemHoverStyle: {
                color: '#FF0000'
            },
            itemHiddenStyle: {
                color: '#333'
            },
            borderWidth: 0,
        },
        labels: {
            style: {
                color: '#CCC'
            }
        },
        tooltip: {
            backgroundColor: {
                linearGradient: [255, 0, 0, 50],
                stops: [
                    [0, 'rgba(128, 0, 0, .8)'],
                    [1, 'rgba(64, 0, 0, .8)']
                    ]
            },
            borderWidth: 0,
            style: {
                color: '#FFF'
            }
        },


        plotOptions: {
            line: {
                dataLabels: {
                    color: '#CCC'
                },
                marker: {
                    lineColor: '#333'
                }
            },
            spline: {
                marker: {
                    lineColor: '#333'
                }
            },
            scatter: {
                marker: {
                    lineColor: '#333'
                }
            },
            candlestick: {
                lineColor: 'white'
            }
        },

        toolbar: {
            itemStyle: {
                color: '#CCC'
            }
        },

        navigation: {
            buttonOptions: {
                backgroundColor: {
                    linearGradient: [0, 0, 0, 20],
                    stops: [
                        [0.4, '#606060'],
                        [0.6, '#333333']
                        ]
                },
                borderColor: '#000000',
                symbolStroke: '#C0C0C0',
                hoverSymbolStroke: '#FFFFFF'
            }
        },

        exporting: {
            buttons: {
                exportButton: {
                    symbolFill: '#55BE3B'
                },
                printButton: {
                    symbolFill: '#7797BE'
                }
            }
        },

        // scroll charts
        rangeSelector: {
            buttonTheme: {
                fill: {
                    linearGradient: [0, 0, 0, 20],
                    stops: [
                        [0.4, '#888'],
                        [0.6, '#555']
                        ]
                },
                stroke: '#000000',
                style: {
                    color: '#CCC',
                    fontWeight: 'bold'
                },
                states: {
                    hover: {
                        fill: {
                            linearGradient: [0, 0, 0, 20],
                            stops: [
                                [0.4, '#BBB'],
                                [0.6, '#888']
                                ]
                        },
                        stroke: '#000000',
                        style: {
                            color: 'white'
                        }
                    },
                    select: {
                        fill: {
                            linearGradient: [0, 0, 0, 20],
                            stops: [
                                [0.1, '#000'],
                                [0.3, '#333']
                                ]
                        },
                        stroke: '#000000',
                        style: {
                            color: 'yellow'
                        }
                    }
                }
            },
            inputStyle: {
                backgroundColor: '#333',
                color: 'silver'
            },
            labelStyle: {
                color: 'silver'
            }
        },

        navigator: {
            handles: {
                backgroundColor: '#666',
                borderColor: '#AAA'
            },
            outlineColor: '#CCC',
            maskFill: 'rgba(16, 16, 16, 0.5)',
            series: {
                color: '#7798BF',
                lineColor: '#A6C7ED'
            }
        },

        scrollbar: {
            barBackgroundColor: {
                linearGradient: [0, 0, 0, 20],
                stops: [
                    [0.4, '#888'],
                    [0.6, '#555']
                    ]
            },
            barBorderColor: '#CCC',
            buttonArrowColor: '#CCC',
            buttonBackgroundColor: {
                linearGradient: [0, 0, 0, 20],
                stops: [
                    [0.4, '#888'],
                    [0.6, '#555']
                    ]
            },
            buttonBorderColor: '#CCC',
            rifleColor: '#FFF',
            trackBackgroundColor: {
                linearGradient: [0, 0, 0, 10],
                stops: [
                    [0, '#000'],
                    [1, '#333']
                ]
            },
            trackBorderColor: '#666'
        },

        // special colors for some of the demo examples
        legendBackgroundColor: 'rgba(48, 48, 48, 0.8)',
        legendBackgroundColorSolid: 'rgb(70, 70, 70)',
        dataLabelsColor: '#444',
        textColor: '#E0E0E0',
        maskColor: 'rgba(255,255,255,0.3)'
    };

    // Apply the theme
    var highchartsOptions = Highcharts.setOptions(Highcharts.theme);
    var leistungsChart;
    var studienverlaufChart;
    
    $(document).ready(function() {

        leistungsChart = new Highcharts.Chart({
            colors: ["#CCCCCC", "#228811"],
            chart: {
                renderTo: 'leistungsContainer',
                type: 'bar'
            },
            credits: {
                text: null
            },
            title: {
                text: null
            },
            xAxis: {
                categories: ['Fortschritt'],
                title: {
                    text: null
                },
                labels: {
                    enabled: false,
                },
                
            },

            yAxis: {
             //   categories: ['30'],
             //   min: 0,
             //   max: 210,
                title: {
                    text: null
                },
                stackLabels: {
                    enabled: false,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'red'
                    }
                },
                plotLines: [{
                    color: 'rgba(48, 48, 48, 0.8)',
                    value: 190,
                    width: '2px'
                }]
            },
            legend: {
                enabled: false,
                floating: false,
                reversed: true,
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'top',
                x: 0,
                y: -10,
                backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColorSolid) || 'white',
                borderColor: '#CCC',
                borderWidth: 0,
                shadow: false,
                itemStyle: {
                    cursor: 'pointer',
                    color: '#999999'
                }
            },
            tooltip: {
                formatter: function() {
                    var tooltipMessage = '' +
                    this.series.name + ': ' + this.y + ' CP';
                    return tooltipMessage;
                }
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            return this.y + ' CP';
                        },
                        align: 'center',
                        color: '#FFFFFF'
                    },
                },
                series: {
                    stacking: 'normal',
                    animation: {
                        duration: 2000,
                        easing: 'swing'
                    }
                },
            },
            // JSON Anleitung:
            // http://stackoverflow.com/a/9845251
            series: [{
                name: 'verbleibend',
                data: [{
                    color: '#EEE',
                    // der Wert für bereits erreichte CP
                    // müsste vorher errechnet und via JSON
                    // ausgeliefert werden
                    y: (210 - 90)
                }],
                dataLabels: {
                    color: '#999999',
                    fontWeight: 'bold'
                },
            },
            {
                name: 'erreicht',
                data: [{
                    color: '#228811',
                    y: 90}]
            }]
            });
        
        // Studienverlauf Chart
        studienverlaufChart = new Highcharts.Chart({
            chart: {
                renderTo: 'studienverlaufContainer',
                type: 'area'
            },
            title: {
                text: 'Studienverlauf'

            },

            subtitle: {

            },

            xAxis: {

                labels: {

                    formatter: function() {

                        return this.value; // clean, unformatted number for year
                    }

                }

            },

            yAxis: {
                min: 0,
                gridLineColor: 'rgba(245, 245, 245, 1)',
                categories: ['70','140','210'],
                title: {
                    rotation: 0,
                    text: 'CP',
                    align: 'high',
                    offset: 10
                },
                labels: {
                    step: 70,
                    formatter: function() {
                        return this.value;
                    },
                 //   y: 3
                }
            },

            tooltip: {

                formatter: function() {
                    var tooltipMessage = "Du hast <b>" + Highcharts.numberFormat( this.y , 0 ) + " von 210 Creditpoints </b><br\\>im " + this.x + ". Semester erreicht.";
                    
                    return tooltipMessage;

                }

            },

            plotOptions: {

                area: {

                    pointStart: 0,

                    marker: {

                        enabled: false,

                        symbol: 'circle',

                        radius: 4,



                        states: {

                            hover: {

                                enabled: false

                            },


                            select: {

                                enabled: true
                            }
                        }
                    }

                }

            },

            series: [{

                name: 'Gesamt Creditpoints',

                data: [30, 60, 90, 120, 150, 180, 210]

                },
            {

                name: 'Erreichte Creditpoints',
                marker: {
                    enabled: true,
                    states: {

                        hover: {

                            enabled: true

                        }
                    }
                },
                showInLegend: false,
                data: [{
                    fillColor: '#FF0000',
                    y: 20},
                {
                    fillColor: '#FF0000',
                    y: 70},
                {
                    fillColor: '#FF0000',
                    y: 80},
                {
                    fillColor: '#FF0000',
                    y: 90},
                {
                    fillColor: '#FF0000',
                    y: 100},
                {
                    fillColor: '#FF0000',
                    y: 160},
                {
                    fillColor: '#FF0000',
                    y: 210}]

                }]

        });
        });


    });