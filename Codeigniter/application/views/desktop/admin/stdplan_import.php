<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Stundenplan importieren<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	<div class="span3"></div>
	<div class="span6 well well-small">
<?php endblock(); ?>
	
<?php
	// general form setup
	
?>

<?php startblock('content'); # additional markup before content ?>
		
		<div class="row-fluid">
			<h2>Stundenplan importieren</h2>
		</div>
		<hr>

		<div class="row-fluid">
			<?php
			// preparing some varibles
				$btn_send_attrs = 'class = "btn-warning"';
				$input_attrs = array(
					'name' => 'userfile',
					'type' => 'file',
					'value' => ''
				);

			?>

			<div class="well">
				<!-- print out submission-form -->
				<?php 
					echo form_open_multipart('admin/stdplan_import_parse');
					echo form_input($input_attrs);
					echo '<br />';
					echo form_submit('import_stdplan', 'Stundenplan importieren', $btn_send_attrs);
					echo form_close();
				?>
			</div>
			
			<h3>Hochgeladene Dateien:</h3>

			<?php
			if($stdgng_uploads != null){
				// load filelist
				echo $stdgng_uploads_list_filelist;
				}
			?>

		</div>
		<div id="info-modal-container"></div>
		
<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
	</div>
	<div class="span3"></div>
<?php endblock(); ?>
	
<?php startblock('customFooterJQueryCode');?>
	
	// getting data that indicates if dialog should be showed
	// string 'error' if there is one, any other string for successfully parsed data, empty string for no dialog
	var parsedData = "<?php echo $view_feedback_dialog; ?>";
	
	// check if dialog should be showed
	if(parsedData == ''){
		// nothing
	} else {
		if(parsedData != 'error'){
			// parsing was successful - show data in modal
			showInfoDialog('Stundenplan wurde hinzugefügt', parsedData);
		} else {
			// >> ERROR during parsing
			showInfoDialog('Fehler beim Parsen', 'Der Stundenplan konnte nicht geparst werden.');
		}
	}

	// function to show a dialog
	function showInfoDialog(title, text){
		// open dialog and set text to show
		var dialog = createInfoDialog(title, text);
		$('#info-modal-container').html(dialog);
		
		// function of dialog - just disable keyboard-control
		$('#info-dialog').modal({
			keyboard: false
		}).on('show', function(){
			// nothing to do
		}).modal('show');

		return false;
	}
	
	// create dialog element
	// title > simple string; text > string with data and line-breakings
	function createInfoDialog(title, text) {
		var myDialog = 
			$('<div class="modal hide" id="info-dialog"></div>')
			.html('<div class="modal-header"><h3>'+title+'</h3></div>')
			.append('<div class="modal-body"><p>'+text+'</p></div>')
			.append('<div class="modal-footer"><a href="" class="btn btn-primary" id="info-dialog-confirm" data-dismiss="modal">OK</a></div>');

		return myDialog;
    };
	
	
<?php endblock(); ?>
	
	

<?php end_extend(); ?>