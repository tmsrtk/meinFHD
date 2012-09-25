<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - meine Auswertungen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>

<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>

<div class="well well-small">
    <div class="row-fluid">
        <div class="span12">
            <a href="<?php print base_url('logbuch/index'); ?>" class="btn btn-medium btn-danger" style="font-weight: bold;"><i class="icon-arrow-left icon-white"></i>&nbsp;Logbuchmen&uuml;</a>
        </div>
    </div><!-- END header with backlink -->
    <hr/>
    <p>Im folgenden kannst du dir einen deiner Logbuch-Kurse ausw&auml;hlen, f&uuml;r den du dir detaillierte Auswertungen anschauen m&ouml;chtest.</p>
    <!-- dynamic content foreach logbook course -->
    <table class="table">
        <tbody>
        <?php foreach($logbook_courses as $course => $course_attr) :?>
            <tr data-id="<?php echo $course_attr['LogbuchID']; ?>">
                <td>
                    <span><img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_stats.png" alt="Auswertungen" style="max-width: 20px; height: auto;"/></span>
                    &nbsp;&nbsp;&nbsp;<a href="<?php print base_url('logbuch_analysis/show_analysis_for_course'); ?>"><strong><?php echo $course_attr['kurs_kurz']; ?></strong></a>
                </td>
                <td>
                </td>
                <td>
                </td>
                <td>
                    <a href="<?php print base_url('logbuch_analysis/show_analysis_for_course'); ?>/<?php echo $course_attr['KursID']; ?>" class="btn pull-right"><img src="<?php print base_url(); ?>resources/img/logbuch-icons/glyphicons_thin_right_arrow.png" alt="delete" style="max-width: 18px; height: auto;"/></a>
                </td>
            </tr>
        <?php endforeach ?> <!-- end foreach content -->
        </tbody>
    </table>

</div><!-- /div well well -->

<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup after content ?>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>
<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>