<div class="well widget default">
    <i class="icon icon-question-sign pull-right"></i>
    <h5><i class="icon icon-tasks"></i>&nbsp;Auswertungen</h5>
    <div class="widget-content">
        <?php if(count($running_course)!=0): # only display the following if any course is running ?>
        <div>
            <strong>Deine Kenntnisse f&uuml;r: </strong><span style="color: red;"><strong><?php echo $running_course['Kursname']; ?></strong></span><br/>
        </div>
        <div id="chartContainer" style="width:280px; height: 80px;  margin-top: -16px; margin-bottom: -6px;"></div>
        <div class="row-fluid">
            <div class="span12">Die Themen des Kurses kannst du bisher zu 100%. Schaue dir doch
                detailliertere Auswertungen an.</div>
        </div>
        <div class="pagination-centered" style="margin-top: 1px;">
            <span><a href="<?php print base_url('logbuch_analysis/show_analysis_for_course');?>/<?php echo $running_course['KursID']; ?>" class="btn btn-small"><strong>mehr Auswertungen</strong></a></span>
        </div>
        <?php else: # display the following if no course is running?>
        <div><!-- ELSE part -->
            <p>
                Gerade l&auml;uft kein Kurs. Du kannst dir aber Auswertungen zu all deinen Logb&uuml;chern anschauen!
            </p>
        </div>
        <div class="pagination-centered">
            <a href="<?php print base_url('logbuch_analysis/show_possible_courses'); ?>" class="btn btn-medium">zu meinen Auswertungen</a>
        </div><!-- end ELSE part -->
        <?php endif; ?>
    </div>
</div><!-- end analysis widget -->
