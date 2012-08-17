<div class="well">
    
    <h3>Your file was successfully uploaded!</h3>

    foreach ($upload_data as $item => $value){
		'<li>'.$item;.':'.$value.'</li>'
	}

    <p><?php echo anchor('admin/stdplan_import', 'Weitere Datei hochladen?'); ?></p>
    
</div>
