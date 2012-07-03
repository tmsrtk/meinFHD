<!DOCTYPE html>
<html lang="de">
	<head>
		<!-- Meta stuff -->
		<meta charset="UTF-8">
		<meta http-equiv="Content-type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="HandheldFriendly" content="true" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		
		<!-- Dynamic title -->
		<title><?php start_block_marker('title'); # breadcrumb like title-tag ?>meinFHD<?php end_block_marker(); ?></title>
		
		<!-- Styles -->
		<!--
		<link rel="stylesheet/less" type="text/css" href="../resources/bootstrap/less/bootstrap.less">
		<link rel="stylesheet/less" type="text/css" href="../resources/bootstrap/less/responsive.less">
		-->
<?php start_block_marker('less'); ?>
		<link rel="stylesheet/less" type="text/css" href="<?php print base_url(); ?>resources/less/meinfhd.less">
		<link rel="stylesheet/less" type="text/css" href="<?php print base_url(); ?>resources/less/meinfhd-responsive.less">
<?php end_block_marker(); ?>
		
<?php start_block_marker('headerJS'); # additional js files ?>
		<!--LESS compiler-->
		<script src="<?php print base_url(); ?>resources/lessjs/less-1.3.0.min.js" type="text/javascript"></script>
<?php end_block_marker(); ?>

	</head> <!-- /head -->
	<body>
		<!-- First header has an ID so you can give it individual styles, and target stuff inside it -->
		<header id="header">
<?php start_block_marker('menu'); ?>
			<!-- Main nav, styled by targeting "#header nav"; you can have more than one nav element per page -->
<?php $this->load->view('base/menu'); ?>
<?php end_block_marker(); ?>
<?php start_block_marker('hgroup'); # hgroup tags can be used ?>
			<!-- "hgroup" is used to make two headings into one, to prevent a new document node from forming -->
			<!--
			<hgroup>
			<h1>Easy HTML5 Template</h1>
			<h2>tagline</h2>
			</hgroup>
			-->
<?php end_block_marker(); ?>
		</header><!-- #header -->