<!-- overview over relevant persons for this course -->
<h3>Personen:</h3>
<table class="table table-striped table-bordered table-condensed">
    <tbody>
	<tr>
	    <td class="span1 ">
		<?php // if( TODO ask for role - only profs and labings may add ?>
		<span class="label label-info">Dozent(en)</span>
	    </td>
	    <td>
		<?php if($role_tutor == '0'){
		    echo '<a class="btn btn-mini" href="#">+</a>';
		}
		?>
		<?php // print out all profs ?>
		Hier stehen die Dozenten
	    </td>
	</tr>
	<tr>
	    <td>
		<span class="label label-info">Betreuer</span>
	    </td>
	    <td>
		<?php if($role_tutor == '0'){
		    echo '<a class="btn btn-mini" href="#">+</a>';
		}
		?>
		<?php // print out all betreuer ?>
		Hier stehen die Betreuer
	    </td>
	</tr>
	<tr>
	    <td>
		<span class="label label-info">Tutor(en)</span>
	    </td>
	    <td>
		<?php if($role_tutor == '0'){
		    echo '<a class="btn btn-mini" href="#">+</a>';
		}
		?>
		<?php // print out all tuts ?>
		Hier stehen die Tutoren
	    </td>
	</tr>
    </tbody>
</table>