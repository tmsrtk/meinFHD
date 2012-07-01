<?php
    $btn_delete_attrs = 'class = "btn-danger"';
    // show files in directory
    foreach($stdgng_uploads as $key => $entry) :?>
	<div> <!-- area that shows all files belonging to a single po -->

	    <!-- show headline-->
	    <h3><?php echo $stdgng_uploads_headlines[$key]; ?></h3>

	    <?php foreach($entry as $e) : ?>
		<div><!-- one line holds a file + button-->

		    <!-- one form for each line -->
		    <?php echo form_open('admin/delete_stdplan_file'); ?>
		    <div style="float:left; margin-right:20px;">
			<?php echo form_submit('delete_file', 'Löschen!', $btn_delete_attrs); ?>
			<?php echo form_hidden('std_file_to_delete', $e); ?>
		    </div>
		    <div><?php	echo form_label($e, 'filename'); ?></div>
		    <?php echo form_close(); ?>

		</div>
	    <?php endforeach; ?>
	</div>
<?php endforeach; ?>
