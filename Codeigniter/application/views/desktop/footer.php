		<!-- only head.js is required, due to asynchronous js file handling -->
		<script src="../resources/headjs/head.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			// load scripts and assign labels to them due to better js management
			head.js( <?php # $javascripFilesAlsArray ?>
				{jquery: "../resources/jquery/jquery.min.js"},
				{meinfhd: "../resources/js/meinfhd.js"},
				{bootstrapAlert: "../resources/bootstrap/js/bootstrap-alert.js"}
			);
		</script>
	</body> <!-- /body -->
</html> <!-- /html -->