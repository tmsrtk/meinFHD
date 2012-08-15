<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - FAQ<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>
		<!-- FAQ content -->
				<div class="row-fluid faq" id="faqContent">
					<!-- Block #1 -->
					<div class="span4">
						<!-- content Titel -->
						<div class="well well-small well-first">
							<h3>FAQ</h3>
						</div>
					</div><!-- /.span4, Block #1 -->
					<!-- Block #2 -->
					<div class="span8">
						<div class="accordion well well-small" id="accordion-XY">
							<!-- accordion-group -->
							<div class="accordion-heading">
								FAQ Accordion Header
								<a class="btn accordion-toggle pull-right" data-toggle="collapse" data-parent="#accordion-XY" href="#target-XY">
									<i class="icon-plus"></i>
								</a>
							</div>
							<div id="target-XY" class="accordion-body collapse">
								<hr>
								FAQ Accordion Content
							</div>
						</div><!-- /#accordion -->
					</div><!-- /.span8, Block #2 -->
				</div><!-- /.row, FAQ content -->
<?php endblock(); ?>
<?php end_extend(); ?>