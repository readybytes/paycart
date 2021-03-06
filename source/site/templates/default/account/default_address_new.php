<?php

/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		PAYCART
* @subpackage	Front-end
* @contact		support+paycart@readybytes.in
*/

// no direct access
if(!defined( '_JEXEC' )){
	die( 'Restricted access' );
}

echo $this->loadTemplate('css');
?>

<div id="rbWindowTitle">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel"><?php echo JText::_('COM_PAYCART_BUYERADDRESS_ADD_NEW'); ?></h3>
	</div>
</div>

<div class="modal-body form-horizontal" id="rbWindowBody">
	<!--  New_atrribute_creation body		-->
	<form id="paycart_buyeraddress_form" class="pc-form-validate">	 
		<?php echo Rb_HelperTemplate::renderLayout('paycart_buyeraddress_edit', $display_data);?>					
	</form>
</div>

<div id="rbWindowFooter">
	<div class="modal-footer">
		<button class="btn btn-primary " onClick="paycart.account.address.add();"> 
			<?php echo JText::_('COM_PAYCART_SAVE'); ?> </button>
		<button class="btn" data-dismiss="modal" aria-hidden="true" ><?php echo JText::_('COM_PAYCART_CANCEL'); ?> </button>
	</div>
</div>

<script>
(function($)
		{
			$(document).ready(function($) 
					{
						paycart.formvalidator.initialize('#paycart_buyeraddress_form');		
					});
		})(paycart.jQuery)
</script>
<?php 