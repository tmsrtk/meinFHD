<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Veranstaltungen<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>
	

<!- ------------------------------------------------ -->
<!-- Veranstaltungen -------------------------------- -->
<!- ------------------------------------------------ -->

		<div class="span4">
			<div class="well well-small clearfix">
		
				<h6>Veranstaltungsliste</h6>
		
				<!--Titel-->
    			<h1>Semester <?php echo $userdata["act_semester"]; ?></h1>
				<hr />

					<?php $alte_kursID = 1; ?>

					<?php foreach ($kurse as $kurs => $value): ?>

					<?php if ($value['KursID'] != $alte_kursID): ?>

							<a href="<?php echo base_url('modul/show/' . $value['KursID']); ?>" class="btn btn-large btn-fullwidth btn-primary"> <?php echo $value['Kursname']; ?><i class="icon-ok icon-white"></i></a>

					<?php endif ?>

					<?php $alte_kursID = $value['KursID']; ?>

					<?php endforeach ?>
			    		    	   									  
			</div>
			
		</div>
										

<?php endblock(); ?>
<?php end_extend(); ?>
