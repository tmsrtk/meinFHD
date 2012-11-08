<div class="well well-small">
	<?php
		$btn_delete_attrs = 'class = "btn btn-danger"';
		$btn_open_attrs = 'class = "btn btn-info"';
		// show files in directory
		foreach($stdgng_uploads as $key => $entry) :?>
		<div> <!-- area that shows all files belonging to a single po -->

			<!-- show headline-->
			<h4 class="label label-info"><?php echo $stdgng_uploads_headlines[$key]; ?></h4>
			<table class="table table-striped">
				<thead>
					<th>Dateiname:</th>
					<th>Löschen:</th>
					<th>Öffnen:</th>
				</thead>
				<tbody>
					<?php foreach($entry as $filename) : ?>
					<tr>
						<!-- one form for each line -->
						<td><?php echo $filename; ?></td>
						<td>
							<?php
								echo form_open('admin/delete_stdplan_file');
								echo form_submit('delete_file', 'Löschen', $btn_delete_attrs);
								echo form_hidden('std_file_to_delete', $filename);
								echo form_close();
							?>
						</td>
						<td>
							<?php
								echo form_open('admin/open_stdplan_file');
								echo form_submit('open_file', 'Datei ansehen', $btn_open_attrs);
								echo form_hidden('std_file_to_open', $filename);
								echo form_close();
							?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endforeach; ?>
</div>
