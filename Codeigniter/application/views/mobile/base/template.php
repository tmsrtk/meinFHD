<?php $this->load->view('base/header'); ?>
		<!-- CONTENT -->
		<div class="container container-fluid">

<?php start_block_marker('preCodeContent'); # additional markup before content ?>
				
<?php end_block_marker(); ?>

<?php start_block_marker('content'); # content for this view ?>
				
<?php end_block_marker(); ?>

<?php start_block_marker('postCodeContent'); # additional markup after content ?>
				
<?php end_block_marker(); ?>

		</div>
		<!-- CONTENT ENDE-->
		
<?php $this->load->view('base/footer'); ?>