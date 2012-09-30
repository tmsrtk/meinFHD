<?php extend('base/template.php'); # extend main template  ?>

<?php startblock('title'); ?><?php get_extended_block(); ?> Hilfe<?php endblock(); ?>

<?php startblock('content'); # content for this view ?>


<?php
// the statements below are the data for FAQ. If we have enough time, please move them into the database.
$help = array();

// $help[X][0] means "Title"	
$help[1][0] = 'Persönliche Daten';
$help[2][0] = 'Semesterplan anlegen';
$help[3][0] = 'Aufbau des Semesterplans';
$help[4][0] = 'Fächer verschieben';
$help[5][0] = 'Prüfungen anmelden';
$help[6][0] = 'Noten eintragen';
$help[7][0] = 'Leistungs-Kurzübersicht';


// $help[X][1] means "Content belongs to the Title"		
$help[1][1] = 'Auf der Seite persönliche Daten verwalten kannst Du Deine persönlichen Daten einsehen und ändern.<br />Dazu gehört zum Beispiel die Angabe des Studiengangs in dem Du eingeschrieben bist und das Jahr und das Semester (Wintersemester (WS) oder Sommersemester (SS)) in dem Du Dein Studium begonnen hast.<br />Diese Informationen werden benötigt, damit du Deine Semester planen und Deinen Stundenplan benutzen kannst.<br />Bei den Auswahl des Studiengangs ist zusätzlich die "Version" angegeben, z.B. B.Eng Medientechnik [2008]. Die 2008 bezeichnet das Jahr in dem diese Version vom Fachbereich definiert wurde.<br />Erstsemester wählen immer die Version Ihres Studiengangs mit der höchsten Jahreszahl, also die neueste Version.<br />Alle anderen Studierenden wählen die Version Ihres Studiengangs, die der entspricht, mit der sie ihr aktuelles Studium begonnen haben.<br />Zusätzlich kannst Du Deine Login-Daten nach Lust und Laune ändern und bei Bedarf auch eine andere Email-Adresse angeben.<br />Dein korrekter Vor- und Nachname wird allerdings ebenfalls gebraucht, um Teilnehmerlisten für Übungen oder Praktika erstellen zu können.<br />Weiterhin kannst du, sobald du diese besitzt, deine Matrikelnummer eintragen.<br />Doch Vorsicht! Aus Sicherheitsgründen, ist es Dir nur einmal erlaubt die Matrikelnummer einzutragen.<br />Eine Sicherheitsabfrage schützt Dich allerdings davor eine falsche Eingabe abzuspeichern.<br />Solltest Du Dich dennoch vertan haben und eine falsche Matrikelnummer eingetragen haben, kannst Du Dich bei einem Fachbereich-Administrator melden, damit dieser deine korrekte Matrikelnummer eintragen kann: <a href="mailto:meinFHD.medien@fh-duesseldorf.de">meinFHD.medien@fh-duesseldorf.de</a>.';
$help[2][1] = 'Wenn Du zum ersten Mal die Semesterplan-Seite öffnest, wirst Du gefragt, ob Du einen neuen Semesterplan anlegen möchtest, da Du bisher keinen hast.<br />Das geht natürlich nur, wenn Du vorher in Deinen persönlichen Daten Deinen Studiengang ausgewählt und Dein Startsemester angegeben hast.<br />Lässt Du einen neuen Plan erstellen, wird dieser mit der Standardbelegung angelegt.<br />Dieser erste Semesterplan entspricht dem Studienverlauf, den der Fachbereich vorgesehen hat.<br />Die Fächer bauen dabei logisch aufeinander auf und führen Dich in der Regelstudienzeit zum Abschluss (sofern Du alles bestehst ;-)<br />Dieser Studienverlauf ist aber "nur" eine Empfehlung, wenn auch eine fundierte. Wenn Du langsamer lernen möchtest, Fächer wiederholen musst oder Zeit für einen Nebenjob brauchst, solltest Du Dir überlegen, wie Du Deinen eigenen Studienplan zusammenstellst. Dabei beraten Dich gerne sowohl die Fachschaft als auch die Fachstudienberatung Deines Faches (wer das zurzeit gerade ist, findest Du auf den Web-Seiten des Fachbereichs). Mach Dir Dein eigenes Bild am besten aus Informationen aus mehreren Quellen.<br />Du kannst Dein Studium organisieren, wie Du möchtest, aber informiere Dich und überlege!<br />Und natürlich kannst Du Deinen Plan im Laufe des Studiums auch immer Deiner aktuellen Situation anpassen.';
$help[3][1] = 'Die Fächer des gesamten Studiums werden in einer Tabelle dargestellt. Sie besteht aus drei Bereichen und so vielen Spalten wie Semester benötigt werden.<br />Die drei Bereiche unterscheiden sich darin, ob Du ein Fach hören und/oder schreiben möchtest.<br />Beispiel:  (hier werden alle Fächer gehört und geschrieben)<br />"Hören und Schreiben" eines Faches ist der Standardfall, gerade für Erstsemester: Du besuchst die Veranstaltungen und schreibst danach die Klausur, oder wirst anders geprüft, je nachdem, was für dieses Fach vorgesehen ist.<br />Wenn Du die Prüfung in einem Fach wiederholen möchtest, aber nicht mehr dessen Veranstaltungen besuchen willst (oder kannst), dann schieb dieses Fach in den Bereich nur schreiben.<br />Wenn Du die Veranstaltung besuchen möchtest, aber weißt, dass Du die Prüfung in diesem Semester nicht ablegen wirst, dann schieb das Fach in den Bereich nur hören.<br />Wenn Du länger als standardmäßig <br />vorgesehen studieren möchtest, geht das auch:<br />Möchtest Du ein Semester an Deinen Semesterplan anhängen, brauchst Du nur auf den Button "+" rechts neben der Tabelle zu klicken, damit wird ein neues, leeres Semester angehangen. Solltest Du aus Versehen auf "+" geklickt haben, ist das überhaupt kein Problem. Denn wenn Du Deine Änderungen speicherst, werden leere Semester am Ende wieder entfernt - sofern sie nicht zwischen gefüllten Semestern liegen.<br />Jede Tabellenspalte zeigt in ihrem Kopf an, wie viele Credit-Points (CP) und Semester-Wochen-Stunden (SWS) für dieses Semester insgesamt eingetragen sind (im Beispiel oben: 31 CP und 26 SWS für das 1. Semester).<br />Credit Points (CP) stehen übrigens für den zeitlichen Aufwand der Studierenden für ein Fach. 1 CP entspricht dabei 30 Zeitstunden Arbeit, verteilt über das ganze Semester. Beispiel: Mathematik 1 ist mit 5 CP bewertet. Das bedeutet, dass erwartet wird, dass Du im ganzen Semester 150 Zeitstunden in das Fach investierst. Das schließt die Anwesenheit in den Veranstaltungen, Vor- und Nachbereitung und die Prüfungsvorbereitung ein.<br />Semester-Wochen-Stunden (SWS) sind die Stunden der Veranstaltungen pro Woche. Mathematik 1 umfasst im Studiengang Medientechnik z.B. 5 SWS, davon 3 SWS in der Vorlesung (V3) und 2 SWS in der Übung (Ü2). Die "Stunden" der Veranstaltungen sind übrigens, wie Schulstunden, 45 Minuten lang.';
$help[4][1] = 'Wenn Du nicht dem Standard-Studienverlauf folgen möchtest, kannst Du die Fächer gemäß Deiner persönlichen Planung im Semesterplan verschieben.<br />Möchtest Du ein bestimmtes Fach erst in einem späteren Semester hören oder auch schreiben, kannst Du es bequem per Drag and Drop von einem Semester zum anderen ziehen. Klicke dazu einfach auf ein Fach, halte die Maustaste gedrückt und ziehe das Fach dann in das Semester in dem Du es gerne hören, schreiben oder hören und schreiben würdest.<br />Es gibt allerdings einige logische Regeln für das Verschieben zu beachten: Du kannst Fächer nur verschieben, wenn:<br />sie vor dem Verschieben in Deinem aktuellen oder einem höheren Semester liegen und die zugehörige Prüfung noch nicht bestanden wurde und Du von Sommer- zu Sommer- oder von Winter- zu Wintersemester verschiebst.<br />Ausnahme: Wird ein Fach nur geschrieben und nicht gehört, kann es in jedes Semester verschoben werden, da jedes Semester alle Prüfungen angeboten werden.';
$help[5][1] = 'meinFHD besitzt (noch) keine direkte Verbindung zum IT-System der Prüfungsorganisation<br />Du musst Dich auf jeden Fall über das Prüfungsamt zu Deinen Prüfungen anmelden';
$help[6][1] = 'Hast Du eine Prüfung bestanden, kannst Du die erworbenen Punkte in das entsprechende Feld im Fach eintragen.<br />Nach dem Abspeichern Deiner Änderungen werden die Noten anhand der eingegebenen Punkte errechnet und das Fach farblich gekennzeichnet. Die Farbmarkierung geht dabei von sattem Grün (Note 1) bis zu hellem Gelb-Orange (Note 5).';
$help[7][1] = 'Oberhalb der Tabelle der Fächer befindet sich ein hervorgehobener Bereich, in dem Deine aktuelle Durchschnittsnote, deine aktuelle Durchschnitts-Punktzahl und deine bisher erreichten Credit Points dargestellt werden.<br />Beispiel: <br />So hast du immer einen schnellen Überblick über Deinen Stand im Studium.';

$cycleTimes = count($help);
?>


<!-- begin : CONTENT -->
<div class="container container-fluid">

    <!-- begin : row -->
    <div class="row">

        <!-- begin : carousel -->
        <div id="carousel" class="carousel slide">
            <div class="carousel-inner">

                <!-- begin : first div for menu items -->
                <div class="item active">
                    <div class="row-fluid day" id="itemList">

                        <!-- begin : the title of this page -->
                        <div class="span4">
                            <div class="well well-small clearfix">
                                <h6>Hilfe</h6>
                            </div>
                        </div>
                        <!-- end : the title of this page -->						

                        <!-- begin : the list title -->
                        <div class="span8">
                            <div class="accordion well well-small clearfix">
<?php for ($n = 1; $n < $cycleTimes; $n++): ?>
                                    <a class="helpContent_<?php echo $n; ?> btn btn-large span8 accordion" href="#helpContent_<?php echo $n; ?>"><?php echo $help[$n][0]; ?><i class="icon-chevron-right pull-right"></i></a>
                                <?php endfor; ?>
                            </div>	
                        </div>
                        <!-- end : the list title -->

                    </div>
                </div>
                <!-- end : first div for menu items -->

                <!-- begin : help content -->
<?php for ($n = 1; $n < $cycleTimes; $n++): ?>
                    <div class="item">
                        <div class="row-fluid day" id="helpContent_<?php echo $n; ?>">
                            <div class="span8">
                                <div class="accordion well well-small">

                                    <a class="backTitle btn span6 pull-left" href="#itemList"><i class="icon-chevron-left pull-left"></i>zur Titelliste</a>
                                    <br />
                                    <p>
    <?php echo $help[$n][1]; ?>
                                    </p>
                                    <br />
                                    <a class="backTitle btn span6 pull-left" href="#itemList"><i class="icon-chevron-left pull-left"></i>zur Titelliste</a>

                                </div>
                            </div>
                        </div>
                    </div>
<?php endfor; ?>
                <!-- end : help content -->

            </div>
        </div>
        <!-- end : carousel -->

        <div class="row">
            <div class="span12">
                <div class="fhd-box clearfix">
                    <a href="http://localhost/meinFHD/Codeigniter/dashboard/index" class="btn btn-large btn-primary pull-left"> <i class="icon-arrow-left icon-white"></i> Dashboard </a>
                </div>
            </div>
            <!-- /.span12-->
        </div>
        <!-- /.row-fluid -->

    </div>
    <!-- end : row -->
</div>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode'); ?>

// js for slide controlling when the 'CONTENT' button be clicked
$('.backTitle').click(function(){
$('.carousel').carousel(0);
});

// js for slide controlling when the 'TITLE' button be clicked
$(function(){
<?php for ($n = 1; $n < $cycleTimes; $n++): ?>
    $('.helpContent_<?php echo $n; ?>').click(function(){	
    $('.carousel').carousel(<?php echo $n; ?>);
    });
<?php endfor; ?>
});	

<?php endblock(); ?>

<?php end_extend(); ?>
