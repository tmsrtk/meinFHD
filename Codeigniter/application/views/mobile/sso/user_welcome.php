<?php extend('sso/index.php'); ?>

<?php startblock('title'); # extend the site's title ?><?php get_extended_block(); ?> - Herzlich Willkommen<?php endblock();?>

<?php startblock('content'); # content for this view ?>
            <div class="well well-small clearfix">
                <div class="row-fluid">
                    <h3>Herzlich Willkommen bei meinFHD!</h3>
                    <hr>
                    <p>
                        Vielen Dank f&uuml;r deine Anmeldung bei meinFHD. Dein Account wurde erfolgreich erstellt.
                    </p>
                    <p>
                        <strong>meinFHD</strong> hilft Dir Dein Studium zu planen, zu organisieren und den &Uuml;berblick zu behalten.
                    </p>
                    <h4>F&uuml;r Erstemester / Erstnutzer</h4>
                    <br/>
                    <p>
                        Wenn du meinFHD zum ersten Mal benutzt, z.B. weil Du Dein Studium gerade beginnst, also "Erstsemester" bist, gilt f&uuml;r Dich:
                    </p>
                    <ol>
                        <li>Du gibst in Deinen pers&ouml;nlichen Daten Deinen Studiengang und Dein Startsemester an.</li>
                        <li>Du l&auml;sst einen neuen Semesterplan f&uuml;r dein Studium anlegen.</li>
                        <li>Du kannst Dich f&uuml;r die Veranstaltungen des ersten Semesters im Stundenplan anmelden.</li>
                    </ol>
                    <p>
                        Dann kannst Du alle M&ouml;glichkeiten von meinFHD f&uuml;r Studierende nutzen.
                    </p>
                    <p>
                        Weitere n&uuml;tzliche Hinweise f&uuml;r den Umgang mit meinFHD findest Du unter <strong>Hilfe</strong>,
                        sowie Antworten auf h&auml;ufig gestellte Fragen unter <strong>FAQ</strong>.
                    </p>
                    <p>
                        Mit einem Klick auf den Weiter-Button gelangst du in deinen pers&ouml;nlichen Bereich von meinFHD.
                    </p>
                    <hr>
                   <a href="<?php print base_url('sso/establish_local_session'); ?>" class="btn btn-primary btn-medium pull-right">Weiter</a>
                </div>
            </div>
<?php endblock(); ?>

<?php end_extend(); ?>