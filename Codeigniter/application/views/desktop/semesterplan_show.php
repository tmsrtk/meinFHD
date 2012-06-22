<h3>Semesterplan</h3>

<?php FB::log($modules_order_sem_1); ?>







<?php for ($i=1; $i<=$count_semester; $i++ ) { ?>
	<ul id="semesterplansemester_<?php echo $i ?>" class="unstyled semesterplanspalte">
		<?php $varname = 'modules_order_sem_'.$i; ?>
		<?php foreach(${$varname} as $modul): ?>
			<li id="module_<?php echo $modul['KursID'] ?>">
				<div class="semestermodul btn btn-success btn-large">
					<span class="modulfach"><?php echo $modul['kurs_kurz'] ?></span>
					<span class="modulfachnote">NP:</span>
					<input id="modulnote" class="input-small" name="modulnote" type="text" value="55" size="3">
				</div>
			</li>
		<?php endforeach ?>
	</ul>
<?php } ?>


<h1><?php echo $user ?></h1>