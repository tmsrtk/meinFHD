<?php
    # author: Christian Kundruss
    # view for 'Logbuchverwaltung'
    # partial view for adding / saving base topics to the actual viewed course

    $course_topics_textarea_data = array(
        'name' => $course_id.'_topics',
        'id' => 'input-course-topics-' . $course_id,
        'class' => 'input-xlarge span',
        'value' => $course_topics,
        'rows' => 7,
        'cols' => 40
    );

    $course_topics_textarea_readonly = array(
        'name' => $course_id.'_topics',
        'id' => 'input-course-topics-' . $course_id,
        'class' => 'input-xlarge span',
        'value' => $course_topics,
        'rows' => 7,
        'cols' => 40,
        'readonly' => 'readonly'
    );
?>

<h3>Themen</h3>
<p>Hier k&ouml;nnen Sie die Themen der Veranstaltung in kurzen Stichworten auff&uuml;hren. Diese k&ouml;nnen die Studenten zur eigenen Lernstandskontrolle
   verwenden.<br/>Jede Zeile wird dabei als ein Thema aufgefasst. Bitte geben Sie keine HTML-Tags oder anderweitig formatierten Texte ein.
</p>
<div>
    <?php
        if(!$is_tutor){ // user is not an tutor -> can work with topics
            echo form_textarea($course_topics_textarea_data);
        }
        else { // user is an tutor
            echo form_textarea($course_topics_textarea_readonly);
        }
    ?>
</div>
