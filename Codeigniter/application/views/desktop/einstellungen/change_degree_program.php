<?php
    # general form setup
    $data_change_formopen = array(
        'class' => 'form-horizontal',
        'id' => 'change_degree_program'
    );

    $data_labelattrs = array(
        'class' => 'control-label'
    );

    $data_semesteranfang = array(
        'name' => 'semesteranfang_change',
        'id' => 'semesteranfang_change',
    );

    $data_startjahr = array(
        'class' => 'span3',
        'name' => 'startjahr_change',
        'id' => 'startjahr_change',
        'placeholder' => 'Startjahr',
        'value' => $student_data['StudienbeginnJahr'],
    );

    $class_dd = 'class="span8 studiengang_change_dd" id="studiengang_change"';

    // define variables for studienbeginn semester type radio buttons, that should be selected by default (only for students)
    if($student_data['StudienbeginnSemestertyp'] == 'WS'){
        $check_ws = TRUE;
        $check_ss = FALSE;
    }
    else{
        $check_ws = FALSE;
        $check_ss = TRUE;
    }

    // create the degree program dropdown value array
    $degree_program_dd = array();
    $degree_program_dd[0] = "Bitte ausw&auml;hlen";

    foreach($degree_programs as $single_program){
        $degree_program_dd[$single_program['StudiengangID']] = $single_program['StudiengangName'] . ' (PO ' . $single_program['Pruefungsordnung'] . ')';
    }

?>

        <div id="change_degree_program_modal" class="modal hide">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h3>Studiengang wechseln</h3>
            </div>

            <?php
                echo form_open('einstellungen/validate_change_degree_program', $data_change_formopen);
                echo validation_errors();
            ?>

            <div class="modal-body">
                <div class="alert">
                    <p>
                        Willst Du wirklich einen neuen Studiengang ausw&auml;hlen? Dadurch wird Dein Studien- & Stundenplan resettet. Du kannst Deinen bisherigen Studienplan auch als Tabelle speichern.
                        Klicke dazu auf den <a href="<?php echo base_url($csv_filepath); ?>">Link</a>.
                    </p>
                </div>

                <div class="control-group">
                    <?php echo form_label('Studiengang', 'degree_program_change_to_id', $data_labelattrs); ?>
                    <div class="controls">
                        <?php echo form_dropdown('degree_program_change_to_id', $degree_program_dd, $student_data['StudiengangID'], $class_dd); ?>
                    </div>
                </div>

                <div class="control-group">
                    <?php echo form_label('Startjahr', 'startjahr_change', $data_labelattrs); ?>
                    <div class="controls">
                        <?php echo form_input($data_startjahr); ?>
                    </div>
                </div>

                <div class="control-group">
                    <?php echo form_label('Semesteranfang', 'semesteranfang_change', $data_labelattrs); ?>
                    <div class="controls">
                        <label class="radio">
                            <?php echo form_radio($data_semesteranfang, 'WS', $check_ws); ?>
                            Wintersemester
                        </label>
                    </div>
                    <div class="controls">
                        <label class="radio">
                            <?php echo form_radio('semesteranfang_change', 'SS', $check_ss); ?>
                            Sommersemester
                        </label>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Schlie&szlig;en</a>
                <input type="submit" name="submit_degree_prog_change" class="btn btn-primary" value="Studiengang wechseln"/>
            </div>
            <?php echo form_close(); ?>
        </div>