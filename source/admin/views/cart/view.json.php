<?php

/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		PAYCART
* @contact		support+readybytes@readybytes.in
* @author		rimjhim
*/

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

/** 
 * Cart Json View
* @author rimjhim
 */
require_once dirname(__FILE__).'/view.php';

class PaycartAdminViewCart extends PaycartAdminBaseViewCart
{	
	/**
	 * Create new shipment from the current cart
	 */
	public function createShipment()
	{
		$response = new stdClass();
		$response->valid   = true;
		$response->message = JText::_("COM_PAYCART_ADMIN_SHIPMENT_SUCCESSFULLY_SAVED");
		
		$data   = $this->input->get('shipmentDetails',array(),'ARRAY');
		$cartId = $this->input->get('cart_id','','INT');
		$data['cart_id'] = $cartId;
		
		//save shipment
		$result = PaycartShipment::getInstance(0,$data)->save();
		
		if(!$result){
			$response->valid  = false;
			$response->message = JText::_("COM_PAYCART_ADMIN_SHIPMENT_ERROR_WHILE_SAVING");
		}
		
		$data['shipment_id'] = $result->getId();
		$response->data = $data;
		

		$this->assign('json', $response);
		return true;
	}	
	
	/**
	 * remove shipment from the current cart
	 */
	public function removeShipment()
	{
		$response = new stdClass();
		$response->valid = true;
		$response->message = JText::_('COM_PAYCART_ADMIN_SHIPMENT_SUCCESSFULLY_DELETED');
		
		$id   = $this->input->get('shipment_id',0,'INT');
		$result = PaycartShipment::getInstance($id)->delete();
		
		if(!$result){
			$response->valid  = false;
			$response->message = JText::_('COM_PAYCART_ADMIN_SHIPMENT_ERROR_IN_DELETION');
		}

		$this->assign('json', $response);
		return true;
	}
}