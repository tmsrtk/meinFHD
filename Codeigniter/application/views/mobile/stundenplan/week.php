<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Stundenplan - Woche<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>

<?php $event_height = 50; # variable that defines the height of an simple event box in the table, won`t work with an smaller size?>

<!-- CONTENT -->
<div class="well well-small">
    <div class="row-fluid">
        <div class="span4">
            <h6>Wochenansicht</h6>
            <h1>Stundenplan</h1>
        </div>
		<!-- legend -->
        <div class="span8">
            <h4>Legende</h4>
            <span class="label btn-info">Vorlesung</span>
            <span class="label btn-primary">&Uuml;bung</span>
            <span class="label btn-warning">Tutorium</span>
            <span class="label btn-success">Pratikum</span>
            <span class="label btn-inverse">Seminar</span>
        </div>
    </div>
    <hr/>

    <!-- timetable -->
    <div class="row-fluid">
        <div class="stundenplan span12">
            <table class="table table-bordered table-condensed">
                <thead>
                    <tr>
                        <th style="width: 40px;"></th>
                        <?php $i = 1; $wochentag = date("N"); ?>
                        <?php foreach ($stundenplan as $dayname => $day) : ?>
                            <th style="<?php ($i == $wochentag) ? print 'background-color: #dee4c5;' : print 'background-color: #eeeeee;';?>">
                                <?php print substr($dayname, 0, 2); ?>.
                            </th>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                        <?php $i = 1; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="std-time-cell">
                            <?php foreach ($zeiten as $zeit) : ?>
                                <div style="height:<?php print $event_height; ?>px; vertical-align: middle; line-height: 50px;" class="std-time-cell">
                                    <?php print $zeit['Beginn']; ?>
                                </div>
                            <?php endforeach; ?>
                        </td>
                        <?php foreach ($stundenplan as $dayname => $day) : ?>
                            <td class="std-time-cell" <?php if($i == $wochentag) print 'style="background-color: #dee4c5;"'; ?> >
                                <div class="std-rel">
                                    <?php foreach ($day as $event) : ?>
                                        <?php
                                        switch ($event['VeranstaltungsformID']){
                                            case 1: $color = '3a87ad'; break; // Vorlesung - blau
                                            case 2: $color = 'b94a48'; break; // Uebung - rot
                                            case 3: $color = 'f89406'; break; // Seminar - gelb
                                            case 4: $color = '468847'; break; // Praktikum - gruen
                                            case 5: $color = '999999'; break; // Seminar-Unterricht
                                            case 6: $color = '999999'; break; // Tutorium - grau
                                        }

                                        switch ($event['VeranstaltungsformID']){
                                            case 1: $class = 'btn-info'; break; // Vorlesung - blau
                                            case 2: $class = 'btn-primary'; break; // Uebung - rot
                                            case 3: $class = 'btn-warning'; break; // Seminar - gelb
                                            case 4: $class = 'btn-success'; break; // Praktikum - gruen
                                            case 5: $class = 'btn-inverse'; break; //
                                            case 6: $class = 'btn-warning'; break; // Tutorium - grau
                                        }

                                        $css = 'width:' . $event['display_data']['width'] * 100 . '%;';
                                        $css .= 'height:' . $event_height * $event['display_data']['duration'] . 'px;';
                                        $css .= 'margin-top:' . $event_height * ($event['display_data']['start']-1) . 'px;';
                                        $css .= 'margin-left:' . 100 * (1 / $event['display_data']['max_cols']) * $event['display_data']['column'] . '%;';
                                        $css .= 'z-index:' . (100 - $event['display_data']['column']);
                                        ?>

                                        <a href="<?php print base_url('modul/show/' . $event['KursID']); ?>" class="std-abs std-event <?php print $class; ?>" style="<?php print $css; ?>">
                                            <div class="std-event-container">
                                                <h5><?php print $event['kurs_kurz']; ?></h5>
                                                <h6><?php ( ! empty($event['Raum'])) ? print '('.$event['Raum'].')' : '' ?></h6>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row-fluid">
	<div class="span12">
		<div class="fhd-box clearfix">
			<a href="<?php print base_url('dashboard/mobile'); ?>" class="btn btn-large btn-primary">&Uuml;bersicht</a>
			<a href="<?php print base_url('stundenplan'); ?>" class="btn btn-large pull-right">Tag</a>
		</div>
	</div><!-- /.span12-->
</div><!-- /.row-fluid -->

<?php endblock(); ?>
<?php end_extend(); ?>
