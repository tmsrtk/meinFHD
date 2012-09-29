<div class="modal hide" id="achievementModal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">x</button>
        <h3>Achievement unlocked</h3>
    </div>
    <div class="modal-body">
        <div class="row-fluid">
            <div class="span1"></div>
            <div class="span2">
                <img src="<?php echo $badge_url; ?>"/>
            </div>
            <div class="span1">
            </div>
            <div class="span8">
                <p>Herzlichen Gl&uuml;ckwunsch, du hast gerade das Achievement <strong><?php echo $achievement_information['Achievementname']; ?></strong>
                    f&uuml;r <strong><?php echo $course_information['Kursname']; ?></strong> freigeschaltet.</p>
                <p><?php echo $achievement_information['Motivationstext']; ?></p>
                <p>Weitere Informationen findest du in deiner pers&ouml;nlichen <a href="<?php print base_url('achievement/show_achievement_gallery'); ?>">Achievement&uuml;bersicht</a>.</p>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-primary" data-dismiss="modal">Schlie&szlig;en</a>
    </div>
</div>