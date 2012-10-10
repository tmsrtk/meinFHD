			<nav class="navbar navbar-fixed-top">
				<div class="navbar-inner">
					<div class="container">
						<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</a>
						<a class="brand" href="#">meinFHD<span>mobile</span></a>
						<div class="nav-collapse">
							<ul class="nav level-1"> <!-- .nav.level-1 -->
								<?php if ( $this->authentication->has_permissions('hat_dashboard') ) : ?>
								<li><a href="<?php print base_url('dashboard/index'); ?>">Dashboard</a></li>
								<?php endif ?>
								<?php if ( $this->authentication->has_permissions('hat_stundenplan') ) : ?>
								<li><a href="<?php print base_url('stundenplan/index'); ?>">Stundenplan</a></li>
								<?php endif ?>
								<?php if ( $this->authentication->has_permissions('hat_semesterplan') ) : ?>
								<li><a href="<?php print base_url('studienplan/index'); ?>">Studienplan</a></li>
								<?php endif ?>
								<?php if ( $this->authentication->has_permissions('hat_meine_kurse') ) : ?>
								<li><a href="<?php print base_url('#'); ?>">Meine Kurse</a></li>
								<?php endif ?>
								<?php if ( $this->authentication->has_permissions('hat_benutzerverwaltung') ) : ?>
								<li class="dropdown">
									<a href="<?php print base_url('admin/index'); ?>" class="dropdown-toggle" data-toggle="dropdown">
										Benutzerverwaltung
										<b class="caret"></b>
									</a>
									<ul class="dropdown-menu level-2">
										<?php if ( $this->authentication->has_permissions('hat_einladungsaufforderung') ) : ?>
										<li><a href="<?php print base_url('admin/request_user_invitation_mask'); ?>">Einladungsaufforderung</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_benutzer_anlegen') ) : ?>
										<li><a href="<?php print base_url('admin/create_user_mask'); ?>">Benutzer anlegen</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_benutzer_importieren') ) : ?>
										<li><a href="<?php print base_url('admin/show_permissions'); ?>">Benutzer importieren</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_benutzer_bearbeiten') ) : ?>
										<li><a href="<?php print base_url('admin/edit_user_mask'); ?>">Benutzer bearbeiten</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_benutzer_loeschen') ) : ?>
										<li><a href="<?php print base_url('admin/delete_user_mask'); ?>">Benutzer löschen</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_rechte_verwalten') ) : ?>
										<li><a href="<?php print base_url('admin/show_role_permissions'); ?>">Rechte verwalten</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_rollen_verwalten') ) : ?>
										<li><a href="<?php print base_url('admin/edit_roles_mask'); ?>">Rollen verwalten</a></li>
										<?php endif ?>
									</ul> <!-- /.nav .level-2 -->
								</li>
								<?php endif ?>
								<?php if ( $this->authentication->has_permissions('hat_studiengang_verwaltung') ) : ?>
								<li class="dropdown">
									<a href="<?php print base_url('admin/show_stdgng_course_list'); ?>" class="dropdown-toggle" data-toggle="dropdown">
										Studiengangverwaltung
										<b class="caret"></b>
									</a>
									<ul class="dropdown-menu level-2">
										<?php if ( $this->authentication->has_permissions('hat_studiengang_anlegen') ) : ?>
										<li><a href="<?php print base_url('admin/degree_program_add'); ?>">Studiengang anlegen</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_studiengang_importieren') ) : ?>
										<li><a href="<?php print base_url('#'); ?>">Studiengang importieren</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_studiengang_bearbeiten') ) : ?>
										<li><a href="<?php print base_url('admin/degree_program_edit'); ?>">Studiengang bearbeiten</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_studiengang_kopieren') ) : ?>
										<li><a href="<?php print base_url('admin/degree_program_copy'); ?>">Studiengang kopieren</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_studiengang_loeschen') ) : ?>
										<li><a href="<?php print base_url('admin/degree_program_delete'); ?>">Studiengang loeschen</a></li>
										<?php endif ?>
									</ul> <!-- /.nav .level-2 -->
								</li>
								<?php endif ?>
								<?php if ( $this->authentication->has_permissions('hat_stundenplan_verwaltung') ) : ?>
								<li class="dropdown">
									<a href="<?php print base_url('admin/import_stdplan_view'); ?>" class="dropdown-toggle" data-toggle="dropdown">
										Stundenplanverwaltung
										<b class="caret"></b>
									</a>
									<ul class="dropdown-menu level-2">
										<?php if ( $this->authentication->has_permissions('hat_stundenplan_importieren') ) : ?>
										<li><a href="<?php print base_url('admin/stdplan_import'); ?>">Stundenplan importieren</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_stundenplan_bearbeiten') ) : ?>
										<li><a href="<?php print base_url('admin/stdplan_edit'); ?>">Stundenplan bearbeiten</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_stundenplan_loeschen') ) : ?>
										<li><a href="<?php print base_url('admin/stdplan_delete'); ?>">Stundenplan loeschen</a></li>
										<?php endif ?>
									</ul> <!-- /.nav .level-2 -->
								</li>
								<?php endif ?>
								<?php if ( $this->authentication->has_permissions('hat_datenbank_verwaltung') ) : ?>
								<li class="dropdown">
									<a href="<?php print base_url('#'); ?>" class="dropdown-toggle" data-toggle="dropdown">
										Datenbankverwaltung
										<b class="caret"></b>
									</a>
									<ul class="dropdown-menu level-2">
										<?php if ( $this->authentication->has_permissions('hat_datenbank_importieren') ) : ?>
										<li><a href="<?php print base_url('#'); ?>">Datenbank importieren</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_datenbank_exportieren') ) : ?>
										<li><a href="<?php print base_url('#'); ?>">Datenbank exportieren</a></li>
										<?php endif ?>
									</ul> <!-- /.nav .level-2 -->
								</li>
								<?php endif ?>
								<?php if ( $this->authentication->has_permissions('hat_kurse') ) : ?>
								<li class="dropdown">
									<a href="<?php print base_url('#'); ?>" class="dropdown-toggle" data-toggle="dropdown">
										Kursverwaltung
										<b class="caret"></b>
									</a>
									<ul class="dropdown-menu level-2">
										<?php if ( $this->authentication->has_permissions('hat_kurse') ) : ?>
										<li><a href="<?php print base_url('kursverwaltung/show_coursemgt'); ?>">Meine Kurse</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_kurse') ) : ?>
										<li><a href="<?php print base_url('kursverwaltung/show_labmgt'); ?>">Praktikumsverwaltung</a></li>
										<?php endif ?>
									</ul> <!-- /.nav .level-2 -->
								</li>
								<?php endif ?>
								<?php if ( $this->authentication->has_permissions('hat_faq') ) : ?>
								<li><a href="<?php print base_url('faq/index'); ?>">FAQ</a></li>
								<?php endif ?>
								<?php if ( $this->authentication->has_permissions('hat_hilfe') ) : ?>
								<li><a href="<?php print base_url('hilfe/index'); ?>">Hilfe</a></li>
								<?php endif ?>
								<?php if ( $this->authentication->is_logged_in() ) : ?>
								<li><a href="<?php print base_url('app/logout'); ?>">Logout</a></li>
								<?php endif; ?>
							</ul> <!-- /.nav.level-1 -->
						</div> <!-- /.nav-collapse -->
					</div>
				</div>
			</nav>