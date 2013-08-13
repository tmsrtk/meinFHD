<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Studiengang anlegen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	    <div class="span2"></div>
	    <div class="span8">
		    <div class="well well-small">
<?php endblock(); ?>

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
        'value' => set_value('pruefungsordnung')
    );

    $data_studiengangname = array(
        'class' => 'input-medium',
        'name' => 'studiengangname',
        'id' => 'studiengangname',
        'placeholder' => 'z.B. B.Sc. Medieninformatik',
        'value' => set_value('studiengangname')
    );

    $data_abkuerzung = array(
        'class' => 'input-medium',
        'name' => 'studiengangsabkuerzung',
        'id' => 'studiengangsabkuerzung',
        'placeholder' => 'z.B. BMI',
        'value' => set_value('studiengangsabkuerzung')
    );

    $data_regelsemester = array(
        'class' => 'input-medium',
        'name' => 'regelsemester',
        'id' => 'regelsemester',
        'placeholder' => 'z.B. 7',
        'value' => set_value('regelsemester')
    );

    $data_creditpoints = array(
        'class' => 'input-medium',
        'name' => 'creditpoints',
        'id' => 'creditpoints',
        'placeholder' => 'z.B. 210',
        'value' => set_value('creditpoints')
    );

    #textarea
    $data_beschreibung = array(
        'name' => 'beschreibung',
        'id' => 'beschreibung',
        'class' => 'input-xxlarge',
        'placeholder' => 'Beschreibung des Studiengangs in textueller Form. Bitte keine HTML-Tags oder HTML-Elemente eintragen.',
        'rows' => 7,
        'cols' => 40,
        'value' => set_value('beschreibung')
    );

    $submit_data = array(
        'name'			=> 'submit',
        'class'			=> 'btn btn-danger input-medium'
    );
?>
<?php startblock('content'); # additional markup before content ?>
	    <div class="row-fluid">
		    <h2>Studiengang anlegen</h2>
            <p>
                Zum Anlegen eines neuen Studiengangs m&uuml;ssen alle mit einem Stern (*) gekennzeichneten Felder
                ausgef&uuml;llt werden, bevor das Formular abgesendet wird.
            </p>
	    </div>
        <?php echo validation_errors(); ?>
	    <hr/>

        <div class="row-fluid">
            <?php echo form_open('admin/validate_create_degree_program', $data_formopen); ?>

            <div class="control-group">
                <?php echo form_label('Pr&uuml;fungsordnung*','pruefungsordnung', $data_labelattrs); ?>
                <div class="controls docs-input-sizes">
                    <?php echo form_input($data_pruefungsordnung); ?>
                </div>
            </div>
            <div class="control-group">
                <?php echo form_label('Name des Studiengangs*', 'studiengangname', $data_labelattrs); ?>
                <div class="controls docs-input-sizes">
                    <?php echo form_input($data_studiengangname); ?>
                </div>
            </div>
            <div class="control-group">
                <?php echo form_label('Studiengangsabk&uuml;rzung*', 'studiengangsabkuerzung', $data_labelattrs); ?>
                <div class="controls docs-input-sizes">
                    <?php echo form_input($data_abkuerzung); ?>
                </div>
            </div>
            <div class="control-group">
                <?php echo form_label('Anzahl der Regelsemester*', 'regelsemester', $data_labelattrs); ?>
                <div class="controls docs-input-sizes">
                    <?php echo form_input($data_regelsemester); ?>
                </div>
            </div>
            <div class="control-group">
                <?php echo form_label('Summe der Creditpoints*', 'creditpoints', $data_labelattrs); ?>
                <div class="controls docs-input-sizes">
                    <?php echo form_input($data_creditpoints); ?>
                </div>
            </div>
            <div class="control-group">
                <?php echo form_label('Studiengangsbeschreibung*', 'beschreibung', $data_labelattrs); ?>
                <br/>
                <br/>
                <?php echo form_textarea($data_beschreibung); ?>
            </div>
            <hr/>
            <div class="control-group">
                <div class="controls docs-input-sizes">
                    <?php echo form_submit($submit_data, 'Neuen Studiengang anlegen'); ?>
                </div>
            </div>
            <?php
                echo form_hidden('fachbereich', 5);
                echo form_hidden('creditpoints_min', 0);
                echo form_close();
            ?>
	    </div><!-- /.row-fluid -->
<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup before content ?>
		    </div>
	    </div>
	    <div class="span2"></div>
<?php endblock(); ?>
<?php end_extend(); ?>