<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Studiengang kopieren<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
    <div class="span2"></div>
    <div class="span8 well well-small">
        <?php endblock(); ?>

        <?php startblock('content'); # additional markup before content ?>
        <div class="row-fluid">
            <h2>Studiengang kopieren</h2>
            <p>
                Um einen Studiengang zu kopieren bitte den 'Studiengang kopieren'-Button in der
                zu dem Studiengang geh&ouml;hrenden Zeile anklicken.
            </p>
        </div>
        <hr/>
        <div class="row-fluid" id="degree_program_delete_header">
            <div class="span4"><strong>Studiengang</strong></div>
            <div class="span4"><strong>Pr&uuml;fungsordnung</strong></div>
            <div class="span4"><strong>Kopieren?</strong></div>
        </div>
        <div id="delete_degree_program_content">
            <!-- place for the single degree program forms -->
            <?php foreach($all_degree_programs as $single_degree_program): // create an entry for each single degree program in the array?>
                <?php    // general form setup
                $data_formopen = array(
                    'id' => 'copy_degree_program_row'. $single_degree_program->StudiengangID, // custom id because of validation
                );


                $submit_data = array(
                    'id' 			=> 'copy_degree_program_button' . $single_degree_program->StudiengangID,
                    'name'			=> 'copy_degree_program_button',
                    'class'			=> 'btn btn-mini btn-danger'
                );

                ?>
                <div class="row-fluid zebra-striped-div">
                    <?php echo form_open('admin/copy_degree_program', $data_formopen); ?>
                    <div class="span4"><?php echo $single_degree_program->StudiengangName; ?></div>
                    <div class="span4"><?php echo $single_degree_program->Pruefungsordnung; ?></div>
                    <?php echo form_hidden('degree_program_id', $single_degree_program->StudiengangID); ?>
                    <div class="span4"><?php echo form_submit($submit_data, 'Studiengang kopieren'); ?></div>
                    <?php echo form_close(); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endblock(); ?>

        <?php startblock('postCodeContent'); # additional markup before content ?>
    </div>
    <div class="span2"></div>
    <div id="modalcontent"><!-- place for modal dialog html markup --></div>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>

    $('div').on('click', '[id^=copy_degree_program_button]', function(){

        $(this).attr("data-clicked", "true");
        // show an modal for additonal user confirmation
        _showModal("Studiengang kopieren", "Soll der ausgew&auml;hlte Studiengang wirklich kopiert werden?", true);

        // prevent default submit behaviour
        return false;
    });

<?php endblock(); ?>
<?php end_extend(); ?>