<?php

/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		PAYCART
* @subpackage	Back-end
* @contact		support+paycart@readybytes.in
* @author 		rimjhim
*/

// no direct access
defined( '_JEXEC' ) OR die( 'Restricted access' );

PaycartHtml::_('behavior.formvalidation');

$currencyId = $cart->getCurrency();
?>
<script type="text/javascript">

   	(function($)
    {
   		$(document).ready(function(){
    		$(".pc-popover").popover();
        });
   	})(paycart.jQuery);

 </script>

<div class="span8 table-responsive">
	<table class="table table-bordered">
		
		<!--=========================================
	              Individual Product Details 
		===========================================-->
		
		<thead>
			<tr>
				<td colspan="7">
					<h4><?php echo JText::_("COM_PAYCART_PRODUCT_DETAILS")?></h4>
				</td>
			</tr>
			<tr>
				<th><?php echo JText::_("COM_PAYCART_PRODUCT")?></th>
				<th><?php echo JText::_("COM_PAYCART_UNIT_PRICE")?></th>
				<th><?php echo JText::_("COM_PAYCART_QUANTITY")?></th>
				<th><?php echo JText::_("COM_PAYCART_PRICE")?></th>
				<th><?php echo JText::_("COM_PAYCART_TAX")?></th>
				<th><?php echo JText::_("COM_PAYCART_DISCOUNT")?></th>
				<th><?php echo JText::_("COM_PAYCART_TOTAL")?></th>
			</tr>
		</thead>
		<tbody>
			<?php 
				$price    = 0;
				$tax      = 0;
				$discount = 0;
				$total    = 0;
			?>
			
			
			<?php foreach ($product_particular as $particular) :?>
				<?php $price    += $particular->price;?>
				<?php $tax      += $particular->tax;?>
				<?php $discount += $particular->discount;?>
				<?php $total    += $particular->total;?>
				<tr>
					<td><?php echo $particular->title; ?></td>
					<td><?php echo $formatter->amount($particular->unit_price, true, $currencyId); ?></td>
					<td><?php echo $particular->quantity; ?></td>
					<td><?php echo $formatter->amount($particular->price, true, $currencyId); ?></td>
					<td>
						<?php echo $formatter->amount($particular->tax, true, $currencyId); ?>
						&nbsp;
							<?php $key = $particular->type.'-'.$particular->particular_id;?>
							<?php if(isset($usageDetails[$key]) && isset($usageDetails[$key][Paycart::PROCESSOR_TYPE_TAXRULE])):?>
							  <a href="#" class="pc-popover" title="<?php echo JText::_("COM_PAYCART_DETAILS")?>"
							  	 data-content="<?php echo implode("<hr/>", $usageDetails[$key][Paycart::PROCESSOR_TYPE_TAXRULE]);?>" data-trigger="hover">
							 	 <i class="fa fa-info-circle"></i>
							  </a>
							<?php endif;?>
					</td>
					<td>
						<?php echo $formatter->amount($particular->discount, true, $currencyId); ?>
						&nbsp;
						<?php $key = $particular->type.'-'.$particular->particular_id;?>
						<?php if(isset($usageDetails[$key]) && isset($usageDetails[$key][Paycart::PROCESSOR_TYPE_DISCOUNTRULE])):?>
							  <a href="#" class="pc-popover" title="<?php echo JText::_("COM_PAYCART_DETAILS")?>"
							  data-content="<?php echo implode("<hr/>", $usageDetails[$key][Paycart::PROCESSOR_TYPE_DISCOUNTRULE]);?>" data-trigger="hover">
							 	 <i class="fa fa-info-circle"></i>
							  </a>
						<?php endif;?>
					</td>
					<td><?php echo $formatter->amount($particular->total, true, $currencyId); ?></td>
				</tr>
			<?php endforeach;?>
				
				<!--=========================================
	           			 	Products total 
				===========================================-->
			
				<tr>
					<td colspan="3">
						<strong><?php echo JText::_("COM_PAYCART_TOTAL")?></strong>
					</td>
					<td><?php echo $formatter->amount($price, true, $currencyId); ?></td>
					<td><?php echo $formatter->amount($tax, true, $currencyId); ?></td>
					<td><?php echo $formatter->amount($discount, true, $currencyId); ?></td>
					<td><?php echo $formatter->amount($total, true, $currencyId); ?></td>
				</tr>
						
			<!--=========================================
	           			Cart Discount and taxes 
			===========================================-->
			
			<?php $finalTotal = $total; ?>	
			<?php if(!empty($promotion_particular)):?>
				<tr>
					<td colspan="7"><h4><?php echo JText::_("COM_PAYCART_CART_DISCOUNT_AND_TAX")?></h4></td>
				</tr>
			
				<?php $promotion_particular = array_shift($promotion_particular);?>
				<tr>
					<td colspan="6">
						<?php echo $promotion_particular->title;?>
						<br>
						<small>
							<?php $key = $promotion_particular->type.'-'.$promotion_particular->particular_id;?>
							<?php if(isset($usageDetails[$key]) && isset($usageDetails[$key][Paycart::PROCESSOR_TYPE_DISCOUNTRULE])):?>
								 	<?php echo '('.implode("<br/>", $usageDetails[$key][Paycart::PROCESSOR_TYPE_DISCOUNTRULE]).')';?>
							<?php endif;?>
						</small>
					</td>
					<td><?php echo $formatter->amount($promotion_particular->total, true, $currencyId);?></td>
					
					<?php $finalTotal += $promotion_particular->total;?>
				</tr>
			<?php endif;?>
		
			<?php if(!empty($duties_particular)):?>
				<?php $duties_particular = array_shift($duties_particular);?>
				<tr>
					<td colspan="6">
						<?php echo $duties_particular->title;?>
						<br>
						<small>
							<?php $key = $duties_particular->type.'-'.$duties_particular->particular_id;?>
							<?php if(isset($usageDetails[$key]) && isset($usageDetails[$key][Paycart::PROCESSOR_TYPE_TAXRULE])):?>
								 	<?php echo '('.implode("<br/>", $usageDetails[$key][Paycart::PROCESSOR_TYPE_TAXRULE]).')';?>
							<?php endif;?>
						</small>
					</td>
					<td><?php echo $formatter->amount($duties_particular->total, true, $currencyId);?></td>
				
					<?php $finalTotal += $duties_particular->total;?>
				</tr>
			<?php endif;?>
				
				
			<!--=========================================
	               		Shipping Details
			===========================================-->
			<?php if(!empty($shipping_particular)):?>
				<tr>
					<td colspan="7"><h4><?php echo JText::_('COM_PAYCART_SHIPPING_DETAILS')?></h4></td>
				</tr>
			
				<tr>
					<td><strong><?php echo JText::_('COM_PAYCART_ADMIN_CART_SHIPPING_METHOD');?></strong></td>
					<td><strong><?php echo JText::_('COM_PAYCART_ADMIN_PRODUCT_DETAILS').'<br>';?></strong><?php echo '('.JText::_('COM_PAYCART_ADMIN_SHIPMENT_PRODUCT_QUANTITY').')'?></td>
					<td><strong><?php echo JText::_('COM_PAYCART_SHIPPING_COST');?></strong></td>
					<td><strong><?php echo JText::_('COM_PAYCART_TAX');?></strong></td>
					<td><strong><?php echo JText::_('COM_PAYCART_DISCOUNT');?></strong></td>
					<td><strong><?php echo JText::_('COM_PAYCART_SHIPPING_ESTIMATED_DELIVERY_DATE');?></strong></td>
					<td><strong><?php echo JText::_('COM_PAYCART_TOTAL');?></strong></td>
				</tr>
				<?php foreach ($shipping_particular as $id => $particular):?>
					<tr>
						<?php $params = json_decode($particular->params);?>
						<td><?php echo PaycartShippingrule::getInstance($id)->getTitle();?></td>
						<td>
							<?php foreach ($params->product_list as $productId => $details):?>
							<?php echo $product_particular[$productId]->title.' : '.$details->quantity.'<br>'; ?>
							<?php endforeach;?>
						</td>
						<td><?php echo $formatter->amount($particular->unit_price, true, $currencyId);?></td>
						<td>
							<?php echo $formatter->amount($particular->tax, true, $currencyId);?>
							&nbsp;
							<?php $key = $particular->type.'-'.$particular->particular_id;?>
							<?php if(isset($usageDetails[$key]) && isset($usageDetails[$key][Paycart::PROCESSOR_TYPE_TAXRULE])):?>
								  <a href="#" class="pc-popover" title="<?php echo JText::_("COM_PAYCART_DETAILS")?>"
								  data-content="<?php echo implode("<hr/>", $usageDetails[$key][Paycart::PROCESSOR_TYPE_TAXRULE]);?>" data-trigger="hover">
								 	 <i class="fa fa-info-circle"></i>
								  </a>
							<?php endif;?>
						</td>
						<td>
							<?php echo $formatter->amount($particular->discount, true, $currencyId);?>
							&nbsp;
							<?php if(isset($usageDetails[$key]) && isset($usageDetails[$key][Paycart::PROCESSOR_TYPE_DISCOUNTRULE])):?>
								  <a href="#" class="pc-popover" title="<?php echo JText::_("COM_PAYCART_DETAILS")?>"
								  data-content="<?php echo implode("<hr/>", $usageDetails[$key][Paycart::PROCESSOR_TYPE_DISCOUNTRULE]);?>" data-trigger="hover">
								 	 <i class="fa fa-info-circle"></i>
								  </a>
							<?php endif;?>
						</td>
						<td><?php echo $formatter->date(new Rb_Date($params->delivery_date));?> </td>
						<td><?php echo $formatter->amount($particular->total, true, $currencyId);?></td>
						
						<?php $finalTotal += $particular->total;?>
					</tr>
				<?php endforeach;?>
			<?php endif;?>
			   <tr><td colspan="7">&nbsp;</td></tr>
			
			<!-- ======================================== 
				   		Final total
			==========================================-->
				<tr>
					<td colspan="6"><h3><?php echo JText::_('COM_PAYCART_PAYABLE_AMOUNT')?></h3></td>
					<td><strong><?php echo $formatter->amount($finalTotal, true, $currencyId);?></strong></td>
				</tr>
			
		</tbody>
	</table>
</div>
<?php 