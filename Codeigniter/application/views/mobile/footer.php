		<!-- only head.js is required, due to asynchronous js file handling -->
		<script src="<?php print base_url(); ?>resources/headjs/head.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			// load scripts and assign labels to them due to better js management
			head.js( <?php # $javascripFilesAlsArray ?>
				// custom modernizr build created to only support html5 formfield attributes
				{modernizr_custom: "<?php print base_url(); ?>resources/js/modernizr.custom.js"}, 
				{jquery: "<?php print base_url(); ?>resources/jquery/jquery.min.js"},
				{meinfhd_polyfills: "<?php print base_url(); ?>resources/js/meinfhd.polyfills.js"},
				{meinfhd_labels: "<?php print base_url(); ?>resources/js/meinfhd.labels.js"},
				{meinfhd_radiobuttons: "<?php print base_url(); ?>resources/js/meinfhd.radiobuttons.js"},
				{meinfhd_checkboxes: "<?php print base_url(); ?>resources/js/meinfhd.checkboxes.js"},
				//{meinfhd: "<?php print base_url(); ?>resources/js/meinfhd.js"},
				{bootstrap_alert: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-alert.js"},
				{bootstrap_alert: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-collapse.js"},				
				{bootstrap_dropdown: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-dropdown.js"},
				{bootstrap_dropdown: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-carousel.js"}
			);										
		</script>
	</body> <!-- /body -->
</html> <!-- /html -->