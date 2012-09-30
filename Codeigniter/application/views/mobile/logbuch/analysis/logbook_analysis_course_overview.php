<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - meine Auswertungen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>

<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>

<div class="well well-small">
    <div class="row-fluid">
        <div class="span12">
            <a href="<?php print base_url('logbuch/index'); ?>" class="btn btn-medium btn-danger" style="font-weight: bold;"><i class="icon-arrow-left icon-white"></i>&nbsp;Logbuchmen&uuml;</a>
        </div>
    </div><!-- END header with backlink -->
    <hr/>

    <!-- Collapsable Menue Content -->
    <div class="container-fluid">
        <div class="accordion" id="analysisAccordion">
            <!-- Collapsable 1 overall analysis -->
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h3 class="accordion-toggle" data-toggle="collapse" data-parent="#analysisAccordion" data-target="#overallAnalysis">Gesamtauswertung &uuml;ber alle meine Logbuchkurse<i class="icon-plus pull-right"></i></h3>
                </div>
                <div id="overallAnalysis" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="row-fluid">
                            <p>Hier erh&auml;lst du einen kurzen zusammengefassten &Uuml;berblick &uuml;ber deine F&auml;higkeiten und Anwesenheiten zu
                                all deinen Logbuchkursen.</p>
                            <hr/>
                        </div>
                        <div class="row-fluid">
                            <h4>Kenntnisse</h4>
                            <hr/>
                            <div class="span6" id="overallSkillChart" style="max-width:500px; height: 300px; margin-left: 3%;"></div>
                            <div class="span4"></div>
                        </div>
                        <div class="row-fluid">
                            <hr/>
                            <h4>Anwesenheiten</h4>
                            <hr/>
                            <div class="span6" id="overallAttendanceChart" style="max-width:500px; height: 300px; margin-left: 3%;"></div>
                            <div class="span4"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Collapsable course analysis -->
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h3 class="accordion-toggle" data-toggle="collapse" data-parent="#analysisAccordion" data-target="#courseAnalysis">Einzelauswertungen f&uuml;r meine Logbuchkurse<i class="icon-plus pull-right"></i></h3>
                </div>
                <div id="courseAnalysis" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="row-fluid">
                            <p>Im folgenden kannst du dir einen deiner Logbuch-Kurse ausw&auml;hlen, f&uuml;r den du dir detaillierte Auswertungen anschauen m&ouml;chtest.</p>
                            <!-- dynamic content foreach logbook course -->
                            <table class="table">
                                <tbody>
                                <?php foreach($logbook_courses as $course => $course_attr) :?>
                                <tr data-id="<?php echo $course_attr['LogbuchID']; ?>">
                                    <td>
                                        <span><img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_stats.png" alt="Auswertungen" style="max-width: 20px; height: auto;"/></span>
                                        &nbsp;&nbsp;&nbsp;<a href="<?php print base_url('logbuch_analysis/show_analysis_for_course'); ?>/<?php echo $course_attr['KursID']; ?>"><strong><?php echo $course_attr['kurs_kurz']; ?></strong></a>
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <a href="<?php print base_url('logbuch_analysis/show_analysis_for_course'); ?>/<?php echo $course_attr['KursID']; ?>" class="btn pull-right"><img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_thin_right_arrow.png" alt="delete" style="max-width: 18px; height: auto;"/></a>
                                    </td>
                                </tr>
                                    <?php endforeach ?> <!-- end foreach content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div><!-- /div well well -->

<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup after content ?>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>

    // global chart variables
    var coursesSkillChart;
    var overallAttendanceChart;
    var attendanceSeries_data = []; // array to hold the reformatted data for the overall attenance chart


    /*
    * Create the charts, after the document is loaded
    */
    $(document).ready(function(){

        // render out the overall courses skill chart
        overallSkillChart();

        // format the overall attendance data
        format_attendance_series();
        // render out the overall attendance chart
        overallAttendanceChart();

        // collapse the overall analysis element after loading
        $('#overallAnalysis').collapse('show');

    });

    /*
    * Funciton to define and render out the skill chart
    */
    function overallSkillChart() {
        coursesSkillChart = new Highcharts.Chart({
            chart: {
                renderTo: 'overallSkillChart',
                type: 'bar',
            },
            title: {
                text: 'Kenntnisse nach Kursen'
            },
            xAxis: {
                title: {
                    text: 'Kurse',
                },
                categories: <?php echo $all_logbook_courses; ?>,
                    tickmarkPlacement: 'on'
            },
            yAxis: {
                min: 0,
                max: 100,
                tickInterval: 50,
                title: {
                    text: 'Gesamtkenntnisse in %'
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
                data: <?php echo $all_logbook_courses_rating;?>,
            }]
        });
    }

    /*
    * Funciton to define and render out the overall attendance chart
    */
    function overallAttendanceChart() {
        overallAttendanceChart = new Highcharts.Chart({
            chart: {
            renderTo: 'overallAttendanceChart',
            type: 'line'
            },
            title: {
            text: 'Anwesenheiten'
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
            tickInterval: 28 * 24 * 3600 * 1000, // 14 days ticks
            title: {
            text: 'Zeitraum'
            },
        },

        yAxis: {
            min: 0,
            max: <?php echo $att_chart_y_scaling; ?>,
            tickInterval: 10,
            title: {
            text: 'Anzahl'
            }
        },

        legend: {
            enabled: false,
        },

        credits: {
            enabled: false,
        },

        series: [{
            name: 'Anwesenheit insgesamt',
            data: attendanceSeries_data
            }]

        });
    }

    /*
    * Function to format the attendance series, extract the proper data from the json object
    */
    function format_attendance_series(){
        // get the attendance rough data
        var attendance_data_php = <?php echo $overall_attendance;?>;

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

<?php startblock('headJSfiles'); # custom js files only for this page?>
{jQuery_highcharts: "<?php print base_url(); ?>resources/js/highcharts.js"},
<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>