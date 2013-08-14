<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Stundenplan - Tag<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>

<div class="row-fluid">
    <div id="carousel" class="carousel slide">
        <!-- Carousel items -->
        <div class="carousel-inner">
            <?php
                //--------------------Loop for one day--------------------
                $day_number = 0 ; // incremented after a day
                $no_courses = 1 ; // flag, set 0 if theres at least one course today
                foreach ($stundenplan as $dayname => $day) :
            ?>
            <div class="item <?php if ($tage[$day_number]['IstHeute']) echo "active"; ?>">
                <!--Tag-->
                <div class="day" id= "<?php echo $dayname ?>">
                    <!--Tagestitel-->
                    <div class="span12 well well-small">
                        <h6>Stundenplan</h6>
                        <h1><?php echo $dayname ?>&nbsp;<small><?php echo $tage[$day_number]['Datum'] ?></small></h1>
                        <hr/>
                        <table class="table table-condensed">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Uhrzeit</th>
                                    <th>Veranstaltung</th>
                                    <th style="width: 40px;">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                # Loop for one course
                                foreach ($day as $hourID => $hour) :
                                    # If entry at this hour exists
                                    if ($hour) :
                                        # Loop for one hour (if many courses)
                                        foreach ($hour as $courseID => $course) :
                                            # Only if there are courses -> print them
                                            if ($course) :
                                                $no_courses = 0; // no courses courses!
                            ?>
                                                <tr>
                                                    <td>
                                                        <small><?php echo $course['Beginn']; ?> - <?php echo $course['Ende']; ?></small>
                                                    </td>
                                                    <td><?php echo $course['kurs_kurz']; ?>&nbsp;<?php echo $course['VeranstaltungsformName']; ?></td>
                                                    <td>
                                                        <a href="<?php print base_url('modul/show/' . $course['KursID']); ?>" class="btn btn-primary pull-right">
                                                        <i class="icon-arrow-right icon-white"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                            <?php
                                                endif;
                                            # End Loop for one hour (if many courses)
                                            endforeach;
                                        # End If entry at this hour exists
                                        endif;
                                    # End Loop for one course
                                    endforeach;
                            ?>
                            <?php
                                if($no_courses == 1):
                                    echo "<tr>";
                                    echo "  <td></td>";
                                    echo "  <td>";
                                    echo "Du hast heute keine Veranstaltungen!";
                                    echo "  </td>";
                                    echo "  <td></td>";
                                    echo "</tr>";
                                endif;

                                $no_courses = 1; // flag reset
                            ?>
                            </tbody>
                        </table>
                    </div><!-- /.span8 -->
                </div><!-- /.row, Tag-->
            </div>
            <?php
                # increment day_number
                $day_number++;

                # End Loop for one day
                endforeach;
            ?>
        </div>
    </div>
</div>

<div class="row-fluid">
	<div class="span12">
		<div class="pagination pagination-centered">
			<ul>
				<li><a href="#carousel" data-slide="prev">&lsaquo;</a></li>
				<li><a href="#Montag">M</a></li>
				<li><a href="#Dienstag">D</a></li>
				<li><a href="#Mittwoch">M</a></li>
				<li><a href="#Donnerstag">D</a></li>
				<li><a href="#Freitag">F</a></li>	
				<li><a href="#carousel" data-slide="next">&rsaquo;</a></li>
			</ul>
		</div>
	</div><!-- /.span12-->
</div><!-- /.row-fluid -->

<div class="row-fluid">
	<div class="span12">
		<div class="fhd-box clearfix">
			<a href="<?php print base_url('dashboard/mobile'); ?>" class="btn btn-large btn-primary">&Uuml;bersicht</a>
			<a href="<?php print base_url('stundenplan/woche'); ?>" class="btn btn-large pull-right">Woche</a>
		</div>
	</div><!-- /.span12-->
</div><!-- /.row-fluid -->
<?php endblock(); ?>
<?php end_extend(); ?>