<!-- overview over relevant persons for this course -->
<h3>Personen:</h3>
<table class="table table-striped table-bordered table-condensed">
    <tbody>
	<tr>
	    <td class="span1 ">
		<span class="label label-info">Dozent(en)</span>
	    </td>
	    <td>
		<?php if($role_tutor == '0'){
		    echo '<a class="btn btn-mini" href="#">+</a>';
		    // TODO while adding labings to courses (at the moment only whole
		    // course is possible (>> all spkursids for that courseid)
		    // write in labing-table to save data - same with tuts
		    
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