<?php include('header.php'); ?>

<style>
	.std-rel { position: relative; }
	.std-abs { position: absolute; }
	
	.std-col {
		width: 150px;
		margin-right: 20px;
	}
	
	.std-event {
		overflow: hidden;
		border: 5px #A5A5A5 solid;
	}
	
	.std-event-container {
		padding: 4px;
		word-break: break-word;
	}
	
</style>

<!-- CONTENT -->
<div class="container container-fluid">
	<div class="row">		
		<div class="span12 clearfix">
			<div class="pull-left std-col">
				<div>&nbsp;</div>
				<?php foreach ($zeiten as $zeit) : ?>
					<div  style="height:60px;">
						<?php print $zeit['Beginn']; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<!-- Loop for every day -->
			<?php foreach ($stundenplan as $dayname => $day) : ?>
				<div class="pull-left std-col">
					<div class="std-head"><?php print $dayname; ?></div>
					<div class="std-rel">
						<?php foreach ($day as $event) : ?>
							
							<?php
								$event_height = 60;
								$event_width = 150;
								
								$css  = 'background-color:'	. $event['display_data']['color'] . ';';
								$css .= 'width:'			. $event_width * $event['display_data']['width'] . 'px;';
								$css .= 'height:'			. $event_height * $event['display_data']['duration'] . 'px;';
								$css .= 'margin-top:'		. $event_height * $event['display_data']['start'] . 'px;';								
								$css .= 'margin-left:'		. $event_width * (1 / $event['display_data']['max_cols']) * $event['display_data']['column'] . 'px;';
							?>
							
							<div class="std-abs std-event" style="<?php print $css; ?>">
								<div class="std-event-container">
									<h5><?php print $event['kurs_kurz']; ?></h5>
									<p><?php print $event['VeranstaltungsformName']; ?></p>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
			
		</div>
	</div>
	
	<div class="row">		
		<div class="span12">
			<div class="fhd-box clearfix">
				<a href="<?php print base_url(); ?>" class="btn btn-large btn-primary pull-left">
					<i class="icon-arrow-left icon-white"></i>
					Start
				</a>
				<a href="<?php print base_url('stundenplan'); ?>" class="btn btn-large pull-right">Tag</a>
			</div>
		</div><!-- /.span12-->		
	</div><!-- /.row-->
	
	
</div>

<?php include('footer.php'); ?>

