<?php extend('base/template.php'); # extend main template  ?>

<?php startblock('title'); # extend the site's title ?><?php get_extended_block(); ?> - FAQ<?php endblock(); ?>

<?php startblock('content'); # content for this view ?>

<?php
// the statements below are the data for FAQ.
$faq = array();

// $faq[X][0] means title/asked question
$faq[0][0] = 'Warum soll ich mein Studium jetzt schon planen?';
$faq[1][0] = 'Wie kann ich mein Studium jetzt schon planen?';
$faq[2][0] = 'Wie kann ich mich für ein Praktikum anmelden?';
$faq[3][0] = 'Für welche Gruppe soll ich mich entscheiden?';
$faq[4][0] = 'Woran erkenne ich, wo ich mich anmelden muss?';
$faq[5][0] = 'Woran sehe ich, welche Gruppen zusammengehören, welche Alternativen sind?';
$faq[6][0] = 'Was passiert, wenn ich mich nicht anmelde?';
$faq[7][0] = 'Wie kann ich eine Gruppe wechseln?';
$faq[8][0] = 'Muss ich alle Veranstaltungen des Stundenplans besuchen?';
$faq[9][0] = 'An wen wende ich mich, wenn ich weitere Fragen zu meinFHD habe?';

// $faq[X][1] means "Content belongs to the title/question"
$faq[0][1] = 'Damit Du direkt einen guten Überblick über die Kurse und Prüfungen, die auf Dich zukommen, hast.';
$faq[1][1] = 'Indem Du einen Semesterplan anlegst und ihn individuell einrichtest. Das kannst du über den Hauptmenüpunkt <strong>"Studienplan"</strong> tun.';
$faq[2][1] = 'Indem du im "<strong>Stundenplan</strong>" auf den Link "anmelden" der jeweiligen Praktikumsgruppe klickst und anschließend Deine Änderungen speicherst. Voraussetzung dafür ist aber, dass Du vorher einen Studiengang und Deinen Studienbeginn in Deinen persönlichen Daten angegeben hast.';
$faq[3][1] = 'Diese Entscheidung liegt bei Dir. Aber Du solltest Deine Gruppen so planen, dass sie sich nicht überkreuzen und Du an allen notwendigen Gruppen teilnehmen kannst.';
$faq[4][1] = 'Für Praktika musst Du Dich immer anmelden. Übungen sind optional. An dem An-/Abmelde-Link jeder Veranstaltung kannst Du sehen, ob du bereits für <strong>diese</strong> Veranstaltung angemeldet bist.';
$faq[5][1] = 'Einmal an der Gruppenbezeichnung. Wenn zwei Veranstaltungen z.B. <strong>OOP1 P2a</strong> heißen, gehören sie zusammen. Nachdem Du Dich für eine Veranstaltung angemeldet hast, werden alle Praktika-Alternativen komplett ausgeblendet und alle Übungs- und Seminar-Alternativen heller gefärbt.';
$faq[6][1] = 'Dann bist du über die meinFHD-Email-Verteiler nicht erreichbar und bekommst, im Falle eines nicht angemeldeten Praktikums, keine Zulassung zur Klausur.';
$faq[7][1] = 'Indem Du Dich von der einen Gruppe abmeldest und Dich dann bei einer anderen Gruppe wieder anmeldest.';
$faq[8][1] = 'Praktika sind die einzigen Veranstaltungen die Du besuchen <strong>musst</strong>. Vorlesungen, Übungen und Seminare sind freiwillig.';
$faq[9][1] = 'Per Email an <strong><a href="mailto:meinFHD.medien@fh-duesseldorf.de">meinFHD.medien@fh-duesseldorf.de</a></strong>.<br/>Vielleicht findest Du auch die passende Antwort in der detaillierten Hilfe.';
?>

<div class="well well-small">
    <div class="row-fluid">
        <div class="span12">
            <h6>Fragen und Antworten</h6>
            <h1>FAQ</h1>
        </div>
    </div>
    <hr/>
    <div class="row-fluid">
        Auf dieser Seite sind häufig auftretende Fragen und die dazugehörigen Antworten notiert, um so bei konkreten Fragen schneller helfen zu können.
    </div>
    <br/>


    <div class="container-fluid">
        <div class="accordion" id="faqAccordion">
            <?php for ($n = 0; $n < count($faq); $n++): // for each defined question (see above) print the accordion?>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 class="accordion-toggle" data-toggle="collapse" data-parent="#faqAccordion" data-target="#<?php echo $n; ?>"><?php echo $faq[$n][0]; ?><i class="icon-plus pull-right"></i></h4>
                </div>
                <div id="<?php echo $n; ?>" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="alert alert-info clearfix">
                            <?php echo $faq[$n][1]; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>
<?php endblock(); ?>
<?php end_extend(); # end extend main template  ?>
