		<!-- only head.js is required, due to asynchronous js file handling -->
		<script src="<?php print base_url(); ?>resources/headjs/head.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			// load scripts and assign labels to them due to better js management
			head.js( <?php # $javascripFilesAlsArray ?>
				{jquery: "<?php print base_url(); ?>resources/jquery/jquery.min.js"},
				{meinfhd: "<?php print base_url(); ?>resources/js/meinfhd.js"},
				{bootstrap_alert: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-alert.js"},
				{bootstrap_dropdown: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-dropdown.js"},				
				{bootstrap_dropdown: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-button.js"},
				{bootstrap_dropdown: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-collapse.js"},
				{bootstrap_dropdown: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-transition.js"},				
				{bootstrap_dropdown: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-modal.js"}
			);
		</script>
	</body> <!-- /body -->
</html> <!-- /html -->