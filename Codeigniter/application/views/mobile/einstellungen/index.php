<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Stundenplan - Tag<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>

<form id="user-details" class="form-horizontal" method="post" action="<?php print base_url('einstellungen'); ?>">

	<div class="row-fluid">
	
		<div class="span6 well" id="matrikel-und-sem">
			<h6>Einstellungen</h6>
			<h1>Meine Daten</h1>
			
			<table class="table table-condensed">
				<thead>
					<tr>
						<th colspan="2">Persönliche Informationen</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td width="135px">Matrikelnummer</td>
						<td><?php print $info['Matrikelnummer'] ?></td>
					</tr>
					<tr>
						<td>Fachsemester</td>
						<td><?php print $info['Semester'] ?></td>
					</tr>
				</tbody>
			</table>
	    </div>
	    	
		<div class="span6 well">
		    <fieldset id="login-details">
			    <legend>Login-Details</legend>
			    
				<label for="login-name">Loginname</label>
				<input class="span special" type="text" id="login-name" name="login" placeholder="Loginname"<?php if(isset($info['LoginName'])) echo ' value="' . $info['LoginName'] . '"' ?> />
				
				<label for="password">Passwort</label>
				<input class="span special" type="password" id="password" name="pw" placeholder="Passwort" />
				 
				<label for="confirm-password">Passwort bestätigen</label>
				<input class="span special" type="password" id="confirm-password" name="pw2" placeholder="Passwort bestätigen" />
				 
			</fieldset>
		</div>
	
	</div>
	
	<div class="row-fluid">
		
		<div class="span6 well">
			<fieldset id="contact-details">
				<legend>Kontaktinformationen</legend>
	
	            <label for="first-name">Vorname</label>
	            <input class="span special" type="text" id="first-name" name="firstname" placeholder="Vorname"<?php if ( isset($info['Vorname']) ) echo ' value="' . $info['Vorname'] . '"' ?> />
	            
	            <label for="last-name">Nachname</label>
	            <input class="span special" type="text" id="last-name" name="lastname" placeholder="Nachname"<?php if ( isset($info['Nachname']) ) echo ' value="' . $info['Nachname'] . '"' ?> />
	            
	            <label for="email">E-Mail-Adresse</label>
	            <input class="span special" type="email" id="email" name="email" placeholder="E-Mail-Adresse"<?php if ( isset($info['Email']) ) echo ' value="' . $info['Email'] . '"' ?> />
	            
	            <label for="private-correspondence" class="checkbox">
	            	<input type="checkbox" id="private-correspondence" name="emailflag"<?php if ( isset($info['EmailDarfGezeigtWerden']) && $info['EmailDarfGezeigtWerden'] == 1 ) echo ' checked' ?> /> Dozenten dürfen mich unter dieser Adresse auch persönlich erreichen.
	            </label>
	
	        </fieldset>
		</div>
		
		<div class="span6 well">
			<fieldset id="study-course-details">
				<legend>Studiengang</legend>
	
	            <label for="study-course">Studiengang</label>
	            <select name="stgid" id="study-course" class="span special">
	              <?php
	                $option = '<option value="%s">%s</option>';
	                echo sprintf($option, '0', '--Bitte Wählen--');
	                $option = '<option value="%s">%s (PO%s)</option>';             
	                for( $i = 0; $i < count($stgng); $i++ ) {
	                  $s = "";
	                  if ( $stgng[$i]['StudiengangID'] == $info['StudiengangID'] ) {$s = '<option selected value="%s">%s (PO%s)</option>';}
	                  else {$s = $option;}
	                  echo sprintf($s, $stgng[$i]['StudiengangID'], $stgng[$i]['StudiengangName'], $stgng[$i]['Pruefungsordnung']);
	                }
	              ?>
	            </select>
	            
	            <label for="year">Startjahr</label>
	            <input class="span special" type="text" id="year" name="year" placeholder="Startjahr"<?php if ( isset($info['StudienbeginnJahr']) ) echo ' value="' . $info['StudienbeginnJahr'] . '"' ?> />
	            
	            <label for="start-term-winter" class="radio">
	            	<input type="radio" id="start-term-winter" name="semester" value="WS"<?php if ( isset($info['StudienbeginnSemestertyp']) && $info['StudienbeginnSemestertyp'] == 'WS' ) echo ' checked' ?> /> Wintersemester
	            </label>
	            <label for="start-term-summer" class="radio">
	            	<input type="radio" id="start-term-summer" name="semester" value="SS"<?php if ( isset($info['StudienbeginnSemestertyp']) && $info['StudienbeginnSemestertyp'] == 'SS' ) echo ' checked' ?> /> Sommersemester
	            </label>
	            
	        </fieldset>
	    </div>
	
	</div>
	      
	<div class="fhd-box">
		<a href="<?php print base_url('dashboard/mobile'); ?>" class="btn btn-large btn-primary">Übersicht</a>
		<button type="submit" class="btn btn-large pull-right" value="save">Speichern</button>
	</div>

</form>

<?php endblock(); ?>

<?php startblock('headJSfiles'); ?>
	{meinfhd_radiobuttons: "<?php print base_url(); ?>resources/js/meinfhd.radiobuttons.js"},
	{meinfhd_checkboxes: "<?php print base_url(); ?>resources/js/meinfhd.checkboxes.js"},
<?php endblock(); ?>

<?php end_extend(); ?>