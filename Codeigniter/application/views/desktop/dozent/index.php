<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Dozent<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>

<?php
	
	// PHP FOR FUTURE IMAGE USE by SIMON VOM EYSER!
	
	// If there is no URL in the dozentinfo table
	/*
	if ($dozentinfo[0]['BildURL'] == '')
	{
		sprintf('<img src="%s" alt="dozent">', base_url('resources\img\dozent_standart.png'));
	}
	else
	{ 
		sprintf('<img src="%s" alt="dozent">', $dozentinfo[0]['BildURL']);
	}
	*/
?>

<div class="row-fluid">
	<div class="span6 well">
		<h6><?php echo $dozentinfo[0]['Titel']; ?></h6>	    			
		<h1><?php echo $dozentinfo[0]['Vorname']; ?> <?php echo $dozentinfo[0]['Nachname']; ?></h1>				
		<table class="table table-condensed" width="100%">
			<thead>
				<tr>
					<th width="100px">Informationen</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>								
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Büro</td>
					<td colspan="2"><?php echo $dozentinfo[0]['Raum']; ?></td>
				</tr>
				<tr>
					<td>Fachbereich</td>
					<td colspan="2"><?php echo $dozentinfo[0]['FachbereichID']; ?></td>
				</tr>
				<tr>
					<td>Email</td>
					<td><?php echo $dozentinfo[0]['Email']; ?></td>
					<td>
						<a href="mailto:<?php echo $dozentinfo[0]['Email']; ?>" class="btn pull-right">
							<i class="icon-envelope"></i>
						</a>
						</td>
				</tr>
			</tbody>
		</table>				    		    	   									  														
	</div><!--first row ends here-->		
</div>

<?php endblock(); ?>
<?php end_extend(); ?>