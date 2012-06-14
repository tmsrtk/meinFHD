<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8">
	<title><?php echo $title?></title>
	<link rel="stylesheet/less" href="<?php echo base_url(); ?>resources/less/meinfhd.less">
	<script src="<?php echo base_url(); ?>resources/lessjs/less-1.3.0.min.js"></script>
	<script src="<?php echo base_url(); ?>resources/jquery/jquery.min.js"></script>
	<script src="<?php echo base_url(); ?>resources/jquery/jquery-ui.js"></script>
	<script src="<?php echo base_url(); ?>resources/bootstrap/js/bootstrap-dropdown.js"></script>
	<script src="<?php echo base_url(); ?>resources/bootstrap/js/bootstrap-modal.js"></script>
</head>

<body>

	<div class="container">
		
		<!-- Menue -->
		<?php $this->load->view('includes/menue'); ?>