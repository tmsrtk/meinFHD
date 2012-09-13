<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Logbucheintrag bearbeiten<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>

<?php # general form setup

$data_formopen = array('class' => 'form-horizontal', 'id' => 'edit_logbook_entry');

$data_labelattrs = array(
    'class' => 'control-label',
    'style' => 'font-weight: bold;'
);

$data_topic = array(
    'class' => 'span6',
    'name' => 'topic',
    'id' => 'topic',
    'placeholder' => 'Themenname',
    'value' => $logbook_entry['Thema']
);

$data_rating = array(
    'class' => 'span2',
    'name' => 'topic_rating',
    'id' => 'topic_rating',
    'placeholder' => '0%',
    'value' => $logbook_entry['Bewertung'].'%',
    'readonly' => 'readonly'

);

$data_annotation_textarea = array(
    'name' => 'input-topic-annotation',
    'id' => 'input-topic-annotation',
    'value' => $logbook_entry['Erlaeuterung'],
    'class' => 'input-medium',
    'placeholder' => 'Platz für Erläuterungen',
    'rows' => 4,
    'cols' => 40
);

$submit_data = array(
    'name'			=> 'Speichern',
    'class'			=> 'btn btn-danger'
);

?>

<?php endblock(); ?>

<?php startblock('content'); # content ?>
<div class="well well-small">
    <div class="row-fluid">
        <div class="span12">
            <a href="<?php print base_url('logbuch/show_logbook_content/' . $logbook_entry['LogbuchID']); ?>" class="btn btn-medium btn-danger" style="font-weight: bold;"><i class="icon-arrow-left icon-white"></i>&nbsp;Themen&uuml;bersicht</a>
        </div>
    </div>
    <hr><!-- END add logbuch entry header -->
    <div class="row-fluid">
        <div class="span12">
            <h4>Eintrag bearbeiten</h4>
            <p>
                In den folgenden Feldern kannst du den ausgew&auml;hlten Logbucheintrag f&uuml;r <strong><?php echo $course_name; ?></strong> bearbeiten.
            </p>
        </div>
    </div>
    <hr>
    <?php echo validation_errors(); # space for displaying validation errors ?>
    <div class="row-fluid">
        <!-- form to add an logbook entry -->
        <?php echo form_open('logbuch/validate_edit_entry_form', $data_formopen); ?>
        <?php echo form_hidden('LogbuchID', $logbook_entry['LogbuchID']); ?>
        <?php echo form_hidden('LogbucheintragID', $logbook_entry['LogbucheintragID']); ?>
        <div class="control-group">
            <?php echo form_label('Thema', 'topic', $data_labelattrs); ?>
            <div class="controls docs-input-sizes">
                <?php echo form_input($data_topic); ?>
            </div>
        </div>
        <div class="control-group">
            <?php echo form_label('Erläuterung', 'input-topic-annotation', $data_labelattrs); ?>
            <div class="controls docs-input-sizes">
                <?php echo form_textarea($data_annotation_textarea); ?>
            </div>
        </div>
        <div class="control-group">
            <?php echo form_label('Kann ich', 'topic_rating', $data_labelattrs); ?>
            <div class="controls docs-input-sizes">
                <?php echo form_input($data_rating); ?>
            </div>
        </div>
        <div id="topicRatingSlider" style="width: 50%; margin-left: 2%;"></div>
        <hr/>
        <?php echo form_submit($submit_data, 'Speichern'); ?>
        <?php echo form_close(); ?>
        <!-- end add logbook entry form -->
    </div>
</div><!-- /div well well-small -->

<div id="modalcontent"></div>

<?php endblock(); # end content?>


<?php startblock('customFooterJQueryCode');?>

    var rating_to_set = $('[name=topic_rating]').val();
    rating_to_set = rating_to_set.replace('%',''); // remove the % sign

    // set up and init the rating slider -> for new entries: 0, max: 100
    $("#topicRatingSlider").slider({
        range: "min",
        value: rating_to_set,
        min: 0,
        max: 100,
        step: 5,
        stop: function(event, ui){ // display slider value in textbox
                var slider_value = $("#topicRatingSlider").slider("option", "value"); // get the actual slider value
                document.getElementById('topic_rating').value =  slider_value + '%';
        },
        slide: function(event, ui){ // display text during sliding
            var slider_value = $("#topicRatingSlider").slider("option", "value"); // get the actual slider value
            document.getElementById('topic_rating').value =  slider_value + '%';

                // change the background color of the slider depending on the act. value
            if (slider_value < 50) {
                $("#topicRatingSlider").children("div").css("background","#3dc1dc");
            }
            else if(slider_value >= 50 && slider_value < 70) {
                $("#topicRatingSlider").children("div").css("background","orange");
            }
            else {
                $("#topicRatingSlider").children("div").css("background","green");
            }
        }
    });

    // init the slider with the correct colors when the view is loaded
    $(document).ready(function(){

        // change the background color of the slider depending on the act. value
        if (rating_to_set < 50) {
            $("#topicRatingSlider").children("div").css("background","#3dc1dc");
        }
        else if(rating_to_set >= 50 && rating_to_set< 70) {
            $("#topicRatingSlider").children("div").css("background","orange");
        }
        else {
            $("#topicRatingSlider").children("div").css("background","green");
        }
});
<?php endblock(); # end custom jQueryCode ?>

<?php startblock('headJSfiles'); # add additional js files?>
    {jQuery_ui_touch_punch: "<?php print base_url(); ?>resources/jquery/jquery.ui.touch-punch.min.js"},

<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>