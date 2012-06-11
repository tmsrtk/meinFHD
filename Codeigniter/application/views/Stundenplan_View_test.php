<html>
<head>
<title> <?php echo $titel ?></title>
</head>
<body>
<h1>Stundenplan View! </h1>
<h3>Inhalt:</h3>
<h4>Zeige von die aktiven Kurse der Benutzerkurs-Tabelle: </h4>

<table width=100% border=1>
	<tr>
		<td></td>
		
		<td><?php echo $tage[0]['TagName'] ?></td>
		<td>Dienstag</td>
		<td>Mittwoch</td>
		<td>Donnerstag</td>
		<td>Freitag</td>
		<td>Samstag</td>
		<td>Sonntag</td>
	</tr>
	<tr>
		<td>-</td>
		<td>-</td>
		<td>-</td>
		<td>-</td>
		<td>-</td>
		<td>-</td>
		<td>-</td>
		<td>-</td>
	</tr>
</table>


<?php foreach ($aktivekurse as $aktiverkurs): ?>

    <h2><?php 
	
	
	echo 'An Tag '. $aktiverkurs['TagID'] . ' von Stunde ' .$aktiverkurs['StartID'] . ' bis Stunde '. $aktiverkurs['EndeID'] . ': ' . $aktiverkurs['kurs_kurz'] . ' ' . $aktiverkurs['VeranstaltungsformName'] . $aktiverkurs['VeranstaltungsformAlternative'] ;
		
	?></h2>
	
<?php endforeach; ?>




</h4>
</body>
</html>
	
	
	