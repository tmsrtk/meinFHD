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
								<li><a href="<?php print base_url('dashboard/mobile'); ?>">&Uuml;bersicht</a></li>
								<?php endif ?>
								<?php if ( $this->authentication->has_permissions('hat_stundenplan') ) : ?>
								<li><a href="<?php print base_url('stundenplan'); ?>">Stundenplan</a></li>
								<?php endif ?>
								<?php if ( $this->authentication->has_permissions('hat_semesterplan') ) : ?>
								<li><a href="<?php print base_url('studienplan'); ?>">Studienplan</a></li>
								<?php endif ?>
								<?php if ( $this->authentication->has_permissions('hat_benutzerverwaltung') ) : ?>
								<li class="dropdown">
									<a href="<?php print base_url('admin/index'); ?>" class="dropdown-toggle" data-toggle="dropdown">
										Benutzerverwaltung
										<b class="caret"></b>
									</a>
									<ul class="dropdown-menu level-2">
										<?php if ( $this->authentication->has_permissions('hat_einladungsaufforderung') ) : ?>
										<li><a href="<?php print base_url('admin/show_open_user_requests'); ?>">Einladungsaufforderung</a></li>
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
										<li><a href="<?php print base_url('admin/delete_user_mask'); ?>">Benutzer l&ouml;schen</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_rechte_verwalten') ) : ?>
										<li><a href="<?php print base_url('admin/show_role_permissions'); ?>">Rechte verwalten</a></li>
										<?php endif ?>
									</ul> <!-- /.nav .level-2 -->
								</li>
								<?php endif ?>
								<?php if ( $this->authentication->has_permissions('hat_studiengang_verwaltung') ) : ?>
								<li class="dropdown">
									<a href="<?php print base_url('admin/show_stdgng_course_list'); ?>" class="dropdown-toggle" data-toggle="dropdown">
										Studiengangsverwaltung
										<b class="caret"></b>
									</a>
									<ul class="dropdown-menu level-2">
										<?php if ( $this->authentication->has_permissions('hat_studiengang_anlegen') ) : ?>
										<li><a href="<?php print base_url('admin/create_new_stdgng'); ?>">Studiengang anlegen</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_studiengang_importieren') ) : ?>
										<li><a href="<?php print base_url('#'); ?>">Studiengang importieren</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_studiengang_bearbeiten') ) : ?>
										<li><a href="<?php print base_url('admin/show_stdgng_course_list'); ?>">Studiengang bearbeiten</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_studiengang_kopieren') ) : ?>
										<li><a href="<?php print base_url('#'); ?>">Studiengang kopieren</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_studiengang_loeschen') ) : ?>
										<li><a href="<?php print base_url('admin/delete_stdgng_view'); ?>">Studiengang loeschen</a></li>
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
										<li><a href="<?php print base_url('admin/show_timetable_import'); ?>">Stundenplan importieren</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_stundenplan_bearbeiten') ) : ?>
										<li><a href="<?php print base_url('admin/show_stdplan_list'); ?>">Stundenplan bearbeiten</a></li>
										<?php endif ?>
										<?php if ( $this->authentication->has_permissions('hat_stundenplan_loeschen') ) : ?>
										<li><a href="<?php print base_url('admin/delete_stdplan_view'); ?>">Stundenplan l&ouml;schen</a></li>
										<?php endif ?>
									</ul> <!-- /.nav .level-2 -->
								</li>
								<?php endif ?>
                                <?php if ( $this->authentication->has_permissions('hat_kurse') ) : ?>
                                    <li><a href="<?php print base_url('kursverwaltung/show_coursemgt'); ?>">Kursverwaltung</a></li>
                                <?php endif ?>
                                <?php if( $this->authentication->has_permissions('hat_logbuch') ) :?>
                                <li><a href="<?php print base_url('logbuch/index'); ?>">Logbuch</a></li>
                                <?php endif ?>
								<?php if ( $this->authentication->is_logged_in() ) : ?>
								<li><a href="<?php print base_url('einstellungen'); ?>">Einstellungen</a></li>
								<?php endif ?>
								<?php if ( $this->authentication->has_permissions('hat_faq') ) : ?>
								<li><a href="<?php print base_url('faq'); ?>">FAQ</a></li>
								<?php endif ?>
								<?php if ( $this->authentication->has_permissions('hat_hilfe') ) : ?>
								<li><a href="<?php print base_url('hilfe'); ?>">Hilfe</a></li>
								<?php endif ?>
								<?php if ( $this->authentication->is_logged_in() ) : ?>
								<li><a href="<?php print base_url('logout'); ?>">Logout</a></li>
								<?php endif; ?>
                                <li><a href="<?php print base_url('app/imprint'); ?>">Impressum</a></li>
							</ul> <!-- /.nav.level-1 -->
						</div> <!-- /.nav-collapse -->
					</div>
				</div>
			</nav>