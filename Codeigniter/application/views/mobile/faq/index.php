<?php extend('base/template.php'); # extend main template  ?>

<?php startblock('title'); # extend the site's title ?><?php get_extended_block(); ?> FAQ<?php endblock(); ?>

<?php startblock('content'); # content for this view ?>

<?php
// the statements below are the data for FAQ. If we have enough time, please move them into the database.
$faq = array();

// $faq[X][0] means "Title"	
$faq[0][0] = 'Warum soll ich mein Studium jetzt schon planen?';
$faq[1][0] = 'Wie kann ich mein Studium jetzt schon planen?';
$faq[2][0] = 'Wie kann ich mich für ein Praktikum anmelden?';
$faq[3][0] = 'Für welche Gruppe soll ich mich entscheiden?';
$faq[4][0] = 'Woran erkenne ich, wo ich mich anmelden muss?';
$faq[5][0] = 'Woran sehe ich, welche Gruppen zusammengehören, welche Alternativen sind?';
$faq[6][0] = 'Was passiert, wenn ich mich nicht anmelde?';
$faq[7][0] = 'Wie kann ich eine Gruppe wechseln?';
$faq[8][0] = 'Muss ich alle Veranstaltungen des Stundenplans besuchen?';
$faq[9][0] = 'An wen wende ich mich, wenn ich noch weitere Fragen zu meinFHD habe?';

// $faq[X][1] means "Content belongs to the Title"		
$faq[0][1] = 'Damit Du direkt einen guten Überblick über die Kurse und Prüfungen, die auf Dich zukommen, hast.';
$faq[1][1] = 'Indem Du einen Semesterplan anlegst und ihn individuell einrichtest. Das kannst du über den Hauptmenüpunkt "Studium planen" tun.';
$faq[2][1] = '<p>Indem du im "<strong><a href="stundenplan_simple.html">Stundenplan</a></strong>" auf den Link "anmelden" der jeweiligen Praktikumsgruppe klickst und anschließend Deine nderungen speicherst. Voraussetzung dafür ist aber, dass Du vorher einen Studiengang und Deinen Studienbeginn in Deinen persönlichen Daten angegeben hast.</p>';
$faq[3][1] = 'Diese Entscheidung liegt bei Dir. Aber Du solltest Deine Gruppen so planene, dass sie sich nicht überkreuzen und Du an allen notwendigen Gruppen teilnehmen kannst.';
$faq[4][1] = 'Für Praktika musst Du Dich immer anmelden. Übungen sind optional. An dem An-/Abmelde-Link jeder Veranstaltung kannst Du sehen, ob du bereits für <strong>diese</strong> Veranstaltung angemeldet bist.';
$faq[5][1] = 'Einmal an der Gruppenbezeichnung. Wenn zwei Veranstaltungen z.B. <strong>OOP1 P2a</strong> heißen, gehören sie zusammen. Nachdem Du Dich für eine Veranstaltung angemeldet hast, werden alle Praktika-Alternativen komplett ausgeblendet und alle Übungs- und Seminar-Alternativen heller gefärbt.';
$faq[6][1] = 'Dann bist du über die meinFHD-Email-Verteiler nicht erreichbar und bekommst, im Falle eines nicht angemeldeten Praktikums, keine Zulassung zur Klausur.';
$faq[7][1] = 'Indem Du Dich von der einen Gruppe abmeldest und Dich dann bei einer anderen Gruppe wieder anmeldest.';
$faq[8][1] = 'Praktika sind die einzigen Veranstaltungen die Du besuchen <strong>musst</strong>. Vorlesungen, Übungen und Seminare sind freiwillig.';
$faq[9][1] = 'Per Email an <strong><a href="mailto:meinFHD.medien@fh-duesseldorf.de">meinFHD.medien@fh-duesseldorf.de</a></strong>.<br>Vielleicht findest Du auch die passende Antwort in der detaillierten Hilfe.';
?>


<!-- begin : CONTENT -->
<div class="container container-fluid">	

    <!-- begin : the first row -->
    <div class="row">

        <!-- begin : the title of this page -->
        <div class="span4">
            <div class="well well-small clearfix">
                <h6>FAQ</h6>
            </div>
        </div>
        <!-- end : the title of this page -->

        <!-- begin : the FAQ list -->
        <div class="span8">
<?php for ($n = 0; $n < count($faq); $n++): ?>

                <div class="accordion">
                    <div class="accordion-group">
                        <!-- begin : FAQ title -->
                        <div class="accordion-heading">
                            <div class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse_<?php echo $n; ?>">
    <?php echo $faq[$n][0]; ?>
                                <i class="icon-plus pull-right"></i>
                            </div>
                        </div>
                        <!-- end : FAQ title -->

                        <!-- begin : FAQ content -->
                        <div id="collapse_<?php echo $n; ?>" class="accordion-body collapse">
                            <div class="accordion-inner">
                                <div class="alert alert-info clearfix">
    <?php echo $faq[$n][1]; ?>
                                </div>
                            </div>
                        </div>
                        <!-- begin : FAQ content-->
                    </div>
                </div>
<?php endfor; ?>

        </div>
        <!-- end : the FAQ list -->
    </div>
    <!-- end : the first row -->

    <!-- begin : the second row -->
    <div class="row">
        <!-- begin : optionbox -->
        <div class="span12">
            <div class="fhd-box clearfix">
                <a href="http://localhost/meinFHD/Codeigniter/dashboard/index" class="btn btn-large btn-primary pull-left"> <i class="icon-arrow-left icon-white"></i> Dashboard </a>
            </div>
        </div>
        <!-- end : optionbox -->
    </div>
    <!-- begin : the second row -->

</div>
<!-- end : CONTENT-->
<?php endblock(); ?>
<?php end_extend(); # end extend main template  ?>
