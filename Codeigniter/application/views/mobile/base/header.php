<!DOCTYPE html>
<html lang="de">
	<head>
        <link rel="shortcut icon" href="<?php echo site_url('/resources/favicon/favicon.ico');?>" type="image/x-icon">
        <link rel="icon" href="<?php echo site_url('/resources/favicon/favicon.ico');?>" type="image/x-icon">
		<!-- Meta stuff -->
		<meta charset="UTF-8">
		<meta http-equiv="Content-type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="HandheldFriendly" content="true" />
		<meta name="viewport" content="width=device-width, user-scalable=no" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		
		<!-- Dynamic title -->
		<title><?php start_block_marker('title'); # breadcrumb like title-tag ?>meinFHD<?php end_block_marker(); ?></title>
        <!-- styles -->
		<?php $this->less->auto_compile_less(array('meinfhd.less','meinfhd-responsive.less')); // autocompile files ?>
<?php start_block_marker('headerCss'); ?>
		<link rel="stylesheet" type="text/css" href="<?php print base_url(); ?>resources/css/meinfhd.css">
		<link rel="stylesheet" type="text/css" href="<?php print base_url(); ?>resources/css/meinfhd-responsive.css">
<?php end_block_marker(); ?>
		
<?php start_block_marker('headerJS'); # additional js files ?>
		
<?php end_block_marker(); ?>

	</head> <!-- /head -->
	<body>
		<header id="header">
            <?php start_block_marker('menu'); ?>
                <?php $this->load->view('base/menu'); ?>
            <?php end_block_marker(); ?>
            <?php start_block_marker('hgroup'); # hgroup tags can be used ?>
            <?php end_block_marker(); ?>
		</header><!-- #header -->
		<?php print $messages; ?>