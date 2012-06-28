<?php $this->load->view('includes/header'); ?>

<?php 
if (isset($siteinfo)) {
	$this->load->view($siteinfo['main_content']);
}
else {
	$this->load->view($main_content);
}
?>

<?php $this->load->view('includes/footer'); ?>