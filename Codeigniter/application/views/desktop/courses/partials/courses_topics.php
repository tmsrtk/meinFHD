<?php
    # author: Christian Kundruss
    # view for 'Logbuchverwaltung'

    $course_topics_textarea_data = array(
        'name' => $course_id.'_topics',
        'id' => 'input-course-topics',
        'class' => 'input-xlarge span',
        'value' => $course_topics,
        'rows' => 7,
        'cols' => 40
    );
?>

<h3>Themen</h3>
<p>Hier k&ouml;nnen Sie die Themen der Veranstaltung in kurzen Stichworten auff&uuml;hren. Diese k&ouml;nnen die Studenten zur eigenen Lernstandskontrolle
   verwenden.<br/>Jede Zeile wird dabei als ein Thema aufgefasst.
</p>
<div>
    <?php
        if(!$is_tutor){ // user is not tutor -> can work with topics
            echo    form_textarea($course_topics_textarea_data);
        }
        else { // user is tutor
            if ($course_topics) {
                echo $course_topics;
            }
            else {
                echo 'Keine Themen vorhanden.';
            }
        }
    ?>
</div>
