<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Logbuch anlegen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
<?php
# general button / form setup for courses dropdown
$data_formopen = array('class' => 'form-horizontal', 'id' => 'add_logbook');

$courseDropdownParams = 'class="input-small" id="kurs"';

$submit_data = array(
    'name'			=> 'Logbbuch erstellen',
    'class'			=> 'btn btn-danger'
);
$data_labelattrs = array(
    'class' => 'control-label',
    'style' => 'font-weight: bold;'
);

?>
<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>
<div class ="well well-small">
    <div class="row-fluid">
        <div class="span12">
            <a href="javascript:history.back()" class="btn btn-medium btn-danger" style="font-weight: bold;"><i class="icon-arrow-left icon-white"></i>&nbsp;zur&uuml;ck</a>
        </div>
    </div><!-- end add logbook header-->
    <hr>
    <div class="row-fluid">
        <h4>Logbuch f&uuml;r weiteren Kurs anlegen:</h4>
        <br/>
        <div class="alert alert-info">Bitte w&auml;hle einen Kurs aus, zudem Du dir ein Logbuch erstellen m&ouml;chtest.</div>
    <?php echo validation_errors(); ?>
    <?php echo form_open('logbuch/validate_add_logbook_form', $data_formopen); ?>

        <div class="control-group">
            <?php echo form_label('Kurs','kurs', $data_labelattrs); ?>
            <div class="controls docs-input-sizes">
                <?php echo form_dropdown('kurs', $possible_courses, '', $courseDropdownParams); ?>
            </div>
        </div>

        <div class="form-actions">
            <?php echo form_submit($submit_data, 'Logbuch erstellen') ?>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup after content ?>

<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>