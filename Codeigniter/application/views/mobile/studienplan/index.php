<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title'); # extend the site's title ?><?php get_extended_block(); ?>Studienplan<?php endblock();?>

<?php startblock('content'); # content for this view ?>
<!-- begin : pop window (one-to-one relation between class and it's pop window) -->
<?php foreach ($studienplan as $semester) : ?>
    <?php $semesterNum = 0; ?>
    <?php foreach ($semester as $modul) : ?>
        <?php foreach ($modul as $data) : ?>

            <!-- begin : html for pop window -->
            <form id="popwinform" name="popwinform" method="post" action="<?php print base_url('studienplan/speichern'); ?>">
                <?php echo '<div id="popWindow' . $data['KursID'] . '" class="modal hide" >'; ?>
                <div class="modal-header">
                    <a class="close" data-dismiss="modal">×</a>
                    <h3><?php echo $data['Kurzname']; ?></h3>
                </div>
                <div class="modal-body">
                    <?php
                    /**
                     * use the value of $data['Teilnehmen'] and $data['Pruefen'] 
                     * to control the status of "Button-Checkbox"( 'Prüfen' and 'Teilnehmen' )
                     * if the value = 1, 'Status' = 'active', means switch on.
                     * */
                    $listenStatus = '';
                    $writeStatus = '';

                    ($data['Teilnehmen'] == 1) ? $listenStatus = 'active' : '';
                    ($data['Pruefen'] == 1) ? $writeStatus = 'active' : '';
                    ?>
                    <input id="hoeren_<?php echo $data['KursID']; ?>" name="hoeren_<?php echo $data['KursID']; ?>" type="hidden" value="<?php echo $data['Teilnehmen']; ?>" />
                    <input id="schreiben_<?php echo $data['KursID']; ?>" name="schreiben_<?php echo $data['KursID']; ?>" type="hidden" value="<?php echo $data['Pruefen']; ?>" />
                    <h4>
                        <span>aktuelle Note: <?php echo $data['Notenpunkte']; ?></span>
                        <a onclick="changeWriteStatus(<?php echo $data['KursID']; ?>);" class="btn btn-mini pull-right <?php echo $writeStatus; ?>" data-toggle="button"><i class="icon-pencil"></i> Prüfen</a>
                        <a onclick="changelistenStatus(<?php echo $data['KursID']; ?>);" class="btn btn-mini pull-right <?php echo $listenStatus; ?>" data-toggle="button"><i class="icon-headphones"></i> Teilnehmen</a>
                    </h4>
                    <br />
                    <h4>
                        <span>Notenpunkte:</span>
                        <?php echo '<input id="note_' . $data['KursID'] . '" name="note_' . $data['KursID'] . '" type="text" class="span1 input-mini pull-right" value="">'; ?>
                    </h4>
                    <br />
                    <h4>
                        <span>teilnehmen im:</span>
                        <select id="semester_<?php echo $data['KursID']; ?>" name="semester_<?php echo $data['KursID']; ?>" class="span2">                        
                        <?php for ($n = 0; $n < count($semester); $n++) : ?>						
                            <option value="<?php echo $n; ?>"><?php echo $n; ?>. Semester</option>
                        <?php endfor; ?>               
                        </select>
                    </h4>
                </div>

                <div class="modal-footer">
                    <a href="#" class="btn" data-dismiss="modal">schließen</a>
                    <button type="submit" class="btn btn-primary">speichern</button>
                </div>

            </div>
            </form>
            <!-- end : html for pop window -->

        <?php endforeach; ?>
        <?php $semesterNum++; ?>
    <?php endforeach; // end : for "Semester" ?>
<?php endforeach;   // end : for "Modul" ?>
<!-- end : pop window (one-to-one relation between class and it's pop window) -->



<div class="row-fluid">
	<div class="span4 well">
		<h6>Semesterplanung</h6>
		<h1>Übersicht</h1>
		<a name="jumpkpoint"></a>
	</div>
        
	<div class="span8">
		<?php foreach ($studienplan as $semester) : ?>
			<?php $semesterNum = 0; ?>
				<?php foreach ($semester as $modul) : ?>
					<div class="accordion">
                        <div class="accordion-group">

                            <!-- begin : Semester title -->				
                            <div class="accordion-heading">
                                <div class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse_<?php echo $semesterNum; ?>">
                                    <?php
                                    if ($semesterNum == 0) {
                                        echo 'Anerkennungssemster';
                                    } else {
                                        echo $semesterNum . ' Semester';
                                    }
                                    ?>
                                    <i class="icon-plus pull-right"></i>
                                </div>
                            </div>				
                            <!-- end : Semester title -->		

                            <!-- begin : Classes title -->				
                            <div id="collapse_<?php echo $semesterNum; ?>" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <?php $moduleCount = count($modul);
                                    
                                            foreach($modul as $data){
                                            
                                            $noClassMSG = '';
                                            
                                            if($data['KursID'] == null){
                                                $moduleCount--;
                                                
                                                if($moduleCount == 0){
                                                    echo '<div class="alert alert-info clearfix">	
                                                        In diesem Semester finden keine Module statt.
                                                        </div>';
                                                }
                                                continue;
                                            }
                                                ?>
                                            <div class="alert alert-info clearfix">										
                                            <?php echo $data['Kurzname']; ?>				
                                                <a class="btn btn-mini pull-right" onclick="sendSemesterNum(<?php echo $semesterNum.', '.$data['KursID']; ?>);" data-toggle="modal" data-backdrop="static" href="#popWindow<?php echo $data['KursID']; ?>" >bearbeiten</a>							
                                            </div>
                                        <?php }// end : for "classes"  ?>
                                </div>
                            </div>	
                            <!-- begin : Classes title -->

                        </div>
                    </div>

                    <?php $semesterNum++; ?>
                <?php endforeach; // end : for "Semester"  ?>
            <?php endforeach;    // end : for "Modul"?>	
        <!-- end : the Semester list -->
    </div>
    <!-- end : the first row -->
</div>

<div class="row-fluid">
	<div class="span12">
		<div class="fhd-box clearfix">
			<a href="<?php print base_url('dashboard/mobile'); ?>" class="btn btn-large btn-primary">Übersicht</a>
			<a href="<?php print base_url('studienplan/spalteEinfuegen'); ?>" class="btn btn-large pull-right"><i class="icon-plus"></i>&nbsp;Semester</a>
		</div>
	</div><!-- /.span12-->
</div><!-- /.row-fluid -->

<?php endblock();?>


<?php startblock('customFooterJQueryCode');?>

	// change the icon for title
	$('.collapse').on('shown', function(e) {
		$(e.target).parent().find('i.icon-plus').removeClass('icon-plus').addClass('icon-minus');
	}).on('hidden', function(e) {
		$(e.target).parent().find('i.icon-minus').removeClass('icon-minus').addClass('icon-plus');
	});

<?php endblock(); ?>

<script type="text/javascript">
	
	// set the current semester to be the "Selected" semester
	function sendSemesterNum(semesterNum, classID) {
        $('body,html').animate({scrollTop:0},1);        
        $('#semester_' + classID).val(semesterNum);
	}
	
	// if the status of Teilnehmen-Button be changed, change the value of "hidden input"(writeHidden)
	function changeWriteStatus(classID) {
		if ($('#hoeren_' + classID).val() == 1) {
			$('#hoeren_' + classID).val(0);
		} else {
			$('#hoeren_' + classID).val(1);
		}
	}
	
	// if the status of Teilnehmen-Button be changed, change the value of "hidden input"(listenHidden)
	function changelistenStatus(classID) {
		if ($('#schreiben_' + classID).val() == 1) {
			$('#schreiben_' + classID).val(0);
		} else {
			$('#schreiben_' + classID).val(1);
		}
    }
</script>


<?php end_extend(); # end extend main template ?>
