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
				{bootstrap_alert: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-alert.js"},
				{bootstrap_collapse: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-collapse.js"},
				{bootstrap_carousel: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-carousel.js"},
                {bootstrap_modal: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-modal.js"},
                {bootstrap_button: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-button.js"},
                {bootstrap_transition: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-transition.js"},
<?php start_block_marker('headJSfiles'); ?>
                {bootstrap_dropdown: "<?php print base_url(); ?>resources/bootstrap/js/bootstrap-dropdown.js"},
<?php end_block_marker(); ?>
				{meinfhd: "<?php print base_url(); ?>resources/js/meinfhd.js"},
                {meinfhd: "<?php print base_url(); ?>resources/js/meinfhd.mobile.js"},
				{meinfhd: "<?php print base_url(); ?>resources/js/hammer.js"},
				{meinfhd: "<?php print base_url(); ?>resources/js/jquery.specialevent.hammer.js"}
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