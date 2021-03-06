<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title'); # extend the site's title ?><?php get_extended_block(); ?> - Dashboard<?php endblock();?>

<?php startblock('headerJS'); # additional js files ?>
<!-- Edit by CK 2012, JS-Code to save the base url in an global variable. Makes the base url visible in external JS files -->
<script type="text/javascript">
    var CI = {'base_url': "<?php print base_url();?>"};

    // -- EDIT BY CK: Save data for skill_widget to make it available in the widget` js file --
    var act_skills = <?php echo $act_skills; ?>;
    var missing_skills = <?php echo $missing_skills; ?>;
    // -- EDIT END --
</script>
<!-- end Edit -->
<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
<div class="container container-fluid" id="sortable">
    <div class="row">
        <?php endblock(); ?>

        <?php startblock('postCodeContent'); # additional markup after content ?>
    </div><!--third row ends here-->
</div><!-- /.fluid container-->
<?php endblock(); ?>

<?php startblock('content'); # content for this view ?>
<?php if( $this->authentication->has_permissions('hat_logbuch') ) :?>
<div class="span4" id="attendanceWidget"><!-- attendance / logbuch widget -->
    <?php echo $attendance_widget; ?>
</div><!-- /.span4-->
<?php endif; ?>
<?php if ( $this->authentication->has_permissions('hat_logbuch') ) :?>
<div class="span4">
    <?php echo $analysis_widget; ?>
</div><!-- /.span4-->
<?php endif; ?>
<div class="span4">
    <div class="well widget  default">
        <i class="icon icon-question-sign pull-right"></i>
        <h5><i class="icon icon-tasks"></i>N&auml;chste Veranstaltung</h5>
        <div class="widget-content">
            <div class="row-fluid">
                <div class="span3">
                    <span class="bold">18.05.2012</span>
                    <span class="bold date">8:00</span>
                </div>
                <div class="span8">
                    <div class="bold">Mathematik 2</div>
                    <span class="grey">Bei: </span>Prof. Dr. rer. nat D&ouml;rries
                    <div class="grey">Raum: H 1.11 </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- /. span4-->
</div><!--first row ends here-->
<div class="row"><!--second row starts here-->
    <div class="span4 flipBox" style="position: relative;">
        <div class="well widget frontSide">
            <div class="widgetHeader">
                <i class="icon icon-question-sign pull-right" title="Ein Blick in deine Credits" rel="tooltip"></i>
                <h5><i class="icon icon-tasks"></i>Deine Credits</h5>
            </div>
            <div class="widgetContent">
                <div id="leistungsContainer" style="width: 278px; height: 70px;"></div>
            </div>
            <div class="widgetFooter">
                <a href="#" class="flipLink"><i class="icon icon-info-sign pull-right"></i></a>
            </div>
        </div> <!-- ende frontSide -->
        <div class="well widget backSide">
            <i class="icon icon-question-sign pull-right" title="Willst Du es wissen wile lange noch in diesen Semester hast?" rel="tooltip"></i>
            <h5><i class="icon icon-tasks"></i>CREDIT EDIT</h5>
            <div class="widget-edit">
                Edit Credit Anzeige
                <form >
                    <label>Edit Credits Box </label>
                    <input type="checkbox" id="semesterende" onclick="toggle_credits($(this))" checked="true"> Anzeigen

                </form>
                <a href="#" class="flipLink btn btn-success pull-right">
                    <i class="icon-white icon-ok pull-left"></i>
                </a>
            </div>
        </div>
    </div><!-- /.span4-->
    <div  class="span4">
        <div  class="well widget default">
            <i class="icon icon-question-sign pull-right"></i>
            <h5><i class="icon icon-tasks"></i>Ziel</h5>
            <div class="widget-content">
                <div id="fehlendeKurse">
                    Dir fehlen noch <span class="badge badge-important">6</span> Kurse. Darunter befinden sich <span class="badge badge-important">3</span> Praktika.
                </div>
            </div>
        </div>
    </div><!-- /. span4-->
    <div class="span4 flipBox" style="position: relative;">
        <div class="well widget frontSide">
            <div class="widgetHeader">
                <i class="icon icon-question-sign pull-right" title="Willst Du es wissen wile lange noch in diesen Semester hast?" rel="tooltip"></i>
                <h5><i class="icon icon-tasks"></i>Semestercountdown</h5>
            </div>
            <div class="widgetContent">
                <p id="note"></p>
                <div id="counter1">
                    <div class="bold counterLabel" >Semesterende</div>
                    <div id="countdown" class="countdownHolder"> </div>
                </div>
                <div id="counter2">
                    <div class="bold counterLabel" >Klausuren</div>
                    <div id="countdown2" class="countdownHolder"> </div>
                </div>
                <div id="counter3">
                    <div class="bold counterLabel" >Klausuren2</div>
                    <div id="countdown3" class="countdownHolder"> </div>
                </div>
                <div id="counter4">
                    <div class="bold counterLabel">Klausuren3</div>
                    <div id="countdown4" class="countdownHolder"> </div>
                </div>
            </div>
            <div class="widgetFooter">
                <a href="#" class="flipLink"><i class="icon icon-info-sign pull-right"></i></a>
            </div>
        </div> <!-- ende frontSide -->
        <div class="well widget backSide">
            <i class="icon icon-question-sign pull-right" title="Willst Du es wissen wile lange noch in diesen Semester hast?" rel="tooltip"></i>
            <h5><i class="icon icon-tasks"></i>Semestercountdown EDIT</h5>
            <div class="widget-edit">
                <form >
                    <fieldset>
                        <legend>Wähle deine Counter </legend>
                        <input type="checkbox" id="semesterende" onclick="toggle_counter($(this))" checked="checked"><label for="semesterende">Semester Ende</label>
                        <input type="checkbox" id="klausurstart1" onclick="toggle_counter($(this))" checked="checked"><label for="semesterende">Klausur1</label>
                        <input type="checkbox" id="klausurstart2" onclick="toggle_counter($(this))" checked="checked"><label for="semesterende">Klausur2</label>
                        <input type="checkbox" id="klausurstart3" onclick="toggle_counter($(this))" checked="checked"><label for="semesterende">Klausur3</label>
                    </fieldset>
                </form>
                <a href="#" class="flipLink btn btn-success pull-right">
                    <i class="icon-white icon-ok pull-left"></i>
                </a>
            </div>
        </div>
    </div><!-- /.span4-->
</div><!--second row ends here-->
			<div class="row"><!--third row starts here-->
                <div class="span4">
                    <div class="well widget default">
                        <i class="icon icon-question-sign pull-right"></i>
                        <h5><i class="icon icon-tasks"></i>Noten</h5>
                        <div class="widget-content">Hier kommt das Noten-Widget hin.</div>
                    </div>
                </div><!-- /.span4-->
                <div class="span4">
                    <div class="well widget default">
                        <i class="icon icon-question-sign pull-right"></i>
                        <h5><i class="icon icon-tasks"></i>Dein Studienverlauf</h5>
                        <div class="widget-content">
                            <div id="studienverlaufContainer" style="height: 200px;"></div>
                        </div>
                    </div>
                </div><!-- /. span4-->
                <div class="span4">
                    <div class="well widget default">
                        <i class="icon icon-question-sign pull-right"></i>
                        <h5><i class="icon icon-tasks"></i>FAQ</h5>
                        <div class="widget-content">
                            <div id="test1">Hilfe - MeinFHD</div>
                            <div id="test2">Faq</div>
                        </div>
                    </div>
                </div><!-- /.span4-->
                <!-- CONTENT ENDE-->
<?php endblock(); ?>

<?php startblock('headJSfiles'); ?>
                {jQuery_highcharts: "<?php print base_url(); ?>resources/js/highcharts.js"},
                {jQuery_countdown: "<?php print base_url(); ?>resources/js/jquery.countdown.js"},
                {widget_studienverlauf: "<?php print base_url(); ?>resources/js/widget.studienverlauf.js"},
                {widget_semestercountdown: "<?php print base_url(); ?>resources/js/meinfhd.semestercountdown.js"},
                {jquery_tooltip: "<?php print base_url(); ?>resources/js/meinfhd.tooltip.js"},
                {jQuery_flip: "<?php print base_url(); ?>resources/js/jquery.flip.js"},
                {widget_attendance: "<?php print base_url(); ?>resources/js/attendance_widget.js"},
                {widget_skills: "<?php print base_url(); ?>resources/js/skills_widget.js"},
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>

<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>