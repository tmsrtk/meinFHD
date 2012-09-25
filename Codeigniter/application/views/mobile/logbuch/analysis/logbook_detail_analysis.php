<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Auswertungen zu [Kurskurzname]<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>

<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>
<div class="well well-small">
    <div class="row-fluid">
        <div class="span12">
            <a href="<?php print base_url('logbuch_analysis/show_possible_courses'); ?>" class="btn btn-medium btn-danger" style="font-weight: bold;"><i class="icon-arrow-left icon-white"></i>&nbsp;Auswertungsmen&uuml;</a>
        </div>
    </div><!-- END header with backlink -->
    <hr/>
    <div class="container-fluid">
        <h3>Kenntnisse</h3>
        <br/>
        <div class="row-fluid">
            <div class="span8" id="skillsChart" style="max-width:500px; height: 200px;"></div>
            <div class="span4">
                <p>Aktuell beherrschst du alle Themen durchschnittlich zu <span style="color: red;">x%</span>.</p>
                <p>[MOTIVATIONSTEXT]</p>
                <p>Mach weiter so!</p>
            </div>
        </div>
    </div><!-- /div kenntnisse container -->
    <hr/>
    <div class="container-fluid">
        <h3>Anwesenheit</h3>
        <br/>
        <div class="row-fluid">
            <div class="span8" id="attendanceChart" style="max-width: 500px; height:200px;"></div>
            <div class="span4">
                <p>Du warst im bisherigen Semester insgesamt <span style="color: red;"><strong><?php echo $attended_events; ?></strong></span> von <strong><?php echo $max_semester_weeks; # max events equal to max semesterweeks ?></strong>
                    mal da!</p>
                <p>[MOTIVATIONSTEXT]</p>
            </div>
        </div>
    </div>
    <hr/>
</div><!-- /div well well -->
<?php endblock(); ?>

<?php startblock('headJSfiles'); # custom js files only for this page?>
    {jQuery_highcharts: "<?php print base_url(); ?>resources/js/highcharts.js"},
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>


    // global chart variables
    var attendanceChart;
    var attendanceSeries_data = []; // empty array for the attendance chart data

    var skillChart;

    /*
     * Create the charts, after the document is loaded
     */
     $(document).ready(function(){
        // preformat the incoming data for the attendance chart
        format_attendance_series();

        // render out the attendance chart
        attendanceChart();

        // render out the skill chart
        skillChart();
    });

    /*
     * Function to define and render out the attendance chart
     */
     function attendanceChart() {
        attendanceChart = new Highcharts.Chart({
            chart: {
                renderTo: 'attendanceChart',
                type: 'line'
            },
                title: {
                    text: 'Anwesenheit'
            },

            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: {
                    day: '%e. %b',
                    month: '%b %y',
                    year: '%b'
                },
                labels: {
                    rotation: -45,
                    align: 'right',
                },
                min: <?php echo $att_chart_x_scaling['min_value']; ?>,
                max: <?php echo $att_chart_x_scaling['max_value']; ?>,
                tickInterval: 28 * 24 * 3600 * 1000, // 28 days ticks
                title: {
                    text: 'Zeitraum'
                },
            },

            yAxis: {
                min: 0,
                max: <?php echo $max_semester_weeks; ?>, // Abhaengig von der Zahl der SW
                tickInterval: 4,
                title: {
                    text: 'Anzahl '
                }
            },

            legend: {
                enabled: false,
            },

            credits: {
                enabled: false,
            },

            series: [{
                name: 'Bis jetzt insgesamt',
                data: attendanceSeries_data
            }]
        });
      }

        /*
        * Funciton to define and render out the skill chart
        */
        function skillChart() {
            skillChart = new Highcharts.Chart({
                chart: {
                  renderTo: 'skillsChart',
                  type: 'bar'
                },
                title: {
                    text: 'Kenntnisse'
                },
                xAxis: {
                    title: {
                    text: 'Themen',

                    },
                    categories: <?php echo json_encode($skill_data['topic']); ?>,
                    tickmarkPlacement: 'on',
                },
                yAxis: {
                    min: 0,
                    max: 100, // Abhaengig von der Zahl der SW
                    tickInterval: 25,
                    title: {
                        text: 'Kann ich'
                    }
                },

                legend: {
                    enabled: true,
                },

                credits: {
                    enabled: false,
                },

                series: [{
                    name: 'Kenntnisse pro Thema',
                    type: 'column',
                data: <?php echo json_encode($skill_data['rating']);?>,
                }, {
                    name: 'Durchschnittliche Kenntnisse',
                    type: 'line',
                    data: <?php echo json_encode($skill_data['avg_rating']); ?>,
                }]
            });
        }

      /*
       * Function to format the attendance series, extract the proper data from the json object
       */
       function format_attendance_series(){
            // get the attendance rough data
            var attendance_data_php = <?php echo $attendance_chart_series; ?>

            $.each(attendance_data_php, function(i, elem) {
                // remove the [] sign from the element (is not needed..)
                var single_element = elem.replace("[", '').replace("]",''); //remove the '[]'

                // extract the date from the elment
                var date = parseInt(single_element.split(",")[0]); // split the element, because after the ',' there is the needed count
                // extract the count from the element
                var count = parseInt(single_element.split(",")[1]); // split the element after the ',' and get the count
                // push data to the series array
                attendanceSeries_data.push([date, count]);
            });
       }
<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>