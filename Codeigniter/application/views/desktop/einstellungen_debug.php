<?php

echo form_error('login');
echo form_error('pw');
echo form_error('email');
//echo form_error('username');

echo form_open('einstellungen');
echo "Login-Daten: <br/>";
echo form_label('Login', 'login');
echo form_input('login', $info['LoginName']); 
echo "<br/>";
echo form_label('Passwort', 'pw');
echo form_password('pw', '');
echo form_password('pw2', ''); 
echo "<hr/>";
echo "Persönliche Infos: <br/>";
echo form_label('Titel', 'title');
echo form_input('title', $info['Titel']);
echo form_label('Raum', 'room');
echo form_input('room', $info['Raum']);
echo "<br/>";
echo form_label('Vorname', 'firstname');
echo form_input('firstname', $info['Vorname']);
echo form_label('Nachname', 'lastname');
echo form_input('lastname', $info['Nachname']); 
echo "<hr/>";
echo "Semester/Studiengang: <br/>";
echo form_label('Studiengang', 'studiengang');
echo form_input('studiengang', $info['StudiengangName']." "."[".$info['Pruefungsordnung']."]");	    //Dropdown
echo form_label('Jahr', 'year');
echo form_input('year', $info['StudienbeginnJahr']);
echo form_label('Semester', 'semester');
echo form_input('semester', $info['StudienbeginnSemestertyp']);				    //Radio-Buttons
echo "<hr/>";
echo "Email und Erreichbarkeit: <br/>";
echo form_label('Email', 'email');
echo form_input('email', $info['Email']);
echo form_label('Darf gezeigt werden', 'emailflag');
echo form_checkbox('emailflag', '1' ,($info['EmailDarfGezeigtWerden'] == 0 ? FALSE : TRUE));					    //Checkbox
echo "<hr/>";
echo "Sonstiges: <br/>";
echo form_label('Farbschema', 'theme');
echo form_input('theme', $info['farbschema']);						    //Dropdown
echo "<hr/>";
echo form_submit('update', 'Update');
echo form_close();
?>
