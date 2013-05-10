<?php
    // general form setup before content
    $data_formopen = array(
        'class' => 'form-horizontal',
        'id' => 'create_degree_program'
    );


    $data_labelattrs = array(
        'class' => 'control-label'
    );

    $data_pruefungsordnung = array(
        'class' => 'input-medium',
        'name' => 'pruefungsordnung',
        'id' => 'pruefungsordnung',
        'placeholder' => 'z.B. 2010',
        'value' => $dp_details->Pruefungsordnung
    );


    $data_studiengangname = array(
        'class' => 'input-medium',
        'name' => 'studiengangname',
        'id' => 'studiengangname',
        'placeholder' => 'z.B. B.Sc. Medieninformatik',
        'value' => $dp_details->StudiengangName
    );

    $data_abkuerzung = array(
        'class' => 'input-medium',
        'name' => 'studiengangsabkuerzung',
        'id' => 'studiengangsabkuerzung',
        'placeholder' => 'z.B. BMI',
        'value' => $dp_details->StudiengangAbkuerzung
    );

    $data_regelsemester = array(
        'class' => 'input-medium',
        'name' => 'regelsemester',
        'id' => 'regelsemester',
        'placeholder' => 'z.B. 7',
        'value' => $dp_details->Regelsemester
    );

    $data_creditpoints = array(
        'class' => 'input-medium',
        'name' => 'creditpoints',
        'id' => 'creditpoints',
        'placeholder' => 'z.B. 210',
        'value' => $dp_details->Creditpoints
    );

    #textarea
    $data_beschreibung = array(
        'name' => 'beschreibung',
        'id' => 'beschreibung',
        'class' => 'input-xxlarge',
        'placeholder' => 'Beschreibung des Studiengangs in textueller Form. Bitte keine HTML-Tags oder HTML-Elemente eintragen.',
        'rows' => 13,
        'cols' => 40,
        'value' => $dp_details->Beschreibung
    );

    $submit_data = array(
        'name'			=> 'submit',
        'class'			=> 'btn btn-warning input-medium'
    );

?>

<div id="degree-program-details" class="well well-small clearfix">
    <?php echo form_open('admin/validate_edit_degree_program_details', $data_formopen); ?>
    <?php echo form_hidden('degree_program_id', $dp_id);?>
    <div id="degree-program-main-details" class="span6">
        <div class="control-group">
            <?php echo form_label('Pr&uuml;fungsordnung','pruefungsordnung', $data_labelattrs); ?>
            <div class="controls docs-input-sizes">
                <?php echo form_input($data_pruefungsordnung); ?>
            </div>
        </div>
        <div class="control-group">
            <?php echo form_label('Name des Studiengangs', 'studiengangname', $data_labelattrs); ?>
            <div class="controls docs-input-sizes">
                <?php echo form_input($data_studiengangname); ?>
            </div>
        </div>
        <div class="control-group">
            <?php echo form_label('Studiengangsabk&uuml;rzung', 'studiengangsabkuerzung', $data_labelattrs); ?>
            <div class="controls docs-input-sizes">
                <?php echo form_input($data_abkuerzung); ?>
            </div>
        </div>
        <div class="control-group">
            <?php echo form_label('Anzahl der Regelsemester', 'regelsemester', $data_labelattrs); ?>
            <div class="controls docs-input-sizes">
                <?php echo form_input($data_regelsemester); ?>
            </div>
        </div>
        <div class="control-group">
            <?php echo form_label('Summe der Creditpoints', 'creditpoints', $data_labelattrs); ?>
            <div class="controls docs-input-sizes">
                <?php echo form_input($data_creditpoints); ?>
            </div>
        </div>
    </div>
    <div class="span5">
        <?php echo form_textarea($data_beschreibung); ?>
    </div>
    <div class="clearfix"></div>
    <div class="row-fluid">
        <div class="span3"></div>
        <div class="span6">
            <?php echo form_submit($submit_data, 'Informationen speichern'); ?>
        </div>
        <div class="span3"></div>
    </div>
    <?php echo form_close(); ?>
</div>
<hr/>