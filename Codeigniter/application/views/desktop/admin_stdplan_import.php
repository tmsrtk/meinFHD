<div class="well">
    <?php
	// preparing some varibles
    	$btn_send_attrs = 'class = "btn-warning"';
	$input_attrs = array(
	    'name' => 'userfile',
	    'type' => 'file'
	);
	
//	echo '<pre>';
//	print_r($stdgng_uploads);
//	echo '</pre>';
    ?>
    
    <!-- print out submission-form -->
    <?php 
	echo $error;
	echo form_open_multipart('admin/import_stdplan_and_parse');
	echo form_input($input_attrs);
	echo form_submit('import_stdplan', 'Stundenplan importieren', $btn_send_attrs);
	echo form_close();
    ?>
    
    <?php
	if($stdgng_uploads != null){
	    // load viewlist
//	    $this->load->view('admin_stdplan_import_filelist');
	    echo $stdgng_uploads_list_filelist;
        }
    ?>
    
</div>