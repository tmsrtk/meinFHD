<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>meinFHD</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<meta name="HandheldFriendly" content="true" />
		
		<!--apple meta tags-->
		<meta name="apple-mobile-web-app-capable" content="yes" />
		
		<!-- Styles -->
	<!--	<link rel="stylesheet/less" type="text/css" href="../resources/bootstrap/less/bootstrap.less">
		<link rel="stylesheet/less" type="text/css" href="../resources/bootstrap/less/responsive.less"> -->
		<link rel="stylesheet/less" type="text/css" href="<?php print base_url(); ?>resources/less/meinfhd.less">
		
		<!--NEEDS TO BE INLINE!	-->
		<style type="text/css">
			body {
				padding-top: 80px;
				padding-bottom: 40px;
			}
		</style>
		
		<link rel="stylesheet/less" type="text/css" href="<?php print base_url(); ?>resources/less/meinfhd-responsive.less">
		
		<!--LESS compiler-->
		<script src="<?php print base_url(); ?>resources/lessjs/less-1.3.0.min.js" type="text/javascript"></script>
	</head> <!-- /head -->
	<body>
		
		<?php if ($this->authentication->is_logged_in()) : // if user eingeloggt ?>
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<a class="brand" href="<?php print base_url(); ?>">meinFHD<span>mobile</span></a>
					<div class="nav-collapse">
						<ul class="nav">
							<li class="active"><a href="<?php print base_url('dashboard'); ?>">Dashboard</a></li>
							<li><a href="<?php print base_url('stundenplan'); ?>">Stundenplan</a></li>
							<li><a href="<?php print base_url('einstellungen'); ?>">Persönlich Daten</a></li>
							<li><a href="<?php print base_url('hilfe'); ?>">Hilfe</a></li>
							<li><a href="<?php print base_url('impressum'); ?>">Impressum</a></li>
							<!--LOGOUT-->
							<li><a href="<?php print base_url('logout'); ?>">Logout</a></li>
						</ul>
					</div><!--/.nav-collapse -->
				</div>
			</div>
		</div>
		<?php else : // else zeige pseudonav ?>
		<!--pseudonav-->
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<a class="brand" href="<?php print base_url(); ?>">meinFHD<span>mobile</span></a>
			</div>
		</div>
		<!--pseudonav ends here-->
		<?php endif; ?>
		<?php print $messages; ?>