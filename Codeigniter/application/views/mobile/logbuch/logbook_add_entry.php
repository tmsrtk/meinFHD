<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - neuen Logbucheintrag anlegen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>

<?php # general form setup

    $data_formopen = array('class' => 'form-horizontal', 'id' => 'add_logbook_entry');

    $data_labelattrs = array(
        'class' => 'control-label',
        'style' => 'font-weight: bold;'
    );

    $data_topic = array(
        'class' => 'span6',
        'name' => 'topic',
        'id' => 'topic',
        'placeholder' => 'Themenname',
        'value' => set_value('topic')
    );

    $data_rating = array(
        'class' => 'span2',
        'name' => 'topic_rating',
        'id' => 'topic_rating',
        'placeholder' => '0%',
        'value' => set_value('topic_rating'),
        'readonly' => 'readonly'

    );

    $data_annotation_textarea = array(
        'name' => 'input-topic-annotation',
        'id' => 'input-topic-annotation',
        'value' => set_value('input-topic-annotation'),
        'placeholder' => 'Platz für Erläuterungen',
        'class' => 'input-medium',
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
            <a href="<?php print base_url('logbuch/show_logbook_content/' . $LogbuchID); ?>" class="btn btn-medium btn-danger" style="font-weight: bold;"><i class="icon-arrow-left icon-white"></i>&nbsp;Themen&uuml;bersicht</a>
        </div>
    </div>
    <hr><!-- END add logbuch entry header -->
    <div class="row-fluid">
        <div class="span12">
            <h4>Eintrag zum Logbuch hinzuf&uuml;gen</h4>
            <p>
                Bitte f&uuml;lle die folgenden Felder aus und klicke auf Speichern, um einen neuen Eintrag deinem Logbuch für <strong><?php echo $kursname; ?></strong> hinzuzuf&uuml;gen.
            </p>
        </div>
    </div>
    <hr>
    <?php echo validation_errors(); # space for displaying validation errors ?>
    <div class="row-fluid">
    <!-- form to add an logbook entry -->
        <?php echo form_open('logbuch/validate_create_entry_form', $data_formopen); ?>
        <?php echo form_hidden('LogbuchID', $LogbuchID); ?>
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

<?php endblock(); # end content?>


<?php startblock('customFooterJQueryCode');?>

    // set up and init the rating slider -> for new entries: 0, max: 100
    $("#topicRatingSlider").slider({
        range: "min",
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
                        $("#topicRatingSlider").children("div").css("background","red");
                    }
                    else if(slider_value >= 50 && slider_value< 70) {
                        $("#topicRatingSlider").children("div").css("background","orange");
                    }
                    else {
                        $("#topicRatingSlider").children("div").css("background","green");
                    }
                }
    });
<?php endblock(); # end custom jQueryCode ?>

<?php startblock('headJSfiles'); # add additional js files?>
       {jQuery_ui_touch_punch: "<?php print base_url(); ?>resources/jquery/jquery.ui.touch-punch.min.js"},
<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>