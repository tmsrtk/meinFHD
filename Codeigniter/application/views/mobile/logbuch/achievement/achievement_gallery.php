<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - meine Achievements<?php endblock(); ?>

<?php startblock('content'); # content ?>
<div class="well well-small">
    <div class="row-fluid">
        <div class="span12">
            <a href="<?php print base_url('logbuch/index'); ?>" class="btn btn-medium btn-danger" style="font-weight: bold;"><i class="icon-arrow-left icon-white"></i>&nbsp;Logbuchmen&uuml;</a>
        </div>
    </div>
    <hr/>
    <!-- display content if no achievements have been unlocked -->
    <?php if(!$achievement_data) :?>
        <div class="row-fluid">
            <h3>Sorry, du hast noch keine Achievments freigeschaltet</h3>
            <p>
                Nutze doch das Logbuch und dessen Funktionen intensiver, um viele nette Achievements zu sammeln. Diese kannst du dir dann hier anschauen!
            </p>
        </div>
    <?php endif ?>
    <!-- END achievement gallery Header -->
    <!-- dynamic collapsables for the achievement levels -->
    <div class="container-fluid">
        <div class="accordion" id="achievementGalleryAccordion">
            <?php foreach($achievement_data as $level): ?>
                <!-- single collapsable -->
                <div class="accordion-group">
                    <div class="accordion-heading">
                        <h3 class="accordion-toggle" data-toggle="collapse" data-parent="#achievementGalleryAccordion" data-target="#level<?php echo $level['LevelNr']; ?>">Achievements Level <?php echo $level['LevelNr']; ?><i class="icon-plus pull-right"></i></h3>
                    </div>
                    <div id="level<?php echo $level['LevelNr']; ?>" class="accordion-body collapse">
                        <div class="accordion-inner">
                            <!-- single achievement level object // for each achievementtype-->
                            <?php foreach($level[0] as $single_achievementtype) :?>
                                    <div class="row-fluid">
                                        <h4><?php echo $single_achievementtype['Achievementname']; ?></h4>
                                    </div>
                                    <br/>
                                    <div class="row-fluid">
                                        <div class="span1"></div>
                                        <div class="span2">
                                            <img src="<?php print base_url(); ?>resources/img/achievement_badges/<?php echo $single_achievementtype['Aktivitaetenname'];?>/level_<?php echo $single_achievementtype['LevelNr']; ?>.png" alt="Achievement Badge" />
                                        </div>
                                        <div class="span1"></div>
                                        <div class="span8"><?php echo $single_achievementtype['Motivationstext']; ?></div>
                                    </div>
                                    <br/>
                                    <div class="row-fluid">
                                        <p>
                                            <strong>Das Achievement hast du f&uuml;r folgende Kurse freigeschaltet: </strong>
                                            <?php
                                                // variable to hold the string, that displays all courses
                                                $courses_to_display = '';
                                                foreach($single_achievementtype['courses'] as $unlocked_course){
                                                    // foreach single course construct the courses string
                                                    $courses_to_display = $courses_to_display . $unlocked_course['Kursname'] . ', ';
                                                }

                                                // echo out all courses, where the achievement have been owned for
                                                echo $courses_to_display;
                                            ?>
                                        </p>
                                    </div>
                                    <hr/> <!-- end achievementtype 1 -->
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div> <!-- /well well-small-->

<?php endblock(); #end content ?>

<?php startblock('customFooterJQueryCode');?>

    /*
     * When the document finished loading collapse the last collapsable element (highest achievement level)
     */
     $(document).ready(function(){
        var highest_level = $('div.accordion-body:last').attr('id'); // get the highes achievement level accordion
        $('#'+highest_level+'').collapse('show'); // colapse the element
     });
<?php endblock(); ?>

<?php end_extend(); ?>