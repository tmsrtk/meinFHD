<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Logbuch Hilfe & FAQ<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>

<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>

<div class="well well-small">
    <div class="row-fluid">
        <div class="span12">
            <a href="<?php print base_url('logbuch/index'); ?>" class="btn btn-medium btn-danger" style="font-weight: bold;"><i class="icon-arrow-left icon-white"></i>&nbsp;Logbuchmen&uuml;</a>
        </div>
    </div><!-- END Logbuch help Header -->
    <hr/>
    <div class="row-fluid">
        Auf dieser Seite sind häufig auftretende Fragen und die dazugehörigen Antworten notiert, um Dir ein wenig Hilfe zu geben.
        Klicke auf die gesuchte Frage, um zur Antwort zu gelangen.
    </div>
    <br/>
    <div class="container-fluid">
        <div class="accordion" id="logbuchHelpAccordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 class="accordion-toggle" data-toggle="collapse" data-parent="#logbuchHelpAccordion" data-target="#whatIsLogbook">Was ist das Logbuch überhaupt?<i class="icon-plus pull-right"></i></h4>
                </div>
                <div id="whatIsLogbook" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="row-fluid">
                            <p>
                                Mit Hilfe des Logbuchs hast du die M&ouml;glichkeit zu jedem Deiner Kurse beliebig viele Themen zu hinterlegen und dort deine eigenen
                                F&auml;higkeiten zu dokumentieren. Vielleicht hat dein Dozent Dir auch schon zentrale Themen des Kurses vorgegeben.
                                Durch gezielte Auswertungen kannst du dir dann anschauen, an welchen Themen du noch arbeiten solltest.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 class="accordion-toggle" data-toggle="collapse" data-parent="#logbuchHelpAccordion" data-target="#whyLogbook">Warum sollte ich das Logbuch benutzen?<i class="icon-plus pull-right"></i></h4>
                </div>
                <div id="whyLogbook" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="row-fluid">
                            <p>Das Logbuch bietet dir hilfreiche Unterst&uuml;tzung beim Lernen, sowie der Klausurvorbereitung. Durch die Dokumentation von zentralen Themen und dem dazu geh&ouml;hrenden Wissensstand erh&auml;ltst Du schnell einen
                            &Uuml;berblick &uuml;ber deine Gesamtf&auml;higkeiten und noch bestehende  L&uuml;cken.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 class="accordion-toggle" data-toggle="collapse" data-parent="#logbuchHelpAccordion" data-target="#needLogbook">Muss ich das Logbuch benutzen?<i class="icon-plus pull-right"></i></h4>
                </div>
                <div id="needLogbook" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="row-fluid">
                            <p>
                                Nein, Du musst das Logbuch nicht benutzen. Es ist rein freiwillig und bietet dir Unterst&uuml;tzung beim Lernen.
                                Die im Logbuch hinterlegten Daten sind nur f&uuml;r dich sichtbar. Du musst also keine Angst haben, dass jemand
                                mitbekommt, wie gut Du bist.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 class="accordion-toggle" data-toggle="collapse" data-parent="#logbuchHelpAccordion" data-target="#howtoCreateLogbook">Wie kann ich ein Logbuch anlegen?<i class="icon-plus pull-right"></i></h4>
                </div>
                <div id="howtoCreateLogbook" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="row-fluid">
                            <p>
                                Um ein Logbuch anzulegen, klicke einfach in deinem pers&ouml;nlichen Loguchmen&uuml; auf Logbuch anlegen und w&auml;hle dort den Kurs aus,
                                f&uuml;r den ein Logbuch angelegt werden soll.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 class="accordion-toggle" data-toggle="collapse" data-parent="#logbuchHelpAccordion" data-target="#howmanyLogbooks">Kann ich beliebig viele Logbücher anlegen?<i class="icon-plus pull-right"></i></h4>
                </div>
                <div id="howmanyLogbooks" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="row-fluid">
                            <p>Ja, du kannst dir beliebig viele Logb&uuml;cher anlegen.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 class="accordion-toggle" data-toggle="collapse" data-parent="#logbuchHelpAccordion" data-target="#howtoCreateEntry">Wie kann ich einen Eintrag zu einem Logbuch hinzufügen?<i class="icon-plus pull-right"></i></h4>
                </div>
                <div id="howtoCreateEntry" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="row-fluid">
                            <p>Um einen Eintrag zu einem Logbuch hinzuf&uuml;gen zu k&ouml;nnen musst du zun&auml;chst das entsprechende Logbuch in deiner
                                Logbuch&uuml;bersicht ausw&auml;hlen. Anschlie&szlig; kannst du mit "+"-Button am Ende der Themen&uuml;bersicht einen neuen Eintrag hinzuf&uuml;gen.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 class="accordion-toggle" data-toggle="collapse" data-parent="#logbuchHelpAccordion" data-target="#whatAnalysis">Wozu dienen die Auswertungen?<i class="icon-plus pull-right"></i></h4>
                </div>
                <div id="whatAnalysis" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="row-fluid">
                            <p>Die Auswertungen bieten dir einen schnellen grafischen &Uuml;berblick &uuml;ber deine Kenntnisse und Anwesenheiten zu allen Logbuchkursen.
                               Hier kannst du schnell erkennen, welchem Kurs du besondere Beachtung schenken solltest.
                            </p>
                            <p>Zus&auml;tzlich kannst du dir auch detaillierte Auswertungen für jeden einzelnen Logbuchkurs anschauen. Hier erh&auml;ltst du dann einen Hinweis
                               welche Themen du nochmal intensiver betrachten solltest.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <h4 class="accordion-toggle" data-toggle="collapse" data-parent="#logbuchHelpAccordion" data-target="#whatAchievements">Wofür sind die Auszeichnungen?<i class="icon-plus pull-right"></i></h4>
                </div>
                <div id="whatAchievements" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="row-fluid">
                            <p>Die Auszeichnungen erh&auml;ltst du f&uuml;r besonders intensive Nutzung des Logbuch, sowie herausragende Leistungen. Diese sollen dich
                               zur Nutzung des Logbuchs animieren. Welche Auszeichnungen du sammeln kannst erf&auml;hrst du nur, wenn du das Logbuch intensiv
                               benutzt.
                            </p>
                            <p>
                                In deiner persönlichen Auszeichnungs&uuml;bersicht werden all deine bisher gesammelten Auszeichnungen dargestellt. Hier kannst du dich mit
                                deinen Kommilitone messen. Gleichzeitig tust du damit gutes f&uuml;r dein Studium.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
</div><!-- /div well well -->

<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup after content ?>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>

<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>