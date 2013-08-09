<?php
/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		PayCart
* @subpackage	Frontend
* @contact 		support+paycart@readybytes.in
* @author 		Manish Trivedi
*/

/**
 * 
 * List of Populated Variables
 * 
 */
defined('_JEXEC') or die();

$attributes = Array(1,2,3,4,4,4,4,4,4,4,4,4,4,4,4,4);

?>
<script>
(function($)
		{
			$(document).ready(function()
			{
				$('*[rel=tooltip]').tooltip()

				// Turn radios into btn-group
				$('.radio.btn-group label').addClass('btn');
				$(".btn-group label:not(.active)").click(function()
				{
					var label = $(this);
					var input = $('#' + label.attr('for'));

					if (!input.prop('checked')) {
						label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
						if (input.val() == '') {
							label.addClass('active btn-primary');
						} else if (input.val() == 0) {
							label.addClass('active btn-danger');
						} else {
							label.addClass('active btn-success');
						}
						input.prop('checked', true);
					}
				});
				$(".btn-group input[checked=checked]").each(function()
				{
					if ($(this).val() == '') {
						$("label[for=" + $(this).attr('id') + "]").addClass('active btn-primary');
					} else if ($(this).val() == 0) {
						$("label[for=" + $(this).attr('id') + "]").addClass('active btn-danger');
					} else {
						$("label[for=" + $(this).attr('id') + "]").addClass('active btn-success');
					}
				});
			})
		})(jQuery);
</script>

	<div id="rbWindowTitle">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="myModalLabel"><?php echo Rb_Text::_('COM_PAYCART_ADD_NEW_ATTRIBUTE'); ?></h3>
		</div>
	</div>
	
	<div class="modal-body" id="rbWindowBody">
		<div class="row-fluid">
		
			<div class="span8 ">
				
				<!--  New_atrribute_creation header		-->
				<div class="pc-attribute-window-header" >
					<?php echo Rb_Text::_('COM_PAYCART_NEW_ATTRIBUTE'); ?>
				</div>
				
				<!--  New_atrribute_creation body		-->
				<div class="pc-attribute-window-body">
					<?php
						echo $this->loadtemplate('edit');
					?> 
				</div>
				
				<!--  New_atrribute_creation footer		-->
				<div class="pull-right">
  					<button class="btn btn-primary"> create</button>
				</div>
				
			</div>
				
			<div class="span4">
				<!--<div class="pc-attribute-window-header" >
					<?php echo Rb_Text::_('COM_PAYCART_ATTRIBUTE_LIST'); ?>
				</div>
				
				-->
				<div class="input-append">
						<input class="span9" id="appendedInputButton" type="text" placeholder="Search Attribute!!">
					  	<span class="add-on">
							<input type="checkbox" id="" value="">
						</span>
				</div>
					
				<div class="pc-attribute-window-body">
					
					<table class="table table-striped" >
						<?php
							$cbCount = 0; 
						?>
						<?php foreach($attributes as $attribute) :?>
							<tr class="<?php echo "row".$count%2; ?>">								
								
								<th>
				    				<?php echo 'attribute_title' ; ?>
				    			</th>
				    			
								<th>
				    				<?php echo PaycartHtml::_('grid.id', $cbCount++, 'attribute_id' ); ?>
				    			</th>
				    			
				    		</tr>		
						<?php endforeach;?>
					</table>				
				</div>
				
				<div class="pull-right">
  					<button class="btn btn-primary"> Add</button>
				</div>
					
			</div>
		</div>
	</div>
	<div id="rbWindowFooter">
		<div class="modal-footer">
			<button class="btn btn-primary"> create &amp; Add</button>
			<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		</div>
	</div>

<?php 