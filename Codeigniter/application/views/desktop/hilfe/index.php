<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Hilfe<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>
		<!-- Hilfe content -->
				<div class="row-fluid faq" id="faqContent">
					<!-- Block #1 -->
					<div class="span4">
						<!-- content Titl -->
						<div class="well well-small well-first">
							<h3>Hilfe</h3>
						</div>
					</div><!-- /.span4, Block #1 -->
					<!-- Block #2 -->
					<div class="span8">
						<div class="accordion well well-small" id="accordion-XY">
							<!--accordion-group-->
							<div class="accordion-heading">
								Hilfe Accordion Header
								<a class="btn accordion-toggle pull-right" data-toggle="collapse" data-parent="#accordion-XY" href="#target-XY">
									<i class="icon-plus"></i>
								</a>
							</div>
							<div id="target-XY" class="accordion-body collapse">
								<hr>
								Hilfe Accordion Content
							</div>
						</div><!-- /#accordion -->
					</div><!-- /.span8, Block #2 -->
				</div><!-- /.row, Hilfe content -->
<?php endblock(); ?>
<?php end_extend(); ?>