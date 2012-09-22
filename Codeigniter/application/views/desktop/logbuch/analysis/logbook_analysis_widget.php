<div class="well widget default">
    <i class="icon icon-question-sign pull-right"></i>
    <h5><i class="icon icon-tasks"></i>&nbsp;Auswertungen</h5>
    <div class="widget-content">
    <?php if(count($running_course)!=0): # only display the following if any course is running ?>
        <div>
            <strong>Deine Kenntnisse f&uuml;r: </strong><span style="color: red;">[Kursname]</span><br/>
        </div>
        <div class="pagination-centered" style="margin-top: 2%;">
            <span><a href="#" class="btn btn-small"><strong>zu meinen Auswertungen</strong></a></span>
        </div>
        <!-- zweispaltigkeit durch tabelle und grafik darstellen -->
        <?php else: # display the following if no course is running?>
        <div><!-- ELSE part -->
            <p>
                Gerade l&auml;uft kein Kurs. Du kannst dir aber Auswertungen zu all deinen Logb&uuml;chern anschauen!
            </p>
        </div>
        <div class="pagination-centered">
            <a href="#" class="btn btn-medium">zu meinen Auswertungen</a>
        </div><!-- end ELSE part -->
        <?php endif; ?>
    </div>
</div><!-- end analysis widget -->
