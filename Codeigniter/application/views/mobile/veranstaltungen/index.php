<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Veranstaltungen<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>
	

<!- ------------------------------------------------ -->
<!-- Veranstaltungen -------------------------------- -->
<!- ------------------------------------------------ -->
<div class="row-fluid">
	<div class="span6 well">

		<h6>Veranstaltungsliste</h6>
		<h1>Semester <?php echo $userdata["act_semester"]; ?></h1>

		<?php $alte_kursID = 1; ?>
		
		<table class="table table-condensed" width="100%">
			<thead>
				<tr>
					<th width="85%">Veranstaltung</th>
					<th>Details</th>					
				</tr>
			</thead>
			<tbody>
		
			<?php foreach ($kurse as $kurs => $value): ?>
	
				<?php if ($value['KursID'] != $alte_kursID): ?>
					<tr>
						<td><?php echo $value['Kursname']; ?></td>
						<td>
							<a href="<?php echo base_url('modul/show/' . $value['KursID']); ?>" class="btn btn-primary pull-right">
								<i class="icon-arrow-right icon-white"></i>
							</a>
						</td>						
					</tr>
				<?php endif ?>
	
				<?php $alte_kursID = $value['KursID']; ?>
	
			<?php endforeach ?>
			</tbody>
		</table>
		    		    	   									  	
	</div>
</div>										

<?php endblock(); ?>
<?php end_extend(); ?>
