<div class="navbar">
	<div class="navbar-inner">
		<div class="container">
			<ul class="nav">
				<?php if (in_array(100, $global_data['userdata']['userpermissions'])) : ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						Dashboard
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<?php if (in_array(101, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="#">FAQ</a></li>
						<?php endif ?>
						<?php if (in_array(102, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="#">Hilfe</a></li>
						<?php endif ?>
						<?php if (in_array(103, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="#">Email Verwaltung</a></li>
						<?php endif ?>
					</ul>
				</li>
				<?php endif ?>
				<?php if (in_array(600, $global_data['userdata']['userpermissions'])) : ?>
				<li><a href="#">Mein Semesterplan</a></li>
				<?php endif ?>
				<?php if (in_array(700, $global_data['userdata']['userpermissions'])) : ?>
				<li><a href="#">Mein Stundenplan</a></li>
				<?php endif ?>
				<?php if (in_array(200, $global_data['userdata']['userpermissions'])) : ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						Benutzerverwaltung
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<?php if (in_array(201, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="#">Einladungsaufforderung</a></li>
						<?php endif ?>
						<?php if (in_array(202, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/create_user_mask">Benutzer anlegen</a></li>
						<?php endif ?>
						<?php if (in_array(203, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/show_permissions">Benutzer importieren</a></li>
						<?php endif ?>
						<?php if (in_array(204, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/edit_user_mask">Benutzer bearbeiten</a></li>
						<?php endif ?>
						<?php if (in_array(205, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/delete_user_mask">Benutzer loeschen</a></li>
						<?php endif ?>
						<?php if (in_array(206, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/show_role_permissions">Rechte verwalten</a></li>
						<?php endif ?>
					</ul>
				</li>
				<?php endif ?>
				<?php if (in_array(300, $global_data['userdata']['userpermissions'])) : ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						Studiengangverwaltung
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<?php if (in_array(301, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/create_new_stdgng">Studiengang anlegen</a></li>
						<?php endif ?>
						<?php if (in_array(302, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="#">Studiengang importieren</a></li>
						<?php endif ?>
						<?php if (in_array(303, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/show_stdgng_course_list">Studiengang bearbeiten</a></li>
						<?php endif ?>
						<?php if (in_array(304, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="#">Studiengang kopieren</a></li>
						<?php endif ?>
						<?php if (in_array(305, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/delete_stdgng_view">Studiengang loeschen</a></li>
						<?php endif ?>
					</ul>
				</li>
				<?php endif ?>
				<?php if (in_array(400, $global_data['userdata']['userpermissions'])) : ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						Stundenplanverwaltung
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<?php if (in_array(401, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="#">Stundenplan importieren</a></li>
						<?php endif ?>
						<?php if (in_array(402, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/show_stdplan_list">Stundenplan bearbeiten</a></li>
						<?php endif ?>
						<?php if (in_array(403, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/delete_stdplan_view">Stundenplan loeschen</a></li>
						<?php endif ?>
					</ul>
				</li>
				<?php endif ?>
				<?php if (in_array(500, $global_data['userdata']['userpermissions'])) : ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						Datenbankverwaltung
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<?php if (in_array(501, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="#">Datenbank importieren</a></li>
						<?php endif ?>
						<?php if (in_array(502, $global_data['userdata']['userpermissions'])) : ?>
						<li><a href="#">Datenbank exportieren</a></li>
						<?php endif ?>
					</ul>
				</li>
				<?php endif ?>
			</ul>
		</div>
	</div>
</div>