<?php

/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		PAYCART
* @subpackage	front-end
* @contact		support+paycart@readybytes.in
* @author		mManishTrivedi, Rimjhim Jain
*/

defined( '_JEXEC' ) or	die( 'Restricted access' );

require_once dirname(__FILE__).'/view.php';

class PaycartSiteViewCart extends PaycartSiteBaseViewCart
{
	public function display($tpl = NULL)
	{	
		$this->_renderOptions = array('domObject'=>'pc-cart-products','domProperty'=>'innerHTML');
		
		$helper = PaycartFactory::getHelper('cart');
		$cart 	= $helper->getCurrentCart();
		
		$productParticulars = $cart->getCartparticulars(Paycart::CART_PARTICULAR_TYPE_PRODUCT);
		
		$this->assign('products',$productParticulars);
		$this->assign('cart', $cart);
	
		$this->setTpl('products');
		
		return true;
	}
	
	/**
	 * add product to cart
	 */
	public function addProduct()
	{
		$productId = $this->input->get('product_id');
		
		$this->assign('productId', $productId);
		$this->setTpl('product');
		return true;	
	}
	
	public function login()
	{
		$this->_renderOptions = array('domObject'=>'pc-checkout-step-html','domProperty'=>'innerHTML');
		
		// if cart is invalid, then do nothing
		$isCartValid = $this->get('isCartValid', true);
		if(!$isCartValid){
			return true;
		}
		
		$errors = $this->get('errors', array());
		if(!empty($errors)){
			$ajax = PaycartFactory::getAjaxResponse();
			$ajax->addScriptCall('paycart.cart.login.error', $errors);
			$ajax->sendResponse();
		}
		
		$this->_setupCartVars();
		
		$this->setTpl('login');
		
		return true;
	}
	
	public function address()
	{
		$this->_renderOptions = array('domObject'=>'pc-checkout-step-html','domProperty'=>'innerHTML');
		
		// if cart is invalid, then do nothing
		$isCartValid = $this->get('isCartValid', true);
		if(!$isCartValid){
			return true;
		}
		
		$errors = $this->get('errors', array());
		if(!empty($errors)){
			$ajax = PaycartFactory::getAjaxResponse();
			$ajax->addScriptCall('paycart.cart.address.error', $errors);
			$ajax->sendResponse();
		}
		
		$this->_setupCartVars();
		
		//contain all buyer address 
		$buyer_addresses	=	Array();
		
		//no need to get address on guest-checkout
		if(!$this->cart->getIsGuestCheckout()) {
			// if user is login then get buyer address
			$buyer_addresses		= PaycartFactory::getModel('buyeraddress')->loadRecords(Array('buyer_id' => $this->cart->getBuyer()));
		}
		
		// address on cart
		$shipping_address_id	= $this->cart->getShippingAddress();
		$billing_address_id		= $this->cart->getBillingAddress();
		
		$this->assign('shipping_address_id',	$shipping_address_id);
		$this->assign('billing_address_id',		$billing_address_id);
		$this->assign('buyer_addresses',		$buyer_addresses);
		
		$this->setTpl('address');
		
		return true;
	}
	
	protected function _setupCartVars()
	{			
		// get std class object for cart
		$cart = (object)$this->cart->toArray();
		
		// initialization
		$buyer = $billing_address = $shipping_address = new stdClass();
		
		if ( isset($cart->params['buyer'])) {
			$buyer = (object)$cart->params['buyer'];
		}
		
		if ( isset($cart->params['billing_address'])) {
			$billing_address	=	(object)$cart->params['billing_address'];
		}
			
		if ( isset($cart->params['shipping_address'])) {
			$shipping_address	=	(object)$cart->params['shipping_address'];
		}
		
		//default value 
		$billing_to_shipping	=	true;
		if ( isset($cart->params['billing_to_shipping'])) {
			$billing_to_shipping	=	(bool)$cart->params['billing_to_shipping'];
		}
		
		$this->assign('currency_id', $this->cart->getCurrency());
		
		$this->assign('is_platform_mobile', PaycartFactory::getApplication()->client->mobile);

		//setup basic stuff		
		$this->assign('available_steps', 		$this->_getSteps());	
		$this->assign('active_task', 			$this->getTask());	
		$this->assign('buyer', 					$buyer);
		$this->assign('billing_address', 		$billing_address);
		$this->assign('shipping_address', 		$shipping_address);
		$this->assign('billing_to_shipping', 	$billing_to_shipping);
		$this->assign('cart', 					$cart);
		$this->assign('cart_total', 			$this->cart->getTotal());
		
		return true;
	}
	
	public function confirm()
	{	
		$this->_renderOptions = array('domObject'=>'pc-checkout-step-html','domProperty'=>'innerHTML');
		
		// if cart is invalid, then do nothing
		$isCartValid = $this->get('isCartValid', true);
		if(!$isCartValid){
			return true;
		}
		
		$errors = $this->get('errors', array());
		if(!empty($errors)){
			$ajax = PaycartFactory::getAjaxResponse();
			$ajax->addScriptCall('paycart.cart.confirm.error', $errors);
			$ajax->sendResponse();
		}
		
		$this->_setupCartVars();
		
		$product_particular	= Array();
		$product_total	 	= 0;
		$product_quantity	= 0;
		$product_media		= Array();
		$product_usage		= Array();
		
		foreach ($this->cart->getCartparticulars(paycart::CART_PARTICULAR_TYPE_PRODUCT) as  $key => $particular) {
			/* @var $particular Paycartcartparticular */
			$product_particular[] = $particular->toObject();
			$product_usage[$particular->getParticularId()] = $particular->getUsage();
			
			$product_total 	 	+=	$particular->getTotal(true);
			$product_quantity 	+=	$particular->getQuantity();
			
			// get product media
			$product_id = $particular->getParticularId();
			
			$product_media[$product_id]	=	PaycartProduct::getInstance($product_id)->getCoverMedia();
		}
		
		$shipping_particular	=	Array();
		$shipping_total			=	0;
		$shipping_usage		= Array();
		
		foreach ($this->cart->getCartparticulars(paycart::CART_PARTICULAR_TYPE_SHIPPING) as  $key => $particular) {
			/* @var $particular Paycartcartparticular */
			$shipping_particular[] = $particular->toObject();
			$shipping_total		  += $particular->getTotal(true);
			$shipping_usage[$particular->getParticularId()] = $particular->getUsage();
		}
		
		$promotion_particular	=	Array();
		$promotion_total		=	0;
		$promotion_usage		= Array();
		foreach ($this->cart->getCartparticulars(paycart::CART_PARTICULAR_TYPE_PROMOTION) as  $key => $particular) {
			/* @var $particular Paycartcartparticular */
			$promotion_particular[] = $particular->toObject();
			$promotion_total		  += $particular->getTotal(true);
			$promotion_usage[$particular->getParticularId()] = $particular->getUsage();
		}
		
		$duties_particular	=	Array();
		$duties_total		=	0;
		$duties_usage		= Array();
		foreach ($this->cart->getCartparticulars(paycart::CART_PARTICULAR_TYPE_DUTIES) as  $key => $particular) {
			/* @var $particular Paycartcartparticular */
			$duties_particular[] = $particular->toObject();
			$duties_total  	    += $particular->getTotal(true);
			$duties_usage[$particular->getParticularId()] = $particular->getUsage();
		}
		
		// set all particular details
		$this->assign('product_total',			$product_total);
		$this->assign('product_quantity',		$product_quantity);
		$this->assign('shipping_total',			$shipping_total);
		$this->assign('promotion_total',		$promotion_total);
		$this->assign('duties_total',			$duties_total);
		
		$this->assign('product_media',			$product_media);
		$this->assign('product_particular',		$product_particular);
		$this->assign('shipping_particular',	$shipping_particular);
		$this->assign('promotion_particular',	$promotion_particular);
		$this->assign('duties_particular',		$duties_particular);
		
		$this->assign('product_usage', 	$product_usage);
		$this->assign('promotion_usage', 	$promotion_usage);
		$this->assign('duties_usage', 	$duties_usage);
		$this->assign('shipping_usage', $shipping_usage);
		
		$this->setTpl('confirm');		
		return true;
	}
	
	/**	
	 * 
	 * Setup Payment Collection page
	 */
	public function gatewayselection() 
	{
		$this->_renderOptions = array('domObject'=>'pc-checkout-step-html','domProperty'=>'innerHTML');
		
		// if cart is invalid, then do nothing
		$isCartValid = $this->get('isCartValid', true);
		if(!$isCartValid){
			return true;
		}
		
		$errors = $this->get('errors', array());
		if(!empty($errors)){
			$ajax = PaycartFactory::getAjaxResponse();
			$ajax->addScriptCall('paycart.cart.gatewaySelection.error', $errors);
			$ajax->sendResponse();
		}
		
		$this->_setupCartVars();
		
		//available payment gateway
		$payment_gateway	=	PaycartFactory::getModel('paymentgateway')->loadRecords(Array('published' => 1 ));
		
		// @PCFIXME :: get default payment gateway then get gateway html and assign here
		$payment_gateway_html	=	'';
		
		$this->assign('payment_gateway', $payment_gateway);
		$this->assign('payment_gateway_html', $payment_gateway_html);

		$this->setTpl('gateway_selection');
	
		return true;	
	}
	
	/**
	 * 
	 * Invoke to get all available steps
	 * 
	 * @return all available steps
	 */
	protected  function _getSteps() 
	{
		$steps 				=  Array();
		
		//Step :: Address
		$step  = new stdClass();
		$step->icon		= 'fa-user';
		$step->class	= 'pc-checkout-step-'.Paycart::CHECKOUT_STEP_LOGIN;
		$step->title	=  JText::_('COM_PAYCART_CART_STEP_LOGIN');
		$step->onclick	= ($this->cart->getIsGuestCheckout()) ? 'paycart.cart.login.get();' : false;		
		$steps['login'] = $step;
		
		//Step :: Address
		$step  = new stdClass();
		$step->icon = 'fa-truck';
		$step->class = 'pc-checkout-step-'.Paycart::CHECKOUT_STEP_ADDRESS;
		$step->title	=  JText::_('COM_PAYCART_CART_STEP_ADDRESS');
		$step->onclick	= 'paycart.cart.address.get()';
		$steps['address']= $step;

		//Step :: Order-Confirm
		$step  = new stdClass();
		$step->icon = 'fa-thumbs-up';
		$step->class = 'pc-checkout-step-'.Paycart::CHECKOUT_STEP_CONFIRM;
		$step->title	=  JText::_('COM_PAYCART_CART_STEP_CONFIRM');
		$step->onclick	= ' paycart.cart.confirm.get()';
		$steps['confirm']= $step;
		
		//Step :: Payment
		$step  = new stdClass();
		$step->icon = 'fa-credit-card';
		$step->class = 'pc-checkout-step-'.Paycart::CHECKOUT_STEP_PAYMENT;
		$step->title	=  JText::_('COM_PAYCART_CART_STEP_PAYMENT');
		$step->onclick	= false;
		$steps['gatewayselection']= $step;
		
		return $steps;
	}
}