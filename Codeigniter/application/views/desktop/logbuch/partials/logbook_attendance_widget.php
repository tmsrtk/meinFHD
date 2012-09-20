<div class="well widget default">
    <i class="icon icon-question-sign pull-right"></i>
    <h5><i class="icon icon-tasks"></i>&nbsp;Anwesenheit</h5>
    <div class="widget-content" data-id="<?php if(count($running_course)!=0){echo $running_course['KursID'];} ?>">
        <?php if(count($running_course)!=0): # only display the following if any course is running ?>
        <div>
            <strong>Gerade l&auml;uft: </strong><span style="color: red;"><?php echo $running_course['Kursname']; ?></span> in <strong><?php echo $running_course['Raum']; ?></strong><br/>
            Du warst bisher insgesamt <strong><?php echo($running_course['attended_events']); ?>/<?php echo $max_events; ?></strong> mal da. Du hast <strong><?php echo round($running_course['attended_events_percent']); ?>%</strong> in diesem Semester bereits erfolgreich hinter dich gebracht.
        </div>
        <div>
            <br>
            <div class="progress progress-success progress-striped active" style="width: 90%; margin-left: 5%;">
                <div class="bar" style="width: <?php echo $running_course['attended_events_percent']; ?>%;"></div>
            </div>
        </div>
        <div class="pagination-centered" style="margin-top: 2%;">
            <span><a href="#" class="btn btn-small btn-danger <?php echo $running_course['btn_attend_state'];?>" id="attendButton"><strong>Ich bin hier!</strong></a></span>
            <span><a href="#" class="btn btn-small" id="switchToLogbookButton"><strong>zum Logbuch</strong></a></span>
        </div>
        <?php else: # display the following if no course is running?>
        <div>
            <p>
                Gerade l&auml;uft kein Kurs. Du kannst dich mit deinen Logb&uuml;chern auf deine n&auml;chste Veranstaltung vorbereiten.
            </p>
        </div>
        <div class="pagination-centered">
            <a href="<?php print base_url('logbuch/show_logbooks'); ?>" class="btn btn-medium">zu meinen Logb&uuml;chern</a>
        </div>
        <?php endif; # end else?>
    </div>
</div><!-- end attendance widget -->

<div id="modalcontent"></div><!-- content space for displaying modals -->