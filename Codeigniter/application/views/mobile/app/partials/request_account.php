<?php
# general form setup
$data_formopen = array('class' => 'form-horizontal', 'id' => 'request_invitation');
$data_forename = array(
    'class' => 'span12',
    'name' => 'forename',
    'id' => 'forename',
    'placeholder' => 'Vorname',
    'value' => set_value('forename')
);
$data_lastname = array(
    'class' => 'span12',
    'name' => 'lastname',
    'id' => 'lastname',
    'placeholder' => 'Nachname',
    'value' => set_value('lastname')
);
$data_jahr = array(
    'class' => 'span12',
    'name' => 'startjahr',
    'id' => 'startjahr',
    'placeholder' => 'Startjahr',
    'value' => set_value('startjahr')
);
$class_dd = 'class="span12 studiengang_dd" id="studiengang"';
$data_email = array(
    'class' => 'span12',
    'name' => 'email',
    'id' => 'email',
    'placeholder' => 'E-Mail*',
    'value' => set_value('email')
);
$data_matrikelnummer = array(
    'class' => 'span12',
    'name' => 'matrikelnummer',
    'id' => 'matrikelnummer',
    'placeholder' => 'Matrikelnummer',
    'value' => set_value('matrikelnummer')
);
$submit_data = array(
    'name'			=> 'los',
    'class'			=> 'btn btn-danger'
);
$data_labelattrs = array(
    'class' => 'control-label'
);

$data_role_radio_attrs = array(
    'name' => 'role',
    'id' => 'role',
);

$data_erstsemestler_cb = array(
    'name' => 'erstsemestler',
    'id' => 'erstsemestler',
);

$data_startsemester_radio = array(
    'name' => 'semesteranfang',
    'id' => 'semesteranfang'
);

// modify the studiengaenge dropdown
$studiengaenge_dropdown = array();
$studiengaenge_dropdown[0] = "Bitte ausw&auml;hlen";
$i = 1;
foreach($studiengaenge as $single_studiengang){
    $studiengaenge_dropdown[$i] = $single_studiengang;
    $i = $i + 1;
}
?>
<?php
echo form_open('app/validate_user_invitation_form/', $data_formopen);
?>

<?php
$v = set_radio('role', '5', TRUE);
$b = set_radio('role', '2');
$radio_val1 = FALSE;
$radio_val2 = FALSE;

if( ! empty( $v ) ) $radio_val1 = TRUE;
if( ! empty( $b ) ) $radio_val2 = TRUE;

?>

<div id="additional-info" class="alert">
    Gib bitte die folgenden Daten an, damit wir feststellen können, dass Du ein Student an diesem Fachbereich bist.
    Die Emailadresse wird für die Kommunikation mit meinFHD, den Dozenten und Studierenden verwendet.
</div>
<?php echo validation_errors(); ?>
<div class="control-group">
    <?php echo form_label('Ich bin ein', 'role', $data_labelattrs); ?>
    <div class="controls docs-input-sizes">
        <label class="radio">
            <?php echo form_radio($data_role_radio_attrs, '5', TRUE) ?>
            Student
        </label>
        <label class="radio">
            <?php echo form_radio('role', '2', FALSE) ?>
            Dozent
        </label>
    </div>
</div>

<div class="control-group">
    <?php echo form_label('Vorname', 'forename', $data_labelattrs); ?>
    <div class="controls docs-input-sizes">
        <?php echo form_input($data_forename); ?>
    </div>
</div>
<div class="control-group">
    <?php echo form_label('Nachname', 'lastname', $data_labelattrs); ?>
    <div class="controls docs-input-sizes">
        <?php echo form_input($data_lastname); ?>
    </div>
</div>

<div class="control-group">
    <?php echo form_label('E-Mail', 'email', $data_labelattrs); ?>
    <div class="controls docs-input-sizes">
        <?php echo form_input($data_email); ?>
    </div>
</div>

<div id="studentendaten">
    <hr />
    <div class="control-group">
        <?php echo form_label('Ich bin Erstsemestler!', 'erstsemestler', $data_labelattrs); ?>
        <div class="controls docs-input-sizes">
            <?php  echo form_checkbox($data_erstsemestler_cb, 'accept', FALSE) ?>
        </div>
    </div>

    <div id="erstsemestler_daten">

        <div class="control-group">
            <?php echo form_label('Jahr', 'startjahr', $data_labelattrs); ?>
            <div class="controls docs-input-sizes">
                <?php echo form_input($data_jahr); ?>
            </div>
        </div>

        <div class="control-group">
            <?php echo form_label('Semesteranfang', 'semesteranfang', $data_labelattrs); ?>
            <div class="controls">
                <label class="radio">
                    <?php echo form_radio($data_startsemester_radio, 'WS', TRUE); ?>
                    WS
                </label>
            </div>
            <div class="controls">
                <label class="radio">
                    <?php echo form_radio('semesteranfang', 'SS', FALSE); ?>
                    SS
                </label>
            </div>
        </div>
    </div>

    <div class="control-group">
        <?php echo form_label('Studiengang', 'studiengang', $data_labelattrs); ?>
        <div class="controls docs-input-sizes">
            <?php echo form_dropdown('studiengang', $studiengaenge_dropdown, '', $class_dd); ?>
        </div>
    </div>

    <div class="control-group">
        <?php echo form_label('Matrikelnummer', 'matrikelnummer', $data_labelattrs); ?>
        <div class="controls docs-input-sizes">
            <?php echo form_input($data_matrikelnummer) ?>
        </div>
    </div>
    <p style="text-align: center;">
        Der Verarbeitung meiner Daten auf Grundlage der <a href="#datenschutzerklaerung" data-toggle="modal">Datenschutzerkl&auml;rung</a> stimme ich mit dem Absenden dieses Formulars ausdr&uuml;cklich zu.
    </p>
</div>

<div class="form-actions">
    <?php echo form_submit($submit_data, 'Zugang anfordern'); ?>
</div>

<div id="datenschutzerklaerung" class="modal hide ">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h3>Datenschutzerkl&auml;rung</h3>
    </div>
    <div class="modal-body">
        <h4>Sinn und Zweck des Web-Angebots meinFHD</h4>
        <p>Das Web-Angebot meinFHD soll Sie bei der Planung Ihres Studiums und Ihrer Stundenplanung unterst&uuml;tzen sowie den Informationsflu&szlig; von DozentInnen und MitarbeiterInnen zu Studierenden erleichtern. Den Fachbereich, die DozentInnen und die MitarbeiterInnen unterst&uuml;tzt das meinFHD bei der notwendigen Organisation von Gruppen f&uuml;r &Uuml;bungen, Praktika, Seminare, etc. sowie durch automatisch erstellte anonyme E-Mail-Verteiler.</p>

        <h4>Personenbezogene Daten</h4>
        <p>Alle personenbezogenen Daten Ihres Accounts geben Sie selber ein. Wichtig f&uuml;r die Organisation sind f&uuml;r den Fachbereich Ihr Name und eine E-Mail-Adresse, unter der Sie wirklich st&auml;ndig erreichbar sind.</p>
        <p>Ihr Name wird gebraucht, um Sie eindeutig in einer Gruppe (&Uuml;bungsgruppe, ...) identifizieren zu k&ouml;nnen. Au&szlig;erdem kann so gepr&uuml;ft werden, ob sie in dieser Gruppe eine Pr&uuml;fungsvorleistung erbracht haben. Sie sollten also Ihren korrekten Namen angeben.</p>
        <p>An die von Ihnen angegebene E-Mail-Adresse werden im Verlaufe des Semesters von Dozenten Aufgaben, Hinweise und andere Informationen verschickt. Sie sollten also eine Adresse verwenden, unter der Sie auch erreichbar sind. E-Mail-Adressen werden automatisch zu anonymen Verteilern zusammengestellt (z.B. alle H&ouml;rer der Vorlesung Mathematik 1 im Studiengang BSc Medieninformatik), die teilweise f&uuml;r alle Nutzer verf&uuml;gbar sind. Die Teilnehmer der Verteiler sind nicht sichtbar und daher anonym.</p>
        <p>Ihre Matrikelnummer k&ouml;nnen Sie zus&auml;tzlich angeben. Sie erleichtern den DozentInnen und BetreuerInnen damit sehr die Organisation der Lehre.<p>Alle weiteren Daten und Informationen, die Sie in meinFHD eingeben, sind ausschlie&szlig;lich zu Ihrer eigenen Information und zur Organisation Ihres eigenen Studienverlaufs bestimmt. Sie werden weder von uns kontrolliert, noch kann ein anderer Studierender oder Dozent diese einsehen.</p>

        <h4>Freiwilligkeit Ihrer Nutzung und &Auml;ndern und L&ouml;schen Ihrer Daten</h4>
        <p>Falls Sie <strong>ab</strong> dem Wintersemester <strong>2012/13</strong> Ihren aktuellen Studiengang an der FH D&uuml;sseldorf aufgenommen haben gilt:
        <ul>
            <li>F&uuml;r die Unterst&uuml;tzung der Studienorganisation in ihrem Studiengang am FB Medien sind Sie auf Grundlage von &sect; 13 Abs. 3 Satz 4 Nr. 2 der Einschreibungordnung der FH D vom 3.7.2012 verpflichtet den Dienst meinFHD zu nutzen. </li>
            <li>Alle Daten, die uns &uuml;ber unsere Website mitgeteilt worden sind, werden nur so lange gespeichert, bis der Zweck - die Organisation Ihres Studiums - erf&uuml;llt ist. Da Sie selber Zugriff zu allen Ihren Daten haben, k&ouml;nnen Sie diese jederzeit selber &auml;ndern und auch l&ouml;schen - sofern das nicht dem oben angegebenen Zweck zuwider l&auml;uft. Beachten Sie dabei, dass Sie selber f&uuml;r die Richtigkeit der Daten verantwortlich sind. </li>
        </ul>
        <p>Falls Sie <strong>vor</strong> dem Wintersemester <strong>2012/13</strong> Ihren aktuellen Studiengang an der FH D&uuml;sseldorf aufgenommen haben gilt: </p>
        <ul>
            <li>Sie nutzen zur Unterst&uuml;tzung der Studienorganisation in ihrem Studiengang am FB Medien freiwillig den Dienst meinFHD, Ihre Einwilligung k&ouml;nnen Sie jederzeit widerrufen. Im Falle einer Nicht-Zustimmung bzw. eines Widerrufs werden Ihnen alternative Zug&auml;nge zu studien- bzw. pr&uuml;fungsrelevanten Unterlagen bereitgestellt. Dies gilt insbesondere f&uuml;r Pflichtveranstaltungen. Wenden Sie sich im Fall der Nicht-Zustimmung bzw. eines Widerrufs bitte umgehend an die jeweilige Dozentin/an den jeweiligen Dozenten, um einen alternativen Weg zu den Unterlagen zu erhalten.</li>
            <li>Alle Daten, die uns &uuml;ber unsere Website mitgeteilt worden sind, werden nur so lange gespeichert, bis der Zweck - die Organisation Ihres Studiums - erf&uuml;llt ist. Da Sie selber Zugriff zu allen Ihren Daten haben, k&ouml;nnen Sie diese jederzeit &auml;ndern und auch l&ouml;schen. Beachten Sie dabei, dass Sie selber f&uuml;r die Richtigkeit der Daten verantwortlich sind. </li>
        </ul>

        <h4>Keine Weitergabe der Daten</h4>
        <p>Wir, die FH D&uuml;sseldorf, geben Ihre personenbezogenen Daten im &Uuml;brigen nicht an Dritte weiter, es sei denn, dass wir dazu gesetzlich verpflichtet w&auml;ren oder Sie vorher ausdr&uuml;cklich eingewilligt haben. Soweit wir zur Durchf&uuml;hrung und Abwicklung von Verarbeitungsprozessen Dienstleistungen Dritter in Anspruch nehmen, werden die Bestimmungen des Bundesdatenschutzgesetzes eingehalten.</p>

        <h4>L&ouml;schen von Daten und Account</h4>
        <p>Wenn Sie Ihren Account in meinFHD komplett entfernen m&ouml;chten, schicken Sie bitte eine formlose Mitteilung an die Kontakt-Email-Adresse, die auf der Webseite von meinFHD angegeben ist.
            Wenn Sie gegen die allgemeinen Nutzungsbedingungen von IT im Fachbereich versto&szlig;en, beh&auml;lt sich der Fachbereich vor, Ihren Account zu l&ouml;schen.</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Schlie&szlig;en</button>
    </div>
</div>

<?php echo form_close(); ?>

