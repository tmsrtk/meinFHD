<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<ul class="nav">
				<?php if (in_array(100, $userdata['userpermissions'])) : ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						Dashboard
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<?php if (in_array(101, $userdata['userpermissions'])) : ?>
						<li><a href="#">FAQ</a></li>
						<?php endif ?>
						<?php if (in_array(102, $userdata['userpermissions'])) : ?>
						<li><a href="#">Hilfe</a></li>
						<?php endif ?>
						<?php if (in_array(103, $userdata['userpermissions'])) : ?>
						<li><a href="#">Email Verwaltung</a></li>
						<?php endif ?>
					</ul>
				</li>
				<?php endif ?>
				<?php if (in_array(600, $userdata['userpermissions'])) : ?>
				<li><a href="<?php echo site_url(); ?>studienplan/studienplan_show">Mein Semesterplan</a></li>
				<?php endif ?>
				<?php if (in_array(700, $userdata['userpermissions'])) : ?>
				<li><a href="#">Mein Stundenplan</a></li>
				<?php endif ?>
				<?php if (in_array(200, $userdata['userpermissions'])) : ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						Benutzerverwaltung
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<?php if (in_array(201, $userdata['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/request_user_invitation_mask">Einladungsaufforderung</a></li>
						<?php endif ?>
						<?php if (in_array(202, $userdata['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/create_user_mask">Benutzer anlegen</a></li>
						<?php endif ?>
						<?php if (in_array(203, $userdata['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/show_permissions">Benutzer importieren</a></li>
						<?php endif ?>
						<?php if (in_array(204, $userdata['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/edit_user_mask">Benutzer bearbeiten</a></li>
						<?php endif ?>
						<?php if (in_array(205, $userdata['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/delete_user_mask">Benutzer loeschen</a></li>
						<?php endif ?>
						<?php if (in_array(206, $userdata['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/show_role_permissions">Rechte verwalten</a></li>
						<?php endif ?>
					</ul>
				</li>
				<?php endif ?>
				<?php if (in_array(300, $userdata['userpermissions'])) : ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						Studiengangverwaltung
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<?php if (in_array(301, $userdata['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/create_new_stdgng">Studiengang anlegen</a></li>
						<?php endif ?>
						<?php if (in_array(302, $userdata['userpermissions'])) : ?>
						<li><a href="#">Studiengang importieren</a></li>
						<?php endif ?>
						<?php if (in_array(303, $userdata['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/show_stdgng_course_list">Studiengang bearbeiten</a></li>
						<?php endif ?>
						<?php if (in_array(304, $userdata['userpermissions'])) : ?>
						<li><a href="#">Studiengang kopieren</a></li>
						<?php endif ?>
						<?php if (in_array(305, $userdata['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/delete_stdgng_view">Studiengang loeschen</a></li>
						<?php endif ?>
					</ul>
				</li>
				<?php endif ?>
				<?php if (in_array(400, $userdata['userpermissions'])) : ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						Stundenplanverwaltung
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<?php if (in_array(401, $userdata['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/import_stdplan_view">Stundenplan importieren</a></li>
						<?php endif ?>
						<?php if (in_array(402, $userdata['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/show_stdplan_list">Stundenplan bearbeiten</a></li>
						<?php endif ?>
						<?php if (in_array(403, $userdata['userpermissions'])) : ?>
						<li><a href="<?php echo site_url(); ?>admin/delete_stdplan_view">Stundenplan loeschen</a></li>
						<?php endif ?>
					</ul>
				</li>
				<?php endif ?>
				<?php if (in_array(500, $userdata['userpermissions'])) : ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						Datenbankverwaltung
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<?php if (in_array(501, $userdata['userpermissions'])) : ?>
						<li><a href="#">Datenbank importieren</a></li>
						<?php endif ?>
						<?php if (in_array(502, $userdata['userpermissions'])) : ?>
						<li><a href="#">Datenbank exportieren</a></li>
						<?php endif ?>
					</ul>
				</li>
				<?php endif ?>
			</ul>
		</div>
	</div>
</div>