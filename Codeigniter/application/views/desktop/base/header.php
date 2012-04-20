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
		<?php $this->less->auto_compile_less(array('meinfhd.less','meinfhd-responsive.less')); // autocompile files ?>
<?php start_block_marker('headerCss'); ?>
		<link rel="stylesheet" type="text/css" href="<?php print base_url(); ?>resources/css/meinfhd.css">
		<link rel="stylesheet" type="text/css" href="<?php print base_url(); ?>resources/css/meinfhd-responsive.css">
		<link rel="stylesheet" type="text/css" href="<?php print base_url(); ?>resources/css/datepicker.css">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800,300italic,400italic,600italic,700italic,800italic' rel='stylesheet' type='text/css'>
<?php end_block_marker(); ?>
		
<?php start_block_marker('headerJS'); # additional js files ?>
		
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