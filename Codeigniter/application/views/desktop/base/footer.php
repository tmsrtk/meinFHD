<?php start_block_marker('postCodeFooter'); # use for hidden markup like modals ?>
<?php end_block_marker(); ?>
		<div class="clearfix"></div>
		<!-- only head.js is required, due to asynchronous js file handling -->
		<script src="<?php print base_url(); ?>resources/headjs/head.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			// load scripts and assign labels to them due to better js management
			head.js(
				{jquery: "<?php print base_url(); ?>resources/jquery/jquery.min.js"},
				{jquery_ui: "<?php print base_url(); ?>resources/jquery/jquery-ui.js"},
				{meinfhd: "<?php print base_url(); ?>resources/js/meinfhd.js"},
				{bootstrap_alert: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-alert.js"},
				{bootstrap_collapse: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-collapse.js"},
				{bootstrap_carousel: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-carousel.js"},
<?php start_block_marker('headJSfiles'); ?>
				{bootstrap_button: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-button.js"},
				{bootstrap_modal: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-modal.js"},
				{bootstrap_transition: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-transition.js"},
<?php end_block_marker(); ?>
				{bootstrap_dropdown: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-dropdown.js"}
			);
		</script>
		<script type="text/javascript">
			
			head.ready(function() {
<?php start_block_marker('customFooterJQueryCode'); ?>
				
<?php end_block_marker(); ?>
			})();
			
		</script>
	</body> <!-- /body -->
</html> <!-- /html -->