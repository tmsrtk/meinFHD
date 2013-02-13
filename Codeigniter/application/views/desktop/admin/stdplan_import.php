<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Stundenplan importieren<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	<div class="span3"></div>
	<div class="span6 well well-small">
<?php endblock(); ?>

<?php // general form setup

    // preparing some variables
    $btn_send_attrs = 'class = "btn-warning"';
?>

<?php startblock('content'); # additional markup before content ?>
		<div class="row-fluid">
			<h2>Stundenplan importieren</h2>
		</div>
		<hr/>
		<div class="row-fluid">
			<div class="well">
				<!-- print out submission-form -->
				<?php 
					echo form_open_multipart('admin/upload_and_parse_timetable');
				?>
                <input type="file" name="userfile"/>
                <?php
					echo '<br />';
					echo form_submit('import_stdplan', 'Stundenplan importieren', $btn_send_attrs);
					echo form_close();
				?>
			</div>
			
			<h3>Importierte Dateien:</h3>

			<?php
				if($stdgng_uploads != null){
					// display the list with the uploaded files
					echo $stdgng_uploads_list_filelist;
                }
                else{
                    echo 'Es wurden bisher keine Dateien hochgeladen';
                }

			?>

		</div>

<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
	</div>
	<div class="span3"></div>
    <div id="modalcontent"></div>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode'); # custom jquery code?>

    // if there is an error message passed from the controller open up a modal view
    var error = "<?php echo $error_starting_import; ?>";

    if(error != '0'){
        _showModal('Fehler beim Hochladen der Datei', error, false);
    }

<?php endblock(); ?>

<?php end_extend(); ?>