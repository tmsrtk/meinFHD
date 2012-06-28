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
	<title><?php echo $siteinfo['title'] ?></title>

    <!-- Styles -->
    <!--	<link rel="stylesheet/less" type="text/css" href="../resources/bootstrap/less/bootstrap.less">
            <link rel="stylesheet/less" type="text/css" href="../resources/bootstrap/less/responsive.less"> -->
    <link rel="stylesheet/less" type="text/css" href="<?php print base_url(); ?>resources/less/meinfhd.less">
    <link rel="stylesheet/less" type="text/css" href="<?php print base_url(); ?>resources/less/meinfhd-responsive.less">
    <script src="<?php echo base_url(); ?>resources/jquery/jquery.min.js"></script>
    <script src="<?php print base_url(); ?>resources/jquery/jquery-ui.js"></script>
    

    <!--LESS compiler-->
    <script src="<?php print base_url(); ?>resources/lessjs/less-1.3.0.min.js" type="text/javascript"></script>

</head> <!-- /head -->

<body>
	<!-- Menu -->
	<?php  $this->load->view('includes/menu'); ?>
